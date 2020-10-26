<?php
require_once(__DIR__ . '/lib/base.php');
/** @var $connection */

$select_user_query = "SELECT users.id, users.username, users.avatar FROM users WHERE users.username = ?";
$form_error_codes = [
    'login' => 'Логин',
    'password' => 'Пароль',
];
$validation_rules = [
    'login' => 'filled',
    'password' => 'filled|correctpassword:users,username,password'
];
if (count($_POST) > 0) {
    $form['values'] = $_POST;
    $form['errors'] = validate($connection, $form['values'], $validation_rules);
    $form['errors'] = array_filter($form['errors']);
    if (empty($form['errors'])) {
        $user_mysqli = secure_query($connection, $select_user_query, $form['values']['login']);
        $user = mysqli_fetch_assoc($user_mysqli);
        $_SESSION['is_auth'] = 1;
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['id'] = $user['id'];
        header("Location: feed.php");
        exit();
    }
}
$page_content = include_template(
    'anonym.php',
    [
        'form_values' => $form['values'],
        'form_errors' => $form['errors'],
        'form_error_codes' => $form_error_codes
    ]
);
print($page_content);

