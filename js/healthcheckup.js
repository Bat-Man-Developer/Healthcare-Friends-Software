// Virtual Doctor Consultation System
class VirtualDoctor {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 5;
        this.consultationData = {};
        this.conversationHistory = [];
        this.isRecording = false;
        this.recognition = null;
        
        this.initializeSpeechRecognition();
        this.initializeTextToSpeech();
        this.setupEventListeners();
    }

    initializeSpeechRecognition() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = 'en-US';

            this.recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                document.getElementById('patientInput').value = transcript;
                this.sendMessage();
            };

            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                this.stopRecording();
            };

            this.recognition.onend = () => {
                this.stopRecording();
            };
        }
    }

    initializeTextToSpeech() {
        this.synth = window.speechSynthesis;
    }

    setupEventListeners() {
        const patientInput = document.getElementById('patientInput');
        patientInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize textarea
        patientInput.addEventListener('input', () => {
            patientInput.style.height = 'auto';
            patientInput.style.height = patientInput.scrollHeight + 'px';
        });
    }

    speak(text) {
        if (this.synth) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 0.8;
            utterance.pitch = 1;
            utterance.volume = 0.8;
            
            utterance.onstart = () => {
                document.getElementById('speakingIndicator').style.display = 'block';
            };
            
            utterance.onend = () => {
                document.getElementById('speakingIndicator').style.display = 'none';
            };
            
            this.synth.speak(utterance);
        }
    }

    addMessage(message, sender = 'doctor') {
        const conversationArea = document.getElementById('conversationArea');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message';
        
        const messageClass = sender === 'doctor' ? 'doctor-message' : 'patient-message';
        messageDiv.innerHTML = `<div class="${messageClass}">${message}</div>`;
        
        conversationArea.appendChild(messageDiv);
        conversationArea.scrollTop = conversationArea.scrollHeight;
        
        if (sender === 'doctor') {
            this.speak(message);
        }
    }

    updateProgress() {
        document.getElementById('currentStep').textContent = this.currentStep;
        
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach((step, index) => {
            const stepNumber = index + 1;
            if (stepNumber < this.currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
            } else if (stepNumber === this.currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('active', 'completed');
            }
        });
    }

    updateQuickResponses(responses) {
        const quickResponsesDiv = document.getElementById('quickResponses');
        if (responses && responses.length > 0) {
            quickResponsesDiv.innerHTML = responses.map(response => 
                `<button class="quick-response-btn" onclick="virtualDoctor.selectQuickResponse('${response}')">${response}</button>`
            ).join('');
            quickResponsesDiv.style.display = 'block';
        } else {
            quickResponsesDiv.style.display = 'none';
        }
    }

    selectQuickResponse(response) {
        document.getElementById('patientInput').value = response;
        this.sendMessage();
    }

    async sendMessage() {
        const input = document.getElementById('patientInput');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Add patient message
        this.addMessage(message, 'patient');
        input.value = '';
        input.style.height = 'auto';
        
        // Process the message based on current step
        await this.processStep(message);
    }

    async processStep(message) {
        switch (this.currentStep) {
            case 1:
                await this.handleAgeRangeStep(message);
                break;
            case 2:
                await this.handleBiologicalSexStep(message);
                break;
            case 3:
                await this.handleSymptomsStep(message);
                break;
            case 4:
                await this.handleMedicalHistoryStep(message);
                break;
            case 5:
                await this.handleFinalStep(message);
                break;
        }
    }

    async handleAgeRangeStep(message) {
        this.consultationData.ageRange = message;
        document.getElementById('ageRange').value = message;
        
        this.addMessage(`Thank you. I've noted your age range as ${message}. Now, could you please tell me your biological sex? This helps me provide more accurate health assessments.`);
        
        this.updateQuickResponses(['Male', 'Female', 'Prefer not to say']);
        this.currentStep = 2;
        this.updateProgress();
    }

    async handleBiologicalSexStep(message) {
        this.consultationData.biologicalSex = message;
        document.getElementById('biologicalSex').value = message;
        
        this.addMessage(`Thank you. Now, let's discuss your current health concerns. What is the main symptom or health issue you'd like me to assess today? Please describe it in detail.`);
        
        this.updateQuickResponses([]);
        this.currentStep = 3;
        this.updateProgress();
    }

    async handleSymptomsStep(message) {
        this.consultationData.mainSymptom = message;
        document.getElementById('mainSymptom').value = message;
        
        this.addMessage(`I understand you're experiencing ${message}. Let me ask a few more questions to better assess your condition.

How long have you been experiencing these symptoms?`);
        
        this.updateQuickResponses([
            'Less than 24 hours',
            '1-3 days', 
            '4-7 days',
            '1-2 weeks',
            'More than 2 weeks'
        ]);
        
        this.currentStep = 4;
        this.updateProgress();
    }

    async handleMedicalHistoryStep(message) {
        // Parse duration response
        const durationMap = {
            'Less than 24 hours': '1',
            '1-3 days': '2',
            '4-7 days': '3',
            '1-2 weeks': '4',
            'More than 2 weeks': '5'
        };
        
        this.consultationData.symptomDuration = durationMap[message] || '3';
        document.getElementById('symptomDuration').value = this.consultationData.symptomDuration;
        
        this.addMessage(`Thank you. On a scale of 1 to 10, how would you rate the severity of your symptoms? (1 = very mild, 10 = extremely severe)`);
        
        this.updateQuickResponses(['1-2 (Mild)', '3-4 (Mild-Moderate)', '5-6 (Moderate)', '7-8 (Severe)', '9-10 (Very Severe)']);
        this.currentStep = 5;
        this.updateProgress();
    }

    async handleFinalStep(message) {
        // Parse severity
        const severityMap = {
            '1-2 (Mild)': '2',
            '3-4 (Mild-Moderate)': '4',
            '5-6 (Moderate)': '6',
            '7-8 (Severe)': '8',
            '9-10 (Very Severe)': '10'
        };
        
        this.consultationData.severity = severityMap[message] || '5';
        document.getElementById('severity').value = this.consultationData.severity;
        
        this.addMessage(`Thank you for providing all the information. I'm now analyzing your symptoms and medical history to provide you with a comprehensive health assessment. Please wait a moment...`);
        
        this.updateQuickResponses([]);
        
        // Process the diagnosis
        setTimeout(() => {
            this.processDiagnosis();
        }, 2000);
    }

    async processDiagnosis() {
        try {
            // Prepare diagnosis prompt for IBM Granite model
            const prompt = this.createDiagnosisPrompt();

            const response = await fetch('call_python/ibm_granite_models/getIbmGraniteModels.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    model_type: 'granite33', // Using Granite 3.3 for medical analysis
                    prompt: prompt
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                this.displayDiagnosisResults(result.response);
            } else {
                throw new Error(result.error || 'Unknown error occurred');
            }
            
        } catch (error) {
            console.error('Diagnosis error:', error);
            this.addMessage(`I apologize, but I encountered an error while processing your health assessment. Please try again later or consult with a healthcare professional directly.`);
        }
    }

    createDiagnosisPrompt() {
        return `You are a medical AI assistant providing health assessments. Analyze the following patient information and provide a comprehensive health assessment:

Patient Information:
- Age Range: ${this.consultationData.ageRange || 'Not specified'}
- Biological Sex: ${this.consultationData.biologicalSex || 'Not specified'}
- Main Symptom: ${this.consultationData.mainSymptom || 'Not specified'}
- Duration: ${this.getDurationText(this.consultationData.symptomDuration)}
- Severity: ${this.consultationData.severity || '5'}/10

Please provide:
1. Possible conditions or causes
2. Urgency level (Low/Moderate/High)
3. Recommended actions
4. When to seek immediate medical care
5. General health advice

Format your response in a clear, patient-friendly manner. Always emphasize that this is not a substitute for professional medical advice.

Important: Always recommend consulting with a healthcare professional for proper diagnosis and treatment.`;
    }

    getDurationText(duration) {
        const durationTexts = {
            '1': 'Less than 24 hours',
            '2': '1-3 days',
            '3': '4-7 days',
            '4': '1-2 weeks',
            '5': 'More than 2 weeks'
        };
        return durationTexts[duration] || 'Not specified';
    }

    displayDiagnosisResults(response) {
        const formattedResponse = response.replace(/\n/g, '<br>');
        
        this.addMessage(`Based on my analysis of your symptoms and information, here is your health assessment:

${formattedResponse}

Please remember that this assessment is for informational purposes only. I strongly recommend scheduling an appointment with a healthcare professional for a proper medical evaluation and personalized treatment plan.

Would you like me to provide any additional information or clarify anything about this assessment?`);

        // Show detailed results in modal
        this.showDetailedResults(response);
    }

    showDetailedResults(response) {
        const modal = document.getElementById('resultsModal');
        const content = document.getElementById('resultsContent');
        
        content.innerHTML = `
            <h2>Your Health Assessment Results</h2>
            <div class="results-content">
                <div class="assessment-summary">
                    <h3>Assessment Summary</h3>
                    <div class="patient-info">
                        <p><strong>Age Range:</strong> ${this.consultationData.ageRange}</p>
                        <p><strong>Main Concern:</strong> ${this.consultationData.mainSymptom}</p>
                        <p><strong>Duration:</strong> ${this.getDurationText(this.consultationData.symptomDuration)}</p>
                        <p><strong>Severity Level:</strong> ${this.consultationData.severity}/10</p>
                    </div>
                </div>
                <div class="ai-assessment">
                    <h3>AI Health Assessment</h3>
                    <div class="assessment-text">
                        ${response.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="disclaimer">
                    <h4>Important Disclaimer</h4>
                    <p>This assessment is provided by an AI system and is for informational purposes only. It is not intended to replace professional medical advice, diagnosis, or treatment. Always seek the advice of your physician or other qualified healthcare provider with any questions you may have regarding a medical condition.</p>
                </div>
                <div class="action-buttons">
                    <button onclick="window.print()" class="print-btn">Print Results</button>
                    <button onclick="virtualDoctor.startNewConsultation()" class="new-consultation-btn">New Consultation</button>
                </div>
            </div>
        `;
        
        modal.style.display = 'block';
    }

    startNewConsultation() {
        this.currentStep = 1;
        this.consultationData = {};
        this.conversationHistory = [];
        
        document.getElementById('conversationArea').innerHTML = `
            <div class="chat-message">
                <div class="doctor-message">
                    Hello! I'm Dr. AI Assistant, your virtual health consultant. I'm here to help assess your health concerns through a comprehensive consultation.
                    <br><br>
                    Before we begin, please note that this is an assessment tool and not a replacement for professional medical care. In case of emergency, please call 112 immediately.
                    <br><br>
                    Let's start with some basic information. What is your age range?
                </div>
            </div>
        `;
        
        this.updateQuickResponses([
            '0-12 years', '13-17 years', '18-29 years', 
            '30-49 years', '50-64 years', '65+ years'
        ]);
        
        this.updateProgress();
        this.closeResults();
    }

    toggleVoiceRecording() {
        if (!this.recognition) {
            alert('Speech recognition is not supported in your browser.');
            return;
        }

        if (this.isRecording) {
            this.stopRecording();
        } else {
            this.startRecording();
        }
    }

    startRecording() {
        if (this.recognition) {
            this.isRecording = true;
            document.getElementById('voiceBtn').classList.add('recording');
            this.recognition.start();
        }
    }

    stopRecording() {
        if (this.recognition) {
            this.isRecording = false;
            document.getElementById('voiceBtn').classList.remove('recording');
            this.recognition.stop();
        }
    }

    closeResults() {
        document.getElementById('resultsModal').style.display = 'none';
    }
}

