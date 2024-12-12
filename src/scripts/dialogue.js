// Variable pour l'utilisateur actif dans le chat
let currentUser = '';

// Ouvrir un chat avec un utilisateur spécifique
function openChat(user) {
    currentUser = user;
    document.getElementById('chatHeader').innerText = `Chat avec ${user}`;
    document.getElementById('messagesArea').innerHTML = ''; 
}

// Envoi d'un message lors d'un clic ou de l'appui sur "Entrée"
function sendMessage(event) {
    if (event.key === 'Enter' || event.type === 'click') {
        const messageInput = document.getElementById("messageInput");
        const messageText = messageInput.value.trim();

        if (messageText !== "") {
            const messageElement = document.createElement("div");
            messageElement.classList.add("message", "sent"); 
            messageElement.textContent = messageText;

            document.getElementById("messagesArea").appendChild(messageElement);
            messageInput.value = "";
            
            // Scroll automatique vers le bas
            document.getElementById("messagesArea").scrollTop = document.getElementById("messagesArea").scrollHeight;
        }
    }
}

// Envoi d'un fichier
function sendFile() {
    const fileInput = document.getElementById("fileInput");
    const file = fileInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            appendMessage('sent', `<a href="${e.target.result}" download="${file.name}" class="sent-file">${file.name}</a>`);
        };
        reader.readAsDataURL(file);
        fileInput.value = ''; 
    }
}

// Fonction utilitaire pour ajouter un message
function appendMessage(type, content) {
    const messagesArea = document.getElementById("messagesArea");
    const messageDiv = document.createElement('div');
    messageDiv.className = type === 'sent' ? 'sent-message' : 'received-message';
    messageDiv.innerHTML = content; 
    messagesArea.appendChild(messageDiv);
    messagesArea.scrollTop = messagesArea.scrollHeight; 
}

// Enregistrement audio
let mediaRecorder;
let audioChunks = [];
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
                };
            });
    }
}

// Affichage d'un message audio
function displayAudioMessage(audioUrl) {
    const messagesArea = document.getElementById('messagesArea');
    const audioElement = document.createElement('audio');
    audioElement.src = audioUrl;
    audioElement.controls = true; 
    
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('audio-message'); 
    messageDiv.appendChild(audioElement);
    
    messagesArea.appendChild(messageDiv);
    messagesArea.scrollTop = messagesArea.scrollHeight; 
}

// Menu déroulant de profil
function toggleMenu() {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
}

// Notification clic sur l'icône de Messenger
function openMessenger() {
    alert("Messenger icon clicked!");
}

// Ajouter l’événement de "click" pour le bouton "Envoyer"
document.querySelector(".styled-button").addEventListener("click", sendMessage);