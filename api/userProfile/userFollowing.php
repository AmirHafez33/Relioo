<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";

// header("Content-Type:application/json");
$following = json_decode(file_get_contents('php://input'));
$user_id = $_SESSION['login_user_data']['id'];
$user_name = $_SESSION['login_user_data']['user_name'];

$followed_id = $following->followed_id ?? '' ;


$follow = new database("user_following");
$select = $follow->select("followed_id",$followed_id);

$select_exsist = $select['followed_id'] ?? 0 ;


if($select_exsist>0){
    $delete_follow = $follow->delete("followed_id",$followed_id);
    echo json_encode(["message"=>"you are unfollow this user"]);

}else{
    $insert_follow = $follow->insert([
        "user_id"=>$user_id,
        "followed_id"=>$followed_id
    ]);
    echo json_encode(["message"=>"you are following this user now"]);
    
   // add notificatoin
if($row['user_id'] == $user['id']){

}else{
$notification = new database("notifications");
$insert_notification = $notification->addNotification($post_owner_id, "{$user_name} commented on your review!", "Allposts.php?post_id=$post_id");
}
}
