<?php

session_start();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php';
include $_SERVER['DOCUMENT_ROOT'] . '/data/categories.php';
require $_SERVER['DOCUMENT_ROOT'] . '/config.php';

use function functions\{showMenu, verifyUser, getUser};

if (isset($_GET['login']) && $_GET['login'] == 'no') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

if (isset($_POST['email'])) {

        if (verifyUser($_POST['email'], $_POST['password'])) {
            $_SESSION['userRole'] = getUser($_POST['email'])['role'];
            $_SESSION['isAuth'] = true;
        } else {
            $_SESSION['isAuth'] = false;
        }
}

if (isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'filter_data':
            if ($_POST['requestFrom'] == 'shop') {
                $result = functions\getProductsByAjax();
                if (!isset($result['output'])) {
                    $result['output'] = '<h3>товары не найдены</h3>';
                }

                $data = $result['output'] ? ['result' => $result] : ['error' => $result];
                die(json_encode($data));

            } elseif ($_POST['requestFrom'] == 'admin') {
                $result = functions\getProductsForAdmin();

                $data = $result['output'] ? ['result' => $result] : ['error' => $result];
                die(json_encode($data));
            }
            break;

        case 'create_order':
            $error = functions\validateData();

            if ($error == null) {
                $data = ['result' => functions\createOrder()];

            } else {
                $data = ['error' => $error];
            }

            die(json_encode($data));

        case 'change_order_status':
            $result = functions\changeOrderStatus();

            $data = ['result' => $result];
            die(json_encode($data));

        case 'add_product':
            $error = functions\validateProduct();

            if ($error != null) {
                $data = ['error' => $error];
            }
            else {
                $result = functions\addProduct();

                $data = ['result' => $result];
            }

            die(json_encode($data));

        case 'change_product':
            $result = functions\changeProduct();

            $data = ['result' => $result];
            die(json_encode($data));

        case 'delete_product':
            if (functions\disableProduct($_POST['product_id_to_delete'])) {
                die(json_encode(['result' => 'success']));
            }
            die(json_encode(['result' => 'error']));
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Fashion</title>

    <meta name="description" content="Fashion - интернет-магазин">
    <meta name="keywords" content="Fashion, интернет-магазин, одежда, аксессуары">
    <meta name="theme-color" content="#393939">

    <link rel="preload" href="/img/intro/coats-2018.jpg" as="image">
    <link rel="preload" href="/fonts/opensans-400-normal.woff2" as="font" crossorigin>
    <link rel="preload" href="/fonts/roboto-400-normal.woff2" as="font" crossorigin>
    <link rel="preload" href="/fonts/roboto-700-normal.woff2" as="font" crossorigin>

    <link rel="icon" href="/img/favicon.png">
    <link rel="stylesheet" href="/css/style.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/js/scripts.js" defer=""></script>
</head>
<body>
<header class="page-header">
    <a class="page-header__logo" href="/">
        <img src="/img/logo.svg" alt="Fashion">
    </a>
    <?php showMenu('header'); ?>
</header>
