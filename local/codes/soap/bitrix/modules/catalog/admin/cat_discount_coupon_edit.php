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

if (!empty($return_url) && strtolower(substr($return_url, strlen($APPLICATION->GetCurPage())))==strtolower($APPLICATION->GetCurPage()))
	$return_url = "";

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CDEN_TAB_DISCOUNT"), "ICON" => "catalog", "TITLE" => GetMessage("CDEN_TAB_DISCOUNT_DESCR")),
);

$tabControl = new CAdminForm("tabControl", $aTabs);
$tabControl->SetShowSettings(false);

$errorMessage = "";
$bVarsFromForm = false;

$ID = intval($ID);

if (!$bReadOnly && $_SERVER['REQUEST_METHOD']=="POST" && strlen($Update)>0 && check_bitrix_sessid())
{
	$DB->StartTransaction();

	$arFields = array(
		"DISCOUNT_ID" => intval(isset($_POST['DISCOUNT_ID']) ? $_POST['DISCOUNT_ID'] : 0),
		"ACTIVE" => (isset($_POST['ACTIVE']) && 'Y' == $_POST['ACTIVE'] ? 'Y' : 'N'),
		"COUPON" => (isset($_POST["COUPON"]) ? $_POST["COUPON"] : ''),
		"DATE_APPLY" => (isset($_POST['DATE_APPLY']) ? $_POST['DATE_APPLY'] : ''),
		"ONE_TIME" => (isset($_POST['ONE_TIME']) ? $_POST['ONE_TIME'] : ''),
		"DESCRIPTION" => (isset($_POST['DESCRIPTION']) ? $_POST['DESCRIPTION'] : ''),
	);

	if ($ID > 0)
	{
		$res = CCatalogDiscountCoupon::Update($ID, $arFields);
	}
	else
	{
		$ID = CCatalogDiscountCoupon::Add($arFields);
		$res = ($ID>0);
	}

	if (!$res)
	{
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString()."<br>";
		else
			$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('DSC_CPN_ERR_UPDATE')) : GetMessage('DSC_CPN_ERR_ADD'))."<br>";
		$bVarsFromForm = true;
		$DB->Rollback();
	}
	else
	{
		$DB->Commit();
		if (strlen($apply)<=0)
			LocalRedirect("/bitrix/admin/cat_discount_coupon.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false));
		else
			LocalRedirect("/bitrix/admin/cat_discount_coupon_edit.php?lang=".LANGUAGE_ID."&ID=".intval($ID).GetFilterParams("filter_", false));
	}
}

