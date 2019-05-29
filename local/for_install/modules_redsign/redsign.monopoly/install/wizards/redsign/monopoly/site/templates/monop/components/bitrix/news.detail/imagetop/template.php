<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="newsdetail imgageleft"><?
	
	?><div class="row"><?
		if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]) {
			?><div class="col col-md-12 activefrom"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div><?
		}
		if( is_array($arResult["DETAIL_PICTURE"]) ) {
			?><div class="col col-md-12 pic"><?
				?><img <?
					?>src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" <?
					?>alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" <?
					?>title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>" <?
				?>/><?
			?></div><?
		}
		?><div class="col col-md-12 text"><?=$arResult["DETAIL_TEXT"]?></div><?
	?></div><?

?></div><?