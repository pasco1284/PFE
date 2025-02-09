<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Messenger</h1>

<!-- Section pour afficher les utilisateurs en ligne -->
<div id="online-users"></div>

<!-- Chat Box -->
<div id="chat-box">
    <div id="messages"></div>

    <form id="message-form">
        <textarea id="message" placeholder="Write a message..."></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const onlineUsersContainer = document.getElementById('online-users');

    // Charger les utilisateurs en ligne via AJAX
    function loadOnlineUsers() {
        fetch('php/online-users.php')
            .then(response => response.json())
            .then(onlineUsers => {
                // Vider l'affichage actuel
                onlineUsersContainer.innerHTML = '';

                // Ajouter les utilisateurs en ligne
                onlineUsers.forEach(user => {
                    const userElement = document.createElement('div');
                    userElement.textContent = user.name; // Afficher le nom de l'utilisateur
                    onlineUsersContainer.appendChild(userElement);
                });
            });
    }

    // Charger les utilisateurs en ligne au chargement de la page
    loadOnlineUsers();

    // Mettre à jour la liste d'utilisateurs en ligne toutes les 5 secondes
    setInterval(loadOnlineUsers, 5000);
});
</script>

</body>
</html>