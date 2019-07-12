<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock;
use Bitrix\Currency;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('iblock'))
	return;
$catalogIncluded = Loader::includeModule('catalog');


$IBLOCK_ID = $arCurrentValues['IBLOCK_ID'];
$arProperty = array();
if (intval($IBLOCK_ID) > 0) {
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'));
	while ($arr = $rsProp->Fetch()) {
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$iblocCatalogkExists = (!empty($arCurrentValues['CATALOG_IBLOCK_ID']) && (int)$arCurrentValues['CATALOG_IBLOCK_ID'] > 0);

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['CATALOG_IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['CATALOG_IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);

$arCatalogProperty = array();
$arCatalogProperty_N = array();
$arCatalogProperty_X = array();
$arCatalogProperty_F = array();
if ($iblocCatalogkExists)
{
	$propertyIterator = Iblock\PropertyTable::getList(array(
		'select' => array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE'),
		'filter' => array('=IBLOCK_ID' => $arCurrentValues['CATALOG_IBLOCK_ID'], '=ACTIVE' => 'Y'),
		'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
	));
	while ($property = $propertyIterator->fetch())
	{
		$propertyCode = (string)$property['CODE'];
		if ($propertyCode == '')
			$propertyCode = $property['ID'];
		$propertyName = '['.$propertyCode.'] '.$property['NAME'];
        
		if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE)
		{
			$arCatalogProperty[$propertyCode] = $propertyName;

			if ($property['MULTIPLE'] == 'Y')
				$arCatalogProperty_X[$propertyCode] = $propertyName;
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST)
				$arCatalogProperty_X[$propertyCode] = $propertyName;
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property['LINK_IBLOCK_ID'] > 0)
				$arCatalogProperty_X[$propertyCode] = $propertyName;
		}
		else
		{
			//if ($property['MULTIPLE'] == 'N')
				$arCatalogProperty_F[$propertyCode] = $propertyName;
		}

		if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_NUMBER)
			$arCatalogProperty_N[$propertyCode] = $propertyName;
	}
	unset($propertyCode, $propertyName, $property, $propertyIterator);
}
$arCatalogProperty_LNS = $arCatalogProperty;

$arUserFields_S = array("-"=>" ");
$arUserFields_F = array("-"=>" ");
if ($iblocCatalogkExists)
{
	$arUserFields = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_'.$arCurrentValues['CATALOG_IBLOCK_ID'].'_SECTION', 0, LANGUAGE_ID);
	foreach ($arUserFields as $FIELD_NAME => $arUserField)
	{
		$arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
		$arCatalogProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$FIELD_NAME.']'.$arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
		if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "string")
			$arUserFields_S[$FIELD_NAME] = $arCatalogProperty_UF[$FIELD_NAME];
		if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "file" && $arUserField['MULTIPLE'] == 'N')
			$arUserFields_F[$FIELD_NAME] = $arCatalogProperty_UF[$FIELD_NAME];
	}
	unset($arUserFields);
}

