<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

IncludeModuleLangFile(__FILE__);

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!CBXFeatures::IsFeatureEnabled('SaleAccounts'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

ClearVars();

/*****************************************************************************/
/******************************** BUYERS *************************************/
/*****************************************************************************/

$APPLICATION->SetTitle(GetMessage("BUYER_TITLE"));

$rsSites = CSite::GetList($sby="sort", $sorder="desc", array("ACTIVE" => "Y"));
$arSites = array();
while ($arSite = $rsSites->Fetch())
	$arSites[$arSite["ID"]] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);

$arUsersGroups = array();
$dbGroups = CGroup::GetList(($b = "c_sort"), ($o = "asc"), array("ANONYMOUS" => "N"));
while ($arGroups = $dbGroups->Fetch())
	$arUsersGroups[] = $arGroups;

$sTableID = "tbl_sale_buyers";
$oSort = new CAdminSorting($sTableID, "LAST_LOGIN", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_universal",
	"filter_ID",
	"filter_login",
	"filter_mail",
	"filter_phone",
	"filter_lid",
	"find_last_login_1",
	"filter_mobile",
	"filter_dateauth",
	"filter_group",
	"find_last_login_2",
	"filter_price_all_from",
	"filter_price_all_to",
	"filter_quantity_all_from",
	"filter_quantity_all_to",
);
$lAdmin->InitFilter($arFilterFields);

/* COLLECTION FILTER */
$arFilter = array();

if (!isset($filter_currency) || $filter_currency == "")
{
	if (CModule::IncludeModule("currency"))
	{
		$arFilter["CURRENCY"] = CCurrency::GetBaseCurrency();
		$filter_currency = $arFilter["CURRENCY"];
	}
}
else
	$arFilter["CURRENCY"] = $filter_currency;

if (!isset($filter_lid) || $filter_lid == "")
{
	$rsSites = CSite::GetList($by2="SORT", $order2="ASC", array("ACTIVE" => "Y"));
	$arSite = $rsSites->Fetch();
	$arFilter["LID"] = $arSite["ID"];
	$filter_lid = $arFilter["LID"];
}
else
	$arFilter["LID"] = $filter_lid;

if (isset($filter_login))
	$arFilter["LOGIN"] = trim($filter_login);

if (isset($filter_ID))
	$arFilter["USER_ID"] = IntVal($filter_ID);

if (isset($filter_mail))
	$arFilter["EMAIL"] = trim($filter_mail);

if (isset($filter_phone))
	$arFilter["PERSONAL_PHONE"] = trim($filter_phone);

if (isset($filter_mobile))
	$arFilter["PERSONAL_MOBILE"] = trim($filter_mobile);

