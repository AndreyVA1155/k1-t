<?php

namespace functionAll;

if (!isset($_SESSION['user'])) {
    $menu = menu(4);
    $auth['path'] = '/admin/index.php';
    $auth['name'] = 'Авторизация';
} elseif ($_SESSION['user']['status'] == 'admin') {
    $menu = menu(6);
    $auth['path'] = $_SERVER['DOCUMENT_ROOT'];
    $auth['name'] = 'Выйти с сайта';
} elseif ($_SESSION['user']['status'] == 'operator') {
    $menu = menu(5);
    $auth['path'] = $_SERVER['DOCUMENT_ROOT'];
    $auth['name'] = 'Выйти с сайта';
} elseif ($_SESSION['user']['status'] == 'user') {
    $menu = menu(4);
    $auth['path'] = '/?=';
    $auth['name'] = 'Выйти с сайта';
}
?>
<footer class="page-footer">
    <div class="container">
        <a class="page-footer__logo" href="/">
            <img src="/img/logo--footer.svg" alt="Fashion">
        </a>
        <nav class="page-footer__menu">
            <ul class="main-menu main-menu--footer">
                <?php foreach ($menu as $item): ?>
                    <li>
                        <a class="main-menu__item" href="<?=$item['path']?>"><?=$item['name']?></a>
                    </li>
                <?php endforeach ?>
            </ul>
        </nav>
        <address class="page-footer__copyright">
            © Все права защищены
        </address>
    </div>
</footer>
</body>
</html>

