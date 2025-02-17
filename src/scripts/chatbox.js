document.addEventListener("DOMContentLoaded", function() {
    const messageForm = document.getElementById("message-form");
    const messageInput = document.getElementById("message-input");
    const imageInput = document.getElementById("image-input");
    const audioInput = document.getElementById("audio-input");
    const conversation = document.getElementById("conversation");

    // Liste des utilisateurs en ligne
    const onlineUsers = document.getElementById("online-users").querySelector("ul");

    // Fonction pour afficher les utilisateurs en ligne
    function loadOnlineUsers() {
        // Ici, tu peux faire une requête AJAX pour récupérer les utilisateurs en ligne
        // Pour le moment, on les simule
        const users = ["Alice", "Bob", "Charlie"];
        users.forEach(user => {
            const userItem = document.createElement("li");
            userItem.textContent = user;
            userItem.addEventListener("click", () => openChat(user));
            onlineUsers.appendChild(userItem);
        });
    }

    // Ouvrir une conversation avec un utilisateur
    function openChat(user) {
        conversation.innerHTML = ""; // Réinitialiser la conversation précédente
        loadMessages(user);
    }

    // Charger les messages de la conversation
    function loadMessages(user) {
        // Tu devrais récupérer les messages depuis ta base de données via une API
        // Simuler les messages ici
        const messages = [
            { sender: "Alice", message: "Bonjour!", type: "text" },
            { sender: "Bob", message: "Salut!", type: "text" }
        ];

        messages.forEach(msg => {
            const messageDiv = document.createElement("div");
            if (msg.type === "text") {
                messageDiv.textContent = `${msg.sender}: ${msg.message}`;
            }
            // Affichage d'images ou de messages vocaux
            if (msg.type === "image") {
                const img = document.createElement("img");
                img.src = msg.message;
                img.alt = "Image";
                messageDiv.appendChild(img);
            }
            if (msg.type === "audio") {
                const audio = document.createElement("audio");
                audio.controls = true;
                audio.src = msg.message;
                messageDiv.appendChild(audio);
            }
            conversation.appendChild(messageDiv);
        });
    }

    // Gérer l'envoi de messages
    messageForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const message = messageInput.value;
        let messageType = "text";

        // Gérer l'envoi d'une image
        if (imageInput.files.length > 0) {
            messageType = "image";
            // Simuler l'envoi de l'image (en réalité, tu l'enverrais au serveur)
            const imageUrl = URL.createObjectURL(imageInput.files[0]);
            sendMessage(message, messageType, imageUrl);
            imageInput.value = ""; // Réinitialiser l'image
        }
        // Gérer l'envoi d'un message vocal
        else if (audioInput.files.length > 0) {
            messageType = "audio";
            // Simuler l'envoi de l'audio
            const audioUrl = URL.createObjectURL(audioInput.files[0]);
            sendMessage(message, messageType, audioUrl);
            audioInput.value = ""; // Réinitialiser l'audio
        }
        // Gérer l'envoi d'un message texte
        else if (message.trim() !== "") {
            sendMessage(message, messageType);
        }

        messageInput.value = ""; // Réinitialiser l'input du message
    });

    // Fonction pour envoyer un message
    function sendMessage(message, type, mediaUrl = null) {
        const messageData = {
            sender_id: 1, // L'ID de l'utilisateur connecté
            receiver_id: 2, // L'ID de l'utilisateur avec qui on discute
            message: message,
            message_type: type
        };

        // Envoie le message via AJAX (exemple en utilisant fetch)
        fetch('/send_message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(messageData)
        })
        .then(response => response.json())
        .then(data => {
            loadMessages('User'); // Recharge les messages
        });
    }

    loadOnlineUsers();
});
