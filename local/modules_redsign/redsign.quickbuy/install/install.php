<?if(!check_bitrix_sessid()) return;

IncludeModuleLangFile(__FILE__);
echo CAdminMessage::ShowNote(GetMessage('MOD_INST_OK'));

?><form action="<?=$APPLICATION->GetCurPage()?>"><?
	?><input type="hidden" name="lang" value="<?=LANG?>"><?
	?><input type="submit" name="" value="<?=GetMessage('MOD_BACK')?>"><?
?></form><?

?><form action="/bitrix/admin/settings.php"><?
	?><input type="hidden" name="lang" value="<?=LANG?>"><?
	?><input type="hidden" name="mid" value="redsign.quickbuy"><?
	?><input type="submit" name="" value="<?=GetMessage('RSQB.GO_SETTINGS')?>"><?
?></form>