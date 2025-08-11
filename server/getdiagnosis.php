<?php
// Diagnosis API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once('connection.php');

class FreeAssessmentSystem {
    private $conn;
    private $data;
    private $symptomWeightCache = [];
    private const MIN_CONFIDENCE_THRESHOLD = 25;
    private const SEVERITY_MODIFIERS = [
        'age_factor' => [
            '0-12' => 1.3,
            '13-17' => 1.1,
            '18-29' => 1.0,
            '30-49' => 1.0,
            '50-64' => 1.1,
            '65+' => 1.3
        ],
        'risk_factor' => [
            'diabetes' => 1.2,
            'hypertension' => 1.15,
            'heart_disease' => 1.3,
            'asthma' => 1.1,
            'cancer_history' => 1.25,
            'autoimmune' => 1.2,
            'current' => 1.2, // smoking
            'heavy' => 1.15   // alcohol
        ]
    ];
    
    public function __construct($conn, $data) {
        $this->conn = $conn;
        $this->data = $this->sanitizeInput($data);
        $this->loadSymptomWeights();
    }

    /**
     * Sanitize and validate input data (no personal info stored)
     */
    private function sanitizeInput($data) {
        $sanitized = [];
        $sanitized['mainSymptom'] = strip_tags(strtolower(trim($data['mainSymptom'])));
        $sanitized['duration'] = filter_var($data['duration'], FILTER_VALIDATE_INT);
        $sanitized['severity'] = filter_var($data['severity'], FILTER_VALIDATE_INT);
        $sanitized['symptoms'] = array_map(function($symptom) {
            return strip_tags(strtolower(trim($symptom)));
        }, $data['symptoms'] ?? []);
        $sanitized['ageRange'] = strip_tags($data['ageRange'] ?? '18-29');
        $sanitized['biologicalSex'] = strip_tags($data['biologicalSex'] ?? '');
        $sanitized['conditions'] = array_filter($data['conditions'] ?? []);
        $sanitized['smoking'] = strip_tags($data['smoking'] ?? 'never');
        $sanitized['alcohol'] = strip_tags($data['alcohol'] ?? 'none');
        $sanitized['exercise'] = strip_tags($data['exercise'] ?? 'none');
        
        return $sanitized;
    }

    /**
     * Load symptom weights for assessment
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
     * Calculate match score based on symptoms
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

        // Bonus for multiple symptoms in same category
        foreach ($categoryMatches as $matches) {
            if ($matches > 1) {
                $matchScore *= (1 + ($matches * 0.1));
            }
        }
        
        return $totalWeight > 0 ? ($matchScore / $totalWeight) * 100 : 0;
    }

    /**
     * Calculate urgency level (not severity to avoid medical diagnosis)
     */
    private function calculateUrgencyLevel($duration, $userSeverity, $matchScore) {
        $baseUrgency = 20;
        
        // Duration factor
        $durationMultiplier = [
            '1' => 0.9,  // Less than 24 hours
            '2' => 1.0,  // 1-3 days
            '3' => 1.1,  // 4-7 days
            '4' => 1.3,  // 1-2 weeks
            '5' => 1.5   // More than 2 weeks
        ];
        
        $ageFactor = self::SEVERITY_MODIFIERS['age_factor'][$this->data['ageRange']] ?? 1.0;
        
        // Calculate risk factor multiplier
        $riskMultiplier = 1.0;
        foreach ($this->data['conditions'] as $condition) {
            $riskMultiplier *= self::SEVERITY_MODIFIERS['risk_factor'][$condition] ?? 1.0;
        }
        
        // Add lifestyle risk factors
        $riskMultiplier *= self::SEVERITY_MODIFIERS['risk_factor'][$this->data['smoking']] ?? 1.0;
        $riskMultiplier *= self::SEVERITY_MODIFIERS['risk_factor'][$this->data['alcohol']] ?? 1.0;
        
        $durationFactor = $durationMultiplier[$duration] ?? 1.0;
        $severityFactor = $userSeverity / 10;
        $matchFactor = $matchScore / 100;
        
        $urgency = $baseUrgency * $durationFactor * $severityFactor * $matchFactor * $ageFactor * $riskMultiplier;
        
        return min(100, max(0, round($urgency)));
    }

    /**
     * Get general recommendations (not medical advice)
     */
    private function getRecommendations($conditionId, $urgencyLevel) {
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
        
        // Add urgency-specific recommendations
        if ($urgencyLevel > 70) {
            array_unshift($recommendations, "Consider seeking immediate medical attention");
        } elseif ($urgencyLevel > 50) {
            array_unshift($recommendations, "Consider scheduling an appointment with a healthcare provider soon");
        } else {
            array_unshift($recommendations, "Monitor symptoms and consider consulting a healthcare provider if they worsen");
        }
        
        // Add general wellness recommendations
        $recommendations[] = "Stay hydrated and get adequate rest";
        $recommendations[] = "Keep track of symptom changes";
        
        return array_unique($recommendations);
    }

    /**
     * Main assessment function (provides information, not diagnosis)
     */
    public function assess() {
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
            $urgencyLevel = $this->calculateUrgencyLevel(
                $this->data['duration'],
                $this->data['severity'],
                $bestMatch['score']
            );
            
            $recommendations = $this->getRecommendations($bestMatch['condition']['condition_id'], $urgencyLevel);
            
            // No personal data storage for free service
            
            return [
                'possible_condition' => $bestMatch['condition']['name'],
                'information' => $bestMatch['condition']['description'],
                'urgency_level' => $urgencyLevel,
                'match_confidence' => round($bestMatch['score']),
                'recommendations' => $recommendations,
                'seek_immediate_care' => $urgencyLevel > 70,
                'disclaimer' => 'This assessment provides general information only and is not a medical diagnosis.',
                'alternative_possibilities' => $this->getAlternativePossibilities($matches)
            ];
            
        } catch (Exception $e) {
            throw new Exception('Error during assessment: ' . $e->getMessage());
        }
    }

    /**
     * Get alternative possibilities
     */
    private function getAlternativePossibilities($matches) {
        $alternatives = [];
        for ($i = 1; $i < min(3, count($matches)); $i++) {
            if ($matches[$i]['score'] > self::MIN_CONFIDENCE_THRESHOLD) {
                $alternatives[] = [
                    'condition' => $matches[$i]['condition']['name'],
                    'confidence' => round($matches[$i]['score'])
                ];
            }
        }
        return $alternatives;
    }

    /**
     * Generate default response when no clear match
     */
    private function generateDefaultResponse() {
        return [
            'possible_condition' => 'Non-specific symptoms',
            'information' => 'Based on the symptoms provided, a specific condition pattern could not be identified.',
            'urgency_level' => 30,
            'match_confidence' => 0,
            'recommendations' => [
                'Consider consulting with a healthcare professional for proper evaluation',
                'Monitor symptoms and note any changes or worsening',
                'Ensure adequate rest and hydration',
                'Seek immediate care if symptoms become severe or concerning'
            ],
            'seek_immediate_care' => false,
            'disclaimer' => 'This assessment provides general information only and is not a medical diagnosis.'
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

    // Initialize assessment system and get results
    $assessmentSystem = new FreeAssessmentSystem($conn, $data);
    $result = $assessmentSystem->assess();

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