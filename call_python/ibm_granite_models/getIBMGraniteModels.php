<?php
/**
 * Get IBM Granite Models Interface
 */

// Clear any previous output and set headers
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

// Turn off error display for clean JSON output
ini_set('display_errors', 0);
ini_set('log_errors', 1);

class IBMGraniteConnectionTest
{
    private $pythonExePath = "c:/Users/user/anaconda3/python.exe";
    private $scriptPath = "c:/Xampp/htdocs/Business Websites/fcsholdix-healthcare-website/python/ibm_granite_models/ibmGraniteModelsApi.py";
    private $logFile = "c:/Xampp/htdocs/Business Websites/fcsholdix-healthcare-website/logs/connection_test.log";
    private $configPath = "c:/Xampp/htdocs/Business Websites/fcsholdix-healthcare-website/config/ibm_config.json";

    public function __construct()
    {
        // Convert relative paths to absolute
        $this->scriptPath = realpath($this->scriptPath);
        
        // Ensure directories exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $configDir = dirname($this->configPath);
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }
    }

    private function logMessage($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    private function loadConfigFromDB()
    {
        try {
            require_once('c:/Xampp/htdocs/Business Websites/fcsholdix-healthcare-website/server/connection.php');

            $config = [];
            $configKeys = ['project_id', 'endpoint_url', 'api_key', 'granite33_model', 'granite40_model', 'iam_token'];
            
            foreach ($configKeys as $key) {
                $stmt = $conn->prepare("SELECT config_value FROM configurations WHERE config_key = ?");
                $stmt->bind_param("s", $key);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $config[$key] = $row['config_value'];
                } else {
                    $config[$key] = '';
                }
                $stmt->close();
            }
            
            // Save config to file for Python script
            file_put_contents($this->configPath, json_encode($config, JSON_PRETTY_PRINT));
            
            return $config;
        } catch (Exception $e) {
            $this->logMessage("Failed to load config from DB: " . $e->getMessage());
            return [];
        }
    }

    private function executeConnectionTest($modelType, $prompt)
    {
        try {
            // Load configuration from database
            $config = $this->loadConfigFromDB();
            
            if (empty($config['project_id'])) {
                throw new Exception('Project ID not configured. Please configure IBM Watson settings.');
            }
            
            if (empty($config['api_key']) && empty($config['iam_token'])) {
                throw new Exception('Neither API key nor IAM token configured. Please provide authentication credentials.');
            }
            
            // Create temporary files
            $tempPromptFile = tempnam(sys_get_temp_dir(), 'connection_test_');
            if ($tempPromptFile === false) {
                throw new Exception('Failed to create temporary prompt file');
            }
            
            if (file_put_contents($tempPromptFile, $prompt) === false) {
                unlink($tempPromptFile);
                throw new Exception('Failed to write prompt file');
            }
            
            // Prepare command arguments
            $escapedPythonScript = escapeshellarg($this->scriptPath);
            $escapedModelType = escapeshellarg($modelType);
            $escapedPromptFile = escapeshellarg($tempPromptFile);
            $escapedConfigFile = escapeshellarg($this->configPath);
            
            // Build the command
            $fullCommand = $this->pythonExePath . " " . $escapedPythonScript . " " . $escapedModelType . " " . $escapedPromptFile . " " . $escapedConfigFile . " 2>&1";
            
            $this->logMessage("Executing connection test command: " . $fullCommand);
            
            // Execute the command
            $output = shell_exec($fullCommand);
            
            // Clean up temp files
            if (file_exists($tempPromptFile)) {
                unlink($tempPromptFile);
            }
            
            return $output;
        } catch (Exception $e) {
            throw new Exception('Connection test execution error: ' . $e->getMessage());
        }
    }

    public function testConnection($modelType, $prompt)
    {
        try {
            $this->logMessage("Starting connection test - Type: {$modelType}");

            // Validate inputs
            if (empty($modelType) || empty($prompt)) {
                return [
                    'success' => false,
                    'error' => 'Model type and prompt are required for connection test'
                ];
            }

            $validModels = ['granite33', 'granite40'];
            if (!in_array($modelType, $validModels)) {
                return [
                    'success' => false,
                    'error' => 'Invalid model type. Must be granite33 or granite40'
                ];
            }

            // Check if Python script exists
            if (!file_exists($this->scriptPath)) {
                return [
                    'success' => false,
                    'error' => 'Python script not found at: ' . $this->scriptPath
                ];
            }

            // Check if Python executable exists
            if (!file_exists($this->pythonExePath)) {
                return [
                    'success' => false,
                    'error' => 'Python executable not found at: ' . $this->pythonExePath
                ];
            }

            // Execute the connection test
            $output = $this->executeConnectionTest($modelType, $prompt);

            if ($output === null || $output === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to execute Python connection test script'
                ];
            }

            $output = trim($output);
            if (empty($output)) {
                return [
                    'success' => false,
                    'error' => 'Python script produced no output'
                ];
            }

            $this->logMessage("Python script output: " . substr($output, 0, 500) . "...");

            // Extract JSON from output (last line should be JSON)
            $lines = explode("\n", $output);
            $jsonLine = '';
            
            // Find the last line that looks like JSON
            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $line = trim($lines[$i]);
                if (!empty($line) && (substr($line, 0, 1) === '{' || substr($line, 0, 1) === '[')) {
                    $jsonLine = $line;
                    break;
                }
            }
            
            if (empty($jsonLine)) {
                return [
                    'success' => false,
                    'error' => 'No JSON response found in script output. Output: ' . substr($output, 0, 500)
                ];
            }
            
            // Try to decode JSON output
            $result = json_decode($jsonLine, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => 'Invalid JSON response from Python script. Error: ' . json_last_error_msg() . '. JSON: ' . $jsonLine
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
            $this->logMessage("Connection test successful");
            return [
                'success' => true,
                'message' => 'Connection successful! IBM Granite models are accessible.',
                'response' => $result['response'] ?? 'Test response received',
                'model_type' => $modelType,
                'timestamp' => time(),
                'processing_time' => $result['processing_time'] ?? null
            ];
        } catch (Exception $e) {
            $this->logMessage("Error in testConnection: " . $e->getMessage());
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

    $modelType = $data['model_type'] ?? 'granite33';
    $prompt = $data['prompt'] ?? 'Hello, this is a connection test.';

    $connectionTest = new IBMGraniteConnectionTest();
    $result = $connectionTest->testConnection($modelType, $prompt);
    
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