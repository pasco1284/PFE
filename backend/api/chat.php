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
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter.");
}
$user_id = $_SESSION['user_id'];

// Infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) die("Utilisateur non trouvé.");

$photo = ($user['photo'] && file_exists('images/' . $user['photo'])) ? $user['photo'] : 'default-profile.png';

// Liste des autres utilisateurs
$stmt = $pdo->prepare("SELECT id, firstname, lastname, photo FROM accounts WHERE id != :id");
$stmt->execute(['id' => $user_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi d’un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['receiver_id'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = htmlspecialchars($_POST['message'] ?? '');
    $file_path = null;
    $file_type = null;

    // Traitement des fichiers uploadés
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . "_" . basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
            $file_type = mime_content_type($target_file);
        }
    }

    // Traitement du message vocal
    if (!empty($_POST['voice_data'])) {
        $voiceData = $_POST['voice_data'];
        $voiceData = explode(',', $voiceData);
        $voiceBin = base64_decode($voiceData[1]);
        $voiceFile = "uploads/voice_" . time() . ".webm";
        file_put_contents($voiceFile, $voiceBin);
        $file_path = $voiceFile;
        $file_type = 'audio/webm';
    }

    // Insertion en base
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path, file_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $receiver_id, $message, $file_path, $file_type]);

    // Notification sonore
    echo "<script>new Audio('notif.mp3').play();</script>";

    header("Location: chat.php?receiver_id=" . $receiver_id);
    exit();
}

// Chargement des messages entre les 2 utilisateurs
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;
$messages = [];

if ($receiver_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE 
        (sender_id = :me AND receiver_id = :them) OR 
        (sender_id = :them AND receiver_id = :me)
        ORDER BY created_at ASC");
    $stmt->execute([
        'me' => $user_id,
        'them' => $receiver_id
    ]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="css/messenger.css">
    <style>
        body, html {
  margin: 0; padding: 0; height: 100%;
  font-family: sans-serif;
}
.container {
  display: flex;
  height: 100vh;
  overflow: hidden;
}
.sidebar {
  width: 300px;
  background: #2c3e50;
  color: white;
  padding: 20px;
  overflow-y: auto;
  flex-shrink: 0;
}
.user {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-bottom: 10px;
}
.user img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}
.chat-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #f4f4f4;
  height: 100vh;
}
.chat-header {
  padding: 15px;
  background: #3498db;
  color: white;
  font-weight: bold;
  flex-shrink: 0;
}
.messages {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}
.message {
  max-width: 60%;
  padding: 10px;
  margin: 5px;
  border-radius: 10px;
  word-wrap: break-word;
}
.sent {
  background: #0078ff;
  color: white;
  align-self: flex-end;
}
.received {
  background: #ccc;
  align-self: flex-start;
}
.message-input {
  display: flex;
  padding: 10px;
  background: #fff;
  border-top: 1px solid #ddd;
  flex-shrink: 0;
}
.message-input input[type=text] {
  flex: 1;
  padding: 10px;
  border-radius: 20px;
  border: 1px solid #ccc;
  font-size: 16px;
}
.message-input button {
  margin-left: 10px;
  border: none;
  background: #3498db;
  color: white;
  border-radius: 20px;
  padding: 10px 20px;
  cursor: pointer;
  font-size: 16px;
}

