let mediaRecorder;
let audioChunks = [];

// Fonction pour commencer l'enregistrement
function startRecording(teacherName) {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.start();

            mediaRecorder.addEventListener("dataavailable", event => {
                audioChunks.push(event.data);
            });

            mediaRecorder.addEventListener("stop", () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                audioChunks = [];
                const audioUrl = URL.createObjectURL(audioBlob);
                sendAudioMessage(audioUrl, teacherName);
            });
        })
        .catch(error => {
            console.error("Erreur lors de l'accès au microphone:", error);
        });
}

// Fonction pour arrêter l'enregistrement
function stopRecording() {
    mediaRecorder.stop();
}

// Fonction pour envoyer le message audio
function sendAudioMessage(audioUrl, teacherName) {
    const messagesArea = document.getElementById(`messagesArea-${teacherName}`);
    const audioMessage = document.createElement('audio');
    audioMessage.controls = true;
    audioMessage.src = audioUrl;
    audioMessage.className = 'message student'; // Classe pour les messages de l'étudiant

    messagesArea.appendChild(audioMessage);
    messagesArea.scrollTop = messagesArea.scrollHeight; // Faire défiler vers le bas
}

// Ajout d'écouteurs d'événements pour les boutons d'enregistrement
teachers.forEach(teacher => {
    teacher.addEventListener('click', () => {
        const teacherName = teacher.dataset.teacher;

        // ... (le code précédent pour gérer la boîte de dialogue de chat)

        const recordButton = document.getElementById(`recordButton-${teacherName}`);
        recordButton.onclick = () => {
            if (!mediaRecorder || mediaRecorder.state === "inactive") {
                startRecording(teacherName);
                recordButton.textContent = "⏹️"; // Changer l'icône à un bouton d'arrêt
            } else {
                stopRecording();
                recordButton.textContent = "🎤"; // Revenir à l'icône d'enregistrement
            }
        };
    });
});