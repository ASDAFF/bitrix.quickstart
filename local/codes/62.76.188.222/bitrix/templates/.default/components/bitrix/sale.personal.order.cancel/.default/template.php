<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a name="tb"></a>
<a href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SALE_RECORDS_LIST")?></a>
<br /><br />
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
		<?=GetMessage("SALE_CANCEL_ORDER1") ?>
		<a href="<?=$arResult["URL_TO_DETAIL"]?>"><?=GetMessage("SALE_CANCEL_ORDER2")?> #<?=$arResult["ID"]?></a>?
		<b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b><br /><br />
<div class="b-reviews_add__title">Укажите, пожалуйста, причину отмены заказа:</div>
                
                <table class="b-subcribe__table">
                        <tbody><tr>
                <td><textarea class="b-text"  name="REASON_CANCELED" rows="5" id="" name=""></textarea></td>
                        </tr>
                </tbody></table>
                 
		<input type="hidden" name="CANCEL" value="Y">
		<input type="submit"  class="b-button" name="action" value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>">
	</form>
<?
else:
	echo ShowError($arResult["ERROR_MESSAGE"]);
endif;?>