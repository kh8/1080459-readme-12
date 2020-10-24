<?php

/**
 * Этот файл должен отвечать ИСКЛЮЧИТЕЛЬНО за функции работы с базой данных.
 * Причем ТОЛЬКО за функции. Здесь они только объявляются, не более, использоваться они будут не здесь.
 */

$select_subscribe_query = "SELECT * FROM subscribe WHERE follower_id = ? AND author_id = ?";
$add_comment_query = "INSERT into comments SET user_id = ?, post_id = ?, dt_add = ?, content = ?";


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
