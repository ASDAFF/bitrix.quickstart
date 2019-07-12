<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}
?>

<?php if(strlen($arResult["ERROR_MESSAGE"])<=0): ?>
<div class="row">
    <div class="col col-md-12">
        <?=$arResult["DATE"];?>
        <br>
        <ul>
            <?php foreach($arResult["ACCOUNT_LIST"] as $val): ?>
                <li><?= $val["INFO"] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
