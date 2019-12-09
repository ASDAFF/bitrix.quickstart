<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><link href="<?=$templateFolder?>/style.css" type="text/css" rel="stylesheet" /><?
?><script type="text/javascript" src="<?=$templateFolder?>/scrpt.js"></script><?
?><script type="text/javascript" src="/bitrix/js/main/cphttprequest.js"></script><?


?><input <?
	?>size="<?=$arParams['SIZE1']?>" <?
	?>name="<?=$arParams['CITY_INPUT_NAME']?>_val" <?
	?>id="<?=$arParams['CITY_INPUT_NAME']?>_val" <?
	?>value="<?=$arResult['LOCATION_STRING']?>" <?
	?>class="search-suggest mysityinp" type="text" <?
	?>autocomplete="off" <?
	?>onfocus="loc_sug_CheckThis(this, this.id);" <?
	?><?=($arResult['SINGLE_CITY']=='Y'?' disabled':'')?> <?
	?> data-old="<?=$arResult['LOCATION_STRING']?>" /> <?
?><input type="hidden" name="<?=$arParams['CITY_INPUT_NAME']?>" id="<?=$arParams['CITY_INPUT_NAME']?>" value="<?=$arParams['LOCATION_VALUE']?>"><?
?><script type="text/javascript">
	if (typeof oObject != "object")
		window.oObject = {};

	document.loc_sug_CheckThis = function(oObj, id)
	{
		try
		{
			if(SuggestLoadedSale)
			{
				window.oObject[oObj.id] = new JsSuggestSale(oObj, '<?=$arResult["ADDITIONAL_VALUES"]?>', '', '', '<?=CUtil::JSEscape($arParams['ONCITYCHANGE'])?>');
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
		var inp = BX("<?=$arParams["CITY_INPUT_NAME"]?>_val");			
		if(inp)
		{
			inp.value = "";
			inp.focus();
		}
	}	
</script>