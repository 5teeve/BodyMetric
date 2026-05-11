document.addEventListener("DOMContentLoaded", () => {
  const alertBox = document.getElementById("profileAlert");

  function showAlert(message, type) {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.classList.remove("alert-success", "alert-danger");
    alertBox.classList.add(
      type === "success" ? "alert-success" : "alert-danger",
    );
    alertBox.hidden = false;
  }

  function clearFieldErrors(form) {
    form.querySelectorAll(".field-error").forEach((el) => el.remove());
    form
      .querySelectorAll(".error")
      .forEach((el) => el.classList.remove("error"));
  }

  function applyFieldErrors(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
      const input = form.querySelector(`[name="${name}"]`);
      if (!input) return;
      input.classList.add("error");

      const container = input.closest(".field");
      if (!container) return;

      const errorDiv = document.createElement("div");
      errorDiv.className = "field-error";
      errorDiv.textContent = message;
      container.appendChild(errorDiv);
    });
  }

  function validatePersonal(form) {
    const errors = {};
    const nom = form.querySelector('[name="nom"]');
    const prenom = form.querySelector('[name="prenom"]');
    const email = form.querySelector('[name="email"]');
    const genre = form.querySelector('[name="genre"]');

    if (!nom || !nom.value.trim()) errors.nom = "Le nom est requis";
    if (!prenom || !prenom.value.trim()) errors.prenom = "Le prenom est requis";
    if (!email || !email.value.trim()) {
      errors.email = "L'email est requis";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      errors.email = "Email invalide";
    }
    if (!genre || !genre.value) errors.genre = "Le genre est requis";

    return errors;
  }

  function validateHealth(form) {
    const errors = {};
    const taille = form.querySelector('[name="taille"]');
    const poids = form.querySelector('[name="poids"]');
    const tailleVal = taille ? parseFloat(taille.value) : NaN;
    const poidsVal = poids ? parseFloat(poids.value) : NaN;

    if (!taille || isNaN(tailleVal)) errors.taille = "La taille est requise";
    if (!poids || isNaN(poidsVal)) errors.poids = "Le poids est requis";

    return errors;
  }

  function setFormEditable(form, isEditable) {
    form.querySelectorAll(".js-editable").forEach((input) => {
      input.disabled = !isEditable;
    });

    const submit = form.querySelector(".js-submit");
    if (submit) submit.disabled = !isEditable;

    const editBtn = form.querySelector(".js-edit-btn");
    if (editBtn) editBtn.disabled = isEditable;
  }

  document.querySelectorAll(".js-edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const form = btn.closest("form");
      if (!form) return;
      setFormEditable(form, true);
    });
  });

  document.querySelectorAll('form[data-ajax="profile"]').forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();

      clearFieldErrors(form);

      const section = form.getAttribute("data-section");
      const localErrors =
        section === "health" ? validateHealth(form) : validatePersonal(form);
      if (Object.keys(localErrors).length > 0) {
        applyFieldErrors(form, localErrors);
        showAlert("Veuillez corriger les erreurs du formulaire.", "error");
        return;
      }

      const endpoint = form.getAttribute("action");
      const formData = new FormData(form);

      const xhr = new XMLHttpRequest();
      xhr.open("POST", endpoint, true);
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

      xhr.onload = () => {
        let payload = null;

        try {
          payload = JSON.parse(xhr.responseText);
        } catch (e) {
          showAlert("Reponse invalide du serveur.", "error");
          return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || !payload.success) {
          if (payload && payload.errors) {
            applyFieldErrors(form, payload.errors);
          }
          showAlert(
            payload && payload.message
              ? payload.message
              : "Une erreur est survenue.",
            "error",
          );
          return;
        }

        showAlert(payload.message || "Mise a jour reussie.", "success");
        setFormEditable(form, false);

        if (payload.imc && payload.imcLabel) {
          const bmiValue = document.getElementById("bmiValue");
          const bmiLabel = document.getElementById("bmiLabelText");
          if (bmiValue) bmiValue.textContent = Number(payload.imc).toFixed(1);
          if (bmiLabel) bmiLabel.textContent = `— ${payload.imcLabel}`;
        }
      };

      xhr.onerror = () => {
        showAlert("Erreur reseau. Veuillez reessayer.", "error");
      };

      xhr.send(formData);
    });
  });

  document.querySelectorAll('form[data-ajax="gold"]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();

      const endpoint = form.getAttribute('action');
      const formData = new FormData(form);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', endpoint, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      xhr.onload = () => {
        let payload = null;

        try {
          payload = JSON.parse(xhr.responseText);
        } catch (e) {
          showAlert('Reponse invalide du serveur.', 'error');
          return;
        }

        if (xhr.status < 200 || xhr.status >= 300 || !payload.success) {
          showAlert(
            payload && payload.message
              ? payload.message
              : 'Une erreur est survenue.',
            'error',
          );
          return;
        }

        const walletAmount = document.getElementById('walletAmount');
        const walletStatus = document.getElementById('walletStatus');

        if (walletAmount && typeof payload.wallet === 'number') {
          walletAmount.textContent = payload.wallet.toFixed(2);
        }

        if (walletStatus) {
          walletStatus.textContent = 'Gold';
          walletStatus.classList.remove('standard');
          walletStatus.classList.add('gold');
        }

        const goldForm = form.closest('.gold-form');
        if (goldForm) {
          goldForm.remove();
        }

        showAlert(payload.message || 'Passage Gold reussi.', 'success');
      };

      xhr.onerror = () => {
        showAlert('Erreur reseau. Veuillez reessayer.', 'error');
      };

      xhr.send(formData);
    });
  });
});