if (!empty($find_last_login_1))
{
	$date1_stm = MkDateTime(FmtDate($find_last_login_1,"D.M.Y"),"d.m.Y");
	if($date1_stm && strlen(trim($find_last_login_1))>0)
		$arFilter[">=LAST_LOGIN"] = trim($find_last_login_1);
}
if (!empty($find_last_login_2))
{
	if ($arDate = ParseDateTime($find_last_login_2, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($find_last_login_2) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$find_last_login_2 = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["<=LAST_LOGIN"] = $find_last_login_2;
	}
	else
	{
		$find_last_login_2 = "";
	}

}
if (!empty($find_last_order_date_1))
{
	$date1_stm = MkDateTime(FmtDate($find_last_order_date_1,"D.M.Y"),"d.m.Y");
	if($date1_stm && strlen(trim($find_last_order_date_1))>0)
		$arFilter[">=LAST_ORDER_DATE"] = trim($find_last_order_date_1);
}
if (!empty($find_last_order_date_2))
{
	if ($arDate = ParseDateTime($find_last_order_date_2, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($find_last_order_date_2) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$find_last_order_date_2 = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["<=LAST_ORDER_DATE"] = $find_last_order_date_2;
	}
	else
	{
		$find_last_order_date_2 = "";
	}
}
if (!empty($filter_register_date_1))
{
	$date1_stm = MkDateTime(FmtDate($filter_register_date_1,"D.M.Y"),"d.m.Y");
	if($date1_stm && strlen(trim($filter_register_date_1))>0)
		$arFilter[">=DATE_REGISTER"] = trim($filter_register_date_1);
}
if (!empty($filter_register_date_2))
{
	if ($arDate = ParseDateTime($filter_register_date_2, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($filter_register_date_2) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$filter_register_date_2 = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["<=DATE_REGISTER"] = $filter_register_date_2;
	}
	else
	{
		$filter_register_date_2 = "";
	}
}
if (isset($filter_group) && count($filter_group) > 0)
	$arFilter["GROUPS_ID"] = $filter_group;
if (isset($filter_universal) && strlen($filter_universal) > 0)
	$arFilter["%NAME_SEARCH"] = trim($filter_universal);
if (FloatVal($filter_price_all_from) > 0)
	$arFilter[">=ORDER_SUM"] = FloatVal($filter_price_all_from);
if (FloatVal($filter_price_all_to) > 0)
	$arFilter["<=ORDER_SUM"] = FloatVal($filter_price_all_to);
if (FloatVal($filter_quantity_all_from) > 0)
	$arFilter[">=ORDER_COUNT"] = FloatVal($filter_quantity_all_from);
if (FloatVal($filter_quantity_all_to) > 0)
	$arFilter["<ORDER_COUNT"] = FloatVal($filter_quantity_all_to);


if (isset($_GET["del_filter"]) && $_GET["del_filter"] == "Y")
{
	$arfiltertmp = $arFilter;
	$arFilter = array();
	$arFilter["LID"] = $arfiltertmp["LID"];
	$arFilter["CURRENCY"] = $arfiltertmp["CURRENCY"];
}

$arSitesShop = array();
foreach ($arSites as $key => $val)
{
	$site = COption::GetOptionString("sale", "SHOP_SITE_".$key, "");
	if ($key == $site)
		$arSitesShop[] = array("ID" => $key, "NAME" => $val["NAME"]);
}
if (empty($arSitesShop))
	$arSitesShop = $arSites;

$arCountry = GetCountryArray();
$arCountry["reference_id"] = array_flip($arCountry["reference_id"]);

/*
 * select all user (byuers)
 */
$arHeaders = array(
	array("id"=>"USER_ID", "content"=>"ID", "sort"=>"USER_ID"),
	array("id"=>"BUYER","content"=>GetMessage("BUYER_ROW_BUYER"), "sort"=>"NAME", "default"=>true),
	array("id"=>"LOGIN","content"=>GetMessage("BUYER_ROW_LOGIN"), "sort"=>"LOGIN"),
	array("id"=>"LAST_NAME","content"=>GetMessage("BUYER_ROW_LAST"), "sort"=>"LAST_NAME"),
	array("id"=>"NAME","content"=>GetMessage("BUYER_ROW_NAME"), "sort"=>"NAME"),
	array("id"=>"SECOND_NAME","content"=>GetMessage("BUYER_ROW_SECOND"), "sort"=>"SECOND_NAME"),
	array("id"=>"EMAIL","content"=>GetMessage("BUYER_ROW_MAIL"), "sort"=>"EMAIL", "default"=>true),
	array("id"=>"PERSONAL_PHONE","content"=>GetMessage("BUYER_ROW_PHONE"), "sort"=>"PERSONAL_PHONE", "default"=>true),
	array("id"=>"LAST_LOGIN","content"=>GetMessage('BUYER_ROW_LAST_LOGIN'), "sort"=>"LAST_LOGIN", "default"=>false),
	array("id"=>"DATE_REGISTER","content"=>GetMessage('BUYER_ROW_DATE_REGISTER'), "sort"=>"DATE_REGISTER", "default"=>true),
	array("id"=>"LAST_ORDER_DATE","content"=>GetMessage('BUYER_ROW_LAST_ORDER_DATE'), "sort"=>"LAST_ORDER_DATE", "default"=>true),
	array("id"=>"ORDER_COUNT","content"=>GetMessage('BUYER_ROW_COUNT_ORDER'), "sort"=>"ORDER_COUNT", "default"=>true, "align" => "right"),
	array("id"=>"ORDER_SUM","content"=>GetMessage('BUYER_ROW_SUM_ORDER'), "sort"=>"ORDER_SUM", "default"=>true, "align" => "right"),
	array("id"=>"GROUPS_ID","content"=>GetMessage('BUYER_ROW_GROUP')),
);
$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$arOrderBuyer[$by] = $order;

$dbUsersList = CSaleUser::GetBuyersList(
		$arOrderBuyer,
		$arFilter,
		false,
		array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
		array("ID", "LID", "ACTIVE", "DATE_REGISTER", "LOGIN", "EMAIL", "NAME", "LAST_NAME", "SECOND_NAME", "PERSONAL_PHONE", "USER_ID", "LAST_LOGIN", "ORDER_COUNT", "ORDER_SUM", "CURRENCY", "LAST_ORDER_DATE")
	);
$dbUsersList = new CAdminResult($dbUsersList, $sTableID);
$dbUsersList->NavStart();
$lAdmin->NavText($dbUsersList->GetNavPrint(GetMessage("BUYER_PRLIST")));

while ($arBuyers = $dbUsersList->Fetch())
{
	$row =& $lAdmin->AddRow($arBuyers["USER_ID"], $arBuyers, "sale_buyers_profile.php?USER_ID=".$arBuyers["USER_ID"]."&lang=".LANGUAGE_ID, GetMessage("BUYER_SUB_ACTION_PROFILE"));

	$profile = '<a href="sale_buyers_profile.php?USER_ID='.$arBuyers["USER_ID"].'&lang='.LANGUAGE_ID.'">'.$arBuyers["USER_ID"].'</a>';
	$row->AddField("USER_ID", $profile);

	if (floatVal($arBuyers["ORDER_SUM"]) > 0 && in_array("ORDER_SUM", $arVisibleColumns))
		$row->AddField("ORDER_SUM", SaleFormatCurrency($arBuyers["ORDER_SUM"], $arBuyers["CURRENCY"]));

	if (floatVal($arBuyers["ORDER_COUNT"]) <= 0)
		$row->AddField("ORDER_COUNT", '&nbsp;');

	if (in_array("GROUPS_ID", $arVisibleColumns))
	{
		$strUserGroup = '';
		$arUserGroups = CUser::GetUserGroup($arBuyers["USER_ID"]);

		foreach ($arUsersGroups as $arGroup)
		{
			if (in_array($arGroup["ID"], $arUserGroups))
				$strUserGroup .= htmlspecialcharsbx($arGroup["NAME"])."<br>";
		}
		$row->AddField("GROUPS_ID", $strUserGroup);
	}

	if (in_array("LID", $arVisibleColumns))
	{
		$row->AddField("LID", $arSites[$arBuyers["LID"]]["NAME"]);
	}

	/*BUYER*/
	$fieldBuyer = GetFormatedUserName($arBuyers["USER_ID"], false);
	$row->AddField("BUYER", $fieldBuyer);

	$arActions = array();
	$arActions[] = array("ICON"=>"view", "TEXT"=>GetMessage("BUYER_SUB_ACTION_PROFILE"), "ACTION"=>$lAdmin->ActionRedirect("sale_buyers_profile.php?USER_ID=".$arBuyers["USER_ID"]."&lang=".LANGUAGE_ID), "DEFAULT"=>true);

	foreach($arSitesShop as $val)
		$arActions[] = array("ICON"=>"view", "TEXT"=>GetMessage("BUYER_SUB_ACTION_ORDER")." [".$val["ID"]."]", "ACTION"=>$lAdmin->ActionRedirect("sale_order_new.php?user_id=".$arBuyers["USER_ID"]."&LID=".$val["ID"]."&lang=".LANGUAGE_ID));

	$row->AddActions($arActions);
}

$arFooterArray = array(
	array(
		"title" => GetMessage('MAIN_ADMIN_LIST_SELECTED'),
		"value" => $dbUsersList->SelectedRowsCount()
	)
);
$lAdmin->AddFooter($arFooterArray);


$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filters",
	array(
		"filter_universal" => GetMessage("BUYER_ROW_BUYER"),
		"filter_ID" => GetMessage("BUYER_F_ID"),
		"filter_login" => GetMessage("BUYER_F_LOGIN"),
		"filter_mail" => GetMessage("BUYER_F_MAIL"),
		"filter_phone" => GetMessage("BUYER_F_PHONE"),
		"find_last_login_1" => GetMessage("BUYER_F_DATE_AUTH"),
		"filter_price_all" => GetMessage("BUYER_F_PRICE_ALL"),
		"filter_quantity_all" => GetMessage("BUYER_F_QUANTITY_ALL"),
		"find_last_order_date" => GetMessage("BUYER_F_LAST_ORDER_DATE"),
		"filter_register_date" => GetMessage("BUYER_ROW_DATE_REGISTER"),
		"filter_group" => GetMessage("BUYER_F_GROUP"),
		"filter_currency" => GetMessage("BUYER_F_CURRENCY"),
		"filter_lid" => GetMessage("BUYER_ORDERS_LID"),
	)
);

$oFilter->AddPreset(array(
		"ID" => "find_best",
		"NAME" => GetMessage("BUYER_F_BEST"),
		"FIELDS" => array(
			"filter_universal" => "",
			),
		"SORT_FIELD" => array("ORDER_SUM" => "DESC"),

	));
$oFilter->AddPreset(array(
		"ID" => "find_throw",
		"NAME" => GetMessage("BUYER_F_BUYERS_NEW"),
		"FIELDS" => array(
			"filter_register_date_1_FILTER_PERIOD" => "month",
			"filter_register_date_1_FILTER_DIRECTION" => "current"
			),
		"SORT_FIELD" => array("DATE_REGISTER" => "DESC"),
	));

$oFilter->Begin();
?>
	<tr>
		<td><?=GetMessage('BUYER_ROW_BUYER')?>:</td>
		<td>
			<input type="text" name="filter_universal" value="<?echo htmlspecialcharsbx($filter_universal)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><?=GetMessage('BUYER_F_ID')?>:</td>
		<td>
			<?echo FindUserID("filter_ID", $filter_ID, "", "find_form");?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_LOGIN");?>:</td>
		<td>
			<input type="text" name="filter_login" value="<?echo htmlspecialcharsbx($filter_login)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_MAIL");?>:</td>
		<td>
			<input type="text" name="filter_mail" value="<?echo htmlspecialcharsbx($filter_mail)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_PHONE");?>:</td>
		<td>
			<input type="text" name="filter_phone" value="<?echo htmlspecialcharsbx($filter_phone)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_DATE_AUTH").":"?></td>
		<td><?echo CalendarPeriod("find_last_login_1", htmlspecialcharsbx($find_last_login_1), "find_last_login_2", htmlspecialcharsbx($find_last_login_2), "find_form","Y")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_PRICE_ALL");?>:</td>
		<td>
			<?echo GetMessage("BUYER_F_FROM");?>
			<input type="text" name="filter_price_all_from" id="filter_price_all_from" value="<?echo (IntVal($filter_price_all_from) > 0) ? IntVal($filter_price_all_from):""?>" size="10">
			<?echo GetMessage("BUYER_F_TO");?>
			<input type="text" name="filter_price_all_to" id="filter_price_all_to" value="<?echo (IntVal($filter_price_all_to)>0)?IntVal($filter_price_all_to):""?>" size="10">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_QUANTITY_ALL");?>:</td>
		<td>
			<?echo GetMessage("BUYER_F_FROM");?>
			<input type="text" name="filter_quantity_all_from" value="<?echo (IntVal($filter_quantity_all_from) > 0) ? IntVal($filter_quantity_all_from):""?>" size="10">
			<?echo GetMessage("BUYER_F_TO");?>
			<input type="text" name="filter_quantity_all_to" value="<?echo (IntVal($filter_quantity_all_to)>0)?IntVal($filter_quantity_all_to):""?>" size="10">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_LAST_ORDER_DATE").":"?></td>
		<td><?echo CalendarPeriod("find_last_order_date_1", htmlspecialcharsbx($find_last_order_date_1), "find_last_order_date_2", htmlspecialcharsbx($find_last_order_date_2), "find_form","Y")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_ROW_DATE_REGISTER").":"?></td>
		<td><?echo CalendarPeriod("filter_register_date_1", htmlspecialcharsbx($filter_register_date_1), "filter_register_date_2", htmlspecialcharsbx($filter_register_date_2), "find_form","Y")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_GROUP");?>:</td>
		<td>
			<?
			$z = CGroup::GetDropDownList("AND ID!=2");
			echo SelectBoxM("filter_group[]", $z, $filter_group, "", false, 5);
			?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_F_CURRENCY");?>:</td>
		<td>
			<?echo CCurrency::SelectBox("filter_currency", $filter_currency, false, True, "", "") ?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("BUYER_ORDERS_LID");?>:</td>
		<td>
			<select name="filter_lid">
				<?
				$dbSitesList = CSite::GetList($by="sort", $order="asc", array("ACTIVE" => "Y"));
				while ($arSitesList = $dbSitesList->Fetch())
				{
					?><option value="<?= htmlspecialcharsbx($arSitesList["LID"])?>"<?if ($arSitesList["LID"] == $filter_lid) echo " selected";?>><?= htmlspecialcharsex($arSitesList["NAME"]) ?>&nbsp;[<?= htmlspecialcharsex($arSitesList["LID"]) ?>]</option><?
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