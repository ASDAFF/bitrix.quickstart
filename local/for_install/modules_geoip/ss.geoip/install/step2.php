<?
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

if($ex = $APPLICATION->GetException())
	echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "ERROR",
		"MESSAGE" => GetMessage("MOD_INST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true,
	));
else
	echo CAdminMessage::ShowNote(GetMessage("SS_GEOIP_MOD_INST_OK"));
?>
<!--form action="/bitrix/admin/settings.php" style="display: inline-block;">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="hidden" name="mid" value="ss.geoip">
	<input type="submit" name="" value="<?echo GetMessage("SS_GEOIP_MOD_SETTINGS")?>">
</form-->
<form action="<?=$APPLICATION->GetCurPage()?>" style="display: inline-block; margin-left:10px;">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("SS_GEOIP_MOD_BACK")?>">
</form>