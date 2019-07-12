<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Localization\Loc;
?>
<div class="row order-cancel">
    <div class="col col-md-12">
        <a href="<?=$arResult["URL_TO_LIST"]?>"><?=Loc::getMessage('SALE_RECORDS_LIST');?></a>
    </div>
    <div class="col col-md-12">
        <?php if(strlen($arResult["ERROR_MESSAGE"]) <= 0): ?>
        <form class="form-horizontal" method="post" action="<?=POST_FORM_ACTION_URI?>">
            <input type="hidden" name="CANCEL" value="Y">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
            <p>
                <?=Loc::getMessage('SALE_CANCEL_ORDER1');?>
                <a href="<?=$arResult["URL_TO_DETAIL"]?>"><?=Loc::getMessage("SALE_CANCEL_ORDER2")?> #<?=$arResult["ACCOUNT_NUMBER"]?></a>
                <b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b>
            </p>
            <div clas="field">
                <label>
                    <?=Loc::getMessage("SALE_CANCEL_ORDER4")?>:
                    <textarea class="form-control" name="REASON_CANCELED"></textarea>
                </label>
            </div>
            <div class="field">
                <input class="btn btn-default btn2" type="submit" name="action" value="<?=Loc::getMessage("SALE_CANCEL_ORDER_BTN") ?>">
            </div>
        </form>
        <?php else: ?>
            <?=ShowError($arResult["ERROR_MESSAGE"]);?>
        <?php endif; ?>
    </div>
</div>