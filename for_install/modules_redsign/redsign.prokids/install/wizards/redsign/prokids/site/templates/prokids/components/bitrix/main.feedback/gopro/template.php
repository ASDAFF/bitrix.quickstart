<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['ERROR_MESSAGE']))
{
	foreach($arResult['ERROR_MESSAGE'] as $v)
		ShowError($v);
}

if(strlen($arResult['OK_MESSAGE'])>0)
{
	ShowMessage(  array('MESSAGE'=>$arResult['OK_MESSAGE'],'TYPE'=>'OK') );
}

?><div class="someform clearfix"><?
	
	?><form action="<?=POST_FORM_ACTION_URI?>" method="POST"><?
		
		?><?=bitrix_sessid_post()?><?
		?><input type="hidden" name="PARAMS_HASH" value="<?=$arResult['PARAMS_HASH']?>"><?
		
		?><div class="line clearfix"><?
			?><input type="text" name="user_name" value="<?=$arResult['AUTHOR_NAME']?>" placeholder="<?=GetMessage('MFT_NAME')?><?if(empty($arParams['REQUIRED_FIELDS']) || in_array('NAME', $arParams['REQUIRED_FIELDS'])):?>*<?endif;?>:" /><?
		?></div><?
		
		?><div class="line clearfix"><?
			?><input type="text" name="user_email" value="<?=$arResult['AUTHOR_EMAIL']?>" placeholder="<?=GetMessage('MFT_EMAIL')?><?if(empty($arParams['REQUIRED_FIELDS']) || in_array('EMAIL', $arParams['REQUIRED_FIELDS'])):?>*<?endif;?>:" /><?
		?></div><?
		
		?><div class="line clearfix"><?
			?><textarea name="MESSAGE" placeholder="<?=GetMessage('MFT_MESSAGE')?><?if(empty($arParams['REQUIRED_FIELDS']) || in_array('MESSAGE', $arParams['REQUIRED_FIELDS'])):?>*<?endif;?>:"><?=$arResult['MESSAGE']?></textarea><?
		?></div><?
		
		// CAPTCHA
		if($arParams['USE_CAPTCHA']=='Y')
		{
			?><div class="line captcha clearfix"><?
				?><input type="hidden" name="captcha_sid" value="<?=$arResult['capCode']?>"><?
				?><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['capCode']?>" width="180" height="40" alt="CAPTCHA"><?
				?><input type="text" name="captcha_word" size="30" maxlength="50" value="" placeholder="<?=GetMessage('MFT_CAPTCHA_CODE')?>*" /><?
			?></div><?
		}
		// /CAPTCHA
		
		?><div class="line buttons clearfix"><?
			?><input class="btn btn1" type="submit" name="submit" value="<?=GetMessage('MFT_SUBMIT')?>"><?
		?></div><?
		
	?></form><?
	
?></div>