// Global functions for HTML onclick events
function selectAgeRange(ageRange) {
    document.getElementById('patientInput').value = ageRange;
    virtualDoctor.sendMessage();
}

function toggleVoiceRecording() {
    virtualDoctor.toggleVoiceRecording();
}

function sendMessage() {
    virtualDoctor.sendMessage();
}

function closeResults() {
    virtualDoctor.closeResults();
}

// Initialize the virtual doctor when page loads
let virtualDoctor;
document.addEventListener('DOMContentLoaded', function() {
    virtualDoctor = new VirtualDoctor();
});

// Modal styles
const modalStyles = `
<style>
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    padding: 2rem;
    border-radius: 10px;
    max-width: 800px;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
    margin: 2rem;
}

.close {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 2rem;
    cursor: pointer;
    color: #999;
}

.close:hover {
    color: #333;
}

.results-content h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.results-content h3 {
    color: #34495e;
    margin: 1.5rem 0 1rem 0;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 0.5rem;
}

.patient-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.assessment-text {
    line-height: 1.6;
    color: #2c3e50;
}

.disclaimer {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 1rem;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.print-btn, .new-consultation-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.print-btn {
    background: #6c757d;
    color: white;
}

.print-btn:hover {
    background: #5a6268;
}

.new-consultation-btn {
    background: #4caf50;
    color: white;
}

.new-consultation-btn:hover {
    background: #45a049;
}

@media (max-width: 768px) {
    .modal-content {
        margin: 1rem;
        padding: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
`;

