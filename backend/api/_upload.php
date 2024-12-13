<?php
$target_dir = "uploads/";
$subject = $_POST['subject'];
$course_title = $_POST['course_title'];
$exercise_title = $_POST['exercise_title'];

$target_file = $target_dir . basename($_FILES["file"]["name"]);
$file_type = $_POST['file_type']; // 'course' or 'exercise'

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    $conn = new mysqli("localhost", "username", "password", "siteweb");
    
    $stmt = $conn->prepare("INSERT INTO files (subject, course_title, exercise_title, file_path, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $subject, $course_title, $exercise_title, $target_file, $file_type);
    
    if ($stmt->execute()) {
        echo "Le fichier a été téléchargé avec succès!";
    } else {
        echo "Erreur lors de l'enregistrement dans la base de données.";
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "Erreur lors du téléchargement du fichier.";
}
?>
