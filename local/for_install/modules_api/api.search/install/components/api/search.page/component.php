<?
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 * @var CCacheManager    $CACHE_MANAGER
 */

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();


$arResultModules = array(
	 'api.search' => CModule::IncludeModule('api.search'),
	 'iblock'     => CModule::IncludeModule('iblock'),
	 'catalog'    => CModule::IncludeModule('catalog'),
);

if(!$arResultModules['api.search']) {
	ShowError(GetMessage('API_SEARCH_MODULE_ERROR'));
	return;
}

if(!$arResultModules['iblock']) {
	ShowError(GetMessage('API_SEARCH_IBLOCK_ERROR'));
	return;
}

$isAjax             = $_REQUEST['API_SEARCH_PAGE_AJAX'] == 'Y';
$arResult['isAjax'] = ($isAjax ? true : false);

global $MESS;
CApiSearch::incComponentLang($this);

$query         = preg_replace('/\s+/', ' ', trim(strip_tags($_REQUEST['q'])));
$arResult['q'] = $query;

if($isAjax)
	CUtil::decodeURIComponent($query);


$SEARCH_MODE = $arParams['SEARCH_MODE'];
if($_REQUEST['sm'])
	$SEARCH_MODE = ToUpper($_REQUEST['sm']);


//$MASK = ($SEARCH_MODE == 'EXACT' ? '%' : '?');
$MASK = '?';

CApiSearch::setMode($SEARCH_MODE);
$arResult['query'] = CApiSearch::getWords($query);



//==============================================================================
//                                  $arParams
//==============================================================================
//$arParams['RESULT_PAGE']    = trim($arParams['RESULT_PAGE']) ? trim($arParams['RESULT_PAGE']) : '/search/';
//$arParams['RESULT_URL']     = CHTTP::urlAddParams($arParams['RESULT_PAGE'], array('q' => $query), array('encode' => true));

$arParams['IBLOCK_ID'] = (array)$arParams['IBLOCK_ID'];
foreach($arParams['IBLOCK_ID'] as $k => $v)
	if($v == "")
		unset($arParams['IBLOCK_ID'][ $k ]);


$arParams['USE_TITLE_RANK'] = ($arParams['USE_TITLE_RANK'] == 'Y');

$arParams['SORT_BY1']    = trim($arParams['SORT_BY1']);
$arParams['SORT_ORDER1'] = trim($arParams['SORT_ORDER1']);
$arParams['SORT_BY2']    = trim($arParams['SORT_BY2']);
$arParams['SORT_ORDER2'] = trim($arParams['SORT_ORDER2']);
$arParams['SORT_BY3']    = trim($arParams['SORT_BY3']);
$arParams['SORT_ORDER3'] = trim($arParams['SORT_ORDER3']);


//VISUAL LANGS
$arParams['INPUT_PLACEHOLDER'] = ($MESS['API_SEARCH_PAGE_INPUT_PLACEHOLDER'] ? $MESS['API_SEARCH_PAGE_INPUT_PLACEHOLDER'] : trim($arParams['INPUT_PLACEHOLDER']));
$arParams['BUTTON_TEXT']       = ($MESS['API_SEARCH_PAGE_BUTTON_TEXT'] ? $MESS['API_SEARCH_PAGE_BUTTON_TEXT'] : trim($arParams['BUTTON_TEXT']));
$arParams['RESULT_NOT_FOUND']  = ($MESS['API_SEARCH_PAGE_RESULT_NOT_FOUND'] ? $MESS['API_SEARCH_PAGE_RESULT_NOT_FOUND'] : trim($arParams['RESULT_NOT_FOUND']));
$arParams['MORE_BUTTON_TEXT']  = htmlspecialcharsback($arParams['MORE_BUTTON_TEXT']);
$arParams['MORE_BUTTON_CLASS'] = htmlspecialcharsback($arParams['MORE_BUTTON_CLASS']);

//VISUAL OTHER
$arParams['THEME']           = ($arParams['THEME'] ? trim($arParams['THEME']) : 'list');
$arParams['TRUNCATE_LENGTH'] = intval($arParams['TRUNCATE_LENGTH']);
$arParams['PICTURE']         = (array)$arParams['PICTURE'];
$arParams['RESIZE_PICTURE']  = ($arParams['RESIZE_PICTURE'] ? explode('x', $arParams['RESIZE_PICTURE']) : array());
$arParams['PICTURE_WIDTH']   = $arParams['RESIZE_PICTURE'][0];
$arParams['PICTURE_HEIGHT']  = ($arParams['RESIZE_PICTURE'][1] ? $arParams['RESIZE_PICTURE'][1] : (int)$arParams['RESIZE_PICTURE'][0] * 2);
foreach($arParams['PICTURE'] as $key => $val)
	if(!$val)
		unset($arParams['PICTURE'][ $key ]);

$issetPreviewPicture = false;
$issetDetailPicture  = false;
if($arParams['PICTURE']) {
	$paramsPicture = array_flip($arParams['PICTURE']);
	if(is_set($paramsPicture, 'PREVIEW_PICTURE'))
		$issetPreviewPicture = true;

	if(is_set($paramsPicture, 'DETAIL_PICTURE'))
		$issetDetailPicture = true;

	unset($paramsPicture);
}