// Add modal styles to head
document.head.insertAdjacentHTML('beforeend', modalStyles);


// 3D Feature
// 3D Doctor Avatar Integration
let doctorAvatar3D = null;

// Initialize 3D Avatar when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDoctorAvatar3D();
});

function initializeDoctorAvatar3D() {
    // Check for WebGL support
    if (isWebGLSupported()) {
        try {
            // Create avatar container
            const avatarContainer = document.querySelector('.doctor-avatar');
            if (avatarContainer) {
                // Add loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'avatar-loading';
                loadingDiv.textContent = 'Loading 3D Avatar...';
                avatarContainer.appendChild(loadingDiv);
                
                // Add 3D container
                const avatar3DContainer = document.createElement('div');
                avatar3DContainer.className = 'doctor-avatar-3d';
                avatar3DContainer.id = 'doctorAvatar3D';
                avatarContainer.appendChild(avatar3DContainer);
                
                // Add expression indicator
                const expressionIndicator = document.createElement('div');
                expressionIndicator.className = 'expression-indicator';
                expressionIndicator.id = 'expressionIndicator';
                avatarContainer.appendChild(expressionIndicator);
                
                // Add avatar controls
                addAvatarControls(avatarContainer);
                
                // Initialize 3D avatar
                setTimeout(() => {
                    doctorAvatar3D = new DoctorAvatar3D('doctorAvatar3D');
                    
                    // Remove loading indicator
                    setTimeout(() => {
                        if (loadingDiv.parentNode) {
                            loadingDiv.parentNode.removeChild(loadingDiv);
                        }
                        // Start with idle animation
                        doctorAvatar3D.setIdle();
                        showExpressionIndicator('Ready to help');
                    }, 1000);
                }, 500);
            }
        } catch (error) {
            console.error('Failed to initialize 3D avatar:', error);
            fallbackToEmojiAvatar();
        }
    } else {
        console.warn('WebGL not supported, using emoji avatar');
        fallbackToEmojiAvatar();
    }
}

