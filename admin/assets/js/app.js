// ============================================
// API FUNCTIONS
// ============================================

/**
 * POST request with FormData
 * @param {string} url - API endpoint
 * @param {FormData} formData - Form data to send
 * @returns {Promise<object>} - JSON response
 */
async function apiPost(url, formData) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        return await res.json();
    } catch (error) {
        console.error('API POST Error:', error);
        showToast('Error', 'Network error occurred', 'error');
        return { status: 'error', message: error.message };
    }
}

/**
 * GET request
 * @param {string} url - API endpoint
 * @returns {Promise<object>} - JSON response
 */
async function apiGet(url) {
    try {
        const res = await fetch(url);
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        return await res.json();
    } catch (error) {
        console.error('API GET Error:', error);
        showToast('Error', 'Network error occurred', 'error');
        return { status: 'error', message: error.message };
    }
}

/**
 * POST request with JSON data
 * @param {string} url - API endpoint
 * @param {object} data - JSON data to send
 * @returns {Promise<object>} - JSON response
 */
async function apiPostJSON(url, data) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        return await res.json();
    } catch (error) {
        console.error('API POST JSON Error:', error);
        showToast('Error', 'Network error occurred', 'error');
        return { status: 'error', message: error.message };
    }
}

/**
 * PUT request with JSON data
 * @param {string} url - API endpoint
 * @param {object} data - JSON data to send
 * @returns {Promise<object>} - JSON response
 */
async function apiPut(url, data) {
    try {
        const res = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        return await res.json();
    } catch (error) {
        console.error('API PUT Error:', error);
        showToast('Error', 'Network error occurred', 'error');
        return { status: 'error', message: error.message };
    }
}

/**
 * DELETE request
 * @param {string} url - API endpoint
 * @returns {Promise<object>} - JSON response
 */
async function apiDelete(url) {
    try {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        return await res.json();
    } catch (error) {
        console.error('API DELETE Error:', error);
        showToast('Error', 'Network error occurred', 'error');
        return { status: 'error', message: error.message };
    }
}

/**
 * Show alert message (traditional)
 * @param {string} message - Message to display
 */
function showAlert(message) {
    alert(message);
}

/**
 * Create FormData from form element
 * @param {HTMLFormElement} form - Form element
 * @returns {FormData} - FormData object
 */
function formDataFromForm(form) {
    return new FormData(form);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Show modern toast notification
 * @param {string} title - Toast title
 * @param {string} message - Toast message
 * @param {string} type - Type: 'success', 'error', 'warning', 'info'
 */
function showToast(title, message, type = 'success') {
    // Remove existing toasts
    $('.toast-notification').remove();
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        warning: '#ff9800',
        info: '#2196f3'
    };
    
    const toast = $(`
        <div class="toast-notification ${type}" style="border-left-color: ${colors[type]}">
            <i class="fas ${icons[type]}" style="color: ${colors[type]}"></i>
            <div>
                <strong>${title}</strong>
                <p>${message}</p>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    toast.css({
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        zIndex: '9999',
        animation: 'slideIn 0.3s ease'
    });
    
    setTimeout(() => {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

/**
 * Show loading spinner
 * @param {string} elementId - Element to show loader in
 */
function showLoading(elementId) {
    const loader = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-pulse"></i>
            <span>Loading...</span>
        </div>
    `;
    $(`#${elementId}`).html(loader);
}

/**
 * Hide loading spinner
 * @param {string} elementId - Element to clear loader from
 */
function hideLoading(elementId) {
    $(`#${elementId}`).empty();
}

/**
 * Validate form fields
 * @param {string} formId - Form ID
 * @returns {boolean} - Is valid
 */
function validateForm(formId) {
    let isValid = true;
    $(`#${formId} input[required], #${formId} select[required], #${formId} textarea[required]`).each(function() {
        if ($(this).val().trim() === '') {
            $(this).addClass('error');
            isValid = false;
        } else {
            $(this).removeClass('error');
        }
    });
    return isValid;
}

/**
 * Confirm action dialog
 * @param {string} message - Confirmation message
 * @param {function} callback - Function to execute on confirm
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Get URL parameters
 * @param {string} param - Parameter name
 * @returns {string|null} - Parameter value
 */
function getUrlParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

/**
 * Escape HTML to prevent XSS
 * @param {string} str - String to escape
 * @returns {string} - Escaped string
 */
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// Add CSS styles for toast notifications
$('<style>').text(`
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
    
    .toast-notification {
        background: #fff;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        border-left: 4px solid;
        animation: slideIn 0.3s ease;
        z-index: 10000;
    }
    
    .toast-notification i {
        font-size: 24px;
    }
    
    .toast-notification strong {
        display: block;
        margin-bottom: 4px;
    }
    
    .toast-notification p {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }
    
    .loading-spinner {
        text-align: center;
        padding: 40px;
    }
    
    .loading-spinner i {
        font-size: 32px;
        color: #ff6b35;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    .error {
        border-color: #f44336 !important;
        background-color: #ffebee !important;
    }
`).appendTo('head');

// Initialize when document is ready
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
    
    // Close toasts on click
    $(document).on('click', '.toast-notification', function() {
        $(this).fadeOut(300, function() {
            $(this).remove();
        });
    });
});