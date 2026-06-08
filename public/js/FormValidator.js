class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (this.form) {
            this.initEvents();
        }
    }


    initEvents() {
        this.form.addEventListener('submit', (event) => this.handleSubmit(event));
    }

    // Gère la soumission
    handleSubmit(event) {
        const emailInput = this.form.querySelector('input[type="email"]');
        const passwordInput = this.form.querySelector('input[type="password"]');
        
        let isValid = true;

        if (!this.validateEmail(emailInput.value)) {
            alert("Veuillez entrer une adresse email valide.");
            isValid = false;
        }

        if (passwordInput.value.length < 4) {
            alert("Le mot de passe semble trop court.");
            isValid = false;
        }

      
        if (!isValid) {
            event.preventDefault();
        }
    }

    validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
}