<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once('connection.php');

class DiagnosisSystem {
    private $conn;
    private $data;
    private $symptomWeightCache = [];
    private const MIN_CONFIDENCE_THRESHOLD = 30;
    private const SEVERITY_MODIFIERS = [
        'age_factor' => [
            'child' => 1.2,
            'elderly' => 1.3,
            'adult' => 1.0
        ],
        'risk_factor' => [
            'smoking' => 1.2,
            'obesity' => 1.15,
            'diabetes' => 1.25,
            'hypertension' => 1.2
        ]
    ];
    
    public function __construct($conn, $data) {
        $this->conn = $conn;
        $this->data = $this->sanitizeInput($data);
        $this->loadSymptomWeights();
    }

    /**
     * Sanitize and validate input data
     */
    private function sanitizeInput($data) {
        $sanitized = [];
        $sanitized['mainSymptom'] = strip_tags(strtolower(trim($data['mainSymptom'])));
        $sanitized['duration'] = filter_var($data['duration'], FILTER_VALIDATE_INT);
        $sanitized['symptoms'] = array_map(function($symptom) {
            return strip_tags(strtolower(trim($symptom)));
        }, $data['symptoms'] ?? []);
        $sanitized['age'] = filter_var($data['age'] ?? 'adult', FILTER_SANITIZE_STRING);
        $sanitized['riskFactors'] = array_filter($data['riskFactors'] ?? [], function($factor) {
            return in_array($factor, array_keys(self::SEVERITY_MODIFIERS['risk_factor']));
        });
        $sanitized['userId'] = filter_var($data['userId'] ?? null, FILTER_VALIDATE_INT);
        return $sanitized;
    }

    /**
     * Preload symptom weights for better performance
     */
    private function loadSymptomWeights() {
        $stmt = $this->conn->prepare("
            SELECT cs.condition_id, s.name, cs.weight, s.category
            FROM condition_symptoms cs
            JOIN symptoms s ON cs.symptom_id = s.symptom_id
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $this->symptomWeightCache[$row['condition_id']][] = [
                'name' => $row['name'],
                'weight' => $row['weight'],
                'category' => $row['category']
            ];
        }
    }

    /**
     * Enhanced match score calculation with symptom relationships
     */
    private function calculateMatchScore($conditionId, $userSymptoms) {
        if (!isset($this->symptomWeightCache[$conditionId])) {
            return 0;
        }

        $matchScore = 0;
        $totalWeight = 0;
        $categoryMatches = [];
        
        foreach ($this->symptomWeightCache[$conditionId] as $symptom) {
            $totalWeight += $symptom['weight'];
            
            if (in_array(strtolower($symptom['name']), $userSymptoms)) {
                $matchScore += $symptom['weight'];
                $categoryMatches[$symptom['category']] = ($categoryMatches[$symptom['category']] ?? 0) + 1;
            }
        }

        // Apply category bonus for multiple symptoms in same category
        foreach ($categoryMatches as $matches) {
            if ($matches > 1) {
                $matchScore *= (1 + ($matches * 0.1)); // 10% bonus per additional symptom in same category
            }
        }
        
        return $totalWeight > 0 ? ($matchScore / $totalWeight) * 100 : 0;
    }

    /**
     * Enhanced severity calculation with multiple factors
     */
    private function calculateSeverity($duration, $symptomCount, $matchScore) {
        $severityBase = 30;
        
        // Duration factor
        $durationMultiplier = [
            '1' => 0.8,  // Less than 24 hours
            '2' => 1.0,  // 1-3 days
            '3' => 1.2,  // 4-7 days
            '4' => 1.5   // More than a week
        ];
        
        $ageFactor = self::SEVERITY_MODIFIERS['age_factor'][$this->data['age']] ?? 1.0;
        
        // Calculate risk factor multiplier
        $riskMultiplier = 1.0;
        foreach ($this->data['riskFactors'] as $factor) {
            $riskMultiplier *= self::SEVERITY_MODIFIERS['risk_factor'][$factor] ?? 1.0;
        }
        
        $durationFactor = $durationMultiplier[$duration] ?? 1.0;
        $symptomFactor = min(($symptomCount / 10), 1.5);
        $matchFactor = $matchScore / 100;
        
        $severity = $severityBase * $durationFactor * $symptomFactor * $matchFactor * $ageFactor * $riskMultiplier;
        
        return min(100, max(0, round($severity)));
    }

