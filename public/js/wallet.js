document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('walletCodeForm');
  const input = document.getElementById('walletCode');
  const button = document.getElementById('walletSubmitButton');
  const feedback = document.getElementById('walletFeedback');
  const heroBalance = document.getElementById('walletHeroBalance');
  const balanceValue = document.getElementById('walletBalanceValue');
  const historyList = document.getElementById('walletHistoryList');
  const emptyState = document.getElementById('walletEmptyState');
  const validateUrl =
    window.walletConfig && window.walletConfig.validateUrl
      ? window.walletConfig.validateUrl
      : '';

  if (!form || !input || !button || !validateUrl) {
    return;
  }

  const formatAmount = (value) => {
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '0,00';
    const fixed = numeric.toFixed(2);
    const parts = fixed.split('.');
    const integer = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return `${integer},${parts[1]}`;
  };

  const setFeedback = (message, state) => {
    if (!feedback) return;
    feedback.textContent = message || '';
    feedback.dataset.state = state || 'info';
  };

  const updateBalances = (balance) => {
    const formatted = formatAmount(balance);
    if (heroBalance) heroBalance.textContent = `${formatted} Ar`;
    if (balanceValue) balanceValue.textContent = formatted;
  };

  const setButtonState = () => {
    const hasValue = input.value.trim().length > 0;
    button.disabled = !hasValue;
    button.style.cursor = hasValue ? 'pointer' : 'not-allowed';
    button.style.opacity = hasValue ? '1' : '0.65';
  };

  const addHistoryItem = (item) => {
    if (!historyList || !item) return;
    if (emptyState && emptyState.parentNode) {
      emptyState.parentNode.removeChild(emptyState);
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'history-item';

    const info = document.createElement('div');
    const label = document.createElement('strong');
    label.textContent = item.label || 'Recharge';
    const date = document.createElement('p');
    date.textContent = item.date || '';
    info.appendChild(label);
    info.appendChild(date);

    const amount = document.createElement('span');
    const type = item.type === 'debit' ? 'is-debit' : 'is-credit';
    amount.className = `history-amount ${type}`;
    amount.textContent = item.amount || '';

    wrapper.appendChild(info);
    wrapper.appendChild(amount);

    historyList.prepend(wrapper);
  };

  setButtonState();

  input.addEventListener('input', () => {
    setButtonState();
  });

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    if (button.disabled) return;

    input.value = input.value.trim().toUpperCase();
    setButtonState();

    setFeedback('Validation du code en cours...', 'info');
    button.disabled = true;

    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', validateUrl, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = () => {
      button.disabled = false;
      setButtonState();

      let payload = null;
      try {
        payload = JSON.parse(xhr.responseText);
      } catch (e) {
        setFeedback('Reponse invalide du serveur.', 'error');
        return;
      }

      if (xhr.status < 200 || xhr.status >= 300 || !payload.success) {
        setFeedback(payload && payload.message ? payload.message : 'Erreur.', 'error');
        return;
      }

      if (typeof payload.balance === 'number') {
        updateBalances(payload.balance);
      }

      addHistoryItem(payload.historyItem);
      form.reset();
      setButtonState();
      setFeedback(payload.message || 'Portefeuille credite.', 'success');
    };

    xhr.onerror = () => {
      button.disabled = false;
      setButtonState();
      setFeedback('Erreur reseau. Veuillez reessayer.', 'error');
    };

    xhr.send(formData);
  });
});
