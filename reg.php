<?php
require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$add_user_query = "INSERT into users SET username = ?, email = ?, password = ?, avatar = ?";

$validation_rules = [
    'login' => ['validateFilled'],
    'email' => ['validateFilled','validateEmailExist'],
    'password' => ['validateFilled','validateRepeatPassword'],
    'password-repeat' => ['validateFilled','validateRepeatPassword']
];
$field_error_codes = [
    'email' => 'Заголовок',
    'login' => 'Логин',
    'password' => 'Пароль',
    'password-repeat' => 'Повторный пароль'
];

$con = db_connect("localhost", "root", "", "readme");
if (count($_POST) > 0) {
    foreach ($_POST as $field_name => $val) {
        $fields['values'][$field_name] = $_POST[$field_name];
        if (isset($validation_rules[$field_name])) {
            $fields['errors'][$field_name] = validate($field_name, $validation_rules[$field_name]);
        }
    }
    $fields['errors'] = array_filter($fields['errors']);
    if (empty($fields['errors'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $avatar = save_image('userpic-file');
        secure_query($con, $add_user_query, 'ssss', $_POST['login'], $_POST['email'], $password_hash, $avatar);
        $post_id = mysqli_insert_id($con);
        $URL = '/';
        header("Location: $URL");
    }
}

$page_content = include_template('registration.php', ['fields_values' => $fields['values'], 'fields_errors' => $fields['errors'], 'field_error_codes' => $field_error_codes]);
print($page_content);
?>
