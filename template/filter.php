<?php

$priceForSlider = functions\getPriceForSlider();

$min = $_GET['min'] ?? $priceForSlider['minimumPrice'];
$max = $_GET['max'] ?? $priceForSlider['maximumPrice'];

?>
<div class="filter__wrapper" xmlns="http://www.w3.org/1999/html">
    <b class="filter__title">Фильтры</b>
    <div class="filter__range range">
        <span class="range__info">Цена</span>
        <div class="range__line" data-min="<?= $priceForSlider['minimumPrice']?>" data-max="<?= $priceForSlider['maximumPrice'] ?>" aria-label="Range Line"></div>
        <div class="range__res">
            <input type="hidden" name="min" id="minimumPrice" value="<?= $min ?>">
            <span class="range__res-item min-price"> <?= $min ?> руб. </span>
            <input type="hidden" name="max"  id="maximumPrice" value="<?= $max ?>">
            <span class="range__res-item max-price"><?= $max?> руб. </span>
        </div>
    </div>
</div>
<fieldset class="custom-form__group">
    <input type="checkbox" name="new" id="new" class="custom-form__checkbox" <?php if (isset($_GET['new'])) echo 'checked'?>>
    <label for="new" class="custom-form__checkbox-label custom-form__info" style="display: block;">Новинка</label>
    <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox" <?php if (isset($_GET['sale'])) echo 'checked'?>>
    <label for="sale" class="custom-form__checkbox-label custom-form__info" style="display: block;">Распродажа</label>
</fieldset>
<button class="button" type="submit" style="width: 100%">Применить</button>
