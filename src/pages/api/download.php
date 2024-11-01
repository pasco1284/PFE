<?php
$conn = new mysqli("localhost", "username", "password", "siteweb");

$result = $conn->query("SELECT * FROM files ORDER BY subject");

while ($row = $result->fetch_assoc()) {
    echo "<div class='course-category'>";
    echo "<h3>" . htmlspecialchars($row['subject']) . "</h3>";
    echo "<ul class='file-list'>";
    echo "<li class='file-item'>";
    echo "<span>" . htmlspecialchars($row['course_title']) . "</span>";
    echo "<a href='" . htmlspecialchars($row['file_path']) . "' download>Télécharger</a>";
    echo "</li>";
    echo "</ul>";
    echo "</div>";
}

$conn->close();
?>