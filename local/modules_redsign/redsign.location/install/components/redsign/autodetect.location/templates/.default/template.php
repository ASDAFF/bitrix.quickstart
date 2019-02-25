<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?><div class="rslocation"><?

	ShowMessage($arResult['ERROR_MESSAGE']);
	
	?><form action="<?=$arResult["ACTION_URL"]?>" method="POST"><?
		echo bitrix_sessid_post();
		?><input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" /><?
		?><input type="hidden" name="PARAMS_HASH" value="<?=$arParams["PARAMS_HASH"]?>" /><?
		echo GetMessage("RSLOC_QUESTION_1");
		?>: <a href="#" onclick="document.getElementById('rsloc_ajax_locations').style.display='block';return false;"><?=$arResult["LOCATION"]["CITY_NAME"]?></a><?
		if(is_array($arResult["LOCATIONS"]) && count($arResult["LOCATIONS"])>0)
		{
			?><br /><?
			?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arResult["LOCATION"]["ID"]?>" <?
				?>id="rsloc_<?=$arResult["LOCATION"]["ID"]?>" checked="checked" <?
				?>onclick="document.getElementById('<?=$arParams["CITY_ID"]?>').value=this.value;" /><?
				?><label for="rsloc_<?=$arResult["LOCATION"]["ID"]?>"><?=$arResult["LOCATION"]["CITY_NAME"]?></label><br /><?
			foreach($arResult["LOCATIONS"] as $arLocation)
			{
				?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arLocation["ID"]?>" <?
					?>id="rsloc_<?=$arLocation["ID"]?>" <?
					?>onclick="document.getElementById('<?=$arParams["CITY_ID"]?>').value=this.value;" /><?
					?><label for="rsloc_<?=$arLocation["ID"]?>"><?=$arLocation["CITY_NAME"]?></label><br /><?
			}
			?><br /><?
		}
		?><div id="rsloc_ajax_locations" style="display:<?if($arResult["AUTO_DETECT"]=="N"):?>none<?else:?>block<?endif;?>;"><?
			$APPLICATION->IncludeComponent(
				"bitrix:sale.ajax.locations",
				"popup",
				array(
					"AJAX_CALL" => "N",
					"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
					"REGION_INPUT_NAME" => "REGION_tmp",
					"CITY_INPUT_NAME" => $arParams["CITY_ID"],
					"CITY_OUT_LOCATION" => "Y",
					"LOCATION_VALUE" => $arResult["LOCATION"]["ID"],
					"ONCITYCHANGE" => "",
				),
				null,
				array("HIDE_ICONS" => "Y")
			);
			?><br /><input type="submit" name="submit" value="<?if($arResult["AUTO_DETECT"]!="Y"):?><?=GetMessage("RSLOC_BTN_CHOSE")?><?else:?><?=GetMessage("RSLOC_BTN_ÍÓÛ")?><?endif;?>" /><?
		?></div><?
	?></form><?

?></div>