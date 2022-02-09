<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

global $APPLICATION;

/*
* B_ADMIN_SUBCOUPONS
* if defined and equal 1 - working, another die
* B_ADMIN_SUBCOUPONS_LIST - true/false
* if not defined - die
* if equal true - get list mode
* 	include prolog and epilog
* other - get simple html
*
* need variables
* 		$strSubElementAjaxPath - path for ajax
*		$intDiscountID - ID for filter
*		$strSubTMP_ID - string identifier for link with new product ($intSubPropValue = 0, in edit form send -1)
*
*
*created variables
*		$arSubElements - array subelements for product with ID = 0
*/
if ((false == defined('B_ADMIN_SUBCOUPONS')) || (1 != B_ADMIN_SUBCOUPONS))
	return '';
if (false == defined('B_ADMIN_SUBCOUPONS_LIST'))
	return '';

$strSubElementAjaxPath = trim($strSubElementAjaxPath);

if ($_REQUEST['mode']=='list' || $_REQUEST['mode']=='frame')
	CFile::DisableJSFunction(true);

$intDiscountID = intval($intDiscountID);
$strSubTMP_ID = intval($strSubTMP_ID);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/catalog/admin/cat_discount_coupon.php");
IncludeModuleLangFile(__FILE__);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/general/subelement.php');

$sTableID = "tbl_catalog_sub_coupon_".md5($strSubIBlockType.".".$intSubIBlockID);

$arHideFields = array('DISCOUNT_ID');
$lAdmin = new CAdminSubList($sTableID, false, $strSubElementAjaxPath, $arHideFields);

$arFilterFields = Array(
	"find_discount_id",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
	"DISCOUNT_ID" => $intDiscountID,
);

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_discount')))
	return '';

$boolCouponsReadOnly = (isset($boolCouponsReadOnly) && false === $boolCouponsReadOnly ? false : true);

if ($lAdmin->EditAction() && !$boolCouponsReadOnly)
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


if (($arID = $lAdmin->GroupAction()) && !$boolCouponsReadOnly)
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

$CAdminCalendar_ShowScript = '';
if (true == B_ADMIN_SUBCOUPONS_LIST)
	$CAdminCalendar_ShowScript = CAdminCalendar::ShowScript();

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
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

if (!((false == B_ADMIN_SUBCOUPONS_LIST) && ($bCopy)))
{
	if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "excel")
		$arNavParams = false;
	else
		$arNavParams = array("nPageSize"=>CAdminSubResult::GetNavSize($sTableID, 20, $lAdmin->GetListUrl(true)));

	$dbResultList = CCatalogDiscountCoupon::GetList(
		array($by => $order),
		$arFilter,
		false,
		$arNavParams,
		$arVisibleColumns
	);
	$dbResultList = new CAdminSubResult($dbResultList, $sTableID, $lAdmin->GetListUrl(true));
	$dbResultList->NavStart();
	$lAdmin->NavText($dbResultList->GetNavPrint(htmlspecialcharsbx(GetMessage("DSC_NAV"))));

	while ($arCouponDiscount = $dbResultList->NavNext(true, "f_"))
	{
		$edit_url = '/bitrix/admin/cat_subcoupon_edit.php?ID='.$arCouponDiscount['ID'].'&DISCOUNT_ID='.$intDiscountID.'&lang='.LANGUAGE_ID.'&TMP_ID='.$strSubTMP_ID;

		$row =& $lAdmin->AddRow($f_ID, $arCouponDiscount, $edit_url, '', true);

		$row->AddField("ID", $f_ID);
		$row->AddViewField("DISCOUNT_NAME", $f_DISCOUNT_NAME);

		$strCreatedBy = '';
		$strModifiedBy = '';
		$arCouponDiscount['CREATED_BY'] = intval($arCouponDiscount['CREATED_BY']);
		if (0 < $arCouponDiscount['CREATED_BY'])
		{
			if (!array_key_exists($arCouponDiscount['CREATED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arCouponDiscount['CREATED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arCouponDiscount['CREATED_BY']]))
				$strCreatedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arCouponDiscount['CREATED_BY'].'">'.$arUserList[$arCouponDiscount['CREATED_BY']].'</a>';
		}
		$arCouponDiscount['MODIFIED_BY'] = intval($arCouponDiscount['MODIFIED_BY']);
		if (0 < $arCouponDiscount['MODIFIED_BY'])
		{
			if (!array_key_exists($arCouponDiscount['MODIFIED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arCouponDiscount['MODIFIED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arCouponDiscount['MODIFIED_BY']]))
				$strModifiedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arCouponDiscount['MODIFIED_BY'].'">'.$arUserList[$arCouponDiscount['MODIFIED_BY']].'</a>';
		}

		$row->AddViewField("CREATED_BY", $strCreatedBy);
		$row->AddViewField("DATE_CREATE", $arCouponDiscount['DATE_CREATE']);
		$row->AddViewField("MODIFIED_BY", $strModifiedBy);
		$row->AddViewField("TIMESTAMP_X", $arCouponDiscount['TIMESTAMP_X']);

		if ($boolCouponsReadOnly)
		{
			$row->AddCheckField("ACTIVE", false);
			$row->AddViewField("COUPON", $f_COUPON);
			$row->AddCalendarField("DATE_APPLY", false);
			$row->AddViewField("ONE_TIME", $arCouponType[$arCouponDiscount['ONE_TIME']]);
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
		$arActions[] = array(
			"ICON" => "edit",
			"TEXT" => GetMessage("DSC_UPDATE_ALT"),
			"DEFAULT" => true,
			"ACTION"=>"(new BX.CAdminDialog({
				'content_url': '/bitrix/admin/cat_subcoupon_edit.php?ID=".$arCouponDiscount['ID']."&DISCOUNT_ID=".$intDiscountID."&lang=".LANGUAGE_ID."&TMP_ID=".$strSubTMP_ID."',
				'content_post': 'bxpublic=Y',
				'draggable': true,
				'resizable': true,
				'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
			})).Show();",
		);

		if (!$boolCouponsReadOnly)
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

	if (!$boolCouponsReadOnly)
	{
		$lAdmin->AddGroupActionTable(
			array(
				"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
				"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
				"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
			)
		);
	}

