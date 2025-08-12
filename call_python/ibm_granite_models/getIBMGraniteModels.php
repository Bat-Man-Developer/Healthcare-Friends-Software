<?php
/**
 * IBM Granite Models PHP Interface for Medical Diagnosis
 */

// Clear any previous output and set headers
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

// Turn off error display for clean JSON output
ini_set('display_errors', 0);
ini_set('log_errors', 1);

class IBMGraniteModels
{
    private $pythonExePath = "c:/Users/user/anaconda3/python.exe";
    private $scriptPath = "c:/Xampp/htdocs/healthcarefriends-website/python/ibm_granite_models/ibmGraniteModelsApi.py";
    private $logFile = "c:/Xampp/htdocs/healthcarefriends-website/logs/healthcheckup.log";

    public function __construct()
    {
        // Ensure log directory exists
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    private function logMessage($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($this.logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    private function executeCommand($modelType, $prompt)
    {
        try {
            // Create a temporary prompt file to handle large prompts and special characters
            $tempPromptFile = tempnam(sys_get_temp_dir(), 'medical_prompt_');
            if ($tempPromptFile === false) {
                throw new Exception('Failed to create temporary prompt file');
            }
            
            if (file_put_contents($tempPromptFile, $prompt) === false) {
                unlink($tempPromptFile);
                throw new Exception('Failed to write prompt file');
            }
            
            $escapedPythonScript = escapeshellarg($this->scriptPath);
            $escapedModelType = escapeshellarg($modelType);
            $escapedPromptFile = escapeshellarg($tempPromptFile);
            
            // Use file-based approach for better handling of large prompts
            $fullCommand = $this->pythonExePath . " " . $escapedPythonScript . " " . $escapedModelType . " " . $escapedPromptFile . " 2>nul";
            
            $this->logMessage("Executing medical diagnosis command");
            
            $output = shell_exec($fullCommand);
            
            // Clean up temp file
            if (file_exists($tempPromptFile)) {
                unlink($tempPromptFile);
            }
            
            return $output;
        } catch (Exception $e) {
            throw new Exception('Command execution error: ' . $e->getMessage());
        }
    }

    public function getModelResponse($modelType, $prompt)
    {
        try {
            $this->logMessage("Medical diagnosis request - Type: {$modelType}, Prompt length: " . strlen($prompt));

            // Validate inputs
            if (empty($modelType) || empty($prompt)) {
                return [
                    'success' => false,
                    'error' => 'Model type and prompt are required'
                ];
            }

            $validModels = ['granite33', 'granite40'];
            if (!in_array($modelType, $validModels)) {
                return [
                    'success' => false,
                    'error' => 'Invalid model type. Must be granite33 or granite40'
                ];
            }

            // Increased limit for medical prompts
            if (strlen($prompt) > 10000) {
                return [
                    'success' => false,
                    'error' => 'Prompt too long. Maximum 10000 characters allowed'
                ];
            }

            // Check if Python script exists
            if (!file_exists($this->scriptPath)) {
                return [
                    'success' => false,
                    'error' => 'Medical diagnosis script not found at: ' . $this->scriptPath
                ];
            }

            // Check if Python executable exists
            if (!file_exists($this->pythonExePath)) {
                return [
                    'success' => false,
                    'error' => 'Python executable not found at: ' . $this->pythonExePath
                ];
            }

            // Execute the command
            $output = $this->executeCommand($modelType, $prompt);

            if ($output === null || $output === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to execute medical diagnosis script'
                ];
            }

            $output = trim($output);
            if (empty($output)) {
                return [
                    'success' => false,
                    'error' => 'Medical diagnosis script produced no output'
                ];
            }

            $this->logMessage("Python script output received: " . substr($output, 0, 200) . "...");

            // Extract JSON from output (last line should be JSON)
            $lines = explode("\n", $output);
            $jsonLine = end($lines);
            
            // Try to decode JSON output
            $result = json_decode($jsonLine, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => 'Invalid JSON response from medical diagnosis script. Error: ' . json_last_error_msg()
                ];
            }

            // If Python script returned an error
            if (isset($result['error'])) {
                $this->logMessage("Python script error: " . $result['error']);
                return [
                    'success' => false,
                    'error' => $result['error']
                ];
            }

            // Return successful response
            $this->logMessage("Successful medical diagnosis response received");
            return [
                'success' => true,
                'response' => $result['response'] ?? 'No response from model',
                'model_type' => $modelType,
                'timestamp' => time(),
                'processing_time' => $result['processing_time'] ?? null
            ];
        } catch (Exception $e) {
            $this->logMessage("Error in getModelResponse: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

try {
    // Get and decode JSON input
    $input = file_get_contents('php://input');
    if ($input === false) {
        throw new Exception('Failed to read input');
    }
    
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    $modelType = $data['model_type'] ?? '';
    $prompt = $data['prompt'] ?? '';

    $graniteModels = new IBMGraniteModels();
    $result = $graniteModels->getModelResponse($modelType, $prompt);
    
    // Clean output buffer before sending JSON
    ob_end_clean();
    echo json_encode($result);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => time()
    ]);
}