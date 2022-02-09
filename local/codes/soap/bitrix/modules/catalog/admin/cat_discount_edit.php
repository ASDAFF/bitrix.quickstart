<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_discount')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_discount');
$boolShowCoupons = true;

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

if (!empty($return_url) && strtolower(substr($return_url, strlen($APPLICATION->GetCurPage())))==strtolower($APPLICATION->GetCurPage()))
	$return_url = "";

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CDEN_TAB_DISCOUNT"), "ICON" => "catalog", "TITLE" => GetMessage("CDEN_TAB_DISCOUNT_DESCR")),
	array("DIV" => "edit4", "TAB" => GetMessage("BT_CAT_DISCOUNT_EDIT_TAB_NAME_CONDITIONS"), "ICON" => "catalog", "TITLE" => GetMessage("BT_CAT_DISCOUNT_EDIT_TAB_TITLE_CONDITIONS")),
	array("DIV" => "edit2", "TAB" => GetMessage("CDEN_TAB_DISCOUNT_PAR"), "ICON" => "catalog", "TITLE" => GetMessage("CDEN_TAB_DISCOUNT_PAR_DESCR")),
	array("DIV" => "edit3", "TAB" => GetMessage("BT_CAT_DISCOUNT_EDIT_TAB_NAME_COUPONS"), "ICON" => "catalog", "TITLE" => GetMessage("BT_CAT_DISCOUNT_EDIT_TAB_TITLE_COUPONS")),
	array("DIV" => "edit5", "TAB" => GetMessage("BT_CAT_DISCOUNT_EDIT_TAB_NAME_MISC"), "ICON" => "catalog", "TITLE" => GetMessage("BT_CAT_DISCOUNT_EDIT_TAB_TITLE_MISC")),
);

$tabControl = new CAdminForm("fdiscount_edit", $aTabs);

$arCouponTypeList = array(
	'Y' => GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COUPONS_TYPE_ONE_TIME3'),
	'O' => GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COUPONS_TYPE_ONE_ORDER'),
	'N' => GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COUPONS_TYPE_NO_LIMIT'),
);

$errorMessage = "";
$bVarsFromForm = false;
$boolCondParseError = false;
$boolCouponAdd = false;

$ID = intval($ID);

$boolCopy = false;
if (0 < $ID)
{
	$boolCopy = (isset($_REQUEST['action']) && 'copy' == $_REQUEST['action']);
}

