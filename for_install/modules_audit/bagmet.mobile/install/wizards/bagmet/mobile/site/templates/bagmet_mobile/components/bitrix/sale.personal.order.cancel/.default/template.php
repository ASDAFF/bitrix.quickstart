<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="order_wrapper">

	<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
		<form class="order_cancel_form" method="post" action="<?=POST_FORM_ACTION_URI?>">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
			<p><?=str_replace("#URL_TO#", $arResult["URL_TO_DETAIL"], str_replace("#ID#", $arResult["ID"], GetMessage("SALE_CANCEL_ORDER")));?>
			<?= GetMessage("SALE_CANCEL_ORDER3") ?></p>

			<p><?=GetMessage("SALE_GO_BACK")?><a href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SALE_RECORDS_LIST")?></a></p>
			<br><br>
			<p><?= GetMessage("SALE_CANCEL_ORDER4") ?>:</p>
			<textarea name="REASON_CANCELED" cols="60" class="order_cancel_textarea"></textarea><br /><br />
			<input type="hidden" name="CANCEL" value="Y">
			<input type="submit" name="action" class="orders_cancel_btn" value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>">
		</form>
	<?
	else:
		echo ShowError($arResult["ERROR_MESSAGE"]);
	endif;?>
</div>