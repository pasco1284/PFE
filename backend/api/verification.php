<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $enteredCode = $_POST['verification_code'];

    if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $enteredCode) {
        echo "Code validé.";
        // Permettre à l'utilisateur de changer son mot de passe
    } else {
        echo "Code incorrect.";
    }
}
?>
