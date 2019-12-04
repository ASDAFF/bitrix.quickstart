<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<p><a href="/bitrix/admin/settings.php?lang=<?=LANG?>&mid=page.error_404&mid_menu=1"><?=GetMessage('ERR404_GO_TO_OPTIONS')?></a></p>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage("MOD_BACK")?>">
<form>
