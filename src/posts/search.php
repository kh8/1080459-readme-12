<?php

function search_posts($connection, $keywords)
{
    $search_query = "SELECT posts.*, users.username, users.avatar, content_types.type_class,
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
    WHERE MATCH(title,content) AGAINST(?)";
    $search_results_mysqli = secure_query($connection, $search_query, $keywords);
    $search_results = mysqli_fetch_all($search_results_mysqli, MYSQLI_ASSOC);
    return $search_results;
}
