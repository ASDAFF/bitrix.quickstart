<?IncludeModuleLangFile(__FILE__);?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANG?>"/>
	<input type="hidden" name="id" value="wsm.callback"/>
	<input type="hidden" name="uninstall" value="Y"/>
	<input type="hidden" name="step" value="2"/>
	<?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	<p><?echo GetMessage("MOD_UNINST_SAVE")?></p>
	<p><input type="checkbox" name="save_iblock" id="save_demo_iblock" value="Y" checked /><label for="save_demo_iblock"><?echo GetMessage("MOD_UNINST_SAVE_IBLOCK")?></label></p>
	<p><input type="checkbox" name="save_demo_section" id="save_demo_section" value="Y" checked /><label for="save_demo_section"><?echo GetMessage("MOD_UNINST_SAVE_SECTION")?></label></p>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>"/>
</form>