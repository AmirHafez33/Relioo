<?php
// session_start();

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
require_once "../userDataBase/database.php";

// header("Content-Type:application/json");

/*********************************************************** */
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

$data = json_decode(file_get_contents('php://input'));
$id = $data->id ?? false ;
// echo(json_encode($id));
if($id){
if($id!=$user['id']){
 
    $following = new database("user_following");
    $followed_users = $following->select("user_id", $id);

    $list = new database("users");
    $following_list = [];

    if (isset($followed_users)) {
        foreach ($followed_users as $row) {
            $followed_id = $row['followed_id'];
            $user_data = $list->select("id", $followed_id); // Assumes this returns an array with one row
            if (!empty($user_data)) {
                $following_list[] = $user_data[0]; // Get the user_name of followed user
            }
        }
    }
    echo json_encode(["following_list" => $following_list]);

    // $user_id = $_SESSION['login_user_data']['id']; // current user

    // Get all users who follow you
    $followersTable = new database("user_following");
    $followers = $followersTable->select("followed_id", $id); // people who follow you

    $usersTable = new database("users");
    $followers_list = [];

    if (isset($followers)) {
        foreach ($followers as $row) {
            $follower_id = $row['user_id'];
            $user_data = $usersTable->select("id", $follower_id); // fetch follower user data

            if (!empty($user_data)) {
                $followers_list[] = $user_data[0]; // add full follower data (or just 'user_name')
            }
        }
        echo json_encode(["followers_list" => $followers_list]);
    }
    // echo json_encode(["status" => "success", "message" => "Login successful", "user_data" => $user]);
} else {
    echo (json_encode(["status" => "failed", "message" => "please login"]));
}
exit ;
}

if (isset($user)) {
    $following = new database("user_following");
    $followed_users = $following->select("user_id", $user_id);

    $list = new database("users");
    $following_list = [];

    if (isset($followed_users)) {
        foreach ($followed_users as $row) {
            $followed_id = $row['followed_id'];
            $user_data = $list->select("id", $followed_id); // Assumes this returns an array with one row
            if (!empty($user_data)) {
                $following_list[] = $user_data[0]; // Get the user_name of followed user
            }
        }
    }
    echo json_encode(["following_list" => $following_list]);

    // $user_id = $_SESSION['login_user_data']['id']; // current user

    // Get all users who follow you
    $followersTable = new database("user_following");
    $followers = $followersTable->select("followed_id", $user_id); // people who follow you

    $usersTable = new database("users");
    $followers_list = [];

    if (isset($followers)) {
        foreach ($followers as $row) {
            $follower_id = $row['user_id'];
            $user_data = $usersTable->select("id", $follower_id); // fetch follower user data

            if (!empty($user_data)) {
                $followers_list[] = $user_data[0]; // add full follower data (or just 'user_name')
            }
        }
        echo json_encode(["followers_list" => $followers_list]);
    }
    // echo json_encode(["status" => "success", "message" => "Login successful", "user_data" => $user]);
} else {
    echo (json_encode(["status" => "failed", "message" => "please login"]));
}