<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;
global $DB;

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_discount')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_discount');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");
if (!CBXFeatures::IsFeatureEnabled('CatDiscountSave'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
	CCatalogDiscountSave::Disable();
	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}
IncludeModuleLangFile(__FILE__);

$sTableID = "tbl_catalog_disc_save";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$FilterArr = array(
	"find_id_from",
	"find_id_to",
	"find_site_id",
	"find_name",
	"find_active",
	"find_currency",
	"find_active_from_from",
	"find_active_from_to",
	"find_active_to_from",
	"find_active_to_to",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = array(

);
if (!empty($find_id_from))
	$arFilter['>=ID'] = $find_id_from;
if (!empty($find_id_to))
	$arFilter['<=ID'] = $find_id_to;
if (!empty($find_site_id))
	$arFilter['=SITE_ID'] = $find_site_id;
if (strlen($find_name) > 0)
	$arFilter['%NAME'] = $find_name;
if (!empty($find_active))
	$arFilter['ACTIVE'] = $find_active;
if (!empty($find_currency))
	$arFilter['CURRENCY'] = $find_currency;
if (!empty($find_active_from_from))
	$arFilter['+>=ACTIVE_FROM'] = $find_active_from_from;
if (!empty($find_active_from_to))
	$arFilter['+<=ACTIVE_FROM'] = $find_active_from_to;
if (!empty($find_active_to_from))
	$arFilter['+>=ACTIVE_TO'] = $find_active_to_from;
if (!empty($find_active_to_to))
	$arFilter['+<=ACTIVE_TO'] = $find_active_to_to;

if($lAdmin->EditAction() && !$bReadOnly)
{
	$obDiscSave = new CCatalogDiscountSave();
	foreach($_POST['FIELDS'] as $ID=>$arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		$DB->StartTransaction();
		$ID = intval($ID);

		if(($rsDiscSaves = $obDiscSave->GetByID($ID)) && ($arData = $rsDiscSaves->Fetch()))
		{
			foreach($arFields as $key=>$value)
			$arData[$key]=$value;
			if(!$obDiscSave->Update($ID, $arData))
			{
				if ($ex = $APPLICATION->GetException())
				{
					$lAdmin->AddGroupError(str_replace('ERR',$ex->GetString(),GetMessage("BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_ERR")), $ID);
				}
				else
				{
					$lAdmin->AddGroupError(GetMessage("BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_UNKNOWN"), $ID);
				}
				$DB->Rollback();
			}
		}
		else
		{
			$lAdmin->AddGroupError(GetMessage('BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_ABSENT'), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if(($arID = $lAdmin->GroupAction()) && !$bReadOnly)
{
	$obDiscSave = new CCatalogDiscountSave();
	if($_REQUEST['action_target']=='selected')
	{
		$rsDiscSaves = $obDiscSave->GetList(array($by=>$order), $arFilter);
		while($arRes = $rsDiscSaves->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID)
	{
		if(intval($ID)<=0)
			continue;
		$ID = intval($ID);

		switch($_REQUEST['action'])
		{
		case "delete":
			@set_time_limit(0);
			$DB->StartTransaction();
			if(!CCatalogDiscountSave::Delete($ID))
			{
				if ($ex = $APPLICATION->GetException())
				{
					$lAdmin->AddGroupError(str_replace('ERR',$ex->GetString(),GetMessage("BT_CAT_DISC_SAVE_ADM_ERR_DELETE_ERR")), $ID);
				}
				else
				{
					$lAdmin->AddGroupError(GetMessage("BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_DELETE"), $ID);
				}
				$DB->Rollback();
			}
			$DB->Commit();
			break;

		case "activate":
		case "deactivate":
			if(($rsDiscSaves = $obDiscSave->GetByID($ID)) && ($arFields = $rsDiscSaves->Fetch()))
			{
				$arFields["ACTIVE"] = ($_REQUEST['action'] == "activate" ? "Y" : "N");
				if(!$obDiscSave->Update($ID, $arFields))
				{
					if ($ex = $APPLICATION->GetException())
					{
						$lAdmin->AddGroupError(str_replace('ERR',$ex->GetString(),GetMessage("BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_ERR")), $ID);
					}
					else
					{
						$lAdmin->AddGroupError(GetMessage("BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_UNKNOWN"), $ID);
					}
				}
			}
			else
			{
				$lAdmin->AddGroupError(GetMessage('BT_CAT_DISC_SAVE_ADM_ERR_UPDATE_ABSENT'), $ID);
			}
			break;
		}
	}
}

$lAdmin->AddHeaders(array(
	array(
		"id" => "ID",
		"content" => "ID",
		"sort" => "ID",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" => "SITE_ID",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_SITE_ID"),
		"sort" => "SITE_ID",
		"default" => true,
	),
	array(
		"id" => "NAME",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_NAME"),
		"sort" => "NAME",
		"default" => true,
	),
	array(
		"id" => "ACTIVE",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_ACTIVE"),
		"sort" => "ACTIVE",
		"default" => true,
	),
	array(
		"id" => "SORT",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_SORT"),
		"sort" => "SORT",
		"align" => "right",
		"default" => true,
	),
	array(
		"id" =>"CURRENCY",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_CURRENCY"),
		"sort" => "CURRENCY",
	),
	array(
		"id" =>"ACTIVE_FROM",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_ACTIVE_FROM"),
		"sort" => "ACTIVE_FROM",
	),
	array(
		"id" => "ACTIVE_TO",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_ACTIVE_TO"),
		"sort" => "ACTIVE_TO",
	),
	array(
		"id" => "ACTION",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_ACTION"),
		"sort" => "",
	),
	array(
		"id" =>"COUNT_FROM",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_COUNT_FROM"),
		"sort" => "COUNT_FROM",
	),
	array(
		"id" => "COUNT_TO",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_COUNT_TO"),
		"sort" => "COUNT_TO",
	),
	array(
		"id" => "COUNT",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_COUNT"),
		"sort" => "",
	),
	array(
		"id" => "XML_ID",
		"content" => GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_XML_ID"),
		"sort" => "XML_ID",
	),
	array("id" => "MODIFIED_BY", "content" => GetMessage('BT_CAT_DISC_SAVE_ADM_TITLE_MODIFIED_BY'), "sort" => "MODIFIED_BY", "default" => true),
	array("id" => "TIMESTAMP_X", "content" => GetMessage('BT_CAT_DISC_SAVE_ADM_TITLE_TIMESTAMP_X'), "sort" => "TIMESTAMP_X", "default" => true),
	array("id" => "CREATED_BY", "content" => GetMessage('BT_CAT_DISC_SAVE_ADM_TITLE_CREATED_BY'), "sort" => "CREATED_BY", "default" => false),
	array("id" => "DATE_CREATE", "content" => GetMessage('BT_CAT_DISC_SAVE_ADM_TITLE_DATE_CREATE'), "sort" => "DATE_CREATE", "default" => false),
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

$intKey = array_search('ACTION', $arSelectFields);
if (false !== $intKey)
{
	$arSelectFields[] = 'ACTION_SIZE';
	$arSelectFields[] = 'ACTION_TYPE';
	unset($arSelectFields[$intKey]);
}

$intKey = array_search('COUNT', $arSelectFields);
if (false !== $intKey)
{
	$arSelectFields[] = 'COUNT_SIZE';
	$arSelectFields[] = 'COUNT_TYPE';
	unset($arSelectFields[$intKey]);
}

$arUserList = array();
$strNameFormat = CSite::GetNameFormat(true);

$arSiteList = array();
$arSiteLinkList = array();
if (array_key_exists('SITE_ID', $arSelectFieldsMap))
{
	$rsSites = CSite::GetList(($by2 = 'sort'),($order2 = 'asc'));
	while ($arSite = $rsSites->Fetch())
	{
		$arSiteList[$arSite['LID']] = $arSite['LID'];
		$arSiteLinkList[$arSite['LID']] = '<a href="/bitrix/admin/site_edit.php?lang='.urlencode(LANGUAGE_ID).'&LID='.urlencode($arSite['LID']).'" title="'.GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_SITE_ID').'">'.htmlspecialcharsex($arSite['LID']).'</a>';
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

$arPeriodTypeList = CCatalogDiscountSave::GetPeriodTypeList(true);

$arSelectFields = array_values($arSelectFields);
$obDiscSave = new CCatalogDiscountSave();
$rsDiscSaves = $obDiscSave->GetList(array($by=>$order), $arFilter, false, false, $arSelectFields);

$rsDiscSaves = new CAdminResult($rsDiscSaves, $sTableID);

$rsDiscSaves->NavStart();

$lAdmin->NavText($rsDiscSaves->GetNavPrint(GetMessage("BT_CAT_DISC_SAVE_ADM_DISCOUNTS")));

while($arRes = $rsDiscSaves->Fetch())
{
	$row = &$lAdmin->AddRow($arRes['ID'], $arRes);

	$strCreatedBy = '';
	$strModifiedBy = '';
	if (array_key_exists('CREATED_BY', $arSelectFieldsMap))
	{
		$arRes['CREATED_BY'] = intval($arRes['CREATED_BY']);
		if (0 < $arRes['CREATED_BY'])
		{
			if (!array_key_exists($arRes['CREATED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arRes['CREATED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arRes['CREATED_BY']]))
				$strCreatedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arRes['CREATED_BY'].'">'.$arUserList[$arRes['CREATED_BY']].'</a>';
		}
	}
	if (array_key_exists('MODIFIED_BY', $arSelectFieldsMap))
	{
		$arRes['MODIFIED_BY'] = intval($arRes['MODIFIED_BY']);
		if (0 < $arRes['MODIFIED_BY'])
		{
			if (!array_key_exists($arRes['MODIFIED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arRes['MODIFIED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arRes['MODIFIED_BY']]))
				$strModifiedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arRes['MODIFIED_BY'].'">'.$arUserList[$arRes['MODIFIED_BY']].'</a>';
		}
	}

	if (array_key_exists('CREATED_BY', $arSelectFieldsMap))
		$row->AddViewField("CREATED_BY", $strCreatedBy);
	if (array_key_exists('DATE_CREATE', $arSelectFieldsMap))
		$row->AddViewField("DATE_CREATE", $arRes['DATE_CREATE']);
	if (array_key_exists('MODIFIED_BY', $arSelectFieldsMap))
		$row->AddViewField("MODIFIED_BY", $strModifiedBy);
	if (array_key_exists('TIMESTAMP_X', $arSelectFieldsMap))
		$row->AddViewField("TIMESTAMP_X", $arRes['TIMESTAMP_X']);

	$row->AddViewField("ID", '<a href="/bitrix/admin/cat_discsave_edit.php?lang='.urlencode(LANGUAGE_ID).'&ID='.$arRes["ID"].'">'.$arRes["ID"].'</a>');

	if (array_key_exists('ACTION', $arSelectFieldsMap))
	{
		if (intval($arRes['ACTION_SIZE']) == 0)
		{
			$strViewAction = '';
		}
		else
		{
			$strViewAction = str_replace('#TYPE#',htmlspecialcharsex($arPeriodTypeList[$arRes['ACTION_TYPE']]),GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_ACTION_TYPE')).'<br />'.str_replace('#SIZE#',$arRes['ACTION_SIZE'],GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_ACTION_SIZE'));
		}
		$strHtmlAction = '<input type="text" name="FIELDS['.$arRes['ID'].'][ACTION_SIZE]" size="3" value="'.intval($arRes['ACTION_SIZE']).'"> ';
		$strHtmlAction .= '<select name="FIELDS['.$arRes['ID'].'][ACTION_TYPE]">';
		foreach ($arPeriodTypeList as $strTypeID => $strTypeName)
		{
			$strHtmlAction .= '<option value="'.htmlspecialcharsbx($strTypeID).'" '.($strTypeID == $arRes['ACTION_TYPE'] ? 'selected' : '').'>'.htmlspecialcharsex($strTypeName).'</option>';
		}
		$strHtmlAction .= '</select>';
	}

	if (array_key_exists('COUNT', $arSelectFieldsMap))
	{
		if (intval($arRes['COUNT_SIZE']) == 0)
		{
			$strViewCount = '';
		}
		else
		{
			$strViewCount = str_replace('#TYPE#',htmlspecialcharsex($arPeriodTypeList[$arRes['COUNT_TYPE']]),GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_COUNT_TYPE')).'<br />'.str_replace('#SIZE#',$arRes['COUNT_SIZE'],GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_COUNT_SIZE'));
		}
		$strHtmlCount = '<input type="text" name="FIELDS['.$arRes['ID'].'][COUNT_SIZE]" size="3" value="'.intval($arRes['COUNT_SIZE']).'"> ';
		$strHtmlCount .= '<select name="FIELDS['.$arRes['ID'].'][COUNT_TYPE]">';
		foreach ($arPeriodTypeList as $strTypeID => $strTypeName)
		{
			$strHtmlCount .= '<option value="'.htmlspecialcharsbx($strTypeID).'" '.($strTypeID == $arRes['COUNT_TYPE'] ? 'selected' : '').'>'.htmlspecialcharsex($strTypeName).'</option>';
		}
		$strHtmlCount .= '</select>';
	}

	if (!$bReadOnly)
	{
		if (array_key_exists('SITE_ID', $arSelectFieldsMap))
		{
			$row->AddSelectField("SITE_ID", $arSiteList);
			$row->AddViewField('SITE_ID',$arSiteLinkList[$arRes['SITE_ID']]);
		}
		if (array_key_exists('NAME', $arSelectFieldsMap))
			$row->AddInputField("NAME", array("size"=>30));
		if (array_key_exists('ACTIVE', $arSelectFieldsMap))
			$row->AddCheckField("ACTIVE");
		if (array_key_exists('SORT', $arSelectFieldsMap))
			$row->AddInputField("SORT", array("size"=>3));
		if (array_key_exists('CURRENCY', $arSelectFieldsMap))
			$row->AddSelectField("CURRENCY", $arCurrencyList);

		if (array_key_exists('ACTIVE_FROM', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_FROM");
		if (array_key_exists('ACTIVE_TO', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_TO");
		if (array_key_exists('ACTION', $arSelectFieldsMap))
		{
			$row->AddViewField('ACTION',$strViewAction);
			$row->AddEditField('ACTION',$strHtmlAction);
		}

		if (array_key_exists('COUNT_FROM', $arSelectFieldsMap))
			$row->AddCalendarField("COUNT_FROM");
		if (array_key_exists('COUNT_TO', $arSelectFieldsMap))
			$row->AddCalendarField("COUNT_TO");
		if (array_key_exists('COUNT', $arSelectFieldsMap))
		{
			$row->AddViewField('COUNT',$strViewCount);
			$row->AddEditField('COUNT',$strHtmlCount);
		}

		if (array_key_exists('XML_ID', $arSelectFieldsMap))
			$row->AddInputField("XML_ID", array("size"=>20));
	}
	else
	{
		if (array_key_exists('SITE_ID', $arSelectFieldsMap))
			$row->AddViewField('SITE_ID',$arSiteLinkList[$arRes['SITE_ID']]);
		if (array_key_exists('NAME', $arSelectFieldsMap))
			$row->AddViewField("NAME", '<a href="/bitrix/admin/cat_discsave_edit.php?lang='.urlencode(LANGUAGE_ID).'&ID='.$arRes["ID"].'">'.htmlspecialcharsex($arRes['NAME']).'</a>');
		if (array_key_exists('ACTIVE', $arSelectFieldsMap))
			$row->AddCheckField("ACTIVE", false);
		if (array_key_exists('SORT', $arSelectFieldsMap))
			$row->AddInputField('SORT', false);

		if (array_key_exists('ACTIVE_FROM', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_FROM", false);
		if (array_key_exists('ACTIVE_TO', $arSelectFieldsMap))
			$row->AddCalendarField("ACTIVE_TO", false);
		if (array_key_exists('ACTION', $arSelectFieldsMap))
			$row->AddViewField('ACTION',$strViewAction);

		if (array_key_exists('COUNT_FROM', $arSelectFieldsMap))
			$row->AddCalendarField("COUNT_FROM", false);
		if (array_key_exists('COUNT_TO', $arSelectFieldsMap))
			$row->AddCalendarField("COUNT_TO", false);
		if (array_key_exists('COUNT', $arSelectFieldsMap))
			$row->AddViewField('COUNT',$strViewCount);

		if (array_key_exists('XML_ID', $arSelectFieldsMap))
			$row->AddInputField("XML_ID", false);

		if (array_key_exists('CURRENCY', $arSelectFieldsMap))
			$row->AddViewField("CURRENCY", $arRes['CURRENCY']);
	}

	$arActions = array();

	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => true,
		"TEXT" => GetMessage("BT_CAT_DISC_SAVE_ADM_CONT_EDIT"),
		"ACTION"=>$lAdmin->ActionRedirect("/bitrix/admin/cat_discsave_edit.php?ID=".$arRes['ID'].'&lang='.urlencode(LANGUAGE_ID))
	);
	if (!$bReadOnly)
	{
		$arActions[] = array(
			"ICON" => "copy",
			"DEFAULT" => false,
			"TEXT" => GetMessage("BT_CAT_DISC_SAVE_ADM_CONT_COPY"),
			"ACTION"=>$lAdmin->ActionRedirect("/bitrix/admin/cat_discsave_edit.php?ID=".$arRes['ID'].'&action=copy&lang='.urlencode(LANGUAGE_ID))
		);

		$arActions[] = array("SEPARATOR" => true);

		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage("BT_CAT_DISC_SAVE_ADM_CONT_DELETE"),
			"ACTION"=>"if(confirm('".GetMessage('BT_CAT_DISC_SAVE_ADM_CONT_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($arRes['ID'], "delete")
		);
	}

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsDiscSaves->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

if (!$bReadOnly)
{
	$lAdmin->AddGroupActionTable(array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	));
}

$aContext = array();
if (!$bReadOnly)
{
	$aContext = array(
		array(
			"TEXT"=>GetMessage("BT_CAT_DISC_SAVE_ADM_PAGECONT_ADD"),
			"LINK"=>"/bitrix/admin/cat_discsave_edit.php?lang=".urlencode(LANGUAGE_ID),
			"TITLE"=>GetMessage("BT_CAT_DISC_SAVE_ADM_PAGECONT_ADD_TITLE"),
			"ICON"=>"btn_new",
		),
	);
}
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("BT_CAT_DISC_SAVE_ADM_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_SITE_ID"),
		GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_NAME2"),
		GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_ACTIVE2"),
		GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_CURRENCY"),
	)
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><? echo "ID" ?>:</td>
	<td>
		<input type="text" name="find_id_from" size="10" value="<?echo htmlspecialcharsbx($find_id_from)?>">
			...
		<input type="text" name="find_id_to" size="10" value="<?echo htmlspecialcharsbx($find_id_to)?>">
	</td>
</tr>
<tr>
	<td><? echo GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_SITE_ID"); ?>:</td>
	<td><select name="find_site_id"><option value="" <? echo ($find_site_id == '' ? 'selected' : ''); ?>><? echo htmlspecialcharsex(GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_ALL_SITES')) ?></option><?
		foreach ($arSiteList as $strSiteID => $strSiteName)
		{
			?><option value="<? echo htmlspecialcharsbx($strSiteID); ?>" <? echo ($strSiteID == $find_site_id ? 'selected' : ''); ?>>(<? echo htmlspecialcharsex($strSiteID)?>) <? echo htmlspecialcharsex($strSiteName); ?></option><?
		}
	?></select></td>
</tr>
<tr>
	<td><? echo GetMessage("BT_CAT_DISC_SAVE_ADM_TITLE_NAME2")?>:</td>
	<td><input type="text" size="30" name="find_name" value="<? echo htmlspecialcharsbx($find_name); ?>"></td>
</tr>
<tr>
	<td><? echo GetMessage('BT_CAT_DISC_SAVE_ADM_TITLE_ACTIVE2') ?>:</td>
	<td><select name="find_active">
		<option value=""><? echo htmlspecialcharsex(GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_ACTIVE_ANY'))?></option>
		<option value="Y"<?if($find_active=="Y")echo " selected"?>><? echo htmlspecialcharsex(GetMessage("BT_CAT_DISC_SAVE_ADM_MESS_ACTIVE_YES"))?></option>
		<option value="N"<?if($find_active=="N")echo " selected"?>><? echo htmlspecialcharsex(GetMessage("BT_CAT_DISC_SAVE_ADM_MESS_ACTIVE_NO"))?></option>
		</select>
	</td>
</tr>
<tr>
	<td><? echo htmlspecialcharsex(GetMessage('BT_CAT_DISC_SAVE_ADM_TITLE_CURRENCY')); ?>:</td>
	<td><select name="find_currency"><option value="" <? echo ($find_currency == '' ? 'selected' : ''); ?>><? echo htmlspecialcharsex(GetMessage('BT_CAT_DISC_SAVE_ADM_MESS_CURRENCY_ANY')); ?></option>
	<?
	foreach ($arCurrencyList as $strCurrencyID => $strCurrencyName)
	{
		?><option value="<? echo htmlspecialcharsbx($strCurrencyID); ?>" <? echo ($strCurrencyID == $find_currency ? 'selected' : ''); ?>><? echo htmlspecialcharsex($strCurrencyName); ?></option><?
	}
	?>
	</select></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?></form><?

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

?>