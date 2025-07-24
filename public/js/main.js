/**
 * Main JavaScript file for Sistema de Análisis de Precios
 * Handles form validation, AJAX requests, and UI interactions
 */

// Global configuration
const Config = {
    csrfToken: window.csrfToken || '',
    ajaxTimeout: 30000,
    validationTimeout: 500
};

// Utility functions
const Utils = {
    // Validate email format
    validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },
    
    // Validate password strength
    validatePassword(password) {
        return password.length >= 8 && 
               /[A-Z]/.test(password) && 
               /[a-z]/.test(password) && 
               /[0-9]/.test(password);
    },
    
    // Validate name (letters, spaces, accents)
    validateName(name) {
        const regex = /^[a-zA-ZÀ-ÿ\s]{2,50}$/;
        return regex.test(name);
    },
    
    // Show loading state on button
    showButtonLoading(button) {
        button.classList.add('loading');
        button.disabled = true;
    },
    
    // Hide loading state on button
    hideButtonLoading(button) {
        button.classList.remove('loading');
        button.disabled = false;
    },
    
    // Show form field error
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    },
    
    // Show form field success
    showFieldSuccess(field) {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    },
    
    // Clear field validation
    clearFieldValidation(field) {
        field.classList.remove('is-invalid', 'is-valid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
};

// Form validation
class FormValidator {
    constructor(form) {
        this.form = form;
        this.setupValidation();
    }
    
    setupValidation() {
        // Real-time validation
        this.form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', this.debounce(() => this.validateField(field), Config.validationTimeout));
        });
        
        // Form submission validation
        this.form.addEventListener('submit', (e) => this.validateForm(e));
    }
    
    validateField(field) {
        const value = field.value.trim();
        const type = field.type || field.dataset.validate;
        
        Utils.clearFieldValidation(field);
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            Utils.showFieldError(field, 'Este campo es obligatorio');
            return false;
        }
        
        if (!value) return true; // Skip validation for empty non-required fields
        
        // Type-specific validation
        switch (type) {
            case 'email':
                if (!Utils.validateEmail(value)) {
                    Utils.showFieldError(field, 'Ingrese un email válido');
                    return false;
                }
                break;
                
            case 'password':
                if (!Utils.validatePassword(value)) {
                    Utils.showFieldError(field, 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número');
                    return false;
                }
                break;
                
            case 'text':
                if (field.name === 'nombre' && !Utils.validateName(value)) {
                    Utils.showFieldError(field, 'El nombre solo puede contener letras y espacios');
                    return false;
                }
                break;
                
            case 'password-confirm':
                const passwordField = this.form.querySelector('input[type="password"]:not([data-validate="password-confirm"])');
                if (passwordField && value !== passwordField.value) {
                    Utils.showFieldError(field, 'Las contraseñas no coinciden');
                    return false;
                }
                break;
        }
        
        Utils.showFieldSuccess(field);
        return true;
    }
    
    validateForm(e) {
        let isValid = true;
        
        this.form.querySelectorAll('input, select, textarea').forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            
            // Focus on first invalid field
            const firstInvalid = this.form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
            }
        }
        
        return isValid;
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// AJAX helper
class AjaxHelper {
    static async request(url, options = {}) {
        const defaultOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: Config.ajaxTimeout
        };
        
        // Add CSRF token for POST requests
        if (options.method !== 'GET' && Config.csrfToken) {
            if (options.body instanceof FormData) {
                options.body.append('csrf_token', Config.csrfToken);
            } else if (typeof options.body === 'string') {
                const data = JSON.parse(options.body);
                data.csrf_token = Config.csrfToken;
                options.body = JSON.stringify(data);
            }
        }
        
        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return await response.text();
            }
        } catch (error) {
            console.error('AJAX request failed:', error);
            throw error;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation for all forms
    document.querySelectorAll('form[data-validate="true"]').forEach(form => {
        new FormValidator(form);
    });
    
    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    });
    
    // Confirm dialogs for dangerous actions
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
            const message = this.dataset.confirm;
            if (!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
    
    // Auto-logout warning
    if (window.sessionTimeout) {
        setTimeout(() => {
            if (confirm('Su sesión está a punto de expirar. ¿Desea continuar?')) {
                // Refresh the page to extend session
                window.location.reload();
            } else {
                window.location.href = '/logout.php';
            }
        }, (window.sessionTimeout - 5) * 60 * 1000); // 5 minutes before expiry
    }
});

// Export for global use
window.Utils = Utils;
window.FormValidator = FormValidator;
window.AjaxHelper = AjaxHelper;