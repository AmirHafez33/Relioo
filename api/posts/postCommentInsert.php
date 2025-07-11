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


// header("Content-Type:application/json");
$post_data = json_decode(file_get_contents('php://input'));
$post_id = $post_data->post_id ?? '';
$text = $post_data->text ?? '' ;
$user_id = $user['id'];
$user_name = $user['name'];
$comment = new database("comments");
$insert_comment = $comment->insert([
    "user_id"=>$user_id,
    "post_id"=>$post_id,
    "text"=>$text
]);
    $inserted_id = $comment->conn->insert_id;
$last_comment = $comment->select("id",$inserted_id);

$post = new database("posts");
$post = $post->select("post_id",$post_id);

$post_user = new database("users");
$post_user = $post_user->select("id",$post[0]['user_id']);
// echo(json_encode($post_user));
$likes = new database("likes");
$likes = $likes->select("post_id",$post_id);



    echo json_encode(["status"=>"success","message"=>"comment inserted successfuly","post-data"=>$post[0]+$post_user[0],"comment_data"=>$last_comment[0]+$user]);

/**************** update num of comments on post ***************** */

$update_post = new database("posts");
$select_comments = "SELECT * FROM posts WHERE post_id = '$post_id'";
$result = $update_post->conn->query($select_comments);
    $row = $result->fetch_assoc();
    $old_comments = $row['comments']; // assuming your column is named 'likes'
   $post_owner_id = $row['user_id'];
$total_comments = $old_comments + 1 ;

// get post data
$post = "SELECT * FROM posts WHERE post_id = '$post_id'";
$result = $update_post->conn->query($post);
$row = $result->fetch_assoc();
    $post_text = $row['text'];

$update = "UPDATE posts SET comments = $total_comments WHERE post_id = '$post_id' ";
$update_query = $update_post->conn->query($update);
die();
// add notificatoin
if($row['user_id'] == $user['id']){

}else{
$notification = new database("notifications");
$insert_notification = $notification->addNotification($post_owner_id, "{$user_name} commented on your review", $post_id ,$post_text,$user_id);

// add activity to database
$activity = new database("activities");
$action = "comment";
$insert_activity = $activity->addActivity($user_id, "you added comment on a review", $post_id , $post_text , $action);


}