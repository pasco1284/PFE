<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // PHPMailer via Composer

$servername = "localhost";
$username = "root"; // Modifier si nécessaire
$password = "12345678";
$dbname = "siteweb"; // Nom de ta base de données

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Vérifier si l'email existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Aucun compte associé à cet email.";
        exit();
    }

    // Générer un code de confirmation (OTP)
    $otp = rand(100000, 999999);

    // Insérer le code OTP dans la table password_reset
    $stmt = $conn->prepare("INSERT INTO password_reset (email, code) VALUES (?, ?) 
                            ON DUPLICATE KEY UPDATE code=?, created_at=NOW()");
    $stmt->bind_param("sss", $email, $otp, $otp);
    $stmt->execute();

    // Envoi du code OTP par email avec PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'louey.saadaoui10@gmail.com';
        $mail->Password = 'jjgm mihv otsa izdx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('louey.saadaoui10@gmail.com', 'Support');
        $mail->addAddress($email);
        $mail->Subject = "Code de confirmation";
        $mail->Body = "Votre code de confirmation est : $otp";

        $mail->send();
        echo "Un email contenant un code de confirmation a été envoyé.";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email: " . $mail->ErrorInfo;
    }
}
?>
