<?if(!check_bitrix_sessid()) return;?>
<?
$path = str_replace("\\", "/", __FILE__);
$path = substr($path, 0, strlen($path) - strlen("/install/unstep.php"));
@include(GetLangFileName($path."/lang/", "/install/unstep.php"));
IncludeModuleLangFile($path."/install/unstep.php");

echo CAdminMessage::ShowNote(GetMessage("SUCCESS"));
?>