<?php

$mainMenu = [
    [
        'title' => 'Главная',
        'path' => '/',
        'role' => 'user'
    ],
    [
        'title' => 'Новинки',
        'path' => '/?new=on',
        'role' => 'user'
    ],
    [
        'title' => 'Sale',
        'path' => '/?sale=on',
        'role' => 'user'
    ],
    [
        'title' => 'Доставка',
        'path' => '/route/delivery/',
        'role' => 'user'
    ],
    [
        'title' => 'Товары',
        'path' => '/route/admin/products/',
        'role' => 'admin'
    ],
    [
        'title' => 'Заказы',
        'path' => '/route/admin/orders/',
        'role' => 'operator'
    ],
    [
        'title' => 'Авторизация',
        'path' => '/route/admin/',
        'role' => 'user'
    ],
];
