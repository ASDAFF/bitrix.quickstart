<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
?>
<?=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<div class="page_wrapper">
	<div class="auth_form forgot_pass_left ">
		<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
		<?=$arResult["BX_SESSION_CHECK"]?>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
		<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
		<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

			<h4 class="auth_form_title"><?=GetMessage("LEGEND_PROFILE")?></h4>
			<label for="NAME"><?=GetMessage('NAME')?></label><br>
			<input type="text" name="NAME" id="NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["NAME"]?>" /><br>

			<label for="LAST_NAME"><?=GetMessage('LAST_NAME')?></label><br>
			<input type="text" name="LAST_NAME" id="LAST_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /><br>

			<label for="SECOND_NAME"><?=GetMessage('SECOND_NAME')?></label><br>
			<input type="text" name="SECOND_NAME" id="SECOND_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /><br>

			<h4 class="auth_form_title"><?=GetMessage("MAIN_PSWD")?></h4>
			<label for="NEW_PASSWORD"><?=GetMessage('NEW_PASSWORD_REQ')?></label><br>
			<input type="password" name="NEW_PASSWORD" id="NEW_PASSWORD" maxlength="50" class="input_text_style" value="" autocomplete="off" /> <br>

			<label for="NEW_PASSWORD_CONFIRM"><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label><br>
			<input type="password" name="NEW_PASSWORD_CONFIRM" id="NEW_PASSWORD_CONFIRM" maxlength="50" class="input_text_style" value="" autocomplete="off" /> <br>

			<input name="save" value="<?=GetMessage("MAIN_SAVE")?>" class="login_button" type="submit">
		</form>
	</div>
<br>
<?
if($arResult["SOCSERV_ENABLED"])
{
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
			"SHOW_PROFILES" => "Y",
			"ALLOW_DELETE" => "Y"
		),
		false
	);
}
?>
	<div class="splitter"></div>
</div>
