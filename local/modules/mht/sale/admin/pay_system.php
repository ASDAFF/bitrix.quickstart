<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$sTableID = "tbl_sale_pay_system";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	//"filter_lang",
	"filter_currency",
	"filter_active",
	"filter_person_type",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

//if (strlen($filter_lang)>0 && $filter_lang!="NOT_REF") $arFilter["LID"] = Trim($filter_lang);
if (strlen($filter_currency)>0) $arFilter["CURRENCY"] = Trim($filter_currency);
if (strlen($filter_active)>0 && $filter_active != "NOT_REF") $arFilter["ACTIVE"] = Trim($filter_active);

if (!empty($filter_person_type) && !in_array("NOT_REF", $filter_person_type))
	$arFilter["PSA_PERSON_TYPE_ID"] = $filter_person_type;
else
	$filter_person_type = Array();

if (($arID = $lAdmin->GroupAction()) && $saleModulePermissions >= "W")
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = CSalePaySystem::GetList(
			array($by => $order),
			$arFilter,
			false,
			false,
			array("ID")
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
				@set_time_limit(0);

				$DB->StartTransaction();

				if (!CSalePaySystem::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("SPSAN_ERROR_DELETE"), $ID);
				}

				$DB->Commit();

				break;

			case "activate":
			case "deactivate":

				$arFields = array(
					"ACTIVE" => (($_REQUEST['action']=="activate") ? "Y" : "N")
				);

				if (!CSalePaySystem::Update($ID, $arFields))
				{
					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(GetMessage("SPSAN_ERROR_UPDATE"), $ID);
				}

				break;
		}
	}
}

$arPersonTypeList = array();
$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());

while ($arPersonType = $dbPersonType->Fetch())
	$arPersonTypeList[$arPersonType["ID"]] = $arPersonType["NAME"];

$dbResultList = CSalePaySystem::GetList(
	array($by => $order),
	$arFilter,
	false,
	false,
	array("ID", "LID", "CURRENCY", "NAME", "ACTIVE", "SORT", "DESCRIPTION")
	//array("ID", "NAME", "ACTIVE", "SORT", "DESCRIPTION")
);

$siteList = array();
$rsSites = CSite::GetList($b = "sort", $o = "asc", Array());

while($arRes = $rsSites->Fetch())
	$siteList[$arRes['ID']] = $arRes['NAME'];

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("SALE_PRLIST")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"id", "default"=>true),
	array("id"=>"NAME","content"=>GetMessage("SALE_NAME"), "sort"=>"NAME", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("SALE_ACTIVE"),  "sort"=>"ACTIVE", "default"=>true),
	array("id"=>"SORT", "content"=>GetMessage("SALE_SORT"),  "sort"=>"SORT", "default"=>true),
	array("id"=>"LID", "content"=>GetMessage('SALE_LID'),	"sort"=>"LID", "default"=>false),
	array("id"=>"CURRENCY", "content"=>GetMessage("SALE_H_CURRENCY"),  "sort"=>"CURRENCY", "default"=>false),
	array("id"=>"DESCRIPTION", "content"=>GetMessage("SALE_H_DESCRIPTION"), "default"=>false),
	array("id"=>"PERSON_TYPES", "content"=>GetMessage("SALE_H_PERSON_TYPES"), "default"=>false),
	array("id"=>"ACTION_FILES", "content"=>GetMessage("SALE_H_ACTION_FILES"), "default"=>false),

));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

