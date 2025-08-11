// Diagnosis JS
const durationMap = {
    'hours': '1',
    'days_1_3': '2',
    'days_4_7': '3',
    'weeks_1_2': '4',
    'weeks_more': '5'
};

document.getElementById('diagnosisForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    // Get all checked symptoms
    const checkedSymptoms = Array.from(document.querySelectorAll('input[name="symptoms"]:checked'))
        .map(cb => cb.value);

    // Get all checked conditions
    const checkedConditions = Array.from(document.querySelectorAll('input[name="conditions"]:checked'))
        .map(cb => cb.value);

    // Prepare the data object matching backend expectations
    const formData = {
        mainSymptom: document.getElementById('mainSymptom').value,
        symptoms: checkedSymptoms,
        duration: document.getElementById('symptomDuration').value,
        severity: document.getElementById('severity').value,
        ageRange: document.getElementById('ageRange').value,
        biologicalSex: document.getElementById('biologicalSex').value,
        conditions: checkedConditions,
        smoking: document.getElementById('smoking').value,
        alcohol: document.getElementById('alcohol').value,
        exercise: document.getElementById('exercise').value,
        allergies: document.getElementById('allergies').value,
        currentMedications: document.getElementById('currentMedications').value,
        symptomDescription: document.getElementById('symptomDescription').value
    };

    try {
        const response = await fetch('server/getdiagnosis.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        updateSeverityIndicator(result.urgency_level);

        // Update diagnosis result
        updateDiagnosisResult(result);

        // Update recommendations
        updateRecommendations(result.recommendations);

        // Show urgent care warning if needed
        if (result.seek_immediate_care) {
            showUrgentCareWarning();
        }

        // Show alternative possibilities if available
        if (result.alternative_possibilities && result.alternative_possibilities.length > 0) {
            updateAlternativePossibilities(result.alternative_possibilities);
        }

        // Scroll to results
        document.querySelector('.results-container').scrollIntoView({ behavior: 'smooth' });

    } catch (error) {
        console.error('Error:', error);
        showError('An error occurred while processing your assessment: ' + error.message);
    }
});

// Helper functions
function updateSeverityIndicator(urgencyLevel) {
    const severityFill = document.getElementById('severityFill');
    const severityText = document.getElementById('severityText');
    
    if (severityFill) {
        severityFill.style.width = `${urgencyLevel}%`;
        
        // Update color based on urgency level
        if (urgencyLevel > 70) {
            severityFill.style.backgroundColor = '#ef4444'; // red
        } else if (urgencyLevel > 40) {
            severityFill.style.backgroundColor = '#f59e0b'; // yellow
        } else {
            severityFill.style.backgroundColor = '#10b981'; // green
        }
    }
    
    if (severityText) {
        severityText.textContent = getUrgencyText(urgencyLevel);
    }
}

function updateDiagnosisResult(result) {
    const assessmentResult = document.getElementById('assessmentResult');
    if (assessmentResult) {
        assessmentResult.innerHTML = `
            <div class="diagnosis-card p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Assessment Information</h3>
                <p class="text-lg mb-2">${result.possible_condition}</p>
                <p class="text-gray-600">${result.information}</p>
                <div class="mt-2">
                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded">
                        Match Confidence: ${result.match_confidence}%
                    </span>
                </div>
                <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400">
                    <p class="text-sm text-yellow-800">
                        <strong>Disclaimer:</strong> ${result.disclaimer}
                    </p>
                </div>
            </div>
        `;
    }
}

function updateRecommendations(recommendations) {
    const recommendationsList = document.getElementById('recommendationsList');
    if (recommendationsList) {
        recommendationsList.innerHTML = recommendations.map(rec => `
            <li class="recommendation-item">
                <div class="recommendation-icon">💡</div>
                <div class="recommendation-text">${rec}</div>
            </li>
        `).join('');
    }
}

function updateAlternativePossibilities(alternatives) {
    const assessmentResult = document.getElementById('assessmentResult');
    if (assessmentResult) {
        const alternativeSection = document.createElement('div');
        alternativeSection.className = 'mt-4';
        alternativeSection.innerHTML = `
            <h3 class="text-lg font-semibold mb-2">Other Possible Conditions</h3>
            <ul class="space-y-2">
                ${alternatives.map(alt => `
                    <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <span>${alt.condition}</span>
                        <span class="text-sm text-gray-500">Confidence: ${alt.confidence}%</span>
                    </li>
                `).join('')}
            </ul>
        `;
        assessmentResult.appendChild(alternativeSection);
    }
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
    
    const resultsContainer = document.querySelector('.results-container');
    const severityContainer = document.querySelector('.severity-container');
    if (resultsContainer && severityContainer) {
        resultsContainer.insertBefore(warning, severityContainer);
    }
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
    errorDiv.role = 'alert';
    errorDiv.innerHTML = `
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">${message}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    `;
    
    const form = document.querySelector('.diagnosis-form');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
    }
    
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

function getUrgencyText(urgencyLevel) {
    if (urgencyLevel > 70) return 'High Urgency';
    if (urgencyLevel > 40) return 'Moderate Urgency';
    return 'Low Urgency';
}