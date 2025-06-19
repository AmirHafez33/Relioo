<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");

$post_data = json_decode(file_get_contents('php://input'));
$post_id = $post_data->post_id ?? '';

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
$user_name = $user['user_name'];

$like = new database("likes");
$like_check = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
$query = $like->conn->query($like_check);
// $like_exsist = count($query);
$like_exsist = $query->num_rows;


// get the number of total likes before update
$update_post = new database("posts");
$select_likes = "SELECT * FROM posts WHERE post_id = '$post_id'";
$result = $update_post->conn->query($select_likes);
    $row = $result->fetch_assoc();
    $old_likes = $row['likes']; // assuming your column is named 'likes'
   

if($like_exsist == 1){
    $like_delete = "DELETE FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id' ";
    $delete_like = $like->conn->query($like_delete);
    $total_likes = $old_likes - 1 ;
}else{
    $insert_like = $like->insert([
        "user_id"=>$user_id,
        "post_id"=>$post_id
    ]);
    $total_likes = $old_likes + 1;
    $post_owner_id = $row['user_id'];
  // add notificatoin
if($row['user_id'] == $user['id']){

}else{
$notification = new database("notifications");
$insert_notification = $notification->addNotification($post_owner_id, "{$user_name} commented on your review!", "Allposts.php?post_id=$post_id");
}
echo(json_encode(["sucess"=>true , "message"=>"like inserted successfully"]));
}

$update = "UPDATE posts SET likes = $total_likes WHERE post_id = '$post_id' ";
$update_query = $update_post->conn->query($update);
