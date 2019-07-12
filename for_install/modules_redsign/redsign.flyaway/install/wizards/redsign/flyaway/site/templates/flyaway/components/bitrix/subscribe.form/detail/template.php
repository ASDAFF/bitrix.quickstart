<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><form action="<?=$arResult["FORM_ACTION"]?>" class="news-detail_subs"><?
	?><div class="row"><?
		?><div class="col col-sm-6"><?
			?><p class="news-detail_subs-text"><?=( $arParams["RSFLYAWAY_DETAIL_SUBSCRIBE_NOTE"]!='' ? $arParams["RSFLYAWAY_DETAIL_SUBSCRIBE_NOTE"] : GetMessage('RS.FLYAWAY.DETAIL_SUBSCRIBE_NOTE_DEFAULT') )?></p><?
		?></div><?
		?><div class="col col-sm-6 col-lg-5 col-lg-push-1"><?
			?><div class="input-group form"><?
				?><input type="text" name="sf_EMAIL" class="form-item form-control" placeholder="<?=GetMessage('RS.FLYAWAY.DETAIL_SUBSCRIBE_PLACEHOLDER')?>"><?
				?><span class="input-group-btn"><?
					?><button class="btn btn-default btn2" type="submit"><?=GetMessage('RS.FLYAWAY.SUBSCRIBE_BTN_SEND')?></button><?
				?></span><?
			?></div><?
		?></div><?
	?></div><?
?></form><?
