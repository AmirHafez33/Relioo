<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // دومين React
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

session_start();
require_once "../userDataBase/database.php"; // Your DB connection

// $userId = $_SESSION['login_user_data']['id'];
// $result = $conn->query("SELECT * FROM notifications WHERE user_id = $userId AND is_read = 0 ORDER BY created_at DESC");


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
$userId = $user['id'];

$notification = new database("notifications");
$select = "SELECT * FROM notifications WHERE user_id = $userId AND is_read = 0 ORDER BY created_at DESC";
$result = $notification->conn->query($select);

$notifications = [];
while($row = $result->fetch_assoc()) {
      $users = new database("users");
      $by_user_id = $row['by_user_id'];
      $select = "SELECT * FROM users WHERE id = $by_user_id" ;
    $sel_user = $users->conn->query($select);
    
    if ($sel_user && $sel_user->num_rows > 0) {
        $user_data = $sel_user->fetch_assoc();

        // تأكد إن pic_url موجود قبل استخدامه
        $row['by_user_pic_url'] = isset($user_data['pic_url']) ? $user_data['pic_url'] : null;
    } else {
        $row['by_user_pic_url'] = null; // أو حط صورة افتراضية مثلاً
    }

    $notifications[] = $row;
}


echo json_encode($notifications);
