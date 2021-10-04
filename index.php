<?php

require $_SERVER['DOCUMENT_ROOT'] . '/template/header.php';

?>
<main class="shop-page">
    <header class="intro">
        <div class="intro__wrapper">
            <h1 class=" intro__title">COATS</h1>
            <p class="intro__info">Collection 2018</p>
        </div>
    </header>
  <section class="shop container">
    <section class="shop__filter filter">
      <form id="filter">
        <?php
         include $_SERVER['DOCUMENT_ROOT'] . '/template/categories.php';
         include $_SERVER['DOCUMENT_ROOT'] . '/template/filter.php'
        ?>
      </form>
    </section>
    <div class="shop__wrapper">
        <section class="shop__sorting">
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/template/sorting.php'; ?>
        </section>
        <section class="shop__list">
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/template/products.php'; ?>
        </section>
        <ul class="shop__paginator paginator">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/template/pagination.php' ?>
        </ul>
    </div>
  </section>
  <section class="shop-page__order" hidden="">
      <?php include $_SERVER['DOCUMENT_ROOT'] . '/template/order.php' ?>
  </section>
  <section class="shop-page__popup-end" hidden="">
      <?php include $_SERVER['DOCUMENT_ROOT'] . '/template/popup.php' ?>
  </section>
</main>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/template/footer.php';