//ADDITIONAL_SETTINGS
$arParams['INCLUDE_CSS']    = $arParams['INCLUDE_CSS'] == 'Y';
$arParams['INCLUDE_JQUERY'] = $arParams['INCLUDE_JQUERY'] == 'Y';
if($arParams['INCLUDE_JQUERY'])
	CJSCore::Init(array('jquery'));


//PRICES
$arParams['PRICE_CODE']          = (array)$arParams['PRICE_CODE'];
$arParams['PRICE_EXT']           = ($arParams['PRICE_EXT'] == 'Y');
$arParams['CURRENCY_ID']         = ($arParams['CURRENCY_ID'] ? $arParams['CURRENCY_ID'] : 'RUB');
$arParams['PRICE_VAT_INCLUDE']   = $arParams['PRICE_VAT_INCLUDE'] == 'Y';
$arParams['CONVERT_CURRENCY']    = $arParams['CONVERT_CURRENCY'] == 'Y';
$arParams['USE_CURRENCY_SYMBOL'] = $arParams['USE_CURRENCY_SYMBOL'] == 'Y';
$arParams['CURRENCY_SYMBOL']     = ($arParams['USE_CURRENCY_SYMBOL'] ? htmlspecialcharsback($arParams['CURRENCY_SYMBOL']) : '');

foreach($arParams['PRICE_CODE'] as $key => $val)
	if(!$val)
		unset($arParams['PRICE_CODE'][ $key ]);


//$arrFilter
$arParams['FILTER_NAME'] = isset($arParams['FILTER_NAME']) ? $arParams['FILTER_NAME'] : 'arrFilter';
if(empty($arParams['FILTER_NAME']) || !preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['FILTER_NAME'])) {
	$arrFilter = array();
}
else {
	global ${$arParams['FILTER_NAME']};
	$arrFilter = ${$arParams['FILTER_NAME']};

	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams['CACHE_FILTER'] = $arParams['CACHE_FILTER'] == 'Y';
if(!$arParams['CACHE_FILTER'] && count($arrFilter) > 0)
	$arParams['CACHE_TIME'] = 0;

//$arNavParams
CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');
$arParams['ITEMS_LIMIT']                     = $arParams['ITEMS_LIMIT'] ? intval($arParams['ITEMS_LIMIT']) : 15;
$arParams['RESULT_LIMIT']                    = $arParams['RESULT_LIMIT'] ? intval($arParams['RESULT_LIMIT']) : 100;
$arParams['CACHE_TIME']                      = $arParams['CACHE_TIME'] >= 0 ? $arParams['CACHE_TIME'] : 86400;
$arParams['DISPLAY_TOP_PAGER']               = $arParams['DISPLAY_TOP_PAGER'] == 'Y';
$arParams['DISPLAY_BOTTOM_PAGER']            = $arParams['DISPLAY_BOTTOM_PAGER'] == 'Y';
$arParams['PAGER_DESC_NUMBERING']            = $arParams['PAGER_DESC_NUMBERING'] == 'Y';
$arParams['PAGER_SHOW_ALWAYS']               = $arParams['PAGER_SHOW_ALWAYS'] == 'Y';
$arParams['PAGER_SHOW_ALL']                  = $arParams['PAGER_SHOW_ALL'] == 'Y';
$arParams['PAGER_DESC_NUMBERING_CACHE_TIME'] = $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'] >= 0 ? $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'] : 36000;

