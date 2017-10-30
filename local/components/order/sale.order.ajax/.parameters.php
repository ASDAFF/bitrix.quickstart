<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Catalog;
use Bitrix\Iblock;

if (!Loader::includeModule('sale'))
	return;

$arColumns = array(
	"PREVIEW_PICTURE" => GetMessage("SOA_PREVIEW_PICTURE"),
	"DETAIL_PICTURE" => GetMessage("SOA_DETAIL_PICTURE"),
	"PREVIEW_TEXT" => GetMessage("SOA_PREVIEW_TEXT"),
	"PROPS" => GetMessage("SOA_PROPS"),
	"NOTES" => GetMessage("SOA_PRICE_TYPE"),
	"DISCOUNT_PRICE_PERCENT_FORMATED" => GetMessage("SOA_DISCOUNT"),
	"WEIGHT_FORMATED" => GetMessage("SOA_WEIGHT"),
);

if (Loader::includeModule('catalog'))
{
	$arIblockIDs = array();
	$arIblockNames = array();
	$catalogIterator = Catalog\CatalogIblockTable::getList(array(
		'select' => array('IBLOCK_ID', 'NAME' => 'IBLOCK.NAME'),
		'order' => array('IBLOCK_ID' => 'ASC')
	));
	while ($catalog = $catalogIterator->fetch())
	{
		$catalog['IBLOCK_ID'] = (int)$catalog['IBLOCK_ID'];
		$arIblockIDs[] = $catalog['IBLOCK_ID'];
		$arIblockNames[$catalog['IBLOCK_ID']] = $catalog['NAME'];
	}
	unset($catalog, $catalogIterator);

	if (!empty($arIblockIDs))
	{
		$arProps = array();
		$propertyIterator = Iblock\PropertyTable::getList(array(
			'select' => array('ID', 'CODE', 'NAME', 'IBLOCK_ID'),
			'filter' => array('@IBLOCK_ID' => $arIblockIDs, '=ACTIVE' => 'Y', '!=XML_ID' => CIBlockPropertyTools::XML_SKU_LINK),
			'order' => array('IBLOCK_ID' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC')
		));
		while ($property = $propertyIterator->fetch())
		{
			$property['ID'] = (int)$property['ID'];
			$property['IBLOCK_ID'] = (int)$property['IBLOCK_ID'];
			$property['CODE'] = (string)$property['CODE'];
			if ($property['CODE'] == '')
				$property['CODE'] = $property['ID'];
			if (!isset($arProps[$property['CODE']]))
			{
				$arProps[$property['CODE']] = array(
					'CODE' => $property['CODE'],
					'TITLE' => $property['NAME'].' ['.$property['CODE'].']',
					'ID' => array($property['ID']),
					'IBLOCK_ID' => array($property['IBLOCK_ID'] => $property['IBLOCK_ID']),
					'IBLOCK_TITLE' => array($property['IBLOCK_ID'] => $arIblockNames[$property['IBLOCK_ID']]),
					'COUNT' => 1
				);
			}
			else
			{
				$arProps[$property['CODE']]['ID'][] = $property['ID'];
				$arProps[$property['CODE']]['IBLOCK_ID'][$property['IBLOCK_ID']] = $property['IBLOCK_ID'];
				if ($arProps[$property['CODE']]['COUNT'] < 2)
					$arProps[$property['CODE']]['IBLOCK_TITLE'][$property['IBLOCK_ID']] = $arIblockNames[$property['IBLOCK_ID']];
				$arProps[$property['CODE']]['COUNT']++;
			}
		}
		unset($property, $propertyIterator, $arIblockNames, $arIblockIDs);

		$propList = array();
		foreach ($arProps as &$property)
		{
			$iblockList = '';
			if ($property['COUNT'] > 1)
			{
				$iblockList = ($property['COUNT'] > 2 ? ' ( ... )' : ' ('.implode(', ', $property['IBLOCK_TITLE']).')');
			}
			$propList['PROPERTY_'.$property['CODE']] = $property['TITLE'].$iblockList;
		}
		unset($property, $arProps);

		if (!empty($propList))
			$arColumns = array_merge($arColumns, $propList);
		unset($propList);
	}
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"PATH_TO_BASKET" => array(
			"NAME" => GetMessage("SOA_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "basket.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_PERSONAL" => array(
			"NAME" => GetMessage("SOA_PATH_TO_PERSONAL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "index.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_PAYMENT" => array(
			"NAME" => GetMessage("SOA_PATH_TO_PAYMENT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "payment.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_AUTH" => array(
			"NAME" => GetMessage("SOA_PATH_TO_AUTH"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/auth/",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PAY_FROM_ACCOUNT" => array(
			"NAME"=>GetMessage("SOA_ALLOW_PAY_FROM_ACCOUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"Y",
			"PARENT" => "BASE",
		),
		"ONLY_FULL_PAY_FROM_ACCOUNT" => array(
			"NAME"=>GetMessage("SOA_ONLY_FULL_PAY_FROM_ACCOUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"COUNT_DELIVERY_TAX" => array(
			"NAME"=>GetMessage("SOA_COUNT_DELIVERY_TAX"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"ALLOW_AUTO_REGISTER" => array(
			"NAME"=>GetMessage("SOA_ALLOW_AUTO_REGISTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"SEND_NEW_USER_NOTIFY" => array(
			"NAME"=>GetMessage("SOA_SEND_NEW_USER_NOTIFY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"Y",
			"PARENT" => "BASE",
		),
		"DELIVERY_NO_AJAX" => array(
			"NAME" => GetMessage("SOA_DELIVERY_NO_AJAX"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
		),
		"DELIVERY_NO_SESSION" => array(
			"NAME" => GetMessage("SOA_DELIVERY_NO_SESSION"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
		),
		"TEMPLATE_LOCATION" => array(
			"NAME"=>GetMessage("SBB_TEMPLATE_LOCATION"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"N",
			"VALUES"=>array(
				".default" => GetMessage("SBB_TMP_DEFAULT"),
				"popup" => GetMessage("SBB_TMP_POPUP")
			),
			"DEFAULT"=>".default",
			"COLS"=>25,
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		),
		"DELIVERY_TO_PAYSYSTEM" => array(
			"NAME" => GetMessage("SBB_DELIVERY_PAYSYSTEM"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
				"d2p" => GetMessage("SBB_TITLE_PD"),
				"p2d" => GetMessage("SBB_TITLE_DP")
			),
			"PARENT" => "BASE",
		),
		"SET_TITLE" => array(),
		"USE_PREPAYMENT" => array(
			"NAME" => GetMessage('SBB_USE_PREPAYMENT'),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		),
		"DISABLE_BASKET_REDIRECT" => array(
			"NAME" => GetMessage('SOA_DISABLE_BASKET_REDIRECT'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"PRODUCT_COLUMNS" => array(
			"NAME" => GetMessage("SOA_PRODUCT_COLUMNS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"COLS" => 25,
			"SIZE" => 7,
			"VALUES" => $arColumns,
			"DEFAULT" => array(),
			"ADDITIONAL_VALUES" => "N",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	)
);

$dbPerson = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"));
while($arPerson = $dbPerson->GetNext())
{
	$arPers2Prop = array("" => GetMessage("SOA_SHOW_ALL"));
	$bProp = false;
	$dbProp = CSaleOrderProps::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("PERSON_TYPE_ID" => $arPerson["ID"]));
	while($arProp = $dbProp -> Fetch())
	{
		$arPers2Prop[$arProp["ID"]] = $arProp["NAME"];
		$bProp = true;
	}

	if($bProp)
	{
		$arComponentParameters["PARAMETERS"]["PROP_".$arPerson["ID"]] =  array(
			"NAME" => GetMessage("SOA_PROPS_NOT_SHOW")." \"".$arPerson["NAME"]."\" (".$arPerson["LID"].")",
			"TYPE"=>"LIST", "MULTIPLE"=>"Y",
			"VALUES" => $arPers2Prop,
			"DEFAULT"=>"",
			"COLS"=>25,
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		);
	}
}
unset($arPerson, $dbPerson);