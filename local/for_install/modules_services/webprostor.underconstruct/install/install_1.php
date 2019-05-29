<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<p><?=GetMessage("WEBPROSTOR_UNDERCONSTRUCT_INSTALL_INFO")?></p>
<ol>
	<li><?=GetMessage("WEBPROSTOR_UNDERCONSTRUCT_INSTALL_GO_TO_MASTERS_LIST_TITLE")?></li>
	<li><?=GetMessage("WEBPROSTOR_UNDERCONSTRUCT_INSTALL_RUN_MASTER")?></li>
</ol>
<form action="/bitrix/admin/wizard_list.php?lang=<?=LANG?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("WEBPROSTOR_UNDERCONSTRUCT_INSTALL_GO_TO_MASTERS_LIST")?>">	
<form>