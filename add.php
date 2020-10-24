<?php

require_once(__DIR__ . '/lib/base.php');
/** @var $connection */

$validation_rules = [
    'text' => [
        'heading' => 'filled',
        'content' => 'filled',
        'tags' => 'filled'
    ],
    'photo' => [
        'heading' => 'filled',
        'photo-url' => 'filled|correctURL|ImageURLContent|required_if_not:photo-file',
        'tags' => 'filled',
        'photo-file' => 'imgloaded|required_if_not:photo-url'
    ],
    'link' => [
        'heading' => 'filled',
        'link-url' => 'filled|correctURL',
        'tags' => 'filled'
    ],
    'quote' => [
        'heading' => 'filled',
        'content' => 'filled',
        'quote-author' => 'filled',
        'tags' => 'filled'
    ],
    'video' => [
        'heading' => 'filled',
        'video-url' => 'filled|correctURL|youtubeurl',
        'tags' => 'filled'
    ],
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

$content_types = get_content_types($connection);
$post_types = array_column($content_types, 'id', 'type_class');

$form = [
    'values' => [],
    'errors' => [],
];


$form_type = $_GET['tab'] ?? 'text';

if (count($_POST) === 1 && isset($_POST['form_type'])) {
    $form['values'] = array_merge($form['values'], $_POST['form_type']);

    $form['values']['photo-file'] = $_FILES['photo-file'];
    $form['errors'] = validate($form['values'], $validation_rules[$_POST['form_type']], $connection);

    if (count ($form['errors']) < 1) {
        $file_url = null;
        if ($form_type === 'photo') {
            $file_url = upload_file($form);
        }

        $post_id = save_post($_POST['form_type'], $post_types, $_POST, $connection, $user, $file_url);
        add_tags($_POST['tags'], $post_id, $connection);

        $URL = '/post.php?id=' . $post_id;
        header("Location: $URL");
    }
}



$page_content = include_template(
    'add-template.php',
    [
        'content_types' => $content_types,
        'form_values' => $form['values'],
        'form_errors' => $form['errors'],
        'field_error_codes' => $field_error_codes,
        'form_type' => $form_type,
        'user' => $user
    ]
);
print($page_content);
