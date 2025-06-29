<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");

$movie_data = json_decode(file_get_contents('php://input'));
$movie_id = $movie_data->movie_id ?? '';

/********************التحقق من التوكن************************* */
// استخراج التوكن من الهيدر
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(["error" => "Token missing"]);
    exit;
}

$token = trim(str_replace('Bearer', '', $authHeader));

// تحقق من التوكن في قاعدة البيانات
 $stmt = new database("users");
$stmt = $stmt->conn->query("SELECT * FROM users WHERE api_token = '$token' ");
if ($stmt->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}

$user = $stmt->fetch_assoc(); // ← ممكن تستخدمه في البوست أو التعليقات

$user_id = $user['id'];
$user_name = $user['name'];

$activities = new database("activities");

// $notification = new database("notifications");
$select = "SELECT * FROM activities WHERE user_id = $user_id AND is_read = 0 ORDER BY created_at DESC";
$result = $activities->conn->query($select);


$activities = [];
while($row = $result->fetch_assoc()) {
     
    $activities[] = $row;
}


echo json_encode($activities);
