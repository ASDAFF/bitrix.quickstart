<?php
/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 */
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/culture_admin.php");

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

use Bitrix\Main\Localization\CultureTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$tableID = "tbl_culture";
$sorting = new CAdminSorting($tableID, "name", "asc");
$adminList = new CAdminList($tableID, $sorting);

if($adminList->EditAction() && $isAdmin)
{
	foreach($_REQUEST["FIELDS"] as $ID => $arFields)
	{
		if(!$adminList->IsUpdated($ID))
			continue;

		$result = CultureTable::update($ID, $arFields);
		if(!$result->isSuccess())
		{
			$adminList->AddUpdateError("(ID=".$ID.") ".implode("<br>", $result->getErrorMessages()), $ID);
		}
	}
}

if(($arID = $adminList->GroupAction()) && $isAdmin)
{
	if($_REQUEST['action_target'] == 'selected')
	{
		$arID = array();
		$data = CultureTable::getList();
		while($culture = $data->Fetch())
			$arID[] = $culture['ID'];
	}

	foreach($arID as $ID)
	{
		if(intval($ID) <= 0)
			continue;

		switch($_REQUEST['action'])
		{
			case "delete":
				$result = CultureTable::delete($ID);
				if(!$result->isSuccess())
				{
					$adminList->AddGroupError("(ID=".$ID.") ".implode("<br>", $result->getErrorMessages()), $ID);
				}
				break;
		}
	}
}

$APPLICATION->SetTitle(Loc::getMessage("TITLE"));

/**
 * @global $by
 * @global $order
 */
$cultureList = CultureTable::getList(array(
	'order' => array($by => $order)
));
$data = new CAdminResult($cultureList, $tableID);
$data->NavStart();

$adminList->NavText($data->GetNavPrint(Loc::getMessage("PAGES"), false));

$adminList->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"NAME", "content"=>Loc::getMessage("NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"CODE", "content"=>Loc::getMessage("culture_code"), "sort"=>"CODE", "default"=>true),
	array("id"=>"FORMAT_DATE", "content"=>Loc::getMessage("culture_date"), "sort"=>"FORMAT_DATE", "default"=>true),
	array("id"=>"FORMAT_DATETIME", "content"=>Loc::getMessage("culture_datetime"), "sort"=>"FORMAT_DATETIME", "default"=>true),
	array("id"=>"FORMAT_NAME", "content"=>Loc::getMessage("culture_name"), "sort"=>"FORMAT_NAME", "default"=>true),
	array("id"=>"CHARSET", "content"=>Loc::getMessage("culture_charset"), "sort"=>"CHARSET", "default"=>true),
	array("id"=>"WEEK_START", "content"=>Loc::getMessage("culture_week"), "sort"=>"WEEK_START", "default"=>false),
	array("id"=>"DIRECTION", "content"=>Loc::getMessage("culture_direction"), "sort"=>"DIRECTION", "default"=>false),
));

$days = array(Loc::getMessage("culture_su"), Loc::getMessage("culture_mo"), Loc::getMessage("culture_tu"), Loc::getMessage("culture_we"), Loc::getMessage("culture_th"), Loc::getMessage("culture_fr"), Loc::getMessage("culture_sa"));

while($culture = $data->Fetch())
{
	$id = htmlspecialcharsbx($culture["ID"]);
	$name = htmlspecialcharsbx($culture["NAME"]);

	$row = &$adminList->AddRow($id, $culture, "culture_edit.php?ID=".$id."&lang=".LANGUAGE_ID, Loc::getMessage("LANG_EDIT_TITLE"));
	$row->AddViewField("ID", $id);
	$row->AddField("NAME", '<a href="culture_edit.php?ID='.$id.'&amp;lang='.LANGUAGE_ID.'" title="'.Loc::getMessage("LANG_EDIT_TITLE").'">'.$name.'</a>', $name);
	$row->AddInputField("CODE");
	$row->AddInputField("FORMAT_DATE");
	$row->AddInputField("FORMAT_DATETIME");
	$row->AddInputField("FORMAT_NAME");
	$row->AddViewField("WEEK_START", $days[$culture["WEEK_START"]]);
	$row->AddInputField("CHARSET");
	$row->AddViewField("DIRECTION", ($culture["DIRECTION"] == CultureTable::LEFT_TO_RIGHT? Loc::getMessage("culture_left_to_right") : Loc::getMessage("culture_right_to_left")));

	$arActions = array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>Loc::getMessage("CHANGE"), "ACTION"=>$adminList->ActionRedirect("culture_edit.php?ID=".$f_ID));
	if($isAdmin)
	{
		$arActions[] = array("ICON"=>"copy", "TEXT"=>Loc::getMessage("COPY"), "ACTION"=>$adminList->ActionRedirect("culture_edit.php?COPY_ID=".$f_ID));
		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>Loc::getMessage("DELETE"), "ACTION"=>"if(confirm('".Loc::getMessage('CONFIRM_DEL')."')) ".$adminList->ActionDoGroup($f_ID, "delete"));
	}

	$row->AddActions($arActions);
}

$adminList->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$data->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

$adminList->AddGroupActionTable(Array(
	"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

$aContext = array(
	array(
		"TEXT"	=> Loc::getMessage("ADD_LANG"),
		"LINK"	=> "culture_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> Loc::getMessage("ADD_LANG_TITLE"),
		"ICON"	=> "btn_new"
	),
);
$adminList->AddAdminContextMenu($aContext);

$adminList->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$adminList->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
