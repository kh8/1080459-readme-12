<?php
require_once('helpers.php');
require_once('functions.php');
$user_name = 'Михаил';
$title = 'ReadMe';
$is_auth = rand(0,1);
$con = mysqli_connect("localhost", "root", "", "readme");
if ($con == false) {
    $error = mysqli_connect_error();
    print($error);
} else {
    mysqli_set_charset($con, "utf8");
    if (isset($_GET['post_type'])) {
        $post_type = $_GET['post_type'];
        $select_posts_query = "SELECT posts.*, users.username, users.avatar, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id WHERE content_types.id = ? ORDER BY view_count DESC;";
        $select_posts_query_stmt = mysqli_prepare($con, $select_posts_query);
        mysqli_stmt_bind_param($select_posts_query_stmt, 'i', $post_type);
        mysqli_stmt_execute($select_posts_query_stmt);
        $posts_mysqli= mysqli_stmt_get_result($select_posts_query_stmt);
        $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    } else {
        $select_posts_query = 'SELECT posts.*, users.username, users.avatar, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id ORDER BY view_count DESC;';
        $posts_mysqli = mysqli_query($con, $select_posts_query);
        $posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    }
    $select_content_types_query = 'SELECT * FROM content_types;';
    $content_types_mysqli = mysqli_query($con, $select_content_types_query);
    $content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
    $page_content = include_template('main.php', ['posts' => $posts, 'content_types' => $content_types, 'post_type' => $post_type]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user_name, 'title' => $title, 'is_auth' => $is_auth]);
    print($layout_content);
    mysqli_close($con);
}
