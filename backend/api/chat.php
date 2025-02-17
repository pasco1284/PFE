<?php
// Connexion à la base de données
$host = 'localhost'; 
$dbname = 'siteweb'; 
$username = 'root'; 
$password = '12345678'; 

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
    <title>Messenger</title>
    <link rel="stylesheet" href="css/messenger.css">
    <link rel="icon" type="image/png" href="images/icon.png">
</head>
<body>
    <!-- Profile Menu -->
<div class="profile-menu">
    <img src="images/<?php echo htmlspecialchars($photo); ?>" alt="Votre photo de profil" class="profile-icon" id="profileIcon" onclick="toggleMenu()">
  
    <i class="fas fa-comments chat-icon" id="messengerIcon" onclick="openMessenger()"></i>
  
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
            <ul id="userList">
                <li class="user" onclick="openChat('User 1')">
                    <img src="images/chat en direct.png" alt="User 1" class="user-pic">
                    <span class="user-name">User 1</span>
                </li>
                <li class="user" onclick="openChat('User 2')">
                    <img src="images/Suivi des Progrès.png" alt="User 2" class="user-pic">
                    <span class="user-name">User 2</span>
                </li>
            </ul>
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
    <button class="button" onclick="window.history.back();">
  <svg class="svgIcon" viewBox="0 0 384 512">
    <path
      d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"
    ></path>
  </svg>
</button>
    <style>
        
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
}

@media (max-width: 1000px){
    .sidebar {
        width: 300px;
        background-color: #2c3e50;
        color: #ecf0f1;
        padding: 20px;
    }
  
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


.profile-menu {
    position: fixed;
    top: 100px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

@media (max-width: 380px){
    .profile-menu {
        position: fixed;
        top: 600px;
        right: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
  
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
/* From Uiverse.io by vinodjangid07 */ 
.button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: rgb(20, 20, 20);
  border: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0px 0px 0px 4px rgba(180, 160, 255, 0.253);
  cursor: pointer;
  transition-duration: 0.3s;
  overflow: hidden;
  position: absolute;
  top: 1%;
  right: 80%;
  rotate: 10px;
}

.svgIcon {
  width: 12px;
  transition-duration: 0.3s;
}

.svgIcon path {
  fill: white;
}

.button:hover {
  width: 140px;
  border-radius: 50px;
  transition-duration: 0.3s;
  background-color: rgb(181, 160, 255);
  align-items: center;
}

.button:hover .svgIcon {
  /* width: 20px; */
  transition-duration: 0.3s;
  transform: translateY(-200%);
}

.button::before {
  position: absolute;
  content: "Back";
  color: white;
  /* transition-duration: .3s; */
  font-size: 0px;
}

.button:hover::before {
  font-size: 13px;
  opacity: 1;
  bottom: unset;
  /* transform: translateY(-30px); */
  transition-duration: 0.3s;
}

    </style>

    <script src="/scripts/chatbox.js"> </script>
    <script src="/scripts/messagev.js"> </script>
</body>
</html>
