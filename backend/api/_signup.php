<?php
session_start();
include '_Database.php'; // Fichier de connexion

// Récupération des données du formulaire
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? '';
$elements = isset($_POST['elements']) && is_array($_POST['elements']) ? implode(",", $_POST['elements']) : '';

// Validation
$errors = [];

if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
    $errors[] = "Tous les champs sont requis.";
}

if ($password !== $confirm_password) {
    $errors[] = "Les mots de passe ne correspondent pas.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email est invalide.";
}

if (strlen($password) < 6) {
    $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
}

// Si aucune erreur, procéder à l'insertion
if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO accounts (firstname, lastname, email, password, role, elements) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Erreur de préparation : " . $conn->error);
    }

    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $hashed_password, $role, $elements);

    if ($stmt->execute()) {
        header("Location: http://57.129.134.101/login");
        exit;
    } else {
        $errors[] = "Erreur lors de l'inscription : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

// Affichage des erreurs
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}
?>
