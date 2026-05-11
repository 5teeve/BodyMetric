// Fonction globale pour toggle mot de passe (appelée par onclick)
function togglePassword(btn) {
    const input = btn.parentElement.querySelector('input');
    const svg = btn.querySelector('svg');

    if (input.type === 'password') {
        input.type = 'text';
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        input.type = 'password';
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}
window.togglePassword = togglePassword;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form2');
    if (!form) return;

    const tailleInput = form.taille;
    const poidsInput = form.poids;
    const bmiValue = document.querySelector('.bmi-value');
    const bmiIndicator = document.querySelector('.indicator');
    const bmiLabel = document.querySelector('.bmi-label');

    let debounceTimer;
    const DEBOUNCE_DELAY = 300; // ms

    function getCsrfToken() {
        const csrfInput = form.querySelector('input[name="csrf_token"]');
        return csrfInput ? csrfInput.value : '';
    }

    // Calcul IMC via AJAX avec debounce
    function calculateIMC() {
        const taille = parseFloat(tailleInput.value);
        const poids = parseFloat(poidsInput.value);

        if (taille <= 0 || poids <= 0) {
            bmiValue.textContent = '--.-';
            bmiIndicator.style.left = '0%';
            bmiLabel.textContent = 'IMC Estimé';
            return;
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const csrfToken = getCsrfToken();
            const url = `/ajax/calculate-imc?taille=${encodeURIComponent(taille)}&poids=${encodeURIComponent(poids)}`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bmiValue.textContent = data.imc.toFixed(1);
                    bmiLabel.textContent = `IMC - ${data.label}`;
                    updateBMIGauge(data.imc, data.color);
                }
            })
            .catch(err => {
                console.error('Erreur calcul IMC:', err);
                // Fallback calcul local
                calculateIMCLocal(taille, poids);
            });
        }, DEBOUNCE_DELAY);
    }

    // Calcul local en fallback si AJAX échoue
    function calculateIMCLocal(taille, poids) {
        const tailleM = taille / 100;
        const imc = poids / (tailleM * tailleM);

        bmiValue.textContent = imc.toFixed(1);

        let color = '#22c55e';
        if (imc < 18.5) color = '#3b82f6';
        else if (imc < 25) color = '#22c55e';
        else if (imc < 30) color = '#eab308';
        else color = '#ef4444';

        updateBMIGauge(imc, color);
    }

    function updateBMIGauge(imc, color = null) {
        let position = 0;
        let gaugeColor = color;

        if (!gaugeColor) {
            if (imc < 18.5) gaugeColor = '#3b82f6';
            else if (imc < 25) gaugeColor = '#22c55e';
            else if (imc < 30) gaugeColor = '#eab308';
            else gaugeColor = '#ef4444';
        }

        if (imc < 18.5) {
            position = (imc / 18.5) * 25;
        } else if (imc < 25) {
            position = 25 + ((imc - 18.5) / 6.5) * 25;
        } else if (imc < 30) {
            position = 50 + ((imc - 25) / 5) * 25;
        } else {
            position = Math.min(75 + ((imc - 30) / 10) * 25, 100);
        }

        bmiIndicator.style.left = position + '%';
        bmiIndicator.style.background = gaugeColor;
    }

    // Validation des champs
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

    const mdpInput = form.mdp;

    const fields = {
        taille: {
            el: tailleInput,
            rules: [
                { test: v => v > 0, msg: 'La taille est requise' },
                { test: v => v >= 50, msg: 'La taille doit être supérieure à 50 cm' },
                { test: v => v <= 250, msg: 'La taille doit être inférieure à 250 cm' }
            ]
        },
        poids: {
            el: poidsInput,
            rules: [
                { test: v => v > 0, msg: 'Le poids est requis' },
                { test: v => v >= 20, msg: 'Le poids doit être supérieur à 20 kg' },
                { test: v => v <= 300, msg: 'Le poids doit être inférieur à 300 kg' }
            ]
        },
        mdp: {
            el: mdpInput,
            rules: [
                { test: v => v.length >= 8, msg: 'Le mot de passe doit avoir au moins 8 caractères' },
                { test: v => /[a-z]/.test(v), msg: 'Le mot de passe doit contenir une minuscule' },
                { test: v => /[A-Z]/.test(v), msg: 'Le mot de passe doit contenir une majuscule' },
                { test: v => /\d/.test(v), msg: 'Le mot de passe doit contenir un chiffre' }
            ]
        }
    };

    function validateField(name) {
        const field = fields[name];
        const value = field.el.value;

        clearError(field.el);

        for (const rule of field.rules) {
            if (!rule.test(value)) {
                showError(field.el, rule.msg);
                return false;
            }
        }
        return true;
    }

    // Écouteurs pour IMC temps réel
    tailleInput.addEventListener('input', calculateIMC);
    poidsInput.addEventListener('input', calculateIMC);

    // Validation en temps réel
    Object.keys(fields).forEach(name => {
        const field = fields[name];
        field.el.addEventListener('blur', () => validateField(name));
        field.el.addEventListener('input', () => {
            if (field.el.classList.contains('error')) {
                validateField(name);
            }
        });
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

    // Calcul initial si valeurs présentes (old input)
    calculateIMC();
});