$arNavParams = array();
if($arParams['DISPLAY_TOP_PAGER'] || $arParams['DISPLAY_BOTTOM_PAGER']) {
	$arNavParams  = array(
		 'nPageSize'          => $arParams['ITEMS_LIMIT'],
		 'bDescPageNumbering' => $arParams['PAGER_DESC_NUMBERING'],
		 'bShowAll'           => $arParams['PAGER_SHOW_ALL'],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation['PAGEN'] == 0 && $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'] > 0)
		$arParams['CACHE_TIME'] = $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'];
}
else {
	$arNavParams  = array(
		 'nTopCount'          => $arParams['ITEMS_LIMIT'],
		 'bDescPageNumbering' => $arParams['PAGER_DESC_NUMBERING'],
	);
	$arNavigation = false;
}

//==============================================================================
//                               $arResult
//==============================================================================
$arResult['COUNT_ITEMS']       = 0;
$arResult['COUNT_SECTIONS']    = 0;
$arResult['COUNT_RESULT']      = '';
$arResult['CATEGORIES']        = array();
$arResult['ELEMENTS']          = array();
$arResult['PRICES']            = array();
$arResult['SECTION_ID']        = array();
$arResult['SECTIONS']          = array();
$arResult['ITEMS']             = array();
$arResult['OFFERS_ELEMENT_ID'] = array();



$arElementLink = array();
$arIblockId    = $arParams['IBLOCK_ID'];
if($arIblockId && strlen($arResult['query']) >= API_SEARCH_CHAR_LENGTH) {
	$obParser = new CTextParser();

	//Prepare prices
	$arConvertParams = array();
	if($arParams['PRICE_CODE']) {
		if(CModule::IncludeModule('currency')) {
			$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices(0, $arParams["PRICE_CODE"]);

			$arConvertParams['CURRENCY_ID'] = CCurrency::GetBaseCurrency();
			if($arParams['CONVERT_CURRENCY']) {
				if($arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID'])) {
					$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
				}
			}
		}
	}

	$arSort = array();
	if($arParams['SORT_BY1'] && $arParams['SORT_ORDER1'])
		$arSort[ $arParams['SORT_BY1'] ] = $arParams['SORT_ORDER1'];
	if($arParams['SORT_BY2'] && $arParams['SORT_ORDER2'])
		$arSort[ $arParams['SORT_BY2'] ] = $arParams['SORT_ORDER2'];
	if($arParams['SORT_BY3'] && $arParams['SORT_ORDER3'])
		$arSort[ $arParams['SORT_BY3'] ] = $arParams['SORT_ORDER3'];


	$maxItemsInCat = $arParams['RESULT_LIMIT'];
	$countIblockId = count($arIblockId);

	if($countIblockId > 1)
		$maxItemsInCat = round($arParams['RESULT_LIMIT'] / count($arIblockId), 0, PHP_ROUND_HALF_DOWN);

	$lastItemsInCat = 0;
	foreach($arIblockId as $iblockID) {

		$arResult['CATEGORIES'][ $iblockID ] = array(
			 'TITLE' => htmlspecialcharsback(trim($arParams[ 'IBLOCK_' . $iblockID . '_TITLE' ])),
			 'ITEMS' => array(),
		);

		$arFilter = array(
			 'IBLOCK_ID'             => $iblockID,
			 'IBLOCK_LID'            => SITE_ID,
			 'ACTIVE'                => 'Y',
			 'SECTION_ACTIVE'        => 'Y',
			 'SECTION_GLOBAL_ACTIVE' => 'Y',
			 'SECTION_ID'            => false,
		);

		//Sections for filter
		if($arIblockSection = $arParams[ 'IBLOCK_' . $iblockID . '_SECTION' ]) {
			foreach($arIblockSection as $sectionId) {
				if($sectionId)
					$arFilter['SECTION_ID'][] = $sectionId;
			}

			if($arFilter['SECTION_ID'])
				$arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
		}

		$bShowOffers  = ($arParams[ 'IBLOCK_' . $iblockID . '_SHOW_OFFERS' ] == 'Y');
		$bShowSection = ($arParams[ 'IBLOCK_' . $iblockID . '_SHOW_SECTION' ] == 'Y');

		//Prepare propertyList
		$propertyList   = array();
		$arShowProperty = $arParams[ 'IBLOCK_' . $iblockID . '_SHOW_PROPERTY' ];
		if($arShowProperty) {
			foreach($arShowProperty as $propCode) {
				if($propCode)
					$propertyList[] = $propCode;
			}
		}

		$brandProperty = $arParams[ 'IBLOCK_' . $iblockID . '_SHOW_BRAND' ];
		if($brandProperty)
			$propertyList[] = $brandProperty;


		$bGetProperties = !empty($propertyList);



		//Prepare iblock filter
		$preg_replace = '';
		$strRegex     = $arParams[ '~IBLOCK_' . $iblockID . '_REGEX' ];
		if($strRegex && preg_match($strRegex, $arResult['query']))
			$preg_replace = preg_replace($strRegex, '%\\1%', $arResult['query']);


		$arIblockProperty = $arParams[ 'IBLOCK_' . $iblockID . '_PROPERTY' ];
		$arIblockField    = $arParams[ 'IBLOCK_' . $iblockID . '_FIELD' ];

		if($arIblockProperty || $preg_replace || count($arIblockField) > 1) {
			//MAX 30 items
			$arSubFilter = array('LOGIC' => 'OR');

			//Fields
			foreach($arIblockField as $fieldId) {
				if($fieldId) {
					$arSubFilter[ $MASK . $fieldId ] = $arResult['query'];

					if($preg_replace)
						$arSubFilter[ '?' . $fieldId ] = $preg_replace;
				}
			}


			//Properties
			foreach($arIblockProperty as $propertyId) {
				if($propertyId) {
					$arSubFilter[ $MASK . 'PROPERTY_' . $propertyId ] = $arResult['query'];

					if($preg_replace)
						$arSubFilter[ '?' . 'PROPERTY_' . $propertyId ] = $preg_replace;
				}
			}

			$arFilter[] = $arSubFilter;
		}
		else {
			if($arIblockField) {
				foreach($arIblockField as $fieldId) {
					if($fieldId) {
						$arFilter[ $MASK . $fieldId ] = $arResult['query'];

						if($preg_replace)
							$arFilter[ '?' . $fieldId ] = $preg_replace;
					}
				}
			}
		}


		//Global filter for bitrix:catalog.section
		$GLOBALS['apiSearchFilter'] = $arFilter;


		//$arSelect
		$arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL');

		if($arParams['PICTURE'])
			$arSelect = array_merge($arSelect, $arParams['PICTURE']);


		$arShowFields = $arParams[ 'IBLOCK_' . $iblockID . '_SHOW_FIELD' ];
		if($arShowFields) {
			foreach($arShowFields as $fieldId)
				$arSelect[] = $fieldId;
		}

		if($arResult['PRICES']) {
			foreach($arResult['PRICES'] as $value) {
				$arSelect[] = $value['SELECT'];
				//$arFilter[ 'CATALOG_SHOP_QUANTITY_' . $value['ID'] ] = 1;
			}
		}

		$arSelect = array_unique($arSelect);


		//OFFERS
		$offers       = array();
		$iblockOffers = array();
		if($arResultModules['catalog']) {
			$offers       = CCatalogSku::GetInfoByOfferIBlock($iblockID);
			$iblockOffers = CCatalogSku::GetInfoByProductIBlock($iblockID);
		}

		//$nTopCount
		$nTopCount = $maxItemsInCat;
		if($countIblockId > 1 && $lastItemsInCat != $maxItemsInCat) {
			if($lastItemsInCat < $maxItemsInCat)
				$nTopCount = $maxItemsInCat + ($maxItemsInCat - $lastItemsInCat);
		}
		$lastItemsInCat = 0;

		$dbRes = CIBlockElement::GetList($arSort, array_merge($arFilter, $arrFilter), false, array('nTopCount' => $nTopCount), $arSelect);

		$sectionUrl = trim($arParams['IBLOCK_'. $iblockID .'_SECTION_URL']);
		$detailUrl = trim($arParams['IBLOCK_'. $iblockID .'_DETAIL_URL']);
		if(strlen($detailUrl)>0 || strlen($sectionUrl)>0)
			$dbRes->SetUrlTemplates($detailUrl,$sectionUrl);

		while($arItem = $dbRes->GetNext(true, false)) {
			$lastItemsInCat++;

			//Название товара для вывода с подсветкой ключевых слов
			$arItem['FAKE_NAME'] = CApiSearch::replaceName($arItem['NAME'], $query, $arResult['query']);

			//Раздел товара для вывода
			if($arItem['IBLOCK_SECTION_ID']) {
				if($bShowSection)
					$arResult['SECTION_ID'][ $arItem['IBLOCK_SECTION_ID'] ] = $arItem['IBLOCK_SECTION_ID'];
				else
					unset($arItem['IBLOCK_SECTION_ID']);
			}



			//Изображение товара для вывода
			$arItem['PICTURE'] = array();
			$picture           = false;
			if($arParams['PICTURE']) {
				if($issetPreviewPicture && $arItem['PREVIEW_PICTURE'])
					$picture = $arItem['PREVIEW_PICTURE'];
				elseif($issetDetailPicture && $arItem['DETAIL_PICTURE'])
					$picture = $arItem['DETAIL_PICTURE'];
			}

			if($picture) {
				if($arParams['RESIZE_PICTURE']) {
					$arItem['PICTURE'] = CApiSearch::ResizeImageGet(
						 $picture,
						 $arParams['PICTURE_WIDTH'],
						 $arParams['PICTURE_HEIGHT']
					);
				}
				else {
					$arItem['PICTURE'] = CFile::GetFileArray($picture);
				}
			}


			//Описание товара для вывода
			$arItem['DESCRIPTION'] = array();
			if($arShowFields) {
				$descValue = '';
				$descField = '';

				if($arItem['PREVIEW_TEXT'] && is_set(array_flip($arShowFields), 'PREVIEW_TEXT')) {
					$descField = 'PREVIEW_TEXT';
					$descValue = $arItem['PREVIEW_TEXT'];
				}
				elseif($arItem['DETAIL_TEXT'] && is_set(array_flip($arShowFields), 'DETAIL_TEXT')) {
					$descField = 'DETAIL_TEXT';
					$descValue = $arItem['DETAIL_TEXT'];
				}

				if($descValue) {
					if($arParams['TRUNCATE_LENGTH'] > 0)
						$arItem['DESCRIPTION'] = $obParser->html_cut(strip_tags($descValue), $arParams['TRUNCATE_LENGTH']);
					else
						$arItem['DESCRIPTION'] = $descValue;

					if($descField && is_set(array_flip($arIblockField), $descField))
						$arItem['DESCRIPTION'] = CApiSearch::replaceName($arItem['DESCRIPTION'], $query, $arResult['query']);
				}
			}

			//Свойства и поля товара для вывода
			$arItem['PROPERTY'] = array();
			if($arShowFields) {
				foreach($arShowFields as $fieldId) {
					if($fieldId == 'NAME' || $fieldId == 'PREVIEW_TEXT' || $fieldId == 'DETAIL_TEXT')
						continue;

					if($f_value = $arItem[ $fieldId ]) {
						$fake_value           = ($arIblockField && is_set(array_flip($arIblockField), $fieldId) ? CApiSearch::replaceName($f_value, $query, $arResult['query']) : $f_value);
						$arItem['PROPERTY'][] = array(
							 'NAME'       => $MESS[ 'API_SEARCH_PAGE_FIELD_' . $fieldId ],
							 'CODE'       => $fieldId,
							 'VALUE'      => $f_value,
							 'FAKE_VALUE' => $fake_value,
						);
					}
				}
			}


			$arItem['PRICES']             = array();
			$arItem['MIN_PRICE']          = array();
			$arItem['SHOW_OFFERS']        = ($bShowOffers ? true : false);
			$arItem['OFFERS']             = array();
			$arItem['PROPERTIES']         = array();
			$arItem['DISPLAY_PROPERTIES'] = array();
			$arItem['SECTION']            = array();
			$arItem['BRAND']              = array();
			$arItem['PARENT_ELEMENT_ID']  = "";


			$intKey = $arItem['ID'];

			$arResult['ELEMENTS'][ $intKey ] = $arItem['ID'];
			$arResult['ITEMS'][ $intKey ]    = $arItem;

			$arElementLink[ $intKey ] = &$arResult['ITEMS'][ $intKey ];
		}
		unset($arItem);

		//==============================================================================
		// Ищет значения свойств для товаров и Брэнд/Производитель
		//==============================================================================
		if($bGetProperties && $arElementLink && $arResult["ELEMENTS"]) {
			$arPropFilter = array(
				 'ID'        => $arResult["ELEMENTS"],
				 'IBLOCK_ID' => $iblockID,
			);
			CIBlockElement::GetPropertyValuesArray($arElementLink, $iblockID, $arPropFilter, array('CODE' => $propertyList));

			foreach($arElementLink as &$arItem) {
				if($arResultModules['catalog'])
					CCatalogDiscount::SetProductPropertiesCache($arItem['ID'], $arItem["PROPERTIES"]);

				foreach($propertyList as $pid) {
					if(!isset($arItem["PROPERTIES"][ $pid ]) || isset($arItem['DISPLAY_PROPERTIES'][ $pid ]))
						continue;

					$prop = &$arItem["PROPERTIES"][ $pid ];
					if($prop['VALUE']) {
						$arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, 'catalog_out');

						if($brandProperty && $brandProperty == $prop['CODE']) {
							if(is_array($arProperty['LINK_ELEMENT_VALUE']) && $arProperty['LINK_ELEMENT_VALUE']) {
								foreach($arProperty['LINK_ELEMENT_VALUE'] as $arVal) {
									$brandPicture = ($arVal['PREVIEW_PICTURE'] ? $arVal['PREVIEW_PICTURE'] : $arVal['DETAIL_PICTURE']);

									$arItem['BRAND'] = array(
										 'ID'              => $arVal['ID'],
										 'NAME'            => $arVal['NAME'],
										 'DETAIL_PAGE_URL' => $arVal['DETAIL_PAGE_URL'],
										 'PREVIEW_PICTURE' => $arVal['PREVIEW_PICTURE'],
										 'DETAIL_PICTURE'  => $arVal['DETAIL_PICTURE'],
										 'PICTURE'         => CApiSearch::ResizeImageGet($brandPicture, 210, 50),
									);
								}

								unset($brandPicture, $arVal);
							}
							elseif($arProperty['VALUE'] && !is_array($arProperty['VALUE'])) {
								$arBrand = array();

								if(!$arBrand[ $arProperty['VALUE'] ]) {
									$arVal = CIBlockElement::GetList(
										 array(),
										 array('ID' => $arProperty['VALUE']),
										 false,
										 false,
										 array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
									)->GetNext(true, false);

									$brandPicture = ($arVal['PREVIEW_PICTURE'] ? $arVal['PREVIEW_PICTURE'] : $arVal['DETAIL_PICTURE']);

									$arBrand[ $arProperty['VALUE'] ] = array(
										 'ID'              => $arVal['ID'],
										 'NAME'            => $arVal['NAME'],
										 'DETAIL_PAGE_URL' => $arVal['DETAIL_PAGE_URL'],
										 'PREVIEW_PICTURE' => $arVal['PREVIEW_PICTURE'],
										 'DETAIL_PICTURE'  => $arVal['DETAIL_PICTURE'],
										 'PICTURE'         => CApiSearch::ResizeImageGet($brandPicture, 210, 50),
									);
								}

								$arItem['BRAND'] = $arBrand[ $arProperty['VALUE'] ];
							}

							continue;
						}

						if($arProperty['DISPLAY_VALUE']) {
							$display_value = (is_array($arProperty['DISPLAY_VALUE']) ? implode(', ', $arProperty['DISPLAY_VALUE']) : $arProperty['DISPLAY_VALUE']);
							//$fake_value = (in_array($pid,$arIblockProperty) ? CApiSearch::replaceName($display_value, $query) : $display_value);
							$fake_value = ($arIblockProperty && is_set(array_flip($arIblockProperty), $pid) ? CApiSearch::replaceName($display_value, $query, $arResult['query']) : $display_value);

							$arItem['PROPERTY'][] = array(
								 'NAME'       => $arProperty['NAME'],
								 'VALUE'      => $display_value,
								 'FAKE_VALUE' => $fake_value,
							);
						}

						$arItem['DISPLAY_PROPERTIES'][ $pid ] = $arProperty;
					}
					unset($prop, $arProperty);
				}
				unset($pid);
			}
			unset($arItem, $arPropFilter);
		}



		//==============================================================================
		// Ищет для ТП изображение Брэнда/Производителя из свойства привязки товара и раздел товара
		//==============================================================================
		if($offers['IBLOCK_ID'] && $arResult['ELEMENTS'] && $arElementLink && ($bShowSection || $brandProperty)) {

			//Получим соответствия ID ТП и товаров
			if($arOffersItems = CCatalogSKU::getProductList($arResult['ELEMENTS'], $iblockID)) {
				foreach($arOffersItems as $key => $val) {
					$arResult['OFFERS_ELEMENT_ID'][ $val['ID'] ] = $val['ID'];

					if($arElementLink[ $key ]) {
						$arElementLink[ $key ]['PARENT_ELEMENT_ID'] = $val['ID'];
					}
				}

				//Получим всю необходимую информацию о товарах ТП
				if($arResult['OFFERS_ELEMENT_ID']) {
					$arParentElements = array();
					$rsElement        = CIBlockElement::GetList(array(), array('ID' => $arResult['OFFERS_ELEMENT_ID']), false, false, array());
					while($arItem = $rsElement->Fetch()) {
						$arItem['PROPERTIES']         = array();
						$arItem['DISPLAY_PROPERTIES'] = array();
						$arItem['BRAND']              = array();

						$arParentElements[ $arItem['ID'] ] = $arItem;
					}

					//Если в настройках задано свойство Брэнд/Производитель товара
					if($brandProperty) {
						$arPropFilter = array(
							 'ID'        => $arResult['OFFERS_ELEMENT_ID'],
							 'IBLOCK_ID' => $offers['PRODUCT_IBLOCK_ID'],
						);
						CIBlockElement::GetPropertyValuesArray($arParentElements, $offers['PRODUCT_IBLOCK_ID'], $arPropFilter, array('CODE' => $brandProperty));

						foreach($arParentElements as &$arItem) {
							if($arResultModules['catalog'])
								CCatalogDiscount::SetProductPropertiesCache($arItem['ID'], $arItem["PROPERTIES"]);

							$prop = &$arItem["PROPERTIES"][ $brandProperty ];
							if($prop['VALUE']) {
								$arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, 'catalog_out');

								if(is_array($arProperty['LINK_ELEMENT_VALUE']) && $arProperty['LINK_ELEMENT_VALUE']) {
									foreach($arProperty['LINK_ELEMENT_VALUE'] as $arVal) {
										$brandPicture = ($arVal['PREVIEW_PICTURE'] ? $arVal['PREVIEW_PICTURE'] : $arVal['DETAIL_PICTURE']);

										$arItem['BRAND'] = array(
											 'ID'              => $arVal['ID'],
											 'NAME'            => $arVal['NAME'],
											 'DETAIL_PAGE_URL' => $arVal['DETAIL_PAGE_URL'],
											 'PREVIEW_PICTURE' => $arVal['PREVIEW_PICTURE'],
											 'DETAIL_PICTURE'  => $arVal['DETAIL_PICTURE'],
											 'PICTURE'         => CApiSearch::ResizeImageGet($brandPicture, 210, 50),
										);
									}

									unset($brandPicture, $arVal);
								}
								elseif($arProperty['VALUE'] && !is_array($arProperty['VALUE'])) {
									if(!$arBrand[ $arProperty['VALUE'] ]) {
										$arVal = CIBlockElement::GetList(
											 array(),
											 array('ID' => $arProperty['VALUE']),
											 false,
											 false,
											 array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
										)->GetNext(true, false);

										$brandPicture = ($arVal['PREVIEW_PICTURE'] ? $arVal['PREVIEW_PICTURE'] : $arVal['DETAIL_PICTURE']);

										$arBrand[ $arProperty['VALUE'] ] = array(
											 'ID'              => $arVal['ID'],
											 'NAME'            => $arVal['NAME'],
											 'DETAIL_PAGE_URL' => $arVal['DETAIL_PAGE_URL'],
											 'PREVIEW_PICTURE' => $arVal['PREVIEW_PICTURE'],
											 'DETAIL_PICTURE'  => $arVal['DETAIL_PICTURE'],
											 'PICTURE'         => CApiSearch::ResizeImageGet($brandPicture, 210, 50),
										);
									}

									$arItem['BRAND'] = $arBrand[ $arProperty['VALUE'] ];
								}
							}
						}
						unset($arItem, $arPropFilter);
					}


					//Запишем в ТП необходимые данные для вывода в результатах поиска
					foreach($arElementLink as $key => &$arItem) {
						if($arParentItem = $arParentElements[ $arItem['PARENT_ELEMENT_ID'] ]) {
							//Запишем раздел
							if($bShowSection && $iblockSectionId = $arParentItem['IBLOCK_SECTION_ID']) {
								$arItem['IBLOCK_SECTION_ID']                = $iblockSectionId;
								$arResult['SECTION_ID'][ $iblockSectionId ] = $iblockSectionId;
							}

							//$arItem['PROPERTIES']         = $arParentItem['PROPERTIES'];
							//$arItem['DISPLAY_PROPERTIES'] = $arParentItem['DISPLAY_PROPERTIES'];

							//Запишем брэнд
							$arItem['BRAND'] = $arParentItem['BRAND'];
						}
					}

					unset($dbRes, $rsElement, $arItem);
				}
			}
		}


		//==============================================================================
		// Ищет изображение товара в ТП и сами ТП для товара
		//==============================================================================
		if($iblockOffers['IBLOCK_ID'] && $arElementLink) {
			$offersFilter = array(
				 'IBLOCK_ID'          => $iblockID,
				 'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
			);
			if(!$arParams["USE_PRICE_COUNT"])
				$offersFilter['SHOW_PRICE_COUNT'] = $arParams['SHOW_PRICE_COUNT'];

			$offersSelect = array(
				 'ID',
				 'PREVIEW_PICTURE',
				 'DETAIL_PICTURE',
				 'LINK_ELEMENT_ID',
				 'DETAIL_PAGE_URL',
			);

			$arOffers = CIBlockPriceTools::GetOffersArray(
				 $offersFilter,
				 $arResult['ELEMENTS'],
				 array('SORT' => 'ASC', 'CATALOG_PRICE_BASE' => 'ASC'), //$arSort
				 $offersSelect,//$arParams["OFFERS_FIELD_CODE"],
				 array(),//$arParams["OFFERS_PROPERTY_CODE"],
				 0,//$arParams["OFFERS_LIMIT"],
				 $arResult["PRICES"],
				 $arParams['PRICE_VAT_INCLUDE'],
				 $arConvertParams
			);

			if($arOffers) {
				foreach($arOffers as &$arOffer) {
					if($arItem = $arElementLink[ $arOffer['LINK_ELEMENT_ID'] ]) {
						$linkElementId = $arOffer['LINK_ELEMENT_ID'];

						$picture = false;
						if($arParams['PICTURE']) {
							if($issetPreviewPicture && $arOffer['PREVIEW_PICTURE'])
								$picture = $arOffer['PREVIEW_PICTURE'];
							elseif($issetDetailPicture && $arOffer['DETAIL_PICTURE'])
								$picture = $arOffer['DETAIL_PICTURE'];
						}

						$arPicture = array();
						if($picture) {
							if($arParams['RESIZE_PICTURE']) {
								$arPicture = CApiSearch::ResizeImageGet(
									 $picture,
									 $arParams['PICTURE_WIDTH'],
									 $arParams['PICTURE_HEIGHT']
								);
							}
							else {
								$arPicture = CFile::GetFileArray($picture);
							}


							if(!$arItem['PICTURE'])
								$arElementLink[ $linkElementId ]['PICTURE'] = $arPicture;

							$arOffer['PICTURE'] = $arPicture;
						}
						else {
							$arOffer['PICTURE'] = $arItem['PICTURE'];
						}


						$arElementLink[ $arOffer['LINK_ELEMENT_ID'] ]['OFFERS'][ $arOffer['ID'] ] = $arOffer;
					}
				}
			}
			unset($arOffers, $arOffer, $arItem, $arPicture, $picture);
		}
	}


	//==============================================================================
	// Получаем разделы по ID и кэшируем
	//==============================================================================
	if($arResult['SECTION_ID'] && $arElementLink) {
		$obCache    = new CPHPCache();
		$cacheTime  = 3600;
		$sCacheId   = md5(serialize($arResult['SECTION_ID']));
		$sCachePath = $GLOBALS['CACHE_MANAGER']->GetCompCachePath($this->__relativePath);

		if($obCache->InitCache($cacheTime, $sCacheId, $sCachePath)) {
			$arCacheVars = $obCache->GetVars();

			$arResult['SECTIONS'] = $arCacheVars['SECTIONS'];
		}
		elseif($obCache->StartDataCache()) {

			$rsSections = CIBlockSection::GetList(
				 array('SORT' => 'ASC', 'NAME' => 'ASC'),
				 array('=ID' => $arResult['SECTION_ID']),
				 false,
				 array('ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL'),
				 false
			);

			$sectionUrl = trim($arParams['IBLOCK_'. $iblockID .'_SECTION_URL']);
			if(strlen($sectionUrl)>0)
				$rsSections->SetUrlTemplates('',$sectionUrl);

			while($arSection = $rsSections->GetNext(true, false)) {
				$arResult['SECTIONS'][ $arSection['ID'] ] = $arSection;
			}
			unset($rsSections, $arSection);

			$obCache->EndDataCache(array(
				 'SECTIONS' => $arResult['SECTIONS'],
			));
		}

		//Подсчет разделов
		$arResult['COUNT_SECTIONS'] = count($arResult['SECTIONS']);
	}



	//==============================================================================
	// $arResult
	//==============================================================================
	$arResultItems = array();
	if($arElementLink) {
		foreach($arElementLink as $key => &$arItem) {
			//Подставим информацию о цене
			if($arResult["PRICES"]) {
				if($arItem['OFFERS'] && !$arItem['PRICES']) {
					$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
						 $arItem['OFFERS'],
						 $arConvertParams['CURRENCY_ID']//$boolConvert ? $arConvertParams['CURRENCY_ID'] : $strBaseCurrency
					);
				}
				else {
					$arItem['PRICES'] = CIBlockPriceTools::GetItemPrices(
						 $arItem['IBLOCK_ID'],
						 $arResult['PRICES'],
						 $arItem,
						 $arParams['PRICE_VAT_INCLUDE'],
						 $arConvertParams
					);
				}
			}

			//Подставим информацию о разделе товара
			if($arItem['IBLOCK_SECTION_ID'] && $arResult['SECTIONS']) {
				$arItem['SECTION'] = $arResult['SECTIONS'][ $arItem['IBLOCK_SECTION_ID'] ];
			}

			//Prepare items
			$arResultItems[ $key ] = $arItem;
		}

		unset($arResult['ITEMS'], $arElementLink, $arItem, $arOffer);
	}


	//PREPARE NEW ITEMS
	$arResult['COUNT_ITEMS'] = count($arResultItems);


	if($arParams['USE_TITLE_RANK'] && $arResultItems) {
		$arTmpSortItems = array();
		foreach($arResultItems as $key => $arItem) {
			if(
				 preg_match('#\b' . preg_quote($query, '#') . '\b#im' . BX_UTF_PCRE_MODIFIER, $arItem['NAME']) ||
				 preg_match('#\b' . preg_quote($query, '#') . '\b#im' . BX_UTF_PCRE_MODIFIER, $arItem['TAGS']) ||
				 preg_match('#\b' . preg_quote($query, '#') . '\b#im' . BX_UTF_PCRE_MODIFIER, $arItem['PREVIEW_TEXT']) ||
				 preg_match('#\b' . preg_quote($query, '#') . '\b#im' . BX_UTF_PCRE_MODIFIER, $arItem['DETAIL_TEXT'])
			) {
				$arTmpSortItems[ $key ] = $arItem;
				unset($arResultItems[ $key ]);
			}
		}

		if($arTmpSortItems)
			$arResultItems = array_merge($arTmpSortItems, $arResultItems);

		unset($arTmpSortItems, $arItem);
	}

	$rsContent = new CDBResult;
	$rsContent->InitFromArray($arResultItems);
	unset($arResultItems);

	$rsContent->NavStart($arParams['ITEMS_LIMIT'], $arParams['PAGER_SHOW_ALL'], false);

	//GetPageNavStringEx
	ob_start();
	if($rsContent->IsNavPrint()) {
		$rsContent->NavPrint(
			 $arParams['PAGER_TITLE'],
			 $arParams['PAGER_SHOW_ALWAYS'],
			 null,
			 $arParams['PAGER_TEMPLATE']
		);
	}
	$arResult['NAV_STRING'] = ob_get_contents();
	ob_end_clean();

	/*$arResult['NAV_STRING'] = $rsContent->GetPageNavStringEx(
		$navComponentObject,
		$arParams['PAGER_TITLE'],
		$arParams['PAGER_TEMPLATE'],
		$arParams['PAGER_SHOW_ALWAYS'],
		$this
	);*/



	while($arItem = $rsContent->Fetch()) {
		$arResult['ITEMS'][] = $arItem;
	}

	unset($rsContent, $arItem);
}


