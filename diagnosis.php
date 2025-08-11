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
    <link rel="stylesheet" href="css/diagnosis.css">
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
    <div id="particles"></div>

    <div class="dashboard-container">
        <?php require_once 'layouts/sidebar.php'; ?>

        <main class="main-content">
            <form class="diagnosis-form" id="diagnosisForm">
                <h2 class="section-title"><br>Free Symptom Assessment</h2>
                
                <!-- Privacy Notice -->
                <div class="privacy-notice">
                    <h4>Your Privacy Matters</h4>
                    <p>This free assessment only collects essential information needed for symptom evaluation. 
                    We do not store personal identifying information like names, addresses, or contact details.</p>
                </div>

                <!-- Emergency Warning -->
                <div class="emergency-warning bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex items-center">
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                If you are experiencing chest pain, severe breathing difficulties, or other life-threatening symptoms, 
                                please call emergency services (112) immediately or visit your nearest emergency room.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Basic Information (Essential Only) -->
                <section class="form-section">
                    <h3 class="section-title">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="ageRange">Age Range</label>
                            <select id="ageRange" class="form-control" required>
                                <option value="">Select age range</option>
                                <option value="0-12">0-12 years</option>
                                <option value="13-17">13-17 years</option>
                                <option value="18-29">18-29 years</option>
                                <option value="30-49">30-49 years</option>
                                <option value="50-64">50-64 years</option>
                                <option value="65+">65+ years</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="biologicalSex">Biological Sex</label>
                            <select id="biologicalSex" class="form-control" required>
                                <option value="">Select biological sex</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Medical History (Essential Only) -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Relevant Medical History</h3>
                    
                    <div class="form-group">
                        <label class="font-semibold">Do you have any of these conditions?</label>
                        <div class="conditions-grid grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="diabetes">
                                <span>Diabetes</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="hypertension">
                                <span>High Blood Pressure</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="heart_disease">
                                <span>Heart Disease</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="asthma">
                                <span>Asthma</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="cancer_history">
                                <span>Cancer History</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="autoimmune">
                                <span>Autoimmune Disease</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="form-group">
                            <label for="allergies">Known Allergies (if any)</label>
                            <textarea id="allergies" class="form-control" rows="2"
                                placeholder="List any known allergies (medications, food, environmental)"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="currentMedications">Current Medications</label>
                            <textarea id="currentMedications" class="form-control" rows="2"
                                placeholder="List current medications (optional but helpful for assessment)"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Current Symptoms -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Current Symptoms</h3>
                    
                    <div class="form-group">
                        <label for="mainSymptom">Primary Symptom or Concern</label>
                        <input type="text" id="mainSymptom" class="form-control" required
                            placeholder="What is your main symptom or concern?">
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-semibold">Additional Symptoms (check all that apply)</label>
                        <div class="symptoms-grid grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- General Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">General</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="fever">
                                    <span>Fever</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="fatigue">
                                    <span>Fatigue</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="weakness">
                                    <span>Weakness</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="weight_loss">
                                    <span>Unexplained Weight Loss</span>
                                </label>
                            </div>

                            <!-- Pain Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Pain</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="headache">
                                    <span>Headache</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="chest_pain">
                                    <span>Chest Pain</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="abdominal_pain">
                                    <span>Abdominal Pain</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="joint_pain">
                                    <span>Joint Pain</span>
                                </label>
                            </div>

                            <!-- Respiratory Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Respiratory</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="cough">
                                    <span>Cough</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="shortness_breath">
                                    <span>Shortness of Breath</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="wheezing">
                                    <span>Wheezing</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="sore_throat">
                                    <span>Sore Throat</span>
                                </label>
                            </div>

                            <!-- Gastrointestinal Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Digestive</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="nausea">
                                    <span>Nausea</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="vomiting">
                                    <span>Vomiting</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="diarrhea">
                                    <span>Diarrhea</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="constipation">
                                    <span>Constipation</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="form-group">
                            <label for="symptomDuration">How long have you had these symptoms?</label>
                            <select id="symptomDuration" class="form-control" required>
                                <option value="">Select duration</option>
                                <option value="1">Less than 24 hours</option>
                                <option value="2">1-3 days</option>
                                <option value="3">4-7 days</option>
                                <option value="4">1-2 weeks</option>
                                <option value="5">More than 2 weeks</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="severity">Severity Level (1 = Mild, 10 = Severe)</label>
                            <div class="severity-slider-container">
                                <input type="range" id="severity" min="1" max="10" value="5" class="severity-slider" required>
                                <div class="severity-labels">
                                    <span>Mild (1-3)</span>
                                    <span>Moderate (4-7)</span>
                                    <span>Severe (8-10)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="symptomDescription">Additional Details (Optional)</label>
                        <textarea id="symptomDescription" class="form-control" rows="3" 
                            placeholder="Describe any patterns, triggers, or other relevant information about your symptoms"></textarea>
                    </div>
                </section>

                <!-- Basic Lifestyle Factors -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Lifestyle Factors</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label for="smoking">Smoking Status</label>
                            <select id="smoking" class="form-control">
                                <option value="never">Never Smoked</option>
                                <option value="former">Former Smoker</option>
                                <option value="current">Current Smoker</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="alcohol">Alcohol Use</label>
                            <select id="alcohol" class="form-control">
                                <option value="none">None</option>
                                <option value="occasional">Occasional</option>
                                <option value="moderate">Moderate</option>
                                <option value="heavy">Heavy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exercise">Exercise Level</label>
                            <select id="exercise" class="form-control">
                                <option value="none">Rarely/Never</option>
                                <option value="light">Light (1-2 times/week)</option>
                                <option value="moderate">Moderate (3-4 times/week)</option>
                                <option value="active">Very Active (5+ times/week)</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Consent and Submission -->
                <section class="form-section mt-8">
                    <div class="consent-box bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-start space-x-2">
                            <input type="checkbox" required class="mt-1">
                            <span class="text-sm">
                                I understand this is a free symptom assessment tool that provides general health information only. 
                                This assessment is not a substitute for professional medical advice, diagnosis, or treatment. 
                                For emergencies, I will call emergency services immediately.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="submit-btn mt-6 w-full md:w-auto">
                        Get Free Assessment
                    </button>
                </section>
            </form>

            <div class="results-container" style="display: none;">
                <h2>Assessment Results</h2>
                
                <!-- Severity Indicator -->
                <div class="severity-container">
                    <h3>Assessed Urgency Level</h3>
                    <div class="severity-bar">
                        <div id="severityFill" class="severity-fill"></div>
                    </div>
                    <div id="severityText" class="severity-text"></div>
                </div>

                <!-- Assessment Result -->
                <div id="assessmentResult" class="diagnosis-details">
                    <!-- This will be populated by JavaScript -->
                </div>

                <!-- Recommendations -->
                <div class="recommendations">
                    <h3>Recommendations</h3>
                    <ul id="recommendationsList" class="recommendation-list">
                        <!-- This will be populated by JavaScript -->
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add security measures for JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Sanitize all user inputs
            const inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    if (typeof DOMPurify !== 'undefined') {
                        this.value = DOMPurify.sanitize(this.value);
                    }
                });
            });

            // Add CSRF token to all AJAX requests
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            }
        });
    </script>
    <script src="js/diagnosis.js"></script>
    <script src="js/main.js"></script>
</body>
</html>