/**
 * API Communication Module
 * Handles all AJAX requests to the PHP backend
 */

const API_BASE_URL = 'php/api.php';

/**
 * Make API request
 * @param {string} action - API action to perform
 * @param {object} data - Data to send
 * @param {string} method - HTTP method (GET or POST)
 * @returns {Promise} - Response data
 */
async function apiRequest(action, data = {}, method = 'POST') {
    try {
        const url = method === 'GET' 
            ? `${API_BASE_URL}?action=${action}&${new URLSearchParams(data)}`
            : `${API_BASE_URL}?action=${action}`;
        
        console.log('API Request:', { url, action, data, method });
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        };
        
        if (method === 'POST' && Object.keys(data).length > 0) {
            options.body = JSON.stringify(data);
        }
        
        const response = await fetch(url, options);
        
        console.log('API Response status:', response.status);
        
        if (!response.ok) {
            const text = await response.text();
            console.error('API Error Response:', text);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned non-JSON response');
        }
        
        const result = await response.json();
        console.log('API Result:', result);
        return result;
        
    } catch (error) {
        console.error('API Request Error:', error);
        console.error('Error details:', {
            action: action,
            data: data,
            error: error.message
        });
        return {
            success: false,
            error: 'Network error: ' + error.message
        };
    }
}

/**
 * Show loading overlay
 */
function showLoading(message = 'Loading...') {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        const messageEl = overlay.querySelector('p');
        if (messageEl) messageEl.textContent = message;
        overlay.classList.remove('hidden');
    }
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('hidden');
    }
}

/**
 * Show message to user
 */
function showMessage(message, type = 'info', elementId = 'gameMessage') {
    const messageEl = document.getElementById(elementId);
    if (!messageEl) return;
    
    messageEl.textContent = message;
    messageEl.className = `game-message ${type}`;
    messageEl.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageEl.classList.add('hidden');
    }, 5000);
}

/**
 * Format time in seconds to MM:SS
 */
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

/**
 * Show modal
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Hide modal
 */
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Add event listeners for modal close buttons
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modalId = btn.getAttribute('data-modal');
            if (modalId) {
                hideModal(modalId);
            }
        });
    });
    
    // Close modals when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    });
});
