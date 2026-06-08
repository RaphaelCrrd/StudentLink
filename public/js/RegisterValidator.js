class RegisterValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (this.form) {
            this.initEvents();
        }
    }

    initEvents() {
        this.form.addEventListener('submit', (e) => this.validate(e));
    }

    validate(e) {
        const password = this.form.querySelector('#password').value;
        const age = parseInt(this.form.querySelector('#age').value, 10);
        const school = this.form.querySelector('#school_id').value;
        
        let errors = [];

        if (password.length < 6) {
            errors.push("Le mot de passe doit contenir au moins 6 caractères.");
        }

        if (isNaN(age) || age < 16 || age > 80) {
            errors.push("Veuillez entrer un âge valide (16 ans minimum).");
        }

        if (school === "") {
            errors.push("Veuillez sélectionner un établissement d'étude.");
        }

       
        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    }
}