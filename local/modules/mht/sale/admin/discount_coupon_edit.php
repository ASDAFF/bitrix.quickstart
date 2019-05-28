<?
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type;
use Bitrix\Sale\Internals;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/prolog.php');

$subWindow = defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1;
$prefix = ($subWindow ? 'COUPON_' : '');

$saleModulePermissions = $APPLICATION->GetGroupRight('sale');
$readOnly = ($saleModulePermissions < 'W');
if ($saleModulePermissions < 'R')
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

Loader::includeModule('sale');
Loc::loadMessages(__FILE__);

if ($subWindow)
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/iblock/classes/general/subelement.php');
}

if (!$subWindow && $ex = $APPLICATION->GetException())
{
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	ShowError($ex->GetString());
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

$couponTypes = Internals\DiscountCouponTable::getCouponTypes(true);

$multiCoupons = false;
$discountID = 0;
if ($subWindow)
{
	$multiCoupons = (isset($_REQUEST['MULTI']) && $_REQUEST['MULTI'] == 'Y');
	if (isset($_REQUEST['DISCOUNT_ID']))
		$discountID = (int)$_REQUEST['DISCOUNT_ID'];
	$discountIterator = Internals\DiscountTable::getList(array(
		'select' => array('ID'),
		'filter' => array('ID' => $discountID)
	));
	if (!($discount = $discountIterator->fetch()))
	{
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		ShowError(Loc::getMessage('BX_SALE_DISCOUNT_COUPON_ERR_DISCOUNT_ID_ABSENT'));
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
		die();
	}
}
$returnUrl = '';
if (!$subWindow && !empty($_REQUEST['return_url']))
{
	$currentUrl = $APPLICATION->GetCurPage();
	if (strtolower(substr($_REQUEST['return_url'], strlen($currentUrl))) != strtolower($currentUrl))
	{
		$returnUrl = $_REQUEST['return_url'];
	}
}

$tabList = array(
	array(
		'ICON' => 'sale',
		'DIV' => 'couponEdit01',
		'TAB' => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TAB_NAME_COMMON'),
		'TITLE' => (
			$multiCoupons
			? Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TAB_TITLE_MULTI_COMMON')
			: Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TAB_TITLE_COMMON')
		)
	)
);
if ($subWindow)
{
	$arPostParams = array(
		'bxpublic' => 'Y',
		'DISCOUNT_ID' => $discountID,
		'sessid' => bitrix_sessid()
	);
	$listUrl = array(
		'LINK' => $APPLICATION->GetCurPageParam(),
		'POST_PARAMS' => $arPostParams,
	);

	$control = new CAdminSubForm('subCouponControl', $tabList, false, true, $listUrl, false);
}
else
{
	$control = new CAdminForm('couponControl', $tabList);
	$control->SetShowSettings(false);
}
unset($tabList);

$errors = array();
$fields = array();
$couponID = 0;
$copy = false;
if (isset($_REQUEST['ID']))
{
	$couponID = (int)$_REQUEST['ID'];
	if ($couponID < 0)
		$couponID = 0;
}
if ($couponID > 0)
{
	$copy = (isset($_REQUEST['action']) && (string)$_REQUEST['action'] == 'copy');
}

if (
	check_bitrix_sessid()
	&& !$readOnly
	&& $_SERVER['REQUEST_METHOD'] == 'POST'
	&& isset($_POST['Update']) && (string)$_POST['Update'] == 'Y'
)
{
	if ($multiCoupons)
	{
		$fields['COUNT'] = 0;
		$fields['COUPON'] = array(
			'DISCOUNT_ID' => $discountID
		);
		if (!empty($_POST[$prefix.'ACTIVE_FROM']))
			$fields['COUPON']['ACTIVE_FROM'] = new Type\DateTime($_POST[$prefix.'ACTIVE_FROM']);
		if (!empty($_POST[$prefix.'ACTIVE_TO']))
			$fields['COUPON']['ACTIVE_TO'] = new Type\DateTime($_POST[$prefix.'ACTIVE_TO']);
		if (isset($_POST[$prefix.'TYPE']))
			$fields['COUPON']['TYPE'] = $_POST[$prefix.'TYPE'];
		if (isset($_POST[$prefix.'MAX_USE']))
			$fields['COUPON']['MAX_USE'] = $_POST[$prefix.'MAX_USE'];
		if (isset($_POST[$prefix.'COUNT']))
			$fields['COUNT'] = (int)$_POST[$prefix.'COUNT'];

		if ($fields['COUNT'] <= 0)
			$errors[] = Loc::getMessage('BX_SALE_DISCOUNT_COUPON_ERR_COUPON_COUNT');

		$checkResult = Internals\DiscountCouponTable::checkPacket($fields['COUPON'], false);
		if (!$checkResult->isSuccess(true))
		{
			$errors = $checkResult->getErrorMessages();
		}
		else
		{
			$couponsResult = Internals\DiscountCouponTable::addPacket(
				$fields['COUPON'],
				$fields['COUNT']
			);
			if (!$couponsResult->isSuccess())
			{
				$errors = $couponsResult->getErrorMessages();
			}
		}
		unset($checkResult);
	}
	else
	{
		if ($subWindow)
		{
			$fields['DISCOUNT_ID'] = $discountID;
		}
		elseif (!empty($_POST['DISCOUNT_ID']))
		{
			$fields['DISCOUNT_ID'] = $_POST['DISCOUNT_ID'];
		}
		if (isset($_POST['COUPON']))
			$fields['COUPON'] = $_POST['COUPON'];
		if (!empty($_POST[$prefix.'ACTIVE']))
			$fields['ACTIVE'] = $_POST[$prefix.'ACTIVE'];
		if (!empty($_POST[$prefix.'ACTIVE_FROM']))
			$fields['ACTIVE_FROM'] = new Type\DateTime($_POST[$prefix.'ACTIVE_FROM']);
		if (!empty($_POST[$prefix.'ACTIVE_TO']))
			$fields['ACTIVE_TO'] = new Type\DateTime($_POST[$prefix.'ACTIVE_TO']);
		if (isset($_POST[$prefix.'TYPE']))
			$fields['TYPE'] = $_POST[$prefix.'TYPE'];
		if (isset($_POST[$prefix.'MAX_USE']))
			$fields['MAX_USE'] = $_POST[$prefix.'MAX_USE'];
		if (isset($_POST[$prefix.'USER_ID']))
			$fields['USER_ID'] = $_POST[$prefix.'USER_ID'];
		if (isset($_POST[$prefix.'DESCRIPTION']))
			$fields['DESCRIPTION'] = $_POST[$prefix.'DESCRIPTION'];

		if ($couponID == 0)
		{
			$result = Internals\DiscountCouponTable::add($fields);
		}
		else
		{
			$result = Internals\DiscountCouponTable::update($couponID, $fields);
		}
		if (!$result->isSuccess())
		{
			$errors = $result->getErrorMessages();
		}
		else
		{
			if ($couponID == 0)
				$couponID = $result->getId();
		}
	}

	if (empty($errors))
	{
		if ($subWindow)
		{
			?><script type="text/javascript">
top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();
top.ReloadOffers();
</script><?
			die();
		}
		else
		{
			if (!empty($_POST['apply']))
				LocalRedirect('sale_discount_coupon_edit.php?lang='.LANGUAGE_ID.'&ID='.$couponID.GetFilterParams('filter_', false));
			else
				LocalRedirect('sale_discount_coupons.php?lang='.LANGUAGE_ID.'&'.$control->ActiveTabParam().GetFilterParams('filter_', false));
		}
	}
}
elseif ($subWindow)
{
	if (!empty($_REQUEST['dontsave']))
	{
		?><script type="text/javascript">top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();</script><?
		die();
	}
}

$APPLICATION->SetTitle(
	$couponID == 0
	? (
		!$multiCoupons
		? Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TITLE_ADD')
		: Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TITLE_MULTI_ADD')
	)
	: (
		!$copy
		? Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TITLE_UPDATE', array('#ID#' => $couponID))
		: Loc::getMessage('BX_SALE_DISCOUNT_COUPON_EDIT_TITLE_COPY', array('#ID#' => $couponID))
	)
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$contextMenuItems = array(
	array(
		'ICON' => 'btn_list',
		'TEXT' => Loc::getMessage('BX_SALE_DISCOUNT_COUPONT_CONTEXT_COUPON_LIST'),
		'LINK' => 'sale_discount_coupons.php?lang='.LANGUAGE_ID.GetFilterParams('filter_')
	)
);

if (!$subWindow && !$readOnly && $couponID > 0)
{
	if (!$copy)
	{
		$contextMenuItems[] = array('SEPARATOR' => 'Y');
		$contextMenuItems[] = array(
			'ICON' => 'btn_new',
			'TEXT' => Loc::getMessage('BX_SALE_DISCOUNT_COUPONT_CONTEXT_NEW'),
			'LINK' => 'sale_discount_coupon_edit.php?lang='.LANGUAGE_ID.GetFilterParams('filter_')
		);
		$contextMenuItems[] = array(
			'ICON' => 'btn_copy',
			'TEXT' => Loc::getMessage('BX_SALE_DISCOUNT_COUPONT_CONTEXT_COPY'),
			'LINK' => 'sale_discount_coupon_edit.php?lang='.LANGUAGE_ID.'&ID='.$couponID.'&action=copy'.GetFilterParams('filter_')
		);
		$contextMenuItems[] = array(
			'ICON' => 'btn_delete',
			'TEXT' => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_CONTEXT_DELETE'),
			'LINK' => "javascript:if(confirm('".CUtil::JSEscape(Loc::getMessage('BX_SALE_DISCOUNT_COUPON_CONTEXT_DELETE_CONFIRM'))."')) window.location='/bitrix/admin/sale_discount_coupons.php?lang=".LANGUAGE_ID."&ID=".$couponID."&action=delete&".bitrix_sessid_get()."';",
			'WARNING' => 'Y',
		);
	}
}

$contextMenu = new CAdminContextMenu($contextMenuItems);
$contextMenu->Show();
unset($contextMenu, $contextMenuItems);

if (!empty($errors))
{
	$errorMessage = new CAdminMessage(
		array(
			'DETAILS' => implode('<br>', $errors),
			'TYPE' => 'ERROR',
			'MESSAGE' => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_ERR_SAVE'),
			'HTML' => true
		)
	);
	echo $errorMessage->Show();
	unset($errorMessage);
}

$selectFields = array();
if (!$multiCoupons)
{
	$defaultValues = array(
		'DISCOUNT_ID' => '',
		'COUPON' => '',
		'ACTIVE' => 'Y',
		'ACTIVE_FROM' => null,
		'ACTIVE_TO' => null,
		'TYPE' => Internals\DiscountCouponTable::TYPE_ONE_ORDER,
		'MAX_USE' => 0,
		'USE_COUNT' => 0,
		'USER_ID' => 0
	);
	$selectFields = array('ID', 'DISCOUNT_NAME' => 'DISCOUNT.NAME');
	$selectFields = array_merge($selectFields, array_keys($defaultValues));
}
else
{
	$defaultValues = array(
		'COUNT' => '',
		array(
			'DISCOUNT_ID' => '',
			'ACTIVE_FROM' => null,
			'ACTIVE_TO' => null,
			'TYPE' => Internals\DiscountCouponTable::TYPE_ONE_ORDER,
			'MAX_USE' => 0,
		)
	);
}

$coupon = array();
if (!$multiCoupons && $couponID > 0)
{
	$couponIterator = Internals\DiscountCouponTable::getList(array(
		'select' => $selectFields,
		'filter' => array('ID' => $couponID)
	));
	if (!($coupon = $couponIterator->fetch()))
	{
		$couponID = 0;
	}
	unset($couponIterator);
}
if ($couponID == 0)
{
	$coupon = $defaultValues;
}
if (!$multiCoupons)
{
	$coupon['DISCOUNT_NAME'] = (string)$coupon['DISCOUNT_NAME'];
	$coupon['DISCOUNT_ID'] = (int)$coupon['DISCOUNT_ID'];
	$coupon['TYPE'] = (int)$coupon['TYPE'];
	$coupon['USE_COUNT'] = (int)$coupon['USE_COUNT'];
	$coupon['MAX_USE'] = (int)$coupon['MAX_USE'];
	$coupon['USER_ID'] = (int)$coupon['USER_ID'];
}
else
{
	$coupon['COUNT'] = (int)$coupon['COUNT'];
	$coupon['COUPON']['DISCOUNT_ID'] = (int)$coupon['COUPON']['DISCOUNT_ID'];
	$coupon['COUPON']['TYPE'] = (int)$coupon['COUPON']['TYPE'];
	$coupon['COUPON']['MAX_USE'] = (int)$coupon['COUPON']['MAX_USE'];
}

if (!empty($errors))
{
	$coupon = array_merge($coupon, $fields);
}

$control->BeginPrologContent();
CJSCore::Init(array('date'));
$control->EndPrologContent();
$control->BeginEpilogContent();
echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<? echo LANGUAGE_ID; ?>">
<input type="hidden" name="ID" value="<? echo $couponID; ?>">
<?
if ($subWindow)
{
	?><input type="hidden" name="DISCOUNT_ID" value="<? echo $discountID; ?>">
	<input type="hidden" name="MULTI" value="<? echo ($multiCoupons ? 'Y' : 'N');?>"><?
}
if ($copy)
{
	?><input type="hidden" name="action" value="copy"><?
}
if (!empty($returnUrl))
{
	?><input type="hidden" name="return_url" value="<? echo htmlspecialcharsbx($returnUrl); ?>"><?
}
echo bitrix_sessid_post();
$control->EndEpilogContent();
$control->Begin(array(
	'FORM_ACTION' => 'sale_discount_coupon_edit.php?lang='.LANGUAGE_ID
));
$control->BeginNextFormTab();
if ($multiCoupons)
{
	$control->AddEditField($prefix.'COUNT', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_COUNT'), true, array(), ($coupon['COUNT'] > 0 ? $coupon['COUNT'] : ''));
	$control->BeginCustomField($prefix.'PERIOD', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_PERIOD'), false);
	?><tr id="tr_COUPON_PERIOD">
	<td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
	<td width="60%"><?
		$periodValue = '';
		$activeFrom = ($coupon['COUPON']['ACTIVE_FROM'] instanceof Type\DateTime ? $coupon['COUPON']['ACTIVE_FROM']->toString() : '');
		$activeTo = ($coupon['COUPON']['ACTIVE_TO'] instanceof Type\DateTime ? $coupon['COUPON']['ACTIVE_TO']->toString() : '');
		if ($activeFrom != '' || $activeTo != '')
			$periodValue = CAdminCalendar::PERIOD_INTERVAL;

		$calendar = new CAdminCalendar;
		echo $calendar->CalendarPeriodCustom(
			$prefix.'ACTIVE_FROM', $prefix.'ACTIVE_TO',
			$activeFrom, $activeTo,
			true, 19, true,
			array(
				CAdminCalendar::PERIOD_EMPTY => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_PERIOD_EMPTY'),
				CAdminCalendar::PERIOD_INTERVAL => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_PERIOD_INTERVAL')
			),
			$periodValue
		);
		unset($calendar, $activeTo, $activeFrom, $periodValue);
		?></td>
	</tr><?
	$control->EndCustomField($prefix.'PERIOD');
	$control->AddDropDownField(
		$prefix.'TYPE',
		Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_TYPE'),
		true,
		$couponTypes,
		$coupon['COUPON']['TYPE']
	);
	$control->AddEditField($prefix.'MAX_USE', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_MAX_USE'), false, array(), ($coupon['COUPON']['MAX_USE'] > 0 ? $coupon['COUPON']['MAX_USE'] : ''));
	$control->Buttons(false, '');
	$control->Show();
?>
<script type="text/javascript">top.BX.WindowManager.Get().adjustSizeEx();</script>
<?
}
else
{
	if ($couponID > 0 && !$copy)
		$control->AddViewField($prefix.'ID', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_ID'), $couponID, false);
	$control->AddCheckBoxField($prefix.'ACTIVE', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_ACTIVE'), true, array('Y', 'N'), $coupon['ACTIVE'] == 'Y');
	if ($couponID > 0)
	{
		$discountName = '<a href="sale_discount.php?lang='.LANGUAGE_ID.'&ID='.$coupon['DISCOUNT_ID'].'">['.$coupon['DISCOUNT_ID'].']</a>';
		if ($coupon['DISCOUNT_NAME'] !== '')
			$discountName .= ' '.$coupon['DISCOUNT_NAME'];
		$discountName .= '<input type="hidden" name="DISCOUNT_ID" value="'.$coupon['DISCOUNT_ID'].'">';
		$control->AddViewField('DISCOUNT_ID', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_DISCOUNT'), $discountName, true);
	}
	elseif (!$subWindow)
	{
		$discountList = array();
		$discountIterator = Internals\DiscountTable::getList(array(
			'select' => array('ID', 'NAME'),
			'filter' => array('ACTIVE' => 'Y'),
			'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
		));
		while ($discount = $discountIterator->fetch())
		{
			$discount['ID'] = (int)$discount['ID'];
			$discount['NAME'] = (string)$discount['NAME'];
			$discountList[$discount['ID']] = '['.$discount['ID'].']'.($discount['NAME'] !== '' ? ' '.$discount['NAME'] : '');
		}
		unset($discount, $discountIterator);
		if (!empty($discountList))
		{
			$control->AddDropDownField(
				'DISCOUNT_ID',
				Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_DISCOUNT'),
				true,
				$discountList,
				$coupon['DISCOUNT_ID']
			);
		}
		else
		{
			$control->BeginCustomField('DISCOUNT_ID', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_DISCOUNT'), true);
			$dicountEditPath = 'sale_discount_edit.php?lang='.LANGUAGE_ID.'&return_url='.urlencode($APPLICATION->GetCurPageParam());
			?><tr id="tr_DISCOUNT_ID">
			<td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
			<td width="60%">
				<? echo Loc::getMessage('BX_SALE_DISCOUNT_COUPON_MESS_DISCOUNT_ABSENT'); ?> <a href="<? echo $dicountEditPath ?>"><?
				echo Loc::getMessage('BX_SALE_DISCOUNT_COUPON_MESS_DISCOUNT_ADD'); ?></a></td>
			</tr><?
			unset($dicountEditPath);
			$control->EndCustomField('DISCOUNT_ID');
		}
		unset($discountList);
	}
	$control->BeginCustomField('COUPON', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_COUPON'), true);
	?><tr id="tr_COUPON" class="adm-detail-required-field">
		<td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
		<td width="60%" id="td_COUPON_VALUE">
			<input type="text" id="COUPON" name="COUPON" size="32" maxlength="32" value="<? echo htmlspecialcharsbx($coupon['COUPON']); ?>" />&nbsp;
			<input type="button" value="<? echo Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_COUPON_GENERATE'); ?>" id="COUPON_GENERATE">
		</td>
	</tr><?
	$control->EndCustomField('COUPON',
		'<input type="hidden" name="COUPON" value="'.htmlspecialcharsbx($coupon['COUPON']).'">'
	);
	if ($couponID == 0 || $coupon['USE_COUNT'] == 0 || !isset($couponTypes[$coupon['TYPE']]))
	{
		$control->AddDropDownField(
			$prefix.'TYPE',
			Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_TYPE'),
			true,
			$couponTypes,
			$coupon['TYPE']
		);
	}
	else
	{
		$control->AddViewField(
			$prefix.'TYPE',
			Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_TYPE'),
			$couponTypes[$coupon['TYPE']],
			true
		);
	}
	$control->BeginCustomField($prefix.'PERIOD', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_PERIOD'), false);
	?><tr id="tr_COUPON_PERIOD">
		<td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
		<td width="60%"><?
		$periodValue = '';
		$activeFrom = ($coupon['ACTIVE_FROM'] instanceof Type\DateTime ? $coupon['ACTIVE_FROM']->toString() : '');
		$activeTo = ($coupon['ACTIVE_TO'] instanceof Type\DateTime ? $coupon['ACTIVE_TO']->toString() : '');
		if ($activeFrom != '' || $activeTo != '')
			$periodValue = CAdminCalendar::PERIOD_INTERVAL;

		$calendar = new CAdminCalendar;
		echo $calendar->CalendarPeriodCustom(
			$prefix.'ACTIVE_FROM', $prefix.'ACTIVE_TO',
			$activeFrom, $activeTo,
			true, 19, true,
			array(
				CAdminCalendar::PERIOD_EMPTY => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_PERIOD_EMPTY'),
				CAdminCalendar::PERIOD_INTERVAL => Loc::getMessage('BX_SALE_DISCOUNT_COUPON_PERIOD_INTERVAL')
			),
			$periodValue
		);
			unset($activeTo, $activeFrom, $periodValue);
		?></td>
	</tr><?
	$control->EndCustomField($prefix.'PERIOD');
	$control->BeginCustomField($prefix.'USER_ID', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_USER_ID'), false);
	?><tr id="tr_USER_ID">
		<td width="40%"><? echo $control->GetCustomLabelHTML(); ?></td>
		<td width="60%"><?
			echo FindUserID(
				$prefix.'USER_ID',
				($coupon['USER_ID'] > 0 ? $coupon['USER_ID'] : ''),
				'',
				(!$subWindow ? 'couponControl_form' : 'subCouponControl_form')
			);
		?></td>
	</tr><?
	$control->EndCustomField($prefix.'USER_ID');
	$control->AddEditField($prefix.'MAX_USE', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_MAX_USE'), false, array(), ($coupon['MAX_USE'] > 0 ? $coupon['MAX_USE'] : ''));
	$control->AddTextField($prefix.'DESCRIPTION', Loc::getMessage('BX_SALE_DISCOUNT_COUPON_FIELD_DESCRIPTION'), $coupon['DESCRIPTION'], array(), false);
	if ($subWindow)
	{
		$control->Buttons(false, '');
	}
	else
	{
		$control->Buttons(
			array(
				'disabled' => $readOnly,
				'back_url' => "sale_discount_coupons.php?lang=".LANGUAGE_ID.GetFilterParams('filter_')
			)
		);
	}
	$control->Show();
?>
<script type="text/javascript">
	BX.ready(function(){
		var obCouponValue = BX('COUPON'),
			obCouponBtn = BX('COUPON_GENERATE');
		if (!!obCouponValue && !!obCouponBtn)
		{
			BX.bind(obCouponBtn, 'click', function(){
				var url,
					data;

				BX.showWait();
				url = '/bitrix/tools/sale/generate_coupon.php';
				data = {
					lang: BX.message('LANGUAGE_ID'),
					sessid: BX.bitrix_sessid()
				};
				BX.ajax.loadJSON(
					url,
					data,
					function(data){
						var boolFlag = true,
							strErr = '',
							obCouponErr,
							obCouponCell;
						if (BX.type.isString(data))
						{
							boolFlag = false;
							strErr = data;
						}
						else
						{
							if (data.STATUS != 'OK')
							{
								boolFlag = false;
								strErr = data.MESSAGE;
							}
						}
						obCouponErr = BX('COUPON_GENERATE_ERR');
						if (boolFlag)
						{
							obCouponValue.value = data.COUPON;
							if (!!obCouponErr)
								obCouponErr = BX.remove(obCouponErr);
						}
						else
						{
							if (!obCouponErr)
							{
								obCouponCell = BX('td_COUPON_VALUE');
								if (!!obCouponCell)
								{
									obCouponErr = obCouponCell.insertBefore(BX.create(
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
							}
							BX.adjust(obCouponErr, {props: { title: strErr }});
						}
						BX.closeWait();
					});
			});
		}
	});
	<?
	if ($subWindow)
	{
		?>top.BX.WindowManager.Get().adjustSizeEx();
		<?
	}
?></script><?
}
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');