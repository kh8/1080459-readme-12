<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

session_start();

$select_posts_query = 'SELECT posts.*, users.username, users.avatar, content_types.type_class, COUNT(likes.post_id) AS likes, COUNT(comments.post_id) AS comments
FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
LEFT OUTER JOIN comments ON posts.id = comments.post_id
LEFT OUTER JOIN likes ON posts.id = likes.post_id GROUP BY posts.id
ORDER BY dt_add DESC';
$con = db_connect("localhost", "root", "", "readme");
$posts_mysqli = mysqli_query($con, $select_posts_query);
$posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
if ($_SESSION['is_auth'] == 1) {
    $user['id'] = $_SESSION['id'];
    $user['name'] = $_SESSION['username'];
    $user['avatar'] = $_SESSION['avatar'];
    $page_content = include_template('user-feed.php', ['posts' => $posts, 'content_types' => $content_types, 'user' => $user]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user, 'title' => $title, 'is_auth' => $is_auth]);
    print($layout_content);
    mysqli_close($con);
    exit();
}
header("Location: index.php");
