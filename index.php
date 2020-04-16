<?php
$select_posts_users_query = "SELECT users.username, users.avatar, posts.title, posts.content, posts.view_count, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id ORDER  BY view_count DESC;";
$select_content_types_query = "SELECT * FROM content_types;";
$user_name = 'Михаил'; // укажите здесь ваше имя
$title = 'ReadMe';
$is_auth = rand(0,1);
require_once('helpers.php');
require_once('functions.php');
$con = mysqli_connect("localhost", "root", "", "readme");
if ($con == false) {
    $error = mysqli_connect_error();
    print($error);
} else {
    mysqli_set_charset($con, "utf8");
    $cards = select_query($con, $select_posts_users_query);
    $content_types = select_query($con, $select_content_types_query);
}
$page_content = include_template('main.php', ['cards' => $cards, 'content_types' => $content_types]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user_name, 'title' => $title, 'is_auth' => $is_auth]);
print($layout_content);
