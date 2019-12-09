<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);

// is auth page
$IS_AUTH = 'Y';
if(strpos($APPLICATION->GetCurPage(true), SITE_DIR.'auth/') === false)
	$IS_AUTH = 'N';

?><div class="pcontent<?if($IS_AUTH=='Y'):?> thisisauthpage<?endif;?>"><?

	?><div class="someform auth clearfix<?if($arResult['SECURE_AUTH']):?> secure<?endif;?>"><?
		
		?><form name="form_auth" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>"><?
			
			?><input type="hidden" name="AUTH_FORM" value="Y" /><?
			?><input type="hidden" name="TYPE" value="AUTH" /><?
			if(strlen($arResult['BACKURL'])>0)
			{
				?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>" /><?
			}
			foreach($arResult['POST'] as $key => $value)
			{
				?><input type="hidden" name="<?=$key?>" value="<?=$value?>" /><?
			}
			
			?><div class="line clearfix"><?
				?><input type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult['LAST_LOGIN']?>" placeholder="<?=GetMessage("AUTH_LOGIN")?>" /><?
			?></div><?
			
			?><div class="line password clearfix"><?
				?><input class="text" type="password" name="USER_PASSWORD" maxlength="255" placeholder="<?=GetMessage("AUTH_PASSWORD")?>" /><?
				?><input class="btn btn1" type="submit" name="Login" value="<?=GetMessage('AUTH_AUTHORIZE')?>" /><?
			?></div><?
			
			if($arResult['SECURE_AUTH'])
			{
				?><div class="line clearfix"><?
					?><noscript><?
					ShowError( GetMessage('AUTH_NONSECURE_NOTE') );
					?></noscript><?
				?></div><?
			}
			
			// CAPTCHA
			if($arResult['CAPTCHA_CODE'])
			{
				?><div class="line captcha clearfix"><?
					?><input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" /><?
					?><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="180" height="40" alt="CAPTCHA" /><?
					?><input type="text" name="captcha_word" maxlength="50" value="" size="15" placeholder="<?=GetMessage('AUTH_CAPTCHA_PROMT')?>" /><?
				?></div><?
			}
			// /CAPTCHA
			
			?><div class="line buttons clearfix"><?
			if($arResult['STORE_PASSWORD']=='Y')
				{
					?><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;<?=GetMessage('AUTH_REMEMBER_ME')?></label><?
				}
				?><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a><?
			?></div><?
			
			?><div class="line forgot clearfix"><?
				?><span><?=GetMessage('GOREG')?></span><?
				?><a class="btn btn3" href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage('AUTH_REGISTER')?></a><?
			?></div><?
			
		?></form><?
		
	?></div><?

	?><script type="text/javascript">
	<?if(strlen($arResult["LAST_LOGIN"])>0):?>
	try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
	<?else:?>
	try{document.form_auth.USER_LOGIN.focus();}catch(e){}
	<?endif?>
	</script><?

	if($arResult["AUTH_SERVICES"])
	{
		?><?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
			array(
				"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
				"CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
				"AUTH_URL" => $arResult["AUTH_URL"],
				"POST" => $arResult["POST"],
				"SHOW_TITLES" => $arResult["FOR_INTRANET"]?'N':'Y',
				"FOR_SPLIT" => $arResult["FOR_INTRANET"]?'Y':'N',
				"AUTH_LINE" => $arResult["FOR_INTRANET"]?'N':'Y',
			),
			$component,
			array("HIDE_ICONS"=>"Y")
		);?><?
}

?></div>