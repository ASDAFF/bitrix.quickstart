<?
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2006 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$sTableID = "tbl_sale_discount";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_lang",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if ($filter_lang != "NOT_REF" && strlen($filter_lang) > 0)
	$arFilter["LID"] = $filter_lang;
else
	Unset($arFilter["LID"]);

if ($lAdmin->EditAction() && $saleModulePermissions >= "W")
{
	foreach ($FIELDS as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (!CSaleDiscount::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(GetMessage("ERROR_UPDATE_REC")." (".$ID.", ".$arFields["LID"].", ".$arFields["NAME"].", ".$arFields["SORT"].")", $ID);

			$DB->Rollback();
		}

		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && $saleModulePermissions >= "W")
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = CSaleDiscount::GetList($by, $order, $arFilter);
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

				if (!CSaleDiscount::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("SDSN_DELETE_ERR"), $ID);
				}

				$DB->Commit();

				break;

			case "activate":
			case "deactivate":

				$arFields = array(
					"ACTIVE" => (($_REQUEST['action']=="activate") ? "Y" : "N")
				);

				if (!CSaleDiscount::Update($ID, $arFields))
				{
					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("ERROR_UPDATE_REC"), $ID);
				}

				break;
		}
	}
}

$dbResultList = CSaleDiscount::GetList($by, $order, $arFilter);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("PERS_TYPE_NAV")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>GetMessage("PERS_TYPE_ID"), "sort"=>"ID", "default"=>true),
	array("id"=>"LID","content"=>GetMessage("PERS_TYPE_LID"), "sort"=>"LID", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage('PERS_TYPE_ACTIVE'),"sort"=>"ACTIVE", "default"=>true),
	array("id"=>"PRICE", "content"=>GetMessage("PERS_TYPE_PRICE"), "sort"=>"", "default"=>true),
	array("id"=>"DISCOUNT", "content"=>GetMessage("PERS_TYPE_DISCOUNT"), "sort"=>"", "default"=>true),
	array("id"=>"SORT", "content"=>GetMessage("PERS_TYPE_SORT"), "sort"=>"SORT", "default"=>true),
	array("id"=>"ACTIVE_FROM", "content"=>GetMessage("SDSN_ACTIVE_FROM"), "sort"=>"ACTIVE_FROM", "default"=>true),
	array("id"=>"ACTIVE_TO", "content"=>GetMessage("SDSN_ACTIVE_TO"), "sort"=>"ACTIVE_TO", "default"=>true),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$arLangs = array();
$dbLangsList = CLang::GetList(($b = "sort"), ($o = "asc"));
while ($arLang = $dbLangsList->Fetch())
	$arLangs[$arLang["LID"]] = $arLang["LID"];

while ($arDiscount = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arDiscount, "sale_discount_edit.php?ID=".$f_ID."&lang=".LANG.GetFilterParams("filter_"), GetMessage("SDSN_UPDATE_ALT"));

	$row->AddField("ID", $f_ID);
	$row->AddSelectField("LID", $arLangs, array());
	$row->AddCheckField("ACTIVE");

	$fieldValue = GetMessage("SALE_FROM")." ".$f_PRICE_FROM." ".GetMessage("SALE_TO")." ".$f_PRICE_TO." ".$f_CURRENCY;
	$fieldEdit  = GetMessage("SALE_FROM")." ";

	if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
		$val = $_REQUEST["FIELDS"][$f_ID]["PRICE_FROM"];
	else
		$val = $f_PRICE_FROM;

	$fieldEdit .= "<input type=\"text\" name=\"FIELDS[".$f_ID."][PRICE_FROM]\" value=\"".htmlspecialcharsbx($val)."\" size=\"7\"> ";
	$fieldEdit .= GetMessage("SALE_TO")." ";

	if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
		$val = $_REQUEST["FIELDS"][$f_ID]["PRICE_TO"];
	else
		$val = $f_PRICE_TO;

	$fieldEdit .= "<input type=\"text\" name=\"FIELDS[".$f_ID."][PRICE_TO]\" value=\"".htmlspecialcharsbx($val)."\" size=\"7\"> ";

	if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
		$val = $_REQUEST["FIELDS"][$f_ID]["CURRENCY"];
	else
		$val = $f_CURRENCY;

	$fieldEdit .= CCurrency::SelectBox("FIELDS[".$f_ID."][CURRENCY]", $val, "", false, "", "");
//	$row->AddField("PRICE", $fieldValue, $fieldEdit);
	$row->AddViewField("PRICE", $fieldValue);


	$fieldValue = $f_DISCOUNT_VALUE.(($f_DISCOUNT_TYPE=="P") ? "%" : " ".$val);
	$fieldEdit  = "";

	if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
		$val = $_REQUEST["FIELDS"][$f_ID]["DISCOUNT_VALUE"];
	else
		$val = $f_DISCOUNT_VALUE;

	$fieldEdit .= "<input type=\"text\" name=\"FIELDS[".$f_ID."][DISCOUNT_VALUE]\" value=\"".htmlspecialcharsbx($val)."\" size=\"4\"> ";

	if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
		$val = $_REQUEST["FIELDS"][$f_ID]["DISCOUNT_TYPE"];
	else
		$val = $f_DISCOUNT_TYPE;

	$fieldEdit .= "<select name=\"FIELDS[".$f_ID."][DISCOUNT_TYPE]\">";
	$fieldEdit .= "<option value=\"P\"".(($val=="P") ? " selected" : "").">%</option>";
	$fieldEdit .= "<option value=\"V\"".(($val=="V") ? " selected" : "")."> </option>";
	$fieldEdit .= "</select>";
//	$row->AddField("DISCOUNT", $fieldValue, $fieldEdit);
	$row->AddViewField("DISCOUNT", $fieldValue);

	$row->AddInputField("SORT", array("size" => "2"));

	$row->AddCalendarField("ACTIVE_FROM", array("size" => "10"));
	$row->AddCalendarField("ACTIVE_TO", array("size" => "10"));

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("SDSN_UPDATE_ALT"), "ACTION"=>$lAdmin->ActionRedirect("sale_discount_edit.php?ID=".$f_ID."&lang=".LANG.GetFilterParams("filter_").""), "DEFAULT"=>true);
	if ($saleModulePermissions >= "W")
	{
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SDSN_DELETE_ALT1"), "ACTION"=>"if(confirm('".GetMessage('SDSN_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
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

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	)
);

if ($saleModulePermissions >= "W")
{
	$aContext = array(
		array(
			"TEXT" => GetMessage("SDSN_ADD_NEW"),
			"LINK" => "sale_discount_edit.php?lang=".LANG,
			"TITLE" => GetMessage("SDSN_ADD_NEW_ALT"),
			"ICON" => "btn_new"
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/

$APPLICATION->SetTitle(GetMessage("PERSON_TYPE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array()
);

$oFilter->Begin();
?>
	<tr>
		<td><?echo GetMessage("LANG_FILTER_NAME")?>:</td>
		<td><?echo CLang::SelectBox("filter_lang", $filter_lang, GetMessage("DS_ALL")) ?>
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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
