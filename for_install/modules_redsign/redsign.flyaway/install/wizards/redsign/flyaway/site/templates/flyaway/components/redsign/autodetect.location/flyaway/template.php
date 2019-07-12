<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?><div class="locationbig"><?

	ShowMessage($arResult['ERROR_MESSAGE']);

	?><form id="locforma" class="forma" action="<?=$arResult["ACTION_URL"]?>" method="POST"><?

		echo bitrix_sessid_post();
		?><input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" /><?
		?><input type="hidden" name="PARAMS_HASH" value="<?=$arParams["PARAMS_HASH"]?>" /><?

		?><div class="fancybox-title fancybox-title-inside-wrap"><?=Loc::getMessage("RSLOC_CHOOSE_FROM_LIST")?></div><?

		if (is_array($arResult["LOCATIONS"]) && count($arResult["LOCATIONS"]) > 0) {
			?><div class="items clearfix row"><?
				?><div class="col-xs-6 col-md-4 col-lg-3 gui-box"><?
					?><label class="gui-radiobox" for="rsloc_<?=$arResult["LOCATION"]["ID"]?>"><?
							?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arResult["LOCATION"]["ID"]?>" class="gui-radiobox-item" <?
								?>id="rsloc_<?=$arResult["LOCATION"]["ID"]?>" checked="checked" <?
								?>onclick="RSMSHOPSelectCity(this,'<?=$arParams["CITY_ID"]?>')" /><?
							?><span class="gui-out"><?
								?><span class="gui-inside"></span><?
							?></span><?
							print_r($arResult["LOCATION"]["CITY_NAME"]);
					?></label><?
				?></div><?

				foreach ($arResult["LOCATIONS"] as $arLocation) {
					if ($arResult["LOCATION"]["ID"]!=$arLocation["ID"]) {
						?><div class="col-xs-6 col-md-4 col-lg-3 gui-box"><?
							?><label class="gui-radiobox" for="rsloc_<?=$arLocation["ID"]?>"><?
								?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arLocation["ID"]?>" class="gui-radiobox-item" <?
									?>id="rsloc_<?=$arLocation["ID"]?>" <?
									?>onclick="RSMSHOPSelectCity(this,'<?=$arParams["CITY_ID"]?>')" /><?
								?><span class="gui-out"><?
									?><span class="gui-inside"></span><?
								?></span><?
								print_r($arLocation["CITY_NAME"]);
							?></label><?
						?></div><?
					}
				}
			?></div><?
		}
		?><div class="ajaxlocation"><?
			?><div class="line"></div><?
			?><div class="title"><?=Loc::getMessage('RSLOC_OR_ENTER')?></div><?
			?><div class="cominput field"><?
				$value = $arResult['LOCATION']['ID'];
				CSaleLocation::proxySaleAjaxLocationsComponent(array(
					"AJAX_CALL" => "N",
					"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
					"REGION_INPUT_NAME" => "REGION_tmp",
					"CITY_INPUT_NAME" => $arParams['CITY_ID'],
					"CITY_OUT_LOCATION" => "Y",
					"LOCATION_VALUE" => $value,
					"ORDER_PROPS_ID" => '',
					"ONCITYCHANGE" => '',
					"SIZE1" => '',
				),
					array(
						"ID" => $value,
						"CODE" => "",
						"SHOW_DEFAULT_LOCATIONS" => "Y",
						"JS_CALLBACK" => "submitFormProxy",
						"JS_CONTROL_DEFERRED_INIT" => '',
						"JS_CONTROL_GLOBAL_ID" => '',
						"DISABLE_KEYBOARD_INPUT" => "Y",
						"PRECACHE_LAST_LEVEL" => "Y",
						"PRESELECT_TREE_TRUNK" => "Y",
						"SUPPRESS_ERRORS" => "Y"
					),
					'popup',
					true,
					'location-block-wrapper'
				);
			?></div><?
		?><br /><input class="clickforsubmit btn btn-primary btn2" type="submit" name="submit" value="<?=Loc::getMessage('RSLOC_BTN_YES')?>" /><?
	?></div><?

	?></form><?

?></div><?

?><script>
	$(document).ready(function(){
		setTimeout(function(){
			if ( $('.fancybox-inner').find('.locationbig').length > 0) {
				$('.fancybox-inner').css({overflow:'visible'});
				$('.fancybox-inner').find('.locationbig').find('.forma').attr('action', window.location.href);
			}
		}, 50);
	});
</script>