    /**
     * Get personalized recommendations based on severity and risk factors
     */
    private function getRecommendations($conditionId, $severity) {
        $stmt = $this->conn->prepare("
            SELECT recommendation, priority 
            FROM recommendations 
            WHERE condition_id = ? 
            ORDER BY priority DESC
        ");
        $stmt->bind_param("i", $conditionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recommendations = [];
        while ($row = $result->fetch_assoc()) {
            $recommendations[] = $row['recommendation'];
        }
        
        // Add severity-specific recommendations
        if ($severity > 70) {
            array_unshift($recommendations, "Seek immediate medical attention");
        }
        
        // Add risk factor specific recommendations
        foreach ($this->data['riskFactors'] as $factor) {
            $recommendations[] = $this->getRiskFactorRecommendation($factor);
        }
        
        return array_unique($recommendations);
    }

    private function getRiskFactorRecommendation($factor) {
        $recommendations = [
            'smoking' => 'Consider smoking cessation programs to improve overall health',
            'obesity' => 'Consult with a nutritionist for weight management guidance',
            'diabetes' => 'Monitor blood sugar levels regularly',
            'hypertension' => 'Regular blood pressure monitoring is recommended'
        ];
        return $recommendations[$factor] ?? '';
    }

    /**
     * Main diagnosis function with confidence thresholds
     */
    public function diagnose() {
        try {
            $allSymptoms = array_merge([$this->data['mainSymptom']], $this->data['symptoms'] ?? []);
            
            $stmt = $this->conn->prepare("SELECT * FROM conditions");
            $stmt->execute();
            $conditions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $matches = [];
            foreach ($conditions as $condition) {
                $matchScore = $this->calculateMatchScore($condition['condition_id'], $allSymptoms);
                
                if ($matchScore >= self::MIN_CONFIDENCE_THRESHOLD) {
                    $matches[] = [
                        'condition' => $condition,
                        'score' => $matchScore
                    ];
                }
            }
            
            // Sort matches by score
            usort($matches, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            if (empty($matches)) {
                return $this->generateDefaultResponse();
            }
            
            $bestMatch = $matches[0];
            $severity = $this->calculateSeverity(
                $this->data['duration'],
                count($allSymptoms),
                $bestMatch['score']
            );
            
            $recommendations = $this->getRecommendations($bestMatch['condition']['condition_id'], $severity);
            
            if (!empty($this->data['userId'])) {
                $this->saveDiagnosis($bestMatch['condition']['condition_id'], $severity);
            }
            
            return [
                'condition' => $bestMatch['condition']['name'],
                'description' => $bestMatch['condition']['description'],
                'severity' => $severity,
                'confidence' => round($bestMatch['score']),
                'recommendations' => $recommendations,
                'urgent' => $severity > 70,
                'differential_diagnoses' => $this->getDifferentialDiagnoses($matches),
                'risk_factors' => $this->data['riskFactors']
            ];
            
        } catch (Exception $e) {
            throw new Exception('Error during diagnosis: ' . $e->getMessage());
        }
    }

    /**
     * Get differential diagnoses from other high-scoring matches
     */
    private function getDifferentialDiagnoses($matches) {
        $differential = [];
        for ($i = 1; $i < min(4, count($matches)); $i++) {
            if ($matches[$i]['score'] > self::MIN_CONFIDENCE_THRESHOLD) {
                $differential[] = [
                    'condition' => $matches[$i]['condition']['name'],
                    'confidence' => round($matches[$i]['score'])
                ];
            }
        }
        return $differential;
    }

    /**
     * Save diagnosis to history
     */
    private function saveDiagnosis($conditionId, $severity) {
        $stmt = $this->conn->prepare("
            INSERT INTO diagnosis_history 
            (user_id, condition_id, severity, main_symptom, additional_symptoms) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $userId = $this->data['userId'];
        $mainSymptom = $this->data['mainSymptom'];
        $additionalSymptoms = json_encode($this->data['symptoms'] ?? []);
        
        $stmt->bind_param("iiiss", 
            $userId, 
            $conditionId, 
            $severity, 
            $mainSymptom, 
            $additionalSymptoms
        );
        
        $stmt->execute();
    }

    /**
     * Generate default response when no match is found
     */
    private function generateDefaultResponse() {
        return [
            'condition' => 'Unspecified Condition',
            'description' => 'Based on the provided symptoms, a specific condition could not be determined.',
            'severity' => 30,
            'confidence' => 0,
            'recommendations' => [
                'Consult with a healthcare professional for proper diagnosis',
                'Monitor your symptoms and seek immediate care if they worsen',
                'Keep a detailed record of your symptoms and their progression'
            ],
            'urgent' => false
        ];
    }
}

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    // Get and validate input data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['mainSymptom']) || empty($data['duration'])) {
        throw new Exception('Invalid or missing required data');
    }

    // Initialize diagnosis system and get results
    $diagnosisSystem = new DiagnosisSystem($conn, $data);
    $result = $diagnosisSystem->diagnose();

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

// Close database connection
if (isset($conn)) {
    $conn->close();
}