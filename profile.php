<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
session_start();

$select_user_query = "SELECT users.username, users.avatar, users.id, users.dt_add FROM users WHERE users.id = ?";
$select_user_posts_query = "SELECT posts.*, users.id AS user_id, users.username, users.avatar, content_types.type_class,
COUNT(likes.post_id) AS likes, COUNT(comments.post_id) AS comments
FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
LEFT OUTER JOIN comments ON posts.id = comments.post_id
LEFT OUTER JOIN likes ON posts.id = likes.post_id
WHERE posts.author_id = ?
GROUP BY posts.id
ORDER BY dt_add DESC;";
$count_user_posts_query = "SELECT COUNT(*) FROM posts WHERE author_id = ?;";
$count_user_subscribers_query = "SELECT COUNT(*) FROM subscribe WHERE author_id = ?;";
$select_author_likes = "SELECT likes.user_id, likes.post_id, posts.title, posts.content, users.id AS user_id, users.username, users.avatar,content_types.type_class
FROM likes
INNER JOIN posts ON posts.id = likes.post_id AND posts.author_id = ?
INNER JOIN content_types ON posts.post_type=content_types.id
INNER JOIN users ON users.id = likes.user_id";
$select_author_subscribes = "SELECT users.id AS user_id, users.avatar, users.username, users.dt_add,
COALESCE(post_count, 0) AS post_count,
COALESCE(user_subscribe, 0) AS user_subscribe
FROM subscribe
INNER JOIN users ON subscribe.author_id = users.id
LEFT JOIN (SELECT author_id, COUNT(*) AS post_count
    FROM posts
    GROUP BY author_id) post_counts ON post_counts.author_id = users.id
LEFT JOIN (SELECT author_id, follower_id AS user_subscribe FROM subscribe WHERE follower_id = ?) user_subscribed ON user_subscribed.author_id = users.id
WHERE subscribe.follower_id = ?";

if ($_SESSION['is_auth'] != 1) {
    header("Location: index.php");
    exit();
}
$author_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'posts';
$user['name'] = $_SESSION['username'];
$user['id'] = $_SESSION['id'];
$user['avatar'] = $_SESSION['avatar'];
$con = db_connect("localhost", "root", "", "readme");
$author_mysqli = secure_query($con, $select_user_query, 'i', $author_id);
$author = mysqli_fetch_assoc($author_mysqli);
$user_subscribe_mysqli = secure_query($con, $select_subscribe_query, 'ii', $user['id'], $author_id);
$user['subscribe'] = $user_subscribe_mysqli->num_rows;
$posts_mysqli = secure_query($con, $select_user_posts_query, 'i', $author_id);
$posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
$posts_count_mysqli = secure_query($con, $count_user_posts_query, 'i', $author_id);
$posts_count = mysqli_fetch_row($posts_count_mysqli)[0];
$subscribers_count_mysqli = secure_query($con, $count_user_subscribers_query, 'i', $author_id);
$subscribers_count = mysqli_fetch_row($subscribers_count_mysqli)[0];
$likes_mysqli = secure_query($con, $select_author_likes, 'i', $author_id);
$likes = mysqli_fetch_all($likes_mysqli, MYSQLI_ASSOC);
$author_subscribes_mysqli = secure_query($con, $select_author_subscribes, 'ii', $user['id'], $author_id);
$author_subscribes = mysqli_fetch_all($author_subscribes_mysqli, MYSQLI_ASSOC);
$page_content = include_template('profile-template.php', ['user' => $user, 'author' => $author, 'tab' => $tab, 'posts' => $posts, 'likes' => $likes, 'posts_count' => $posts_count, 'subscribers_count' => $subscribers_count, 'subscribes' => $author_subscribes]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user, 'title' => $title]);
print($layout_content);
mysqli_close($con);
