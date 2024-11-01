document.addEventListener("DOMContentLoaded", () => {
    fetch("fetch_logs.php")
        .then(response => response.json())
        .then(data => {
            const { accountLogs, trafficLogs, fileLogs, conversationLogs } = data;

            // Affiche les logs de création de comptes
            const accountLogsContainer = document.getElementById("accountLogs");
            accountLogs.forEach(log => {
                accountLogsContainer.innerHTML += `
                    <tr>
                        <td>${log.username}</td>
                        <td>${log.account_type}</td>
                        <td>${log.created_at}</td>
                        <td>${log.ip_address}</td>
                    </tr>
                `;
            });

            // Affiche les logs de trafic
            const trafficLogsContainer = document.getElementById("trafficLogs");
            trafficLogs.forEach(log => {
                trafficLogsContainer.innerHTML += `
                    <tr>
                        <td>${log.event_type}</td>
                        <td>${log.sender}</td>
                        <td>${log.receiver}</td>
                        <td>${log.timestamp}</td>
                    </tr>
                `;
            });

            // Affiche les logs de fichiers échangés
            const fileLogsContainer = document.getElementById("fileLogs");
            fileLogs.forEach(log => {
                fileLogsContainer.innerHTML += `
                    <tr>
                        <td>${log.filename}</td>
                        <td>${log.username}</td>
                        <td>${log.timestamp}</td>
                    </tr>
                `;
            });

            // Affiche les logs de conversations
            const conversationLogsContainer = document.getElementById("conversationLogs");
            conversationLogs.forEach(log => {
                conversationLogsContainer.innerHTML += `
                    <tr>
                        <td>${log.sender}</td>
                        <td>${log.receiver}</td>
                        <td>${log.message}</td>
                        <td>${log.timestamp}</td>
                    </tr>
                `;
            });
        })
        .catch(error => console.error("Erreur:", error));
});