// send_message.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=siteweb', 'root', '12345678');

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) 
                           VALUES (:sender_id, :receiver_id, :message, :message_type)");

    $stmt->execute([
        ':sender_id' => $data['sender_id'],
        ':receiver_id' => $data['receiver_id'],
        ':message' => $data['message'],
        ':message_type' => $data['message_type']
    ]);

    echo json_encode(['status' => 'success']);
}
