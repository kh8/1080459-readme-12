<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */
require_once(__DIR__ . '/src/feed.php');

$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}
$title = $settings['site_name'] . ' | Моя лента';
$filter = white_list($_GET['filter'], ["text","quote","photo","link","video"]);
$content_types = get_content_types($connection);
$posts = get_feed_posts($connection, $filter, $user['id']);

$page_content = include_template(
    'feed-template.php',
    [
        'posts' => $posts,
        'filter' => $filter,
        'content_types' => $content_types
    ]
);
$layout_content = include_template(
    'layout.php',
    [
        'title' => $title,
        'active_section' => 'feed',
        'user' => $user,
        'content' => $page_content
    ]
);
print($layout_content);

