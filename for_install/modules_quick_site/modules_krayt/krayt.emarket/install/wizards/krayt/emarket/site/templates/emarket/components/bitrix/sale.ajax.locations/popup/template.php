<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$APPLICATION->AddHeadScript("/bitrix/js/main/cphttprequest.js");

if ($arParams["AJAX_CALL"] != "Y"
	&& count($arParams["LOC_DEFAULT"]) > 0
	&& $arParams["PUBLIC"] != "N"
	&& $arParams["SHOW_QUICK_CHOOSE"] == "Y"):

	$isChecked = "";
	foreach ($arParams["LOC_DEFAULT"] as $val):
		$checked = "";
		if ((($val["ID"] == IntVal($_REQUEST["NEW_LOCATION_".$arParams["ORDER_PROPS_ID"]])) || ($val["ID"] == $arParams["CITY"])) && (!isset($_REQUEST["CHANGE_ZIP"]) || $_REQUEST["CHANGE_ZIP"] != "Y"))
		{
			$checked = "checked";
			$isChecked = "Y";
		}?>

		<div><input onChange="<?=$arParams["ONCITYCHANGE"]?>;" <?=$checked?> type="radio" name="NEW_LOCATION_<?=$arParams["ORDER_PROPS_ID"]?>" value="<?=$val["ID"]?>" id="loc_<?=$val["ID"]?>" /><label for="loc_<?=$val["ID"]?>"><?=$val["LOC_DEFAULT_NAME"]?></label></div>
	<?endforeach;?>
	<div><input <? if($isChecked!="Y") echo 'checked';?> type="radio" onclick="clearLocInput();" name="NEW_LOCATION_<?=$arParams["ORDER_PROPS_ID"]?>" value="0" id="loc_0" /><label for="loc_0"><?=GetMessage("LOC_DEFAULT_NAME_NULL")?>:</label></div>
<?endif;?>

<input
	size="<?=$arParams["SIZE1"]?>"
	name="<?echo $arParams["CITY_INPUT_NAME"]?>_val"
	id="<?echo $arParams["CITY_INPUT_NAME"]?>_val"
	value="<?=$arResult["LOCATION_STRING"]?>"
	class="search-suggest" type="text"
	autocomplete="off"
	onfocus="loc_sug_CheckThis(this, this.id);"
	<?=($arResult["SINGLE_CITY"] == "Y" ? " disabled" : "")?>/>
<input type="hidden" name="<?echo $arParams["CITY_INPUT_NAME"]?>" id="<?echo $arParams["CITY_INPUT_NAME"]?>" value="<?=$arParams["LOCATION_VALUE"]?>">
<script type="text/javascript">

	if (typeof oObject != "object")
		window.oObject = {};

	document.loc_sug_CheckThis = function(oObj, id)
	{
		try
		{
			if(SuggestLoadedSale)
			{
				window.oObject[oObj.id] = new JsSuggestSale(oObj, '<?echo $arResult["ADDITIONAL_VALUES"]?>', '', '', '<?=CUtil::JSEscape($arParams["ONCITYCHANGE"])?>');
				return;
			}
			else
			{
				setTimeout(loc_sug_CheckThis(oObj, id), 10);
			}
		}
		catch(e)
		{
			setTimeout(loc_sug_CheckThis(oObj, id), 10);
		}
	}
	
	clearLocInput = function()
	{				
		var inp = BX("<?echo $arParams["CITY_INPUT_NAME"]?>_val");			
		if(inp)
		{
			inp.value = "";
			inp.focus();
		}
	}	
</script>
