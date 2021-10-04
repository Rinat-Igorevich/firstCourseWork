<?php

include $_SERVER['DOCUMENT_ROOT'] . '/data/main_menu.php';

if (isset($_SESSION['userRole'])) {
    $mainMenu = functions\getMenu($mainMenu, $_SESSION['userRole']);
} else {
    $mainMenu = functions\getMenu($mainMenu);
}

?>
<nav class="page-<?=$menuClass?>__menu">
    <ul class="main-menu main-menu--<?=$menuClass?>">
        <?php foreach ($mainMenu as $key => $url): ?>
            <li>
                <?php if (isset($_SESSION['isAuth']) && $_SESSION['isAuth'] && $url['title'] == 'Авторизация'): ?>
                    <a class="main-menu__item" href="<?= $url['path'] . '?login=no' ?>"><?= 'выйти' ?></a>
                <?php else: ?>
                    <a class="main-menu__item <?= $url['path'] == $_SERVER['REQUEST_URI'] ? 'active' : ''?>" href="<?= $url['path'] ?>"><?= $url['title'] ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>