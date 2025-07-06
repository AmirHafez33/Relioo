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

$data = json_decode(file_get_contents('php://input'));
$id = $data->id ?? false ;
// echo(json_encode($id));
if($id){
if($id!=$user['id']){
    echo (json_encode(["is_currentuser" => false]));
    
    $is_followed = new database("user_following");
    $sel_is_followed = "SELECT * FROM user_following WHERE user_id = $user_id AND followed_id = $id ";
$result = $is_followed->conn->query($sel_is_followed);

if ($result->num_rows > 0) {
    // The post is liked by the user
    $is_followed = true;
} else {
    // Not liked
    $is_followed = false;
}
echo (json_encode(["is_followed" => $is_followed]));

    $user_posts = new database("posts");
    $user_posts_array = $user_posts->select("user_id", $id);
    $user = new database("users");
    $user_data = $user->select("id", $id);
    
    echo (json_encode(["AnotherUserData" => $user_data]));

    require_once "UserFollowList.php";
    $posts = [] ;
    foreach ($user_posts_array as $post) {
        // print(json_encode($post));
        $posts[] = $post;
    }
    print(json_encode(["posts" => $posts]));

    // echo json_encode(["status" => "success", "message" => "Login successful", "user_data" => $user]);
}else{
    echo(json_encode(["status"=>"failed" , "message"=>"please login"]));
}
exit ;
}


if (isset($user)) {
    $user_posts = new database("posts");
    $user_posts_array = $user_posts->select("user_id", $user_id);
    $user = new database("users");
    $user_data = $user->select("id", $user_id);
    
    echo (json_encode(["is_currentuser" => true]));
    echo (json_encode(["userData" => $user_data]));
    
    require_once "UserFollowList.php";
    $posts = [] ;
    foreach ($user_posts_array as $post) {
        // print(json_encode($post));
            $movies = new database("movies");
            $likesDB = new database("likes");
            $commentsDB = new database("comments");

       $movie = $movies->select("movie_id",$post['movie_id']);
    $post_likes = $likesDB->select("post_id", $post['post_id']);
    $post_comments = $commentsDB->select("post_id", $post['post_id']);
    $post_id = $post['post_id'];
    if(isset($user_id)){
    /************************check this post is liked from the current user********************************* */
$is_liked = new database("likes");
$sel_is_liked = "SELECT * FROM likes WHERE user_id = $user_id AND post_id = $post_id ";
$result = $is_liked->conn->query($sel_is_liked);

if ($result->num_rows > 0) {
    // The post is liked by the user
    $is_liked = true;
} else {
    // Not liked
    $is_liked = false;
}

/************************check this post is bookmarked from the current user********************************* */
$is_bookmarked = new database("bookmarks");
$sel_is_liked = "SELECT * FROM bookmarks WHERE user_id = $user_id AND post_id = $post_id ";
$result = $is_bookmarked->conn->query($sel_is_liked);

if ($result->num_rows > 0) {
    // The post is liked by the user
    $is_bookmarked = true;
} else {
    // Not liked
    $is_bookmarked = false;
}
}else{
    $is_bookmarked = false;
    $is_liked = false;
}
    // Build a structured array for each post
    $fullPostList[] = [
        "post" => [
            "post_data" => $post,
            "movie_data" => $movie,
            "likes" => $post_likes,
            "comments" => $post_comments,
            "is_liked" => $is_liked,
            "is_bookmarked" => $is_bookmarked
        ]
    ];
}

echo json_encode(["postsList"=>$fullPostList]);

    // echo json_encode(["status" => "success", "message" => "Login successful", "user_data" => $user]);
}else{
    echo(json_encode(["status"=>"failed" , "message"=>"please login"]));
}