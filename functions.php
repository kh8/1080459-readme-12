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

function absolute_time_to_relative($time, $last_word): string
{
    if (!$time) {
        return '';
    }
    date_default_timezone_set('Asia/Yekaterinburg');
    $date = date_create_from_format('Y-m-d H:i:s', $time);
    $current_date = date_create();
    $interval = date_diff($date, $current_date);
    $interval_in_minutes = $interval->days * 24 * 60;
    $interval_in_minutes += $interval->h * 60;
    $interval_in_minutes += $interval->i;
    if ($interval_in_minutes < 60) {
        $relative_time = $interval->i.' '.get_noun_plural_form($interval->i,'минута','минуты','минут').' '.$last_word;
    } elseif ($interval_in_minutes < 1440) {
        $relative_time = $interval->h.' '.get_noun_plural_form($interval->h,'час','часа','часов').' '.$last_word;
    } elseif ($interval_in_minutes < 10080) {
        $relative_time = $interval->d.' '.get_noun_plural_form($interval->d,'день','дня','дней').' '.$last_word;
    } elseif ($interval_in_minutes < 50400) {
        $weeks = floor($interval->d/7);
        $relative_time = $weeks.' '.get_noun_plural_form($weeks,'неделя','недели','недель').' '.$last_word;
    } elseif ($interval_in_minutes < 525600) {
        $relative_time = $interval->m.' '.get_noun_plural_form($interval->m,'месяц','месяца','месяцев').' '.$last_word;
    } else {
        $relative_time = $interval->y.' '.get_noun_plural_form($interval->y,'год','года','лет').' '.$last_word;
    }
    return $relative_time;
}

function secure_query(mysqli $con, string $sql, ...$params) {
    foreach ($params as $param) {
        $param_types .= (gettype($param) == 'integer') ? 'i' : 's';
    }
    $prepared_sql = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($prepared_sql, $param_types, ...$params);
    mysqli_stmt_execute($prepared_sql);
    return mysqli_stmt_get_result($prepared_sql);
}

function white_list(&$value, $allowed, $message) {
    if ($value === null) {
        return $allowed[0];
    }
    $key = array_search($value, $allowed, true);
    if ($key === false) {
        return false;
    } else {
        return $value;
    }
}

function display_404_page() {
    $page_content = include_template('404.php');
    $layout_content = include_template('layout.php',['content' => $page_content]);
    print($layout_content);
    http_response_code(404);
}

function getValidationRules(array $rules): array {
    $result = [];
    foreach ($rules as $fieldName => $rule) {
        $result[$fieldName] = explode('|', $rule);
    }
    return $result;
}

function getValidationMethodName(string $name): string {
    $studlyWords = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    return "validate{$studlyWords}";
}

function getValidationNameAndParameters(string $rule): array {
    $nameParams = explode(':', $rule);
    $parameters = [];
    $name = $nameParams[0];
    if (isset($nameParams[1])) {
        $parameters = explode(',', $nameParams[1]);
    }
    return [$name, $parameters];
}

