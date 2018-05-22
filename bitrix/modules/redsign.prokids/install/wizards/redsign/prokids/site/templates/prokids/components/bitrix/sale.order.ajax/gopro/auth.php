<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><script>
<!--
function ChangeGenerate(val)
{
	if(val)
	{
		document.getElementById("sof_choose_login").style.display='none';
	}
	else
	{
		document.getElementById("sof_choose_login").style.display='block';
		document.getElementById("NEW_GENERATE_N").checked = true;
	}

	try{document.order_reg_form.NEW_LOGIN.focus();}catch(e){}
}
//-->
</script><?


?><div class="ordertable t1"><?

	?><div class="someform orderauth clearfix"><?
		
		if($arResult['AUTH']['new_user_registration']=='Y')
		{
			?><div class="title"><h3><?=GetMessage('STOF_2REG')?></h3></div><?
		}
		
		?><form method="post" action="" name="order_auth_form"><?
			
			?><?=bitrix_sessid_post()?><?
			foreach($arResult['POST'] as $key => $value)
			{
				?><input type="hidden" name="<?=$key?>" value="<?=$value?>" /><?
			}
			?><input type="hidden" name="do_authorize" value="Y"><?
			
			?><div class="line clearfix"><?
				?><input type="text" name="USER_LOGIN" maxlength="30" size="30" value="<?=$arResult['AUTH']['USER_LOGIN']?>" placeholder="<?=GetMessage('STOF_LOGIN')?>*" /><?
			?></div><?
			
			?><div class="line password clearfix"><?
				?><input class="text" type="password" name="USER_PASSWORD" maxlength="30" size="30" placeholder="<?=GetMessage('STOF_PASSWORD')?>*" /><?
			?></div><?
			
			?><div class="line buttons clearfix"><?
				?><input class="btn btn1" type="submit" value="<?=GetMessage('STOF_NEXT_STEP')?>"><?
			?></div><?
			
			?><div class="line notes clearfix"><?
				?><div><?=GetMessage('STOF_LOGIN_PROMT')?></div><?
			?></div><?
			
		?></form><?
		
	?></div><?
	
?></div><?

if($arResult['AUTH']['new_user_registration']=='Y')
{
	?><div class="ordertable t2"><?
	
		?><div class="someform orderregister clearfix"><?
			
			?><div class="title"><h3><?=GetMessage('STOF_2NEW')?></h3></div><?
			
			?><form method="post" action="" name="order_reg_form"><?
				
				?><?=bitrix_sessid_post()?><?
				foreach($arResult['POST'] as $key => $value)
				{
					?><input type="hidden" name="<?=$key?>" value="<?=$value?>" /><?
				}
				
				?><div class="line clearfix"><?
					?><input type="text" name="NEW_NAME" value="<?=$arResult['AUTH']['NEW_NAME']?>" placeholder="<?=GetMessage('STOF_NAME')?>*" /><?
				?></div><?
				
				?><div class="line clearfix"><?
					?><input type="text" name="NEW_LAST_NAME" value="<?=$arResult['AUTH']['NEW_LAST_NAME']?>" placeholder="<?=GetMessage('STOF_LASTNAME')?>*" /><?
				?></div><?
				
				?><div class="line clearfix"><?
					?><input type="text" name="NEW_EMAIL" value="<?=$arResult['AUTH']['NEW_EMAIL']?>" placeholder="E-Mail*" /><?
				?></div><?
				
				if($arResult['AUTH']['new_user_registration_email_confirmation']!='Y')
				{
					?><div class="line clearfix"><?
						?><input type="radio" id="NEW_GENERATE_Y" name="NEW_GENERATE" value="Y" OnClick="ChangeGenerate(true)"<?if($POST['NEW_GENERATE']!='N') echo ' checked';?>> <?
						?><label for="NEW_GENERATE_Y"><?=GetMessage('STOF_SYS_PASSWORD')?></label><?
					?></div><?
				}
				
				if($arResult['AUTH']['new_user_registration_email_confirmation']!='Y')
				{
					?><div class="line clearfix"><?
						?><input type="radio" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N" OnClick="ChangeGenerate(false)"<?if($_POST['NEW_GENERATE']=='N') echo ' checked';?>> <?
						?><label for="NEW_GENERATE_N"><?=GetMessage('STOF_MY_PASSWORD')?></label><?
					?></div><?
				}
				
				if($arResult['AUTH']['new_user_registration_email_confirmation']!='Y')
				{
					?><div id="sof_choose_login"><?
				}
				
				?><div class="line clearfix"><?
					?><input type="text" name="NEW_LOGIN" value="<?=$arResult['AUTH']['NEW_LOGIN']?>" placeholder="<?=GetMessage('STOF_LOGIN')?>*" /><?
				?></div><?
				
				?><div class="line clearfix"><?
					?><input type="password" name="NEW_PASSWORD" placeholder="<?=GetMessage('STOF_PASSWORD')?>*" /><?
				?></div><?
				
				?><div class="line clearfix"><?
					?><input type="password" name="NEW_PASSWORD_CONFIRM" placeholder="<?=GetMessage('STOF_RE_PASSWORD')?>" /><?
				?></div><?
				
				if($arResult['AUTH']['new_user_registration_email_confirmation']!='Y')
				{
					?></div><?
					?><script language="JavaScript">
					<!--
					ChangeGenerate(<?=(($_POST['NEW_GENERATE']!='N')?'true':'false')?>);
					//-->
					</script><?
				}
				
				// CAPTHCA
				if($arResult['AUTH']['captcha_registration']=='Y')
				{
					?><div class="line captcha clearfix"><?
						?><input type="hidden" name="captcha_sid" value="<?=$arResult['AUTH']['capCode']?>" /><?
						?><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['AUTH']['capCode']?>" width="180" height="40" alt="CAPTCHA" /><?
						?><input class="text" type="text" name="captcha_word" maxlength="50" value="" placeholder="<?=GetMessage('CAPTCHA_REGF_PROMT')?>*" /><?
					?></div><?
				}
				// /CAPTHCA
				
				?><div class="line buttons clearfix"><?
					?><input class="btn btn1" type="submit" value="<?=GetMessage('STOF_NEXT_STEP')?>"><?
					?><input type="hidden" name="do_register" value="Y"><?
				?></div><?
				
				?><div class="line notes clearfix"><?
					?><div><?=GetMessage('STOF_EMAIL_NOTE')?></div><?
				?></div><?
				
			?></form><?
			
		?></div><?
		
	?></div><?
}