/**
 * JavaScript para Sistema de Análisis de Precios y Programa de Obra
 */

// Configuración global
const App = {
    // CSRF Token for AJAX requests
    csrfToken: window.csrfToken || '',
    
    // API Base URL
    apiUrl: '/api/',
    
    // Configuración de notificaciones
    notifications: {
        duration: 5000,
        position: 'top-end'
    }
};

// Utilidades generales
const Utils = {
    /**
     * Formatear número como moneda
     */
    formatMoney: function(amount) {
        if (isNaN(amount)) return '$0.00';
        return '$' + parseFloat(amount).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },
    
    /**
     * Formatear número con decimales
     */
    formatNumber: function(number, decimals = 2) {
        if (isNaN(number)) return '0.00';
        return parseFloat(number).toLocaleString('es-MX', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    },
    
    /**
     * Formatear porcentaje
     */
    formatPercent: function(number, decimals = 2) {
        if (isNaN(number)) return '0.00%';
        return parseFloat(number).toLocaleString('es-MX', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }) + '%';
    },
    
    /**
     * Validar email
     */
    isValidEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    /**
     * Sanitizar entrada de texto
     */
    sanitize: function(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },
    
    /**
     * Escapar HTML
     */
    escapeHtml: function(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    },
    
    /**
     * Debounce function
     */
    debounce: function(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
};

// Manejo de formularios
const Forms = {
    /**
     * Validar formulario
     */
    validate: function(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showError(input, 'Este campo es requerido');
                isValid = false;
            } else {
                this.clearError(input);
                
                // Validaciones específicas
                if (input.type === 'email' && !Utils.isValidEmail(input.value)) {
                    this.showError(input, 'El formato del email no es válido');
                    isValid = false;
                }
                
                if (input.type === 'number' && isNaN(input.value)) {
                    this.showError(input, 'Debe ser un número válido');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    },
    
    /**
     * Mostrar error en campo
     */
    showError: function(input, message) {
        this.clearError(input);
        
        input.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
    },
    
    /**
     * Limpiar error de campo
     */
    clearError: function(input) {
        input.classList.remove('is-invalid');
        
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    },
    
    /**
     * Enviar formulario por AJAX
     */
    submit: function(form, callback) {
        if (!this.validate(form)) {
            return false;
        }
        
        const formData = new FormData(form);
        formData.append('csrf_token', App.csrfToken);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Deshabilitar botón y mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        
        fetch(form.action || window.location.href, {
            method: form.method || 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (callback) {
                callback(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Notifications.error('Error al enviar el formulario');
        })
        .finally(() => {
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
        
        return false;
    }
};

// Sistema de notificaciones
const Notifications = {
    /**
     * Mostrar notificación de éxito
     */
    success: function(message) {
        this.show(message, 'success');
    },
    
    /**
     * Mostrar notificación de error
     */
    error: function(message) {
        this.show(message, 'danger');
    },
    
    /**
     * Mostrar notificación de advertencia
     */
    warning: function(message) {
        this.show(message, 'warning');
    },
    
    /**
     * Mostrar notificación de información
     */
    info: function(message) {
        this.show(message, 'info');
    },
    
    /**
     * Mostrar notificación
     */
    show: function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove after duration
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, App.notifications.duration);
    }
};

// Manejo de tablas
const Tables = {
    /**
     * Inicializar funcionalidad de tablas
     */
    init: function() {
        // Búsqueda en tablas
        const searchInputs = document.querySelectorAll('[data-table-search]');
        searchInputs.forEach(input => {
            input.addEventListener('input', Utils.debounce(function() {
                Tables.search(this);
            }, 300));
        });
        
        // Ordenamiento de columnas
        const sortableHeaders = document.querySelectorAll('[data-sortable]');
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                Tables.sort(this);
            });
        });
    },
    
    /**
     * Búsqueda en tabla
     */
    search: function(input) {
        const tableId = input.getAttribute('data-table-search');
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const searchTerm = input.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    },
    
    /**
     * Ordenamiento de tabla
     */
    sort: function(header) {
        const table = header.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const column = Array.from(header.parentNode.children).indexOf(header);
        const isNumeric = header.hasAttribute('data-numeric');
        const currentDirection = header.getAttribute('data-direction') || 'asc';
        const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
        
        // Limpiar iconos de ordenamiento
        header.parentNode.querySelectorAll('[data-sortable]').forEach(th => {
            th.removeAttribute('data-direction');
            th.innerHTML = th.innerHTML.replace(/ <i class="bi bi-.*"><\/i>/, '');
        });
        
        // Ordenar filas
        rows.sort((a, b) => {
            const aVal = a.children[column].textContent.trim();
            const bVal = b.children[column].textContent.trim();
            
            let comparison = 0;
            
            if (isNumeric) {
                comparison = parseFloat(aVal) - parseFloat(bVal);
            } else {
                comparison = aVal.localeCompare(bVal);
            }
            
            return newDirection === 'desc' ? -comparison : comparison;
        });
        
        // Actualizar tabla
        rows.forEach(row => tbody.appendChild(row));
        
        // Actualizar header
        header.setAttribute('data-direction', newDirection);
        const icon = newDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
        header.innerHTML += ` <i class="bi ${icon}"></i>`;
    }
};

