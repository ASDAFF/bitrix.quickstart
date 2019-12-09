<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

ShowError($arResult['strProfileError']);

if($arResult['DATA_SAVED'] == 'Y')
{
	ShowMessage( array('MESSAGE'=>GetMessage('PROFILE_DATA_SAVED'),'TYPE'=>'OK') );
}
	
?><div class="someform clearfix profil<?if($arResult["SECURE_AUTH"]):?> secure<?endif;?>"><?
	
	?><form method="post" name="form1" action="<?=$arResult['FORM_TARGET']?>" enctype="multipart/form-data"><?
		
		?><?=$arResult['BX_SESSION_CHECK']?><?
		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		?><input type="hidden" name="ID" value=<?=$arResult['ID']?> /><?
		
		?><div class="line clearfix"><?
			?><input class="first" type="text" name="LOGIN" maxlength="50" value="<?=$arResult['arUser']['LOGIN']?>" placeholder="<?=GetMessage('LOGIN')?>" /><?
		?></div><?
		
		?><div class="line clearfix"><?
			?><input class="first" type="text" name="NAME" maxlength="50" value="<?=$arResult['arUser']['NAME']?>" placeholder="<?=GetMessage('NAME')?>" /><?
			?><input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult['arUser']['LAST_NAME']?>" placeholder="<?=GetMessage('LAST_NAME')?>" /><?
		?></div><?
		
		?><div class="line clearfix"><?
			?><input class="first" type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult['arUser']['SECOND_NAME']?>" placeholder="<?=GetMessage('SECOND_NAME')?>" /><?
			?><input type="text" name="EMAIL" maxlength="50" value="<?=$arResult['arUser']['EMAIL']?>" placeholder="<?=GetMessage('EMAIL')?>" /><?
		?></div><?
		
		if($arResult['arUser']['EXTERNAL_AUTH_ID']=='')
		{
			?><div class="line password clearfix"><?
				?><input class="text first" type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('NEW_PASSWORD_REQ')?>" /><?
				?><input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('NEW_PASSWORD_CONFIRM')?>" /><?
			?></div><?
		}
		
		if($arResult['SECURE_AUTH'])
		{
			?><div class="line clearfix"><?
				?><noscript><?
				ShowError( GetMessage('AUTH_NONSECURE_NOTE') );
				?></noscript><?
			?></div><?
		}
		
		if($arResult['TIME_ZONE_ENABLED']==true)
		{
			?><div class="title"><?=GetMessage('main_profile_time_zones')?></div><?
			?><div class="line timezone clearfix"><?
				?><select name="AUTO_TIME_ZONE" onchange="this.form.TIME_ZONE.disabled=(this.value != 'N')"><?
					?><option value=""><?=GetMessage('main_profile_time_zones_auto_def')?></option><?
					?><option value="Y"<?=($arResult['arUser']['AUTO_TIME_ZONE']=='Y'? ' SELECTED="SELECTED"' : '')?>><?=GetMessage('main_profile_time_zones_auto_yes')?></option><?
					?><option value="N"<?=($arResult['arUser']['AUTO_TIME_ZONE']=='N'? ' SELECTED="SELECTED"' : '')?>><?=GetMessage('main_profile_time_zones_auto_no')?></option><?
				?></select><?
			?></div><?
			
			?><div class="line clearfix"><?
				?><select name="TIME_ZONE"<?if($arResult['arUser']['AUTO_TIME_ZONE']<>'N') echo ' disabled="disabled"'?>><?
					foreach($arResult['TIME_ZONE_LIST'] as $tz=>$tz_name)
					{
						?><option value="<?=htmlspecialcharsbx($tz)?>"<?=($arResult['arUser']['TIME_ZONE']==$tz ? ' SELECTED="SELECTED"' : '')?>><?=htmlspecialcharsbx($tz_name)?></option><?
					}
				?></select><?
			?></div><?
		}
		
		if($arResult['USER_PROPERTIES']['SHOW']=='Y')
		{
			// ********************* User properties ***************************************************
			foreach ($arResult['USER_PROPERTIES']['DATA'] as $FIELD_NAME => $arUserField)
			{
				?><div class="line clearfix field_<?=$FIELD_NAME?>" title="<?=$arUserField['EDIT_FORM_LABEL']?><?if($arUserField['MANDATORY']=='Y'):?>*<?endif;?>"><?
					?><span class="likeinput" data-type="<?=$arUserField["USER_TYPE"]["USER_TYPE_ID"]?>"><?=$arUserField['EDIT_FORM_LABEL']?><?if($arUserField['MANDATORY']=='Y'):?>*<?endif;?></span><?
					?><?$APPLICATION->IncludeComponent(
						"bitrix:system.field.edit",
						$arUserField["USER_TYPE"]["USER_TYPE_ID"],
						array(
							"bVarsFromForm" => $arResult["bVarsFromForm"],
							"arUserField" => $arUserField
						),
						null,
						array("HIDE_ICONS"=>"Y")
					);?><?
					?><script>
					$('.field_<?=$FIELD_NAME?>').find('input,textarea,select').attr('placeholder','<?=CUtil::JSEscape($arUserField['EDIT_FORM_LABEL'])?>:<?if($arUserField['MANDATORY']=='Y'):?>*<?endif;?>');
					</script><?
				?></div><?
			}
			// ********************* /User properties ***************************************************
		}
		
		?><div class="line clearfix"><?
		
		?></div><?
		
		?><div class="line clearfix"><?
		
		?></div><?
		
		?><div class="line clearfix"><?
		
		?></div><?
		
		?><div class="line buttons clearfix"><?
			?><input class="btn btn1 nonep" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>" /><?
			?><a class="btn btn1 submit" href="#" onclick=""><?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?></a><?
		?></div><?
		
	?></form><?
	
?></div>