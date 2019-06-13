<?
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

if(SM_VERSION * 1 < 14.0)
{
	echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "ERROR",
		"MESSAGE" => GetMessage("SS_GEOIP_OPT_ENGINE_OLD_VERSION"),
		"DETAILS" => GetMessage("SS_GEOIP_OPT_ENGINE_OLD_VERSION_TEXT", array("#LANG_ID#" => LANG)),
		"HTML" => true,
	));
	?>
	<form action="<?echo $APPLICATION->GetCurPage()?>">
		<input type="hidden" name="lang" value="<?=LANG?>">
		<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
	<form>
	<?
}
else
{
	?>
	<form action="<?=$APPLICATION->GetCurPage()?>">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="lang" value="<?=LANG?>">
		<input type="hidden" name="id" value="ss.geoip">
		<input type="hidden" name="install" value="Y">
		<input type="hidden" name="step" value="2">
		<?
		echo CAdminMessage::ShowMessage(Array(
			"TYPE" => "OK",
			"MESSAGE" => GetMessage("SS_GEOIP_OPT_ENGINE_VERSION_OK", array("#VERSION#" => SM_VERSION)),
			"DETAILS" => GetMessage("SS_GEOIP_OPT_ENGINE_VERSION_TEXT"),
			"HTML" => true,
		));
		?>
		<input type="submit" name="inst" value="<?=GetMessage("SS_GEOIP_OPT_INSTAL_BUTTON")?>">
	</form>
	<?
}
?>