// Calculadora de precios
const Calculator = {
    /**
     * Calcular precio unitario
     */
    calculateUnitPrice: function(materials = [], labor = [], machinery = [], indirectCost = 0, profit = 0) {
        let totalMaterials = materials.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        let totalLabor = labor.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        let totalMachinery = machinery.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        
        let directCost = totalMaterials + totalLabor + totalMachinery;
        let indirectCostAmount = directCost * (indirectCost / 100);
        let subtotal = directCost + indirectCostAmount;
        let profitAmount = subtotal * (profit / 100);
        
        return {
            materials: totalMaterials,
            labor: totalLabor,
            machinery: totalMachinery,
            directCost: directCost,
            indirectCost: indirectCostAmount,
            subtotal: subtotal,
            profit: profitAmount,
            total: subtotal + profitAmount
        };
    },
    
    /**
     * Calcular importe de concepto
     */
    calculateConceptAmount: function(quantity, unitPrice) {
        return quantity * unitPrice;
    },
    
    /**
     * Calcular porcentaje de avance
     */
    calculateProgress: function(executed, total) {
        if (total === 0) return 0;
        return (executed / total) * 100;
    }
};

// Manejo de modales
const Modals = {
    /**
     * Mostrar modal de confirmación
     */
    confirm: function(title, message, callback) {
        const modalHtml = `
            <div class="modal fade" id="confirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${message}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="confirmBtn">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal existente
        const existingModal = document.getElementById('confirmModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Agregar nuevo modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        
        document.getElementById('confirmBtn').addEventListener('click', function() {
            modal.hide();
            if (callback) callback();
        });
        
        modal.show();
    }
};

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidad de tablas
    Tables.init();
    
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Manejo de formularios con validación
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (Forms.validate(this)) {
                this.submit();
            }
        });
    });
    
    // Auto-formatear campos numéricos
    const numberInputs = document.querySelectorAll('input[data-format="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = Utils.formatNumber(this.value);
            }
        });
    });
    
    // Auto-formatear campos de moneda
    const moneyInputs = document.querySelectorAll('input[data-format="money"]');
    moneyInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    
    // Confirmación para botones de eliminar
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm-delete') || '¿Está seguro de que desea eliminar este elemento?';
            
            Modals.confirm('Confirmar eliminación', message, () => {
                // Si es un enlace, navegar
                if (this.tagName === 'A') {
                    window.location.href = this.href;
                }
                // Si es un botón de formulario, enviar formulario
                else if (this.form) {
                    this.form.submit();
                }
            });
        });
    });
});

// Exportar funciones globales
window.App = App;
window.Utils = Utils;
window.Forms = Forms;
window.Notifications = Notifications;
window.Tables = Tables;
window.Calculator = Calculator;
window.Modals = Modals;