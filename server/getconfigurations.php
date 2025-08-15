<?php
// Get Configurations
require_once('connection.php');

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = hash_hmac('sha256', session_id(), 'Blackkarmaholyspirit.01234?');
}

// CSRF protection function
function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Validate CSRF token
    $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
    if (!validateCSRF($csrfToken)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'save_config') {
        try {
            $configs = [
                'project_id' => trim($_POST['project_id'] ?? ''),
                'endpoint_url' => trim($_POST['endpoint_url'] ?? ''),
                'api_key' => trim($_POST['api_key'] ?? ''),
                'granite33_model' => trim($_POST['granite33_model'] ?? ''),
                'granite40_model' => trim($_POST['granite40_model'] ?? ''),
                'iam_token' => trim($_POST['iam_token'] ?? '')
            ];

            $conn->begin_transaction();

            foreach ($configs as $key => $value) {
                if (!empty($value)) {
                    $stmt = $conn->prepare("INSERT INTO configurations (config_key, config_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE config_value = ?, updated_at = NOW()");
                    $stmt->bind_param("sss", $key, $value, $value);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Configuration saved successfully']);

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Configuration save error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to save configuration']);
        }
    }

    if ($action === 'test_connection') {
        try {
            $endpoint_url = trim($_POST['endpoint_url'] ?? '');
            $api_key = trim($_POST['api_key'] ?? '');
            $iam_token = trim($_POST['iam_token'] ?? '');
            $project_id = trim($_POST['project_id'] ?? '');
            $model = trim($_POST['granite33_model'] ?? '');

            if (empty($endpoint_url) || empty($project_id) || empty($model)) {
                echo json_encode(['success' => false, 'message' => 'Missing required configuration fields']);
                exit;
            }

            if (empty($api_key) && empty($iam_token)) {
                echo json_encode(['success' => false, 'message' => 'Either API key or IAM token is required']);
                exit;
            }

            // Use IAM token if available, otherwise generate from API key
            $access_token = $iam_token;
            
            if (empty($access_token) && !empty($api_key)) {
                // Generate IAM token from API key
                $iam_url = 'https://iam.cloud.ibm.com/identity/token';
                $iam_headers = ['Content-Type: application/x-www-form-urlencoded'];
                $iam_data = http_build_query([
                    'grant_type' => 'urn:iam:params:oauth:grant-type:apikey',
                    'apikey' => $api_key
                ]);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $iam_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $iam_data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $iam_headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $iam_response = curl_exec($ch);
                $iam_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($iam_http_code === 200) {
                    $iam_data = json_decode($iam_response, true);
                    $access_token = $iam_data['access_token'] ?? '';
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to authenticate with IBM Cloud']);
                    exit;
                }
            }

            // Test API connection
            $url = rtrim($endpoint_url, '/') . '/ml/v1/text/generation?version=2023-05-29';
            
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $access_token
            ];

            $data = [
                'input' => 'Test connection',
                'parameters' => [
                    'decoding_method' => 'greedy',
                    'max_new_tokens' => 10,
                    'temperature' => 0.1
                ],
                'model_id' => $model,
                'project_id' => $project_id
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                echo json_encode(['success' => false, 'message' => 'Connection error: ' . $error]);
            } elseif ($httpCode === 200) {
                $responseData = json_decode($response, true);
                if (isset($responseData['results'])) {
                    echo json_encode(['success' => true, 'message' => 'Connection test successful! Model is responding correctly.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unexpected response format from API']);
                }
            } elseif ($httpCode === 401) {
                echo json_encode(['success' => false, 'message' => 'Authentication failed. Please check your API key/IAM token.']);
            } elseif ($httpCode === 403) {
                echo json_encode(['success' => false, 'message' => 'Access forbidden. Please check your project ID and permissions.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'API returned HTTP ' . $httpCode . ': ' . $response]);
            }

        } catch (Exception $e) {
            error_log("Connection test error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Connection test failed: ' . $e->getMessage()]);
        }
    }

    exit;
}

// Load configurations for display
$configs = [];
try {
    $stmt = $conn->prepare("SELECT config_key, config_value FROM configurations");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $configs[$row['config_key']] = $row['config_value'];
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Failed to load configurations: " . $e->getMessage());
}