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


$arResult['COMPONENT_ID'] = $this->GetEditAreaId($this->__currentCounter);


//Escape other search with api:search.title component
$isAjax = true;
if(isset($_REQUEST['API_SEARCH_TITLE_ID'])) {
	if($arResult['COMPONENT_ID'] != $_REQUEST['API_SEARCH_TITLE_ID']) {
		$isAjax = false;
	}
}

//Escape double search with api:search.page component
if($_REQUEST['API_SEARCH_PAGE_AJAX'] == 'Y')
	$isAjax = false;


if($isAjax) {
	global $MESS;
	$GLOBALS['apiSearchFilter'] = array();
	CApiSearch::incComponentLang($this);

	$query         = preg_replace('/\s+/', ' ', trim(strip_tags($_REQUEST['q'])));
	$arResult['q'] = $query;
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
	$arParams['USE_TITLE_RANK']   = ($arParams['USE_TITLE_RANK'] == 'Y');
	$arParams['USE_SEARCH_QUERY'] = ($arParams['USE_SEARCH_QUERY'] == 'Y');
	$arParams['ITEMS_LIMIT']      = $arParams['ITEMS_LIMIT'] ? intval($arParams['ITEMS_LIMIT']) : 15;
	$arParams['RESULT_PAGE']      = trim($arParams['RESULT_PAGE']);
	$arParams['RESULT_URL']       = CHTTP::urlAddParams($arParams['RESULT_PAGE'], array('q' => $query), array('encode' => true));

	$arParams['IBLOCK_ID'] = (array)$arParams['IBLOCK_ID'];
	foreach($arParams['IBLOCK_ID'] as $k => $v)
		if($v == "")
			unset($arParams['IBLOCK_ID'][ $k ]);


	$arParams['SORT_BY1']    = trim($arParams['SORT_BY1']);
	$arParams['SORT_ORDER1'] = trim($arParams['SORT_ORDER1']);
	$arParams['SORT_BY2']    = trim($arParams['SORT_BY2']);
	$arParams['SORT_ORDER2'] = trim($arParams['SORT_ORDER2']);
	$arParams['SORT_BY3']    = trim($arParams['SORT_BY3']);
	$arParams['SORT_ORDER3'] = trim($arParams['SORT_ORDER3']);


	//VISUAL
	$arParams['INPUT_PLACEHOLDER'] = ($MESS['API_SEARCH_TITLE_INPUT_PLACEHOLDER'] ? $MESS['API_SEARCH_TITLE_INPUT_PLACEHOLDER'] : $arParams['~INPUT_PLACEHOLDER']);
	$arParams['BUTTON_TEXT']       = ($MESS['API_SEARCH_TITLE_BUTTON_TEXT'] ? $MESS['API_SEARCH_TITLE_BUTTON_TEXT'] : $arParams['~BUTTON_TEXT']);
	$arParams['RESULT_NOT_FOUND']  = ($MESS['API_SEARCH_TITLE_RESULT_NOT_FOUND'] ? $MESS['API_SEARCH_TITLE_RESULT_NOT_FOUND'] : $arParams['~RESULT_NOT_FOUND']);
	$arParams['RESULT_URL_TEXT']   = ($MESS['API_SEARCH_TITLE_RESULT_URL_TEXT'] ? $MESS['API_SEARCH_TITLE_RESULT_URL_TEXT'] : $arParams['~RESULT_URL_TEXT']);
	$arParams['PICTURE']           = (array)$arParams['PICTURE'];
	$arParams['RESIZE_PICTURE']    = ($arParams['RESIZE_PICTURE'] ? explode('x', $arParams['RESIZE_PICTURE']) : array());
	$arParams['PICTURE_WIDTH']     = $arParams['RESIZE_PICTURE'][0];
	$arParams['PICTURE_HEIGHT']    = ($arParams['RESIZE_PICTURE'][1] ? $arParams['RESIZE_PICTURE'][1] : (int)$arParams['RESIZE_PICTURE'][0] * 2);

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
	$arParams['INCLUDE_CSS'] = $arParams['INCLUDE_CSS'] == 'Y';
	$arParams['DETAIL_URL']  = trim($arParams['DETAIL_URL']);


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


	//JQUERY
	$arParams['INCLUDE_JQUERY'] = $arParams['INCLUDE_JQUERY'] == 'Y';
	if($arParams['INCLUDE_JQUERY'])
		CJSCore::Init(array('jquery'));

	$arParams['JQUERY_BACKDROP_BACKGROUND'] = (isset($arParams['JQUERY_BACKDROP_BACKGROUND']) ? trim($arParams['JQUERY_BACKDROP_BACKGROUND']) : '#3879D9');
	$arParams['JQUERY_BACKDROP_OPACITY']    = (isset($arParams['JQUERY_BACKDROP_OPACITY']) ? trim($arParams['JQUERY_BACKDROP_OPACITY']) : '0.1');
	$arParams['JQUERY_BACKDROP_Z_INDEX']    = (isset($arParams['JQUERY_BACKDROP_Z_INDEX']) ? intval($arParams['JQUERY_BACKDROP_Z_INDEX']) : 900);
	$arParams['JQUERY_WAIT_TIME']           = ($arParams['JQUERY_WAIT_TIME'] ? intval($arParams['JQUERY_WAIT_TIME']) : 500);
	$arParams['JQUERY_SCROLL_THEME']        = ($arParams['JQUERY_SCROLL_THEME'] ? $arParams['JQUERY_SCROLL_THEME'] : '_simple');
	//$arParams['USE_SCROLL']                 = $arParams['USE_SCROLL'] == 'Y';

	//==============================================================================
	//                               $arResult
	//==============================================================================
	$arResult['COUNT_ITEMS'] = 0;
	$arResult['CATEGORIES']  = array();
	$arResult['ELEMENTS']    = array();
	$arResult['PRICES']      = array();
	$arResult['SECTION_ID']  = array();
	$arResult['SECTIONS']    = array();


	$arElementLink = array();
	$arIblockId    = $arParams['IBLOCK_ID'];
	$countIblockId = count($arIblockId);

	if($arIblockId && strlen($arResult['query']) >= API_SEARCH_CHAR_LENGTH) {
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



		$maxItemsInCat = $arParams['ITEMS_LIMIT'];
		if($countIblockId > 1)
			$maxItemsInCat = round($arParams['ITEMS_LIMIT'] / count($arIblockId), 0, PHP_ROUND_HALF_DOWN);

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

			//Prepare propertyList
			$propertyList   = array();
			$arShowProperty = $arParams[ 'IBLOCK_' . $iblockID . '_SHOW_PROPERTY' ];
			if($arShowProperty) {
				foreach($arShowProperty as $propCode) {
					if($propCode)
						$propertyList[] = $propCode;
				}
			}
			$bGetProperties = !empty($propertyList);


			/*$propertyList = array();
			if($bGetProperties)
			{
				//Last version select all or one property
				$propertyIterator = CIBlock::GetProperties(
					$iblockID,
					array('SORT' => 'ASC', 'NAME' => 'ASC'),
					array('ACTIVE' => 'Y')
				);

				//Work fine
				$propertyIterator = Bitrix\Iblock\PropertyTable::getList(array(
					'select' => array('ID', 'CODE'),
					'filter' => array('=IBLOCK_ID' => $iblockID, '=CODE' => $arProps),
					'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
				));
				while($property = $propertyIterator->fetch())
				{
					$code = (string)$property['CODE'];
					if($code == '')
						$code = $property['ID'];

					$propertyList[] = $code;
					unset($code);
				}
				unset($property, $propertyIterator);
			}*/


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
				foreach($arIblockField as $fieldId) {
					if($fieldId) {
						$arFilter[ $MASK . $fieldId ] = $arResult['query'];

						if($preg_replace)
							$arFilter[ '?' . $fieldId ] = $preg_replace;
					}
				}
			}


			//Global filter for bitrix:catalog.section
			$GLOBALS['apiSearchFilter'] = $arFilter;


			//$arSelect
			$arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL');

			if($arParams['PICTURE'])
				$arSelect = array_merge($arSelect, $arParams['PICTURE']);

			if($arParams['DESCRIPTION'])
				$arSelect = array_merge($arSelect, $arParams['DESCRIPTION']);


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


			//$nTopCount
			$nTopCount = $maxItemsInCat;
			if($countIblockId > 1 && $lastItemsInCat != $maxItemsInCat) {
				if($lastItemsInCat < $maxItemsInCat)
					$nTopCount = $maxItemsInCat + ($maxItemsInCat - $lastItemsInCat);
			}
			$lastItemsInCat = 0;


			$dbRes = CIBlockElement::GetList($arSort, $arFilter, false, array('nTopCount' => $nTopCount), $arSelect);

			$sectionUrl = trim($arParams['IBLOCK_'. $iblockID .'_SECTION_URL']);
			$detailUrl = trim($arParams['IBLOCK_'. $iblockID .'_DETAIL_URL']);
			if(strlen($detailUrl)>0 || strlen($sectionUrl)>0)
				$dbRes->SetUrlTemplates($detailUrl,$sectionUrl);

			while($arItem = $dbRes->GetNext(true, false)) {
				$lastItemsInCat++;

				$arItem['FAKE_NAME'] = CApiSearch::replaceName($arItem['NAME'], $query, $arResult['query']);


				if($arItem['IBLOCK_SECTION_ID'])
					$arResult['SECTION_ID'][ $arItem['IBLOCK_SECTION_ID'] ] = $arItem['IBLOCK_SECTION_ID'];


				$arItem['PICTURE'] = array();
				$picture           = false;
				if($arParams['PICTURE']) {
					if($arItem['PREVIEW_PICTURE'] && $issetPreviewPicture)
						$picture = $arItem['PREVIEW_PICTURE'];
					elseif($arItem['DETAIL_PICTURE'] && $issetDetailPicture)
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


				$arItem['PROPERTY'] = array();
				if($arShowFields) {
					foreach($arShowFields as $fieldId) {
						if($fieldId == 'NAME')
							continue;

						if($f_value = $arItem[ $fieldId ]) {
							//$fake_value = (in_array($fieldId,$arIblockField) ? CApiSearch::replaceName($f_value, $query) : $f_value);
							$fake_value           = ($arIblockField && is_set(array_flip($arIblockField), $fieldId) ? CApiSearch::replaceName($f_value, $query, $arResult['query']) : $f_value);
							$arItem['PROPERTY'][] = array(
								 'NAME'       => $MESS[ 'API_SEARCH_TITLE_FIELD_' . $fieldId ],
								 'CODE'       => $fieldId,
								 'VALUE'      => $f_value,
								 'FAKE_VALUE' => $fake_value,
							);
						}
					}
				}



				$arItem['PRICES']             = array();
				$arItem['MIN_PRICE']          = array();
				$arItem['OFFERS']             = array();
				$arItem['PROPERTIES']         = array();
				$arItem['DISPLAY_PROPERTIES'] = array();


				$intKey = $arItem['ID'];

				$arResult['ELEMENTS'][ $intKey ]                         = $arItem['ID'];
				$arResult['CATEGORIES'][ $iblockID ]['ITEMS'][ $intKey ] = $arItem;

				//Link
				$arElementLink[ $intKey ] = &$arResult['CATEGORIES'][ $iblockID ]['ITEMS'][ $intKey ];
			}
			unset($arItem);


			if($bGetProperties && $arElementLink && $arResult['ELEMENTS']) {
				$arPropFilter = array(
					 'ID'        => $arResult['ELEMENTS'],
					 'IBLOCK_ID' => $iblockID,
				);
				CIBlockElement::GetPropertyValuesArray($arElementLink, $iblockID, $arPropFilter, array('CODE' => $propertyList));

				foreach($arElementLink as &$arItem) {
					if($arResultModules['catalog'])
						CCatalogDiscount::SetProductPropertiesCache($arItem['ID'], $arItem['PROPERTIES']);


					foreach($propertyList as $pid) {
						if(!isset($arItem["PROPERTIES"][ $pid ]) || isset($arItem['DISPLAY_PROPERTIES'][ $pid ]))
							continue;

						$prop = &$arItem["PROPERTIES"][ $pid ];
						if($prop['VALUE']) {
							$arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, 'catalog_out');

							if($arProperty['DISPLAY_VALUE']) {
								$display_value = (is_array($arProperty['DISPLAY_VALUE']) ? implode(', ', $arProperty['DISPLAY_VALUE']) : $arProperty['DISPLAY_VALUE']);
								//$fake_value = ($arIblockProperty && in_array($pid,$arIblockProperty) ? CApiSearch::replaceName($display_value, $query) : $display_value);
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

			//TODO: Let it be…
			//Search offers picture for iblock element
			/*if($arParams['USE_OFFERS_PICTURE'] && $arResultModules['catalog'] && $arElementEmptyPictureId)
			{
				$iblockOffers = CCatalogSku::GetInfoByProductIBlock($iblockID);
				if($iblockOffers['IBLOCK_ID'])
				{
					$arOffersPerElement = array();
					$productProperty = 'PROPERTY_'.$iblockOffers['SKU_PROPERTY_ID'];
					$productPropertyValue = $productProperty.'_VALUE';

					$offersOrder  = array('SORT' => 'ASC', 'CATALOG_PRICE_BASE' => 'ASC');
					$offersFilter = array(
						'IBLOCK_ID'      => $iblockOffers['IBLOCK_ID'],
						$productProperty => $arElementEmptyPictureId,
						"ACTIVE" => "Y",
						array(
							'LOGIC'            => 'OR',
							'!PREVIEW_PICTURE' => false,
							'!DETAIL_PICTURE'  => false,
						),
					);
					$offersSelect = array(
						'ID',
						'IBLOCK_ID',
						'PREVIEW_PICTURE',
						'DETAIL_PICTURE',
						$productProperty
					);
					$offersLimit = 1;

					$rsOffers = CIBlockElement::GetList(
						$offersOrder,
						$offersFilter,
						false,
						false,
						$offersSelect
					);

					while($arOffer = $rsOffers->Fetch())
					{
						$arOffer['ID'] = (int)$arOffer['ID'];
						$element_id    = (int)$arOffer[$productPropertyValue];

						if($offersLimit > 0)
						{
							$arOffersPerElement[$element_id]++;
							if($arOffersPerElement[$element_id] > $offersLimit)
								continue;
						}

						if($element_id > 0)
						{
							if($arItem = $arElementLink[ $element_id ])
							{
								$linkElementId = $arOffer['LINK_ELEMENT_ID'];

								$picture = false;
								if($arParams['PICTURE'])
								{
									if($issetPreviewPicture && $arOffer['PREVIEW_PICTURE'])
										$picture = $arOffer['PREVIEW_PICTURE'];
									elseif($issetDetailPicture && $arOffer['DETAIL_PICTURE'])
										$picture = $arOffer['DETAIL_PICTURE'];
								}

								$arPicture = array();
								if($picture)
								{
									if($arParams['RESIZE_PICTURE'])
									{
										$arPicture = CApiSearch::ResizeImageGet(
											$picture,
											$arParams['PICTURE_WIDTH'],
											$arParams['PICTURE_HEIGHT']
										);
									}
									else
									{
										$arPicture = CFile::GetFileArray($picture);
									}

									$arElementLink[$element_id]['PICTURE'] = $arPicture;
								}
							}
						}
					}
				}
			}*/

			//Prepare offers
			$iblockOffers = array();
			if($arResultModules['catalog'])
				$iblockOffers = CCatalogSku::GetInfoByProductIBlock($iblockID);

			if($iblockOffers['IBLOCK_ID'] && $arElementLink) {
				$arGroup     = CCatalogGroup::GetBaseGroup();
				$offersOrder = array(
					 'SORT'                            => 'ASC',
					 'CATALOG_PRICE_' . $arGroup['ID'] => 'ASC',
				);

				$offersFilter = array(
					 'IBLOCK_ID'          => $iblockID,
					 'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
				);
				if(!$arParams["USE_PRICE_COUNT"])
					$offersFilter['SHOW_PRICE_COUNT'] = $arParams['SHOW_PRICE_COUNT'];

				$offersSelect = array(
					 'ID',
					 'IBLOCK_ID',
					 'PREVIEW_PICTURE',
					 'DETAIL_PICTURE',
				);

				$arOffers = CIBlockPriceTools::GetOffersArray(
					 $offersFilter,
					 $arResult['ELEMENTS'],
					 $offersOrder,
					 $offersSelect,//$arParams["OFFERS_FIELD_CODE"],
					 array(),//$arParams["OFFERS_PROPERTY_CODE"],
					 1,//$arParams["OFFERS_LIMIT"],
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


		//PRICES
		if($arResult["PRICES"] && $arElementLink) {
			foreach($arElementLink as $key => &$arItem) {
				//$arPrice = CPrice::GetBasePrice($arItem['ID']);
				//$arPrice = CCatalogProduct::GetOptimalPrice($arItem['ID']);

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

					//if (!empty($arItem['PRICES']))
					//$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromList($arItem['PRICES']);
				}
			}

			unset($arElementLink, $arItem);
		}

		//PREPARE ITEMS
		$arResult['COUNT_ITEMS'] = count($arResult['ELEMENTS']);


		if($arParams['USE_TITLE_RANK'] && $arResult['CATEGORIES']) {
			foreach($arResult['CATEGORIES'] as $catId => &$arCat) {
				if($arCat['ITEMS']) {
					$arTmpSortItems = array();
					foreach($arCat['ITEMS'] as $key => $arItem) {
						if(preg_match('#\b' . preg_quote($query, '#') . '\b#im' . BX_UTF_PCRE_MODIFIER, $arItem['NAME'])) {
							$arTmpSortItems[ $key ] = $arItem;
							unset($arCat['ITEMS'][ $key ]);
						}
					}

					if($arTmpSortItems)
						$arCat['ITEMS'] = array_merge($arTmpSortItems, $arCat['ITEMS']);
				}
			}

			unset($arTmpSortItems, $arItem);
		}
	}


	$arResult['FORM_ACTION'] = htmlspecialcharsbx($arParams['RESULT_PAGE']);


	if($_REQUEST['API_SEARCH_TITLE_AJAX'] === 'Y') {
		$APPLICATION->RestartBuffer();

		if(!empty($query))
			$this->IncludeComponentTemplate('ajax');

		require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_after.php');
		die();
	}
}

$this->IncludeComponentTemplate();