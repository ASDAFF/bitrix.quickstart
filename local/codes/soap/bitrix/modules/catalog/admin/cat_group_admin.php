<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_group')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_group');

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

$db_result_lang = CLangAdmin::GetList(($by1="sort"), ($order1="asc"));
$iCount = 0;
while ($db_result_lang_array = $db_result_lang->Fetch())
{
	$arLangsLid[$iCount] = $db_result_lang_array["LID"];
	$arLangsNames[$iCount] = htmlspecialcharsbx($db_result_lang_array["NAME"]);
	$iCount++;
}

$sTableID = "tbl_catalog_group";

$oSort = new CAdminSorting($sTableID, "ID", "asc");

$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array();

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if ($lAdmin->EditAction() && !$bReadOnly)
{
	foreach ($_POST['FIELDS'] as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (!CCatalogGroup::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(GetMessage("ERROR_UPDATING_REC")." (".$arFields["ID"].", ".$arFields["NAME"].", ".$arFields["SORT"].")", $ID);

			$DB->Rollback();
		}

		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && !$bReadOnly)
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = CCatalogGroup::GetList(array($by => $order));
		while ($arResult = $dbResultList->Fetch())
			$arID[] = $arResult['ID'];
	}

	foreach ($arID as $ID)
	{
		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);

				$DB->StartTransaction();

				if (!CCatalogGroup::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("ERROR_DELETING_TYPE"), $ID);
				}

				$DB->Commit();
				break;
		}
	}
}

$dbResultList = CCatalogGroup::GetList(array($by => $order));

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("group_admin_nav")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"NAME","content"=>GetMessage("CODE"), "sort"=>"NAME", "default"=>true),
	array("id"=>"NAME_LID", "content"=>GetMessage('NAME'), "sort"=>"", "default"=>true),
	array("id"=>"SORT", "content"=>GetMessage("SORT"),  "sort"=>"SORT", "default"=>true),
	array("id"=>"BASE", "content"=>GetMessage("BASE"),  "sort"=>"BASE", "default"=>true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

while ($arGroup = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arGroup);

	$row->AddField("ID", $f_ID);

	if ($bReadOnly)
		$row->AddViewField("NAME", $f_NAME);
	else
		$row->AddInputField("NAME", array("size" => "20"));

	$fieldShow = "";
	if (in_array("NAME_LID", $arVisibleColumns))
	{
		for ($i = 0; $i < $iCount; $i++)
		{
			$arcglang = CCatalogGroup::GetByID($f_ID, $arLangsLid[$i]);
			$fieldShow .= htmlspecialcharsbx($arLangsNames[$i].": ".$arcglang["NAME_LANG"])."<br>";
		}
	}
	$row->AddField("NAME_LID", $fieldShow);

	if ($bReadOnly)
		$row->AddViewField("SORT", $f_SORT);
	else
		$row->AddInputField("SORT", array("size" => "4"));

	$row->AddField("BASE", (($f_BASE=="Y") ? GetMessage("BASE_YES") : "&nbsp;"));

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("EDIT_STATUS_ALT"), "ACTION"=>$lAdmin->ActionRedirect("cat_group_edit.php?ID=".$f_ID."&lang=".LANG."&".GetFilterParams("filter_").""), "DEFAULT"=>true);

	if (!$bReadOnly)
	{
		if (CBXFeatures::IsFeatureEnabled('CatMultiPrice') || 'Y' != $arGroup['BASE'])
		{
			$arActions[] = array("SEPARATOR" => true);
			$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("DELETE_STATUS_ALT"), "ACTION"=>"if(confirm('".GetMessage('DELETE_STATUS_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
		}
	}

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResultList->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

if (!$bReadOnly)
{
	if (CBXFeatures::IsFeatureEnabled('CatMultiPrice'))
	{
		$lAdmin->AddGroupActionTable(
			array(
				"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
			)
		);
	}
	else
	{
		$lAdmin->AddGroupActionTable(
			array()
		);
	}
}

if (!$bReadOnly)
{
	$aContext = array();
	$boolEmptyPrice = true;
	$dbCatGroup = CCatalogGroup::GetList(array("ID" => "ASC"), array(), false, array("nTopCount" => 1), array("ID"));
	if ($arCatGroup = $dbCatGroup->Fetch())
	{
		$boolEmptyPrice = false;
	}
	if (CBXFeatures::IsFeatureEnabled('CatMultiPrice') || $boolEmptyPrice)
	{
		$aContext = array(
			array(
				"TEXT" => GetMessage("CGAN_ADD_NEW"),
				"ICON" => "btn_new",
				"LINK" => "cat_group_edit.php?lang=".LANG,
				"TITLE" => GetMessage("CGAN_ADD_NEW_ALT")
			),
		);
	}
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("GROUP_TITLE"));
require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
$lAdmin->DisplayList();
?>

<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>