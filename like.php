<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */
$select_post_query = "SELECT posts.id FROM posts WHERE posts.id = ?";
$like_query = "INSERT INTO likes SET user_id = ?, post_id = ?";

$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}
if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}
$post_id = $_GET['id'];
$user_id = $_SESSION['id'];
$post_mysqli = secure_query($connection, $select_post_query, $post_id);
$post = mysqli_fetch_assoc($post_mysqli);
secure_query($connection, $like_query, $user_id, $post['id']);
header('Location: ' . $_SERVER['HTTP_REFERER']);
