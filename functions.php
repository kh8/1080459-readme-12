<?php
function truncate_text(string $text, int $truncate_length = 300)
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

function get_post_data($post_id): array
{
    $random_date = generate_random_date($post_id);
    $post_date = date_create($random_date);
    $post_data = ['post_time' => $post_date];
    return $post_data;
}

function absolute_time_to_relative($absolute_time)
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
