<?
	IncludeModuleLangFile(__FILE__);
?>
<form action="<?=$APPLICATION->GetCurPage(); ?>">
	<?=bitrix_sessid_post(); ?>
	<input type="hidden" name="lang" value="<?=LANG; ?>">
	<input type="hidden" name="id" value="intec.startshop">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="1">
	<?=CAdminMessage::ShowMessage(GetMessage('module.uninstall.form.warning')); ?>
	<h3><?=GetMessage('module.uninstall.form.delete_data'); ?></h3>
	<p>
		<input class="adm-checkbox adm-designed-checkbox" type="checkbox" name="startshopUninstall[TABLES]" id="startshopUninstall_TABLES" value="Y" checked>
		<label class="adm-checkbox adm-designed-checkbox-label" for="startshopUninstall_TABLES"></label>
		<label class="adm-checkbox-label" for="startshopUninstall_TABLES"><?=GetMessage('module.uninstall.form.tables'); ?></label>
	</p>
	<p>
		<input class="adm-checkbox adm-designed-checkbox" type="checkbox" name="startshopUninstall[SETTINGS]" id="startshopUninstall_SETTINGS" value="Y" checked>
		<label class="adm-checkbox adm-designed-checkbox-label" for="startshopUninstall_SETTINGS"></label>
		<label class="adm-checkbox-label" for="startshopUninstall_SETTINGS"><?=GetMessage('module.uninstall.form.settings'); ?></label>
	</p>
	<input type="submit" value="<?=GetMessage('module.uninstall.form.submit'); ?>">
	<a class="adm-btn" href="/bitrix/admin/partner_modules.php?lang=<?=LANG; ?>"><?=GetMessage('module.uninstall.form.back'); ?></a>
</form>