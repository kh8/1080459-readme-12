<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');
session_start();

$validation_rules = [
    'post-id' => 'notexists:posts,id',
    'comment' => 'filled|long:4'
];

$field_error_codes = [
    'heading' => 'Заголовок',
    'content' => 'Контент',
    'tags' => 'Теги',
    'link-url' => 'Ссылка',
    'photo-url' => 'Ссылка из интернета',
    'video-url' => 'Ссылка YOUTUBE',
    'photo-file' => 'Файл фото',
    'quote-author' => 'Автор'
];
if ($_SESSION['is_auth'] != 1) {
    header("Location: index.php");
    exit();
}
if (!(isset($_POST['post-id']) && isset($_POST['comment']))) {
    header("Location: index.php");
    exit();
}
$user['id'] = $_SESSION['id'];
$user['name'] = $_SESSION['username'];
$user['avatar'] = $_SESSION['avatar'];
$post_id = $_POST['post-id'];
$con = db_connect("localhost", "root", "", "readme");
$content_types_mysqli = mysqli_query($con, $select_content_types_query);
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$post_types = array_column($content_types, 'id', 'type_class');
foreach ($_POST as $field_name => $field_value) {
    $form['values'][$field_name] = $field_value;
}
$form['errors'] = validate($form['values'], $validation_rules, $con);
$form['errors'] = array_filter($form['errors']);
if (!empty($form['errors'])) {
    $_SESSION['errors'] = $form['errors'];
} else {
    $current_time = date('Y-m-d H:i:s');
    $post_comment = trim($_POST['comment']);
    secure_query($con, $add_comment_query, 'isss', $user['id'], $post_id, $current_time, $post_comment, );
}
$URL = '/post.php?id='.$post_id;
header("Location: $URL");
