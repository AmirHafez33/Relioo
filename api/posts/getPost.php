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
$user_id = $user['id'];
// $post_data = json_decode(file_get_contents('php://input'));
// if(isset($_GET['post_id'])){
// $post_id = $_GET['post_id'] ;
// $post = new database("posts");

// $post = $post->select("post_id",$post_id);

// // echo(json_encode($post));

// $post_user_id = $post[0]['user_id'];
// $post_user = new database("users");
// $post_user_data = $post_user->select("id",$post_user_id);


$post_data = json_decode(file_get_contents('php://input'));

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    
    $post_db = new database("posts");
    $post = $post_db->select("post_id", $post_id); // بيرجع array of rows

    if (count($post) > 0) {
        $post_user_id = $post[0]['user_id'];

        $post_user = new database("users");
        $post_user_data = $post_user->select("id", $post_user_id); // بيرجع array of user row(s)

        // دمج بيانات المستخدم داخل نفس العنصر الخاص بالبوست
        $post[0] += $post_user_data[0]; // ← أضفنا user data كـ object داخل post

        // echo json_encode($post[0]);
}

$movie_id =$post[0]['movie_id'];;
// echo(json_encode([$movie_id]));

$movie = new database("movies");
$movie = $movie->select("movie_id",$movie_id);


$likes = new database("likes");
$likes = $likes->select("post_id",$post_id);


$comments = new database("comments");
$comments = $comments->select("post_id",$post_id);
$post_comments = [] ;
foreach($comments as $comment){
    $user = new database("users");
    $comment_user = $user->select("id",$comment['user_id']);
    $post_comments[] = $comment+$comment_user[0];
}
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

echo(json_encode(["success"=>true,"post-data"=>$post[0],"is_liked"=>$is_liked,"is_bookmarked"=>$is_bookmarked,"movie_data"=>$movie,"post-likes"=>$likes,"post-comments"=>$post_comments]));
}else{
    echo(json_encode(["success"=>false,"message"=>"unknown post id"]));
}
?>