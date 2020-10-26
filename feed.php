<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */
$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}
$user_id = $user['id'];
$select_posts_query = "SELECT posts.*, content_types.type_class, users.id AS user_id, users.username, users.avatar,
COALESCE(like_count, 0) AS likes,
COALESCE(comment_count, 0) AS comments
FROM posts
INNER JOIN users ON posts.author_id=users.id
INNER JOIN content_types ON posts.post_type=content_types.id
INNER JOIN subscribe ON posts.author_id = subscribe.author_id
LEFT JOIN (SELECT post_id, COUNT(*) AS like_count
FROM likes
GROUP BY post_id) like_counts ON like_counts.post_id = posts.id
LEFT JOIN (SELECT post_id, COUNT(*) AS comment_count
FROM comments
GROUP BY post_id) comment_counts ON comment_counts.post_id = posts.id
WHERE subscribe.follower_id = $user_id ";
if (isset($_GET['filter'])) {
    $filter = white_list($_GET['filter'], ["text","quote","photo","link","video"], "Invalid field name");
    $select_posts_query.= "AND content_types.type_class = '$filter' ";
}
$select_posts_query.= 'ORDER BY dt_add DESC';
$posts_mysqli = mysqli_query($connection, $select_posts_query);
$posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
$page_content = include_template(
    'feed-template.php',
    [
        'posts' => $posts,
        'content_types' => $content_types,
        'user' => $user
    ]
);
$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title,
        'header_link' => 'feed'
    ]
);
print($layout_content);

