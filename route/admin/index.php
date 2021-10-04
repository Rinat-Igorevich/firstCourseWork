<?php

include $_SERVER['DOCUMENT_ROOT'] . '/template/header.php';

?>

<main class="page-authorization">
  <h1 class="h h--1">Авторизация</h1>
    <?php if(isset($_SESSION['isAuth']) && $_SESSION['isAuth'] != true): ?>
    <p>Неверный логин или пароль</p>
    <?php endif; ?>
    <?php if(!isset($_SESSION['isAuth']) || !$_SESSION['isAuth']): ?>
  <form class="custom-form" action="#" method="POST">
      <input type="email" name="email" class="custom-form__input" value="example@mail.ru" required="">
      <input type="password" name="password" class="custom-form__input" required="">
    <button class="button" type="submit">Войти в личный кабинет</button>
  </form>
    <?php else: ?>
    <h3>Авторизация пройдена успешно!</h3>
    <?php endif;?>
</main>

<?php

include $_SERVER['DOCUMENT_ROOT'] . '/template/footer.php';
