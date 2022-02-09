<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_store')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
$bReadOnly = !$USER->CanDoOperation('catalog_store');

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if (!CBXFeatures::IsFeatureEnabled('CatMultiStore'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

$sTableID = "b_catalog_store";
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
		$arFields['ID']=$ID;
		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (!CCatalogStore::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(GetMessage("ERROR_UPDATING_REC")." (".$arFields["ID"].", ".$arFields["TITLE"].", ".$arFields["SORT"].")", $ID);

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
		$dbResultList = CCatalogStore::GetList(array($by => $order));
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

				if (!CCatalogStore::Delete($ID))
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
$arSelect = array(
	"ID",
	"ACTIVE",
	"TITLE",
	"ADDRESS",
	"DESCRIPTION",
	"GPS_N",
	"GPS_S",
	"IMAGE_ID",
	"PHONE",
	"SCHEDULE",
	"XML_ID",
);
$dbResultList = CCatalogStore::GetList(array($by => $order),false,false,false,$arSelect);
$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();
$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("group_admin_nav")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"TITLE","content"=>GetMessage("TITLE"), "sort"=>"TITLE", "default"=>true),
	array("id"=>"ACTIVE","content"=>GetMessage("STORE_ACTIVE"), "sort"=>"ACTIVE_FLAG", "default"=>true),
	array("id"=>"ADDRESS", "content"=>GetMessage("ADDRESS"), "sort"=>"ADDRESS", "default"=>true),
	array("id"=>"IMAGE_ID", "content"=>GetMessage("STORE_IMAGE"),  "sort"=>"IMAGE_ID", "default"=>false),
	array("id"=>"DESCRIPTION", "content"=>GetMessage("DESCRIPTION"),  "sort"=>"DESCRIPTION", "default"=>true),
	array("id"=>"GPS_N", "content"=>GetMessage("GPS_N"),  "sort"=>"GPS_N", "default"=>false),
	array("id"=>"GPS_S", "content"=>GetMessage("GPS_S"),  "sort"=>"GPS_S", "default"=>false),
	array("id"=>"PHONE", "content"=>GetMessage("PHONE"),  "sort"=>"PHONE", "default"=>true),
	array("id"=>"SCHEDULE", "content"=>GetMessage("SCHEDULE"),  "sort"=>"SCHEDULE", "default"=>true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

while ($arSTORE = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arSTORE);
	$row->AddField("ID", $f_ID);
	if ($bReadOnly)
	{
		$row->AddViewField("TITLE", $f_TITLE);
		$row->AddViewField("ADDRESS", $f_ADDRESS);
		$row->AddViewField("DESCRIPTION", $f_DESCRIPTION);

	}
	else
	{
		$row->AddInputField("TITLE");
		$row->AddCheckField("ACTIVE");
		$row->AddInputField("ADDRESS", array("size" => "30"));
		$row->AddInputField("DESCRIPTION", array("size" => "50"));
		$row->AddInputField("PHONE", array("size" => "25"));
		$row->AddInputField("SCHEDULE", array("size" => "35"));
		$row->AddField("IMAGE_ID", CFile::ShowImage($f_IMAGE_ID, 100, 100, "border=0", "", true));

	}

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("EDIT_STORE_ALT"), "ACTION"=>$lAdmin->ActionRedirect("cat_store_edit.php?ID=".$f_ID."&lang=".LANG."&".GetFilterParams("filter_").""), "DEFAULT"=>true);

	if (!$bReadOnly)
	{
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("DELETE_STORE_ALT"), "ACTION"=>"if(confirm('".GetMessage('DELETE_STORE_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
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
	$lAdmin->AddGroupActionTable(
		array(
			"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
		)
	);
}

if (!$bReadOnly)
{
	$aContext = array(
		array(
			"TEXT" => GetMessage("STORE_ADD_NEW"),
			"ICON" => "btn_new",
			"LINK" => "cat_store_edit.php?lang=".LANG,
			"TITLE" => GetMessage("STORE_ADD_NEW_ALT")
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("STORE_TITLE"));
require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
$lAdmin->DisplayList();
?>

<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>