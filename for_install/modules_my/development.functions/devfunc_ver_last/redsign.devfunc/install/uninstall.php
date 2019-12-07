<?if(!check_bitrix_sessid()) return;

echo CAdminMessage::ShowNote(GetMessage("MOD_UNINST_OK"));

?><form action="<?=$APPLICATION->GetCurPage()?>"><?
	?><input type="hidden" name="lang" value="<?=LANG?>"><?
	?><input type="submit" name="" value="<?=GetMessage("MOD_BACK")?>"><?
?><form>
