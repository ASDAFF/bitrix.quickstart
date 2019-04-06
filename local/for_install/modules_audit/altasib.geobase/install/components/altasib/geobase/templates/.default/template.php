<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->setFrameMode(true);
$frame = $this->createFrame()->begin("");

if(!empty($arParams["SOURCE"]))
{
	if($arParams["SOURCE"] == 'autodetect'){
		if(!empty($arResult["CITY_NAME"])){
		?><span class="altasib_geobase_notetext"><span class="altasib_geobase_notetext_city_name"><?if($arResult["CITY_NAME"] != $arResult["REGION_NAME"]) echo $arResult["CITY_NAME"].(!empty($arResult["REGION_NAME"]) ? ", " : "")?></span><span class="altasib_geobase_notetext_region_name"><?echo $arResult["REGION_NAME"]?></span></span><?
		}
	}
	elseif($arParams["SOURCE"] == 'kladr_auto' || $arParams["SOURCE"] == 'kladr_set'){
		if(!empty($arResult["CITY"]["NAME"])){
		?><span class="altasib_geobase_notetext"><?
		if(isset($arResult["CITY"]["NAME"])){
			?><span class="altasib_geobase_notetext_city_name"><?=$arResult["CITY"]["NAME"];?></span><?
			if($arResult["REGION_DISABLE"] != "Y"){
				?><span class="altasib_geobase_notetext_region_name"><?
					echo ", ".$arResult["REGION"]["FULL_NAME"].((isset($arResult["DISTRICT"]["NAME"]) && $arResult["DISTRICT"]["NAME"] != '') ? ', '.$arResult['DISTRICT']['NAME'].' '.$arResult['DISTRICT']['SOCR'].'.' : '');
				?></span><?
			}?><span class="altasib_geobase_notetext_city_name"></span><?
		}
		?></span><?
		} else if(!empty($arResult["COUNTRY"])){
			?><span class="altasib_geobase_notetext"><?
		if(isset($arResult["CITY"])){
			?><span class="altasib_geobase_notetext_city_name"><?=$arResult["CITY"];?></span><?
			if($arResult["REGION_DISABLE"] != "Y"){
				?><span class="altasib_geobase_notetext_region_name"><?
					echo (!empty($arResult["REGION"]) ? ", ".$arResult["REGION"] : "").(!empty($arResult["COUNTRY"]) ? ", ".$arResult["COUNTRY"] : "");
				?></span><?
			}?><span class="altasib_geobase_notetext_city_name"></span><?
		}
		?></span><?
		}
	}
}
else{
	if($arResult["REGION_NAME"] && $arResult["CITY_NAME"]){
	?><span class="altasib_geobase_notetext"><span class="altasib_geobase_notetext_city_name"><?if($arResult["CITY_NAME"] != $arResult["REGION_NAME"]) echo $arResult["CITY_NAME"].(!empty($arResult["REGION_NAME"]) ? ", " : "")?></span><span class="altasib_geobase_notetext_region_name"><?echo $arResult["REGION_NAME"]?></span></span><?
	}
}

$frame->end(); ?>