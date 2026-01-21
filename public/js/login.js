// Fichier: public/js/login.js

// Gestion de l'affichage du mot de passe (L'œil)
document.querySelectorAll('.password-icon').forEach((icon) => {
    icon.addEventListener('click', () => {
        const input = icon.previousElementSibling; 

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
        else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    });
});

