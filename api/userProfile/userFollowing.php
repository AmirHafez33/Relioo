<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";

    /************************** token validation ************************** */

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
// header("Content-Type:application/json");
$following = json_decode(file_get_contents('php://input'));

$followed_id = $following->followed_id ?? '' ;


$follow = new database("user_following");
$select = "SELECT * FROM user_following WHERE user_id = $user_id AND followed_id = $followed_id ";
$select = $follow->conn->query($select);
$sel_data = $select->fetch_assoc();

$select_exsist = $sel_data['followed_id'] ?? 0 ;


if($select_exsist>0){
    $delete_follow = $follow->delete("followed_id",$followed_id);
    echo json_encode(["success"=>true,"message"=>"you are unfollow this user"]);

}else{
    $insert_follow = $follow->insert([
        "user_id"=>$user_id,
        "followed_id"=>$followed_id
    ]);
    echo json_encode(["success"=>true,"message"=>"you are following this user now"]);
    
   // add notificatoin
if($followed_id == $user['id']){

}else{
$notification = new database("notifications");
$insert_notification = $notification->addNotification($followed_id, "{$user_name} follow your account",$post_id=null,$post_text=null,$user_id);

// add activity to database
$activity = new database("activities");
$action = "follow";
$insert_activity = $activity->addActivity($user_id, "you followed new account", $followed_id , $post_text , $action);


}
}
