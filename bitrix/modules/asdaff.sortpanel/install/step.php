<?
use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
	return;

if ($ex = $APPLICATION->GetException())
	echo CAdminMessage::ShowMessage(array(
		"TYPE"    => "ERROR",
		"MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML"    => true,
	));
else{
	echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}
?>
<form action="<? echo $APPLICATION->GetCurPage(); ?>">
	<input type="hidden" name="lang" value="<? echo LANGUAGE_ID ?>">
	<input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
</form>