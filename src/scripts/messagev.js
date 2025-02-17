let mediaRecorder;
let audioChunks = [];

// Fonction pour commencer l'enregistrement
function startRecording(teacherName) {
    // Demander l'accès au microphone
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            // Créer un nouvel objet MediaRecorder pour chaque enregistrement
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.start();
            audioChunks = []; // Réinitialiser les chunks audio

            mediaRecorder.addEventListener("dataavailable", event => {
                audioChunks.push(event.data);
            });

            mediaRecorder.addEventListener("stop", () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                const audioUrl = URL.createObjectURL(audioBlob);
                sendAudioMessage(audioUrl, teacherName);
            });
        })
        .catch(error => {
            console.error("Erreur lors de l'accès au microphone:", error);
            alert("Veuillez autoriser l'accès au microphone.");
        });
}

// Fonction pour arrêter l'enregistrement
function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== "inactive") {
        mediaRecorder.stop();
    }
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
const teachers = document.querySelectorAll('.teacher'); // Sélectionner tous les enseignants (vous devrez peut-être ajuster la sélection)

teachers.forEach(teacher => {
    teacher.addEventListener('click', () => {
        const teacherName = teacher.dataset.teacher;

        // Afficher la zone de messages spécifique à cet enseignant
        const messagesArea = document.getElementById(`messagesArea-${teacherName}`);
        messagesArea.style.display = 'block'; // Afficher la zone de messages

        // Réinitialiser le bouton d'enregistrement pour chaque enseignant
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