function addAvatarControls(container) {
    const controlsDiv = document.createElement('div');
    controlsDiv.className = 'avatar-controls';
    
    const talkBtn = document.createElement('button');
    talkBtn.className = 'avatar-control-btn';
    talkBtn.innerHTML = '🗣️';
    talkBtn.title = 'Test Talking';
    talkBtn.onclick = () => testTalkingExpression();
    
    const noteBtn = document.createElement('button');
    noteBtn.className = 'avatar-control-btn';
    noteBtn.innerHTML = '📝';
    noteBtn.title = 'Test Note Taking';
    noteBtn.onclick = () => testNoteWritingExpression();
    
    const idleBtn = document.createElement('button');
    idleBtn.className = 'avatar-control-btn active';
    idleBtn.innerHTML = '😌';
    idleBtn.title = 'Idle';
    idleBtn.onclick = () => setIdleExpression();
    
    controlsDiv.appendChild(talkBtn);
    controlsDiv.appendChild(noteBtn);
    controlsDiv.appendChild(idleBtn);
    
    container.appendChild(controlsDiv);
}

function isWebGLSupported() {
    try {
        const canvas = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (
            canvas.getContext('webgl') || 
            canvas.getContext('experimental-webgl')
        ));
    } catch (e) {
        return false;
    }
}

