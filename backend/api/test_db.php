<?php
$mysqli = new mysqli("localhost", "root", "12345678", "siteweb");

if ($mysqli->connect_error) {
    die("Échec de connexion : " . $mysqli->connect_error);
} else {
    echo "Connexion réussie à la base de données !";
}
?>