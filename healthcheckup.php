<?php
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
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
//header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

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
    <title>HealthCare Friends</title>
    <link rel="icon" href="assets/images/FCS_Holdix_Logo.png" type="image/x-icon">
	<link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/healthcheckup.css">
    <link rel="stylesheet" href="css/doctorAvatar3D.css">
    <!-- Add the Three.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Add jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add DOMPurify for sanitization -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify@2.4.0/dist/purify.min.js"></script>
</head>
<body>
    <button class="sidebar-toggle">☰</button>
    <!-- Navigation Bar -->
    <?php require_once 'layouts/navbar.php'; ?>

    <!--------- Website Message ------------>
    <?php if(isset($_GET['error'])){ ?>
        <p class="text-center" id="webmessage_red"><?php if(isset($_GET['error'])){ echo $_GET['error']; }?></p>
    <?php } ?>
    <?php if(isset($_GET['success'])){ ?>
        <p class="text-center" id="webmessage_green"><?php if(isset($_GET['success'])){ echo $_GET['success']; }?></p>
    <?php } ?>

    <!-- Particles Container -->
    <div class="floating-particles" id="particles"></div>

    <?php require_once 'layouts/sidebar.php'; ?>

    <div class="virtual-doctor-container">
        <!-- Doctor Section -->
        <div class="doctor-section">
            <div class="doctor-avatar">👨‍⚕️</div>
            <div class="doctor-info">
                <div class="doctor-name">Dr. AI Assistant</div>
                <div class="doctor-title">Virtual Health Consultant</div>
            </div>
            <div class="speaking-indicator" id="speakingIndicator">
                Doctor is speaking...
            </div>
            
            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-steps">
                    <div class="progress-step active" data-step="1"></div>
                    <div class="progress-step" data-step="2"></div>
                    <div class="progress-step" data-step="3"></div>
                    <div class="progress-step" data-step="4"></div>
                    <div class="progress-step" data-step="5"></div>
                </div>
                <div style="font-size: 0.8rem; margin-top: 0.5rem; text-align: center;">
                    Step <span id="currentStep">1</span> of 5
                </div>
            </div>
        </div>

        <!-- Conversation Section -->
        <div class="conversation-section">
            <div class="conversation-header">
                <h2>Virtual Health Consultation</h2>
                <p>I'm here to help assess your health concerns</p>
            </div>

            <div class="conversation-area" id="conversationArea">
                <div class="chat-message">
                    <div class="doctor-message">
                        Hello! I'm Dr. AI Assistant, your virtual health consultant. I'm here to help assess your health concerns through a comprehensive consultation.
                        <br><br>
                        Before we begin, please note that this is an assessment tool and not a replacement for professional medical care. In case of emergency, please call 112 immediately.
                        <br><br>
                        Let's start with some basic information. What is your age range?
                    </div>
                </div>
            </div>

            <!-- Quick Response Buttons -->
            <div class="quick-responses" id="quickResponses">
                <button class="quick-response-btn" onclick="selectAgeRange('0-12')">0-12 years</button>
                <button class="quick-response-btn" onclick="selectAgeRange('13-17')">13-17 years</button>
                <button class="quick-response-btn" onclick="selectAgeRange('18-29')">18-29 years</button>
                <button class="quick-response-btn" onclick="selectAgeRange('30-49')">30-49 years</button>
                <button class="quick-response-btn" onclick="selectAgeRange('50-64')">50-64 years</button>
                <button class="quick-response-btn" onclick="selectAgeRange('65+')">65+ years</button>
            </div>

            <!-- Input Section -->
            <div class="input-section">
                <div class="input-container">
                    <textarea 
                        class="text-input" 
                        id="patientInput" 
                        placeholder="Type your response here..."
                        rows="1"></textarea>
                    <button class="voice-btn" id="voiceBtn" onclick="toggleVoiceRecording()">🎤</button>
                    <button class="send-btn" id="sendBtn" onclick="sendMessage()">➤</button>
                </div>
            </div>

            <!-- Hidden Diagnosis Form -->
            <div class="diagnosis-form-section" id="diagnosisFormSection">
                <form id="hiddenDiagnosisForm">
                    <input type="hidden" id="ageRange" name="ageRange">
                    <input type="hidden" id="biologicalSex" name="biologicalSex">
                    <input type="hidden" id="mainSymptom" name="mainSymptom">
                    <input type="hidden" id="symptomDuration" name="symptomDuration">
                    <input type="hidden" id="severity" name="severity">
                    <input type="hidden" id="conditions" name="conditions">
                    <input type="hidden" id="allergies" name="allergies">
                    <input type="hidden" id="currentMedications" name="currentMedications">
                    <input type="hidden" id="smoking" name="smoking">
                    <input type="hidden" id="alcohol" name="alcohol">
                    <input type="hidden" id="exercise" name="exercise">
                    <input type="hidden" id="symptomDescription" name="symptomDescription">
                    <input type="hidden" id="symptoms" name="symptoms">
                </form>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div id="resultsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeResults()">&times;</span>
            <div id="resultsContent"></div>
        </div>
    </div>

    <script src="js/doctorAvatar3D.js"></script>
    <script src="js/healthcheckup.js"></script>
    <script src="js/main.js"></script>
</body>
</html>