function fallbackToEmojiAvatar() {
    const avatarContainer = document.querySelector('.doctor-avatar');
    if (avatarContainer) {
        avatarContainer.classList.add('webgl-not-supported');
        avatarContainer.innerHTML = ''; // Clear any loading indicators
    }
}

function showExpressionIndicator(text) {
    const indicator = document.getElementById('expressionIndicator');
    if (indicator) {
        indicator.textContent = text;
        indicator.classList.add('visible');
        setTimeout(() => {
            indicator.classList.remove('visible');
        }, 2000);
    }
}

function testTalkingExpression() {
    if (doctorAvatar3D) {
        doctorAvatar3D.startTalking();
        showExpressionIndicator('Talking to patient');
        updateActiveControl('🗣️');
        
        // Auto return to idle after 5 seconds
        setTimeout(() => {
            if (doctorAvatar3D) {
                doctorAvatar3D.setIdle();
                updateActiveControl('😌');
            }
        }, 5000);
    }
}

function testNoteWritingExpression() {
    if (doctorAvatar3D) {
        doctorAvatar3D.startNoteWriting();
        showExpressionIndicator('Taking notes');
        updateActiveControl('📝');
        
        // Auto return to idle after 5 seconds
        setTimeout(() => {
            if (doctorAvatar3D) {
                doctorAvatar3D.setIdle();
                updateActiveControl('😌');
            }
        }, 5000);
    }
}

function setIdleExpression() {
    if (doctorAvatar3D) {
        doctorAvatar3D.setIdle();
        showExpressionIndicator('Ready to help');
        updateActiveControl('😌');
    }
}

function updateActiveControl(activeIcon) {
    const controls = document.querySelectorAll('.avatar-control-btn');
    controls.forEach(btn => {
        btn.classList.remove('active');
        if (btn.innerHTML === activeIcon) {
            btn.classList.add('active');
        }
    });
}

// Integration with existing chat system
function setDoctorTalking() {
    if (doctorAvatar3D) {
        doctorAvatar3D.startTalking();
        showExpressionIndicator('Speaking...');
    }
    // Show speaking indicator
    const speakingIndicator = document.getElementById('speakingIndicator');
    if (speakingIndicator) {
        speakingIndicator.style.display = 'block';
    }
}

function setDoctorNoteWriting() {
    if (doctorAvatar3D) {
        doctorAvatar3D.startNoteWriting();
        showExpressionIndicator('Analyzing response...');
    }
}

function setDoctorIdle() {
    if (doctorAvatar3D) {
        doctorAvatar3D.setIdle();
    }
    // Hide speaking indicator
    const speakingIndicator = document.getElementById('speakingIndicator');
    if (speakingIndicator) {
        speakingIndicator.style.display = 'none';
    }
}

// Enhanced sendMessage function integration
const originalSendMessage = window.sendMessage;
window.sendMessage = function() {
    // Set doctor to note-writing mode when patient sends message
    setDoctorNoteWriting();
    
    // Call original function
    if (originalSendMessage) {
        originalSendMessage();
    }
    
    // Simulate doctor processing and then responding
    setTimeout(() => {
        setDoctorTalking();
        setTimeout(() => {
            setDoctorIdle();
        }, 3000);
    }, 1500);
};

// Clean up 3D avatar on page unload
window.addEventListener('beforeunload', function() {
    if (doctorAvatar3D) {
        doctorAvatar3D.dispose();
    }
});