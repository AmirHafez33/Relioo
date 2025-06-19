<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";


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
$post_data = json_decode(file_get_contents('php://input'));
$post_id = $post_data->post_id ?? '';


$bookmark = new database("bookmarks");
$bookmark_check = "SELECT * FROM bookmarks WHERE user_id = '$user_id' AND post_id = '$post_id'";
$query = $bookmark->conn->query($bookmark_check);
// $like_exsist = count($query);
$bookmark_exsist = $query->num_rows;


if($bookmark_exsist == 1){
    $bookmark_delete = "DELETE FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id' ";
    $delete_bookmark = $like->conn->query($bookmark_delete);
      echo(json_encode(["sucess"=>true , "message"=>"post bookmark deleted successfully"]));

    // $total_likes = $old_likes - 1 ;
}else{
    $insert_bookmark = $bookmark->insert([
        "user_id"=>$user_id,
        "post_id"=>$post_id
    ]);
    // $total_likes = $old_likes + 1;
    // $post_owner_id = $row['user_id'];
  // add notificatoin
  echo(json_encode(["sucess"=>true , "message"=>"post bookmarked successfully"]));

}