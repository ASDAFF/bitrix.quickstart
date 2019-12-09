<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="pcontent"><?

	ShowMessage($arParams['~AUTH_RESULT']);

	?><div class="someform changepass clearfix<?if($arResult['SECURE_AUTH']):?> secure<?endif;?>"><?
		
		?><form method="post" action="<?=$arResult['AUTH_FORM']?>" name="bform"><?
			
			if(strlen($arResult["BACKURL"])>0)
			{
				?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>" /><?
			}
			
			?><input type="hidden" name="AUTH_FORM" value="Y"><?
			?><input type="hidden" name="TYPE" value="CHANGE_PWD"><?
			
			?><div class="line clearfix"><?
				?><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult['LAST_LOGIN']?>" placeholder="<?=GetMessage('AUTH_LOGIN')?>*" /><?
			?></div><?
			
			?><div class="line clearfix"><?
				?><input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult['USER_CHECKWORD']?>" placeholder="<?=GetMessage('AUTH_CHECKWORD')?>*" /><?
			?></div><?
			
			?><div class="line password clearfix"><?
				?><input class="text" type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult['USER_PASSWORD']?>" placeholder="<?=GetMessage('AUTH_NEW_PASSWORD_REQ')?>*" /><?
			?></div><?
			
			if($arResult['SECURE_AUTH'])
			{
				?><div class="line clearfix"><?
					?><noscript><?
					ShowError( GetMessage('AUTH_NONSECURE_NOTE') );
					?></noscript><?
				?></div><?
			}
			
			?><div class="line clearfix"><?
				?><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>" placeholder="<?=GetMessage('AUTH_NEW_PASSWORD_CONFIRM')?>*" /><?
			?></div><?
			
			?><div class="line buttons clearfix"><?
				?><input class="btn btn1" type="submit" name="change_pwd" value="<?=GetMessage('AUTH_CHANGE')?>" /><?
			?></div><?
			
			?><div class="line notes clearfix"><?
				?><div><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS'];?></div><?
				?><div>* <?=GetMessage('AUTH_REQ')?></div><?
				?><a href="<?=$arResult['AUTH_AUTH_URL']?>"><?=GetMessage('AUTH_AUTH')?></a><?
			?></div><?
			
		?></form><?
		
	?></div><?

?></div><?

?><script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>