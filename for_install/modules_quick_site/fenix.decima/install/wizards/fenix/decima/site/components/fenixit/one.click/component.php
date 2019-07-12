<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule('iblock')
	|| !CModule::IncludeModule('catalog'))
	return;
if (intval($arParams['IBLOCK_ID']) < 1)
  return;
if (intval($arParams['PRICE_ID']) < 1)
   return;

$arParams['INSERT_ELEMENT'] = trim($arParams['INSERT_ELEMENT']);
if (empty($arParams['INSERT_ELEMENT'])) {
	$arParams['INSERT_ELEMENT'] = 11 < intval(substr(SM_VERSION, 0, 2))? 
		'#ocb_intaro' : '.catalog-detail-buttons';
} else {
	$first_char = substr($arParams['INSERT_ELEMENT'], 0, 1);
	if ($first_char != '#' && $first_char != '.')
		$arParams['INSERT_ELEMENT'] = '#' . $arParams['INSERT_ELEMENT'];
}

if (intval($arParams['CACHE_TIME']) < 0)
	$arParams['CACHE_TIME'] = 864000;
if ($arParams['SEF_FOLDERIX'] == '')
	$arParams['SEF_FOLDERIX'] = '/catalog/';

if (strlen($arParams['DEFAULT_CURRENCY']) != 3)
  $arParams['DEFAULT_CURRENCY'] = COption::GetOptionString('sale', 'default_currency', 'RUB');

$arParams['USE_SKU'] = $arParams['USE_SKU'] == 'Y';
$arParams['USE_JQUERY'] = $arParams['USE_JQUERY'] == 'Y';
if (empty($arParams['ORDER_FIELDS']))
	$arParams['ORDER_FIELDS'] = array('FIO', 'PHONE', 'EMAIL');
if (empty($arParams['REQUIRED_ORDER_FIELDS']))
	$arParams['REQUIRED_ORDER_FIELDS'] = array('FIO', 'PHONE');
if (!empty($arParams['DUPLICATE_LETTER_TO_EMAILS']))
	foreach ($arParams['DUPLICATE_LETTER_TO_EMAILS'] as $item)
		$arParams['DUB'] .= $item;
$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);
$arParams['USE_DEBUG_MESSAGES'] = $arParams['USE_DEBUG_MESSAGES'] == 'Y';
$arParams['USE_QUANTITY'] = $arParams['USE_QUANTITY'] == 'Y';
$arParams['USE_ANTISPAM'] = $arParams['USE_ANTISPAM'] == 'Y';
// using current uri as cache parameter
$arParams['PAGE_URI'] = $APPLICATION->GetCurPage();

if ($this->StartResultCache()) {
	$arResult['ERRORS'] = array();

	

	if (empty($arResult['ERRORS'])) {
		if (!$arParams['USE_SKU'] && $arParams['USE_QUANTITY']) {
			$productData = CCatalogProduct::GetById($arParams['ELEMENT_ID']);
			if ($productData['QUANTITY'] < 1) {
				$this->AbortResultCache();
				if ($arParams['USE_DEBUG_MESSAGES'])
					$arResult['ERRORS'][] = GetMessage(
						'ICRM_COMPONENT_ERROR_5',
						array('#ID#' => $arParams['ELEMENT_ID'])
					);
				else
					return;
			}
		}

		if ($arParams['USE_SKU'] && $arParams['ELEMENT_ID'] > 0 && !empty($arParams['SKU_PROPERTIES_CODES'])) {
			if (empty($arParams['ELEMENT_NAME'])) {
				$dbItemData = CIBlockElement::GetByID($arParams['ELEMENT_ID']);
				if ($arItemData = $dbItemData->GetNext())
					$arParams['ELEMENT_NAME'] = $arItemData['NAME'];
			}

			function GenerateOfferString(&$offerData, &$offerProps, &$elementName) {
				$string = $elementName . '(';
				foreach ($offerProps as $cur_prop) {
					if (array_key_exists($cur_prop, $offerData['PROPERTIES'])) {
						$curPropData = $offerData['PROPERTIES'][$cur_prop];
						switch($curPropData['PROPERTY_TYPE']) {
							case 'S':
							case 'L':
								if (!empty($curPropData['VALUE']))
									$string .= $curPropData['NAME'] . ': ' . $curPropData['VALUE'] . ', ';
								break;
							case 'E':
							case 'G':
								// TODO: selecting props from elements or sections can be added
								break;
							case 'F':
							default:
								break;
						}
						
					}
				}
				$string = substr($string, 0, strlen($string)-2) . ')';
				return $string;
			}

			$arResult['SKU_PROPERTIES_STRING'] = implode('|', $arParams['SKU_PROPERTIES_CODES']);
			$arPrice = CCatalogGroup::GetById($arParams['PRICE_ID']);
			$arPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], array($arPrice['NAME']));
			$arOffers = CIBlockPriceTools::GetOffersArray(
				$arParams['IBLOCK_ID'],
				$arParams['ELEMENT_ID'],
				array('ID' => 'DESC'),
				array(),
				$arParams['SKU_PROPERTIES_CODES'],
				0 < intval($arParams['SKU_COUNT'])? intval($arParams['SKU_COUNT']): false,
				$arPrices,
				false
			);
			foreach($arOffers as $arOffer)
				if (!$arParams['USE_QUANTITY'] || 0 < $arOffer['CATALOG_QUANTITY'])
					$arResult['OFFERS'][$arOffer['ID']] = GenerateOfferString($arOffer, $arParams['SKU_PROPERTIES_CODES'], $arParams['ELEMENT_NAME']);
			unset($arOffers);
			if (sizeof($arResult['OFFERS']) < 1) {
				$this->AbortResultCache();
				if ($arParams['USE_DEBUG_MESSAGES'])
					$arResult['ERRORS'][] = GetMessage(
						'ICRM_COMPONENT_ERROR_6',
						array('#ID#' => $arParams['ELEMENT_ID'])
					);
				else
					return;
			}
		}

		$arResult['USER_PHONE'] = '';
		if ($USER->IsAuthorized()) {
			if (!isset($_SESSION['OCB_USER_PHONE'])) {
				global $USER;
				$dbUser = $USER->GetByID($USER->GetID());
				$arUser = $dbUser->Fetch();
				$_SESSION['OCB_USER_PHONE'] = $arUser['PERSONAL_PHONE'];
			}
			$arResult['USER_PHONE'] = $_SESSION['OCB_USER_PHONE'];
		}

		$dir_path = str_replace('\\', '/', dirname(__FILE__));
		$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
		$arResult['SCRIPT_PATH'] = substr($dir_path, strlen($doc_root));
	}

	$this->IncludeComponentTemplate();
}?>