function validateFilled(array $inputArray, string $parameterName): ?string {
    if (empty($inputArray[$parameterName])) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

function validateLong(array $inputArray, string $parameterName, int $length): ?string {
    if (strlen(trim($inputArray[$parameterName])) < $length) {
        return 'Текст должен быть не короче '.$length.' символов';
    }
    return null;
}

function validateCorrectURL(array $inputArray, string $parameterName): ?string {
    if (!filter_var($inputArray[$parameterName], FILTER_VALIDATE_URL)) {
        return 'Некорретный URL-адрес';
    }
    return null;
}

function validateRepeatPassword(array $inputArray, string $parameterName): ?string {
    if ($inputArray['password'] !== $inputArray['password-repeat']) {
        return 'Пароли не совпадают';
    }
    return null;
}

function validateCorrectEmail(array $inputArray, string $parameterName): ?string {
    if (!filter_var($inputArray[$parameterName], FILTER_VALIDATE_EMAIL)) {
        return 'Некорретный email';
    }
    return null;
}

function validateExists(array $validationArray, string $parameterName, $tableName, $columnName, mysqli $dbConnection): ?string {
    $sql = "select count(*) as amount from $tableName where $columnName = ?";
    $prepared_sql = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($prepared_sql, 's', $validationArray[$parameterName]);
    mysqli_stmt_execute($prepared_sql);
    mysqli_stmt_bind_result($prepared_sql, $amount);
    mysqli_stmt_fetch($prepared_sql);
    mysqli_stmt_close($prepared_sql);
    return $amount > 0 ? "Запись с таким $parameterName уже присутствует в базе данных" : null;
}

function validateNotexists(array $validationArray, string $parameterName, $tableName, $columnName, mysqli $dbConnection): ?string {
    $sql = "select count(*) as amount from $tableName where $columnName = ?";
    $prepared_sql = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($prepared_sql, 's', $validationArray[$parameterName]);
    mysqli_stmt_execute($prepared_sql);
    mysqli_stmt_bind_result($prepared_sql, $amount);
    mysqli_stmt_fetch($prepared_sql);
    mysqli_stmt_close($prepared_sql);
    return $amount == 0 ? "Записи с таким $parameterName нет в базе данных" : null;
}

function validateCorrectPassword(array $validationArray, string $parameterName, $tableName, $usersColumnName, $passwordColumnName, mysqli $dbConnection): ?string {
    $sql = "select password as dbpassword from $tableName where $usersColumnName = ?";
    $prepared_sql = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($prepared_sql, 's', $validationArray['login']);
    mysqli_stmt_execute($prepared_sql);
    mysqli_stmt_bind_result($prepared_sql, $dbpassword);
    mysqli_stmt_fetch($prepared_sql);
    mysqli_stmt_close($prepared_sql);
    return !password_verify($validationArray[$parameterName], $dbpassword) ? "Вы ввели неверный email/пароль" : null;
}

function validateImgLoaded(array $inputArray, string $parameterName): ?string {
    if ($inputArray[$parameterName]['error'] != 0) {
        return 'Код ошибки:'.$inputArray[$parameterName]['error'];
    } else {
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $inputArray[$parameterName]['tmp_name'];
        $file_type = finfo_file($file_info, $file_name);
        if (!in_array($file_type, ['image/png','image/jpeg', 'image/gif'])) {
            return 'Недопустимый тип изображения';
        }
    }
}

function save_image($img) {
    if ($_FILES[$img]['error'] != 0) {
        return $file_name = $_POST[$img];
    } else {
        $file_name = $_FILES[$img]['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES[$img]['tmp_name'], $file_path . $file_name);
        return $file_name;
    }
}


function validateImageURLContent(array $inputArray, string $parameterName): ?string {
    if (!file_get_contents($inputArray[$parameterName])) {
        return 'По ссылке отсутствует изображение';
    }
    return null;
}

function validateRequiredIfNot(array $inputArray, string $parameterName, ... $fields): ?string
{
    $should_be_present = true;
    foreach ($fields as $field) {
        if (isset($inputArray[$field])) {
            $should_be_present = false;
        }
    }

    if ($should_be_present && !isset($_POST[$parameterName])) {
        return 'Параметр должен присутствовать, так как отсутствуют ' . implode(', ', $fields);
    }
    return null;
}

/**
 * Проверяет, что переданная ссылка ведет на публично доступное видео с youtube
 * @param string $youtube_url Ссылка на youtube видео
 * @return bool
 */
function validateYoutubeURL(array $inputArray, string $parameterName): ?string {
    $res = false;
    $id = extract_youtube_id($inputArray[$parameterName]);

    if ($id) {
        $api_data = ['id' => $id, 'part' => 'id,status', 'key' => 'AIzaSyD24lsJ4BL-azG188tHxXtbset3ehKXeJg'];
        $url = "https://www.googleapis.com/youtube/v3/videos?" . http_build_query($api_data);

        $resp = file_get_contents($url);

        if ($resp && $json = json_decode($resp, true)) {
            $res = null;
            // $res = $json['pageInfo']['totalResults'] > 0 && $json['items'][0]['status']['privacyStatus'] == 'public';
        } else {
            $res = 'Видео по ссылке не найдено';
        }
    }

    return $res;
}

function validate($db_connection, $fields, $validationArray) {
    $db_functions = ['validateExists', 'validateNotexists', 'validateCorrectpassword'];
    $validations = getValidationRules($validationArray);
    $errors = [];
    foreach ($validations as $field => $rules) {
        foreach ($rules as $rule) {
            [$name, $parameters] = getValidationNameAndParameters($rule);
            $methodName = getValidationMethodName($name);
            $methodParameters = array_merge([$fields, $field], $parameters);
            if (!function_exists($methodName)) {
                return 'Функции валидации ' . $methodName. ' не существует';
            }
            if (in_array($methodName, $db_functions)) {
                array_push($methodParameters, $db_connection);
            }


            if ($errors[$field] = call_user_func_array($methodName, $methodParameters)) {
                break;
            };
        }
    }
    return $errors;
}
