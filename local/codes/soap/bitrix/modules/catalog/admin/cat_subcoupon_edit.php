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

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/catalog/admin/cat_discount_coupon_edit.php");
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/general/subelement.php');

$intDiscountID = intval($_REQUEST['DISCOUNT_ID']);
$strSubTMP_ID = trim($_REQUEST['TMP_ID']);


$rsDiscounts = CCatalogDiscount::GetList(
	array(),
	array('ID' => $intDiscountID),
	false,
	false,
	array("ID")
);
while ($arDiscount = $rsDiscounts->Fetch())
{
	$arDiscountList[] = $arDiscount['ID'];
}

$boolDiscount = true;
if (0 >= $intDiscountID)
{
	$boolDiscount = false;
}
else
{
	$rsDiscounts = CCatalogDiscount::GetList(
		array(),
		array('ID' => $intDiscountID),
		false,
		false,
		array("ID")
	);
	if (!($arDiscount = $rsDiscounts->Fetch()))
	{
		$boolDiscount = false;
	}
}
if (!$boolDiscount)
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
	ShowError(GetMessage('BT_CAT_DISC_SUBCOUPON_DISCOUNT_ID_ABSENT'));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if (!empty($return_url) && strtolower(substr($return_url, strlen($APPLICATION->GetCurPage())))==strtolower($APPLICATION->GetCurPage()))
	$return_url = "";

$boolMulti = (isset($_REQUEST['MULTI']) && 'Y' == $_REQUEST['MULTI']);

if (!$boolMulti)
{
	$aTabs = array(
		array("DIV" => "sub_edit1", "TAB" => GetMessage("CDEN_TAB_DISCOUNT"), "ICON" => "catalog", "TITLE" => GetMessage("CDEN_TAB_DISCOUNT_DESCR")),
	);
}
else
{
	$aTabs = array(
		array("DIV" => "sub_edit1", "TAB" => GetMessage("CDEN_TAB_DISCOUNT_MULTI"), "ICON" => "catalog", "TITLE" => ""),
	);
}

$arPostParams = array(
	'bxpublic' => 'Y',
);
if (0 < $intDiscountID)
{
	$arPostParams['DISCOUNT_ID'] = $intDiscountID;
	$arPostParams['sessid'] = bitrix_sessid();
}

$arListUrl = array(
	'LINK' => $APPLICATION->GetCurPageParam(),
	'POST_PARAMS' => $arPostParams,
);

$errorMessage = "";
$bVarsFromForm = false;

$ID = intval($ID);

$arTypeList = array(
	"Y" => GetMessage('DSC_TYPE_ONE_TIME'),
	"O" => GetMessage('DSC_TYPE_ONE_ORDER'),
	"N" => GetMessage('DSC_TYPE_MULTI'),
);

if (!$bReadOnly && $_SERVER['REQUEST_METHOD']=="POST" && strlen($Update)>0 && check_bitrix_sessid())
{
	if (!$boolMulti)
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
			?><script type="text/javascript">
			top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();
			top.ReloadOffers();
			</script><?
			die();
		}
	}
	else
	{
		$DB->StartTransaction();

		$arFields = array(
			"ONE_TIME" => (isset($_POST['ONE_TIME']) ? $_POST['ONE_TIME'] : ''),
			"COUNT" => intval(isset($_POST['COUNT']) ? $_POST['COUNT'] : 0),
		);
		if (!array_key_exists($arFields['ONE_TIME'], $arTypeList))
		{
			$errorMessage .= GetMessage('BT_CAT_DISC_SUBCOUPON_ERR_COUPON_TYPE_BAD')."<br>";
			$bVarsFromForm = true;
		}
		if (0 >= $arFields['COUNT'])
		{
			$errorMessage .= GetMessage('BT_CAT_DISC_SUBCOUPON_ERR_COUNT_BAD')."<br>";
			$bVarsFromForm = true;
		}

		if (!$bVarsFromForm)
		{
			for ($i = 0; $i < $arFields['COUNT']; $i++)
			{
				$CID = CCatalogDiscountCoupon::Add(
					array(
						"DISCOUNT_ID" => $intDiscountID,
						"ACTIVE" => "Y",
						"ONE_TIME" => $arFields['ONE_TIME'],
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
					break;
				}
			}
		}

		if (!$bVarsFromForm)
		{
			$DB->Commit();
			?><script type="text/javascript">
			top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();
			top.ReloadOffers();
			</script><?
			die();
		}
		else
		{
			$DB->Rollback();
		}
	}
}
else
{
	if (!empty($dontsave) && check_bitrix_sessid())
	{
		?><script type="text/javascript">
		top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();
		</script><?
		die();
	}
}

