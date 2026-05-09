document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('walletCodeForm');
    if (!form) return;

    const input = document.getElementById('walletCode');
    const submitButton = document.getElementById('walletSubmitButton');
    const feedback = document.getElementById('walletFeedback');
    const walletBalanceValue = document.getElementById('walletBalanceValue');
    const walletHeroBalance = document.getElementById('walletHeroBalance');
    const historyList = document.getElementById('walletHistoryList');
    const emptyState = document.getElementById('walletEmptyState');
    const validateUrl = window.walletConfig?.validateUrl;

    if (!validateUrl) return;

    function formatMoney(amount) {
        return Number(amount).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function setFeedback(message, type = 'info') {
        if (!feedback) return;
        feedback.textContent = message;
        feedback.dataset.state = type;
    }

    function setBalance(amount) {
        const formatted = formatMoney(amount);
        if (walletBalanceValue) {
            walletBalanceValue.textContent = formatted;
        }
        if (walletHeroBalance) {
            walletHeroBalance.textContent = `${formatted} €`;
        }
    }

    function prependHistoryItem(item) {
        if (!historyList || !item) return;

        if (emptyState) {
            emptyState.remove();
        }

        const element = document.createElement('div');
        element.className = 'history-item';
        element.innerHTML = `
            <div>
                <strong>${item.label}</strong>
                <p>${item.date}</p>
            </div>
            <span class="history-amount is-credit">${item.amount}</span>
        `;

        historyList.prepend(element);
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const code = input.value.trim();
        if (!code) {
            setFeedback('Veuillez saisir un code.', 'error');
            input.focus();
            return;
        }

        const payload = new FormData(form);
        payload.set('code', code);

        submitButton.disabled = true;
        setFeedback('Validation du code en cours...', 'info');

        try {
            const response = await fetch(validateUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Impossible de valider le code');
            }

            setBalance(data.balance);
            prependHistoryItem(data.historyItem);
            setFeedback(data.message, 'success');
            input.value = '';
        } catch (error) {
            setFeedback(error.message || 'Une erreur est survenue', 'error');
        } finally {
            submitButton.disabled = false;
        }
    });
});