/**
 * IBM Granite Models JavaScript Interface
 */

/**
 * Call IBM Granite model for inference
 * @param {string} modelType - Either 'granite33' or 'granite40'
 * @param {string} prompt - The prompt to send to the model
 * @returns {Promise<string>} - The model's response
 */
async function callGraniteModel(modelType, prompt) {
    if (!validatePrompt(prompt)) {
        throw new Error('Invalid prompt: must be a non-empty string');
    }

    const validModels = ['granite33', 'granite40'];
    if (!validModels.includes(modelType)) {
        throw new Error(`Invalid model type: ${modelType}. Must be granite33 or granite40`);
    }

    try {
        const response = await fetch('../call_python/ibm_granite_models/getIBMGraniteModels.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                model_type: modelType,
                prompt: prompt.trim(),
                timestamp: Date.now()
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Unknown error occurred');
        }

        return data.response || 'No response received from model';
    } catch (error) {
        console.error('Error calling Granite model:', error);
        throw new Error(formatError(error));
    }
}

/**
 * Validate prompt before sending
 * @param {string} prompt - The prompt to validate
 * @returns {boolean} - Whether the prompt is valid
 */
function validatePrompt(prompt) {
    if (!prompt || typeof prompt !== 'string') {
        return false;
    }

    const trimmedPrompt = prompt.trim();
    return trimmedPrompt.length > 0 && trimmedPrompt.length <= 50000; // Increased limit for network logs
}

/**
 * Format error messages for display
 * @param {Error} error - The error object
 * @returns {string} - Formatted error message
 */
function formatError(error) {
    const message = error.message || error.toString();

    if (message.includes('network') || message.includes('fetch')) {
        return 'Network error: Please check your internet connection and try again.';
    } else if (message.includes('timeout')) {
        return 'Request timeout: The model is taking too long to respond. Please try again.';
    } else if (message.includes('401') || message.includes('authentication')) {
        return 'Authentication error: Please check your API credentials.';
    } else if (message.includes('429') || message.includes('rate limit')) {
        return 'Rate limit exceeded: Please wait a moment before trying again.';
    } else if (message.includes('400') || message.includes('bad request')) {
        return 'Bad request: Please check your input and try again.';
    } else {
        return message || 'An unexpected error occurred.';
    }
}

/**
 * Enhanced prompt preprocessing for network logs
 * @param {string} prompt - Raw prompt
 * @returns {string} - Processed prompt
 */
function preprocessNetworkLogPrompt(prompt) {
    // Add network security analysis context
    const networkAnalysisPrefix = `As a Network Security Analyst, please analyze the following network data for security threats, anomalies, and patterns. Focus on identifying:

1. Suspicious IP addresses or domains
2. Unusual traffic patterns
3. Potential security threats
4. Protocol anomalies
5. Data exfiltration indicators
6. Malware communication patterns

Network Data:
`;

    const networkAnalysisSuffix = `

Please provide a comprehensive security analysis including:
- Summary of findings
- Risk assessment
- Recommended actions
- Threat indicators identified
- Network security recommendations`;

    return networkAnalysisPrefix + prompt + networkAnalysisSuffix;
}

/**
 * Test function to verify the connection
 * @returns {Promise<boolean>} - Whether the connection is working
 */
async function testConnection() {
    try {
        await callGraniteModel('granite33', 'Test connection');
        return true;
    } catch (error) {
        console.error('Connection test failed:', error);
        return false;
    }
}

