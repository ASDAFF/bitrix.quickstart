<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script src="/bitrix/js/main/cphttprequest.js"></script>
<script type="text/javascript">

if (typeof oObject != "object")
	window.oObject = {};

document.loc_sug_CheckThis = function(oObj, id)
{
	try
	{
		if(SuggestLoaded)
		{
			window.oObject[oObj.id] = new JsSuggest(oObj, '<?echo $arResult["ADDITIONAL_VALUES"]?>', '', '');
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
</script>

<input class="input_text_style" size="<?=$arParams["SIZE1"]?>" name="<?echo $arParams["CITY_INPUT_NAME"]?>_val" id="<?echo $arParams["CITY_INPUT_NAME"]?>_val" value="<?=$arResult["LOCATION_STRING"]?>" class="search-suggest" type="text" autocomplete="off" onfocus="loc_sug_CheckThis(this, this.id);" />
<input type="hidden" name="<?echo $arParams["CITY_INPUT_NAME"]?>" id="<?echo $arParams["CITY_INPUT_NAME"]?>" value="<?=$arResult["LOCATION_DEFAULT"]?>">