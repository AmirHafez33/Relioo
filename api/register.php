<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
// header('Content-Type: application/json');
require_once "userDataBase/database.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get data from POST body
    $data = json_decode(file_get_contents("php://input"));

    $name = $data->name ?? '';
    $username = $data->username ?? '';
    $email = $data->email ?? '';
    $password = $data->password ?? '';
    $birth_date = $data->birth_date ??  date("Y-m-d H:i:s");
    $bio = $data->bio ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($birth_date)) {
        echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
        exit;
    }

    // Check if the email is already in use
    $register = new database("users");
    if ($register->select("email", $email)) {
        echo json_encode(["status" => "error", "message" => "Email is already used."]);
        exit;
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $insert_success = $register->insert([
        "name" => $name,
        "user_name" => $username,
        "email" => $email,
        "password" => $hashed_password,
        "birth_date" => $birth_date,
        "bio" => $bio,
        "onboarded"=>0,
        "api_token"=>1
        
    ]);
    $inserted_id = $register->conn->insert_id;
    $_SESSION['inserted_id']=$inserted_id;
    if (isset($_FILES['image']['name'])) {
        if ($_FILES['image']['name']) {
            $imgname = $_FILES['image']['name'] ?? 'defult';
            $tmp = $_FILES['image']['tmp_name'] ?? 'defult';
            if ($_FILES['image']['error'] == 0) {
                $extensions = ['jpg', 'png', 'gif'];
                $ext = pathinfo($imgname, PATHINFO_EXTENSION) ?? 'png';
                if (in_array($ext, $extensions)) {
                    $newName = uniqid() . "." . $ext;
                    move_uploaded_file($tmp, "../images/$newName");
                    // $imgnames[] = $newName;
                    $img_query = $register->update(["pic_url" => "$newName"], $inserted_id);
                } else {
                    echo json_encode("file error");
                }
            } else {
                echo json_encode("no image");
            }
        }
    }
    echo json_encode(["status"=>"success" , "message"=>"register done successfuly"]);
}
