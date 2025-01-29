<?php
// Connexion à la base de données
$host = '57.129.134.101'; // Votre hôte
$dbname = 'siteweb'; // Nom de la base de données
$username = 'root'; // Utilisateur de la base de données
$password = '12345678'; // Mot de passe de la base de données

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour accéder à cette page.");
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT id, firstname, lastname, email, role, elements, created_at, photo FROM accounts WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé.");
}

// Vérification de la photo
$photo = ($user['photo'] && file_exists('images/' . $user['photo'])) ? $user['photo'] : 'default-profile.png';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/icon.png">
    <title>Messenger</title>
</head>
<body>
<div class="profile-menu">
    <img src="images/<?php echo htmlspecialchars($photo); ?>" alt="Votre photo de profil" class="profile-icon" id="profileIcon" onclick="toggleMenu()"> 
    <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
        <ul>
            <li><a href="http://57.129.134.101/Profile.php">Accéder au profil</a></li>
            <li><a href="http://57.129.134.101/home">Se déconnecter</a></li>
        </ul>
    </div>
</div>
<div class="container">
        <aside class="sidebar">
            <h2>Utilisateurs</h2>
            <ul id="userList"></ul>  <!-- La liste des utilisateurs en ligne sera affichée ici -->
        </aside>

        <div class="chat-container">
            <div class="chat-header" id="chatHeader">Sélectionnez un utilisateur pour discuter</div>
            <div class="messages" id="messagesArea"></div>
            <div class="message-input">
                <input type="text" id="messageInput" placeholder="Écrivez un message..." onkeypress="sendMessage(event)">
                <button class="styled-button" onclick="sendMessage()">Envoyer</button>
                <input type="file" id="fileInput" accept="*/*" style="display: none;" onchange="sendFile()">
                <label for="fileInput" class="send-file styled-button">📎</label>
                <button id="recordButton" class="styled-button" onclick="toggleRecording()">🎤</button>
            </div>
        </div>
    </div>

    <script>

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
        let selectedUserId;
        let currentUserId = 1; // ID de l'utilisateur connecté

        // Fonction pour ouvrir le chat avec un utilisateur
        function openChat(userId, username) {
            selectedUserId = userId;
            document.getElementById("chatHeader").innerText = "Chat avec " + username;
            loadMessages(currentUserId, selectedUserId); // Charger les messages
        }

        function getOnlineUsers() {
            fetch('getOnlineUsers.php')  // Requête au script PHP
                .then(response => response.json())
                .then(users => {
                    const userList = document.getElementById('userList');
                    userList.innerHTML = '';  // Réinitialiser la liste

                    // Ajouter chaque utilisateur à la liste
                    users.forEach(user => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('user');
                        listItem.onclick = () => openChat(user.id, user.username);

                        const userPic = document.createElement('img');
                        userPic.src = user.profile_picture || '/default-profile.png';  // Image par défaut si l'URL n'est pas définie
                        userPic.alt = user.username;
                        userPic.classList.add('user-pic');

                        const userName = document.createElement('span');
                        userName.classList.add('user-name');
                        userName.textContent = user.username;

                        listItem.appendChild(userPic);
                        listItem.appendChild(userName);
                        userList.appendChild(listItem);
                    });
                })
                .catch(error => console.error('Erreur lors de la récupération des utilisateurs en ligne:', error));
        }

        // Charger les utilisateurs en ligne au chargement de la page
        window.onload = getOnlineUsers;

        // Recharger la liste des utilisateurs en ligne toutes les 30 secondes
        setInterval(getOnlineUsers, 30000);

        // Fonction pour envoyer un message texte
        function sendMessage(event) {
            const messageInput = document.getElementById("messageInput");
            const message = messageInput.value.trim();
            if (message === "") return;

            fetch("messenger.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `sender_id=${currentUserId}&receiver_id=${selectedUserId}&message=${message}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageInput.value = ""; // Effacer le champ de texte
                    loadMessages(currentUserId, selectedUserId); // Recharger les messages
                }
            });
        }

        // Fonction pour envoyer un fichier
        function sendFile() {
            const fileInput = document.getElementById("fileInput");
            const file = fileInput.files[0];

            const formData = new FormData();
            formData.append("sender_id", currentUserId);
            formData.append("receiver_id", selectedUserId);
            formData.append("file", file);

            fetch("messenger.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    loadMessages(currentUserId, selectedUserId); // Recharger les messages
                }
            });
        }

        let mediaRecorder;
    let audioChunks = [];

    // Fonction pour démarrer/arrêter l'enregistrement audio
    function toggleRecording() {
        const recordButton = document.getElementById('recordButton');
        if (mediaRecorder && mediaRecorder.state === "recording") {
            mediaRecorder.stop();
            recordButton.textContent = "🎤"; 
        } else {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();
                    recordButton.textContent = "⏹️"; 
                    audioChunks = []; 

                    mediaRecorder.ondataavailable = event => {
                        audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                        const audioUrl = URL.createObjectURL(audioBlob);
                        displayAudioMessage(audioUrl);
                        uploadAudio(audioBlob);
                    };
                });
        }
    }

    // Fonction pour afficher le message audio dans l'interface
    function displayAudioMessage(audioUrl) {
        const messagesArea = document.getElementById("messagesArea");
        const audioMessageElement = document.createElement("div");
        audioMessageElement.classList.add("sent");
        audioMessageElement.innerHTML = `
            <audio controls>
                <source src="${audioUrl}" type="audio/wav">
                Votre navigateur ne supporte pas l'élément audio.
            </audio>
        `;
        messagesArea.appendChild(audioMessageElement);
    }

    // Fonction pour envoyer l'enregistrement audio au serveur
    function uploadAudio(audioBlob) {
        const formData = new FormData();
        formData.append("sender_id", currentUserId);
        formData.append("receiver_id", selectedUserId);
        formData.append("audio", audioBlob, "audio.wav");

        fetch("messenger.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadMessages(currentUserId, selectedUserId); // Recharger les messages
            }
        });
    }

        // Fonction pour charger les messages
        function loadMessages(senderId, receiverId) {
            fetch(`messenger.php?sender_id=${senderId}&receiver_id=${receiverId}`)
                .then(response => response.json())
                .then(messages => {
                    const messagesArea = document.getElementById("messagesArea");
                    messagesArea.innerHTML = ""; // Effacer les anciens messages
                    messages.forEach(message => {
                        const messageElement = document.createElement("div");
                        if (message.sender_id == senderId) {
                            messageElement.classList.add("sent");
                            messageElement.innerText = message.message || `Fichier: <a href="${message.file_url}" download> Télécharger le fichier </a>`;
                        } else {
                            messageElement.classList.add("received");
                            messageElement.innerText = message.message || `Fichier: <a href="${message.file_url}" download> Télécharger le fichier </a>`;
                        }
                        messagesArea.appendChild(messageElement);
                    });
                });
        }
    </script>

