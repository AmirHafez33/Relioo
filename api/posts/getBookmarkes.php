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

$bookmarkes = new database("bookmarks");
$sel_bookmarkes = $bookmarkes->select("user_id" , $user_id);
// echo(json_encode("bookmarked posts"));

foreach($sel_bookmarkes as $post){
        echo(json_encode(["success"=>true]));
        $posts = new database("posts");
        $post_data = $posts->select("post_id" ,$post['post_id'] );
        $bookmarked_posts[] = $post_data;
        
        foreach($post_data as $post){
            // echo(json_encode(["movie_id"=>$post['movie_id']]));
            $movies = new database("movies");
            $movie = $movies->select("movie_id",$post['movie_id']);
            echo(json_encode(["post_data"=>$post_data[0]+$movie[0]]));
        }
    }