if ($ID > 0)
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("DSC_TITLE_UPDATE")));
else
	$APPLICATION->SetTitle(GetMessage("DSC_TITLE_ADD"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$arDefaultValues = array(
	'DISCOUNT_ID' => '',
	'ACTIVE' => 'Y',
	'ONE_TIME' => 'Y',
	'COUPON' => '',
	'DATE_APPLY' => '',
	'DESCRIPTION' => '',
);

$arSelect = array_merge(array('ID'), array_keys($arDefaultValues));

$arCoupon = array();

$rsCoupons = CCatalogDiscountCoupon::GetList(array(), array("ID" => $ID), false, false, $arSelect);
if (!($arCoupon = $rsCoupons->Fetch()))
{
	$ID = 0;
	$arCoupon = $arDefaultValues;
}

if ($bVarsFromForm)
{
	$arCoupon = $arFields;
}

$aMenu = array(
	array(
		"TEXT" => GetMessage("DSC_TO_LIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/cat_discount_coupon.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false)
	)
);

if ($ID > 0 && !$bReadOnly)
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
		"TEXT" => GetMessage("CDEN_NEW_DISCOUNT"),
		"ICON" => "btn_new",
		"LINK" => "/bitrix/admin/cat_discount_coupon_edit.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false)
	);

	$aMenu[] = array(
		"TEXT" => GetMessage("CDEN_DELETE_DISCOUNT"),
		"ICON" => "btn_delete",
		"LINK" => "javascript:if(confirm('".GetMessage("CDEN_DELETE_DISCOUNT_CONFIRM")."')) window.location='/bitrix/admin/cat_discount_coupon.php?action=delete&ID[]=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."#tb';",
		"WARNING" => "Y"
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show(); ?>
<?CAdminMessage::ShowMessage($errorMessage);?><?

$arDiscountList = array();
$rsDiscounts = CCatalogDiscount::GetList(
	array("NAME" => "ASC"),
	array(),
	false,
	false,
	array("ID", "SITE_ID", "NAME")
);
while ($arDiscount = $rsDiscounts->Fetch())
{
	$arDiscountList[$arDiscount['ID']] = "[".$arDiscount["ID"]."] ".$arDiscount["NAME"]." (".$arDiscount["SITE_ID"].")";
}
$arTypeList = array(
	"Y" => GetMessage('DSC_TYPE_ONE_TIME'),
	"O" => GetMessage('DSC_TYPE_ONE_ORDER'),
	"N" => GetMessage('DSC_TYPE_MULTI'),
);

$tabControl->BeginPrologContent();

$tabControl->EndPrologContent();

$tabControl->BeginEpilogContent();
echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<? echo bitrix_sessid_post()?>
<?
if (!empty($return_url))
{
	?><input type="hidden" name="return_url" value="<? echo htmlspecialcharsbx($return_url); ?>"><?
}
$tabControl->EndEpilogContent();
$tabControl->Begin(array(
	"FORM_ACTION" => '/bitrix/admin/cat_discount_coupon_edit.php?lang='.urlencode(LANGUAGE_ID),
));

$tabControl->BeginNextFormTab();
	if ($ID > 0 && !$boolCopy)
		$tabControl->AddViewField('ID','ID:',$ID,false);
	if (!empty($arDiscountList))
	{
		$tabControl->AddDropDownField("DISCOUNT_ID", GetMessage('DSC_CPN_DISC').':', true, $arDiscountList, $arCoupon['DISCOUNT_ID']);
	}
	else
	{
		$tabControl->BeginCustomField("DISCOUNT_ID", GetMessage('DSC_CPN_DISC').':', true);
		?><tr id="tr_DISCOUNT_ID" class="adm-detail-required-field">
			<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
			<td width="60%">&nbsp;<a href="/bitrix/admin/cat_discount_edit.php?lang=<? echo LANGUAGE_ID; ?>&return_url=<? echo urlencode($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID); ?>"><? echo GetMessage('DSC_ADD_DISCOUNT'); ?></a></td>
		</tr><?
		$tabControl->EndCustomField('DISCOUNT_ID');
	}
	$tabControl->AddCheckBoxField("ACTIVE", GetMessage("DSC_ACTIVE").":", false, "Y", $arCoupon['ACTIVE'] == "Y");
	$tabControl->AddDropDownField("ONE_TIME", GetMessage('DSC_COUPON_TYPE').':', true, $arTypeList, $arCoupon['ONE_TIME']);
	$tabControl->BeginCustomField('COUPON', GetMessage("DSC_CPN_CODE").':', true);
	?><tr id="tr_COUPON" class="adm-detail-required-field">
		<td width="40%"><? echo $tabControl->GetCustomLabelHTML(); ?></td>
		<td width="60%" id="td_COUPON_VALUE">
			<input type="text" id="COUPON" name="COUPON" size="32" maxlength="32" value="<? echo htmlspecialcharsbx($arCoupon['COUPON']); ?>" />&nbsp;
			<input type="button" value="<? echo GetMessage("DSC_CPN_GEN") ?>" id="COUPON_GENERATE">
		</td>
	</tr><?
	$tabControl->EndCustomField('COUPON',
		'<input type="hidden" name="COUPON" value="'.htmlspecialcharsbx($arCoupon['COUPON']).'">'
	);
	$tabControl->AddCalendarField('DATE_APPLY', GetMessage("DDSC_CPN_DATE").':', $arCoupon['DATE_APPLY']);
	$tabControl->AddTextField("DESCRIPTION", GetMessage("DSC_CPN_DESCRIPTION").':', htmlspecialcharsbx($arCoupon['DESCRIPTION']), array("cols" => 50, 'rows' => 6));

	$arButtonsParams = array(
		"disabled" => $bReadOnly,
		"back_url" => "/bitrix/admin/cat_discount_coupon.php?lang=".LANGUAGE_ID.GetFilterParams("filter_", false)
	);

$tabControl->Buttons($arButtonsParams);

$tabControl->Show();

$tabControl->ShowWarnings("tabControl", $obMessages);

?><script type="text/javascript">
BX.ready(function(){
	var obCouponValue = BX('COUPON');
	var obCouponBtn = BX('COUPON_GENERATE');
	if (!!obCouponValue && !!obCouponBtn)
	{
		BX.bind(obCouponBtn, 'click', function(){
			BX.showWait();
			var url = '/bitrix/tools/generate_coupon.php?lang='+'<? echo urlencode(LANGUAGE_ID); ?>'+'&'+'<? echo bitrix_sessid_get(); ?>';
			BX.ajax.loadJSON(url,function(data){
				var boolFlag = true;
				var strErr = '';
				if (BX.type.isString(data))
				{
					boolFlag = false;
					strErr = data;
				}
				else
				{
					if ('OK' != data.STATUS)
					{
						boolFlag = false;
						strErr = data.MESSAGE;
					}
				}
				var obCouponErr = BX('COUPON_GENERATE_ERR');
				if (boolFlag)
				{
					obCouponValue.value = data.RESULT;
					if (!!obCouponErr)
						obCouponErr = BX.remove(obCouponErr);
				}
				else
				{
					if (!obCouponErr)
					{
						var obCouponErr = td_COUPON_VALUE.insertBefore(BX.create(
							'IMG',
							{
								props: {
									id: 'COUPON_GENERATE_ERR',
									src: '/bitrix/panel/main/images_old/icon_warn.gif'
								},
								style: {
									marginRight: '10px',
									verticalAlign: 'middle'
								}
							}
						), obCouponBtn);
					}
					BX.adjust(obCouponErr, {props: { title: strErr }});
				}
				BX.closeWait();
			});
		});
	}
});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>