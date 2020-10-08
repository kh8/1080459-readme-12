<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
session_start();

$select_user_query = "SELECT users.username, users.avatar, users.id FROM users WHERE users.id = ?";
$count_user_posts_query = "SELECT COUNT(*) FROM posts WHERE author_id = ?;";
$count_user_subscribers_query = "SELECT COUNT(*) FROM subscribe WHERE author_id = ?;";

if ($_SESSION['is_auth'] == 1) {
    $author_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];
    $user['name'] = $_SESSION['username'];
    $user['id'] = $_SESSION['id'];
    $user['avatar'] = $_SESSION['avatar'];
    $con = db_connect("localhost", "root", "", "readme");
    $author_mysqli = secure_query($con, $select_user_query, 'i', $author_id);
    $author = mysqli_fetch_assoc($author_mysqli);
    $posts_count_mysqli = secure_query($con, $count_user_posts_query, 'i', $author_id);
    $author['posts'] = mysqli_fetch_row($posts_count_mysqli)[0];
    $subscribers_count_mysqli = secure_query($con, $count_user_subscribers_query, 'i', $author_id);
    $author['subscribers'] = mysqli_fetch_row($subscribers_count_mysqli)[0];
    $page_content = include_template('user-profile.php', ['user' => $user, 'author' => $author, 'posts_count' => $posts_count, 'subscribers_count' => $subscribers_count]);
    print($page_content);
    exit();
}
header("Location: index.php");
