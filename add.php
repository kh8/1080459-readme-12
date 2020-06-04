<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$select_content_types_query = 'SELECT * FROM content_types;';
$select_tags_query = 'SELECT * FROM hashtags;';
$add_quote_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = 1, view_count = 0, quote_author = ?";
$add_text_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = 1, view_count = 0";
$add_photo_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = 1, view_count = 0, img_url = ?";
$add_link_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = 1, view_count = 0";
$add_video_post_query = "INSERT into posts SET title = ?, post_type = ?, content = ?, author_id = 1, view_count = 0, youtube_url = ?";
$add_tag_query = "INSERT into hashtags SET tag_name = ?";
$add_post_tag_query = "INSERT into post_tags SET post_id = ?, hashtag_id = ?";
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
$validation_rules = [
    'heading' => function() {
        return validateFilled('heading');
    },
    'content' => function() {
        return validateFilled('content');
    },
    'tags' => function() {
        return validateFilled('tags');
    },
    'link-url' => function() {
        return validateURL('link-url');
    },
    'photo-url' => function() {
        return validateImageURL('photo-url');
    },
    'video-url' => function() {
        return validateVideoURL('video-url');
    },
    'quote-author' => function() {
        return validateFilled('quote-author');
    }
];
$form_type = 'text';
$con = db_connect("localhost", "root", "", "readme");
$content_types_mysqli = mysqli_query($con, $select_content_types_query);
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$post_types = array_column($content_types, 'id', 'type_class');
$tags_mysqli = mysqli_query($con, $select_tags_query);
$tags = mysqli_fetch_all($tags_mysqli, MYSQLI_ASSOC);
if (count($_POST) > 0) {
    if (isset($_POST['form-type'])) {
        $form_type = $_POST['form-type'];
        foreach ($_POST as $field_name => $val) {
            $fields['values'][$form_type][$field_name] = $_POST[$field_name];
            if (isset($validation_rules[$field_name])) {
                $validation_rule = $validation_rules[$field_name];
                $fields['errors'][$form_type][$field_name] = $validation_rule();
            }
        }
        if  ($form_type == 'photo') {
            $fields['errors'][$form_type]['photo-file'] = validateImageFile($_FILES['photo-file']);
        }
        if (!empty($fields['errors'][$form_type]['photo-file']) && empty($fields['errors'][$form_type]['photo-url'])) {
            unset($fields['errors'][$form_type]['photo-file']);
        }
        $fields['errors'][$form_type] = array_filter($fields['errors'][$form_type]);
        if (empty($fields['errors'][$form_type])) {
            switch ($form_type) {
                case 'quote':
                    secure_query($con, $add_quote_post_query, 'siss', $_POST['heading'], $post_types[$form_type], $_POST['content'], $_POST['quote-author']);
                    $post_id = mysqli_insert_id($con);
                break;
                case 'text':
                    secure_query($con, $add_text_post_query, 'sis', $_POST['heading'], $post_types[$form_type], $_POST['content']);
                    $post_id = mysqli_insert_id($con);
                    break;
                case 'link':
                    secure_query($con, $add_link_post_query, 'sis', $_POST['heading'], $post_types[$form_type], $_POST['link-url']);
                    $post_id = mysqli_insert_id($con);
                    break;
                case 'video':
                    secure_query($con, $add_video_post_query, 'siss', $_POST['heading'], $post_types[$form_type], $_POST['content'], $_POST['youtube_url']);
                    $post_id = mysqli_insert_id($con);
                    break;
                case 'photo':
                    if ($_FILES['photo-file']['error'] != 0) {
                        $file_url = $_POST['photo-url'];
                    } else {
                        $file_name = $_FILES['photo-file']['name'];
                        $file_path = __DIR__ . '/uploads/' . '<br>';
                        $file_url = '/uploads/' . $file_name;
                        move_uploaded_file($_FILES['photo-file']['tmp_name'], $file_path . $file_name);
                    }
                    secure_query($con, $add_photo_post_query, 'siss', $_POST['heading'], $post_types[$form_type], $_POST['content'], $file_url);
                    $post_id = mysqli_insert_id($con);
                }
            $new_tags = explode(' ', $_POST['tags']);
            foreach ($new_tags as $new_tag) {
                $tag_id = -1;
                foreach ($tags as $tag) {
                    if ($new_tag == $tag['tag_name']) {
                        $tag_id = $tag['id'];
                        break;
                    }
                }
                if ($tag_id == -1) {
                    secure_query($con, $add_tag_query, 's', $new_tag);
                    $tag_id = mysqli_insert_id($con);
                }
                secure_query($con, $add_post_tag_query, 'ii', $post_id, $tag_id);
            }
            $URL = '/post.php?id='.$post_id;
            header("Location: $URL");
        }
    }
}
$page_content = include_template('adding-post.php', ['content_types' => $content_types, 'fields_values' => $fields['values'], 'fields_errors' => $fields['errors'], 'field_error_codes' => $field_error_codes, 'form_type' => $form_type]);
print($page_content);
?>
