<?
/** @global CMain $APPLICATION */
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\Entity;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Internals;

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/prolog.php');

$saleModulePermissions = $APPLICATION->GetGroupRight('sale');
$readOnly = ($saleModulePermissions < 'W');
if ($saleModulePermissions < 'R')
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

Loader::includeModule('sale');
Loc::loadMessages(__FILE__);

$adminListTableID = 'tbl_sale_discount';

$adminSort = new CAdminSorting($adminListTableID, 'ID', 'ASC');
$adminList = new CAdminList($adminListTableID, $adminSort);

$filter = array();
$filterFields = array(
	'filter_lang',
	'filter_active'
);

$adminList->InitFilter($filterFields);

if (!empty($_REQUEST['filter_lang']))
{
	$_REQUEST['filter_lang'] = (string)$_REQUEST['filter_lang'];
	if ($_REQUEST['filter_lang'] != 'NOT_REF')
		$filter['LID'] = (string)$_REQUEST['filter_lang'];
}
if (!empty($_REQUEST['filter_active']))
{
	$_REQUEST['filter_active'] = (string)$_REQUEST['filter_active'];
	if ($_REQUEST['filter_active'] == 'Y' || $_REQUEST['filter_active'] == 'N')
		$filter['ACTIVE'] = $_REQUEST['filter_active'];
}

if (!$readOnly && $adminList->EditAction())
{
	if (isset($FIELDS) && is_array($FIELDS))
	{
		$conn = Application::getConnection();
		foreach ($FIELDS as $ID => $fields)
		{
			$ID = (int)$ID;
			if ($ID <= 0 || !$adminList->IsUpdated($ID))
				continue;

			$conn->startTransaction();
			$result = Internals\DiscountTable::update($ID, $fields);
			if ($result->isSuccess())
			{
				$conn->commitTransaction();
			}
			else
			{
				$conn->rollbackTransaction();
				$adminList->AddUpdateError(implode('<br>', $result->getErrorMessages()), $ID);
			}
		}
		unset($fields, $ID);
	}
}

if (!$readOnly && ($listID = $adminList->GroupAction()))
{
	if ($_REQUEST['action_target'] == 'selected')
	{
		$listID = array();
		$discountIterator = Internals\DiscountTable::getList(array(
			'select' => array('ID'),
			'filter' => $filter
		));
		while ($discount = $discountIterator->fetch())
			$listID[] = $discount['ID'];
	}

	$listID = array_filter($listID);
	if (!empty($listID))
	{
		switch ($_REQUEST['action'])
		{
			case 'activate':
			case 'deactivate':
				$fields = array(
					'ACTIVE' => ($_REQUEST['action'] == 'activate' ? 'Y' : 'N')
				);
				foreach ($listID as &$discountID)
				{
					$result = Internals\DiscountTable::update($discountID, $fields);
					if (!$result->isSuccess())
					{
						$adminList->AddGroupError(implode('<br>', $result->getErrorMessages(), $discountID));
					}
				}
				unset($discountID, $fields);
				break;
			case 'delete':
				foreach ($listID as &$discountID)
				{
					$result = Internals\DiscountTable::delete($discountID);
					if (!$result->isSuccess())
					{
						$adminList->AddGroupError(implode('<br>', $result->getErrorMessages(), $discountID));
					}
				}
				unset($discountID);
				break;
		}
	}
	unset($listID);
}

