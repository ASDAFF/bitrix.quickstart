<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="newsdetail action"><?
	
	?><div class="row"><?
		if( is_array($arResult["DETAIL_PICTURE"]) ) {
			?><div class="col col-md-12 pic"><?
				?><img <?
					?>src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" <?
					?>alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" <?
					?>title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>" <?
				?>/><?
			?></div><?
		}
		if(
			$arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_MARKER_TEXT']]['DISPLAY_VALUE']!='' ||
			$arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_ACTION_DATE']]['DISPLAY_VALUE']!=''
		) {
			?><div class="col col-md-12 markers"><?
				if( $arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_MARKER_TEXT']]['DISPLAY_VALUE']!='' ) {
					?><span class="marker" <?
						if( $arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_MARKER_COLOR']]['DISPLAY_VALUE']!='' ) {
							?> style="background-color: <?=$arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_MARKER_COLOR']]['DISPLAY_VALUE']?>;" <?
						}
					?>><?=$arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_MARKER_TEXT']]['DISPLAY_VALUE']?></span><?
				}
				if( $arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_ACTION_DATE']]['DISPLAY_VALUE']!='' ) {
					?><span class="action_date"><?=$arResult['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_ACTION_DATE']]['DISPLAY_VALUE']?></span><?
				}
			?></div><?
		}
		?><div class="col col-md-12 text"><?=$arResult["DETAIL_TEXT"]?></div><?
	?></div><?

?></div><?