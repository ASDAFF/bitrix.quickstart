<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a name="tb"></a>
<a href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SALE_RECORDS_LIST")?></a>
<br /><br />
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
		<p><?=str_replace("#URL_TO#", $arResult["URL_TO_DETAIL"], str_replace("#ID#", $arResult["ID"], GetMessage("SALE_CANCEL_ORDER")));?>
		<b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b></p>
		<p><?= GetMessage("SALE_CANCEL_ORDER4") ?>:</p>
		<textarea name="REASON_CANCELED" cols="60" style="height:90px"></textarea><br /><br />
		<input type="hidden" name="CANCEL" value="Y">
		<input type="submit" name="action" class="bt3" value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>">
	</form>
<?
else:
	echo ShowError($arResult["ERROR_MESSAGE"]);
endif;?>