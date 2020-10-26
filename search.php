<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */
$search_query = "SELECT posts.id, posts.author_id, posts.post_type, posts.dt_add, posts.title, posts.content, posts.quote_author, posts.img_url, posts.youtube_url, posts.url, users.username, users.avatar, content_types.type_class FROM posts INNER JOIN users ON posts.author_id=users.id INNER JOIN content_types ON posts.post_type=content_types.id WHERE MATCH(title,content) AGAINST(?)";
$count_post_likes_query = "SELECT COUNT(*) FROM likes WHERE post_id = ?;";
$count_post_comments_query = "SELECT COUNT(*) FROM comments WHERE post_id = ?;";
$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}
if (count($_GET) > 0) {
    $keywords = trim($_GET['keywords']);
    if ($keywords == '') {
        $page_content = include_template('no-results.php');
        print($page_content);
        exit();
    }
    $posts_mysqli = secure_query($connection, $search_query, $keywords);
    $search_results = mysqli_fetch_all($posts_mysqli, MYSQLI_ASSOC);
    foreach ($search_results as $index => $search_result) {
        $likes_mysqli = secure_query($connection, $count_post_likes_query, $search_result['id']);
        $search_results[$index]['likes'] = mysqli_fetch_row($likes_mysqli)[0];
        $comments_mysqli = secure_query($connection, $count_post_comments_query, $search_result['id']);
        $search_results[$index]['comments'] = mysqli_fetch_row($comments_mysqli)[0];
    }
}
if (count($search_results) == 0) {
    $page_content = include_template('no-results.php', ['keywords' => $keywords]);
    print($page_content);
    exit();
}
$page_content = include_template(
    'search-template.php',
    [
        'keywords' => $keywords,
        'posts' => $search_results
    ]
);
$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title
    ]
);
print($layout_content);


