<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
    <h3 id="myModalLabel-01"><?=GetMessage('BUY_ONE_CLICK')?></h3>
</div>
<div class="modal-body">
    <form autocomplete="on" action="<?= SITE_DIR ?>include/catalog/element/quick-buy.php" name="quick-buy-form-element"
          method="post">
        <input type="hidden" name="colorId" value="0">
        <input type="hidden" name="sizeId" value="0">
        <input type="hidden" name="productId" value="0">
        <input type="hidden" name="url" value="">

        <div id="autorize_inputs_i">
            <? if (isset($arResult['ERROR'])): ?>
                <div id="alert" class="alert alert-error" style="margin-top:0px;">
                    <?= implode("<br>", $arResult['ERROR']) ?>
                </div>
            <? endif; ?>
            <div class="login">
                <div class="name">
                    E-mail
                </div>
                <div class="value">
                    <input type="text" autocomplete="on" value="<?=$arParams['REQUEST']['email'] ?>" maxlength="50" name="email">
                </div>
                <div class="name"><?=GetMessage('PHONE')?>: <span class="starrequired"> * </span></div>
                <div class="value"><input type="text" value="<?=$arParams['REQUEST']['phone'] ?>" name="phone" size="30"></div>
            </div>

        </div>
        <div class="sub-div-n">
            <input type="submit" value="<?=GetMessage('BUY')?>" class="btn">
        </div>
    </form>
    <div class="clear"></div>

</div>