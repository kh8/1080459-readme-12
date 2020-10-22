<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
$select_post_query = "SELECT posts.id FROM posts WHERE posts.id = ?";
$like_query = "INSERT INTO likes SET user_id = ?, post_id = ?";

session_start();
if ($_SESSION['is_auth'] != 1) {
    header("Location: index.php");
    exit();
}
if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}
$post_id = $_GET['id'];
$con = db_connect("localhost", "root", "", "readme");
$user_id = $_SESSION['id'];
$post_mysqli = secure_query($con, $select_post_query, 's', $post_id);
$post = mysqli_fetch_assoc($post_mysqli);
secure_query($con, $like_query, 'ii', $user_id, $post['id']);
header('Location: ' . $_SERVER['HTTP_REFERER']);
