<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock;
use Bitrix\Currency;

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

$arPopupDetailVariable = array(
	'ON_IMAGE' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE_IMAGE'),
	'ON_LUPA' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE_LUPA'),
	'ON_NONE' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE_NONE'),
);

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arPriceFor = array(
	'-' => getMessage('RS_SLINE.UNDEFINED'),
	'products' => getMessage('RS_SLINE.FILTER_PRICES_GROUPED_FOR_PRIDUCTS'),
	'sku' => getMessage('RS_SLINE.FILTER_PRICES_GROUPED_FOR_SKU'),
);

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
	$arTemplateParameters["INSTANT_RELOAD"] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.INSTANT_RELOAD'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	);
	$arTemplateParameters["FILTER_SCROLL_PROPS"] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.FILTER_SCROLL_PROPS'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_LNS),
		'DEFAULT' => '-',
	);
	$arTemplateParameters["FILTER_SEARCH_PROPS"] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.FILTER_SEARCH_PROPS'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_LNS),
		'DEFAULT' => '-',
	);

	$arTemplateParameters["FILTER_PRICES_GROUPED"] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.FILTER_PRICES_GROUPED'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arPrice,
	);
	$arTemplateParameters["FILTER_PRICES_GROUPED_FOR"] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.FILTER_PRICES_GROUPED_FOR'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'DEFAULT' => 'products',
		'VALUES' => $arPriceFor,
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
	$arTemplateParameters["ICON_MEN_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ICON_MEN_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_X),
		'DEFAULT' => '-',
	);
	$arTemplateParameters["ICON_WOMEN_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ICON_WOMEN_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_X),
		'DEFAULT' => '-',
	);
	$arTemplateParameters["ICON_NOVELTY_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ICON_NOVELTY_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_X),
		'DEFAULT' => '-',
	);
	$arTemplateParameters["NOVELTY_TIME"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.NOVELTY_TIME'),
		'TYPE' => 'STRING',
		'DEFAULT' => '720',
	);
	$arTemplateParameters["ICON_DISCOUNT_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ICON_DISCOUNT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_X),
		'DEFAULT' => '-',
	);
	$arTemplateParameters["ICON_DEALS_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ICON_DEALS_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_X),
		'DEFAULT' => '-',
	);
	$arTemplateParameters["ADDITIONAL_PICT_PROP"] = array(
        "PARENT" => "VISUAL",
		'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_F),
		'DEFAULT' => '-',
	);
    /*
	$arTemplateParameters["ARTICLE_PROP"] = array(
        "PARENT" => "VISUAL",   
		'NAME' => getMessage('RS_SLINE.ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_X),
		'DEFAULT' => '-',
	);
    */
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
    
    /**/
	$arTemplateParameters["USE_FILTER"] = array(
        "PARENT" => "FILTER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_USE_FILTER"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
	);
	$arTemplateParameters["USE_COMPARE"] = array(
        "PARENT" => "COMPARE_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_USE_COMPARE_EXT"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
	);
	$arTemplateParameters["SECTION_COUNT_ELEMENTS"] = array(
        "PARENT" => "SECTIONS_SETTINGS",
        "NAME" => GetMessage('CP_BC_SECTION_COUNT_ELEMENTS'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
	);
	$arTemplateParameters["SECTION_TOP_DEPTH"] = array(
        "PARENT" => "SECTIONS_SETTINGS",
        "NAME" => GetMessage('CP_BC_SECTION_TOP_DEPTH'),
        "TYPE" => "STRING",
        "DEFAULT" => "2",
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
	$arTemplateParameters["USE_LIKES"] = array(
		'NAME' => getMessage('RS_SLINE.USE_LIKES'),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"VALUE" => "Y",
		"DEFAULT" =>"N",
		"REFRESH"=> "Y",
	);
    
    if ($arCurrentValues["USE_LIKES"] == "Y") {
        $arTemplateParameters["LIKES_COUNT_PROP"] = array(
            'NAME' => getMessage('RS_SLINE.LIKES_COUNT_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arCatalogProperty_LNS),
            'DEFAULT' => '-',
        );
    }

	$arTemplateParameters["POPUP_DETAIL_VARIABLE"] = array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.POPUP_DETAIL_VARIABLE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'N',
        'VALUES' => $arPopupDetailVariable,
        'REFRESH' => 'N',
	);
	$arTemplateParameters["ERROR_EMPTY_ITEMS"] = array(
        'PARENT' => 'VISUAL',
        'NAME' => getMessage('RS_SLINE.ERROR_EMPTY_ITEMS'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'N',
	);
	$arTemplateParameters["PREVIEW_TRUNCATE_LEN"] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage("RS_SLINE.PREVIEW_TRUNCATE_LEN"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
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
    $arTemplateParameters['OFFER_ADDITIONAL_PICT_PROP'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'DEFAULT' => '-',
	);
    /*
	$arTemplateParameters['OFFER_ARTICLE_PROP'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_ARTICLE_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'DEFAULT' => '-',
	);
    */
	$arTemplateParameters['OFFER_TREE_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_COLOR_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_TREE_BTN_PROPS'] = array(
		'PARENT' => 'OFFERS_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_TREE_BTN_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_FILTER_SCROLL_PROPS'] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_FILTER_SCROLL_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'MULTIPLE' => 'Y',
		'DEFAULT' => '-',
	);
	$arTemplateParameters['OFFER_FILTER_SEARCH_PROPS'] = array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => getMessage('RS_SLINE.OFFER_FILTER_SEARCH_PROPS'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty_Offers),
		'MULTIPLE' => 'Y',
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


$arTemplateParameters['USE_SHARE'] = array(
    'NAME' => getMessage('RS_SLINE.USE_SHARE'),
    "TYPE" => "CHECKBOX",
    "MULTIPLE" => "N",
    "VALUE" => "Y",
    "DEFAULT" =>"N",
    "REFRESH"=> "Y",
);

if ($arCurrentValues['USE_SHARE'] == 'Y') {

    $arSocialServices = array(
        'blogger' => getMessage('RS_SLINE.SOCIAL_SERVICES.BLOGGER'),
        'delicious' => getMessage('RS_SLINE.SOCIAL_SERVICES.DELICIOUS'),
        'digg' => getMessage('RS_SLINE.SOCIAL_SERVICES.DIGG'),
        'evernote' => getMessage('RS_SLINE.SOCIAL_SERVICES.EVERNOTE'),
        'facebook' => getMessage('RS_SLINE.SOCIAL_SERVICES.FACEBOOK'),
        'gplus' => getMessage('RS_SLINE.SOCIAL_SERVICES.GPLUS'),
        'linkedin' => getMessage('RS_SLINE.SOCIAL_SERVICES.LINKEDIN'),
        'lj' => getMessage('RS_SLINE.SOCIAL_SERVICES.LJ'),
        'moimir' => getMessage('RS_SLINE.SOCIAL_SERVICES.MOIMIR'),
        'odnoklassniki' => getMessage('RS_SLINE.SOCIAL_SERVICES.ODNOKLASSNIKI'),
        'pinterest' => getMessage('RS_SLINE.SOCIAL_SERVICES.PINTEREST'),
        'pocket' => getMessage('RS_SLINE.SOCIAL_SERVICES.POCKET'),
        'qzone' => getMessage('RS_SLINE.SOCIAL_SERVICES.QZONE'),
        'reddit' => getMessage('RS_SLINE.SOCIAL_SERVICES.REDDIT'),
        'renren' => getMessage('RS_SLINE.SOCIAL_SERVICES.RENREN'),
        'sinaWeibo ' => getMessage('RS_SLINE.SOCIAL_SERVICES.SINA_WEIBO'),
        'surfingbird' => getMessage('RS_SLINE.SOCIAL_SERVICES.SURFINGBIRD'),
        'telegram' => getMessage('RS_SLINE.SOCIAL_SERVICES.TELEGRAM'),
        'tencentWeibo' => getMessage('RS_SLINE.SOCIAL_SERVICES.TENCENT_WEIBO'),
        'tumblr' => getMessage('RS_SLINE.SOCIAL_SERVICES.TUMBLR'),
        'twitter' => getMessage('RS_SLINE.SOCIAL_SERVICES.TWITTER'),
        'viber' => getMessage('RS_SLINE.SOCIAL_SERVICES.VIBER'),
        'vkontakte' => getMessage('RS_SLINE.SOCIAL_SERVICES.VKONTAKTE'),
        'whatsapp' => getMessage('RS_SLINE.SOCIAL_SERVICES.WHATSAPP'),
    );

    $arSocialCopy = array(
        'first' => getMessage('RS_SLINE.SOCIAL_COPY.FIRST'),
        'last' => getMessage('RS_SLINE.SOCIAL_COPY.LAST'),
        'hidden' => getMessage('RS_SLINE.SOCIAL_COPY.HIDDEN'),
    );

    $arSocialSize = array(
        'm' => getMessage('RS_SLINE.SOCIAL_SIZE.M'),
        's' => getMessage('RS_SLINE.SOCIAL_SIZE.S'),
    );

	$arTemplateParameters['LIST_SOCIAL_SERVICES'] = array(
        'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.SOCIAL_SERVICES'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServices,
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	);

	$arTemplateParameters['DETAIL_SOCIAL_SERVICES'] = array(
        'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => getMessage('RS_SLINE.SOCIAL_SERVICES'),
		'TYPE' => 'LIST',
		'VALUES' => $arSocialServices,
		'MULTIPLE' => 'Y',
		'DEFAULT' => '',
		'ADDITIONAL_VALUES' => 'Y',
	);

	$arTemplateParameters['SOCIAL_COUNTER'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_COUNTER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);

	$arTemplateParameters['SOCIAL_COPY'] = array(
        'NAME' => getMessage('RS_SLINE.SOCIAL_COPY'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialCopy
	);

	$arTemplateParameters['SOCIAL_LIMIT'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_LIMIT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	);

	$arTemplateParameters['SOCIAL_SIZE'] = array(
		'NAME' => getMessage('RS_SLINE.SOCIAL_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => $arSocialSize
	);
}

// "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
// "OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
// "OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
// "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
// "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
// "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
// "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
// "OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],

// 'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
// 'COMPOSITE_FRAME' => 'Y',
//"SHOW_ALL_WO_SECTION" => "Y", // set smart.filter + INCLUDE_SUBSECTIONS=Y = bug
