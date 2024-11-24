document.addEventListener("DOMContentLoaded", () => {
    const removeErrorOnInput = (field, errorElement, validationFn) => {
        field.addEventListener("input", () => {
            if (validationFn(field.value)) {
                field.classList.remove("is-invalid");
                errorElement.textContent = "";
            }
        });
    };

    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        const username = document.getElementById("newUsername");
        const password = document.getElementById("newPassword");
        const email = document.getElementById("email");
        const phone = document.getElementById("phone");
        const firstName = document.getElementById("firstName");
        const lastName = document.getElementById("lastName");

        const usernameError = document.getElementById("usernameError");
        const passwordError = document.getElementById("passwordError");
        const emailError = document.getElementById("emailError");
        const phoneError = document.getElementById("phoneError");
        const firstNameError = document.getElementById("firstNameError");
        const lastNameError = document.getElementById("lastNameError");

        signupForm.addEventListener("submit", (e) => {
            let isValid = true;

            // Nom d'utilisateur
            if (!username.value.trim()) {
                usernameError.textContent = "Le nom d'utilisateur est obligatoire.";
                username.classList.add("is-invalid");
                isValid = false;
            }

            // Prénom
            if (!/^[a-zA-Zéèêëàâîïôùç\s'-]+$/.test(firstName.value.trim())) {
                firstNameError.textContent = "Le prénom ne doit contenir que des lettres.";
                firstName.classList.add("is-invalid");
                isValid = false;
            }

            // Nom
            if (!/^[a-zA-Zéèêëàâîïôùç\s'-]+$/.test(lastName.value.trim())) {
                lastNameError.textContent = "Le nom ne doit contenir que des lettres.";
                lastName.classList.add("is-invalid");
                isValid = false;
            }

            // Mot de passe
            if (password.value.length < 6 || !/\d/.test(password.value) || !/[^a-zA-Z0-9]/.test(password.value)) {
                passwordError.textContent = "Le mot de passe doit contenir au moins 6 caractères, un chiffre et un caractère spécial.";
                password.classList.add("is-invalid");
                isValid = false;
            }

            // Email
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                emailError.textContent = "Adresse e-mail invalide.";
                email.classList.add("is-invalid");
                isValid = false;
            }

            // Numéro de téléphone
            if (!/^(\+213|0)(5|6|7)\d{8}$/.test(phone.value)) {
                phoneError.textContent = "Numéro de téléphone invalide.";
                phone.classList.add("is-invalid");
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });

        // Suppression des erreurs
        removeErrorOnInput(username, usernameError, (value) => value.trim() !== "");
        removeErrorOnInput(firstName, firstNameError, (value) => /^[a-zA-Zéèêëàâîïôùç\s'-]+$/.test(value.trim()));
        removeErrorOnInput(lastName, lastNameError, (value) => /^[a-zA-Zéèêëàâîïôùç\s'-]+$/.test(value.trim()));
        removeErrorOnInput(password, passwordError, (value) => value.length >= 6 && /\d/.test(value) && /[^a-zA-Z0-9]/.test(value));
        removeErrorOnInput(email, emailError, (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value));
        removeErrorOnInput(phone, phoneError, (value) => /^(\+213|0)(5|6|7)\d{8}$/.test(value));
    }
});
