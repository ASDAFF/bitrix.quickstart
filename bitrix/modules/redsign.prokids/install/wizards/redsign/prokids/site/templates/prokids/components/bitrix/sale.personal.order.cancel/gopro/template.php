<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="ordercancel"><?

	if(strlen($arResult['ERROR_MESSAGE'])<=0)
	{
		?><form method="post" action="<?=POST_FORM_ACTION_URI?>"><?
			?><?=bitrix_sessid_post()?><?
			?><input type="hidden" name="CANCEL" value="Y"><?
			?><input type="hidden" name="ID" value="<?=$arResult['ID']?>"><?
			?><br /><?=GetMessage('SALE_CANCEL_ORDER1')?> <?
			?><a href="<?=$arResult['URL_TO_DETAIL']?>" target="_blank"><?=GetMessage('SALE_CANCEL_ORDER2')?> #<?=$arResult['ACCOUNT_NUMBER']?></a>?<br /><?
			?><b><?= GetMessage('SALE_CANCEL_ORDER3')?></b><br /><br /><?
			?><?=GetMessage('SALE_CANCEL_ORDER4')?>:<br /><?
			?><textarea class="reason" name="REASON_CANCELED"></textarea><br /><br /><?
			?><input type="submit" name="action" value="<?=GetMessage('SALE_CANCEL_ORDER_BTN')?>"><?
		?></form><?
	} else {
		?><?=ShowError($arResult['ERROR_MESSAGE']);?><?
	}
	
	?><br /><a class="btn btn3" href="<?=$arResult['URL_TO_LIST']?>"><?=GetMessage('SALE_RECORDS_LIST')?></a><?
?></div>