$offers = false;
$arCatalogProperty_Offers = array();
$arCatalogProperty_OffersWithoutFile = array();
if ($catalogIncluded && $iblocCatalogkExists)
{
	$offers = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['CATALOG_IBLOCK_ID']);
	if (!empty($offers))
	{
		$propertyIterator = Iblock\PropertyTable::getList(array(
			'select' => array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE'),
			'filter' => array('=IBLOCK_ID' => $offers['IBLOCK_ID'], '=ACTIVE' => 'Y', '!=ID' => $offers['SKU_PROPERTY_ID']),
			'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
		));
		while ($property = $propertyIterator->fetch())
		{
			$propertyCode = (string)$property['CODE'];
			if ($propertyCode == '')
				$propertyCode = $property['ID'];
			$propertyName = '['.$propertyCode.'] '.$property['NAME'];

			$arCatalogProperty_Offers[$propertyCode] = $propertyName;
			if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE)
				$arCatalogProperty_OffersWithoutFile[$propertyCode] = $propertyName;
		}
		unset($propertyCode, $propertyName, $property, $propertyIterator);
	}
}

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if ($catalogIncluded)
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
}
else
{
	$arPrice = $arCatalogProperty_N;
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arTemplateParameters = array(
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	'BRAND_PROP' => array(
		'NAME' => GetMessage('RS_SLINE.BRAND_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
        'DEFAULT' => '-',
	),
    "CATALOG_IBLOCK_TYPE" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("RS_SLINE.CATALOG_IBLOCK_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockType,
        "REFRESH" => "Y",
    ),
    "CATALOG_IBLOCK_ID" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("RS_SLINE.CATALOG_IBLOCK_ID"),
        "TYPE" => "LIST",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $arIBlock,
        "REFRESH" => "Y",
    ),
);

if (isset($arCurrentValues['CATALOG_IBLOCK_ID']) && (int)$arCurrentValues['CATALOG_IBLOCK_ID'] > 0)
{
	$arTemplateParameters["CATALOG_FILTER_NAME"] = array(
        "PARENT" => "FILTER_SETTINGS",
        "NAME" => GetMessage("RS_SLINE.CATALOG_FILTER_NAME"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
	);

    $arTemplateParameters["CATALOG_BRAND_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.CATALOG_BRAND_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_LNS),
		'DEFAULT' => '-',
	);

	$arTemplateParameters["CATALOG_PROPERTY_CODE"] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage("RS_SLINE.CATALOG_PROPERTY_CODE"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $arCatalogProperty_LNS,
	);
    
	$arTemplateParameters["RSFLYAWAY_PROP_MORE_PHOTO"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_F),
		'DEFAULT' => '-',
	);
    
	$arTemplateParameters["RSFLYAWAY_PROP_ARTICLE"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_LNS),
		'DEFAULT' => '-',
	);

	$arTemplateParameters["RSFLYAWAY_USE_FAVORITE"] = array(
		'NAME' => GetMessage('RS_SLINE.USE_LIKES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	);
	$arTemplateParameters["FILTER_PROP_SEARCH"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('FILTER_PROP_SEARCH'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_LNS),
		'DEFAULT' => '-',
        'MULTIPLE' => 'Y',
	);
	$arTemplateParameters["RSFLYAWAY_PROP_OFF_POPUP"] = array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_OFF_POPUP'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	);
	$arTemplateParameters["RSFLYAWAY_HIDE_BASKET_POPUP"] = array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.HIDE_BASKET_POPUP'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	);
	$arTemplateParameters["SORTER_USE_AJAX"] = array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_USE_AJAX'),
		'TYPE' => 'LIST',
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	);
	$arTemplateParameters["FILTER_USE_AJAX"] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => Loc::getMessage('RS.FLYAWAY.FILTER_USE_AJAX'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	);
	$arTemplateParameters["SHOW_SECTION_URL"] = array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_SECTION_URL'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	);
    
	$arTemplateParameters["PRICE_CODE"] = array(
        "PARENT" => "PRICES",
        "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arPrice,
	);
	$arTemplateParameters["USE_PRICE_COUNT"] = array(
        "PARENT" => "PRICES",
        "NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
	);
	$arTemplateParameters["SHOW_PRICE_COUNT"] = array(
        "PARENT" => "PRICES",
        "NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
        "TYPE" => "STRING",
        "DEFAULT" => "1"
	);
	$arTemplateParameters["PRICE_VAT_INCLUDE"] = array(
        "PARENT" => "PRICES",
        "NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
	);
	$arTemplateParameters["PRICE_VAT_SHOW_VALUE"] = array(
        "PARENT" => "PRICES",
        "NAME" => GetMessage("IBLOCK_VAT_SHOW_VALUE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
	);
	$arTemplateParameters["BASKET_URL"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("IBLOCK_BASKET_URL"),
        "TYPE" => "STRING",
        "DEFAULT" => "/personal/cart/",
	);
	$arTemplateParameters["ACTION_VARIABLE"] = array(
        "PARENT" => "ACTION_SETTINGS",
        "NAME"		=> GetMessage("IBLOCK_ACTION_VARIABLE"),
        "TYPE"		=> "STRING",
        "DEFAULT"	=> "action"
	);
	$arTemplateParameters["PRODUCT_ID_VARIABLE"] = array(
        "PARENT" => "ACTION_SETTINGS",
        "NAME"		=> GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
        "TYPE"		=> "STRING",
        "DEFAULT"	=> "id"
	);
	$arTemplateParameters["USE_PRODUCT_QUANTITY"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("CP_BC_USE_PRODUCT_QUANTITY"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
	);
	$arTemplateParameters["PRODUCT_QUANTITY_VARIABLE"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("CP_BC_PRODUCT_QUANTITY_VARIABLE"),
        "TYPE" => "STRING",
        "DEFAULT" => "quantity",
        "HIDDEN" => (isset($arCurrentValues['USE_PRODUCT_QUANTITY']) && $arCurrentValues['USE_PRODUCT_QUANTITY'] == 'Y' ? 'N' : 'Y')
	);
	$arTemplateParameters["ADD_PROPERTIES_TO_BASKET"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("CP_BC_ADD_PROPERTIES_TO_BASKET"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
        "REFRESH" => "Y"
	);
	$arTemplateParameters["PRODUCT_PROPS_VARIABLE"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("CP_BC_PRODUCT_PROPS_VARIABLE"),
        "TYPE" => "STRING",
        "DEFAULT" => "prop",
        "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	);
	$arTemplateParameters["PARTIAL_PRODUCT_PROPERTIES"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("CP_BC_PARTIAL_PRODUCT_PROPERTIES"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	);
	$arTemplateParameters["PRODUCT_PROPERTIES"] = array(
        "PARENT" => "BASKET",
        "NAME" => GetMessage("CP_BC_PRODUCT_PROPERTIES"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arCatalogProperty_X,
        "HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	);
	$arTemplateParameters["PRODUCT_SUBSCRIPTION"] = array(
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('CP_BC_TPL_PRODUCT_SUBSCRIPTION'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
	);
	$arTemplateParameters["CATALOG_TEMPLATE_AJAXID"] = array(
		'PARENT' => 'PAGER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.CATALOG_TEMPLATE_AJAXID'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'ajaxpages_catalog_identifier',
	);
	$arTemplateParameters["CATALOG_USE_AJAXPAGES"] = array(
		'PARENT' => 'PAGER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.CATALOG_USE_AJAXPAGES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	);
    
	$arTemplateParameters["USE_COMPARE"] = array(
        "PARENT" => "COMPARE_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_USE_COMPARE_EXT"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
	);
	$arTemplateParameters["USE_MAIN_ELEMENT_SECTION"] = array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BC_USE_MAIN_ELEMENT_SECTION"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
	);
	$arTemplateParameters["SECTION_BACKGROUND_IMAGE"] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage("CP_BC_BACKGROUND_IMAGE"),
        "TYPE" => "LIST",
        "DEFAULT" => "-",
        "MULTIPLE" => "N",
        "VALUES" => array_merge(array("-"=>" "), $arUserFields_F)
	);
	$arTemplateParameters["SECTION_ID_VARIABLE"] = array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME"		=> GetMessage("IBLOCK_SECTION_ID_VARIABLE"),
        "TYPE"		=> "STRING",
        "DEFAULT"	=> "SECTION_ID"
	);
	$arTemplateParameters["SHOW_DEACTIVATED"] = array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage('CP_BC_SHOW_DEACTIVATED'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
	);
	$arTemplateParameters["SET_LAST_MODIFIED"] = array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BC_SET_LAST_MODIFIED"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
	);
	$arTemplateParameters["ADD_SECTIONS_CHAIN"] = array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BC_ADD_SECTIONS_CHAIN"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
	);
}

if($arCurrentValues["USE_COMPARE"]=="Y")
{
	$arTemplateParameters["COMPARE_NAME"] = array(
		"PARENT" => "COMPARE_SETTINGS",
		"NAME" => GetMessage("IBLOCK_COMPARE_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "CATALOG_COMPARE_LIST"
	);
}

if ($catalogIncluded)
{
	$arTemplateParameters['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_EXT2'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'Y' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_HIDE'),
			'L' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_LAST'),
			'N' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_SHOW')
		),
		'ADDITIONAL_VALUES' => 'N'
	);

	$arTemplateParameters['CONVERT_CURRENCY'] = array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BC_CONVERT_CURRENCY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);

	if (isset($arCurrentValues['CONVERT_CURRENCY']) && $arCurrentValues['CONVERT_CURRENCY'] == 'Y')
	{
		$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('CP_BC_CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => Currency\CurrencyManager::getCurrencyList(),
			'DEFAULT' => Currency\CurrencyManager::getBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}

    $arTemplateParameters['SHOW_DISCOUNT_PERCENT'] = array(
		'PARENT' => 'PRICES',
		'NAME' => getMessage('RS_SLINE.SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	);
	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		'PARENT' => 'PRICES',
		'NAME' => getMessage('RS_SLINE.SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	);

	if (isset($arCurrentValues['USE_PRODUCT_QUANTITY']) && $arCurrentValues['USE_PRODUCT_QUANTITY'] === 'Y')
	{
		$arTemplateParameters['DETAIL_SHOW_BASIS_PRICE'] = array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_SHOW_BASIS_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
		);
	}
}

if (!empty($offers))
{
	$arTemplateParameters["CATALOG_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_LIST_OFFERS_FIELD_CODE"), "VISUAL");
	$arTemplateParameters["CATALOG_OFFERS_PROPERTY_CODE"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_LIST_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arCatalogProperty_Offers,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["CATALOG_OFFERS_LIMIT"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_LIST_OFFERS_LIMIT"),
		"TYPE" => "STRING",
		"DEFAULT" => 5,
	);
    $arTemplateParameters['RSFLYAWAY_PROP_SKU_MORE_PHOTO'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => GetMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'DEFAULT' => '-',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_SKU_ARTICLE'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => GetMessage('RS_SLINE.OFFER_ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'DEFAULT' => '-',
	);
}

$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADD_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_ADD_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_COMPARE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE_DEFAULT')
);
$arTemplateParameters['MESS_BTN_DETAIL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_DETAIL'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_DETAIL_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE_DEFAULT')
);

if (\Bitrix\Main\Loader::includeModule("redsign.devcom")) {
	$arTemplateParameters['RSFLYAWAY_SHOW_SORTER'] = array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_SORTER'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);

	if ($arCurrentValues['RSFLYAWAY_SHOW_SORTER'] == 'Y') {
		$arTemplateParameters['RSFLYAWAY_SORTER_SHOW_TEMPLATE'] = array(
			'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_SHOW_TEMPLATE'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
			'PARENT' => 'LIST_SETTINGS',
		);

		$arTemplateParameters['RSFLYAWAY_SORTER_SHOW_SORTING'] = array(
			'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_SHOW_SORTING'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
			'PARENT' => 'LIST_SETTINGS',
		);

		$arTemplateParameters['RSFLYAWAY_SORTER_SHOW_PAGE_COUNT'] = array(
			'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_SHOW_PAGE_COUNT'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
			'PARENT' => 'LIST_SETTINGS',
		);

		if ($arCurrentValues['RSFLYAWAY_SORTER_SHOW_TEMPLATE'] == 'Y') {
			$arTemplateParameters['RSFLYAWAY_SORTER_TEMPLATE_DEFAULT'] = array(
				'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_TEMPLATE_DEFAULT'),
				'TYPE' => 'STRING',
				'VALUE' => '',
				'DEFAULT' => 'showcase',
				'PARENT' => 'LIST_SETTINGS',
			);
		};

		$arTemplateParameters['OFFER_TREE_PROPS'] = array(
			'PARENT' => 'OFFERS_SETTINGS',
			'NAME' => getMessage('RS.FLYAWAY.OFFER_TREE_PROPS'),
			'TYPE' => 'LIST',
			'VALUES' => $listProp2['SNL'],
			'MULTIPLE' => 'Y',
			'DEFAULT' => '-',
		);

		$arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
			'PARENT' => 'OFFERS_SETTINGS',
			'NAME' => getMessage('RS.FLYAWAY.OFFER_TREE_COLOR_PROPS'),
			'TYPE' => 'LIST',
			'VALUES' =>  $listProp2['HL'],
			'MULTIPLE' => 'Y',
			'DEFAULT' => '-',
		);
	}
}