document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const errorDiv = document.createElement("div");
    errorDiv.id = "error-messages";
    form.prepend(errorDiv);

    form.addEventListener("submit", function (event) {
        let errors = [];
        const firstName = form.querySelector("input[name='first_name']");
        const lastName = form.querySelector("input[name='last_name']");
        const department = form.querySelector("select[name='department']");

        const minNameLength = 2;
        const maxNameLength = 50;

        if (!firstName.value.trim()) {
            errors.push("Le champ Prénom est obligatoire.");
            firstName.style.borderColor = "red";
        } else if (firstName.value.length < minNameLength || firstName.value.length > maxNameLength) {
            errors.push(`Le Prénom doit contenir entre ${minNameLength} et ${maxNameLength} caractères.`);
            firstName.style.borderColor = "red";
        } else {
            firstName.style.borderColor = "";
        }

        if (!lastName.value.trim()) {
            errors.push("Le champ Nom est obligatoire.");
            lastName.style.borderColor = "red";
        } else if (lastName.value.length < minNameLength || lastName.value.length > maxNameLength) {
            errors.push(`Le Nom doit contenir entre ${minNameLength} et ${maxNameLength} caractères.`);
            lastName.style.borderColor = "red";
        } else {
            lastName.style.borderColor = "";
        }

        if (!department.value || department.value <= 0) {
            errors.push("Veuillez sélectionner un département valide.");
            department.style.borderColor = "red";
        } else {
            department.style.borderColor = "";
        }

        errorDiv.innerHTML = "";
        if (errors.length > 0) {
            event.preventDefault();
            errors.forEach(error => {
                const errorMessage = document.createElement("p");
                errorMessage.textContent = error;
                errorMessage.style.color = "red";
                errorDiv.appendChild(errorMessage);
            });
        }
    });
});


