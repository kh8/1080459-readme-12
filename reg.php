<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */
$add_user_query = "INSERT into users SET username = ?, email = ?, password = ?, avatar = ?, dt_add =?";

$validation_rules = [
    'email' => 'filled|correctemail|exists:users,email',
    'login' => 'filled',
    'password' => 'filled|repeatpassword',
    'password-repeat' => 'filled|repeatpassword'
];
$form_error_codes = [
    'email' => 'Email',
    'login' => 'Логин',
    'password' => 'Пароль',
    'password-repeat' => 'Повторный пароль'
];

if (count($_POST) > 0) {
    foreach ($_POST as $field_name => $field_value) {
        $form['values'][$field_name] = $field_value;
    }
    $form['errors'] = validate($connection, $form['values'], $validation_rules);
    $form['errors'] = array_filter($form['errors']);
    if (empty($form['errors'])) {
        $current_time = date('Y-m-d H:i:s');
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $avatar = save_image('userpic-file');
        secure_query($connection, $add_user_query, $_POST['login'], $_POST['email'], $password_hash, $avatar, $current_time);
        $post_id = mysqli_insert_id($connection);
        $URL = '/';
        header("Location: $URL");
    }
}

$page_content = include_template(
    'reg-template.php',
    [
        'form_values' => $form['values'],
        'form_errors' => $form['errors'],
        'form_error_codes' => $form_error_codes
    ]
);
print($page_content);