//COUNT_RESULT
if($arResult['COUNT_SECTIONS'] > 0) {
	$arResult['COUNT_RESULT'] = GetMessage(
		 'API_SEARCH_PAGE_RESULT_SECTIONS_TEXT',
		 array(
				'#COUNT_ITEMS#'    => $arResult['COUNT_ITEMS'],
				'#COUNT_SECTIONS#' => $arResult['COUNT_SECTIONS'],
				'#FOUND#'          => CApiSearch::getDeclination($arResult['COUNT_ITEMS'], GetMessage('API_SEARCH_PAGE_RESULT_FOUND_MESS')),
				'#RESULT#'         => CApiSearch::getDeclination($arResult['COUNT_ITEMS'], GetMessage('API_SEARCH_PAGE_RESULT_ITEMS_MESS')),
				'#CATEGORY#'       => CApiSearch::getDeclination($arResult['COUNT_SECTIONS'], GetMessage('API_SEARCH_PAGE_RESULT_SECTIONS_MESS')),
		 )
	);
}
else {
	$arResult['COUNT_RESULT'] = GetMessage(
		 'API_SEARCH_PAGE_RESULT_ITEMS_TEXT',
		 array(
				'#COUNT_ITEMS#' => $arResult['COUNT_ITEMS'],
				'#FOUND#'       => CApiSearch::getDeclination($arResult['COUNT_ITEMS'], GetMessage('API_SEARCH_PAGE_RESULT_FOUND_MESS')),
				'#RESULT#'      => CApiSearch::getDeclination($arResult['COUNT_ITEMS'], GetMessage('API_SEARCH_PAGE_RESULT_ITEMS_MESS')),
		 )
	);
}


$arResult['COMPONENT_ID'] = $this->GetEditAreaId($this->__currentCounter);


if($isAjax) {
	$APPLICATION->RestartBuffer();
	$this->IncludeComponentTemplate('ajax');

	require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_after.php');
	die();
}
else {
	$this->IncludeComponentTemplate();
}