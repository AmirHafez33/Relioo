<?php

session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");
$post_data = json_decode(file_get_contents('php://input'));
$post_id = $post_data->post_id ?? '';
$user_id = $_SESSION['login_user_data']['id'];

$like = new database("likes");
$like_check = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
$query = $like->conn->query($like_check);
// $like_exsist = count($query);
$like_exsist = $query->num_rows;


// get the number of total likes before update
$update_post = new database("posts");
$select_likes = "SELECT * FROM posts WHERE id = '$post_id'";
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
}

$update = "UPDATE posts SET likes = $total_likes WHERE id = '$post_id' ";
$update_query = $update_post->conn->query($update);
