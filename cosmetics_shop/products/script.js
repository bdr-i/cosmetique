document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("productForm");

    // Ajouter des écouteurs d'événements pour chaque champ afin de valider lors du blur
    const fieldsToValidate = [
        "productName",
        "productPrice",
        "productCategory",
        "productSubcategory",
        "productQuantity",
        "productImage"
    ];

    fieldsToValidate.forEach(fieldId => {
        const inputElement = document.getElementById(fieldId);
        if (inputElement) {
            inputElement.addEventListener("blur", () => validateField(inputElement));
        }
    });

    form.addEventListener("submit", (event) => {
        clearErrors();

        const name = document.getElementById("productName").value.trim();
        const price = document.getElementById("productPrice").value.trim();
        const category = document.getElementById("productCategory").value.trim();
        const subcategory = document.getElementById("productSubcategory").value.trim();
        const quantity = document.getElementById("productQuantity").value.trim();
        const image = document.getElementById("productImage").files[0];

        // Initialisation des erreurs
        const errors = {};

        // Validation des champs
        if (!name) {
            errors.productName = "Le nom du produit est requis.";
        }

        if (!price || isNaN(price) || parseFloat(price) <= 0) {
            errors.productPrice = "Le prix doit être un nombre positif.";
        }

        if (!category) {
            errors.productCategory = "Veuillez sélectionner une catégorie.";
        }

        if (!subcategory) {
            errors.productSubcategory = "Veuillez sélectionner une sous-catégorie.";
        }

        if (!quantity || isNaN(quantity) || parseInt(quantity) < 1) {
            errors.productQuantity = "La quantité doit être au moins de 1.";
        }

        if (!image) {
            errors.productImage = "L'image est requise.";
        } else if (!["image/jpeg", "image/png", "image/gif"].includes(image.type)) {
            errors.productImage = "Seuls les formats JPEG, PNG et GIF sont autorisés.";
        }

        if (Object.keys(errors).length > 0) {
            displayErrors(errors);
            event.preventDefault(); // Empêcher l'envoi du formulaire
        }else{
            form.submit();
        }
    });

    // Fonction pour valider un champ individuel
    function validateField(inputElement) {
        const fieldId = inputElement.id;
        let errorMessage = "";

        switch (fieldId) {
            case "productName":
                if (!inputElement.value.trim()) {
                    errorMessage = "Le nom du produit est requis.";
                }
                break;
            case "productPrice":
                const price = inputElement.value.trim();
                if (!price || isNaN(price) || parseFloat(price) <= 0) {
                    errorMessage = "Le prix doit être un nombre positif.";
                }
                break;
            case "productCategory":
                if (!inputElement.value.trim()) {
                    errorMessage = "Veuillez sélectionner une catégorie.";
                }
                break;
            case "productSubcategory":
                if (!inputElement.value.trim()) {
                    errorMessage = "Veuillez sélectionner une sous-catégorie.";
                }
                break;
            case "productQuantity":
                const quantity = inputElement.value.trim();
                if (!quantity || isNaN(quantity) || parseInt(quantity) < 1) {
                    errorMessage = "La quantité doit être au moins de 1.";
                }
                break;
            case "productImage":
                const image = inputElement.files[0];
                if (!image) {
                    errorMessage = "L'image est requise.";
                } else if (!["image/jpeg", "image/png", "image/gif"].includes(image.type)) {
                    errorMessage = "Seuls les formats JPEG, PNG et GIF sont autorisés.";
                }
                break;
            default:
                break;
        }

        if (errorMessage) {
            displayError(inputElement, errorMessage);
        } else {
            clearError(inputElement);
        }
    }

    function displayError(inputElement, errorMessage) {
        const errorElement = document.getElementById(`${inputElement.id}Error`);
        if (errorElement) {
            errorElement.innerText = errorMessage;
        }
        inputElement.classList.add("is-invalid");
    }

    function clearError(inputElement) {
        const errorElement = document.getElementById(`${inputElement.id}Error`);
        if (errorElement) {
            errorElement.innerText = "";
        }
        inputElement.classList.remove("is-invalid");
    }

    function displayErrors(errors) {
        for (const [field, message] of Object.entries(errors)) {
            const inputElement = document.getElementById(field);
            if (inputElement) {
                displayError(inputElement, message);
            }
        }
    }
    function clearErrors() {
        const errorElements = document.querySelectorAll(".error");
        errorElements.forEach((element) => {
            element.innerText = "";
        });

        const inputElements = document.querySelectorAll(".is-invalid");
        inputElements.forEach((element) => {
            element.classList.remove("is-invalid");
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const categorySubcategory = {
        cosmetics: ["Maquillage", "Parfums", "Accessoires"],
        skincare: ["Crèmes hydratantes", "Nettoyants", "Sérums"],
        haircare: ["Shampoings", "Après-shampoings", "Huiles capillaires"]
    };

    const categorySelect = document.getElementById("category");
    const subcategorySelect = document.getElementById("subcategory");

    // Mettre à jour les sous-catégories en fonction de la catégorie sélectionnée
    categorySelect.addEventListener("change", () => {
        const selectedCategory = categorySelect.value;

        // Réinitialiser les options des sous-catégories
        subcategorySelect.innerHTML = '<option value="" disabled selected>Choisir une sous-catégorie</option>';

        // Ajouter les nouvelles sous-catégories
        if (selectedCategory && categorySubcategory[selectedCategory]) {
            categorySubcategory[selectedCategory].forEach(subcategory => {
                const option = document.createElement("option");
                option.value = subcategory.toLowerCase().replace(/\s+/g, "-");
                option.textContent = subcategory;
                subcategorySelect.appendChild(option);
            });
        }
    });
});