if ($ID > 0)
{
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("DSC_TITLE_UPDATE")));
}
else
{
	if (!$boolMulti)
	{
		$APPLICATION->SetTitle(GetMessage("DSC_TITLE_ADD"));
	}
	else
	{
		$APPLICATION->SetTitle(GetMessage("DSC_TITLE_ADD_MULTI"));
	}
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$tabControl = new CAdminSubForm("subcoupons_edit", $aTabs, false, true, $arListUrl, false);
$tabControl->SetShowSettings(false);

if (!$boolMulti)
{
	$arDefaultValues = array(
		'DISCOUNT_ID' => $intDiscountID,
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

	CAdminMessage::ShowMessage($errorMessage);

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
	?>
	<input type="hidden" name="DISCOUNT_ID" value="<? echo intval($intDiscountID)?>">
	<input type="hidden" name="MULTI" value="<? echo ($boolMulti ? 'Y' : 'N');?>">
	<input type="hidden" name="TMP_ID" value="<?echo htmlspecialcharsbx($strSubTMP_ID)?>"><?
	$tabControl->EndEpilogContent();
	$tabControl->Begin(array(
		"FORM_ACTION" => '/bitrix/admin/cat_subcoupon_edit.php?lang='.urlencode(LANGUAGE_ID),
	));

	$tabControl->BeginNextFormTab();
		if ($ID > 0 && !$boolCopy)
			$tabControl->AddViewField('ID','ID:',$ID,false);
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

	$tabControl->Buttons(false,'');

	$tabControl->Show();

	$tabControl->ShowWarnings("subcoupons_edit", $obMessages);

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
	top.BX.WindowManager.Get().adjustSizeEx();
	</script><?
}
else
{
	$arCoupon = array(
		'ONE_TIME' => 'Y',
		'COUNT' => ''
	);

	if ($bVarsFromForm)
	{
		$arCoupon = $arFields;
		CAdminMessage::ShowMessage($errorMessage);
	}
	else
	{
		?><script type="text/javascript">top.BX.WindowManager.Get().hideNotify();</script><?
	}

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
	?>
	<input type="hidden" name="DISCOUNT_ID" value="<? echo intval($intDiscountID)?>">
	<input type="hidden" name="MULTI" value="<? echo ($boolMulti ? 'Y' : 'N');?>">
	<input type="hidden" name="TMP_ID" value="<?echo htmlspecialcharsbx($strSubTMP_ID)?>"><?
	$tabControl->EndEpilogContent();
	$tabControl->Begin(array(
		"FORM_ACTION" => '/bitrix/admin/cat_subcoupon_edit.php?lang='.urlencode(LANGUAGE_ID),
	));

	$tabControl->BeginNextFormTab();
		$tabControl->AddDropDownField("ONE_TIME", GetMessage('DSC_COUPON_TYPE').':', true, $arTypeList, $arCoupon['ONE_TIME']);
		$tabControl->AddEditField("COUNT", GetMessage('BT_CAT_DISC_SUBCOUPON_FIELD_COUNT').':', true, $arCoupon['COUNT']);
	$tabControl->Buttons(false,'');

	$tabControl->Show();

	$tabControl->ShowWarnings("subcoupons_edit", $obMessages);

	?><script type="text/javascript">top.BX.WindowManager.Get().adjustSizeEx();</script><?
}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>