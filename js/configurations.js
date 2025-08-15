// Configurations JS
$(document).ready(function() {
    // Get CSRF token
    function getCSRFToken() {
        return $('meta[name="csrf-token"]').attr('content') || '';
    }

    // Save configuration form
    $('#configForm').on('submit', function(e) {
        e.preventDefault();
        
        const saveBtn = $('#saveBtn');
        const originalText = saveBtn.html();
        
        // Show loading state
        saveBtn.addClass('loading').prop('disabled', true).html('💾 Saving...');
        
        const formData = {
            action: 'save_config',
            csrf_token: getCSRFToken(),
            project_id: $('#project_id').val(),
            endpoint_url: $('#endpoint_url').val(),
            api_key: $('#api_key').val(),
            granite33_model: $('#granite33_model').val(),
            granite40_model: $('#granite40_model').val(),
            iam_token: $('#iam_token').val()
        };
        
        $.ajax({
            url: 'server/getconfigurations.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken()
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                } else {
                    showMessage(response.message || 'Failed to save configuration', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save error:', error);
                let errorMessage = 'Failed to save configuration. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // Use default error message
                }
                showMessage(errorMessage, 'error');
            },
            complete: function() {
                // Reset button state
                saveBtn.removeClass('loading').prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Test connection using Python script
    $('#testBtn').on('click', function() {
        const testBtn = $(this);
        const originalText = testBtn.html();
        const testResult = $('#testResult');
        
        // Validate required fields
        const requiredFields = ['project_id', 'endpoint_url'];
        let missingFields = [];
        
        requiredFields.forEach(field => {
            if (!$('#' + field).val().trim()) {
                missingFields.push(field.replace('_', ' ').toUpperCase());
            }
        });
        
        // Check if either API key or IAM token is provided
        if (!$('#api_key').val().trim() && !$('#iam_token').val().trim()) {
            missingFields.push('API KEY or IAM TOKEN');
        }
        
        if (!$('#granite33_model').val().trim()) {
            missingFields.push('GRANITE 3.3 MODEL');
        }
        
        if (missingFields.length > 0) {
            showMessage('Please fill in required fields: ' + missingFields.join(', '), 'warning');
            return;
        }
        
        // Show loading state
        testBtn.addClass('loading').prop('disabled', true).html('🔗 Testing...');
        testResult.hide();
        
        // Prepare data for Python test
        const testData = {
            model_type: 'granite33',
            prompt: 'Hello, this is a connection test. Please respond with a simple acknowledgment.',
            test_connection: true
        };
        
        $.ajax({
            url: 'call_python/ibm_granite_models/getIBMGraniteModels.php',
            type: 'POST',
            data: JSON.stringify(testData),
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken()
            },
            timeout: 60000, // 60 second timeout for connection test
            success: function(response) {
                testResult.removeClass('test-success test-error');
                
                if (response.success) {
                    testResult.addClass('test-success').html(
                        '✅ Connection successful!<br>' +
                        '<small>Model: ' + (response.model_type || 'granite33') + '</small><br>' +
                        '<small>Response time: ' + (response.processing_time || 'N/A') + 's</small>'
                    );
                    showMessage('Connection test successful! IBM Granite models are accessible.', 'success');
                } else {
                    testResult.addClass('test-error').html('❌ Connection failed: ' + response.error);
                    showMessage('Connection test failed: ' + response.error, 'error');
                }
                
                testResult.show();
            },
            error: function(xhr, status, error) {
                console.error('Test error:', error);
                let errorMessage = 'Connection test failed. Please check your configuration.';
                
                if (status === 'timeout') {
                    errorMessage = 'Connection test timed out. Please check your network connection and try again.';
                } else {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = 'Connection test failed: ' + response.error;
                        }
                    } catch (e) {
                        // Use default error message
                    }
                }
                
                testResult.removeClass('test-success test-error')
                          .addClass('test-error')
                          .html('❌ ' + errorMessage)
                          .show();
                showMessage(errorMessage, 'error');
            },
            complete: function() {
                // Reset button state
                testBtn.removeClass('loading').prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Show/hide password toggle for API key
    $('.form-group').has('#api_key').css('position', 'relative');
    $('#api_key').after('<button type="button" class="btn-toggle-password">👁️</button>');
    
    $('.btn-toggle-password').on('click', function() {
        const input = $(this).prev('input');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).text(type === 'password' ? '👁️' : '🙈');
    });
});

// Show message function
function showMessage(message, type) {
    // Remove existing messages
    $('.webmessage').remove();
    
    let messageClass = 'webmessage-success';
    if (type === 'error') {
        messageClass = 'webmessage-error';
    } else if (type === 'warning') {
        messageClass = 'webmessage-warning';
    }
    
    const messageDiv = $('<div>')
        .addClass('webmessage ' + messageClass)
        .text(message)
        .css({
            'position': 'fixed',
            'top': '20px',
            'right': '20px',
            'z-index': '9999',
            'padding': '15px 20px',
            'border-radius': '5px',
            'color': 'white',
            'font-weight': 'bold',
            'min-width': '300px',
            'text-align': 'center'
        });
    
    // Set background color based on type
    if (type === 'success') {
        messageDiv.css('background-color', '#28a745');
    } else if (type === 'error') {
        messageDiv.css('background-color', '#dc3545');
    } else if (type === 'warning') {
        messageDiv.css('background-color', '#ffc107').css('color', '#212529');
    }
    
    $('body').append(messageDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
}

// Input validation
$('.form-input').on('input', function() {
    const input = $(this);
    const value = input.val().trim();
    
    // URL validation for endpoint
    if (input.attr('id') === 'endpoint_url' && value) {
        try {
            new URL(value);
            input.removeClass('invalid');
        } catch {
            input.addClass('invalid');
        }
    }
    
    // Required field highlighting
    if (input.attr('required') && !value) {
        input.addClass('invalid');
    } else {
        input.removeClass('invalid');
    }
});

// Add CSS for invalid inputs and password toggle
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .form-input.invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
        }
        .btn-toggle-password {
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: none !important;
            border: none !important;
            color: #666 !important;
            cursor: pointer !important;
            z-index: 10 !important;
            font-size: 14px !important;
        }
        .btn-toggle-password:hover {
            color: #333 !important;
        }
        .test-result {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 5px;
            font-weight: 500;
        }
        .test-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .test-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        .webmessage {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `)
    .appendTo('head');