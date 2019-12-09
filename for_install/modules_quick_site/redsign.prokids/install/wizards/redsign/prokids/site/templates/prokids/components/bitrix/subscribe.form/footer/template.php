<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="footersubscribe" id="footersubscribe"><?
	?><div class="title"><?=GetMessage('subscr_title')?></div><?
	
	$frame = $this->createFrame('footersubscribe', false)->begin();

	?><form action="<?=$arResult['FORM_ACTION']?>"><?

		foreach($arResult['RUBRICS'] as $itemID => $itemValue)
		{
			?><input class="noned" type="checkbox" name="sf_RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) { echo ' checked'; }?> title="<?=$itemValue['NAME']?>" /><?
		}
		?><div class="inp"><input type="text" name="sf_EMAIL" size="20" value="<?=$arResult['EMAIL']?>" title="<?=GetMessage('subscr_form_email_title')?>" placeholder="<?=GetMessage('subscr_form_email_title')?>" /></div><?
		?><input class="nonep" type="submit" name="OK" value="<?=GetMessage('subscr_form_button')?>" /><?
		?><a class="btn1 submit" href="#"><?=GetMessage('subscr_form_button')?></a><?
	?></form><?
	
	$frame->beginStub();
	
	?><form action="<?=$arResult['FORM_ACTION']?>"><?

		foreach($arResult['RUBRICS'] as $itemID => $itemValue)
		{
			?><input class="noned" type="checkbox" name="sf_RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue['CHECKED']) { echo ' checked'; }?> title="<?=$itemValue['NAME']?>" /><?
		}

		?><div class="inp"><input type="text" name="sf_EMAIL" size="20" value="" title="<?=GetMessage('subscr_form_email_title')?>" placeholder="<?=GetMessage('subscr_form_email_title')?>" /></div><?
		?><input class="nonep" type="submit" name="OK" value="<?=GetMessage('subscr_form_button')?>" /><?
		?><a class="btn1 submit" href="#"><?=GetMessage('subscr_form_button')?></a><?
	?></form><?
	
	$frame->end();
	
?></div>
