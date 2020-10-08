<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
session_start();

$title = 'ReadMe';
$select_content_types_query = 'SELECT * FROM content_types;';
$select_posts_by_type_query = "SELECT posts.*, users.username, users.avatar, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id WHERE content_types.id = ? ORDER BY view_count DESC;";
$select_posts_query = 'SELECT posts.*, users.username, users.avatar, content_types.type_class, COUNT(likes.post_id) AS likes, COUNT(comments.post_id) AS comments
FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
LEFT OUTER JOIN comments ON posts.id = comments.post_id
LEFT OUTER JOIN likes ON posts.id = likes.post_id GROUP BY posts.id
ORDER BY dt_add DESC';
if ($_SESSION['is_auth'] == 1) {
    $user['id'] = $_SESSION['id'];
    $user['name'] = $_SESSION['username'];
    $user['avatar'] = $_SESSION['avatar'];
    $is_auth = $_SESSION['is_auth'];
    $con = db_connect("localhost", "root", "", "readme");
    if (isset($_GET['post_type'])) {
        $post_type = $_GET['post_type'];
        $posts_mysqli = secure_query($con, $select_posts_by_type_query, 'i', $post_type);
        $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    } else {
        $posts_mysqli = mysqli_query($con, $select_posts_query);
        $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    }
    $content_types_mysqli = mysqli_query($con, $select_content_types_query);
    $content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
    $page_content = include_template('main.php', ['posts' => $posts, 'content_types' => $content_types, 'post_type' => $post_type]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user, 'title' => $title, 'is_auth' => $is_auth]);
    print($layout_content);
    mysqli_close($con);
    exit();
}

header("Location: index.php");