if (!$bReadOnly && $_SERVER['REQUEST_METHOD']=="POST" && strlen($Update)>0 && check_bitrix_sessid())
{
	$obCond2 = new CCatalogCondTree();

	$boolCond = $obCond2->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, array());
	if (!$boolCond)
	{
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString()."<br>";
		else
			$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_UPDATE')) : GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_ADD'))."<br>";
		$bVarsFromForm = true;
	}
	else
	{
		$boolCond = false;
		if (isset($_POST['CONDITIONS']) && isset($_POST['CONDITIONS_CHECK']))
		{
			if (is_string($_POST['CONDITIONS']) && is_string($_POST['CONDITIONS_CHECK']) && md5($_POST['CONDITIONS']) == $_POST['CONDITIONS_CHECK'])
			{
				$CONDITIONS = base64_decode($_POST['CONDITIONS']);
				if (CheckSerializedData($CONDITIONS))
				{
					$CONDITIONS = unserialize($CONDITIONS);
					$boolCond = true;
				}
				else
				{
					$boolCondParseError = true;
				}
			}
		}

		if (!$boolCond)
			$CONDITIONS = $obCond2->Parse();
		if (empty($CONDITIONS))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString()."<br>";
			else
				$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_UPDATE')) : GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_ADD'))."<br>";
			$bVarsFromForm = true;
			$boolCondParseError = true;
		}
	}

	$arGroupID = array();
	if (isset($_POST['GROUP_IDS']) && is_array($_POST['GROUP_IDS']))
	{
		foreach ($_POST['GROUP_IDS'] as &$intValue)
		{
			$intValue = intval($intValue);
			if ($intValue > 0)
			{
				$arGroupID[] = $intValue;
			}
		}
		if (isset($intValue))
			unset($intValue);
	}

	$arCatalogGroupID = array();
	if (isset($_POST['CAT_IDS']) && is_array($_POST['CAT_IDS']))
	{
		foreach ($_POST['CAT_IDS'] as &$intValue)
		{
			$intValue = intval($intValue);
			if ($intValue > 0)
			{
				$arCatalogGroupID[] = $intValue;
			}
		}
		if (isset($intValue))
			unset($intValue);
	}

	$arFields = array(
		"SITE_ID" => (isset($_POST['SITE_ID']) ? $_POST['SITE_ID'] : ''),
		"ACTIVE" => (isset($_POST['ACTIVE']) && 'Y' == $_POST['ACTIVE'] ? 'Y' : 'N'),
		"XML_ID" => (isset($_POST["XML_ID"]) ? $_POST["XML_ID"] : ''),
		"ACTIVE_FROM" => (isset($_POST['ACTIVE_FROM']) ? $_POST['ACTIVE_FROM'] : ''),
		"ACTIVE_TO" => (isset($_POST['ACTIVE_TO']) ? $_POST['ACTIVE_TO'] : ''),
		"RENEWAL" => (isset($_POST['RENEWAL']) && 'Y' == $_POST['RENEWAL'] ? 'Y' : 'N'),
		"NAME" => (isset($_POST['NAME']) ? $_POST['NAME'] : ''),
		"SORT" => intval(isset($_POST['SORT']) ? $_POST['SORT'] : 500),
		"MAX_DISCOUNT" => (isset($_POST['MAX_DISCOUNT']) ? $_POST['MAX_DISCOUNT'] : 0),
		"VALUE_TYPE" => (isset($_POST['VALUE_TYPE']) ? $_POST['VALUE_TYPE'] : ''),
		"VALUE" => (isset($_POST['VALUE']) ? $_POST['VALUE'] : 0),
		"CURRENCY" => (isset($_POST['CURRENCY']) ? $_POST['CURRENCY'] : ''),
		"NOTES" => (isset($_POST['NOTES']) ? $_POST['NOTES'] : ''),
		"PRIORITY" => intval(isset($_POST['PRIORITY']) ? $_POST['PRIORITY'] : ''),
		"LAST_DISCOUNT" => (isset($_POST['LAST_DISCOUNT']) && 'N' == $_POST['LAST_DISCOUNT'] ? 'N' : 'Y'),
		"GROUP_IDS" => $arGroupID,
		"CATALOG_GROUP_IDS" => $arCatalogGroupID,
		"CONDITIONS" => $CONDITIONS,
	);

	if (!($ID > 0 && !$boolCopy))
	{
		$arCouponFields = array(
			'COUPON_ADD' => (isset($_POST['COUPON_ADD']) && 'Y' == $_POST['COUPON_ADD'] ? 'Y' : 'N'),
			'COUPON_TYPE' => (isset($_POST['COUPON_TYPE']) ? $_POST['COUPON_TYPE'] : ''),
			'COUPON_COUNT' => intval(isset($_POST['COUPON_COUNT']) ? $_POST['COUPON_COUNT'] : 0),
		);
		$boolCouponAdd = true;

		if ('Y' == $arCouponFields['COUPON_ADD'])
		{
			if (!array_key_exists($arCouponFields['COUPON_TYPE'], $arCouponTypeList))
			{
				$bVarsFromForm = true;
				$errorMessage .= GetMessage('BT_CAT_DISCOUNT_EDIT_COUPON_TYPE')."<br>";
			}
			if (0 >= $arCouponFields['COUPON_COUNT'])
			{
				$bVarsFromForm = true;
				$errorMessage .= GetMessage('BT_CAT_DISCOUNT_EDIT_COUPON_COUNT')."<br>";
			}
		}
	}

	if (!$bVarsFromForm)
	{
		$DB->StartTransaction();

		if ($ID > 0 && !$boolCopy)
		{
			$res = CCatalogDiscount::Update($ID, $arFields);
		}
		else
		{
			$ID = CCatalogDiscount::Add($arFields);
			$res = ($ID > 0);
		}

		if (!$res)
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString()."<br>";
			else
				$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_UPDATE')) : GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_ADD'))."<br>";
			$bVarsFromForm = true;
			$DB->Rollback();
		}
		else
		{
			$DB->Commit();
			if ($boolCouponAdd)
			{
				for ($i = 0; $i < $arCouponFields['COUPON_COUNT']; $i++)
				{
					$CID = CCatalogDiscountCoupon::Add(
						array(
							"DISCOUNT_ID" => $ID,
							"ACTIVE" => "Y",
							"ONE_TIME" => $arCouponFields['COUPON_TYPE'],
							"COUPON" => CatalogGenerateCoupon(),
							"DATE_APPLY" => false
						)
					);
					$cRes = ($CID > 0);
					if (!$cRes)
					{
						if ($ex = $APPLICATION->GetException())
							$errorMessage .= $ex->GetString()."<br>";
						else
							$errorMessage .= GetMessage('BT_CAT_DISCOUNT_EDIT_ERR_COUPON_ADD')."<br>";
						$bVarsFromForm = true;
					}
				}
			}

			if (!$bVarsFromForm)
			{
				if (strlen($apply)<=0)
				{
					if (!empty($return_url))
						LocalRedirect($return_url);
					else
						LocalRedirect("/bitrix/admin/cat_discount_admin.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false));
				}
				else
				{
					LocalRedirect("/bitrix/admin/cat_discount_edit.php?lang=".LANGUAGE_ID."&ID=".$ID.GetFilterParams("filter_", false).'&'.$tabControl->ActiveTabParam());
				}
			}
		}
	}
}