<style>
        /* Styles généraux */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #eef2f7;
            color: #333;
        }

        .container {
            display: flex;
            height: 100vh;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .sidebar {
            width: 300px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .user {
            display: flex;
            align-items: center;
            padding: 10px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #34495e;
        }

        .user:hover {
            background-color: #34495e;
        }

        .user-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid #ecf0f1;
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }

        .chat-header {
            padding: 20px;
            background-color: #3498db;
            color: #fff;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f0f0f0;
        }

        .message.sent {
            background-color: #0078ff;
            color: white;
            padding: 10px;
            border-radius: 10px;
            max-width: 10%;
            margin: 10px 0;
            align-self: flex-end; 
            text-align: left;
        }

        .message.received {
            background-color: #b1a5a5;
            color: rgb(255, 255, 255);
            padding: 10px;
            border-radius: 10px;
            max-width: 10%;
            margin: 10px 0;
            align-self: flex-start; 
            text-align: right;
        }

        .message-input {
            display: flex;
            padding: 15px;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }

        .message-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
            outline: none;
            font-size: 16px;
        }

        .message-input button, 
        #recordButton {
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 40px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .message-input button:hover,
        #recordButton:hover {
            background-color: #2980b9;
        }

        .send-file {
            display: inline-flex; 
            justify-content: center;
            align-items: center;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px; 
            height: 40px;
            margin-left: 10px; 
            font-size: 18px; 
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-file:hover {
            background-color: #2980b9; 
        }

        #fileInput {
            display: none;
        }

        #recordButton {
            font-size: 20px;
        }

        /* Profile menu */
        .profile-menu {
            position: fixed;
            top: 100px;
            right: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            position: absolute;
            top: 70px;
            right: 0;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            display: none;
        }

        .dropdown-menu ul {
            list-style: none;
            margin: 0;
            padding: 10px;
        }

        .dropdown-menu ul li {
            padding: 10px;
            text-align: center;
            transition: background-color 0.2s;
        }

        .dropdown-menu ul li:hover {
            background-color: #f0f0f0;
        }

        .dropdown-menu ul li a {
            text-decoration: none;
            color: #333;
            display: block;
        }

        /* Media Queries */
        @media (max-width: 1000px) {
            .sidebar {
                width: 250px;
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
            }
            
            .profile-menu {
                position: fixed;
                top: 10px;
                right: 10px;
                flex-direction: row;
            }
        }

        @media (max-width: 600px) {
            .profile-menu {
                position: fixed;
                top: 10px;
                right: 10px;
                flex-direction: row;
            }

            .chat-header {
                font-size: 16px;
                padding: 10px;
            }
        }

        @media (max-width: 380px) {
            .sidebar h2 {
                font-size: 16px;
            }

            .user {
                font-size: 14px;
            }

            .message.sent, .message.received {
                max-width: 70%;
            }

            .message-input {
                flex-direction: column;
            }

            .message-input input {
                width: 100%;
                margin-bottom: 5px;
            }

            .message-input button,
            #recordButton {
                display: inline-flex; 
                justify-content: center;
                align-items: center;
            }
        }
    </style>
    
</body>
</html>