// Configuration Page JavaScript
$(document).ready(function() {
    // Get CSRF token
    function getCSRFToken() {
        return $('meta[name="csrf-token"]').attr('content') || 
               document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
               '<?php echo $_SESSION["csrf_token"] ?? ""; ?>';
    }

    // Save configuration form
    $('#configForm').on('submit', function(e) {
        e.preventDefault();
        
        const saveBtn = $('#saveBtn');
        const originalText = saveBtn.html();
        
        // Show loading state
        saveBtn.addClass('loading').prop('disabled', true).html('💾 Saving...');
        
        const formData = new FormData();
        formData.append('action', 'save_config');
        formData.append('csrf_token', getCSRFToken());
        formData.append('project_id', $('#project_id').val());
        formData.append('endpoint_url', $('#endpoint_url').val());
        formData.append('api_key', $('#api_key').val());
        formData.append('granite33_model', $('#granite33_model').val());
        formData.append('granite40_model', $('#granite40_model').val());
        formData.append('iam_token', $('#iam_token').val());
        
        $.ajax({
            url: 'server/getconfigurations.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken()
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                } else {
                    showMessage(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save error:', error);
                showMessage('Failed to save configuration. Please try again.', 'error');
            },
            complete: function() {
                // Reset button state
                saveBtn.removeClass('loading').prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Test connection
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
        
        const formData = new FormData();
        formData.append('action', 'test_connection');
        formData.append('csrf_token', getCSRFToken());
        formData.append('project_id', $('#project_id').val());
        formData.append('endpoint_url', $('#endpoint_url').val());
        formData.append('api_key', $('#api_key').val());
        formData.append('iam_token', $('#iam_token').val());
        formData.append('granite33_model', $('#granite33_model').val());
        
        $.ajax({
            url: 'server/getconfigurations.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken()
            },
            success: function(response) {
                testResult.removeClass('test-success test-error');
                
                if (response.success) {
                    testResult.addClass('test-success').html('✅ ' + response.message);
                    showMessage(response.message, 'success');
                } else {
                    testResult.addClass('test-error').html('❌ ' + response.message);
                    showMessage(response.message, 'error');
                }
                
                testResult.show();
            },
            error: function(xhr, status, error) {
                console.error('Test error:', error);
                testResult.removeClass('test-success test-error')
                          .addClass('test-error')
                          .html('❌ Connection test failed. Please check your configuration.')
                          .show();
                showMessage('Connection test failed. Please try again.', 'error');
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
    $('[id^="webmessage_"]').remove();
    
    let messageClass = 'webmessage_green';
    if (type === 'error') {
        messageClass = 'webmessage_red';
    } else if (type === 'warning') {
        messageClass = 'webmessage_yellow';
    }
    
    const messageDiv = $('<div>')
        .attr('id', messageClass)
        .addClass('text-center')
        .text(message)
        .prependTo('body');
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
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
    `)
    .appendTo('head');