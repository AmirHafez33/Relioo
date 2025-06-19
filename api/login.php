<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
session_start();
// header('Content-Type: application/json');
require_once "userDataBase/database.php";  // Include your database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get data from POST body
    $data = json_decode(file_get_contents("php://input"));

    $email = $data->email ?? '';
    $password = $data->password ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and Password are required."]);
        exit;
    }

    // Create a new user object to interact with the database
    $user = new database("users");
    $user_data = $user->selectUser("email", $email);
    if (!isset($user_data['password'])) {

        echo json_encode(["status" => "error", "message" => "email not exsist."]);
        exit();
    }
    if (password_verify($password, $user_data['password'])) {
        // If successful, set the session or generate a token (for API security)
        $_SESSION['login_user_data'] = $user_data; // session for all data about the user has login
        $user_id = $user_data['id'];
        
        // توليد توكن (عشوائي مثلاً)
        $token = bin2hex(random_bytes(8));
        // حفظه في قاعدة البيانات مع الـ user
        $stmt = new database("users");
        $stmt = $stmt->conn->query("UPDATE users SET api_token = '$token' WHERE id = '$user_id' ");
        // Respond with success
        // echo json_encode(["status" => "success", "message" => "Login successful", "user_data" => $user_data]);
        echo json_encode([
            "success" => true,
            "token" => $token,
            "user_data" => $user_data
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
