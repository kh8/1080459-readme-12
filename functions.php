<?php

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */

function truncate_text(string $text, int $truncate_length = 300): string
{
    if (mb_strlen($text) <= $truncate_length) {
        return $text;
    } else {
        $words = explode(" ", $text);
        $i = 0;
        while ($current_length - 1 < $truncate_length) {
            $current_length += mb_strlen($words[$i]);
            $current_length++;
            $i++;
        }
        $final_text = implode(" ", array_slice($words, 0, $i - 1));
        $final_text .= '...';
        return $final_text;
    }
}

function get_post_time($post_id): DateTime
{
    $random_date = generate_random_date($post_id);
    return date_create($random_date);
}

function absolute_time_to_relative($absolute_time): string
{
    date_default_timezone_set('Asia/Yekaterinburg');
    $current_date = date_create();
    $interval = date_diff($absolute_time, $current_date);
    $interval_in_minutes = $interval->days * 24 * 60;
    $interval_in_minutes += $interval->h * 60;
    $interval_in_minutes += $interval->i;
    if ($interval_in_minutes < 60) {
        $relative_time = $interval->i.' '.get_noun_plural_form($interval->i,'минута','минуты','минут').' назад';
    } elseif ($interval_in_minutes < 1440) {
        $relative_time = $interval->h.' '.get_noun_plural_form($interval->h,'час','часа','часов').' назад';
    } elseif ($interval_in_minutes < 10080) {
        $relative_time = $interval->d.' '.get_noun_plural_form($interval->d,'день','дня','дней').' назад';
    } elseif ($interval_in_minutes < 50400) {
        $weeks = floor($interval->d/7);
        $relative_time = $weeks.' '.get_noun_plural_form($weeks,'неделя','недели','недель').' назад';
    } else {
        $relative_time = $interval->m.' '.get_noun_plural_form($interval->m,'месяц','месяца','месяцев').' назад';
    }
    return $relative_time;
}

function secure_query(mysqli $con, string $sql, string $type, ...$params) {
    $prepared_sql = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($prepared_sql, $type, ...$params);
    mysqli_stmt_execute($prepared_sql);
    return mysqli_stmt_get_result($prepared_sql);
}

function display_404_page() {
    $page_content = include_template('404.php');
    $layout_content = include_template('layout.php',['content' => $page_content]);
    print($layout_content);
    http_response_code(404);
}

function validateFilled($var) {
    if (empty($_POST[$var])) {
        return 'Это поле должно быть заполнено';
    }
}

function validateURL($var) {
    if (!filter_var($_POST[$var], FILTER_VALIDATE_URL)) {
        return 'Некорретный URL-адрес';
    }
}

function validateRepeatPassword($repeatPassword) {
    if ($_POST['password-repeat'] !== $_POST['password']) {
        return 'Пароли не совпадают';
    }
}

function validateCorrectEmail($var) {
    if (!filter_var($_POST[$var], FILTER_VALIDATE_EMAIL)) {
        return 'Некорретный email';
    }
}

function validateEmailExist($email) {
    $con = db_connect("localhost", "root", "", "readme");
    $select_user_by_email_query = "SELECT * FROM users WHERE email = ?";
    $result = secure_query($con, $select_user_by_email_query, 's', $_POST['email']);
    if (mysqli_num_rows($result) > 0) {
        return 'Пользователь с таким email уже существует';
    }
}

function validateImageURLContent($var) {
    if (!$content = @file_get_contents($_POST[$var])) {
        return 'По ссылке отсутствует изображение';
    }
}

function validateImageFile($file) {
    if ($file['error'] != 0) {
        return 'Код ошибки:'.$file['error'];
    } else {
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $file['tmp_name'];
        $file_type = finfo_file($file_info, $file_name);
        if (!in_array($file_type, ['image/png','image/jpeg', 'image/gif'])) {
            return 'Недопустимый тип изображения';
        }
    }
}

function validate($field, $validation_rules) {
    foreach ($validation_rules as $validation_rule) {
        if (!function_exists($validation_rule)) {
            return 'Функции валидацxии ' . $validation_rule. ' не существует';
        }
        if ($result = $validation_rule($field))  {
            return $result;
        }
    }
}

function save_image($img) {
    if ($_FILES[$img]['error'] != 0) {
        return $file_name = $_POST[$img];
    } else {
        $file_name = $_FILES[$img]['name'];
        $file_path = __DIR__ . '/uploads/' . '<br>';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES[$img]['tmp_name'], $file_path . $file_name);
        return $file_name;
    }
}
