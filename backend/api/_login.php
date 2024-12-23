<?php
session_start();
include '_Database.php'; // Inclure le fichier de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification dans la table 'users'
    $query_users = "SELECT * FROM users WHERE email = ?";
    $stmt_users = $conn->prepare($query_users);
    $stmt_users->bind_param("s", $email);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();

    if ($result_users->num_rows === 1) {
        $user = $result_users->fetch_assoc();
        
        // Vérification du mot de passe dans la table 'users'
        if (password_verify($password, $user['password'])) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Rediriger en fonction du rôle
            if ($user['role'] === 'etudiant') {
                header('Location: http://localhost/siteweb/Etudiant.php');
                exit();
            } elseif ($user['role'] === 'enseignant') {
                header('Location: http://localhost/siteweb/Enseignant.php');
                exit();
            }
        } else {
            echo "Mot de passe incorrect pour la table users.";
        }
    } else {
        // Si l'email n'existe pas dans la table 'users', vérification dans la table 'accounts'
        $query_accounts = "SELECT * FROM accounts WHERE email = ?";
        $stmt_accounts = $conn->prepare($query_accounts);
        $stmt_accounts->bind_param("s", $email);
        $stmt_accounts->execute();
        $result_accounts = $stmt_accounts->get_result();

        if ($result_accounts->num_rows === 1) {
            $user = $result_accounts->fetch_assoc();
            
            // Vérification du mot de passe dans la table 'accounts'
            if (password_verify($password, $user['password'])) {
                // Stocker les informations de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Rediriger en fonction du rôle
                if ($user['role'] === 'etudiant') {
                    header('Location: http://localhost/siteweb/Etudiant.php');
                    exit();
                } elseif ($user['role'] === 'enseignant') {
                    header('Location: http://localhost/siteweb/Enseignant.php');
                    exit();
                }
            } else {
                echo "Mot de passe incorrect pour la table accounts.";
            }
        } else {
            echo "Identifiants invalides.";
        }
    }
}
?>
