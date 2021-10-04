<?php
$active = parse_url($_SERVER['REQUEST_URI'])['path'];
?>
<div class="filter__wrapper">
    <b class="filter__title">Категории</b>
    <ul class="filter__list">
        <li>
            <a class="filter__list-item <?= $active == '/' ? 'active' : '' ?>" href="/">Все</a>
        </li>
        <?php foreach ($categories as $key => $category): ?>
        <li>
            <a class="filter__list-item <?= $active == $category['url'] ? 'active' : '' ?>" href="<?= $category['url'] ?>"><?= $category['ruName'] ?></a>
        </li>
        <?php endforeach ?>
    </ul>
</div>