<?$moduleId = 'fairytale.tpic';?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="<?=$moduleId?>">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	<p><input type="checkbox" name="deleteDirectory" id="deleteDirectory" value="Y" checked><label for="deleteDirectory"><?echo GetMessage($moduleId . '_DELETE_DIRECTORY', array('#PATH#' => ft\CTpic::PATH))?></label></p>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>