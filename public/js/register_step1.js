document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form1');
    if (!form) return;

    const fields = {
        nom: {
            el: form.nom,
            rules: [
                { test: v => v.trim().length > 0, msg: 'Le nom est requis' },
                { test: v => v.trim().length >= 2, msg: 'Le nom doit avoir au moins 2 caractères' },
                { test: v => v.trim().length <= 255, msg: 'Le nom ne doit pas dépasser 255 caractères' },
                { test: v => /^[a-zA-Z\s\-']+$/.test(v), msg: 'Le nom contient des caractères invalides' }
            ]
        },
        prenom: {
            el: form.prenom,
            rules: [
                { test: v => v.trim().length > 0, msg: 'Le prénom est requis' },
                { test: v => v.trim().length >= 2, msg: 'Le prénom doit avoir au moins 2 caractères' },
                { test: v => v.trim().length <= 255, msg: 'Le prénom ne doit pas dépasser 255 caractères' },
                { test: v => /^[a-zA-Z\s\-']+$/.test(v), msg: 'Le prénom contient des caractères invalides' }
            ]
        },
        email: {
            el: form.email,
            rules: [
                { test: v => v.trim().length > 0, msg: 'L\'email est requis' },
                { test: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), msg: 'Format d\'email invalide' }
            ]
        },
        genre: {
            el: form.genre,
            rules: [
                { test: v => form.querySelector('input[name="genre"]:checked') !== null, msg: 'Le genre est requis' }
            ]
        }
    };

    function showError(input, msg) {
        input.classList.add('error');
        let errorDiv = input.parentElement.querySelector('.field-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            input.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = msg;
    }

    function clearError(input) {
        input.classList.remove('error');
        const errorDiv = input.parentElement.querySelector('.field-error');
        if (errorDiv) errorDiv.remove();
    }

    function validateField(name) {
        const field = fields[name];
        const value = name === 'genre'
            ? form.querySelector('input[name="genre"]:checked')?.value || ''
            : field.el.value;

        clearError(field.el);

        for (const rule of field.rules) {
            if (!rule.test(value)) {
                showError(field.el, rule.msg);
                return false;
            }
        }
        return true;
    }

    // Validation en temps réel
    Object.keys(fields).forEach(name => {
        const field = fields[name];
        if (name === 'genre') {
            form.querySelectorAll('input[name="genre"]').forEach(radio => {
                radio.addEventListener('change', () => validateField(name));
            });
        } else {
            field.el.addEventListener('blur', () => validateField(name));
            field.el.addEventListener('input', () => {
                if (field.el.classList.contains('error')) {
                    validateField(name);
                }
            });
        }
    });

    // Validation au submit
    form.addEventListener('submit', function(e) {
        let isValid = true;
        Object.keys(fields).forEach(name => {
            if (!validateField(name)) isValid = false;
        });

        if (!isValid) {
            e.preventDefault();
            const firstError = form.querySelector('.error');
            if (firstError) firstError.focus();
        }
    });
});
