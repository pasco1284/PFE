// script.js
document.getElementById('profileForm').addEventListener('submit', function(event) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    // Si les mots de passe ne correspondent pas, empêcher l'envoi du formulaire
    if (password !== confirmPassword) {
        event.preventDefault();
        alert("Les mots de passe ne correspondent pas.");
    }
});
