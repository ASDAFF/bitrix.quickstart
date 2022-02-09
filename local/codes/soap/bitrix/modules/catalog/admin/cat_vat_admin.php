<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_vat')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_vat');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

$sTableID = "tbl_catalog_vat";

$oSort = new CAdminSorting($sTableID, "C_SORT", "ASC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter",
	"filter_id",
	"filter_active",
	"filter_name",
	"filter_rate",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_id) > 0) $arFilter["ID"] = $filter_id;
if (strlen($filter_active) > 0) $arFilter["ACTIVE"] = $filter_active;
if (strlen($filter_name) > 0) $arFilter["NAME"] = $filter_name;
if (strlen($filter_rate) > 0) $arFilter["RATE"] = $filter_rate;

if ($lAdmin->EditAction() && !$bReadOnly)
{
	foreach ($_POST['FIELDS'] as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		$arFields['ID'] = $ID;

		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (!CCatalogVat::Set($arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(str_replace("#ID#", $ID, GetMessage("ERROR_UPDATE_VAT")), $ID);

			$DB->Rollback();
		}

		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && !$bReadOnly)
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = array();
		$dbResultList = CCatalogVat::GetList(
			array($by => $order),
			$arFilter,
			array('ID')
		);

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
				$DB->StartTransaction();

				if (!CCatalogVat::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("ERROR_DELETE_VAT")), $ID);
				}

				$DB->Commit();

				break;

			case "activate":
			case "deactivate":

				$arFields = array(
					"ID" => $ID,
					"ACTIVE" => (($_REQUEST['action'] == "activate") ? "Y" : "N")
				);

				if (!CCatalogVat::Set($arFields))
				{
					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("ERROR_UPDATE_VAT")), $ID);
				}

				break;
		}
	}
}

$dbResultList = CCatalogVat::GetList(
	array($by => $order),
	$arFilter
);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("CVAT_NAV")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
	array("id"=>"C_SORT", "content"=>GetMessage("CVAT_SORT"), "sort"=>"c_sort", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("CVAT_ACTIVE"), "sort"=>"active", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("CVAT_NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"RATE", "content"=>GetMessage("CVAT_RATE"), "sort"=>"rate", "default"=>true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

while ($arVAT = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arVAT);

	$row->AddField("ID", $f_ID);

	if ($bReadOnly)
	{
		$row->AddViewField("ACTIVE", $f_ACTIVE);
		$row->AddViewField("NAME", $f_NAME);
		$row->AddViewField("C_SORT", $f_C_SORT);
	}
	else
	{
		$row->AddCheckField("ACTIVE");
		$row->AddInputField("NAME", array("size" => "30"));
		$row->AddInputField("C_SORT", array("size" => "3"));

		$row->AddInputField("RATE", array("size" => "3"));
	}

	$row->AddViewField("RATE", $f_RATE."%");

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("CVAT_EDIT_ALT"), "ACTION"=>$lAdmin->ActionRedirect("cat_vat_edit.php?ID=".$f_ID."&lang=".LANG."&".GetFilterParams("filter_").""), "DEFAULT"=>true);

	if (!$bReadOnly)
	{
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("CVAT_DELETE_ALT"), "ACTION"=>"if(confirm('".GetMessage('CVAT_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
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
			"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
			"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
		)
	);
}

if (!$bReadOnly)
{
	$aContext = array(
		array(
			"TEXT" => GetMessage("CVAT_ADD_NEW"),
			"ICON" => "btn_new",
			"LINK" => "cat_vat_edit.php?lang=".LANG,
			"TITLE" => GetMessage("CVAT_ADD_NEW_ALT")
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("CVAT_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("CVAT_ACTIVE"),
		GetMessage("CVAT_NAME"),
		GetMessage("CVAT_RATE")
	)
);

$oFilter->Begin();
?>
	<tr>
		<td>ID:</td>
		<td>
			<input type="text" name="filter_id" size="5" value="<?echo htmlspecialcharsex($filter_id)?>" size="5" />
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("CVAT_FILTER_ACTIVE") ?>:</td>
		<td>
			<select name="filter_active">
				<option value=""><?= htmlspecialcharsex("(".GetMessage("CVAT_ALL").")") ?></option>
				<option value="Y"<?if ($filter_active=="Y") echo " selected"?>><?= htmlspecialcharsex(GetMessage("CVAT_YES")) ?></option>
				<option value="N"<?if ($filter_active=="N") echo " selected"?>><?= htmlspecialcharsex(GetMessage("CVAT_NO")) ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("CVAT_FILTER_NAME") ?>:</td>
		<td>
			<input type="text" name="filter_name" size="50" value="<?echo htmlspecialcharsex($filter_name)?>" size="30" />&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("CVAT_FILTER_RATE") ?>:</td>
		<td>
			<input type="text" name="filter_rate" size="5" value="<?echo htmlspecialcharsex($filter_rate)?>" size="30" />%
		</td>
	</tr>
<?
$oFilter->Buttons(
	array(
		"table_id" => $sTableID,
		"url" => $APPLICATION->GetCurPage(),
		"form" => "find_form"
	)
);
$oFilter->End();
?>
</form>

<?
$lAdmin->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>