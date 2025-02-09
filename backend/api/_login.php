<?php
session_start();
include '_Database.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query_users = "SELECT * FROM users WHERE email = ?";
    $stmt_users = $conn->prepare($query_users);
    $stmt_users->bind_param("s", $email);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();

    if ($result_users->num_rows === 1) {
        $user = $result_users->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Mettre à jour le statut de l'utilisateur à 'online'
    $update_status_query = "UPDATE users SET status = 'online' WHERE id = ?";
    $stmt_update_status = $conn->prepare($update_status_query);
    $stmt_update_status->bind_param("i", $user['id']);
    $stmt_update_status->execute();

            // Redirection selon le rôle
            if ($user['role'] === 'etudiant') {
                header('Location: http://57.129.134.101/Etudiant.php');
                exit();
            } elseif ($user['role'] === 'enseignant') {
                header('Location: http://57.129.134.101/Enseignant.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
            header('Location: http://57.129.134.101/login');
            exit();
        }
    } else {
        $query_accounts = "SELECT * FROM accounts WHERE email = ?";
        $stmt_accounts = $conn->prepare($query_accounts);
        $stmt_accounts->bind_param("s", $email);
        $stmt_accounts->execute();
        $result_accounts = $stmt_accounts->get_result();

        if ($result_accounts->num_rows === 1) {
            $user = $result_accounts->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Mettre à jour le statut de l'utilisateur à 'online'
    $update_status_query = "UPDATE accounts SET status = 'online' WHERE id = ?";
    $stmt_update_status = $conn->prepare($update_status_query);
    $stmt_update_status->bind_param("i", $user['id']);
    $stmt_update_status->execute();

                if ($user['role'] === 'etudiant') {
                    header('Location: http://57.129.134.101/Etudiant.php');
                    exit();
                } elseif ($user['role'] === 'enseignant') {
                    header('Location: http://57.129.134.101/Enseignant.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = "Mot de passe incorrect.";
                header('Location: http://57.129.134.101/login');
                exit();
            }
        } else {
            $_SESSION['error'] = "Identifiants invalides.";
            header('Location: http://57.129.134.101/login');
            exit();
        }
    }
}
?>
