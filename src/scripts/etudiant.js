 // Attendre que la fenêtre soit entièrement chargée
 window.addEventListener("load", function() {
    // Fonction pour afficher ou masquer le menu déroulant
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";
    }

    // Fonction pour rediriger vers la page de messagerie
    function openMessenger() {
        window.location.href = "message.html"; // Remplacez "messagerie.html" par le chemin de votre page de messagerie
    }    
    // Ajouter des événements sur les éléments pour éviter l'utilisation de `onclick` directement dans le HTML
    document.getElementById("profileIcon").addEventListener("click", toggleMenu);
    document.getElementById("messengerIcon").addEventListener("click", openMessenger);
});