while ($arCCard = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arCCard, "sale_pay_system_edit.php?ID=".$f_ID."&lang=".LANG, GetMessage("SALE_EDIT_DESCR"));

	$row->AddField("ID", "<a href=\"sale_pay_system_edit.php?ID=".$f_ID."&lang=".LANG."\">".$f_ID."</a>");
	$row->AddField("NAME", $f_NAME);
	$row->AddField("LID", $siteList[$f_LID]." (".$f_LID.")");
	$row->AddField("CURRENCY", $f_CURRENCY);
	$row->AddField("ACTIVE", (($f_ACTIVE=="Y") ? GetMessage("SPS_YES") : GetMessage("SPS_NO")));
	$row->AddField("SORT", $f_SORT);
	$row->AddField("DESCRIPTION", $f_DESCRIPTION);

	$pTypes = '';
	$aFiles = '';
	$dbPSAction = CSalePaySystemAction::GetList(
			array(),
			array("PAY_SYSTEM_ID" => $f_ID),
			false,
			false,
			array("PERSON_TYPE_ID", "ACTION_FILE")
		);
	while ($arPSAction = $dbPSAction->Fetch())
	{
		if(isset($arPersonTypeList[$arPSAction["PERSON_TYPE_ID"]]))
			$pTypes .= "<div>".$arPersonTypeList[$arPSAction["PERSON_TYPE_ID"]]."</div>";

		$psActFilename = $_SERVER["DOCUMENT_ROOT"].$arPSAction["ACTION_FILE"];
		$psActTitle = "";
		$psActName = "";

		if (is_dir($psActFilename))
		{
			$psActTitle = CSalePaySystemsHelper::getPSActionTitle($psActFilename."/.description.php");
			$psActName = substr(strrchr($psActFilename, '/'), 1 );

		}
		elseif (is_file($psActFilename))
		{
			$psActTitle = CSalePaySystemsHelper::getPSActionTitle_old($psActFilename);
			$psActName = $arPSAction["ACTION_FILE"];
		}

		if (strlen($psActTitle) <= 0)
			$psActTitle = $psActName;
		else
			$psActTitle .= " (".$psActName.")";

		$aFiles .= "<div>".$psActTitle."</div>";
	}

	$row->AddField("PERSON_TYPES", $pTypes);
	$row->AddField("ACTION_FILES", $aFiles);

	$arActions = array(
		array(
			"ICON" => "edit",
			"TEXT" => GetMessage("SALE_EDIT"),
			"TITLE" => GetMessage("SALE_EDIT_DESCR"),
			"ACTION" => $lAdmin->ActionRedirect("sale_pay_system_edit.php?ID=".$f_ID."&lang=".LANG),
			"DEFAULT" => true,
		),
	);
	if ($saleModulePermissions >= "W")
	{
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage("SALE_DELETE"),
			"TITLE" => GetMessage("SALE_DELETE_DESCR"),
			"ACTION" => "if(confirm('".GetMessage('SALE_CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"),
		);
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
if ($saleModulePermissions == "W")
{

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	)
);

	$arDDMenu = array();

	/*
	$arDDMenu[] = array(
		"TEXT" => "<b>".GetMessage("SOPAN_4NEW_PROMT")."</b>",
		"ACTION" => false
	);
	*/
/*
	$dbRes = CLang::GetList(($by1="sort"), ($order1="asc"));
	while(($arRes = $dbRes->Fetch()))
	{
		$arDDMenu[] = array(
			"TEXT" => "[".$arRes["LID"]."] ".$arRes["NAME"],
			"ACTION" => "window.location = 'sale_pay_system_edit.php?lang=".LANG."&LID=".$arRes["LID"]."';"
		);
	}
*/


	$aContext = array(
		array(
			"TEXT" => GetMessage("SPSAN_ADD_NEW"),
			"TITLE" => GetMessage("SPSAN_ADD_NEW_ALT"),
			"LINK" => "sale_pay_system_edit.php?lang=".LANG,
			"ICON" => "btn_new",
			//"MENU" => $arDDMenu
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle(GetMessage("SALE_SECTION_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("SALE_F_PERSON_TYPE"),
	)
);

$oFilter->Begin();
?>
	<!--<tr>
		<td><?echo GetMessage("SALE_F_LANG");?>:</td>
		<td>
			<?echo CLang::SelectBox("filter_lang", $filter_lang, "(".GetMessage("SALE_ALL").")") ?>
		</td>
	</tr>-->
	<tr>
		<td><?echo GetMessage("SALE_F_ACTIVE")?>:</td>
		<td>
			<select name="filter_active">
				<option value="NOT_REF">(<?echo GetMessage("SALE_ALL")?>)</option>
				<option value="Y"<?if ($filter_active=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_active=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("SALE_F_PERSON_TYPE")?>:</td>
		<td>
			<select name="filter_person_type[]" multiple size=5>
				<option value="NOT_REF">(<?echo GetMessage("SALE_ALL")?>)</option>
				<?$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());
				while ($arPersonType = $dbPersonType->GetNext())
				{
					?><option value="<?=$arPersonType["ID"]?>"<?if (in_array($arPersonType["ID"], $filter_person_type)) echo " selected"?>><?=$arPersonType["NAME"]." (".implode(", ", $arPersonType["LIDS"]).")"?></option><?
				}
				?>
			</select>
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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

?>