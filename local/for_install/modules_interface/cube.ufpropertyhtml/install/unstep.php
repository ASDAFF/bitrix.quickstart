<?if(!check_bitrix_sessid()) return;?>
<?
UnRegisterModuleDependences("main", "OnUserTypeBuildList", 
"cube.ufpropertyhtml", "PropertyHTML", "GetUserTypeDescription");

UnRegisterModule("cube.ufpropertyhtml");

echo CAdminMessage::ShowNote(GetMessage("MOD_UNINST_OK"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" method="get">
<p>
	<input type="hidden" name="lang" value="<?echo LANG?>" />
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>" />
</p>
<form>