<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
require_once "../userDataBase/database.php"; // Your DB connection
// header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

$id = $data->notificatoin_id ?? '';
$notification = new database("notifications");
$read = $notification->conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id");

// $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id");
