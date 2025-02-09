<?php
include '_Database.php';

$query = "SELECT id, name FROM users WHERE status = 'online'";
$result = $conn->query($query);

$online_users = [];
while ($row = $result->fetch_assoc()) {
    $online_users[] = $row;
}

echo json_encode($online_users);
?>