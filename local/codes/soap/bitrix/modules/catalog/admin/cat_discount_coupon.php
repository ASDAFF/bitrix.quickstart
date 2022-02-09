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

$sTableID = "tbl_catalog_discount_coupon";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_id_start",
	"filter_id_end",
	"filter_discount_id",
	"filter_active",
	"filter_coupon",
	"filter_one_time",
	"filter_apply_time_start",
	"filter_apply_time_end",
	"filter_description"
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (!empty($filter_id_start))
	$arFilter[">=ID"] = $filter_id_start;
if (!empty($filter_id_end))
	$arFilter["<=ID"] = $filter_id_end;
if (!empty($filter_discount_id))
	$arFilter["DISCOUNT_ID"] = $filter_discount_id;
if (!empty($filter_active))
	$arFilter["ACTIVE"] = $filter_active;
if (!empty($filter_coupon))
	$arFilter["COUPON"] = $filter_coupon;
if (!empty($filter_one_time))
	$arFilter["ONE_TIME"] = $filter_one_time;
if (!empty($filter_apply_time_start))
	$arFilter[">=DATE_APPLY"] = $filter_apply_time_start;
if (!empty($filter_apply_time_end))
	$arFilter["<=DATE_APPLY"] = $filter_apply_time_end;
if (!empty($filter_description))
	$arFilter["%DESCRIPTION"] = $filter_description;

if ($lAdmin->EditAction() && !$bReadOnly)
{
	foreach ($_POST['FIELDS'] as $ID => $arFields)
	{
		$DB->StartTransaction();
		$ID = intval($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		if (!CCatalogDiscountCoupon::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$lAdmin->AddUpdateError($ex->GetString(), $ID);
			else
				$lAdmin->AddUpdateError(str_replace("#ID#", $ID, GetMessage("ERROR_UPDATE_DISCOUNT_CPN")), $ID);

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
		$dbResultList = CCatalogDiscountCoupon::GetList(
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

				if (!CCatalogDiscountCoupon::Delete($ID))
				{
					$DB->Rollback();

					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("ERROR_DELETE_DISCOUNT_CPN")), $ID);
				}

				$DB->Commit();

				break;

			case "activate":
			case "deactivate":

				$arFields = array(
					"ACTIVE" => (($_REQUEST['action']=="activate") ? "Y" : "N")
				);

				if (!CCatalogDiscountCoupon::Update($ID, $arFields))
				{
					if ($ex = $APPLICATION->GetException())
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					else
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("ERROR_UPDATE_DISCOUNT_CPN")), $ID);
				}

				break;
		}
	}
}

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"DISCOUNT_NAME","content"=>GetMessage("DSC_CPN_NAME"), "sort"=>"DISCOUNT_NAME", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("DSC_CPN_ACTIVE"), "sort"=>"ACTIVE", "default"=>true),
	array("id"=>"COUPON", "content"=>GetMessage("DSC_CPN_CPN"), "sort"=>"COUPON", "default"=>true),
	array("id"=>"DATE_APPLY", "content"=>GetMessage("DSC_CPN_DATE"), "sort"=>"DATE_APPLY", "default"=>true),
	array("id"=>"ONE_TIME", "content"=>GetMessage("DSC_CPN_TIME2"), "sort"=>"ONE_TIME", "default"=>true),
	array("id"=>"DESCRIPTION", "content"=>GetMessage("DSC_CPN_DESCRIPTION"), "sort"=>"", "default"=>false),
	array("id" => "MODIFIED_BY", "content" => GetMessage('DSC_MODIFIED_BY'), "sort" => "MODIFIED_BY", "default" => true),
	array("id" => "TIMESTAMP_X", "content" => GetMessage('DSC_TIMESTAMP_X'), "sort" => "TIMESTAMP_X", "default" => true),
	array("id" => "CREATED_BY", "content" => GetMessage('DSC_CREATED_BY'), "sort" => "CREATED_BY", "default" => false),
	array("id" => "DATE_CREATE", "content" => GetMessage('DSC_DATE_CREATE'), "sort" => "DATE_CREATE", "default" => false),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$arUserList = array();
$strNameFormat = CSite::GetNameFormat(true);

$arCouponType = array(
	'Y' => GetMessage('DSC_COUPON_TYPE_ONE_TIME'),
	'O' => GetMessage('DSC_COUPON_TYPE_ONE_ORDER'),
	'N' => GetMessage('DSC_COUPON_TYPE_NO_LIMIT'),
);

$dbResultList = CCatalogDiscountCoupon::GetList(
	array($by => $order),
	$arFilter,
	false,
	false,
	$arVisibleColumns
);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("DSC_NAV")));

