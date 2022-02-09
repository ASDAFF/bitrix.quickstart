<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_discount')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_discount');

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

$sTableID = "tbl_catalog_discount";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_site_id",
	"filter_active",
	"filter_date_active_from",
	"filter_date_active_to",
	"filter_name",
	"filter_coupon",
	"filter_renewal",
	"filter_value_start",
	"filter_value_end",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_site_id) > 0 && $filter_site_id != "NOT_REF") $arFilter["SITE_ID"] = $filter_site_id;
if (strlen($filter_active) > 0) $arFilter["ACTIVE"] = $filter_active;
if (strlen($filter_date_active_from) > 0) $arFilter["!>ACTIVE_FROM"] = $filter_date_active_from;
if (strlen($filter_date_active_to) > 0) $arFilter["!<ACTIVE_TO"] = $filter_date_active_to;
if (strlen($filter_name) > 0) $arFilter["~NAME"] = $filter_name;
if (strlen($filter_coupon) > 0) $arFilter["COUPON"] = $filter_coupon;
if (strlen($filter_renewal) > 0) $arFilter["RENEWAL"] = $filter_renewal;
if (isset($filter_value_start) && doubleval($filter_value_start) > 0)
	$arFilter[">=VALUE"] = doubleval($filter_value_start);
if (isset($filter_value_end) && doubleval($filter_value_end) > 0)
	$arFilter["<=VALUE"] = doubleval($filter_value_end);

if (!$bReadOnly && $lAdmin->EditAction())
{
	foreach ($_POST['FIELDS'] as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (array_key_exists('CONDITIONS', $arFields))
			unset($arFields['CONDITIONS']);

		if (!CCatalogDiscount::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(str_replace("#ID#", $ID, GetMessage("ERROR_UPDATE_DISCOUNT")), $ID);

			$DB->Rollback();
		}

		$DB->Commit();
	}
}


if (!$bReadOnly && ($arID = $lAdmin->GroupAction()))
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = array();
		$dbResultList = CCatalogDiscount::GetList(
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

				if (!CCatalogDiscount::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("ERROR_DELETE_DISCOUNT")), $ID);
				}

				$DB->Commit();

				break;

			case "activate":
			case "deactivate":

				$arFields = array(
					"ACTIVE" => (($_REQUEST['action']=="activate") ? "Y" : "N")
				);

				if (!CCatalogDiscount::Update($ID, $arFields))
				{
					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("ERROR_UPDATE_DISCOUNT")), $ID);
				}

				break;
		}
	}
}

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("DSC_NAME"), "sort"=>"NAME", "default"=>true),
	array("id"=>"VALUE", "content"=>GetMessage("DSC_VALUE"), "sort"=>"", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("DSC_ACT"), "sort"=>"ACTIVE", "default"=>true),
	array("id"=>"ACTIVE_FROM", "content"=>GetMessage('DSC_PERIOD_FROM'), "sort"=>"ACTIVE_FROM", "default"=>true),
	array("id"=>"ACTIVE_TO", "content"=>GetMessage("DSC_PERIOD_TO2"),  "sort"=>"ACTIVE_TO", "default"=>true),
	array("id" => "PRIORITY", "content" => GetMessage('DSC_PRIORITY'), "sort" => "PRIORITY", "default" => true),
	array("id"=>"SORT", "content"=>GetMessage("DSC_SORT"), "sort"=>"SORT", "default"=>true),
	array("id"=>"SITE_ID","content"=>GetMessage("DSC_SITE"), "sort"=>"SITE_ID", "default"=>true),
	array("id" => "MODIFIED_BY", "content" => GetMessage('DSC_MODIFIED_BY'), "sort" => "MODIFIED_BY", "default" => true),
	array("id" => "TIMESTAMP_X", "content" => GetMessage('DSC_TIMESTAMP_X'), "sort" => "TIMESTAMP_X", "default" => true),
	array("id" => "MAX_DISCOUNT", "content" => GetMessage('DSC_MAX_DISCOUNT'), "sort" => "MAX_DISCOUNT", "default" => false),
	array("id"=>"RENEWAL", "content"=>GetMessage("DSC_REN"), "sort"=>"RENEWAL", "default"=>false),
	array("id" => "CREATED_BY", "content" => GetMessage('DSC_CREATED_BY'), "sort" => "CREATED_BY", "default" => false),
	array("id" => "DATE_CREATE", "content" => GetMessage('DSC_DATE_CREATE'), "sort" => "DATE_CREATE", "default" => false),
	array("id" => "XML_ID", "content" => GetMessage('DSC_XML_ID'), "sort" => "XML_ID", "default" => false),
	array("id" => "CURRENCY", "content" => GetMessage('DSC_CURRENCY'), "sort" => "CURRENCY", "default" => false),
	array("id" => "LAST_DISCOUNT", "content" => GetMessage('DSC_LAST_DISCOUNT'), "sort" => "LAST_DISCOUNT", "default" => false),
));

