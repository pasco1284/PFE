<?php
session_start();
include '_Database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Gestion des requêtes AJAX
if (isset($_GET['load_messages'])) {
    $messages_query = "SELECT m.message, m.sender_id, a.name 
                       FROM messages m 
                       JOIN accounts a ON m.sender_id = a.id 
                       ORDER BY m.timestamp ASC";

    $messages_result = $conn->query($messages_query);

    while ($msg = $messages_result->fetch_assoc()) {
        echo '<div class="message ' . ($msg['sender_id'] == $user_id ? 'sent' : 'received') . '">';
        echo '<strong>' . htmlspecialchars($msg['name']) . ':</strong> ';
        echo htmlspecialchars($msg['message']);
        echo '</div>';
    }
    exit();
}

if (isset($_GET['load_users'])) {
    $users_query = "SELECT id, name FROM accounts WHERE status = 'online'";
    $users_result = $conn->query($users_query);

    while ($user = $users_result->fetch_assoc()) {
        echo '<li>' . htmlspecialchars($user['name']) . '</li>';
    }
    exit();
}

// Traitement de l'envoi du message via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="chat-container">
    <!-- Liste des utilisateurs en ligne -->
    <div class="users-list">
        <h3>Utilisateurs en ligne</h3>
        <ul id="users">
            <?php
            $users_query = "SELECT id, name FROM accounts WHERE status = 'online'";
            $users_result = $conn->query($users_query);
            while ($user = $users_result->fetch_assoc()) {
                echo '<li>' . htmlspecialchars($user['name']) . '</li>';
            }
            ?>
        </ul>
    </div>

    <!-- Zone de chat -->
    <div class="chat-box">
        <div id="messages">
            <?php
            $messages_query = "SELECT m.message, m.sender_id, a.name 
                               FROM messages m 
                               JOIN accounts a ON m.sender_id = a.id 
                               ORDER BY m.timestamp ASC";
            $messages_result = $conn->query($messages_query);
            while ($msg = $messages_result->fetch_assoc()) {
                echo '<div class="message ' . ($msg['sender_id'] == $user_id ? 'sent' : 'received') . '">';
                echo '<strong>' . htmlspecialchars($msg['name']) . ':</strong> ';
                echo htmlspecialchars($msg['message']);
                echo '</div>';
            }
            ?>
        </div>

        <form id="chat-form">
            <textarea id="message" placeholder="Écrire un message..." required></textarea>
            <button type="submit">Envoyer</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const chatForm = document.getElementById("chat-form");
    const messageInput = document.getElementById("message");
    const messagesContainer = document.getElementById("messages");
    const usersList = document.getElementById("users");

    const serverURL = "http://57.129.134.101/chat.php";

    function loadMessages() {
        fetch(serverURL + "?load_messages=1")
            .then(response => response.text())
            .then(data => {
                messagesContainer.innerHTML = data;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
    }

    function loadUsers() {
        fetch(serverURL + "?load_users=1")
            .then(response => response.text())
            .then(data => {
                usersList.innerHTML = data;
            });
    }

    chatForm.addEventListener("submit", function(e) {
        e.preventDefault();
        let formData = new FormData();
        formData.append("message", messageInput.value);

        fetch(serverURL, {
            method: "POST",
            body: formData
        }).then(() => {
            messageInput.value = "";
            loadMessages();
        });
    });

    setInterval(loadMessages, 3000);
    setInterval(loadUsers, 5000);

    loadMessages();
    loadUsers();
});
</script>
<style>
    body {
    font-family: Arial, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.chat-container {
    display: flex;
    width: 80%;
    height: 90vh;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Liste des utilisateurs */
.users-list {
    width: 25%;
    background: #3b5998;
    color: white;
    padding: 20px;
}

.users-list h3 {
    margin-top: 0;
    text-align: center;
}

.users-list ul {
    list-style: none;
    padding: 0;
}

.users-list li {
    padding: 10px;
    background: rgba(255, 255, 255, 0.2);
    margin-bottom: 5px;
    border-radius: 5px;
}

/* Chat box */
.chat-box {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #fff;
}

#messages {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    background: #e5ddd5;
}

.message {
    padding: 10px;
    margin: 5px 0;
    border-radius: 5px;
    max-width: 60%;
}

.sent {
    background: #dcf8c6;
    text-align: right;
    margin-left: auto;
}

.received {
    background: #fff;
    border: 1px solid #ddd;
}

#chat-form {
    display: flex;
    padding: 10px;
    background: #f1f1f1;
}

#chat-form textarea {
    flex: 1;
    height: 40px;
    border: none;
    padding: 10px;
    border-radius: 5px;
    resize: none;
}

#chat-form button {
    background: #3b5998;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

</style>
</body>
</html>
