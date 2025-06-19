<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();

// header('Content-Type: application/json');
require_once "userDataBase/database.php";
// require_once "register.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get data from POST body
    $data = json_decode(file_get_contents("php://input"));

    $name = $data->name ?? '';
    $username = $data->username ?? '';
    // $username = $data->image ?? '';
    $bio = $data->bio ?? '';

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
$user = $stmt->fetch_assoc();

$register = new database("users");
 if ($register->select("id", $user['id'])) {

        $complete_data = $register->update(
            [
                "name" => $name ?? $user['name'],
                "user_name" => $username ?? $user['user_name'],
                "bio" => $bio ?? $user['bio'],
                "pic_url" => $pic_url ?? $user['pic_url'],
                "onboarded"=>1
            ],
            $user['id']
        );
    }

    if (isset($_FILES['image']['name'])) {
        if ($_FILES['image']['name']) {
            $imgname = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];
            if ($_FILES['image']['error'] == 0) {
                $extensions = ['jpg', 'png', 'gif'];
                $ext = pathinfo($imgname, PATHINFO_EXTENSION);
                if (in_array($ext, $extensions)) {
                    $newName = uniqid() . "." . $ext;
                    move_uploaded_file($tmp, "../images/$newName");
                    // $imgnames[] = $newName;
                    $img_query = $register->update(["pic_url" => "$newName"], $user['id']);
                } else {
                    echo json_encode("file error");
                }
            } else {
                echo json_encode("no image");
            }
        }
    }
    echo json_encode([
        "success"=>true,
        "user_data"=>$user
    ]);
}
