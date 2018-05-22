<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if($arResult['LAST_ERROR']!='') {
	ShowError( $arResult["LAST_ERROR"] );
}

if($arResult['GOOD_SEND']=='Y') {
	ShowMessage( array('MESSAGE'=>$arParams['ALFA_MESSAGE_AGREE'],'TYPE'=>'OK') );
}

?><div class="someform clearfix"><?
	
	?><form action="<?=$arResult['ACTION_URL']?>" method="POST"><?
		
		?><?=bitrix_sessid_post()?><?
		?><input type="hidden" name="<?=$arParams['REQUEST_PARAM_NAME']?>" value="Y" /><?
		?><input type="hidden" name="PARAMS_HASH" value="<?=$arResult['PARAMS_HASH']?>"><?
		
		foreach($arResult['FIELDS'] as $arField) {
			if($arField['SHOW']=='Y' && $arField['CONTROL_NAME']!='RS_COMPANY_NAME') {
				if($arField['CONTROL_NAME']!='RS_AUTHOR_COMMENT') {
					?><div class="line clearfix"><?
					?><input<?if($arField['CONTROL_NAME']=='RS_AUTHOR_PHONE'):?> class="maskPhone"<?endif;?> type="text" name="<?=$arField['CONTROL_NAME']?>" value="<?=$arField['HTML_VALUE']?>" placeholder="<?=GetMessage('MSG_'.$arField['CONTROL_NAME'])?><?if(in_array($arField['CONTROL_NAME'], $arParams['REQUIRED_FIELDS'])):?>*<?endif;?>:" /><?
					?></div><?
				} else {
					?><textarea class="noned" name="<?=$arField['CONTROL_NAME']?>" placeholder="<?=GetMessage('MSG_'.$arField['CONTROL_NAME'])?><?if(in_array($arField['CONTROL_NAME'], $arParams['REQUIRED_FIELDS'])):?>*<?endif;?>:"><?=$arField['HTML_VALUE']?></textarea><?
				}
			}
		}
		
		foreach($arResult['FIELDS'] as $arField) {
			if($arField['SHOW']=='Y') {
				?><div class="line clearfix"><?
					if($arField['CONTROL_NAME']=='RS_COMPANY_NAME') {
						?><input type="text" name="<?=$arField['CONTROL_NAME']?>" value="<?=$arField['HTML_VALUE']?>" placeholder="<?=GetMessage('MSG_'.$arField['CONTROL_NAME'])?><?if(in_array($arField['CONTROL_NAME'], $arParams['REQUIRED_FIELDS'])):?>*<?endif;?>:" /><?
						?><div class="note" style="color:#666;margin-bottom:10px;font-size:12px;"><?=GetMessage('MSG_RS_COMPANY_NAME_NOTE')?></div><?
					}
				?></div><?
			}
		}
		
		// CAPTCHA
		if($arParams['ALFA_USE_CAPTCHA']=='Y') {
			?><div class="line captcha clearfix"><?
				?><input type="hidden" name="captcha_sid" value="<?=$arResult['CATPCHA_CODE']?>"><?
				?><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CATPCHA_CODE']?>" width="180" height="40" alt="CAPTCHA"><?
				?><input type="text" name="captcha_word" size="30" maxlength="50" value="" placeholder="<?=GetMessage('MSG_CAPTHA')?>*" /><?
			?></div><?
		}
		// /CAPTCHA
		
		?><div class="line buttons clearfix"><?
			?><input class="btn btn1" type="submit" name="submit" value="<?=GetMessage('MSG_SUBMIT')?>" onclick="return RSGoPro_CheaperBeforeSend();" /><?
		?></div><?
		
	?></form><?
	
?></div><?

?><script>
function RSGoPro_CheaperBeforeSend()
{
	var value = $('.fancybox-inner').find('textarea[name="RS_AUTHOR_COMMENT"]').html();
	value = value + '\n' +
		'<?=GetMessage('MSG_RS_COMPANY_NAME')?> - ' + $('.fancybox-inner').find('input[name="RS_COMPANY_NAME"]').val();
	$('.fancybox-inner').find('input[name="RS_COMPANY_NAME"]').val( '' );
	$('.fancybox-inner').find('textarea[name="RS_AUTHOR_COMMENT"]').text( value );
	return true;
}
</script>