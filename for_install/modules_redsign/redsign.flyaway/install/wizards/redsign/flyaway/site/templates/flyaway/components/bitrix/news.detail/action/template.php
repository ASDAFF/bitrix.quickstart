<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="action-detail action"><?
	
	?><div class="row"><?
		if( is_array($arResult["DETAIL_PICTURE"]) ) {
			?><div class="col col-md-10 action-pic"><?
				?><img <?
          ?>class="action-detail__image" <?
					?>src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" <?
					?>alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" <?
					?>title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>" <?
				?>/><?
			?></div><?
		}
		?><div class="col col-md-12 action-markers"><?
			if( $arResult['DISPLAY_PROPERTIES']['TEXT_MARKER']['DISPLAY_VALUE']!='' ) {
				?><span class="action-marker" <?
					if( $arResult['DISPLAY_PROPERTIES']['COLOR_MARKER']['DISPLAY_VALUE']!='' ) {
						?> style="background-color: <?=$arResult['DISPLAY_PROPERTIES']['TEXT_MARKER']['DISPLAY_VALUE']?>;" <?
					}
				?>><?=$arResult['DISPLAY_PROPERTIES']['COLOR_MARKER']['DISPLAY_VALUE']?></span><?
			}
			if( $arResult['DISPLAY_PROPERTIES']['DATA_ACTION']['DISPLAY_VALUE']!='' ) {
				?><span class="action_date"><?=$arResult['DISPLAY_PROPERTIES']['DATA_ACTION']['DISPLAY_VALUE']?></span><?
			}
		?></div>

		<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
			<div class="col col-md-10 action-detail-bar">
					<span class="action-detail-bar__date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
				</div><?
		endif;

		if(
			$arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_MARKER_TEXT']]['DISPLAY_VALUE']!='' ||
			$arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_ACTION_DATE']]['DISPLAY_VALUE']!=''
		) {
			?><div class="col col-md-10 markers"><?
				if( $arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_MARKER_TEXT']]['DISPLAY_VALUE']!='' ) {
					?><span class="marker" <?
						if( $arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_MARKER_COLOR']]['DISPLAY_VALUE']!='' ) {
							?> style="background-color: <?=$arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_MARKER_COLOR']]['DISPLAY_VALUE']?>;" <?
						}
					?>><?=$arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_MARKER_TEXT']]['DISPLAY_VALUE']?></span><?
				}
				if( $arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_ACTION_DATE']]['DISPLAY_VALUE']!='' ) {
					?><span class="action_date"><?=$arResult['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_PROP_ACTION_DATE']]['DISPLAY_VALUE']?></span><?
				}
			?></div><?
		}
		?><div class="col col-md-10 action-detail__text"><?=$arResult["DETAIL_TEXT"]?></div><?
	?></div><?
	if(isset($arResult['PROPERTIES']['STROKA_POD_STATI'])):
		?><div class="action-detail__podstati"><?print_r($arResult['PROPERTIES']['STROKA_POD_STATI']['VALUE']);?></div><?
	endif;

?></div><?

?><div class="row backshare"><?
	?><div class="col col-md-10 yashare"><?
		?><p><?=GetMessage("RS.FLYAWAY.YASHARE")?></p><?
		?><script type="text/javascript">(function() {
      if (window.pluso)if (typeof window.pluso.start == "function") return;
      if (window.ifpluso==undefined) { window.ifpluso = 1;
        var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
        s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
        s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
        var h=d[g]('body')[0];
        h.appendChild(s);
      }})();
    </script>
    <div class="pluso" data-background="transparent" data-options="big,round,line,horizontal,nocounter,theme=04" data-services="twitter,facebook,google,odnoklassniki,vkontakte"></div><?
	?></div><?
	?><div class="col col-md-10"><?
		?><a class="action-detailback" href="<?=$arResult["LIST_PAGE_URL"]?>"><i class="fa"></i><span><?=GetMessage("RS.FLYAWAY.BACK")?></span></a><?
	?></div><?
?></div><?