<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="border_block">
	<?if( strlen($arResult["ERROR_MESSAGE"]) <= 0 ){?>
		<div class="module-form-block-wr order_cancel">
			<form method="post" action="<?=POST_FORM_ACTION_URI?>" class="form-block">
				<?=bitrix_sessid_post()?>
				<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
				<?=GetMessage("SALE_CANCEL_ORDER1")?> <?=GetMessage("SALE_CANCEL_ORDER2")?> <b>¹<?=$arResult["ACCOUNT_NUMBER"]?></b>?<br />
				<b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b>
				
				<div><?= GetMessage("SALE_CANCEL_ORDER4") ?>:</div>
				<textarea name="REASON_CANCELED" cols="60" rows="3"></textarea>
				<input type="hidden" name="CANCEL" value="Y">
				<!--noindex-->
					<button class="button vbig_btn" type="submit" name="action" value="<?=GetMessage("SALE_CANCEL_ORDER_BTN") ?>"><span><?= GetMessage("SALE_CANCEL_ORDER_BTN") ?></span></button>
				<!--/noindex-->
			</form>
		</div>
	<?}else{
		echo ShowError($arResult["ERROR_MESSAGE"]);
	}?>
</div>