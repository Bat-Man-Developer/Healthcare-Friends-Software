const durationMap = {
    'hours': '1',
    'days_1_3': '2',
    'days_4_7': '3',
    'weeks_1_2': '4',
    'weeks_more': '4'
};

document.getElementById('diagnosisForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    // Get all checked symptoms
    const checkedSymptoms = Array.from(document.querySelectorAll('input[name="symptoms"]:checked'))
        .map(cb => cb.value);

    // Get risk factors
    const riskFactors = [];
    if (document.querySelector('input[name="conditions"][value="diabetes"]').checked) {
        riskFactors.push('diabetes');
    }
    if (document.querySelector('input[name="conditions"][value="hypertension"]').checked) {
        riskFactors.push('hypertension');
    }

    // Calculate age group based on date of birth
    const dob = document.getElementById('dateOfBirth').value;
    const age = calculateAgeGroup(dob);

    // Prepare the data object matching backend expectations
    const formData = {
        mainSymptom: document.getElementById('mainSymptom').value.toLowerCase(),
        symptoms: checkedSymptoms,
        duration: document.getElementById('duration').value,
        age: age,
        riskFactors: riskFactors,
        userId: '<?php echo isset($_SESSION["flduserid"]) ? $_SESSION["flduserid"] : ""; ?>'
    };

    try {
        const response = await fetch('server/getdiagnosis.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.error) {
            throw new Error(result.message || 'An error occurred');
        }

        // Show results section
        document.querySelector('.results-container').style.display = 'block';

        // Update severity indicator
        updateSeverityIndicator(result.severity);

        // Update diagnosis result
        updateDiagnosisResult(result);

        // Update recommendations
        updateRecommendations(result.recommendations);

        // Show urgent care warning if needed
        if (result.urgent) {
            showUrgentCareWarning();
        }

        // Show differential diagnoses if available
        if (result.differential_diagnoses) {
            updateDifferentialDiagnoses(result.differential_diagnoses);
        }

    } catch (error) {
        console.error('Error:', error);
        showError('An error occurred while processing your diagnosis: ' + error.message);
    }
});

// Helper functions
function calculateAgeGroup(dateOfBirth) {
    const today = new Date();
    const birthDate = new Date(dateOfBirth);
    const age = today.getFullYear() - birthDate.getFullYear();
    
    if (age < 18) return 'child';
    if (age > 65) return 'elderly';
    return 'adult';
}

function updateSeverityIndicator(severity) {
    const severityFill = document.getElementById('severityFill');
    const severityText = document.getElementById('severityText');
    
    severityFill.style.width = `${severity}%`;
    severityText.textContent = getSeverityText(severity);
    
    // Update color based on severity
    if (severity > 70) {
        severityFill.style.backgroundColor = '#ef4444'; // red
    } else if (severity > 40) {
        severityFill.style.backgroundColor = '#f59e0b'; // yellow
    } else {
        severityFill.style.backgroundColor = '#10b981'; // green
    }
}

function updateDiagnosisResult(result) {
    const diagnosisResult = document.getElementById('diagnosisResult');
    diagnosisResult.innerHTML = `
        <div class="diagnosis-card p-4 bg-white rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-2">Primary Diagnosis</h3>
            <p class="text-lg mb-2">${result.condition}</p>
            <p class="text-gray-600">${result.description}</p>
            <div class="mt-2">
                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded">
                    Confidence: ${result.confidence}%
                </span>
            </div>
        </div>
    `;
}

function updateRecommendations(recommendations) {
    const recommendationsList = document.getElementById('recommendationsList');
    recommendationsList.innerHTML = recommendations.map(rec => `
        <li class="recommendation-item">
            <div class="recommendation-icon">💡</div>
            <div class="recommendation-text">${rec}</div>
        </li>
    `).join('');
}

function updateDifferentialDiagnoses(differentials) {
    const differentialSection = document.createElement('div');
    differentialSection.className = 'mt-4';
    differentialSection.innerHTML = `
        <h3 class="text-lg font-semibold mb-2">Other Possible Conditions</h3>
        <ul class="space-y-2">
            ${differentials.map(diff => `
                <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span>${diff.condition}</span>
                    <span class="text-sm text-gray-500">Confidence: ${diff.confidence}%</span>
                </li>
            `).join('')}
        </ul>
    `;
    document.getElementById('diagnosisResult').appendChild(differentialSection);
}

function showUrgentCareWarning() {
    const warning = document.createElement('div');
    warning.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 my-4';
    warning.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">⚠️</div>
            <div class="ml-3">
                <p class="font-bold">Urgent Care Recommended</p>
                <p>Based on your symptoms, immediate medical attention is advised.</p>
            </div>
        </div>
    `;
    document.querySelector('.results-container').insertBefore(warning, document.querySelector('.severity-container'));
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative';
    errorDiv.role = 'alert';
    errorDiv.innerHTML = `
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">${message}</span>
    `;
    document.querySelector('.diagnosis-form').prepend(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

function getSeverityText(severity) {
    if (severity > 70) return 'Severe';
    if (severity > 40) return 'Moderate';
    return 'Mild';
}

function showResults() {
    const resultSection = document.getElementById('resultSection');
    resultSection.classList.remove('hidden');
    resultSection.classList.add('active');
    
    // Scroll to results
    resultSection.scrollIntoView({ behavior: 'smooth' });
}

// Handle window resize
window.addEventListener('resize', () => {
    if (window.innerWidth > 600) {
        sidebar.classList.remove('active');
    }
});