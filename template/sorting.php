<?php

$count = functions\getProductsCount(functions\getSort());

?>

<div class="shop__sorting-item custom-form__select-wrapper sort-by">
    <select form="filter" class="custom-form__select" id="sortBy" name="sortBy">
        <option hidden="" value="price" >Сортировка</option>
        <option value="price" id="orderByPrice" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'price') echo 'selected'?>>По цене</option>
        <option value="name" id="orderByName" <?php if (isset($_GET['sortBy']) && $_GET['sortBy'] == 'name') echo 'selected'?>>По названию</option>
    </select>
</div>
<div class="shop__sorting-item custom-form__select-wrapper">
    <select form="filter" class="custom-form__select" id="sortOrder" name="sortOrder">
        <option hidden="" value="ASC">Порядок</option>
        <option value="ASC" id="sort_ASC" <?php if (isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'ASC') echo 'selected'?>>По возрастанию</option>
        <option value="DESC" id="sort_DESC" <?php if (isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'DESC') echo 'selected'?>>По убыванию</option>
    </select>
</div>
<p class="shop__sorting-res">Найдено <span class="res-sort" id="productsCount"><?= $count ?></span> моделей</p>

