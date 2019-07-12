<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<a name="tb"></a>
<div class="step">
    <a href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SALE_RECORDS_LIST")?></a>
</div>

<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
<b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b><br /><br />
<div id="order_form" class="profile">
    <form method="post" action="<?=POST_FORM_ACTION_URI?>">
        <?=bitrix_sessid_post()?>
    <div class="order-info">
        <input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
        <?=str_replace("#URL_TO#", $arResult["URL_TO_DETAIL"], str_replace("#ID#", $arResult["ID"], GetMessage("SALE_CANCEL_ORDER")));?>
        <?= GetMessage("SALE_CANCEL_ORDER4") ?>:<br /><br />
        <textarea name="REASON_CANCELED" cols="60" rows="3"></textarea><br /><br />
    </div>
        <input type="hidden" name="CANCEL" value="Y">
    <div class="order-buttons">
        <input type="submit" name="action" value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>">
    </div>
    </form>
</div>
<?
else:
    echo ShowError($arResult["ERROR_MESSAGE"]);
endif;?>