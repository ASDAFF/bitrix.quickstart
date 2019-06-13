<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.mailtpl/include.php');
IncludeModuleLangFile(__FILE__);

if (!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings')) {
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$isAdmin = $USER->CanDoOperation('edit_other_settings');

$sTableID = 'tbl_asd_mailtpl';
$oSort = new CAdminSorting($sTableID, 'ID', 'DESC');
$lAdmin = new CAdminList($sTableID, $oSort);

if ($isAdmin && $arID=$lAdmin->GroupAction()) {
	foreach ($arID as $ID) {
		switch ($_REQUEST['action']) {
			case 'delete':
				CASDMailTplDB::Delete($ID);
				break;
			default:
				break;
		}
	}
}

$arHeaders = array();
$arHeaders[] = array('id' => 'ID', 'content' => 'ID', 'sort' => 'ID', 'default' => true);
$arHeaders[] = array('id' => 'NAME', 'content' => GetMessage('ASD_MAILTPL_NAZVANIE'), 'sort' => 'NAME', 'default' => true);
$arHeaders[] = array('id' => 'TYPE', 'content' => 'html/'.GetMessage('ASD_MAILTPL_TEKST'), 'sort' => 'TYPE', 'default' => true);
//$arHeaders[] = array('id' => 'EVENTS', "content" => GetMessage('ASD_MAILTPL_TIPY'), 'sort' => false, 'default' => true);
$lAdmin->AddHeaders($arHeaders);

$rsRec = CASDMailTplDB::GetList(array($by => $order));
$rsRec = new CAdminResult($rsRec, $sTableID);
$rsRec->NavStart();
$lAdmin->NavText($rsRec->GetNavPrint(''));

while($arRes = $rsRec->NavNext(true, 'f_')) {

	$row =& $lAdmin->AddRow($f_ID, $arRes, 'asd_mailtpl_edit.php?ID='.$f_ID.'&lang='.LANG);
	$row->AddViewField('NAME', '<a href="asd_mailtpl_edit.php?ID='.$f_ID.'&amp;lang='.LANG.'" title="'.GetMessage('ASD_MAILTPL_REDAKTIROVATQ').'">'.$f_NAME.'</a>');

	$arActions = array();
	$arActions[] = array(
		'ICON' => 'edit',
		'DEFAULT' => true,
		'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
		'ACTION' => $lAdmin->ActionRedirect('asd_mailtpl_edit.php?ID='.$f_ID.'&lang='.LANG)
	);
	if ($isAdmin) {
		$arActions[] = array(
			'ICON' => 'delete',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => 'if(confirm(\''.GetMessage('ASD_MAILTPL_VY_DEYSTVITELQNO_HOT').'?\')) '.$lAdmin->ActionDoGroup($f_ID, 'delete')
		);
	}
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array('title' => GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value' => $rsRec->SelectedRowsCount()),
		array('counter' => true, 'title' => GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value' => '0'),
	)
);
$lAdmin->AddGroupActionTable(Array(
	'delete' => '',
));

$aContext = array();
$aContext[] = array(
	'TEXT' => GetMessage('MAIN_ADD'),
	'ICON' => 'btn_new',
	'LINK' => 'asd_mailtpl_edit.php?lang='.LANG,
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('ASD_MAILTPL_SAPKI_I_PODVALY_POCT'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
$lAdmin->DisplayList();
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');