<?php

include $_SERVER['DOCUMENT_ROOT'] . '/template/header.php';
if (isset($_GET['product'])) {
    $product = \functions\getProductByID($_GET['product']);
}
?>

<main class="page-add">
    <h1 class="h h--1"><?= (isset($product) ? 'Изменение товара' : 'Добавление товара') ?></h1>
    <form class="custom-form" action="#" method="post">
        <fieldset class="page-add__group custom-form__group">
            <legend class="page-add__small-title custom-form__title">Данные о товаре</legend>
            <label for="product-name" class="custom-form__input-wrapper page-add__first-wrapper">
                <input type="text" class="custom-form__input" name="product-name" id="product-name" value="<?= $product['name'] ?? '' ;?>">
                <p class="custom-form__input-label">
                    <?= (isset($product) ? '' : 'Название товара') ?>
                </p>
            </label>
            <label for="product-price" class="custom-form__input-wrapper">
                <input type="text" class="custom-form__input" name="product-price" id="product-price" value="<?= $product['price'] ?? '' ?>">
                <p class="custom-form__input-label">
                    <?= isset($product) ? '' : 'Цена товара' ?>
                </p>
            </label>
        </fieldset>
        <fieldset class="page-add__group custom-form__group">
            <legend class="page-add__small-title custom-form__title">Фотография товара</legend>
            <ul class="add-list">
                <li class="add-list__item add-list__item--add" >
                    <input type="file" name="product-photo" id="product-photo" hidden="">
                    <?php if (isset($product)):?>
                    <label for="product-photo" >
                        <img src="<?= $product['src'] ?>">
                    </label>
                        <script>
                        $( '.add-list__item--add label' ).css('display', 'contents');
                        </script>
                    <?php else: ?>
                    <label for="product-photo" >Добавить фотографию</label>
                    <?php endif; ?>
                </li>
            </ul>
        </fieldset>
        <fieldset class="page-add__group custom-form__group">
            <legend class="page-add__small-title custom-form__title">Раздел</legend>
            <div class="page-add__select">

                <select name="category" class="custom-form__select" multiple="multiple">
                    <option hidden="">Название раздела</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['engName'] ?>"
                        <?php if (isset($product)): ?>
                            <?php foreach ($product['category'] as $productCategory): ?>
                                <?= $productCategory == $category['engName'] ? 'selected' : ''?>
                            <?php endforeach; ?>
                        <?php endif;?>
                        ><?= $category['ruName'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="checkbox" name="new" id="new" class="custom-form__checkbox" <?php if (isset($product) && $product['is_new']) echo 'checked'?>>
            <label for="new" class="custom-form__checkbox-label">Новинка</label>
            <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox" <?php if (isset($product) && $product['is_sale']) echo 'checked'?>>
            <label for="sale" class="custom-form__checkbox-label">Распродажа</label>
        </fieldset>
        <button class="button" type="submit" value="<?= (isset($product) ? 'changeProduct' : 'addProduct') ?>"><?= (isset($product) ? 'Изменить товар' : 'Добавить товар') ?></button>
    </form>
    <section class="shop-page__popup-end page-add__popup-end" hidden="">
        <div class="shop-page__wrapper shop-page__wrapper--popup-end">
            <h2 class="h h--1 h--icon shop-page__end-title"><?= (isset($product) ? 'Товар успешно изменен' : 'Товар успешно добавлен') ?></h2>
        </div>
    </section>
</main>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/template/footer.php';
