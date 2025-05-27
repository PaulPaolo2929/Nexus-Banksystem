document.addEventListener('DOMContentLoaded', function() {
    // Password validation
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const password = this.value;
            const feedback = this.nextElementSibling;
            
            if (feedback && feedback.tagName === 'SMALL') {
                const hasUpper = /[A-Z]/.test(password);
                const hasLower = /[a-z]/.test(password);
                const hasNumber = /\d/.test(password);
                const hasSpecial = /[@$!%*?&]/.test(password);
                const isLong = password.length >= 8;
                
                let message = '';
                if (!hasUpper) message += 'Uppercase letter required. ';
                if (!hasLower) message += 'Lowercase letter required. ';
                if (!hasNumber) message += 'Number required. ';
                if (!hasSpecial) message += 'Special character required. ';
                if (!isLong) message += 'Minimum 8 characters.';
                
                feedback.textContent = message ? message : 'Password is strong!';
                feedback.style.color = message ? '#e74c3c' : '#2ecc71';
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form:not(.no-validate)');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const inputs = this.querySelectorAll('input[required], textarea[required], select[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#e74c3c';
                    
                    if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = '#e74c3c';
                        errorMsg.style.fontSize = '0.8em';
                        errorMsg.style.marginTop = '5px';
                        errorMsg.textContent = 'This field is required';
                        input.insertAdjacentElement('afterend', errorMsg);
                    }
                } else {
                    input.style.borderColor = '#ddd';
                    const errorMsg = input.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
                const firstInvalid = this.querySelector('[required]:invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    });

    // Auto-format currency values
    document.querySelectorAll('.currency-format').forEach(element => {
        const value = parseFloat(element.textContent.replace(/[^0-9.-]/g, ''));
        if (!isNaN(value)) {
            element.textContent = '$' + value.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    });

    // Auto-format dates
    document.querySelectorAll('.date-format').forEach(element => {
        const dateText = element.textContent.trim();
        if (dateText) {
            try {
                const date = new Date(dateText);
                if (!isNaN(date.getTime())) {
                    element.textContent = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            } catch (e) {
                console.error('Date formatting error:', e);
            }
        }
    });
});