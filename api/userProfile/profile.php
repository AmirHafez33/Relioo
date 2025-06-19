<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
require_once "../userDataBase/database.php";

/******************** session validation ****************** */
if (isset($_SESSION['login_user_data'])) {
    $user_id = $_SESSION['login_user_data']['id'];
    // header("Content-Type:application/json");
    require_once "../userDataBase/database.php";
    $user_posts = new database("posts");
    $user_posts_array = $user_posts->select("user_id", $user_id);
    $user = new database("users");
    $user_data = $user->select("id", $user_id);
    echo (json_encode(["userData" => $user_data]));

    require_once "UserFollowList.php";

    foreach ($user_posts_array as $post) {
        // print(json_encode($post));
        $posts[] = $post;
    }
    print(json_encode(["posts" => $posts]));
}

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


if (isset($user)) {
    $user_posts = new database("posts");
    $user_posts_array = $user_posts->select("user_id", $user_id);
    $user = new database("users");
    $user_data = $user->select("id", $user_id);
    echo (json_encode(["userData" => $user_data]));

    require_once "UserFollowList.php";

    foreach ($user_posts_array as $post) {
        // print(json_encode($post));
        $posts[] = $post;
    }
    print(json_encode(["posts" => $posts]));

    // echo json_encode(["status" => "success", "message" => "Login successful", "user_data" => $user]);
}else{
    echo(json_encode(["status"=>"failed" , "message"=>"please login"]));
}