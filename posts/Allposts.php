<?php

session_start();
require_once "../userDataBase/database.php";

header("Content-Type:application/json");
$postsDB = new database("posts");
$posts = $postsDB->selectAllPosts();

$likesDB = new database("likes");
$commentsDB = new database("comments");

$fullPostList = [];

foreach ($posts as $post) {
    $post_likes = $likesDB->select("post_id", $post['id']);
    $post_comments = $commentsDB->select("post_id", $post['id']);

    // Build a structured array for each post
    $fullPostList[] = [
        "post" => [
            "post_data" => $post,
            "likes" => $post_likes,
            "comments" => $post_comments
        ]
    ];
}

echo json_encode(["postsList"=>$fullPostList]);
