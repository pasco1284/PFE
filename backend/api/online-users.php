<?php
include '_Database.php';

$query = "SELECT id, name FROM accounts WHERE status = 'online'";
$result = $conn->query($query);

$onlineUsers = [];
while ($row = $result->fetch_assoc()) {
    $onlineUsers[] = $row;
}

echo json_encode($onlineUsers);
?>
