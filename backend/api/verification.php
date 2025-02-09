<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    if (!isset($_SESSION['verification_code'])) {
        echo "Aucun code de vérification trouvé.";
        exit();
    }

    if ($_SESSION['verification_code'] == $_POST['verification_code']) {
        $_SESSION['verified'] = true;
        echo "Code validé.";
    } else {
        echo "Code incorrect.";
    }
}
?>
