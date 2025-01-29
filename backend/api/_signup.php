<?php
session_start();
include '_Database.php'; // Inclure le fichier de connexion avec mysqli

// Récupérer les données du formulaire
$firstname = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? '';
$elements = isset($_POST['elements']) ? implode(",", $_POST['elements']) : ''; // Convertir les éléments sélectionnés en une chaîne

// Validation des données
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

if (empty($errors)) {
    // Hasher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Préparer la requête d'insertion avec mysqli
    $query = "INSERT INTO accounts (firstname, lastname, email, password, role, elements) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    // Utiliser la méthode prepare() de mysqli
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    // Lier les paramètres
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $hashed_password, $role, $elements);

    // Exécuter la requête
    try {
        $stmt->execute();

        
        header("Location: http://57.129.134.101/login");
        exit;
    } catch (Exception $e) {
        $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
    }
}

// Afficher les erreurs, le cas échéant
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}
?>
