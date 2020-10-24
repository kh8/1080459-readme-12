<?php

function get_user(): ?array
{
    if ($_SESSION['is_auth'] !== 1) {
        return null;
    }

    $user = [];

    $user['id'] = $_SESSION['id'];
    $user['name'] = $_SESSION['username'];
    $user['avatar'] = $_SESSION['avatar'];

    return $user;
}
