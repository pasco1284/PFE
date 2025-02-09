<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: http://57.129.134.101/_login.php');
    exit();
}
?>

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

    <div id="online-users"></div>

    <div id="chat-box">
        <div id="messages"></div>

        <form id="message-form">
            <textarea id="message" placeholder="Write a message..."></textarea>
            <button type="submit">Send</button>
        </form>

        <form id="file-form" enctype="multipart/form-data">
            <input type="file" name="image" id="image">
            <input type="file" name="audio" id="audio">
            <button type="submit">Send File</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const messageForm = document.getElementById('message-form');
    const fileForm = document.getElementById('file-form');
    const messagesContainer = document.getElementById('messages');

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('message').value;
        const receiverId = 1;  // Example receiver id
        if (message) {
            fetch('php/send_message.php', {
                method: 'POST',
                body: new URLSearchParams({
                    receiver_id: receiverId,
                    message: message
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      document.getElementById('message').value = '';
                      loadMessages();
                  }
              });
        }
    });

    fileForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(fileForm);
        const receiverId = 1;  // Example receiver id

        fetch('php/upload_image.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  loadMessages();
              }
          });
    });

    function loadMessages() {
        fetch('php/get_messages.php?receiver_id=1')
            .then(response => response.json())
            .then(messages => {
                messagesContainer.innerHTML = '';
                messages.forEach(message => {
                    let messageElement = document.createElement('div');
                    messageElement.textContent = message.message;
                    messagesContainer.appendChild(messageElement);
                });
            });
    }

    loadMessages();
});
</script>
<style>
    /* Reset CSS pour supprimer les marges et le padding par défaut */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Base body styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    color: #333;
    padding: 0;
    margin: 0;
}

/* Header: Titre de la page */
h1 {
    text-align: center;
    color: #2a2d34;
    margin-top: 20px;
    font-size: 2em;
}

/* Conteneur principal */
.container {
    display: flex;
    height: 100vh;
    flex-direction: row;
}

/* Sidebar: Liste des utilisateurs en ligne */
#online-users {
    width: 25%;
    background-color: #ffffff;
    border-right: 1px solid #ccc;
    padding: 20px;
    height: 100%;
    overflow-y: auto;
}

#online-users ul {
    list-style-type: none;
    padding: 0;
}

#online-users ul li {
    padding: 10px;
    margin-bottom: 10px;
    background-color: #eff1f3;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background-color 0.2s ease;
}

#online-users ul li:hover {
    background-color: #e5e5e5;
}

#online-users ul li.online {
    background-color: #d4ffd4;
}

#online-users ul li img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

/* Chatbox principal */
#chat-box {
    width: 75%;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 20px;
    background-color: #ffffff;
}

#messages {
    flex-grow: 1;
    overflow-y: auto;
    margin-bottom: 10px;
}

.message {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.message p {
    padding: 10px;
    border-radius: 10px;
    max-width: 75%;
    word-wrap: break-word;
}

.message.sent p {
    background-color: #4caf50;
    color: white;
    align-self: flex-end;
}

.message.received p {
    background-color: #e5e5e5;
    color: black;
    align-self: flex-start;
}

/* Formulaire d'envoi de message */
#message-form {
    display: flex;
    flex-direction: column;
}

#message-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    resize: none;
    min-height: 60px;
    font-size: 14px;
}

#message-form button {
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s ease;
}

#message-form button:hover {
    background-color: #45a049;
}

/* Formulaire d'upload d'images et de fichiers */
#file-form {
    margin-top: 10px;
    display: flex;
    flex-direction: column;
}

#file-form input[type="file"] {
    margin-bottom: 10px;
}

#file-form button {
    padding: 10px;
    background-color: #2196f3;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s ease;
}

#file-form button:hover {
    background-color: #1976d2;
}

/* Responsive Design pour mobile */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }

    #online-users {
        width: 100%;
        height: auto;
        padding: 10px;
        border-right: none;
    }

    #chat-box {
        width: 100%;
        padding: 15px;
    }

    #messages {
        margin-bottom: 15px;
    }

    #message-form textarea {
        font-size: 14px;
        padding: 8px;
    }

    #message-form button {
        font-size: 14px;
    }

    #file-form button {
        font-size: 14px;
    }
}
</style>
</body>
</html>
