<?php

$select_content_types_query = 'SELECT * FROM content_types';
$add_quote_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = ?, view_count = 0, quote_author = ?, dt_add = ?";
$add_text_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = ?, view_count = 0, dt_add = ?";
$add_link_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = ?, view_count = 0, dt_add = ?";
$add_photo_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = ?, view_count = 0, img_url = ?, dt_add = ?";
$add_video_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = ?, view_count = 0, youtube_url = ?, dt_add = ?";
$add_comment_query = "INSERT into comments SET user_id = ?, post_id = ?, dt_add = ?, content = ?";
$add_tag_query = "INSERT into hashtags SET tag_name = ?";
$add_post_tag_query = "INSERT into post_tags SET post_id = ?, hashtag_id = ?";

function db_connect($host, $user, $pass, $db) {
    $con = mysqli_connect($host, $user, $pass, $db);
    if ($con == false) {
        $error = mysqli_connect_error();
        print($error);
        http_response_code(500);
        exit();
    }
    mysqli_set_charset($con, "utf8");
    return $con;
}
?>