?><script type="text/javascript">
function ShowNewCoupon(id)
{
	var PostParams = {
		'lang': '<? echo LANGUAGE_ID; ?>',
		'DISCOUNT_ID': id,
		'MULTI': 'N',
		'ID': 0,
		'bxpublic': 'Y',
		'sessid': BX.bitrix_sessid()
	};
	(new BX.CAdminDialog({
		'content_url': '/bitrix/admin/cat_subcoupon_edit.php',
		'content_post': PostParams,
		'draggable': true,
		'resizable': true,
		'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
	})).Show();
}

function ShowNewMultiCoupons(id)
{
	var PostParams = {
		'lang': '<? echo LANGUAGE_ID; ?>',
		'DISCOUNT_ID': id,
		'MULTI': 'Y',
		'ID': 0,
		'bxpublic': 'Y',
		'sessid': BX.bitrix_sessid()
	};
	(new BX.CAdminDialog({
		'content_url': '/bitrix/admin/cat_subcoupon_edit.php',
		'content_post': PostParams,
		'draggable': true,
		'resizable': false,
		'buttons': [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel]
	})).Show();
}
</script><?

	$aContext = array();
	if (!$boolCouponsReadOnly)
	{
		if (0 < $intDiscountID)
		{
			$arAddMenu = array();
			$arAddMenu[] = array(
				"TEXT" => GetMessage("BT_CAT_DISC_COUPON_LIST_ADD_ONE_COUPON"),
				"LINK" => "javascript:ShowNewCoupon(".$intDiscountID.")",
				"TITLE" => GetMessage("BT_CAT_DISC_COUPON_LIST_ADD_ONE_COUPON_TITLE")
			);
			$arAddMenu[] = array(
				"TEXT" => GetMessage("BT_CAT_DISC_COUPON_LIST_ADD_MULTI_COUPON"),
				"LINK" => "javascript:ShowNewMultiCoupons(".$intDiscountID.")",
				"TITLE" => GetMessage("BT_CAT_DISC_COUPON_LIST_ADD_MULTI_COUPON_TITLE")
			);

			$aContext[] = array(
				"TEXT" => GetMessage("DSC_CPN_ADD"),
				"ICON" => "btn_new",
				"MENU" => $arAddMenu,
			);
		}
	}

	$aContext[] = array(
		"ICON"=>"btn_sub_refresh",
		"TEXT"=>htmlspecialcharsex(GetMessage('BT_CAT_DISC_COUPON_LIST_REFRESH')),
		"LINK" => "javascript:".$lAdmin->ActionAjaxReload($lAdmin->GetListUrl(true)),
		"TITLE"=>GetMessage("BT_CAT_DISC_COUPON_LIST_REFRESH_TITLE"),
	);

	$lAdmin->AddAdminContextMenu($aContext);

	$lAdmin->CheckListMode();

	if (true == B_ADMIN_SUBCOUPONS_LIST)
	{
		echo $CAdminCalendar_ShowScript;
	}

	$lAdmin->DisplayList(B_ADMIN_SUBCOUPONS_LIST);
}
else
{
	ShowMessage(GetMessage('BT_CAT_DISC_COUPON_LIST_SHOW_AFTER_COPY'));
}
?>