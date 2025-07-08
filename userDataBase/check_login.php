
<?php
// session_start();
// require "database.php";
// extract($_POST);

// $user = new database("users");

// if ($user->select("email", $email)['password'] === md5($password)) {
    
//     $_SESSION['login_user_id'] = $user->select("email", $email)['id'];
//     echo $_SESSION['login_user_id'];
//     echo "\n login succssefull";
// } else {
//     echo "user not found";
// }

session_start();
require "database.php";

// Always access POST variables directly
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Simple check for empty inputs
if (empty($email) || empty($password)) {
    echo "Please enter both email and password.";
    exit;
}

$user = new database("users");

// Fetch user by email once
$user_data = $user->select("email", $email);

if ($user_data && password_verify($password, $user_data['password'])) {
    $_SESSION['login_user_id'] = $user_data['id'];
    echo $_SESSION['login_user_id'];
    echo "\nLogin successful";
} else {
    echo "User not found or wrong password";
}