$arDefaultValues = array(
	'XML_ID' => '',
	'SITE_ID' => '',
	'NAME' => '',
	'ACTIVE' => 'Y',
	'SORT' => 100,
	'ACTIVE_FROM' => '',
	'ACTIVE_TO' => '',
	'RENEWAL' => 'N',
	'VALUE_TYPE' => 'P',
	'VALUE' => '',
	'MAX_DISCOUNT' => '',
	'CURRENCY' => '',
	'PRIORITY' => 1,
	'LAST_DISCOUNT' => 'Y',
	'NOTES' => '',
	'CONDITIONS' => '',
);
$arDefCoupons = array(
	'COUPON_ADD' => 'N',
	'COUPON_TYPE' => 'Y',
	'COUPON_COUNT' => '',
);

$arSelect = array_merge(array('ID', 'VERSION'), array_keys($arDefaultValues));

$arDiscount = array();
$arDiscountGroupList = array();
$arDiscountCatList = array();
$arCoupons = array();

$rsDiscounts = CCatalogDiscount::GetList(array(), array("ID" => $ID), false, false, $arSelect);
if (!($arDiscount = $rsDiscounts->Fetch()))
{
	$ID = 0;
	$arDiscount = $arDefaultValues;
	$arCoupons = $arDefCoupons;
}
else
{
	$rsDiscountGroups = CCatalogDiscount::GetDiscountGroupsList(array(), array("DISCOUNT_ID" => $ID));
	while ($arDiscountGroup = $rsDiscountGroups->Fetch())
	{
		$arDiscountGroupList[] = intval($arDiscountGroup["GROUP_ID"]);
	}
	$rsDiscountCats = CCatalogDiscount::GetDiscountCatsList(array(), array("DISCOUNT_ID" => $ID));
	while ($arDiscountCat = $rsDiscountCats->Fetch())
	{
		$arDiscountCatList[] = intval($arDiscountCat["CATALOG_GROUP_ID"]);
	}

	if (!isset($arDiscount['VERSION']) || CATALOG_DISCOUNT_NEW_VERSION != intval($arDiscount['VERSION']))
	{
		$bReadOnly = true;
		$boolShowCoupons = false;
		$rsAdminNotify = CAdminNotify::GetList(array(), array('MODULE_ID'=>'catalog', 'TAG' => 'CATALOG_DISC_CONVERT'));
		if (!($arAdminNotify = $rsAdminNotify->Fetch()))
		{
			$strLangPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/lang/';
			$strDefLang = false;
			$arLangList = array();
			$rsLangs = CLanguage::GetList(($by="def"), ($order="desc"));
			while ($arOneLang = $rsLangs->Fetch())
			{
				if (empty($strDefLang))
					$strDefLang = $arOneLang['LID'];
				$arLangList[] = $arOneLang['LID'];
			}
			$arMess = __GetCatLangMessages($strLangPath, '/admin/cat_discount_edit.php', array('BT_MOD_CAT_DSC_CONV_INVITE'), '', $arLangList);
			if (is_array($arMess) && !empty($arMess['BT_MOD_CAT_DSC_CONV_INVITE'][$strDefLang]))
			{
				$arFields = array(
					"MESSAGE" => str_replace("#LINK#", '/bitrix/admin/cat_discount_convert.php', $arMess['BT_MOD_CAT_DSC_CONV_INVITE'][$strDefLang]),
					"TAG" => "CATALOG_DISC_CONVERT",
					"MODULE_ID" => "catalog",
					"ENABLE_CLOSE" => "N"
				);
				$arLangMess = array();
				foreach ($arMess['BT_MOD_CAT_DSC_CONV_INVITE'] as $strLangID => $strMess)
				{
					if (empty($strMess))
						continue;
					$arLangMess[$strLangID] = str_replace("#LINK#", '/bitrix/admin/cat_discount_convert.php', $strMess);
				}
				if (!empty($arLangMess))
					$arFields['LANG'] = $arLangMess;
				CAdminNotify::Add($arFields);
			}
		}
	}
}

