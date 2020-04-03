<?php

$user_name = 'Михаил'; // укажите здесь ваше имя
$title = 'ReadMe';
$cards = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'author' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального',
        'author' => 'Владик',
        'avatar' => 'userpic.jpg'
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'author' => 'Виктор',
        'avatar' => 'userpic-mark.jpg'
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'author' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'author' => 'Владик',
        'avatar' => 'userpic.jpg'
    ],
];

function truncate_text(string $text, int $truncate_length = 300)
{
    if (mb_strlen($text) <= $truncate_length) {
        $final_text = $text;
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
    }
    return $final_text;
}

require_once('helpers.php');
$page_content = include_template('main.php', ['cards' => $cards]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user_name, 'title' => $title]);
print($layout_content);

?>
