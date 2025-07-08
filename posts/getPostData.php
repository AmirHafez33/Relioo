<?php
session_start();
header("Content-Type:application/json");
require_once "../userDataBase/database.php";


$post_id = json_decode(file_get_contents('php://input'));
$user_id = $_SESSION['login_user_data']['id'];

$post_id = $post_id->post_id ?? '';
$_SESSION['post_id'] = $post_id;
$post = new database("posts");
$select_data = $post->select("id",$post_id);
print_r(json_encode($select_data));