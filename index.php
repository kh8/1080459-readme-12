<?php

$user_name = 'Михаил'; // укажите здесь ваше имя
$title = 'ReadMe';
$is_auth = rand(0,1);
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
        'content' => 'Не могу дождатьногу я надНе могу дождаться начала финального',
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

require_once('helpers.php');
require_once('functions.php');
$page_content = include_template('main.php', ['cards' => $cards]);
$layout_content = include_template('layout.php', ['content' => $page_content, 'user' => $user_name, 'title' => $title, 'is_auth' => $is_auth]);
print($layout_content);
