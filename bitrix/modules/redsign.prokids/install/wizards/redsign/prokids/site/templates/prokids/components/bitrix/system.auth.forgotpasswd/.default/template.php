<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

?><div class="pcontent"><?

	ShowMessage($arParams['~AUTH_RESULT']);

	?><div class="someform forgot clearfix"><?
		
		?><form name="bform" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>"><?
			
			if(strlen($arResult["BACKURL"])>0)
			{
				?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>" /><?
			}
			
			?><input type="hidden" name="AUTH_FORM" value="Y"><?
			?><input type="hidden" name="TYPE" value="SEND_PWD"><?
			
			?><div class="line clearfix"><?
				?><?=GetMessage('AUTH_FORGOT_PASSWORD_1')?><?
			?></div><?
			
			?><div class="line clearfix"><?
				?><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult['LAST_LOGIN']?>" placeholder="<?=GetMessage('AUTH_LOGIN')?>" /><?
			?></div><?
			
			?><div class="line clearfix"><?
				?><?=GetMessage('AUTH_OR')?><?
			?></div><?
			
			?><div class="line clearfix"><?
				?><input type="text" name="USER_EMAIL" maxlength="255" placeholder="<?=GetMessage('AUTH_EMAIL')?>" /><?
			?></div><?
			
			?><div class="line buttons clearfix"><?
				?><input class="btn btn1" type="submit" name="send_account_info" value="<?=GetMessage('AUTH_SEND')?>" /><?
			?></div><?
			
			?><div class="line notes clearfix"><?
				?><div><a href="<?=$arResult['AUTH_AUTH_URL']?>"><?=GetMessage('AUTH_AUTH')?></a></div><?
			?></div><?
			
			?><div class="line clearfix"><?
				
			?></div><?
			
		?></form><?
		
	?></div><?

?></div><?

?><script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>