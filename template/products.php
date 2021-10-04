<?php foreach ($products = functions\getProducts() as $product): ?>
    <article class="shop__item product" id="<?= $product['id'] ?>" tabindex="0">
        <div class="product__image">
            <img src="<?= $product['src'] ?>" alt="product-name">
        </div>
        <p class="product__name"><?= $product['name'] ?></p>
        <span class="product__price"><?= number_format($product['price'], 0, '.', ' ') ?> руб.</span>
    </article>
<?php endforeach; ?>

