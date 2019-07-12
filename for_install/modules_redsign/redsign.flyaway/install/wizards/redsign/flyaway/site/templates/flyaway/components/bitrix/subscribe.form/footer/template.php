<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

?><form action="<?=$arResult["FORM_ACTION"]?>"><?
	?><div class="footer-label"><?=Loc::getMessage('RS.FLYAWAY.SUBSCRIBE_NOTE')?></div><?
	?><div class="input-group form"><?
		?><input type="text"
				 class="form-item form-control"
				 name="sf_EMAIL"
				 placeholder="<?=Loc::getMessage('RS.FLYAWAY.SUBSCRIBE_PLACEHOLDER');?>"
				 value="<?=!empty($arResult['EMAIL'])?$arResult['EMAIL']:''?>"><?
		?><span class="input-group-btn"><?
			?><button class="btn btn-default btn2 btn-subscribe" type="submit"><?=Loc::getMessage('RS.FLYAWAY.SUBSCRIBE_BTN_SEND')?></button><?
		?></span><?
	?></div><?
?></form><?
