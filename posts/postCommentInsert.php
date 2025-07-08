<?php

session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");
$post_data = json_decode(file_get_contents('php://input'));
$post_id = $post_data->post_id ?? '';
$text = $post_data->text ?? '' ;
$user_id = $_SESSION['login_user_data']['id'];
$comment = new database("comments");
$insert_comment = $comment->insert([
    "user_id"=>$user_id,
    "post_id"=>$post_id,
    "text"=>$text
]);

    echo json_encode(["status"=>"success","message"=>"comment inserted successfuly"]);
// $likes = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";

/**************** update num of comments on post ***************** */

$update_post = new database("posts");
$select_comments = "SELECT * FROM posts WHERE id = '$post_id'";
$result = $update_post->conn->query($select_comments);
    $row = $result->fetch_assoc();
    $old_comments = $row['comments']; // assuming your column is named 'likes'
   
$total_comments = $old_comments + 1 ;

$update = "UPDATE posts SET comments = $total_comments WHERE id = '$post_id' ";
$update_query = $update_post->conn->query($update);
