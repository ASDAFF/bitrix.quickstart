<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arParams["AJAX_CALL"] != "Y"):?><div id="LOCATION_<?=$arParams["CITY_INPUT_NAME"];?>" style="margin:5px 0"><?endif?>
<select class="page-limit-select" name="<?=$arParams["COUNTRY_INPUT_NAME"]?>" onChange="loadCitiesList(this.value, <?=htmlspecialchars($arResult["JS_PARAMS"])?>, '<?=SITE_TEMPLATE_PATH?>')">
<?if (count($arResult["COUNTRY_LIST"]) > 0):?>
	<option><?echo GetMessage('SAL_CHOOSE_COUNTRY')?></option>
	<?foreach ($arResult["COUNTRY_LIST"] as $arCountry):?>
	<option value="<?=$arCountry["ID"]?>"<?if ($arCountry["ID"] == $arParams["COUNTRY"]):?> selected="selected"<?endif;?>><?=$arCountry["NAME_LANG"]?></option>
	<?endforeach;?>
<?endif;?>
</select>
<?if (count($arResult["CITY_LIST"]) > 0):?>
<select class="page-limit-select" name="<?=$arParams["CITY_INPUT_NAME"]?>"<?if (strlen($arParams["ONCITYCHANGE"]) > 0):?> onchange="<?=$arParams["ONCITYCHANGE"]?>"<?endif;?>>
	<option><?echo GetMessage('SAL_CHOOSE_CITY')?></option>
	<?foreach ($arResult["CITY_LIST"] as $arCity):?>
	<option value="<?=$arCity["ID"]?>"<?if ($arCity["ID"] == $arParams["CITY"]):?> selected="selected"<?endif;?>><?=($arCity['CITY_ID'] > 0 ? $arCity["CITY_NAME"] : GetMessage('SAL_CHOOSE_CITY_OTHER'))?></option>
	<?endforeach;?>
</select>
<?endif;?>
<?if ($arParams["AJAX_CALL"] != "Y"):?></div><div id="wait_container_<?=$arParams["CITY_INPUT_NAME"]?>" style="display: none;"></div><?endif;?>