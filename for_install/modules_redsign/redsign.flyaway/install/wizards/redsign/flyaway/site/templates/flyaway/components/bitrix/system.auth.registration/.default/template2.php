<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

if(isset($arParams['~AUTH_RESULT']) && is_array($arParams['~AUTH_RESULT']) && $arParams['~AUTH_RESULT']['TYPE']=='ERROR') {
	?><div class="alert alert-danger" role="alert"><?=$arParams['~AUTH_RESULT']['MESSAGE']?></div><?
} else {
	ShowMessage($arParams["~AUTH_RESULT"]);
}

if($arResult['USE_EMAIL_CONFIRMATION']==='Y' && is_array($arParams['AUTH_RESULT']) &&  $arParams['AUTH_RESULT']['TYPE']==='OK')
{
	ShowMessage( Loc::getMessage('AUTH_EMAIL_SENT') );
}

if($arResult['USE_EMAIL_CONFIRMATION']==='Y')
{
	ShowMessage( Loc::getMessage('AUTH_EMAIL_WILL_BE_SENT') );
}

?><form class="form-horizontal" method="post" action="<?=$arResult['AUTH_URL']?>" name="bform"><?

	if(strlen($arResult['BACKURL'])>0) {
		?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>" /><?
	}

	?><input type="hidden" name="AUTH_FORM" value="Y" /><?
	?><input type="hidden" name="TYPE" value="REGISTRATION" /><?

	?><div class="form-group"><?
		?><label for="USER_NAME" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_NAME")?></label><?
		?><div class="col-sm-10"><?
			?><input class="form-control" type="text" name="USER_NAME" id="USER_NAME" maxlength="50" value="<?=$arResult['USER_NAME']?>" /><?
		?></div><?
	?></div><?

	?><div class="form-group"><?
		?><label for="USER_LAST_NAME" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_LAST_NAME")?></label><?
		?><div class="col-sm-10"><?
			?><input class="form-control" type="text" name="USER_LAST_NAME" id="USER_LAST_NAME" maxlength="50" value="<?=$arResult['USER_LAST_NAME']?>" /><?
		?></div><?
	?></div><?

	?><div class="form-group"><?
		?><label for="USER_LOGIN" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_LOGIN_MIN")?> <span class="starrequired">*</span></label><?
		?><div class="col-sm-10"><?
			?><input class="form-control" type="text" name="USER_LOGIN" id="USER_LOGIN" maxlength="50" value="<?=$arResult['USER_LOGIN']?>" /><?
		?></div><?
	?></div><?

	?><div class="form-group"><?
		?><label for="USER_PASSWORD" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_PASSWORD_REQ")?> <span class="starrequired">*</span></label><?
		?><div class="col-sm-10"><?
			?><input class="form-control" type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult['USER_PASSWORD']?>" /><?
		?></div><?
	?></div><?

	if($arResult['SECURE_AUTH'])
	{
		?><div class="form-group"><?
			?><div class="col-sm-offset-2 col-sm-10"><?
				?><noscript><?
				ShowError( Loc::getMessage('AUTH_NONSECURE_NOTE') );
				?></noscript><?
			?></div><?
		?></div><?
	}

	?><div class="form-group"><?
		?><label for="USER_CONFIRM_PASSWORD" class="col-sm-2 control-label"><?=Loc::getMessage("AUTH_CONFIRM")?> <span class="starrequired">*</span></label><?
		?><div class="col-sm-10"><?
			?><input class="form-control" type="password" name="USER_CONFIRM_PASSWORD" id="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>" /><?
		?></div><?
	?></div><?

	?><div class="form-group"><?
		?><label for="USER_EMAIL" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_EMAIL')?><?if($arResult['EMAIL_REQUIRED']):?> <span class="starrequired">*</span><?endif;?></label><?
		?><div class="col-sm-10"><?
			?><input class="form-control" type="text" name="USER_EMAIL" id="USER_EMAIL" maxlength="255" value="<?=$arResult['USER_EMAIL']?>" /><?
		?></div><?
	?></div><?

	// ********************* User properties ***************************************************
	if($arResult['USER_PROPERTIES']['SHOW'] == 'Y') {
		foreach($arResult['USER_PROPERTIES']['DATA'] as $FIELD_NAME => $arUserField) {
			?><div class="form-group"><?
				?><label for="field_<?=$FIELD_NAME?>" class="col-sm-2 control-label"><?=Loc::getMessage('AUTH_EMAIL')?><?if($arResult['EMAIL_REQUIRED']):?>*<?endif;?></label><?
				?><div class="col-sm-10"><?
					?><?$APPLICATION->IncludeComponent(
						'bitrix:system.field.edit',
						$arUserField['USER_TYPE']['USER_TYPE_ID'],
						array(
							'bVarsFromForm' => $arResult['bVarsFromForm'],
							'arUserField' => $arUserField,
							'form_name' => 'bform'
						),
						null,
						array('HIDE_ICONS'=>'Y')
					);?><?
				?></div><?
			?></div><?
			?><script>
			$('.field_<?=$FIELD_NAME?>').find('input,textarea,select').addClass('form-control');
			</script><?
		}
	}
	// ******************** /User properties ***************************************************

	// CAPTCHA
	if($arResult['USE_CAPTCHA']=='Y') {
		?><div class="form-group"><?
			?><div class="col-sm-offset-2 col-sm-10"><?
				?><input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" /><?
				?><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="180" height="40" alt="CAPTCHA" /><?
				?><input class="form-control" type="text" name="captcha_word" maxlength="50" value="" placeholder="<?=Loc::getMessage('CAPTCHA_REGF_PROMT')?>*" /><?
			?></div><?
		?></div><?
	}
	// /CAPTCHA

	?><div class="form-group"><?
		?><div class="col-sm-offset-2 col-sm-10"><?
			?><input class="btn btn-primary" type="submit" name="Register" value="<?=Loc::getMessage('AUTH_REGISTER')?>" /><?
		?></div><?
	?></div><?

	?><div class="form-group"><?
		?><div class="col-sm-offset-2 col-sm-10"><?
			?><div><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS'];?></div><?
			?><div><span class="starrequired">*</span> <?=Loc::getMessage('AUTH_REQ')?></div><?
			?><div><a class="aprimary" href="<?=$arResult['AUTH_AUTH_URL']?>" rel="nofollow"><?=Loc::getMessage('AUTH_AUTH')?></a></div><?
		?></div><?
	?></div><?

?></form><?

?><script type="text/javascript">
document.bform.USER_NAME.focus();
</script>