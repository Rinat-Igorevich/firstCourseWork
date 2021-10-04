<?php
require $_SERVER['DOCUMENT_ROOT'] . '/template/header.php';

$products = \functions\getProductsForAdmin();

?>

<main class="page-products">
    <h1 class="h h--1">Товары</h1>
    <a class="page-products__button button" href="/route/admin/add">Добавить товар</a>
    <div class="page-products__header">
        <span class="page-products__header-field">Название товара</span>
        <span class="page-products__header-field">ID</span>
        <span class="page-products__header-field">Цена</span>
        <span class="page-products__header-field">Категория</span>
        <span class="page-products__header-field">Новинка</span>
    </div>
    <ul class="page-products__list">
        <?php foreach ($products as $product): ?>
        <li class="product-item page-products__item">
            <b class="product-item__name"><?= $product['name'] ?></b>
            <span class="product-item__field"><?= $product['id'] ?></span>
            <span class="product-item__field"><?= $product['price'] ?> руб.</span>
            <span class="product-item__field"><?= $product['category'] ?></span>
            <span class="product-item__field"><?= $product['is_new'] ? 'да' : 'нет' ?></span>
            <a href="/route/admin/add?product=<?= $product['id'] ?>" class="product-item__edit" aria-label="Редактировать"></a>
            <button class="product-item__delete" value="<?= $product['id'] ?>"></button>
        </li>
        <?php endforeach; ?>
    </ul>
    <ul class="products__paginator paginator">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/template/pagination.php' ?>
    </ul>

</main>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/template/footer.php';

