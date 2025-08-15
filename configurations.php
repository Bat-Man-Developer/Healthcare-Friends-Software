<?php
// Configurations Page
// Start session with enhanced security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Set security headers
header("Content-Security-Policy: default-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com https://cdnjs.cloudflare.com; script-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

include('server/getconfigurations.php');

// Get current configurations
$configs = [];
try {
    $result = $conn->query("SELECT config_key, config_value FROM configurations");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $configs[$row['config_key']] = $row['config_value'];
        }
    }
} catch (Exception $e) {
    error_log("Error fetching configurations: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <!-- Add CSRF token meta -->
    <meta name="csrf-token" content="<?php echo htmlspecialchars(hash_hmac('sha256', session_id(), 'Blackkarmaholyspirit.01234?')); ?>">
    <title>HealthCare Friends - Configurations</title>
    <link rel="icon" href="assets/images/FCS_Holdix_Logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/configurations.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js" integrity="sha512-vJ3hR5OeYZ5dB6U5/3eBoTEfH9Nz+IQwFOk/7ixBHZY1T4cWlPOZ0QeYqziIFbUGA5g/Kjf/p9zrXr3D5K6JA==" crossorigin="anonymous"></script>
    <script>
        // Add security measures for JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
</head>
<body>
    <div class="floating-particles" id="particles"></div>
    <!-- Navigation Bar -->
    <?php require_once 'layouts/navbar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Model Configurations</h1>
            <p class="page-subtitle">Configure your AI model settings and API endpoints</p>
        </div>

        <div class="config-container">
            <form id="configForm">
                <div class="config-grid">
                    <div class="form-group">
                        <label for="project_id" class="form-label">Project ID</label>
                        <input type="text" id="project_id" name="project_id" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['project_id'] ?? ''); ?>" 
                               placeholder="Enter your project ID">
                    </div>

                    <div class="form-group">
                        <label for="endpoint_url" class="form-label">Endpoint URL</label>
                        <input type="url" id="endpoint_url" name="endpoint_url" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['endpoint_url'] ?? ''); ?>" 
                               placeholder="https://us-south.ml.cloud.ibm.com">
                    </div>

                    <div class="form-group">
                        <label for="api_key" class="form-label">API Key</label>
                        <input type="password" id="api_key" name="api_key" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['api_key'] ?? ''); ?>" 
                               placeholder="Enter your API key">
                    </div>

                    <div class="form-group">
                        <label for="granite33_model" class="form-label">Granite 3.3 Model</label>
                        <input type="text" id="granite33_model" name="granite33_model" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['granite33_model'] ?? ''); ?>" 
                               placeholder="ibm/granite-3-3-8b-instruct">
                    </div>

                    <div class="form-group">
                        <label for="granite40_model" class="form-label">Granite 4.0 Model</label>
                        <input type="text" id="granite40_model" name="granite40_model" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['granite40_model'] ?? ''); ?>" 
                               placeholder="ibm/granite-4-0-tiny">
                    </div>

                    <div class="form-group">
                        <label for="iam_token" class="form-label">IAM Token</label>
                        <textarea id="iam_token" name="iam_token" class="form-input form-textarea" 
                                  placeholder="Enter your IAM token"><?php echo htmlspecialchars($configs['iam_token'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        💾 Save Configuration
                    </button>
                    <button type="button" class="btn btn-secondary" id="testBtn">
                        🔗 Test Connection
                    </button>
                </div>

                <div class="test-result" id="testResult"></div>
            </form>
        </div>
    </div>

    <!-- footer.php -->
    <?php require_once 'layouts/footer.php'; ?>

    <script src="js/configurations.js"></script>
    <script src="js/main.js"></script>
</body>
</html>