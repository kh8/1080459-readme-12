<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */

$title = 'ReadMe';
$page_limit = 6;
$count_posts_query =
    'SELECT COUNT(*)
    FROM posts
    INNER JOIN content_types ON posts.post_type=content_types.id ';
$select_posts_query =
    'SELECT posts.*, users.id AS user_id, users.username, users.avatar, content_types.type_class,
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
    GROUP BY post_id) comment_counts ON comment_counts.post_id = posts.id ';

$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}

$page_number = $_GET['page'] ?? '1';
$page_limit = $_GET['limit'] ?? $page_limit;
$page_offset = ($page_number - 1) * $page_limit;
if (isset($_GET['filter'])) {
    $filter = white_list($_GET['filter'], ["text","quote","photo","link","video"], "Invalid field name");
    $select_posts_query.= "WHERE content_types.type_class = '$filter' ";
    $count_posts_query.= "WHERE content_types.type_class = '$filter' ";
}
$sort = $_GET['sort'] ?? 'view_count';
$sort = white_list($sort, ["likes","view_count","dt_add"], "Invalid field name");
$select_posts_query.= "ORDER BY $sort DESC LIMIT ? OFFSET ?;";
$posts_mysqli = secure_query($connection, $select_posts_query, $page_limit, $page_offset);
$posts = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
$posts_count_mysqli = mysqli_query($connection, $count_posts_query);
$posts_count = mysqli_fetch_row($posts_count_mysqli)[0];
$content_types_mysqli = mysqli_query($connection, 'SELECT * FROM content_types');
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$page_content = include_template(
    'popular-template.php',
    [
        'posts' => $posts,
        'posts_count' => $posts_count,
        'page_number' => $page_number,
        'page_limit' => $page_limit,
        'filter' => $filter,
        'sort' => $sort,
        'content_types' => $content_types,
        'filter' => $filter
    ]
);
$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title,
        'header_link' =>
        'popular'
    ]
);
print($layout_content);