$arSelectFields = $lAdmin->GetVisibleHeaderColumns();
if (!in_array('ID', $arSelectFields))
	$arSelectFields[] = 'ID';

$arSelectFieldsMap = array();
foreach ($arSelectFields as &$strOneFieldName)
{
	$arSelectFieldsMap[$strOneFieldName] = true;
}
if (isset($strOneFieldName))
	unset($strOneFieldName);

if (in_array('VALUE', $arSelectFields) || in_array('MAX_DISCOUNT', $arSelectFields))
{
	$arSelectFields[] = 'VALUE_TYPE';
	$arSelectFields[] = 'CURRENCY';
}

$arSiteList = array();
$arSiteLinkList = array();
if (array_key_exists('SITE_ID', $arSelectFieldsMap))
{
	$rsSites = CSite::GetList(($by2 = 'sort'),($order2 = 'asc'));
	while ($arSite = $rsSites->Fetch())
	{
		$arSiteList[$arSite['LID']] = $arSite['LID'];
		$arSiteLinkList[$arSite['LID']] = '<a href="/bitrix/admin/site_edit.php?lang='.urlencode(LANGUAGE_ID).'&LID='.urlencode($arSite['LID']).'" title="'.GetMessage('BT_CAT_DISCOUNT_ADM_MESS_SITE_ID').'">'.htmlspecialcharsex($arSite['LID']).'</a>';
	}
}

$arCurrencyList = array();
if (array_key_exists('CURRENCY', $arSelectFieldsMap))
{
	$rsCurrencies = CCurrency::GetList(($by2 = 'sort'),($order2 = 'asc'));
	while ($arCurrency = $rsCurrencies->Fetch())
	{
		$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
	}
}

$arSelectFields = array_values($arSelectFields);
$dbResultList = CCatalogDiscount::GetList(
	array($by => $order),
	$arFilter,
	false,
	false,
	$arSelectFields
);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("DSC_NAV")));

$arUserList = array();
$strNameFormat = CSite::GetNameFormat(true);

