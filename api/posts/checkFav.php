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


/************************check this movie is in favorities list for the current user********************************* */
$is_fav = new database("favorities");
$sel_is_fav = "SELECT * FROM favorities WHERE user_id = $user_id AND post_id = $post_id ";
$result = $is_fav->conn->query($sel_is_fav);

if ($result->num_rows > 0) {
    // The post is fav by the user
    $is_fav = true;
} else {
    // Not fav
    $is_fav = false;
}


echo(json_encode(["success"=>true,"is_fav"=>$is_fav]));
