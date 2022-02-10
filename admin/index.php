<?php

namespace functionAll;

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/function.php';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$errorMessage = '';

if (isset($_POST['enter'])) {
    $user = getUser($email);
    if ($user && $user['email'] == $email && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: /admin/orders.php');
        die();
    } else {
        $errorMessage = 'ошибка авторизации';
    }
}
$title = 'Авторизация';
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/header.php';

$url = $_SERVER['REQUEST_URI'];
$url = explode(':', $url);
$url = $url[0];

?>

    <main class="page-authorization">
        <span class="error"><?=$errorMessage?></span>
        <h1 class="h h--1">Авторизация</h1>
        <form id="authorization" class="custom-form" action="/admin/index.php" method="post">
            <input type="text" name="email" class="custom-form__input" required="">
            <input type="password" name="password" class="custom-form__input" required="">
            <button class="button" name="enter" type="submit">Войти в личный кабинет</button>
    </main>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/appFiles/footer.php';