if ($bVarsFromForm)
{
	if ($boolCondParseError)
	{
		$mxTempo = $arDiscount['CONDITIONS'];
		$arDiscount = $arFields;
		$arDiscount['CONDITIONS'] = $mxTempo;
		unset($mxTempo);
	}
	else
	{
		$arDiscount = $arFields;
	}
	$arDiscountGroupList = $arFields['GROUP_IDS'];
	$arDiscountCatList = $arFields['CATALOG_GROUP_IDS'];
	if (isset($arCouponFields))
		$arCoupons = $arCouponFields;
}

if ($ID > 0 && !$boolCopy)
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("DSC_TITLE_UPDATE")));
else
	$APPLICATION->SetTitle(GetMessage("DSC_TITLE_ADD"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT" => GetMessage("CDEN_2FLIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/cat_discount_admin.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false)
	)
);

if ($ID > 0)
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
		"TEXT" => GetMessage("CDEN_DCPN_LIST"),
		"LINK" => "/bitrix/admin/cat_discount_coupon.php?lang=".LANGUAGE_ID."&set_filter=Y&filter_discount_id=".$ID
	);

	if (!$bReadOnly)
	{
		$aMenu[] = array("SEPARATOR" => "Y");

		$aMenu[] = array(
			"TEXT" => GetMessage("CDEN_NEW_DISCOUNT"),
			"ICON" => "btn_new",
			"LINK" => "/bitrix/admin/cat_discount_edit.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false)
		);
		if (!$boolCopy)
		{
			$aMenu[] = array(
				"TEXT"=>GetMessage("BT_CAT_DISCOUNT_EDIT_CONT_NAME_COPY"),
				"LINK"=>"/bitrix/admin/cat_discount_edit.php?ID=".$ID."&action=copy&lang=".urlencode(LANGUAGE_ID).GetFilterParams("filter_", false),
				"ICON"=>"btn_copy",
			);

			$aMenu[] = array(
				"TEXT" => GetMessage("CDEN_DELETE_DISCOUNT"),
				"ICON" => "btn_delete",
				"LINK" => "javascript:if(confirm('".GetMessage("CDEN_DELETE_DISCOUNT_CONFIRM")."')) window.location='/bitrix/admin/cat_discount_admin.php?action=delete&ID[]=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."#tb';",
				"WARNING" => "Y"
			);
		}
	}
}

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CAdminMessage::ShowMessage($errorMessage);?>
<?

$arSiteList = array();
$rsSites = CSite::GetList(($by = 'sort'),($order = 'asc'));
while ($arSite = $rsSites->Fetch())
{
	$arSiteList[$arSite['LID']] = '('.$arSite['LID'].') '.$arSite['NAME'];
}

