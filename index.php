<?php

$user_name = 'Михаил'; // укажите здесь ваше имя
$title = 'ReadMe';
$is_auth = rand(0,1);
require_once('helpers.php');
require_once('functions.php');
$con = mysqli_connect("localhost", "root", "", "readme");
if ($con == false) {
    $error = mysqli_connect_error();
} else {
    mysqli_set_charset($con, "utf8");
    $sql = "SELECT users.username, users.avatar, posts.title, posts.content, posts.view_count, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id ORDER  BY view_count DESC;";
    $result = mysqli_query($con, $sql);
    if (!$result) {
        $error = mysqli_error($con);
    } else {
        $cards = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    $sql = "SELECT * FROM content_types;";
    $result = mysqli_query($con, $sql);
    if (!$result) {
        $error = mysqli_error($con);
    } else {
        $content_types = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
$page_content = include_template('main.php', ['cards' => $cards, 'content_types' => $content_types]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user_name, 'title' => $title, 'is_auth' => $is_auth]);
print($layout_content);