while ($arDiscount = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arDiscount);

	$row->AddField("ID", $f_ID);
	$row->AddViewField("DISCOUNT_NAME", $f_DISCOUNT_NAME);

	$strCreatedBy = '';
	$strModifiedBy = '';
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

	$row->AddViewField("CREATED_BY", $strCreatedBy);
	$row->AddViewField("DATE_CREATE", $arDiscount['DATE_CREATE']);
	$row->AddViewField("MODIFIED_BY", $strModifiedBy);
	$row->AddViewField("TIMESTAMP_X", $arDiscount['TIMESTAMP_X']);

	if ($bReadOnly)
	{
		$row->AddCheckField("ACTIVE", false);
		$row->AddViewField("COUPON", $f_COUPON);
		$row->AddCalendarField("DATE_APPLY", false);
		$row->AddViewField("ONE_TIME", $arCouponType[$arDiscount['ONE_TIME']]);
		$row->AddViewField("DESCRIPTION", $f_DESCRIPTION);
	}
	else
	{
		$row->AddCheckField("ACTIVE");
		$row->AddInputField("COUPON", array("size" => "30"));
		$row->AddCalendarField("DATE_APPLY", array("size" => "10"));
		$row->AddSelectField("ONE_TIME", $arCouponType);
		$row->AddInputField("DESCRIPTION");
	}

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("DSC_UPDATE_ALT"), "ACTION"=>$lAdmin->ActionRedirect("cat_discount_coupon_edit.php?ID=".$f_ID."&lang=".LANG.GetFilterParams("filter_", false).""), "DEFAULT"=>true);

	if (!$bReadOnly)
	{
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("DSC_DELETE_ALT"), "ACTION"=>"if(confirm('".GetMessage('DSC_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
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
			"TEXT" => GetMessage("DSC_CPN_ADD"),
			"ICON" => "btn_new",
			"LINK" => "cat_discount_coupon_edit.php?lang=".LANG,
			"TITLE" => GetMessage("DSC_CPN_ADD_ALT")
		),
	);
	$lAdmin->AddAdminContextMenu($aContext);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("DSC_CPN_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("DSC_CPN_DISC"),
		GetMessage("DSC_CPN_ACT"),
		GetMessage("DSC_CPN_CPN"),
		GetMessage("DSC_CPN_TIME2"),
		GetMessage("DSC_CPN_DATE"),
		GetMessage("DSC_CPN_DESCRIPTION"),
	)
);

$oFilter->Begin();
?>
	<tr>
		<td>ID:</td>
		<td>
			<input type="text" name="filter_id_start" size="10" value="<?echo htmlspecialcharsex($filter_id_start)?>">
			...
			<input type="text" name="filter_id_end" size="10" value="<?echo htmlspecialcharsex($filter_id_end)?>">
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage("DSC_CPN_DISC") ?>:</td>
		<td>
			<select name="filter_discount_id">
				<option value=""><? echo GetMessage("DSC_CPN_ALL") ?></option>
				<?
				$dbDiscountList = CCatalogDiscount::GetList(
					array("NAME" => "ASC"),
					array(),
					false,
					false,
					array("ID", "SITE_ID", "NAME")
				);
				while ($arDiscountList = $dbDiscountList->Fetch())
				{
					?><option value="<? echo $arDiscountList["ID"] ?>"<?if ($filter_discount_id == $arDiscountList["ID"]) echo " selected";?>><? echo htmlspecialcharsbx("[".$arDiscountList["ID"]."] ".$arDiscountList["NAME"]." (".$arDiscountList["SITE_ID"].")") ?></option><?
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage("DSC_CPN_ACT") ?>:</td>
		<td>
			<select name="filter_active">
				<option value=""><? echo htmlspecialcharsex("(".GetMessage("DSC_CPN_ALL").")") ?></option>
				<option value="Y"<?if ($filter_active=="Y") echo " selected"?>><? echo htmlspecialcharsex(GetMessage("DSC_CPN_YES")) ?></option>
				<option value="N"<?if ($filter_active=="N") echo " selected"?>><? echo htmlspecialcharsex(GetMessage("DSC_CPN_NO")) ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage("DSC_CPN_CPN") ?>:</td>
		<td>
			<input type="text" name="filter_coupon" value="<?echo htmlspecialcharsbx($filter_coupon)?>" />
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage("DSC_CPN_TIME2") ?>:</td>
		<td>
			<select name="filter_one_time">
				<option value=""><? echo htmlspecialcharsex("(".GetMessage("DSC_CPN_ALL").")") ?></option>
				<option value="Y"<?if ($filter_one_time == "Y") echo " selected"; ?>><? echo htmlspecialcharsex(GetMessage("DSC_COUPON_TYPE_ONE_TIME")); ?></option>
				<option value="O"<?if ($filter_one_time == "O") echo " selected"; ?>><? echo htmlspecialcharsex(GetMessage("DSC_COUPON_TYPE_ONE_ORDER")); ?></option>
				<option value="N"<?if ($filter_one_time == "N") echo " selected"; ?>><? echo htmlspecialcharsex(GetMessage("DSC_COUPON_TYPE_NO_LIMIT")); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("DSC_CPN_DATE").":"?></td>
		<td><?echo CalendarPeriod("filter_apply_time_start", htmlspecialcharsex($filter_apply_time_start), "filter_apply_time_end", htmlspecialcharsex($filter_apply_time_end), "find_form")?></td>
	</tr>
	<tr>
		<td><? echo GetMessage("DSC_CPN_DESCRIPTION") ?>:</td>
		<td>
			<textarea name="filter_description"><?echo htmlspecialcharsbx($filter_description)?></textarea>
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