<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */

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
$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}
if (!(isset($_POST['post-id']) && isset($_POST['comment']))) {
    header("Location: index.php");
    exit();
}
$post_id = $_POST['post-id'];
$content_types_mysqli = mysqli_query($connection, 'SELECT * FROM content_types');
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$post_types = array_column($content_types, 'id', 'type_class');
foreach ($_POST as $field_name => $field_value) {
    $form['values'][$field_name] = $field_value;
}
$form['errors'] = validate($connection, $form['values'], $validation_rules);
$form['errors'] = array_filter($form['errors']);
if (!empty($form['errors'])) {
    $_SESSION['errors'] = $form['errors'];
} else {
    $current_time = date('Y-m-d H:i:s');
    $post_comment = trim($_POST['comment']);
    secure_query($connection, $add_comment_query, $user['id'], $post_id, $current_time, $post_comment, );
}
$URL = '/post.php?id='.$post_id;
header("Location: $URL");
