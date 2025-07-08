<?php
// require "database.php";
// $register = new database("users");

// extract($_POST);

// if ($register->select("email", $email)) {
//     echo "email is not available";
// } else {
//     $password = md5($password);
//     $register->insert([
//         "name" => "$name",
//         "user_name"=>"$username",
//         "pic_url"=>"",
//         "email" => "$email",  // user login by email and password 
//         "password" => "$password",
//         // "join_date"=>"$join_date",
//         "birth_date"=>"$birth_date",
//         "bio"=>"$bio"
//     ]);
//     echo "data inserted without photo";
// }

// $inserted_id = $register->conn->insert_id;

// // for ($i = 0; $i < count($_FILES['image']['name']); $i++) {
//     if($_FILES['image']['name']){
//     $imgname = $_FILES['image']['name'];
//     $tmp = $_FILES['image']['tmp_name'];
//     if ($_FILES['image']['error'] == 0){
//         $extensions = ['jpg', 'png', 'gif'];
//         $ext = pathinfo($imgname, PATHINFO_EXTENSION);
//         if (in_array($ext, $extensions)) {
//                 $newName = uniqid() . "." . $ext;
//                 move_uploaded_file($tmp, "../images/$newName");
//                 // $imgnames[] = $newName;
//                 $img_query = $register->update(["pic_url"=>"$newName"],$inserted_id);
//         } else {
//             echo "file error";
//         }
//     } else {
//         echo "no image";
//     }
//     echo "\n register successfuly";
// }

require "database.php";
$register = new database("users");

$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';
$bio = $_POST['bio'] ?? '';

// Basic validation
if (empty($email) || empty($password) || empty($name) || empty($username)) {
    echo "Missing required fields.";
    exit;
}

// Check if email already exists
if ($register->select("email", $email)) {
    echo "Email is not available";
    exit;
}

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user data without image
$insert_success = $register->insert([
    "name" => "$name",
    "user_name" => "$username",
    "pic_url" => "", // image will be added later
    "email" => "$email",
    "password" => "$hashed_password",
    "birth_date" => "$birth_date",
    "bio" => "$bio"
]);
$inserted_id = $register->conn->insert_id;
        if($_FILES['image']['name']){
        $imgname = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        if ($_FILES['image']['error'] == 0){
            $extensions = ['jpg', 'png', 'gif'];
            $ext = pathinfo($imgname, PATHINFO_EXTENSION);
            if (in_array($ext, $extensions)) {
                    $newName = uniqid() . "." . $ext;
                    move_uploaded_file($tmp, "../images/$newName");
                    // $imgnames[] = $newName;
                    $img_query = $register->update(["pic_url"=>"$newName"],$inserted_id);
            } else {
                echo "file error";
            }
        } else {
            echo "no image";
        }
        echo "\n register successfuly";
    }
