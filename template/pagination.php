<?php

$paginator = \functions\getPaginator($_GET);

?>
<?php if (!($paginator['prevPage'] == 0)): ?>
    <li>
        <a class="paginator__item"><</a>
    </li>
<?php endif; ?>
<?php for ($i = 1; $i <= $paginator['pagesCount']; $i++): ?>
    <?php if ($paginator['currentPage'] == $i):?>
        <li>
            <a class="paginator__item <?= 'active' ?>"><?= $i ?></a>
        </li>
    <?php else: ?>
        <li>
            <a class="paginator__item" ><?= $i ?></a>
        </li>
    <?php endif ?>
<?php endfor; ?>
<?php if (!($paginator['nextPage'] > $paginator['pagesCount'])): ?>
    <li>
        <a class="paginator__item">></a>
    </li>
<?php endif; ?>

