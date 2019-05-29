<?if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);?>
<?
echo CAdminMessage::ShowMessage(GetMessage("MOD_INST_ERROR"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
<form>
