<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();

require_once "../userDataBase/database.php";
// header("Content-Type:application/json");

// $user_id = $_SESSION['login_user_data']['id'];

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

$post_data = json_decode(file_get_contents('php://input'));

$post_id = $post_data->post_id ?? '';

$post = new database("posts");
$old_post = $post->select("post_id",$post_id);
echo(json_encode(["old post"=>$old_post]));

$text = $post_data->text ?? $old_post['text'];
$title = $post_data->title ?? $old_post['title'] ;
$rate = $post_data->rate ?? $old_post['rate'];


$create_post = new database("posts");
$Updated_post = $create_post->update([
    "text"=>$text,
    "title"=>$title,
    "rate"=>$rate

],$post_id);

echo(json_encode(["success"=>true,"message"=>"post updated successfuly"]));
?>