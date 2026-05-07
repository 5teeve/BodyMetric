document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form1');
    if (form) {
        form.onsubmit = function(e) {
            let valid = true;
            const fields = ['nom', 'prenom', 'email'];
            
            fields.forEach(f => {
                const input = this[f];
                if (!input.value.trim()) {
                    input.style.borderColor = '#f43f5e';
                    valid = false;
                } else {
                    input.style.borderColor = 'rgba(255,255,255,0.1)';
                }
            });

            const email = this.email.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                this.email.style.borderColor = '#f43f5e';
                alert('Format d\'email invalide');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        };
    }
});
