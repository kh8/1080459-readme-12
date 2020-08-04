<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$user_name = 'Михаил';
$title = 'ReadMe';
$is_auth = 1;
$select_content_types_query = 'SELECT * FROM content_types;';
$con = db_connect("localhost", "root", "", "readme");
if (isset($_GET['post_type'])) {
    $post_type = $_GET['post_type'];
    $select_posts_query = "SELECT posts.*, users.username, users.avatar, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id WHERE content_types.id = ? ORDER BY view_count DESC;";
    $posts_mysqli = secure_query($con, $select_posts_query, 'i', $post_type);
    $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
} else {
    $select_posts_query = 'SELECT posts.*, users.username, users.avatar, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id ORDER BY view_count DESC;';
    $posts_mysqli = mysqli_query($con, $select_posts_query);
    $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
}
$content_types_mysqli = mysqli_query($con, $select_content_types_query);
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$page_content = include_template('main.php', ['posts' => $posts, 'content_types' => $content_types, 'post_type' => $post_type]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user_name, 'title' => $title, 'is_auth' => $is_auth]);
print($layout_content);
mysqli_close($con);