// Initialize model forms when DOM is ready
function initializeModels() {
    // Granite 3.3 Form
    const granite33Form = document.getElementById('granite33-form');
    if (granite33Form) {
        granite33Form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const prompt = this.querySelector('textarea[name="prompt"]').value;
            const outputDiv = document.getElementById('granite33-output');

            // Show loading state
            outputDiv.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Processing network analysis...</div>';
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
            
            try {
                // Preprocess prompt for network analysis
                const enhancedPrompt = preprocessNetworkLogPrompt(prompt);
                const response = await callGraniteModel('granite33', enhancedPrompt);
                
                outputDiv.innerHTML = `
                    <div class="analysis-result">
                        <h4><i class="fas fa-shield-alt"></i> Network Security Analysis</h4>
                        <div class="timestamp">Generated: ${new Date().toLocaleString()}</div>
                        <pre class="analysis-content">${response}</pre>
                    </div>
                `;
                
                // Update dashboard stats
                localStorage.setItem('last-analysis', new Date().toLocaleString());
                window.AppUtils.updateDashboardStats();
                
            } catch (error) {
                outputDiv.innerHTML = `
                    <div class="error-result">
                        <h4><i class="fas fa-exclamation-triangle"></i> Analysis Error</h4>
                        <p class="error-message">${error.message}</p>
                        <div class="error-suggestions">
                            <strong>Suggestions:</strong>
                            <ul>
                                <li>Check your network connection</li>
                                <li>Verify API credentials in Configuration</li>
                                <li>Try reducing the size of your network log data</li>
                                <li>Ensure the data format is supported</li>
                            </ul>
                        </div>
                    </div>
                `;
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-play"></i> Run Analysis';
            }
        });
    }

    // Granite 4.0 Form
    const granite40Form = document.getElementById('granite40-form');
    if (granite40Form) {
        granite40Form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const prompt = this.querySelector('textarea[name="prompt"]').value;
            const outputDiv = document.getElementById('granite40-output');
            
            outputDiv.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Processing pattern analysis...</div>';
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
            
            try {
                const enhancedPrompt = preprocessNetworkLogPrompt(prompt);
                const response = await callGraniteModel('granite40', enhancedPrompt);
                
                outputDiv.innerHTML = `
                    <div class="analysis-result">
                        <h4><i class="fas fa-chart-line"></i> Pattern Analysis Results</h4>
                        <div class="timestamp">Generated: ${new Date().toLocaleString()}</div>
                        <pre class="analysis-content">${response}</pre>
                    </div>
                `;
                
                localStorage.setItem('last-analysis', new Date().toLocaleString());
                window.AppUtils.updateDashboardStats();
                
            } catch (error) {
                outputDiv.innerHTML = `
                    <div class="error-result">
                        <h4><i class="fas fa-exclamation-triangle"></i> Analysis Error</h4>
                        <p class="error-message">${error.message}</p>
                    </div>
                `;
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-play"></i> Run Analysis';
            }
        });
    }

    // Combined Form
    const combinedForm = document.getElementById('combined-form');
    if (combinedForm) {
        combinedForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const prompt = this.querySelector('textarea[name="prompt"]').value;
            const outputDiv = document.getElementById('combined-output');
            
            outputDiv.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Running comprehensive analysis with both models...</div>';
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
            
            try {
                const enhancedPrompt = preprocessNetworkLogPrompt(prompt);
                
                // Run both models in parallel
                const [response33, response40] = await Promise.allSettled([
                    callGraniteModel('granite33', enhancedPrompt),
                    callGraniteModel('granite40', enhancedPrompt)
                ]);
                
                let combinedHTML = `
                    <div class="combined-analysis-result">
                        <h4><i class="fas fa-layer-group"></i> Comprehensive Network Analysis</h4>
                        <div class="timestamp">Generated: ${new Date().toLocaleString()}</div>
                `;
                
                if (response33.status === 'fulfilled') {
                    combinedHTML += `
                        <div class="model-result granite33-result">
                            <h5><i class="fas fa-brain"></i> Granite 3.3 Security Analysis</h5>
                            <pre class="analysis-content">${response33.value}</pre>
                        </div>
                    `;
                } else {
                    combinedHTML += `
                        <div class="model-error">
                            <h5><i class="fas fa-exclamation-triangle"></i> Granite 3.3 Error</h5>
                            <p class="error-message">${response33.reason.message}</p>
                        </div>
                    `;
                }
                
                if (response40.status === 'fulfilled') {
                    combinedHTML += `
                        <div class="model-result granite40-result">
                            <h5><i class="fas fa-chart-line"></i> Granite 4.0 Pattern Analysis</h5>
                            <pre class="analysis-content">${response40.value}</pre>
                        </div>
                    `;
                } else {
                    combinedHTML += `
                        <div class="model-error">
                            <h5><i class="fas fa-exclamation-triangle"></i> Granite 4.0 Error</h5>
                            <p class="error-message">${response40.reason.message}</p>
                        </div>
                    `;
                }
                
                combinedHTML += '</div>';
                outputDiv.innerHTML = combinedHTML;
                
                localStorage.setItem('last-analysis', new Date().toLocaleString());
                window.AppUtils.updateDashboardStats();
                
            } catch (error) {
                outputDiv.innerHTML = `
                    <div class="error-result">
                        <h4><i class="fas fa-exclamation-triangle"></i> Combined Analysis Error</h4>
                        <p class="error-message">${error.message}</p>
                    </div>
                `;
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-play"></i> Run Combined Analysis';
            }
        });
    }

    // Test connection button
    const testButton = document.getElementById('test-connection');
    if (testButton) {
        testButton.addEventListener('click', async function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
            
            try {
                const isConnected = await testConnection();
                if (isConnected) {
                    window.AppUtils.showNotification('Connection test successful!', 'success');
                } else {
                    window.AppUtils.showNotification('Connection test failed!', 'error');
                }
            } catch (error) {
                window.AppUtils.showNotification('Connection test failed: ' + error.message, 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-plug"></i> Test Connection';
            }
        });
    }

    // Configuration form
    const configForm = document.getElementById('config-form');
    if (configForm) {
        configForm.addEventListener('submit', function(e) {
            e.preventDefault();
            window.AppUtils.saveConfiguration();
        });
    }
}

// Export functions for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        callGraniteModel,
        validatePrompt,
        formatError,
        testConnection,
        preprocessNetworkLogPrompt
    };
}