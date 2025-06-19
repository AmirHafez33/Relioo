<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";

// header("Content-Type:application/json");
/******************************************************* */
$post_data = json_decode(file_get_contents('php://input'));
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

if (isset($user)) {
    $text = $post_data->text ?? '';
$title = $post_data->title ?? '';
$movie_id = $post_data->movie_id ?? '';
$created_at = $post_data->createdAt ??'';
$likes = $post_data->likes ??'';
$comments = $post_data->comments ??'';
$rate = $post_data->rate ??'';
$posterLink = $post_data->posterLink ??'';
$year = $post_data->year ??'';

if(empty($movie_id) || empty($text) || empty($rate)){
    echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
        exit;
}
$create_post = new database("posts");
$post = $create_post->insert([
    "user_id"=>$user_id,
    "movie_id"=>$movie_id,
    "text"=>$text,
    "rate"=>$rate,
    "likes"=>0,
    "comments"=>0
]);

$create_movie = new database("movies");
$sel_movie = $create_movie->select("movie_id",$movie_id);
if(!$sel_movie){
$movie = $create_movie->insert([
    
    "movie_id"=>$movie_id,
    "posterLink"=>$posterLink,
    "title"=>$title,
    "year"=>$year
   
]);

}else{
    // echo(json_encode(["message"=>"the movie is already inserted "]));
}
// echo json_encode("\n post created successfuly");

    $inserted_id = $create_post->conn->insert_id;
    $select_post = $create_post->select("post_id",$inserted_id);
    // $query = $create_post->conn->query($select_post);
    // $inserted_post = $select_post->fetch_assoc();
    
    echo json_encode(["status" => "success", "message" => "post inserted successful but the movie is inserted before", "post_data" => $select_post]);
}else{
    echo(json_encode(["status"=>"failed" , "message"=>"please login"]));
}
/******************************************************* */
// $user_id = $_SESSION['login_user_data']['id'];
// $user_id = $user_id;
?>