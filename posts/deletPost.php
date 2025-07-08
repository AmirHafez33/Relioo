<?php
session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");
$post_data = json_decode(file_get_contents('php://input'));
$user_id = $_SESSION['login_user_data']['id'];
$post_id = $post_data->post_id;

$post = new database("posts");
$delete_post = $post->deletePost($user_id,$post_id);

echo json_encode(["message"=>"the post deleted successfuly"]);

