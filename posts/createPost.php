<?php
session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");
$post_data = json_decode(file_get_contents('php://input'));
$user_id = $_SESSION['login_user_data']['id'];
// $user_id = $user_id;
$text = $post_data->text ?? '';
$title = $post_data->title ?? '';
$movie_id = $post_data->movie_id ?? '';
$created_at = $post_data->createdAt ??'';
$likes = $post_data->likes ??'';
$comments = $post_data->comments ??'';
$rate = $post_data->rate ??'';
$create_post = new database("posts");
$post = $create_post->insert([
    "user_id"=>$user_id,
    "movie_id"=>$movie_id,
    "text"=>$text,
    "title"=>$title,
    "rate"=>$rate,
    "likes"=>0,
    "comments"=>0
]);

echo json_encode("\n post created successfuly");
?>