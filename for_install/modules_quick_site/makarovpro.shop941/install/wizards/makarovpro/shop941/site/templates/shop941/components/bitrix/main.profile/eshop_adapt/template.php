<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
?>
<?=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<div class="bx_profile">
	<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
	<?=$arResult["BX_SESSION_CHECK"]?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
	<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
	<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

		<h2><?=GetMessage("LEGEND_PROFILE")?></h2>
		<strong><?=GetMessage('NAME')?></strong><br/>
		<input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" /><br><br>

		<strong><?=GetMessage('LAST_NAME')?></strong><br/>
		<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /><br><br>

		<strong><?=GetMessage('SECOND_NAME')?></strong><br/>
		<input type="text" name="SECOND_NAME" maxlength="50"  value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /><br><br>

		<h2><?=GetMessage("MAIN_PSWD")?></h2>
		<strong><?=GetMessage('NEW_PASSWORD_REQ')?></strong><br/>
		<input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" /> <br><br>

		<strong><?=GetMessage('NEW_PASSWORD_CONFIRM')?></strong><br/>
		<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /> <br><br>

		<input name="save" value="<?=GetMessage("MAIN_SAVE")?>" class="bt_blue big shadow" type="submit">
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
