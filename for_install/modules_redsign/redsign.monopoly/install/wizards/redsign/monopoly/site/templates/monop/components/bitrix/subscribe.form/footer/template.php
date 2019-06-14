<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="bottom_subs"><?
	?><form action="<?=$arResult["FORM_ACTION"]?>"><?
		?><p><?=GetMessage('RS.MONOPOLY.SUBSCRIBE_NOTE')?></p><?
		?><div class="input-group animated fadeInDown"><?
			?><input type="text" 
					 class="form-control" 
					 name="sf_EMAIL" 
					 placeholder="<?=GetMessage('RS.MONOPOLY.SUBSCRIBE_PLACEHOLDER')?>"
					 value="<?=!empty($arResult['EMAIL'])?$arResult['EMAIL']:''?>"><?
			?><span class="input-group-btn"><?
				?><button class="btn btn-primary" type="submit"><?=GetMessage('RS.MONOPOLY.SUBSCRIBE_BTN_SEND')?></button><?
			?></span><?
		?></div><?
	?></form><?
?></div>