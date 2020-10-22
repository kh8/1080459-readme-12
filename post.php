<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
session_start();
$select_post_by_id = "SELECT posts.*, users.id AS user_id, users.username, users.avatar, content_types.type_class,
COALESCE(like_count, 0) AS likes,
COALESCE(comment_count, 0) AS comments
FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
LEFT JOIN (SELECT post_id, COUNT(*) AS like_count
FROM likes
GROUP BY post_id) like_counts ON like_counts.post_id = posts.id
LEFT JOIN (SELECT post_id, COUNT(*) AS comment_count
FROM comments
GROUP BY post_id) comment_counts ON comment_counts.post_id = posts.id
WHERE posts.id = ?;";
$select_post_comments = "SELECT comments.*, users.id AS author_id, users.username AS author_name, users.avatar FROM comments INNER JOIN users ON comments.user_id=users.id WHERE post_id = ? ORDER BY dt_add DESC;";
$count_posts_by_author = "SELECT COUNT(*) FROM posts WHERE author_id = ?;";
$count_author_followers = "SELECT COUNT(*) FROM subscribe WHERE author_id = ?;";
$update_post_view_count_query = "UPDATE posts SET view_count = view_count + 1 WHERE id = ?";
if ($_SESSION['is_auth'] != 1) {
    header("Location: index.php");
    exit();
}
if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}
$user['name'] = $_SESSION['username'];
$user['id'] = $_SESSION['id'];
$user['avatar'] = $_SESSION['avatar'];
$con = db_connect("localhost", "root", "", "readme");
if (!empty($_SESSION['errors'])) {
    $post_errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}
$post_id = $_GET['id'];
$posts_mysqli = secure_query($con, $select_post_by_id, 'i', $post_id);
if (!mysqli_num_rows($posts_mysqli)) {
    display_404_page();
    exit();
}
$posts_array = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
$post_author_id = $posts_array[0]['author_id'];
$author_posts_count_mysqli = secure_query($con, $count_posts_by_author, 'i', $post_author_id);
$author_posts_count = mysqli_fetch_row($author_posts_count_mysqli)[0];
$author_followers_count_mysqli = secure_query($con, $count_author_followers, 'i', $post_author_id);
$author_followers_count = mysqli_fetch_row($author_followers_count_mysqli)[0];
$comments_mysqli = secure_query($con, $select_post_comments, 'i', $post_id);
$comments = mysqli_fetch_all($comments_mysqli, MYSQLI_ASSOC);
$views_mysqli = secure_query($con, $update_post_view_count_query, 'i', $post_id);
$page_content = include_template('post-details.php', ['post' => $posts_array[0], 'post_errors' => $post_errors, 'comments' => $comments, 'author_posts_count' => $author_posts_count, 'author_followers_count' => $author_followers_count, 'user' => $user]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user, 'title' => $title]);
print($layout_content);
mysqli_close($con);


