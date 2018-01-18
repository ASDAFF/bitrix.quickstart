<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("millcom.phpthumb");
$APPLICATION->SetTitle(GetMessage("MILLCOM_PHPTHUMB_LIST_TEMPLATES"));


if (isset($_REQUEST["action_button"]) && $_REQUEST["action_button"] == 'DELETE') {
	CMillcomPhpThumbTemplates::Delete($_REQUEST["ID"]);
}



$sTableID = "c_millcom_phpthumb";
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка



$cData = new CMillcomPhpThumbTemplates;
$rsData = $cData->GetList();

$rsData = new CAdminResult($rsData, $sTableID);

$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("MILLCOM_PHPTHUMB_TEMPLATES")));

$lAdmin->AddHeaders(array(
	array(
		"id"				=> "ID",
		"content"		=> "ID",
		"sort"			=> "ID",
		"align"			=> "right",
		"default"		=> true,
  ),
  array(
		"id"				=> "NAME",
    "content"		=> GetMessage("MILLCOM_PHPTHUMB_NAME"),
    "sort"			=> "NAME",
    "default"		=> true,
  ),
  array(
		"id"				=> "WIDTH",
    "content"		=> GetMessage("MILLCOM_PHPTHUMB_WIDTH"),
    "sort"			=> false,
    "default"		=> true,
  ),
  array(
		"id"				=> "HEIGHT",
    "content"		=> GetMessage("MILLCOM_PHPTHUMB_HEIGHT"),
    "sort"			=> false,
    "default"		=> true,
  ),
  array(
		"id"				=> "OPTIONS",
    "content"		=> GetMessage("MILLCOM_PHPTHUMB_OPTIONS"),
    "sort"			=> false,
    "default"		=> false,
  )
));

while ($arRes = $rsData->GetNext()) {
	$actions = array(
		array("SEPARATOR" => true),
		array(
			"ICON"		=> "edit",
			"TEXT"		=> GetMessage("MILLCOM_PHPTHUMB_EDIT"),
			"ACTION"	=> $lAdmin->ActionRedirect("millcom_phpthumb_edit.php?ID=" . $arRes["ID"] . "&" . bitrix_sessid_get() . "&lang=" . LANG . "")
		),
		array(
			"ICON"		=> "delete",
			"TEXT"		=> GetMessage("MILLCOM_PHPTHUMB_DELETE"),
			"ACTION"	=> $lAdmin->ActionDoGroup($arRes["ID"], "DELETE")
		)
	);

	$rowCols = array(
		"ID"				=> $arRes["ID"],
		"NAME"			=> $arRes["NAME"],
		"WIDTH"			=> '',
		"HEIGHT"		=> '',
		"OPTIONS"		=> $arRes["OPTIONS"]
	);

	$OPTIONS = unserialize($arRes["~OPTIONS"]);
	if (isset($OPTIONS['w']))
		$rowCols['WIDTH'] = $OPTIONS['w'];

	if (isset($OPTIONS['h']))
		$rowCols['HEIGHT'] = $OPTIONS['h'];

  $row =& $lAdmin->AddRow($id, $rowCols, "millcom_phpthumb_edit.php?ID=" . $arRes["ID"] . "&" . bitrix_sessid_get() . "&lang=" . LANG);
	$row->AddActions($actions);
}

$top_menu = array(
	array(
		"TEXT"  => GetMessage("MILLCOM_PHPTHUMB_ADD"),
		"ICON"  => "btn_new",
		"LINK"  => "millcom_phpthumb_edit.php?" . bitrix_sessid_get() . "&lang=".LANG,
		"TITLE" => "Добавить"
	),
);
$lAdmin->AddAdminContextMenu($top_menu, false, false);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (isset($_REQUEST['MESS'])) {
	switch($_REQUEST['MESS']) {
		case 'ADD':
			CAdminMessage::ShowNote(GetMessage("MILLCOM_PHPTHUMB_NOTE_ADD"));
			break;
		case 'EDIT':
			CAdminMessage::ShowNote(GetMessage("MILLCOM_PHPTHUMB_NOTE_EDIT"));
			break;
		case 'DELETE':
			CAdminMessage::ShowNote(GetMessage("MILLCOM_PHPTHUMB_NOTE_DELETE"));
			break;
	}
}

?>
<?
$lAdmin->DisplayList();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>