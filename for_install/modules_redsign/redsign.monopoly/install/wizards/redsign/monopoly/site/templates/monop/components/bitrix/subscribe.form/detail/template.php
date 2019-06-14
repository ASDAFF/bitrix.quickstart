<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="detail_subs"><?
	?><form action="<?=$arResult["FORM_ACTION"]?>"><?
		?><div class="row"><?
			?><div class="col col-md-7 col-sm-6"><?
				?><p><?=( $arParams["RSMONOPOLY_DETAIL_SUBSCRIBE_NOTE"]!='' ? $arParams["RSMONOPOLY_DETAIL_SUBSCRIBE_NOTE"] : GetMessage('RS.MONOPOLY.DETAIL_SUBSCRIBE_NOTE_DEFAULT') )?></p><?
			?></div><?
			?><div class="col col-md-5 col-sm-6"><?
				?><div class="input-group animated fadeInDown"><?
					?><input type="text" class="form-control" placeholder="<?=GetMessage('RS.MONOPOLY.DETAIL_SUBSCRIBE_PLACEHOLDER')?>"><?
					?><span class="input-group-btn"><?
						?><button class="btn btn-primary" type="submit"><?=GetMessage('RS.MONOPOLY.SUBSCRIBE_BTN_SEND')?></button><?
					?></span><?
				?></div><?
			?></div><?
		?></div><?
	?></form><?
?></div>