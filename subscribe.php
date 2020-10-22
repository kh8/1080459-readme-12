<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
session_start();

$select_user_query = "SELECT users.id FROM users WHERE users.id = ?";
$add_subscribe_query = "INSERT INTO subscribe SET follower_id = ?, author_id = ?";
$remove_subscribe_query = "DELETE FROM subscribe WHERE follower_id = ? AND author_id = ?";

if ($_SESSION['is_auth'] != 1) {
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
$con = db_connect("localhost", "root", "", "readme");
$author_mysqli = secure_query($con, $select_user_query, 's', $author_id);
$author_exists = $author_mysqli->num_rows > 0;
if (!$author_exists) {
    display_404_page();
    exit();
}
$user_subscribe_mysqli = secure_query($con, $select_subscribe_query, 'ii', $user_id, $author_id);
if ($user_subscribe_mysqli->num_rows == 0) {
    secure_query($con, $add_subscribe_query, 'ii', $user_id, $author_id);
} else {
    secure_query($con, $remove_subscribe_query, 'ii', $user_id, $author_id);
}
header("Location: " . $_SERVER['HTTP_REFERER']);