/* Media query pour mobile */
@media (max-width: 768px) {
  .container {
    flex-direction: column;
    height: 100vh;
  }
  .sidebar {
    width: 100%;
    height: 120px;
    padding: 10px;
    overflow-x: auto;
    overflow-y: hidden;
    display: flex;
    align-items: center;
  }
  .user {
    margin: 0 10px;
    flex-direction: column;
    justify-content: center;
  }
  .user img {
    width: 50px;
    height: 50px;
    margin: 0 0 5px 0;
  }
  .chat-container {
    flex: 1;
    height: calc(100vh - 120px);
  }
  .chat-header {
    font-size: 18px;
    padding: 12px;
  }
  .messages {
    padding: 10px;
  }
  .message {
    max-width: 80%;
    font-size: 16px;
    padding: 12px;
  }
  .message-input {
    flex-direction: column;
    padding: 10px 15px;
  }
  .message-input input[type=text] {
    margin-bottom: 10px;
    font-size: 18px;
  }
  .message-input button {
    margin-left: 0;
    padding: 12px;
    font-size: 18px;
  }
}
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>Discussions</h3>
        <?php foreach ($users as $u): ?>
            <div class="user" onclick="location.href='chat.php?receiver_id=<?= $u['id'] ?>'">
                <img src="images/<?= htmlspecialchars($u['photo'] ?? 'default-profile.png') ?>" alt="">
                <span><?= htmlspecialchars($u['firstname']) . " " . htmlspecialchars($u['lastname']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="chat-container">
        <div class="chat-header">
            <?php if ($receiver_id): ?>
                Discussion avec ID <?= $receiver_id ?>
            <?php else: ?>
                Sélectionnez un utilisateur
            <?php endif; ?>
        </div>

        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_id'] == $user_id ? 'sent' : 'received' ?>">
    <?php if ($msg['file_path']): ?>
        <?php if (strpos($msg['file_type'], 'image') !== false): ?>
            <img src="<?= $msg['file_path'] ?>" style="max-width:200px; border-radius:10px;">
        <?php elseif (strpos($msg['file_type'], 'audio') !== false): ?>
            <audio controls src="<?= $msg['file_path'] ?>"></audio>
        <?php else: ?>
            <a href="<?= $msg['file_path'] ?>" download>Télécharger le fichier</a>
        <?php endif; ?>
    <?php endif; ?>
    <?= nl2br(htmlspecialchars($msg['message'])) ?>
</div>
            <?php endforeach; ?>
        </div>

        <?php if ($receiver_id): ?>
            <form method="POST" enctype="multipart/form-data" class="message-input">
    <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
    <input type="text" name="message" id="message" placeholder="Votre message...">
    
    <!-- Upload fichier -->
    <input type="file" name="file" id="fileInput" style="display:none" onchange="document.getElementById('message').placeholder = this.files[0].name;">
    <button type="button" onclick="document.getElementById('fileInput').click()">📎</button>

    <!-- Enregistrement vocal -->
    <button type="button" onclick="startRecording()">🎙️</button>
    <input type="hidden" name="voice_data" id="voiceData">

    <button type="submit">Envoyer</button>
    </form>
        <?php endif; ?>
    </div>
</div>
<script>
let mediaRecorder;
let audioChunks = [];

function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();

        audioChunks = [];

        mediaRecorder.addEventListener("dataavailable", event => {
            audioChunks.push(event.data);
        });

        mediaRecorder.addEventListener("stop", () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const reader = new FileReader();
            reader.onload = () => {
                document.getElementById('voiceData').value = reader.result;
                document.querySelector('form').submit();
            };
            reader.readAsDataURL(audioBlob);
        });

        setTimeout(() => {
            mediaRecorder.stop();
        }, 60000); // 60 secondes d'enregistrement
    });
}
const receiverId = <?= json_encode($receiver_id) ?>;
const userId = <?= json_encode($user_id) ?>;
const messagesDiv = document.querySelector('.messages');
const form = document.querySelector('form.message-input');

function displayMessages(messages) {
    messagesDiv.innerHTML = '';
    messages.forEach(msg => {
        const div = document.createElement('div');
        div.classList.add('message');
        div.classList.add(msg.sender_id == userId ? 'sent' : 'received');

        if (msg.file_path) {
            if (msg.file_type.startsWith('image')) {
                const img = document.createElement('img');
                img.src = msg.file_path;
                img.style.maxWidth = '200px';
                img.style.borderRadius = '10px';
                div.appendChild(img);
            } else if (msg.file_type.startsWith('audio')) {
                const audio = document.createElement('audio');
                audio.controls = true;
                audio.src = msg.file_path;
                div.appendChild(audio);
            } else {
                const a = document.createElement('a');
                a.href = msg.file_path;
                a.download = '';
                a.textContent = "Télécharger le fichier";
                div.appendChild(a);
            }
        }

        const text = document.createElement('div');
        text.innerHTML = msg.message.replace(/\n/g, "<br>");
        div.appendChild(text);

        messagesDiv.appendChild(div);
    });
    messagesDiv.scrollTop = messagesDiv.scrollHeight; // scroll bottom
}

async function fetchMessages() {
    try {
        const res = await fetch(`fetch_messages.php?receiver_id=${receiverId}`);
        if (res.ok) {
            const data = await res.json();
            displayMessages(data);
        }
    } catch (e) {
        console.error(e);
    }
}

// Rafraîchir messages toutes les 2 secondes
setInterval(fetchMessages, 2000);
fetchMessages();

// Soumission du formulaire en AJAX (pour ne pas recharger)
form.addEventListener('submit', async e => {
    e.preventDefault();

    const formData = new FormData(form);
    try {
        const res = await fetch('', { method: 'POST', body: formData });
        if (res.ok) {
            document.getElementById('message').value = '';
            document.getElementById('fileInput').value = '';
            document.getElementById('voiceData').value = '';
            fetchMessages();
        }
    } catch (err) {
        console.error(err);
    }
});
</script>
</body>
</html>
