<?if(!check_bitrix_sessid()) return;?>
<div class="adm-info-message-wrap adm-info-message-green">
	<div class="adm-info-message">
		<div class="adm-info-message-title"><?=GetMessage("OPTIMUS_MOD_INST_OK")?></div>
		<div class="adm-info-message-icon"></div>
	</div>
</div>
<?//=CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));?>
<form action="/bitrix/admin/wizard_list.php?lang=ru">
	<input type="submit" name="" value="<?=GetMessage("OPEN_WIZARDS_LIST")?>" style="margin-right: 10px;">
	<input type="button" value="<?=GetMessage("INSTALL_OPTIMUS_SITE")?>" style="margin-right: 30px;" onclick="document.location.href='/bitrix/admin/wizard_install.php?lang=ru&wizardName=aspro:optimus&<?=bitrix_sessid_get()?>';"> 
<form>
