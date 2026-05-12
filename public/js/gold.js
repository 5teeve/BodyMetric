document.addEventListener('DOMContentLoaded', () => {
  const button = document.getElementById('btn-buy-gold');
  if (!button) return;

  button.addEventListener('click', async () => {
    const price = button.getAttribute('data-price') || '';
    const endpoint = button.getAttribute('data-endpoint') || '';
    const csrfName = button.getAttribute('data-csrf-name') || '';
    const csrfHash = button.getAttribute('data-csrf-hash') || '';

    if (!endpoint || !csrfName || !csrfHash) {
      alert('Configuration manquante pour activer Gold.');
      return;
    }

    if (!confirm(`Confirmer l'achat de l'option Gold pour ${price} Ar ?`)) {
      return;
    }

    const formData = new FormData();
    formData.append(csrfName, csrfHash);

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        alert('Felicitation ! Vous etes maintenant membre Gold.');
        window.location.reload();
      } else {
        alert(data.message || 'Une erreur est survenue.');
      }
    } catch (error) {
      console.error('Erreur:', error);
      alert('Erreur lors de l\'achat. Veuillez reessayer.');
    }
  });
});
