<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['username'];
$page_content = include_template('user-feed.php', ['user' => $user]);
print($page_content);
