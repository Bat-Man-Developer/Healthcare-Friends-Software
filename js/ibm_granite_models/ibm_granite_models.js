/**
 * IBM Granite Models JavaScript Interface
 * Updated to work with the new PHP class structure
 */

/**
 * Call IBM Granite model for inference
 * @param {string} modelType - Either 'granite33' or 'granite40'
 * @param {string} prompt - The prompt to send to the model
 * @returns {Promise<string>} - The model's response
 */
async function callGraniteModel(modelType, prompt) {
    if (!validatePrompt(prompt)) {
        throw new Error('Invalid prompt: must be a non-empty string under 4000 characters');
    }

    const validModels = ['granite33', 'granite40'];
    if (!validModels.includes(modelType)) {
        throw new Error(`Invalid model type: ${modelType}. Must be granite33 or granite40`);
    }

    try {
        const response = await fetch('../../call_python/ibm_granite_models/getIBMGraniteModels.php', {
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
    return trimmedPrompt.length > 0 && trimmedPrompt.length <= 4000;
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

// Export functions for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        callGraniteModel,
        validatePrompt,
        formatError,
        testConnection
    };
}