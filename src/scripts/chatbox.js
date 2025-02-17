let currentUser = '';

function openChat(user) {
    currentUser = user;
    document.getElementById('chatHeader').innerText = `Chat avec ${user}`;
    document.getElementById('messagesArea').innerHTML = ''; 
}

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
            document.getElementById("messagesArea").scrollTop = document.getElementById("messagesArea").scrollHeight;
        }
    }
}

function sendFile(fileInput) {
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

function appendMessage(type, content) {
    const messagesArea = document.getElementById('messagesArea');
    const messageDiv = document.createElement('div');
    messageDiv.className = type === 'sent' ? 'sent-message' : 'received-message';
    messageDiv.innerHTML = content; 
    messagesArea.appendChild(messageDiv);
    messagesArea.scrollTop = messagesArea.scrollHeight; 
}

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

window.addEventListener("load", function() {
    // Function to toggle the dropdown menu visibility
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";
    }

    // Assuming this is where you want to initialize the menu toggle
    document.getElementById("menuButton").addEventListener("click", toggleMenu);
});

function openMessenger() {
    alert("Messenger icon clicked!");
}
