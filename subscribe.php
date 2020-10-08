<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
$select_user_query = "SELECT users.id FROM users WHERE users.id = ?";
$subscribe_query = "INSERT INTO subscribe SET follower_id = ?, author_id = ?";

session_start();

if (!isset($_GET['id'])) {
    display_404_page();
    exit();
}
$author_id = $_GET['id'];
if ($_SESSION['is_auth'] == 1) {
    $con = db_connect("localhost", "root", "", "readme");
    $user_id = $_SESSION['id'];
    $author_mysqli = secure_query($con, $select_user_query, 's', $author_id);
    $author = mysqli_fetch_assoc($author_mysqli);
    secure_query($con, $subscribe_query, 'ii', $user_id, $author['id']);
    header("Location: profile.php?id=".$author_id);
    exit();
}
header("Location: index.php");
?>
