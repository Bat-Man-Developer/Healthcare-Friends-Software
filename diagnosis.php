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
header("Content-Security-Policy: default-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com https://cdnjs.cloudflare.com; script-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com");
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js" integrity="sha512-vJ3hR5OeYZ5dB6U5/3eBoTEfH9Nz+IQwFOk/7ixBHZY1T4cWlPOZ0QeYqziIFbUGA5g/Kjf/p9zrXr3D5K6JA==" crossorigin="anonymous"></script>
    <!-- Add SRI hashes for local scripts -->
    <script nonce="<?php echo htmlspecialchars(base64_encode(random_bytes(32))); ?>">
        // Add security measures for JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Sanitize all user inputs
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    this.value = DOMPurify.sanitize(this.value);
                });
            });

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
	

    <div class="dashboard-container">
        <?php require_once 'layouts/sidebar.php'; ?>

        <main class="main-content">
            <form class="diagnosis-form" id="diagnosisForm">
                <h2 class="section-title"><br>New Diagnosis</h2>
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

                <!-- Patient Information -->
                <section class="form-section">
                    <h3 class="section-title">Patient Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="fullName">Full Legal Name</label>
                            <input type="text" id="fullName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="preferredName">Preferred Name (if different)</label>
                            <input type="text" id="preferredName" class="form-control">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label for="dateOfBirth">Date of Birth</label>
                            <input type="date" id="dateOfBirth" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender Identity</label>
                            <select id="gender" class="form-control" required>
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="transgender_male">Transgender Male</option>
                                <option value="transgender_female">Transgender Female</option>
                                <option value="non_binary">Non-binary</option>
                                <option value="other">Other</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pronouns">Preferred Pronouns</label>
                            <select id="pronouns" class="form-control">
                                <option value="">Select pronouns</option>
                                <option value="he_him">He/Him</option>
                                <option value="she_her">She/Her</option>
                                <option value="they_them">They/Them</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Home Address</label>
                        <input type="text" id="address" class="form-control" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State/Province</label>
                            <input type="text" id="state" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="zipCode">ZIP/Postal Code</label>
                            <input type="text" id="zipCode" class="form-control" required>
                        </div>
                    </div>
                </section>

                <!-- Insurance Information -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Insurance Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="insuranceProvider">Insurance Provider</label>
                            <input type="text" id="insuranceProvider" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="policyNumber">Policy Number</label>
                            <input type="text" id="policyNumber" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="groupNumber">Group Number (if applicable)</label>
                        <input type="text" id="groupNumber" class="form-control">
                    </div>
                </section>

                <!-- Medical History -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Comprehensive Medical History</h3>
                    
                    <div class="form-group">
                        <label class="font-semibold">Existing Medical Conditions</label>
                        <div class="conditions-grid grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="diabetes">
                                <span>Diabetes</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="hypertension">
                                <span>Hypertension</span>
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
                                <input type="checkbox" name="conditions" value="cancer">
                                <span>Cancer</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="thyroid">
                                <span>Thyroid Disease</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="arthritis">
                                <span>Arthritis</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="mental_health">
                                <span>Mental Health Condition</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="autoimmune">
                                <span>Autoimmune Disease</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="form-group">
                            <label for="otherConditions">Other Medical Conditions</label>
                            <textarea id="otherConditions" class="form-control" rows="3" 
                                placeholder="Please list any other medical conditions not mentioned above"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="surgeries">Previous Surgeries</label>
                            <textarea id="surgeries" class="form-control" rows="3"
                                placeholder="List any previous surgeries with dates"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="allergies">Allergies</label>
                            <textarea id="allergies" class="form-control" rows="3"
                                placeholder="List all known allergies (medications, food, environmental)"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="medications">Current Medications</label>
                            <textarea id="medications" class="form-control" rows="3"
                                placeholder="List all current medications with dosages"></textarea>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-semibold">Family Medical History</label>
                        <div class="family-history-grid grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="familyHeartDisease">Heart Disease</label>
                                <select id="familyHeartDisease" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                            <div>
                                <label for="familyDiabetes">Diabetes</label>
                                <select id="familyDiabetes" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                            <div>
                                <label for="familyCancer">Cancer</label>
                                <select id="familyCancer" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                            <div>
                                <label for="familyMentalHealth">Mental Health Conditions</label>
                                <select id="familyMentalHealth" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Current Symptoms -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Current Symptoms Assessment</h3>
                    
                    <div class="form-group">
                        <label for="mainSymptom">Primary Complaint</label>
                        <input type="text" id="mainSymptom" class="form-control" required
                            placeholder="What is your main symptom or concern?">
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-semibold">Associated Symptoms</label>
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
                                    <span>Weight Loss</span>
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
                                    <input type="checkbox" name="symptoms" value="sputum">
                                    <span>Sputum Production</span>
                                </label>
                            </div>

                            <!-- Gastrointestinal Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Gastrointestinal</h4>
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
                            <label for="symptomOnset">When did symptoms begin?</label>
                            <input type="date" id="symptomOnset" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="duration">Duration of Symptoms</label>
                            <select id="duration" class="form-control" required>
                                <option value="">Select duration</option>
                                <option value="1">Less than 24 hours</option>
                                <option value="2">1-3 days</option>
                                <option value="3">4-7 days</option>
                                <option value="4">More than a week</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="severity">Severity Level</label>
                        <div class="severity-slider-container">
                            <input type="range" id="severity" min="1" max="10" value="5" class="severity-slider" required>
                            <div class="severity-labels">
                                <span>Mild (1-3)</span>
                                <span>Moderate (4-7)</span>
                                <span>Severe (8-10)</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Lifestyle Factors -->
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
                            <label for="alcohol">Alcohol Consumption</label>
                            <select id="alcohol" class="form-control">
                                <option value="none">None</option>
                                <option value="occasional">Occasional</option>
                                <option value="moderate">Moderate</option>
                                <option value="heavy">Heavy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exercise">Exercise Frequency</label>
                            <select id="exercise" class="form-control">
                                <option value="none">None</option>
                                <option value="occasional">1-2 times/week</option>
                                <option value="regular">3-4 times/week</option>
                                <option value="frequent">5+ times/week</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="diet">Dietary Habits</label>
                            <textarea id="diet" class="form-control" rows="3"
                                placeholder="Describe your typical daily diet and any dietary restrictions"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="stress">Stress Levels and Management</label>
                            <textarea id="stress" class="form-control" rows="3"
                                placeholder="Describe your current stress levels and how you manage stress"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Additional Information -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Additional Information</h3>
                    
                    <div class="form-group">
                        <label for="description">Detailed Symptom Description</label>
                        <textarea id="description" class="form-control" rows="4" 
                            placeholder="Please provide a detailed description of your symptoms, including any patterns, triggers, or relieving factors"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="treatments">Previous Treatments</label>
                        <textarea id="treatments" class="form-control" rows="3"
                            placeholder="List any treatments or medications you've tried for these symptoms"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="questions">Questions for Healthcare Provider</label>
                        <textarea id="questions" class="form-control" rows="3"
                            placeholder="List any specific questions you have for your healthcare provider"></textarea>
                    </div>
                </section>

                <!-- Consent and Submission -->
                <section class="form-section mt-8">
                    <div class="consent-box bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-start space-x-2">
                            <input type="checkbox" required class="mt-1">
                            <span class="text-sm">
                                I confirm that the information provided is accurate to the best of my knowledge. I understand that this 
                                online assessment is not a substitute for professional medical advice, diagnosis, or treatment. In case 
                                of emergency, I will call emergency services or visit the nearest emergency room.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="submit-btn mt-6 w-full md:w-auto">
                        Get Initial Assessment
                    </button>
                </section>
            </form>

            <div class="results-container">
                <h2>Diagnosis Results</h2>
                
                <!-- Severity Indicator -->
                <div class="severity-container">
                    <h3>Severity Level</h3>
                    <div class="severity-bar">
                        <div id="severityFill" class="severity-fill"></div>
                    </div>
                    <div id="severityText" class="severity-text"></div>
                </div>

                <!-- Diagnosis Result -->
                <div id="diagnosisResult" class="diagnosis-details">
                    <!-- This will be populated by JavaScript -->
                </div>

                <!-- Recommendations -->
                <div class="recommendations">
                    <h3>Recommendations</h3>
                    <ul id="recommendationsList" class="recommendations-list">
                        <!-- This will be populated by JavaScript -->
                    </ul>
                </div>
            </div>
        </main>
        <script src="js/diagnosis.js"></script>
        <script src="js/main.js"></script>
    </div>
</body>
</html>