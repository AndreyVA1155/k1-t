<?php

namespace functionAll;

ini_set('display_errors', 1); //показывает все предупреждения и ошибки
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_URI'] != '/admin/index.php' && $_SERVER["REQUEST_URI"] != '/' && !isset($_SESSION['user']))     {
//    header('Location: /');
//    die();
}

if (!isset($_SESSION['user'])) {
    $menu = menu(4);
    $auth['path'] = '';
    $auth['name'] = '';
} elseif ($_SESSION['user']['status'] == 'admin') {
    $menu = menu(7);
    $auth['path'] = '/?auth=0';
    $auth['name'] = 'Выйти с сайта';
} elseif ($_SESSION['user']['status'] == 'operator') {
    $menu = menu(5);
    $auth['path'] = '/?auth=0';
    $auth['name'] = 'Выйти с сайта';
} elseif ($_SESSION['user']['status'] == 'user') {
    $menu = menu(4);
    $auth['path'] = '/?auth=0';
    $auth['name'] = 'Выйти с сайта';
}


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?=$title?></title>

    <meta name="description" content="Fashion - интернет-магазин">
    <meta name="keywords" content="Fashion, интернет-магазин, одежда, аксессуары">

    <meta name="theme-color" content="#393939">

    <link rel="preload" href="/img/intro/coats-2018.jpg" as="image">
    <link rel="preload" href="/fonts/opensans-400-normal.woff2" as="font">
    <link rel="preload" href="/fonts/roboto-400-normal.woff2" as="font">
    <link rel="preload" href="/fonts/roboto-700-normal.woff2" as="font">


    <link rel="icon" href="/img/favicon.png">
    <link rel="stylesheet" href="/css/style.min.css">


    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/js/scripts.js" defer=""></script>


</head>
<body>
<header class="page-header">
    <a class="page-header__logo" href="/">
        <img src="/img/logo.svg" alt="Fashion">
    </a>
    <nav class="page-header__menu">
        <ul class="main-menu main-menu--header">
            <?php foreach ($menu as $item): ?>
                <li>
                    <a class="main-menu__item" href="<?=$item['path']?>"><?=$item['name']?></a>
                </li>
            <?php endforeach ?>
            <li>
                <a class="main-menu__item" href="<?=$auth['path']?>"><?=$auth['name']?></a>
            </li>
        </ul>
    </nav>
</header>
