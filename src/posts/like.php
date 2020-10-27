<?php

function like_post($connection, $user_id, $post_id)
{
    $like_query = "INSERT INTO likes SET user_id = ?, post_id = ?";
    $like_result = secure_query($connection, $like_query, $user_id, $post_id);
    return $like_result;
}
