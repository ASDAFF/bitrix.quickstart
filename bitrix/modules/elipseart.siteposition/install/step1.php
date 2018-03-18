<?IncludeModuleLangFile(__FILE__);?>
<?
echo CAdminMessage::ShowMessage(GetMessage("MOD_INST_NOT_SUPPORT_DB"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
