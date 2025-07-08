<?php
session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");
$post_data = json_decode(file_get_contents('php://input'));
$user_id = $_SESSION['login_user_data']['id'];
// $user_id = $user_id;

if(isset($_SESSION['post_id']))
$post_id = $_SESSION['post_id'];
unset($_SESSION['post_id']);
$post = new database("posts");
$old_post = $post->select("id",$post_id);


$text = $post_data->text ?? $old_post['text'];
$title = $post_data->title ?? $old_post['title'] ;
$movie_id = $post_data->movie_id ?? $old_post['movie_id'];
$created_at = $post_data->createdAt ?? '';
$likes = $post_data->likes ?? $old_post['likes'];
$comments = $post_data->comments ?? $old_post['comments'];
$rate = $post_data->rate ??'';
$create_post = new database("posts");
$Updated_post = $create_post->update([
    // "user_id"=>$user_id,
    "movie_id"=>$movie_id,
    "text"=>$text,
    "title"=>$title,
    "rate"=>$rate
    // "likes"=>$old_post['likes'],
    // "comments"=>$old_post['comments']
],$post_id);

echo json_encode(["message"=>"post updated successfuly"]);
?>