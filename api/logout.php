<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
// header('Content-Type: application/json');
header('Content-Type: application/json');
session_start();

// Destroy session and logout the user
session_unset();
session_destroy();

require_once 'userDataBase/database.php';
header("Content-Type: application/json");

// احصل على الهيدر
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$token = trim(str_replace('Bearer', '', $authHeader));
// echo(json_encode($token));
 $stmt = new database("users");
// إبطال التوكن
 $stmt = $stmt->conn->query("UPDATE users SET api_token = '' WHERE api_token = '$token' ");
 
 echo json_encode(["message" => "Logged out successfully"]);
?>
