<?php

function get_content_types($connection)
{
    $content_types_mysqli = mysqli_query(
        $connection,
        'SELECT * FROM content_types'
    );
    $content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);

    return $content_types;
}

function save_post(string $form_type, array $post_types, array $post, $connection, array $user, $file_url = null)
{
    $post_id = null;
    $current_time = date('Y-m-d H:i:s');

    $fields = [
        'title',
        'post_type',
        'content',
        'view_count',
        'dt_add',
    ];

    $parameters = [
        $post['heading'],
        $post_types[$form_type],
        $post['content'],
        $user['id'],
        0,
        $current_time
    ];

    if ($form_type === 'quote') {
        array_push($fields, 'quote_author');
        array_push($parameters, $post['quote-author']);
    }

    if ($form_type === 'video') {
        array_push($fields, 'youtube_url');
        array_push($parameters, $post['youtube_url']);
    }

    if ($form_type === 'photo') {
        array_push($fields, 'img_url');
        array_push($parameters, $file_url);
    }

    $finalFields = [];
    foreach ($fields as $field) {
        $finalFields[] = "{$field} = ?";
    }
    $fields = implode(', ', $finalFields);

    $query = "insert into posts set {$fields}";
    secure_query($connection, $query, ...$parameters);
    $post_id = mysqli_insert_id($connection);

    return $post_id;
}

function add_tags(string $tags, $post_id, $connection)
{
    $new_tags = array_unique(explode(' ', $tags));
    // Небезопасный запрос.
    $select_tags_query = "SELECT * FROM hashtags WHERE tag_name in ('" . implode("','", $new_tags) . "')";
    $tags_mysqli = mysqli_query($connection, $select_tags_query);
    $tags = mysqli_fetch_all($tags_mysqli, MYSQLI_ASSOC);
    foreach ($new_tags as $new_tag) {
        $index = array_search($new_tag, array_column($tags, 'tag_name'));
        if ($index !== false) {
            unset($new_tags[$new_tag]);
            $tag_id = $tags[$index]['id'];
        } else {
            secure_query($connection, "INSERT into hashtags SET tag_name = ?", 's', $new_tag);
            $tag_id = mysqli_insert_id($connection);
        }
        secure_query($connection, "INSERT into post_tags SET post_id = ?, hashtag_id = ?", 'ii', $post_id, $tag_id);
    }
}

function upload_file($form)
{
    if (isset($form['values']['photo-file'])) {
        $file_name = $form['values']['photo-file']['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES['photo-file']['tmp_name'], $file_path . $file_name);
    } else {
        $file_url = $_POST['photo-url'];
    }

    return $file_url;
}
