<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
?>
<?=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<div class="workarea personal">
	<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
	<?=$arResult["BX_SESSION_CHECK"]?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
	<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
	<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

		<h2><?=GetMessage("LEGEND_PROFILE")?></h2>
		<?=GetMessage('NAME')?><br>
		<input type="text" name="NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["NAME"]?>" /><br><br>

		<?=GetMessage('LAST_NAME')?><br>
		<input type="text" name="LAST_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /><br><br>

		<?=GetMessage('SECOND_NAME')?><br>
		<input type="text" name="SECOND_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /><br><br>

		<h2><?=GetMessage("MAIN_PSWD")?></h2>
		<?=GetMessage('NEW_PASSWORD_REQ')?><br>
		<input type="password" name="NEW_PASSWORD" maxlength="50" class="input_text_style" value="" autocomplete="off" /> <br><br>

		<?=GetMessage('NEW_PASSWORD_CONFIRM')?><br>
		<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" class="input_text_style" value="" autocomplete="off" /> <br><br>

		<input name="save" value="<?=GetMessage("MAIN_SAVE")?>" class="bt3" type="submit">
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