while ($arDiscount = $dbResultList->Fetch())
{
	$row = &$lAdmin->AddRow($arDiscount['ID'], $arDiscount);

	$strCreatedBy = '';
	$strModifiedBy = '';
	if (array_key_exists('CREATED_BY', $arSelectFieldsMap))
	{
		$arDiscount['CREATED_BY'] = intval($arDiscount['CREATED_BY']);
		if (0 < $arDiscount['CREATED_BY'])
		{
			if (!array_key_exists($arDiscount['CREATED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arDiscount['CREATED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arDiscount['CREATED_BY']]))
				$strCreatedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arDiscount['CREATED_BY'].'">'.$arUserList[$arDiscount['CREATED_BY']].'</a>';
		}
	}
	if (array_key_exists('MODIFIED_BY', $arSelectFieldsMap))
	{
		$arDiscount['MODIFIED_BY'] = intval($arDiscount['MODIFIED_BY']);
		if (0 < $arDiscount['MODIFIED_BY'])
		{
			if (!array_key_exists($arDiscount['MODIFIED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arDiscount['MODIFIED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arDiscount['MODIFIED_BY']]))
				$strModifiedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arDiscount['MODIFIED_BY'].'">'.$arUserList[$arDiscount['MODIFIED_BY']].'</a>';
		}
	}

	if (array_key_exists('CREATED_BY', $arSelectFieldsMap))
		$row->AddViewField("CREATED_BY", $strCreatedBy);
	if (array_key_exists('DATE_CREATE', $arSelectFieldsMap))
		$row->AddViewField("DATE_CREATE", $arDiscount['DATE_CREATE']);
	if (array_key_exists('MODIFIED_BY', $arSelectFieldsMap))
		$row->AddViewField("MODIFIED_BY", $strModifiedBy);
	if (array_key_exists('TIMESTAMP_X', $arSelectFieldsMap))
		$row->AddViewField("TIMESTAMP_X", $arDiscount['TIMESTAMP_X']);

	$row->AddViewField("ID", '<a href="/bitrix/admin/cat_discount_edit.php?lang='.urlencode(LANGUAGE_ID).'&ID='.$arDiscount["ID"].'">'.$arDiscount["ID"].'</a>');

	if ($bReadOnly)
	{
		if (array_key_exists('SITE_ID', $arSelectFieldsMap))
			$row->AddViewField('SITE_ID',$arSiteLinkList[$arDiscount['SITE_ID']]);
		if (array_key_exists('ACTIVE_FROM', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_FROM", false);
		if (array_key_exists('ACTIVE_TO', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_TO", false);
		if (array_key_exists('ACTIVE', $arSelectFieldsMap))
			$row->AddCheckField("ACTIVE", false);
		if (array_key_exists('NAME', $arSelectFieldsMap))
			$row->AddInputField("NAME", false);
		if (array_key_exists('SORT', $arSelectFieldsMap))
			$row->AddInputField("SORT", false);
		if (array_key_exists('XML_ID', $arSelectFieldsMap))
			$row->AddInputField("XML_ID", false);
		if (array_key_exists('CURRENCY', $arSelectFieldsMap))
			$row->AddViewField("CURRENCY", $arDiscount['CURRENCY']);
		if (array_key_exists('PRIORITY', $arSelectFieldsMap))
			$row->AddInputField("PRIORITY", false);
		if (array_key_exists('LAST_DISCOUNT', $arSelectFieldsMap))
			$row->AddCheckField("LAST_DISCOUNT", false);
	}
	else
	{
		if (array_key_exists('SITE_ID', $arSelectFieldsMap))
		{
			$row->AddSelectField("SITE_ID", $arSiteList);
			$row->AddViewField('SITE_ID',$arSiteLinkList[$arDiscount['SITE_ID']]);
		}
		if (array_key_exists('ACTIVE_FROM', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_FROM");
		if (array_key_exists('ACTIVE_TO', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_TO");
		if (array_key_exists('ACTIVE', $arSelectFieldsMap))
			$row->AddCheckField("ACTIVE");
		if (array_key_exists('NAME', $arSelectFieldsMap))
			$row->AddInputField("NAME", array("size" => "30"));
		if (array_key_exists('SORT', $arSelectFieldsMap))
			$row->AddInputField("SORT", array("size" => "3"));
		if (array_key_exists('XML_ID', $arSelectFieldsMap))
			$row->AddInputField("XML_ID", array("size" => "20"));
		if (array_key_exists('CURRENCY', $arSelectFieldsMap))
			$row->AddSelectField("CURRENCY", $arCurrencyList);
		if (array_key_exists('PRIORITY', $arSelectFieldsMap))
			$row->AddInputField("PRIORITY");
		if (array_key_exists('LAST_DISCOUNT', $arSelectFieldsMap))
			$row->AddCheckField("LAST_DISCOUNT");
	}

	if (array_key_exists('VALUE', $arSelectFieldsMap))
	{
		$strDiscountValue = '';
		if ($arDiscount["VALUE_TYPE"]=="P")
		{
			$strDiscountValue = roundEx($arDiscount["VALUE"], CATALOG_VALUE_PRECISION)."%";
		}
		elseif ($arDiscount["VALUE_TYPE"]=="S")
		{
			$strDiscountValue = '= '.FormatCurrency($arDiscount["VALUE"], $arDiscount["CURRENCY"]);
		}
		else
		{
			$strDiscountValue = FormatCurrency($arDiscount["VALUE"], $arDiscount["CURRENCY"]);
		}
		$row->AddViewField("VALUE", $strDiscountValue);
	}

	if (array_key_exists('MAX_DISCOUNT', $arSelectFieldsMap))
	{
		$row->AddViewField("MAX_DISCOUNT", (0 < $arDiscount['MAX_DISCOUNT'] ? FormatCurrency($arDiscount['MAX_DISCOUNT'], $arDiscount["CURRENCY"]) : GetMessage('DSC_MAX_DISCOUNT_UNLIM')));
	}

	if (array_key_exists('RENEWAL', $arSelectFieldsMap))
		$row->AddCheckField("RENEWAL", false);

	$arActions = array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("DSC_UPDATE_ALT"), "ACTION"=>$lAdmin->ActionRedirect("/bitrix/admin/cat_discount_edit.php?ID=".$arDiscount['ID']."&lang=".LANGUAGE_ID.GetFilterParams("filter_", false).""), "DEFAULT"=>true);

	if (!$bReadOnly)
	{
		$arActions[] = array(
			"ICON" => "copy",
			"DEFAULT" => false,
			"TEXT" => GetMessage("BT_CAT_DISCOUNT_ADM_CONT_COPY"),
			"ACTION"=>$lAdmin->ActionRedirect("/bitrix/admin/cat_discount_edit.php?ID=".$arDiscount['ID'].'&action=copy&lang='.urlencode(LANGUAGE_ID))
		);

		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("DSC_DELETE_ALT"), "ACTION"=>"if(confirm('".GetMessage('DSC_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($arDiscount['ID'], "delete"));
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

$aContext = array();
if (!$bReadOnly)
{
	$aContext = array(
		array(
			"TEXT" => GetMessage("CDAN_ADD_NEW"),
			"ICON" => "btn_new",
			"LINK" => "cat_discount_edit.php?lang=".LANG,
			"TITLE" => GetMessage("CDAN_ADD_NEW_ALT")
		),
	);
}
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("DISCOUNT_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("DSC_ACTIVE"),
		GetMessage("DSC_PERIOD"),
		GetMessage("DSC_NAME"),
		GetMessage("DSC_COUPON"),
		GetMessage("DSC_RENEW"),
		GetMessage("DSC_VALUE"),
	)
);

$oFilter->Begin();
?>
	<tr>
		<td><?= GetMessage("DSC_SITE") ?>:</td>
		<td>
			<?echo CSite::SelectBox("filter_site_id", $filter_site_id, "(".GetMessage("DSC_ALL").")"); ?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("DSC_ACTIVE") ?>:</td>
		<td>
			<select name="filter_active">
				<option value=""><?= htmlspecialcharsex("(".GetMessage("DSC_ALL").")") ?></option>
				<option value="Y"<?if ($filter_active=="Y") echo " selected"?>><?= htmlspecialcharsex(GetMessage("DSC_YES")) ?></option>
				<option value="N"<?if ($filter_active=="N") echo " selected"?>><?= htmlspecialcharsex(GetMessage("DSC_NO")) ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("DSC_PERIOD") ?> (<?= CSite::GetDateFormat("SHORT") ?>):</td>
		<td>
			<?echo CalendarPeriod("filter_date_active_from", $filter_date_active_from, "filter_date_active_to", $filter_date_active_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("DSC_NAME") ?>:</td>
		<td>
			<input type="text" name="filter_name" size="50" value="<?echo htmlspecialcharsex($filter_name)?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("DSC_COUPON") ?>:</td>
		<td>
			<input type="text" name="filter_coupon" size="50" value="<?echo htmlspecialcharsex($filter_coupon)?>" size="30">
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("DSC_RENEW") ?>:</td>
		<td>
			<select name="filter_renewal">
				<option value=""><?= htmlspecialcharsex("(".GetMessage("DSC_ALL").")") ?></option>
				<option value="Y"<?if ($filter_renewal=="Y") echo " selected"?>><?= htmlspecialcharsex(GetMessage("DSC_YES")) ?></option>
				<option value="N"<?if ($filter_renewal=="N") echo " selected"?>><?= htmlspecialcharsex(GetMessage("DSC_NO")) ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage('DSC_VALUE'); ?>:</td>
		<td>
			<input type="text" name="filter_value_start" value="<?echo htmlspecialcharsex($filter_value_start)?>" size="15">
			...
			<input type="text" name="filter_value_end" value="<?echo htmlspecialcharsex($filter_value_end)?>" size="15">
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