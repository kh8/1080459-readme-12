<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */

$select_user_query = "SELECT users.id FROM users WHERE users.id = ?";
$add_subscribe_query = "INSERT INTO subscribe SET follower_id = ?, author_id = ?";
$remove_subscribe_query = "DELETE FROM subscribe WHERE follower_id = ? AND author_id = ?";

$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}
if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}
$author_id = $_GET['id'];
$user_id = $_SESSION['id'];
if ($author_id == $user_id) {
    header("Location: profile.php?id=".$author_id);
    exit();
}
$author_mysqli = secure_query($connection, $select_user_query, $author_id);
$author_exists = $author_mysqli->num_rows > 0;
if (!$author_exists) {
    display_404_page();
    exit();
}
$user_subscribe_mysqli = secure_query($connection, $select_subscribe_query, $user_id, $author_id);
if ($user_subscribe_mysqli->num_rows == 0) {
    secure_query($connection, $add_subscribe_query, $user_id, $author_id);
} else {
    secure_query($connection, $remove_subscribe_query, $user_id, $author_id);
}
header("Location: " . $_SERVER['HTTP_REFERER']);
