<?if(!check_bitrix_sessid()) return;?>
<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/step.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/step.php"));

echo CAdminMessage::ShowNote(GetMessage("SUCCESS"));
?>