$arCurrencyList = array();
$rsCurrencies = CCurrency::GetList(($by2 = 'sort'),($order2 = 'asc'));
while ($arCurrency = $rsCurrencies->Fetch())
{
	$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
}
$arTypeList = array(
	'P' => GetMessage('DSC_TYPE_PERCENT'),
	'F' => GetMessage('DSC_TYPE_FIX'),
	'S' => GetMessage('DSC_TYPE_SALE2'),
);

$tabControl->BeginPrologContent();

CAdminCalendar::ShowScript();

$tabControl->EndPrologContent();

$tabControl->BeginEpilogContent();
?><?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<? echo bitrix_sessid_post(); ?><?
if (!empty($return_url))
{
	?><input type="hidden" name="return_url" value="<? echo htmlspecialcharsbx($return_url); ?>"><?
}
if ($boolCopy)
{
	?><input type="hidden" name="action" value="copy"><?
}
$tabControl->EndEpilogContent();
$tabControl->Begin(array(
	"FORM_ACTION" => '/bitrix/admin/cat_discount_edit.php?lang='.urlencode(LANGUAGE_ID),
));

$tabControl->BeginNextFormTab();
	if ($ID > 0 && !$boolCopy)
		$tabControl->AddViewField('ID','ID:',$ID,false);
	$tabControl->AddCheckBoxField("ACTIVE", GetMessage("DSC_ACTIVE").":", false, "Y", $arDiscount['ACTIVE'] == "Y");
	$tabControl->AddEditField("NAME", GetMessage("DSC_NAME").":", true, array("size" => 50, "maxlength" => 255), htmlspecialcharsbx($arDiscount['NAME']));
	$tabControl->AddDropDownField("SITE_ID", GetMessage('DSC_SITE').':', true, $arSiteList, $arDiscount['SITE_ID']);
	$tabControl->BeginCustomField("PERIOD", GetMessage('DSC_PERIOD').":",false);
	?><tr id="tr_PERIOD">
		<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td width="60%"><?
			global $ACTIVE_FROM_FILTER_PERIOD;
			$ACTIVE_FROM_FILTER_PERIOD = "";
			if ('' != $arDiscount['ACTIVE_FROM'] || '' != $arDiscount['ACTIVE_TO'])
				$ACTIVE_FROM_FILTER_PERIOD = CAdminCalendar::PERIOD_INTERVAL;

			echo CAdminCalendar::CalendarPeriodCustom("ACTIVE_FROM", "ACTIVE_TO", $arDiscount['ACTIVE_FROM'], $arDiscount['ACTIVE_TO'], true, 19, true, array(
				CAdminCalendar::PERIOD_EMPTY => GetMessage('BT_CAT_DISCOUNT_EDIT_CALENDARE_PERIOD_EMPTY'),
				CAdminCalendar::PERIOD_INTERVAL => GetMessage('BT_CAT_DISCOUNT_EDIT_CALENDARE_PERIOD_INTERVAL')
			));
		?></td>
	</tr><?
	$tabControl->EndCustomField("PERIOD",
		'<input type="hidden" name="ACTIVE_FROM" value="'.htmlspecialcharsbx($arDiscount['ACTIVE_FROM']).'">'.
		'<input type="hidden" name="ACTIVE_TO" value="'.htmlspecialcharsbx($arDiscount['ACTIVE_FROM']).'">'
	);
	$tabControl->BeginCustomField("VALUE_TYPE", GetMessage('DSC_TYPE').":",true);
	?><tr id="tr_VALUE_TYPE" class="adm-detail-required-field">
		<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td width="60%">
			<select name="VALUE_TYPE" id="ob_value_type"><?
				foreach ($arTypeList as $key => $value)
				{
					?><option value="<? echo htmlspecialcharsbx($key); ?>"<?if ($arDiscount['VALUE_TYPE'] == $key) echo " selected";?>><? echo htmlspecialcharsex($value); ?></option><?
				}
			?></select>
		</td>
	</tr><?
	$tabControl->EndCustomField("VALUE_TYPE",
		'<input type="hidden" name="VALUE_TYPE" value="'.htmlspecialcharsbx($arDiscount['VALUE_TYPE']).'">'
	);
	$tabControl->AddEditField("VALUE", GetMessage("DSC_VALUE").":", true, array('size' => 20, 'maxlength' => 20), roundEx($arDiscount['VALUE'], CATALOG_VALUE_PRECISION));
	$tabControl->AddDropDownField("CURRENCY", GetMessage('DSC_CURRENCY').':', true, $arCurrencyList, $arDiscount['CURRENCY']);
	$tabControl->BeginCustomField("MAX_DISCOUNT", GetMessage('DSC_MAX_SUM').":",false);
	?><tr id="tr_MAX_DISCOUNT" style="display: <? echo 'P' == $arDiscount['VALUE_TYPE'] ? 'table-row' : 'none'; ?>;">
		<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td width="60%"><input id="ob_max_discount" type="text" name="MAX_DISCOUNT" size="20" maxlength="20" value="<?= roundEx($arDiscount['MAX_DISCOUNT'], CATALOG_VALUE_PRECISION) ?>"></td>
	</tr><?
	$tabControl->EndCustomField("MAX_DISCOUNT",
		'<input type="hidden" name="MAX_DISCOUNT" value="'.roundEx($arDiscount['MAX_DISCOUNT'], CATALOG_VALUE_PRECISION).'">'
	);
	$tabControl->AddCheckBoxField("RENEWAL", GetMessage("DSC_RENEW").":", false, "Y", $arDiscount['RENEWAL']=="Y");
	$tabControl->AddEditField("PRIORITY", GetMessage("BT_CAT_DISCOUNT_EDIT_FIELDS_PRIORITY").":", false, array("size" => 20, "maxlength" => 20), intval($arDiscount['PRIORITY']));
	$tabControl->BeginCustomField("LAST_DISCOUNT", GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_LAST_DISCOUNT').":",false);
	?><tr id="tr_LAST_DISCOUNT">
		<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td width="60%">
			<input type="hidden" value="N" name="LAST_DISCOUNT">
			<input type="checkbox" value="Y" name="LAST_DISCOUNT" <? echo ('Y' == $arDiscount['LAST_DISCOUNT']? 'checked' : '');?>>
		</td>
	</tr><?
	$tabControl->EndCustomField("LAST_DISCOUNT",
		'<input type="hidden" name="LAST_DISCOUNT" value="'.htmlspecialcharsbx($arDiscount['LAST_DISCOUNT']).'">'
	);
	$tabControl->AddTextField("NOTES", GetMessage("DSC_DESCR").':', htmlspecialcharsbx($arDiscount['NOTES']), array("cols" => 50, 'rows' => 6));

$tabControl->BeginNextFormTab();

	$tabControl->BeginCustomField("CONDITIONS", GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COND').":",false);
	?><tr id="tr_CONDITIONS">
		<td valign="top"><div id="tree" style="position: relative; z-index: 1;"></div><?
			if (!is_array($arDiscount['CONDITIONS']))
			{
				if (CheckSerializedData($arDiscount['CONDITIONS']))
				{
					$arDiscount['CONDITIONS'] = unserialize($arDiscount['CONDITIONS']);
				}
				else
				{
					$arDiscount['CONDITIONS'] = '';
				}
			}
			$obCond = new CCatalogCondTree();
			$boolCond = $obCond->Init(BT_COND_MODE_DEFAULT, BT_COND_BUILD_CATALOG, array('FORM_NAME' => 'fdiscount_edit_form', 'CONT_ID' => 'tree', 'JS_NAME' => 'JSCatCond'));
			if (!$boolCond)
			{
				if ($ex = $APPLICATION->GetException())
				echo $ex->GetString()."<br>";
			}
			else
			{
				$obCond->Show($arDiscount['CONDITIONS']);
			}
		?></td>
	</tr><?
	$strHidden = '';
	$strCond = base64_encode(serialize($arDiscount['CONDITIONS']));
	$tabControl->EndCustomField("CONDITIONS",
		'<input type="hidden" name="CONDITIONS" value="'.htmlspecialcharsbx($strCond).'">'.
		'<input type="hidden" name="CONDITIONS_CHECK" value="'.htmlspecialcharsbx(md5($strCond)).'">'
	);

$tabControl->BeginNextFormTab();

	$tabControl->BeginCustomField("GROUP_IDS", GetMessage('DSC_USER_GROUPS').":",false);
	?><tr id="tr_GROUP_IDS">
		<td valign="top" width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td valign="top" width="60%">
			<select name="GROUP_IDS[]" multiple size="8">
			<?
			$dbGroups = CGroup::GetList(($b="c_sort"), ($o="asc"), array());
			while ($arGroups = $dbGroups->Fetch())
			{
				?><option value="<?= $arGroups["ID"] ?>"<?if (in_array(intval($arGroups["ID"]), $arDiscountGroupList)) echo " selected";?>>[<?= $arGroups["ID"] ?>] <?= htmlspecialcharsEx($arGroups["NAME"]) ?></option><?
			}
			?>
			</select>
		</td>
	</tr><?
	if ($ID > 0 && !empty($arDiscountGroupList))
	{
		$arHidden = array();
		foreach ($arDiscountGroupList as &$value)
		{
			if (0 < intval($value))
				$arHidden[] = '<input type="hidden" name="GROUP_IDS[]" value="'.intval($value).'">';
		}
		if (isset($value))
			unset($value);
		$strHidden = implode('',$arHidden);
	}
	else
	{
		$strHidden = '<input type="hidden" name="GROUP_IDS[]" value="">';
	}
	$tabControl->EndCustomField("GROUP_IDS",
		$strHidden
	);

	$tabControl->BeginCustomField("CAT_IDS", GetMessage('DSC_PRICE_TYPES').":", false);
	?><tr id="tr_CAT_IDS">
		<td valign="top" width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td valign="top" width="60%">
			<select name="CAT_IDS[]" multiple size="8"><?
				$dbCats = CCatalogGroup::GetList(array("NAME" => "ASC"), array("LID" => LANGUAGE_ID));
				while ($arCats = $dbCats->Fetch())
				{
					?><option value="<?= $arCats["ID"] ?>"<?if (in_array(intval($arCats["ID"]), $arDiscountCatList)) echo " selected";?>>[<?= $arCats["ID"] ?>] <?= htmlspecialcharsEx($arCats["NAME"]) ?> (<?= htmlspecialcharsEx($arCats["NAME_LANG"]) ?>)</option><?
				}
			?></select>
		</td>
	</tr><?
	if ($ID > 0 && !empty($arDiscountCatList))
	{
		$arHidden = array();
		foreach ($arDiscountCatList as &$value)
		{
			if (0 < intval($value))
				$arHidden[] = '<input type="hidden" name="CAT_IDS[]" value="'.intval($value).'">';
		}
		if (isset($value))
			unset($value);
		$strHidden = implode('',$arHidden);
	}
	else
	{
		$strHidden = '<input type="hidden" name="CAT_IDS[]" value="">';
	}
	$tabControl->EndCustomField("CAT_IDS",
		$strHidden
	);

$tabControl->BeginNextFormTab();
	$tabControl->BeginCustomField("COUPONS", GetMessage('BT_CAT_DISCOUNT_EDIT_TAB_NAME_COUPONS').":", false);

	define('B_ADMIN_SUBCOUPONS',1);
	define('B_ADMIN_SUBCOUPONS_LIST',false);
	$TMP_ID = 0;
	$intDiscountID = ((0 == $ID) || ($boolCopy) ? '-'.$TMP_ID : $ID);
	$strSubTMP_ID = $TMP_ID;
	$boolCouponsReadOnly = !$USER->CanDoOperation('catalog_discount');
	$strSubElementAjaxPath = '/bitrix/admin/cat_subcoupons_admin.php?lang='.LANGUAGE_ID.'&find_discount_id='.$intDiscountID.'&TMP_ID='.urlencode($strSubTMP_ID);
	if (0 < $intDiscountID && $boolShowCoupons && !$boolCopy)
	{
		?><tr id="tr_COUPONS"><td><?
		require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/catalog/admin/templates/discount_coupon_list.php');
		?></td></tr><?
	}
	else
	{
		?><tr id="tr_COUPON_ADD">
			<td width="40%"><? echo GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COUPON_ADD'); ?>:</td>
			<td width="60%">
				<input type="hidden" value="N" name="COUPON_ADD" id="COUPON_ADD_N">
				<input type="checkbox" value="Y" name="COUPON_ADD" id="COUPON_ADD_Y" <? echo ('Y' == $arCoupons['COUPON_ADD'] ? 'checked' : ''); ?>>
			</td>
		</tr>
		<tr id="tr_COUPON_TYPE" class="adm-detail-required-field" style="display: <? echo ('Y' == $arCoupons['COUPON_ADD'] ? 'table-row' : 'none'); ?>;">
			<td width="40%"><? echo GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COUPON_TYPE'); ?>:</td>
			<td width="60%">
				<select name="COUPON_TYPE"><?
				foreach ($arCouponTypeList as $strType => $strName)
				{
					?><option value="<? echo htmlspecialcharsbx($strType); ?>" <? echo ($strType == $arCoupons['COUPON_TYPE'] ? 'selected' : ''); ?>><? echo htmlspecialcharsex($strName); ?></option><?
				}
				?></select>
			</td>
		</tr>
		<tr id="tr_COUPON_COUNT" class="adm-detail-required-field" style="display: <? echo ('Y' == $arCoupons['COUPON_ADD'] ? 'table-row' : 'none'); ?>;">
			<td width="40%"><? echo GetMessage('BT_CAT_DISCOUNT_EDIT_FIELDS_COUPON_COUNT'); ?>:</td>
			<td width="60%"><input type="text" name="COUPON_COUNT" value="<? echo intval($arCoupons['COUPON_COUNT']); ?>"></td>
		</tr><?
	}
	$tabControl->EndCustomField("COUPONS",
		$strHidden
	);

$tabControl->BeginNextFormTab();
	$tabControl->AddEditField("XML_ID", GetMessage("BT_CAT_DISCOUNT_EDIT_FIELDS_XML_ID").":", false, array("size" => 50, "maxlength" => 255), htmlspecialcharsbx($arDiscount['XML_ID']));
	$tabControl->AddEditField("SORT", GetMessage("DSC_SORT").":", false, array("size" => 20, "maxlength" => 20), intval($arDiscount['SORT']));

$arButtonsParams = array(
	"disabled" => $bReadOnly,
	"back_url" => "/bitrix/admin/cat_discount_admin.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false)
);

$tabControl->Buttons($arButtonsParams);

$tabControl->Show();

$tabControl->ShowWarnings("fdiscount_edit", $obMessages);
?>
<script type="text/javascript">
BX.ready(function(){
	var obValueType = BX('ob_value_type');
	var obMaxDiscount = BX('tr_MAX_DISCOUNT');
	if (!!obValueType && !!obMaxDiscount)
	{
		BX.bind(obValueType, 'change', function(){
			BX.style(obMaxDiscount, 'display', (-1 < obValueType.selectedIndex && 'P' == obValueType.options[obValueType.selectedIndex].value ? 'table-row' : 'none'));
		});
		BX.style(obMaxDiscount, 'display', (-1 < obValueType.selectedIndex && 'P' == obValueType.options[obValueType.selectedIndex].value ? 'table-row' : 'none'));
	}
	var obCouponAdd = BX('COUPON_ADD_Y');
	var obCouponType = BX('tr_COUPON_TYPE');
	var obCouponCount = BX('tr_COUPON_COUNT');
	if (!!obCouponAdd && !!obCouponType && !!obCouponCount)
	{
		BX.bind(obCouponAdd, 'click', function(){
			BX.style(obCouponType, 'display', (obCouponAdd.checked ? 'table-row' : 'none'));
			BX.style(obCouponCount, 'display', (obCouponAdd.checked ? 'table-row' : 'none'));
		});
		BX.style(obCouponType, 'display', (obCouponAdd.checked ? 'table-row' : 'none'));
		BX.style(obCouponCount, 'display', (obCouponAdd.checked ? 'table-row' : 'none'));
	}
});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>