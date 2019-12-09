<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?><link href="<?=$templateFolder?>/style.css" type="text/css" rel="stylesheet" /><?

?><div class="locationbig"><?

	ShowMessage($arResult['ERROR_MESSAGE']);
	
	?><form id="locforma" class="forma" action="<?=$arResult["ACTION_URL"]?>" method="POST"><?
		
		echo bitrix_sessid_post();
		?><input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" /><?
		?><input type="hidden" name="PARAMS_HASH" value="<?=$arParams["PARAMS_HASH"]?>" /><?
		
		?><div class="title"><?=GetMessage("RSLOC_CHOOSE_FROM_LIST")?></div><?
		
		if(is_array($arResult["LOCATIONS"]) && count($arResult["LOCATIONS"])>0)
		{
			?><div class="items clearfix"><?
				?><div class="item"><?
					?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arResult["LOCATION"]["ID"]?>" <?
						?>id="rsloc_<?=$arResult["LOCATION"]["ID"]?>" checked="checked" <?
						?>onclick="document.getElementById('<?=$arParams["CITY_ID"]?>').value=this.value;$('#locforma .clickforsubmit').trigger('click');" /><?
						?><label for="rsloc_<?=$arResult["LOCATION"]["ID"]?>"><?=$arResult["LOCATION"]["CITY_NAME"]?></label><?
				?></div><?
				foreach($arResult["LOCATIONS"] as $arLocation)
				{
					?><div class="item"><?
						?><input type="radio" name="<?=$arParams["CITY_ID"]?>" value="<?=$arLocation["ID"]?>" <?
							?>id="rsloc_<?=$arLocation["ID"]?>" <?
							?>onclick="document.getElementById('<?=$arParams["CITY_ID"]?>').value=this.value;$('#locforma .clickforsubmit').trigger('click');" /><?
							?><label for="rsloc_<?=$arLocation["ID"]?>"><?=$arLocation["CITY_NAME"]?></label><?
					?></div><?
				}
			?></div><?
		}
		?><div class="ajaxlocation"><?
			?><div class="line"></div><?
			?><div class="title"><?=GetMessage('RSLOC_OR_ENTER')?></div><?
			?><div class="cominput"><?
				$APPLICATION->IncludeComponent(
					'bitrix:sale.ajax.locations',
					'mycity',
					array(
						'AJAX_CALL' => 'N',
						'COUNTRY_INPUT_NAME' => 'COUNTRY_tmp',
						'REGION_INPUT_NAME' => 'REGION_tmp',
						'CITY_INPUT_NAME' => $arParams['CITY_ID'],
						'CITY_OUT_LOCATION' => 'Y',
						'LOCATION_VALUE' => $arResult['LOCATION']['ID'],
						'ONCITYCHANGE' => '',
					),
					null,
					array('HIDE_ICONS' => 'Y')
				);
			?></div><?
		?><input class="clickforsubmit btn btn1" type="submit" name="submit" value="<?=GetMessage('RSLOC_BTN_YES')?>" /><?
	?></div><?
		
	?></form><?

?></div><?
?><script>
$(document).ready(function(){
	setTimeout(function(){
	if( $('.fancybox-inner').find('.locationbig').length>0 )
	{
		$('.fancybox-inner').css({overflow:'visible'});
		$('.fancybox-inner').find('.locationbig').find('.forma').attr('action', window.location.href );
	}
	},50);
});
</script>