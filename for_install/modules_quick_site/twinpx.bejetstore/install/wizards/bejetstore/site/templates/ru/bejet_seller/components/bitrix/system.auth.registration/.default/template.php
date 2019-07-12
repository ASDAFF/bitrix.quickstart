<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="bx_registration_page">
	<div class="bx-auth">
<?
ShowMessage($arParams["~AUTH_RESULT"]);

if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK")
{
?>
	<p><?echo GetMessage("AUTH_EMAIL_SENT")?></p>
<?
}
else
{
?>
	<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
		<p><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></p>
	<?endif?>

	<noindex>
	<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">

		<?if (strlen($arResult["BACKURL"]) > 0):?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="REGISTRATION" />

		<fieldset>
			<legend class="hidden"><?=GetMessage("AUTH_REGISTER")?></legend>
			<h2><?=GetMessage("AUTH_REGISTER")?></h2>
			
			<div class="form-group">
				<label><?=GetMessage("AUTH_NAME")?></label>
				<input type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" class="bx-auth-input form-control">
			</div>
			
			<div class="form-group">
				<label><?=GetMessage("AUTH_LAST_NAME")?></label>
				<input type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" class="bx-auth-input form-control">
			</div>
			
			<div class="form-group">
				<label><span class="starrequired">*</span><?=GetMessage("AUTH_LOGIN_MIN")?></label>
				<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" class="bx-auth-input form-control">
			</div>
			
			<div class="form-group">
				<label><span class="starrequired">*</span><?=GetMessage("AUTH_PASSWORD_REQ")?></label>
				<input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input form-control">
			</div>
			
			<div class="form-group">
				<label><span class="starrequired">*</span><?=GetMessage("AUTH_CONFIRM")?></label>
				<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input form-control">
			</div>
			
			<div class="form-group">
				<label><span class="starrequired">*</span><?=GetMessage("AUTH_EMAIL")?></label>
				<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" class="bx-auth-input form-control">
			</div>

			<!-- User properties -->
			<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
				<?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?>
				<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
				<div class="form-group">
				<label><?if ($arUserField["MANDATORY"]=="Y"):?><span class="required">*</span><?endif;?>
					<?=$arUserField["EDIT_FORM_LABEL"]?>:</label>
					<?$APPLICATION->IncludeComponent(
						"bitrix:system.field.edit",
						$arUserField["USER_TYPE"]["USER_TYPE_ID"],
						array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "bform"), null, array("HIDE_ICONS"=>"Y"));?>
				</div>
				<?endforeach;?>
			<?endif;?>
			<!-- /User properties -->

		</fieldset>

		<?if ($arResult["USE_CAPTCHA"] == "Y"):?>
		<!-- CAPTCHA -->
		<fieldset>
			<legend class="hidden"><?=GetMessage("CAPTCHA_REGF_TITLE")?></legend>
			<h2><?=GetMessage("CAPTCHA_REGF_TITLE")?></h2>
			
			<div class="form-group">
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>">
   				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA">
			</div>
			
			<div class="form-group">
				<label><span class="starrequired">*</span><?=GetMessage("CAPTCHA_REGF_PROMT")?></label>
				<input type="text" name="captcha_word" maxlength="50" value="" class="form-control">
			</div>			
			
		</fieldset>
		<!-- /CAPTCHA -->
		<?endif?>

		<div class="form-group">
			<input type="submit" name="Register" value="<?=GetMessage("AUTH_REGISTER")?>" class="btn btn-default">
		</div>

		<hr>
		<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?><br />
		<span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>

		<p><a href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><b><?=GetMessage("AUTH_AUTH")?></b></a></p>

	</form>
	</noindex>
<?
}
?>
	</div>
</div>

<script type="text/javascript">
	document.bform.USER_NAME.focus();
</script>