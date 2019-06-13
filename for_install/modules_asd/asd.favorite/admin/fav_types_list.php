<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.favorite/include.php');
IncludeModuleLangFile(__FILE__);

if (!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$sTableID = 'tbl_asd_fav_types';
$oSort = new CAdminSorting($sTableID, 'CODE', 'ASC');
$lAdmin = new CAdminList($sTableID, $oSort);

if ($arID = $lAdmin->GroupAction())
{
	foreach($arID as $ID)
	{
		switch ($_REQUEST['action'])
		{
			case 'delete':
				CASDfavorite::DeleteType($ID);
				break;
			default:
				break;
		}
	}
}
$arHeaders = array();
$arHeaders[] = array('id' => 'CODE', "content" => GetMessage('asd_mod_code'), 'sort' => 'CODE', 'default' => true);
$arHeaders[] = array('id' => 'NAME', "content" => GetMessage('asd_mod_name'), 'sort' => 'NAME', 'default' => true);
$arHeaders[] = array('id' => 'MODULE', "content" => GetMessage('asd_mod_module'), 'sort' => 'MODULE', 'default' => true);
$lAdmin->AddHeaders($arHeaders);

$rsRec = CASDfavorite::GetTypes(array($by => $order));
$rsRec = new CAdminResult($rsRec, $sTableID);
$rsRec->NavStart();
$lAdmin->NavText($rsRec->GetNavPrint(GetMessage('asd_mod_nav_title')));

while($arRes = $rsRec->NavNext(true, 'f_'))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes, 'asd_fav_types_edit.php?CODE='.$f_CODE.'&lang='.LANG);
	$row->AddViewField('CODE', '<a href="asd_fav_types_edit.php?CODE='.$f_CODE.'&amp;lang='.LANG.'" title="'.GetMessage('MAIN_ADMIN_MENU_EDIT').'">'.$f_CODE.'</a>');
	$row->AddViewField('MODULE', GetMessage('asd_mod_module_'.$f_MODULE));

	$arActions = array();
	$arActions[] = array(
		'ICON' => 'edit',
		'DEFAULT' => true,
		'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
		'ACTION' => $lAdmin->ActionRedirect('asd_fav_types_edit.php?CODE='.$f_CODE.'&lang='.LANG)
	);
	$arActions[] = array(
		'ICON' => 'delete',
		'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
		'ACTION' => 'if(confirm(\''.GetMessage('asd_mod_confirm_delete').'\')) '.$lAdmin->ActionDoGroup($f_CODE, 'delete')
	);
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(array());

$aContext = array();
$aContext[] = array(
	'TEXT' => GetMessage('MAIN_ADD'),
	'ICON' => 'btn_new',
	'LINK' => 'asd_fav_types_edit.php?lang='.LANG,
	'TITLE'=> GetMessage('asd_mod_new_rec_title')
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('asd_mod_title'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
$lAdmin->DisplayList();
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>