$headerList = array();
$headerList['ID'] = array(
	'id' => 'ID',
	'content' => 'ID',
	'title' => '',
	'sort' => 'ID',
	'default' => true
);
$headerList['LID'] = array(
	'id' => 'LID',
	'content' => Loc::getMessage('PERS_TYPE_LID'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_LID'),
	'sort' => 'LID',
	'default' => true
);
$headerList['NAME'] = array(
	'id' => 'NAME',
	'content' => Loc::getMessage('BT_SALE_DISCOUNT_ADM_TITLE_NAME'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_NAME'),
	'default' => true
);
$headerList['ACTIVE'] = array(
	'id' => 'ACTIVE',
	'content' => Loc::getMessage('PERS_TYPE_ACTIVE'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_ACTIVE'),
	'sort' => 'ACTIVE',
	'default' => true
);
$headerList['PRIORITY'] = array(
	'id' => 'PRIORITY',
	'content' => Loc::getMessage('SDSN_PRIORITY'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_PRIORITY'),
	'sort' => 'PRIORITY',
	'default' => true
);
$headerList['SORT'] = array(
	'id' => 'SORT',
	'content' => Loc::getMessage("PERS_TYPE_SORT"),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_SORT'),
	'sort' => 'SORT',
	'default' => true
);
$headerList['LAST_DISCOUNT'] = array(
	'id' => 'LAST_DISCOUNT',
	'content' => Loc::getMessage('SDSN_LAST_DISCOUNT_NEW'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_LAST_DISCOUNT'),
	'sort' => 'LAST_DISCOUNT',
	'default' => true
);
$headerList['ACTIVE_FROM'] = array(
	'id' => 'ACTIVE_FROM',
	'content' => Loc::getMessage("SDSN_ACTIVE_FROM"),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_ACTIVE_FROM'),
	'sort' => 'ACTIVE_FROM',
	'default' => true
);
$headerList['ACTIVE_TO'] = array(
	'id' => 'ACTIVE_TO',
	'content' => Loc::getMessage("SDSN_ACTIVE_TO"),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_ACTIVE_TO'),
	'sort' => 'ACTIVE_TO',
	'default' => true
);
$headerList['MODIFIED_BY'] = array(
	'id' => 'MODIFIED_BY',
	'content' => Loc::getMessage('SDSN_MODIFIED_BY_NEW'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_MODIFIED_BY'),
	'sort' => 'MODIFIED_BY',
	'default' => true
);
$headerList['TIMESTAMP_X'] = array(
	'id' => 'TIMESTAMP_X',
	'content' => Loc::getMessage('SDSN_TIMESTAMP_X'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_TIMESTAMP_X'),
	'sort' => 'TIMESTAMP_X',
	'default' => true
);
$headerList['CREATED_BY'] = array(
	'id' => 'CREATED_BY',
	'content' => Loc::getMessage('SDSN_CREATED_BY_NEW'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_CREATED_BY'),
	'sort' => 'CREATED_BY',
	'default' => false
);
$headerList['DATE_CREATE'] = array(
	'id' => 'DATE_CREATE',
	'content' => Loc::getMessage('SDSN_DATE_CREATE'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_DATE_CREATE'),
	'sort' => 'DATE_CREATE',
	'default' => false
);
$headerList['XML_ID'] = array(
	'id' => 'XML_ID',
	'content' => Loc::getMessage('SDSN_XML_ID'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_XML_ID'),
	'sort' => 'XML_ID',
	'default' => false
);
$headerList['USE_COUPONS'] = array(
	'id' => 'USE_COUPONS',
	'content' => Loc::getMessage('SDSN_USE_COUPONS'),
	'title' => Loc::getMessage('BX_SALE_ADM_DSC_HEADER_TITLE_USE_COUPONS'),
	'sort' => 'USE_COUPONS',
	'default' => true
);
$adminList->AddHeaders($headerList);

$selectFields = array_fill_keys($adminList->GetVisibleHeaderColumns(), true);
$selectFields['ID'] = true;
$selectFieldsMap = array_fill_keys(array_keys($headerList), false);

$selectFieldsMap = array_merge($selectFieldsMap, $selectFields);
$selectFields['ACTIVE'] = true;

$arSitesShop = array();
$arSitesTmp = array();
$siteList = array();
$siteIterator = SiteTable::getList(array(
	'select' => array('LID', 'NAME', 'ACTIVE'),
	'order' => array('SORT' => 'ASC')
));
while ($site = $siteIterator->fetch())
{
	$siteList[$site['LID']] = $site['LID'];
	if ($site['ACTIVE'] != 'Y')
		continue;
	$arSitesTmp[] = array(
		'ID' => $site['LID'],
		'NAME' => $site['NAME']
	);
	$saleSite = (string)Option::get('sale', 'SHOP_SITE_'.$site['LID']);
	if ($site['LID'] == $site)
	{
		$arSitesShop[] = array(
			'ID' => $site['LID'],
			'NAME' => $site['NAME']
		);
	}
}
unset($site, $siteIterator);
if (empty($arSitesShop))
{
	$arSitesShop = $arSitesTmp;
}
unset($arSitesTmp);

if (!isset($by))
	$by = 'ID';
if (!isset($order))
	$order = 'ASC';

$usePageNavigation = true;
$navyParams = array();
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel')
{
	$usePageNavigation = false;
}
else
{
	$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize($adminListTableID));
	if ($navyParams['SHOW_ALL'])
	{
		$usePageNavigation = false;
	}
	else
	{
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}
}
$getListParams = array(
	'select' => array_keys($selectFields),
	'filter' => $filter,
	'order' => array($by => $order)
);
if ($usePageNavigation)
{
	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}

$discountIterator = new CAdminResult(Internals\DiscountTable::getList($getListParams), $adminListTableID);
if ($usePageNavigation)
{
	$countQuery = new Entity\Query(Internals\DiscountTable::getEntity());
	$countQuery->addSelect(new Entity\ExpressionField('CNT', 'COUNT(1)'));
	$countQuery->setFilter($getListParams['filter']);
	$totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
	$totalCount = (int)$totalCount['CNT'];
	$totalPages = ceil($totalCount/$getListParams['limit']);
	unset($countQuery);
	$discountIterator->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
	$discountIterator->NavRecordCount = $totalCount;
	$discountIterator->NavPageCount = $totalPages;
	$discountIterator->NavPageNomer = $navyParams['PAGEN'];
}
else
{
	$discountIterator->NavStart();
}

$adminList->NavText($discountIterator->GetNavPrint(Loc::getMessage("BT_SALE_DISCOUNT_LIST_MESS_NAV")));

$userList = array();
$arUserID = array();
$nameFormat = CSite::GetNameFormat(true);

$arRows = array();
while ($discount = $discountIterator->Fetch())
{
	$discount['ID'] = (int)$discount['ID'];
	if ($selectFieldsMap['CREATED_BY'])
	{
		$discount['CREATED_BY'] = (int)$discount['CREATED_BY'];
		if ($discount['CREATED_BY'] > 0)
			$arUserID[$discount['CREATED_BY']] = true;
	}
	if ($selectFieldsMap['MODIFIED_BY'])
	{
		$discount['MODIFIED_BY'] = (int)$discount['MODIFIED_BY'];
		if ($discount['MODIFIED_BY'] > 0)
			$arUserID[$discount['MODIFIED_BY']] = true;
	}
	$urlEdit = 'sale_discount_edit.php?ID='.$discount['ID'].'&lang='.LANGUAGE_ID.GetFilterParams('filter_');
	$arRows[$discount['ID']] = $row = &$adminList->AddRow(
		$discount['ID'],
		$discount,
		$urlEdit,
		Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_EDIT_DISCOUNT')
	);
	$row->AddViewField('ID', '<a href="'.$urlEdit.'">'.$discount['ID'].'</a>');

	if ($selectFieldsMap['DATE_CREATE'])
		$row->AddViewField('DATE_CREATE', $discount['DATE_CREATE']);
	if ($selectFieldsMap['TIMESTAMP_X'])
		$row->AddViewField('TIMESTAMP_X', $discount['TIMESTAMP_X']);
	if ($selectFieldsMap['USE_COUPONS'])
		$row->AddCheckField('USE_COUPONS', false);

	if (!$readOnly)
	{
		if ($selectFieldsMap['LID'])
			$row->AddViewField('LID', $siteList[$discount['LID']]);
		if ($selectFieldsMap['ACTIVE'])
			$row->AddCheckField('ACTIVE');

		if ($selectFieldsMap['NAME'])
			$row->AddInputField('NAME', array('size' => 50, 'maxlength' => 255));

		if ($selectFieldsMap['SORT'])
			$row->AddInputField('SORT', array('size' => 4));

		if ($selectFieldsMap['ACTIVE_FROM'])
			$row->AddCalendarField('ACTIVE_FROM');
		if ($selectFieldsMap['ACTIVE_TO'])
			$row->AddCalendarField('ACTIVE_TO');

		if ($selectFieldsMap['PRIORITY'])
			$row->AddInputField('PRIORITY');
		if ($selectFieldsMap['LAST_DISCOUNT'])
			$row->AddCheckField('LAST_DISCOUNT');

		if ($selectFieldsMap['XML_ID'])
			$row->AddInputField('XML_ID', array('size' => 20, 'maxlength' => 255));
	}
	else
	{
		if ($selectFieldsMap['LID'])
			$row->AddViewField('LID', $siteList[$discount['LID']]);
		if ($selectFieldsMap['ACTIVE'])
			$row->AddCheckField('ACTIVE', false);

		if ($selectFieldsMap['NAME'])
			$row->AddInputField('NAME', false);

		if ($selectFieldsMap['SORT'])
			$row->AddInputField('SORT', false);

		if ($selectFieldsMap['ACTIVE_FROM'])
			$row->AddCalendarField('ACTIVE_FROM', false);
		if ($selectFieldsMap['ACTIVE_TO'])
			$row->AddCalendarField('ACTIVE_TO', false);

		if ($selectFieldsMap['PRIORITY'])
			$row->AddInputField('PRIORITY', false);
		if ($selectFieldsMap['LAST_DISCOUNT'])
			$row->AddCheckField('LAST_DISCOUNT', false);

		if ($selectFieldsMap['XML_ID'])
			$row->AddInputField('XML_ID', false);
	}

	$arActions = array();
	$arActions[] = array(
		'ICON' => 'edit',
		'TEXT' => Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_EDIT_DISCOUNT_SHORT'),
		'ACTION' => $adminList->ActionRedirect($urlEdit),
		'DEFAULT' => true
	);
	if (!$readOnly)
	{
		$arActions[] = array(
			'ICON' => 'copy',
			'TEXT' => Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_COPY_DISCOUNT_SHORT'),
			'ACTION' => $adminList->ActionRedirect($urlEdit.'&action=copy'),
			'DEFAULT' => false,
		);
		if ($discount['ACTIVE'] == 'Y')
		{
			$arActions[] = array(
				'ICON' => 'deactivate',
				'TEXT' => Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_DEACTIVATE_DISCOUNT_SHORT'),
				'ACTION' => $adminList->ActionDoGroup($discount['ID'], 'deactivate'),
				'DEFAULT' => false,
			);
		}
		else
		{
			$arActions[] = array(
				'ICON' => 'activate',
				'TEXT' => Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_ACTIVATE_DISCOUNT_SHORT'),
				'ACTION' => $adminList->ActionDoGroup($discount['ID'], 'activate'),
				'DEFAULT' => false,
			);
		}
		$arActions[] = array('SEPARATOR' => true);
		$arActions[] = array(
			'ICON' => 'delete',
			'TEXT' => Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_DELETE_DISCOUNT_SHORT'),
			'ACTION' => "if(confirm('".Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_DELETE_DISCOUNT_CONFIRM')."')) ".$adminList->ActionDoGroup($discount['ID'], 'delete'),
			'DEFAULT' => false,
		);
	}

	$row->AddActions($arActions);
}
if (isset($row))
	unset($row);

if ($selectFieldsMap['CREATED_BY'] || $selectFieldsMap['MODIFIED_BY'])
{
	if (!empty($arUserID))
	{
		$userIterator = UserTable::getList(array(
			'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
			'filter' => array('ID' => array_keys($arUserID)),
		));
		while ($arOneUser = $userIterator->fetch())
		{
			$arOneUser['ID'] = (int)$arOneUser['ID'];
			$userList[$arOneUser['ID']] = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arOneUser['ID'].'">'.CUser::FormatName($nameFormat, $arOneUser).'</a>';
		}
		unset($arOneUser, $userIterator);
	}

	foreach ($arRows as &$row)
	{
		if ($selectFieldsMap['CREATED_BY'])
		{
			$strCreatedBy = '';
			if ($row->arRes['CREATED_BY'] > 0 && isset($userList[$row->arRes['CREATED_BY']]))
			{
				$strCreatedBy = $userList[$row->arRes['CREATED_BY']];
			}
			$row->AddViewField("CREATED_BY", $strCreatedBy);
		}
		if ($selectFieldsMap['MODIFIED_BY'])
		{
			$strModifiedBy = '';
			if ($row->arRes['MODIFIED_BY'] > 0 && isset($userList[$row->arRes['MODIFIED_BY']]))
			{
				$strModifiedBy = $userList[$row->arRes['MODIFIED_BY']];
			}
			$row->AddViewField("MODIFIED_BY", $strModifiedBy);
		}
	}
	if (isset($row))
		unset($row);
}

$adminList->AddFooter(
	array(
		array(
			'title' => Loc::getMessage('MAIN_ADMIN_LIST_SELECTED'),
			'value' => $discountIterator->SelectedRowsCount()
		),
		array(
			'counter' => true,
			'title' => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
			'value' => "0"
		),
	)
);

$adminList->AddGroupActionTable(
	array(
		"delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	)
);

if (!$readOnly)
{
	$siteLID = '';
	$arSiteMenu = array();

	if (count($arSitesShop) == 1)
	{
		$siteLID = "&LID=".$arSitesShop[0]['ID'];
	}
	else
	{
		foreach ($arSitesShop as $val)
		{
			$arSiteMenu[] = array(
				"TEXT" => $val["NAME"]." (".$val['ID'].")",
				"ACTION" => "window.location = 'sale_discount_edit.php?lang=".LANGUAGE_ID."&LID=".$val['ID']."';"
			);
		}
	}
	$aContext = array(
		array(
			"TEXT" => Loc::getMessage("BT_SALE_DISCOUNT_LIST_MESS_NEW_DISCOUNT"),
			"ICON" => "btn_new",
			"LINK" => "sale_discount_edit.php?lang=".LANGUAGE_ID.$siteLID,
			"TITLE" => Loc::getMessage("BT_SALE_DISCOUNT_LIST_MESS_NEW_DISCOUNT_TITLE"),
			"MENU" => $arSiteMenu
		),
	);

	$adminList->AddAdminContextMenu($aContext);
}

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('BT_SALE_DISCOUNT_LIST_MESS_TITLE'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$adminListTableID.'_filter',
	array(
		Loc::getMessage('LANG_FILTER_NAME'),
		Loc::getMessage('FILTER_ACTIVE')
	)
);

$oFilter->Begin();
?>
	<tr>
		<td><?echo Loc::getMessage('LANG_FILTER_NAME')?>:</td>
		<td><?echo CLang::SelectBox('filter_lang', (isset($filter['LID']) ? $filter['LID'] : ''), Loc::getMessage('DS_ALL')); ?>
	</tr>
	<tr>
		<td><? echo Loc::getMessage('FILTER_ACTIVE'); ?>:</td>
		<td>
			<select name="filter_active">
				<option value=""><? echo Loc::getMessage('DS_ALL'); ?></option>
				<option value="Y"<? if (isset($filter['ACTIVE']) && $filter['ACTIVE'] == 'Y') echo ' selected'; ?>><?= htmlspecialcharsex(Loc::getMessage('DSC_YES')) ?></option>
				<option value="N"<? if (isset($filter['ACTIVE']) && $filter['ACTIVE'] == 'N') echo ' selected'; ?>><?= htmlspecialcharsex(Loc::getMessage('DSC_NO')) ?></option>
			</select>
		</td>
	</tr>
<?
$oFilter->Buttons(
	array(
		"table_id" => $adminListTableID,
		"url" => $APPLICATION->GetCurPage(),
		"form" => "find_form"
	)
);
$oFilter->End();
?>
</form>
<?
$adminList->DisplayList();

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');