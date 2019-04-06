<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
CUtil::InitJSCore(array('ajax'));
$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ME_NOT_ACCESS"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$enabled = true;

if(!empty($_POST['acceleration'])){
	if($_POST['acceleration'] == 'Y'){
		// Disable CBitrixCloudBackup
		UnRegisterModuleDependences("main", "OnAdminInformerInsertItems", "bitrixcloud", "CBitrixCloudBackup", "OnAdminInformerInsertItems");
	}elseif($_POST['acceleration'] == 'N'){
		// Enable CBitrixCloudBackup
		RegisterModuleDependences("main", "OnAdminInformerInsertItems", "bitrixcloud", "CBitrixCloudBackup", "OnAdminInformerInsertItems");
		$enabled = false;
	}
}else{
	/* + check CBitrixCloudBackup */
	$rsEvents = GetModuleEvents("main", "OnAdminInformerInsertItems");
	while ($arEvent = $rsEvents->Fetch()){
		if($arEvent['TO_MODULE_ID'] == 'bitrixcloud' && $arEvent['TO_CLASS'] == 'CBitrixCloudBackup'){
			$enabled = false;
			break;
		}
	}
	/* - check CBitrixCloudBackup */
}

$aTabs = array(
	array(
		"DIV" => "ptb_gen",
		"TAB" => (GetMessage('ME_TAB_TITLE')),
		"ICON" => "main_user_edit",
		"TITLE" => (GetMessage('ME_TITLE'))
	)
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>

<div id="ptb_window"></div>
<form id="ptb_codegenerator" method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANG?>" name="ptb_codegenerator">
<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab();
echo GetMessage('ME_DESCRIPTION');
?>

<p>
	<input type="hidden" name="acceleration" value="N" />
	<? $enabled_attr = $enabled ? ' checked="checked"' : ''; ?>
	<input class="adm-designed-checkbox" type="checkbox"<?=$enabled_attr?> id="acceleration" name="acceleration" value="Y" />
	<label class="adm-designed-checkbox-label" for="acceleration" title=""></label>
	<label for="acceleration"><?=GetMessage('ME_LABEL')?></label>
</p>
<?
$tabControl->Buttons();
?>
<input type="submit" id="save" value="<?=GetMessage('ME_SAVE')?>" class="adm-btn-save" />
<?
$tabControl->End();
?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");



