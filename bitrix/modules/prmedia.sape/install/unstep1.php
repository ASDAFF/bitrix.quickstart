<?
IncludeModuleLangFile(__FILE__);

$sape_id = COption::GetOptionString('prmedia.sape', 'sape_id');

DeleteDirFilesEx('/'.$sape_id);

CPageOption::RemoveOption('prmedia.sape');
UnRegisterModule("prmedia.sape");

DeleteDirFilesEx('/bitrix/components/prmedia/sape');

echo CAdminMessage::ShowNote(GetMessage("PRMEDIA_UNSTEP1_MOD_UNINST_OK"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?=GetMessage("PRMEDIA_UNSTEP1_MOD_BACK")?>">
<form>