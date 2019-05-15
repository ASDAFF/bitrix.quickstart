<?
global $APPLICATION;
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="step2use.redirects">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>