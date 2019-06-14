<?php

namespace Api\Core\Iblock;

use Bitrix\Main,
	 Bitrix\Main\Loader,
	 Bitrix\Main\Error,
	 Bitrix\Main\ErrorCollection,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Currency,
	 Bitrix\Iblock,
	 Bitrix\Catalog;

Loc::loadMessages(__FILE__);

Loader::includeModule('iblock');

abstract class Base extends \CBitrixComponent
{
	const ERROR_TEXT = 1;

	/** @var ErrorCollection */
	protected $errorCollection;

	protected $siteId = false;

	protected $useCatalog       = false;
	protected $useCurrency      = false;
	protected $isIblockCatalog  = false;
	protected $useDiscountCache = false;

	protected $filterFields = array();

	protected $elementFilter    = array();
	protected $elementFields    = array();
	protected $elementPropCodes = array();
	protected $offerFields      = array();
	protected $offerPropCodes   = array();

	/** @var array Array of ids to show directly */
	protected $iblockProducts    = array(); //Массив ИБ с ID элементов
	protected $elements          = array(); //Массив елементов ИБ без OFFERS
	protected $elementLinks      = array(); //Ссылки на элементы из $this->elements
	protected $productWithOffers = array(); //Массив ИБ с ID торговых предложений
	protected $productWithPrices = array(); //Массив ID простых товаров (TYPE_PRODUCT|TYPE_SET|TYPE_SKU)

	/** @var array Item prices (new format) */
	protected $prices          = array();
	protected $calculatePrices = array();
	protected $measures        = array();
	protected $ratios          = array();
	protected $quantityRanges  = array();
	protected $storage         = array();
	protected $multiIblockMode = false;

	protected $profile  = array();
	protected $arParams = array();
	protected $arResult = array();

	/**
	 * Base constructor.
	 * @param \CBitrixComponent|null $component		Component object if exists.
	 */
	public function __construct($component = null)
	{
		parent::__construct($component);
		$this->errorCollection = new ErrorCollection();
	}

	/**
	 * Processing of component parameters.
	 *
	 * @param array $params			Raw component parameters values.
	 * @return mixed
	 */
	public function onPrepareComponentParams($params)
	{
		$this->checkModules();

		return $params;
	}

	protected function checkModules()
	{
		$this->useCatalog  = Loader::includeModule('catalog');
		$this->useCurrency = Loader::includeModule('currency');

		$this->storage['MODULES'] = array(
			 'iblock'   => Loader::includeModule('iblock'),
			 'catalog'  => $this->useCatalog,
			 'currency' => $this->useCurrency,
		);

		return true;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// PARAMS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function setDefaultParams()
	{
		$profile = $this->profile;

		$this->setBaseSelect();

		$params['DEBUG_DATA']   = false;
		$params['DEBUG_FILTER'] = false;

		//-- Минимальные параметры --//
		$params['IBLOCK_TYPE']          = (string)$profile['IBLOCK_TYPE_ID'];
		$params['IBLOCK_ID']            = (int)$profile['IBLOCK_ID'];
		$params['ELEMENT_ID']           = array();
		$params['PREVIEW_WIDTH']        = ($profile['PREVIEW_WIDTH'] ? intval($profile['PREVIEW_WIDTH']) : 250);
		$params['PREVIEW_HEIGHT']       = ($profile['PREVIEW_HEIGHT'] ? intval($profile['PREVIEW_HEIGHT']) : 250);
		$params['PRICE_CODE']           = array(); //1 = //array(0 => 'BASE');
		$params['FIELD_CODE']           = $this->getElementFields();
		$params['PROPERTY_CODE']        = $this->getElementProps();
		$params['OFFERS_FIELD_CODE']    = $this->getOfferFields();
		$params['OFFERS_PROPERTY_CODE'] = $this->getOfferProps();
		$params['DETAIL_URL']           = '';

		//-- Цены --//
		$params['USE_PRICE_COUNT']      = $profile['USE_PRICE_COUNT'] == 'Y' ? 'Y' : 'N'; //Использовать вывод цен с диапазонами
		$params['SHOW_PRICE_COUNT']     = 1; //Выводить цены для количества
		$params['PRICE_VAT_INCLUDE']    = $profile['PRICE_VAT_INCLUDE'] == 'Y' ? 'Y' : 'N'; //Включать НДС в цену
		$params['PRICE_VAT_SHOW_VALUE'] = $profile['PRICE_VAT_SHOW_VALUE'] == 'Y' ? 'Y' : 'N'; //Отображать значение НДС
		$params['CONVERT_CURRENCY']     = $profile['CONVERT_CURRENCY'] == 'Y' ? 'Y' : 'N'; //Показывать цены в одной валюте
		$params['CURRENCY_ID']          = $profile['CURRENCY_ID'] ? $profile['CURRENCY_ID'] : 'RUB'; //Валюта, в которую будут сконвертированы цены

		//-- Привязка к сайту --//
		if($profile['SITE_ID'])
			$this->siteId = trim($profile['SITE_ID']);


		//-- Если установлен модуль "Торговый каталог" --//
		if($this->useCatalog) {

			//Типы цен
			if($priceTypeId = intval($profile["PRICE_TYPE"])) {
				if($priceType = Catalog\GroupTable::getRowById($priceTypeId)) {
					$params['PRICE_CODE'][] = $priceType['NAME'];
				}
			}
		}

		$this->arParams = $params;
	}

	protected function setDefaultStorage()
	{
		$params = $this->arParams;

		if($params['IBLOCK_ID']) {
			$this->storage['IBLOCK_PARAMS'] = array(
				 $params['IBLOCK_ID'] => array(
						'FIELD_CODE'           => $params['FIELD_CODE'],
						'PROPERTY_CODE'        => $params['PROPERTY_CODE'],
						'OFFERS_FIELD_CODE'    => $params['OFFERS_FIELD_CODE'],
						'OFFERS_PROPERTY_CODE' => $params['OFFERS_PROPERTY_CODE'],
				 ),
			);
		}
	}

	protected function isMultiIblockMode()
	{
		return (bool)$this->multiIblockMode;
	}

	protected function setMultiIblockMode($state)
	{
		$this->multiIblockMode = (bool)$state;

		return $this;
	}

	protected function hasErrors()
	{
		return (bool)count($this->errorCollection);
	}

	protected function processErrors()
	{
		if(!empty($this->errorCollection)) {

			/** @var Error $error */
			foreach($this->errorCollection as $error) {
				$code = $error->getCode();

				if($code == self::ERROR_TEXT) {
					ShowError($error->getMessage());
				}
				else {
					ShowError($error->getMessage());
				}
			}
		}

		return false;
	}

	/*protected function getSiteId()
	{
		return $this->siteId;
	}*/

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// RESULT
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Clear products data.
	 *
	 * @return void
	 */
	protected function clearItems()
	{
		$this->prices         = array();
		$this->measures       = array();
		$this->ratios         = array();
		$this->quantityRanges = array();
	}

	/**
	 * Create link to elements for fast access.
	 *
	 * @return void
	 */
	protected function makeElementLinks()
	{
		if(!empty($this->elements)) {
			foreach($this->elements as $index => $element) {
				$this->elementLinks[ $element['ID'] ] =& $this->elements[ $index ];
			}
		}
	}

	/**
	 * Check for correct iblocks.
	 */
	protected function checkIblock()
	{
		if(!empty($this->iblockProducts)) {
			$iblocks        = array();
			$iblockIterator = Iblock\IblockSiteTable::getList(array(
				 'select' => array('IBLOCK_ID'),
				 'filter' => array(
						'=IBLOCK_ID' => array_keys($this->iblockProducts),
						//'=SITE_ID'       => $this->getSiteId(),
						//'=IBLOCK.ACTIVE' => 'Y',
				 ),
			));
			while($iblock = $iblockIterator->fetch()) {
				$iblocks[ $iblock['IBLOCK_ID'] ] = true;
			}

			foreach($this->iblockProducts as $iblock => $products) {
				if(!isset($iblocks[ $iblock ])) {
					unset($this->iblockProducts[ $iblock ]);
				}
			}

			if(empty($this->iblockProducts)) {
				$this->errorCollection->setError(new Error(Loc::getMessage('INVALID_IBLOCK'), self::ERROR_TEXT));
			}
		}
	}

	/**
	 * Return user groups. Now worked only with current user.
	 *
	 * @return array
	 */
	protected function getUserGroups()
	{
		/** @global \CUser $USER */
		global $USER;
		$result = array(2);
		if(isset($USER) && $USER instanceof \CUser) {
			$result = $USER->GetUserGroupArray();
			Main\Type\Collection::normalizeArrayValuesByInt($result, true);
		}
		return $result;
	}

	/**
	 * All iblock/section/element/offer initializations starts here.
	 * If have no errors - result showed in $arResult.
	 */
	protected function processResultData()
	{
		$this->checkIblock();

		if($this->hasErrors()) {
			return $this->processErrors();
		}

		$this->initCurrencyConvert();
		$this->initCatalogInfo();
		$this->initPrices();
		$this->initElementList();

		if(!$this->hasErrors()) {
			$this->makeElementLinks();
			$this->clearItems();
			$this->initCatalogDiscountCache();
			$this->processProducts();
			$this->processOffers();
			$this->makeOutputResult();
			$this->clearItems();
		}
	}

	/**
	 * Set component data from storage to $arResult.
	 */
	protected function makeOutputResult()
	{
		$this->arResult = array_merge($this->arResult, (array)$this->storage['URLS']);

		$this->arResult['CONVERT_CURRENCY'] = $this->storage['CONVERT_CURRENCY'];
		$this->arResult['CATALOGS']         = $this->storage['CATALOGS'];
		$this->arResult['MODULES']          = $this->storage['MODULES'];
		$this->arResult['PRICES_ALLOW']     = $this->storage['PRICES_ALLOW'];

		$this->arResult['PRICES']     = $this->storage['PRICES'];
		$this->arResult['ELEMENTS']   = array_keys($this->elementLinks);
		$this->arResult['CURRENCIES'] = $this->getTemplateCurrencies();
		$this->arResult['ITEMS']      = $this->elements;

		if($this->arParams['DEBUG_DATA']) {
			$ttfile = dirname(__FILE__) . '/1_params.php';
			file_put_contents($ttfile, "<pre>" . print_r($this->arParams, 1) . "</pre>\n");

			$ttfile = dirname(__FILE__) . '/1_profile.php';
			file_put_contents($ttfile, "<pre>" . print_r($this->profile, 1) . "</pre>\n");

			$ttfile = dirname(__FILE__) . '/1_storage.php';
			file_put_contents($ttfile, "<pre>" . print_r($this->storage, 1) . "</pre>\n");

			$ttfile = dirname(__FILE__) . '/1_result.php';
			file_put_contents($ttfile, "<pre>" . print_r($this->elements, 1) . "</pre>\n");
		}
	}

	public function execute()
	{
		if($this->hasErrors()) {
			return $this->processErrors();
		}

		//TODO: View action
		$this->iblockProducts[ $this->arParams['IBLOCK_ID'] ] = $this->arParams['ELEMENT_ID'];
		$this->processResultData();
	}

	public function getResult()
	{
		return $this->arResult;
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// INIT STORAGE
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Fill discount cache before price calculation.
	 *
	 * @return void
	 */
	protected function initCatalogDiscountCache()
	{
		if($this->useCatalog && $this->useDiscountCache && !empty($this->elementLinks)) {
			foreach($this->iblockProducts as $iblock => $products) {
				if($this->storage['USE_SALE_DISCOUNTS']) {
					Catalog\Discount\DiscountManager::preloadPriceData($products, $this->storage['PRICES_ALLOW']);
					Catalog\Discount\DiscountManager::preloadProductDataToExtendOrder($products, $this->getUserGroups());
				}
				else {
					\CCatalogDiscount::SetProductSectionsCache($products);
					\CCatalogDiscount::SetDiscountProductCache($products, array('IBLOCK_ID' => $iblock, 'GET_BY_ID' => 'Y'));
				}
			}
		}
	}

	/**
	 * Check the settings for the output price currency.
	 *
	 * @return void
	 */
	protected function initCurrencyConvert()
	{
		$this->storage['CONVERT_CURRENCY'] = array();

		if($this->arParams['CONVERT_CURRENCY'] === 'Y') {
			$correct = false;
			if($this->useCurrency) {
				$correct = Currency\CurrencyManager::isCurrencyExist($this->arParams['CURRENCY_ID']);
			}
			if($correct) {
				$this->storage['CONVERT_CURRENCY'] = array(
					 'CURRENCY_ID' => $this->arParams['CURRENCY_ID'],
				);
			}
			else {
				$this->arParams['CONVERT_CURRENCY'] = 'N';
				$this->arParams['CURRENCY_ID']      = '';
			}
			unset($correct);
		}
	}

	/**
	 * Load used iblocks info to component storage.
	 *
	 * @return void
	 */
	protected function initCatalogInfo()
	{
		$catalogs = array();

		if($this->useCatalog) {
			$this->storage['SHOW_CATALOG_WITH_OFFERS'] = (string)Main\Config\Option::get('catalog', 'show_catalog_tab_with_offers') === 'Y';
			$this->storage['USE_SALE_DISCOUNTS']       = (string)Main\Config\Option::get('sale', 'use_sale_discount_only') === 'Y';

			foreach(array_keys($this->iblockProducts) as $iblockId) {
				$catalog = \CCatalogSku::GetInfoByIBlock($iblockId);
				if(!empty($catalog) && is_array($catalog)) {
					$this->isIblockCatalog = $this->isIblockCatalog || $catalog['CATALOG_TYPE'] != \CCatalogSku::TYPE_PRODUCT;
					$catalogs[ $iblockId ] = $catalog;
				}
			}
		}

		$this->storage['CATALOGS'] = $catalogs;
	}

	/**
	 * Load catalog prices in component storage.
	 *
	 * @return void
	 */
	protected function initPrices()
	{
		// This function returns array with prices description and access rights
		// in case catalog module n/a prices get values from element properties
		$this->storage['PRICES'] = \CIBlockPriceTools::GetCatalogPrices($this->arParams['IBLOCK_ID'], $this->arParams['PRICE_CODE']);
		//$this->storage['PRICES_ALLOW']   = \CIBlockPriceTools::GetAllowCatalogPrices($this->storage['PRICES']);
		$this->storage['PRICES_ALLOW']   = $this->getAllowCatalogPrices($this->storage['PRICES']);
		$this->storage['PRICES_CAN_BUY'] = array();
		$this->storage['PRICES_MAP']     = array();

		foreach($this->storage['PRICES'] as $priceType) {
			$priceType['CAN_VIEW'] = 'Y';
			$priceType['CAN_BUY']  = 'Y';

			$this->storage['PRICES_MAP'][ $priceType['ID'] ] = $priceType['CODE'];
			if($priceType['CAN_BUY'])
				$this->storage['PRICES_CAN_BUY'][] = $priceType['ID'];
		}

		$this->storage['PRICE_TYPES'] = array();
		if($this->useCatalog)
			$this->storage['PRICE_TYPES'] = \CCatalogGroup::GetListArray();

		$this->useDiscountCache = false;
		if($this->useCatalog) {
			if(!empty($this->storage['CATALOGS']) && !empty($this->storage['PRICES_ALLOW']))
				$this->useDiscountCache = true;
		}

		if($this->useCatalog && $this->useDiscountCache) {
			$this->useDiscountCache = \CIBlockPriceTools::SetCatalogDiscountCache(
				 $this->storage['PRICES_ALLOW'],
				 $this->getUserGroups()
			);
		}

		if($this->useCatalog)
			Catalog\Product\Price::loadRoundRules($this->storage['PRICES_ALLOW']);
	}

	/**
	 * @param array $arPriceTypes
	 *
	 * @return array
	 */
	protected function getAllowCatalogPrices($arPriceTypes)
	{
		$arResult = array();
		if(empty($arPriceTypes) || !is_array($arPriceTypes))
			return $arResult;

		foreach($arPriceTypes as $arOnePriceType) {
			//if($arOnePriceType['CAN_VIEW'] || $arOnePriceType['CAN_BUY'])
				//$arResult[] = (int)$arOnePriceType['ID'];

			$arResult[] = (int)$arOnePriceType['ID'];
		}
		unset($arOnePriceType);

		if(!empty($arResult))
			Main\Type\Collection::normalizeArrayValuesByInt($arResult, true);

		return $arResult;
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// PRODUCTS + OFFERS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Load, calculate and fill data (prices, measures, discounts, deprecated fields) for simple products.
	 *
	 * @return void.
	 */
	protected function processProducts()
	{
		$this->initItemsMeasure($this->elements);
		$this->loadMeasures($this->getMeasureIds($this->elements));

		$this->loadMeasureRatios($this->productWithPrices);

		$this->loadPrices($this->productWithPrices);
		$this->calculateItemPrices($this->elements);

		$this->transferItems($this->elements);
	}

	/**
	 * Load, calculate and fill data (prices, measures, discounts, deprecated fields) for offers.
	 * Link offers to products.
	 *
	 * @return void
	 */
	protected function processOffers()
	{
		if($this->useCatalog && !empty($this->iblockProducts)) {
			$offers = array();

			$paramStack = array();

			foreach(array_keys($this->productWithOffers) as $iblock) {
				if(!empty($this->productWithOffers[ $iblock ])) {
					$iblockOffers = $this->getIblockOffers($iblock);

					if(!empty($iblockOffers)) {
						$offersId = array_keys($iblockOffers);
						$this->initItemsMeasure($iblockOffers);
						$this->loadMeasures($this->getMeasureIds($iblockOffers));

						$this->loadMeasureRatios($offersId);

						$this->loadPrices($offersId);
						$this->calculateItemPrices($iblockOffers);

						$this->transferItems($iblockOffers);

						$this->modifyOffers($iblockOffers);
						$this->chooseOffer($iblockOffers, $iblock);

						$offers = array_merge($offers, $iblockOffers);
					}
					unset($iblockOffers);
				}
			}
			unset($paramStack);
		}
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// PRODUCTS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function getElementNav()
	{
		return $this->profile['NAV_PARAMS'];
	}

	protected function setBaseSelect()
	{
		/*return array(
			 'ID', 'IBLOCK_ID', 'CODE', 'XML_ID', 'NAME', 'ACTIVE', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO', 'SORT',
			 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE', 'DATE_CREATE', 'CREATED_BY', 'TAGS',
			 'TIMESTAMP_X', 'MODIFIED_BY', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE',
		);*/

		$profile = $this->profile;

		$arSelect            = array('ID', 'XML_ID', 'ACTIVE', 'LID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL');
		$this->elementFields = $this->offerFields = $arSelect;

		if($profile['FIELDS']) {
			foreach($profile['FIELDS'] as $key => $arField) {
				if($arField['TYPE']) {
					foreach($arField['TYPE'] as $typeKey => $typeID) {
						$typeValue = $arField['VALUE'][ $typeKey ];

						//NONE|FIELD|PROPERTY|OFFER_FIELD|OFFER_PROPERTY|CATALOG|PRICE|CURRENCY|BOOLEAN
						if(in_array($typeID, array('NONE', 'PRICE', 'CURRENCY', 'BOOLEAN')))
							continue;

						if($typeID == 'FIELD') {
							$this->elementFields[] = $typeValue;
							continue;
						}
						if($typeID == 'PROPERTY') {
							$this->elementPropCodes[] = $typeValue;
							//$val = 'PROPERTY_' . $val;
							continue;
						}
						if($typeID == 'OFFER_FIELD') {
							$this->offerFields[] = $typeValue;
							continue;
						}
						if($typeID == 'OFFER_PROPERTY') {
							$this->offerPropCodes[] = $typeValue;
							continue;
						}

						if($typeID == 'PRODUCT') {
							$typeValue = 'CATALOG_' . $typeValue;
						}

						$this->elementFields[] = $typeValue;
						$this->offerFields[]   = $typeValue;
					}
				}
			}
		}

		return array_unique($arSelect);
	}

	protected function getElementFields()
	{
		return array_unique($this->elementFields);
	}

	protected function getElementProps()
	{
		return array_unique($this->elementPropCodes);
	}

	protected function getElementFilter()
	{
		$profile = $this->profile;

		$filter1 = (array)$profile['ELEMENTS_FILTER'];

		$filter2 = array(
			 'IBLOCK_ID' => $profile['IBLOCK_ID'],
			 ">ID"       => $profile['LAST_ELEMENT_ID'],
		);

		if($profile['SECTION_ID']) {
			$filter2['SECTION_ID']          = $profile['SECTION_ID'];
			$filter2['INCLUDE_SUBSECTIONS'] = 'Y';
		}
		else {
			//$arFilter3['!SECTION_ID'] = false;
		}

		$filter3 = $this->elementFilter;

		$arFilter = array_merge($filter1, $filter2, $filter3);

		if($this->useCatalog && is_array($profile['ELEMENTS_CONDITION']) && $profile['ELEMENTS_CONDITION']) {
			$condition = new Condition();
			$filter4   = (array)$condition->parseCondition($profile['ELEMENTS_CONDITION'], $arFilter);

			if($filter4) {
				//$arFilter = array_merge($arFilter, $filter4);
				$arFilter[] = $filter4;
			}

			unset($filter4, $condition);
		}
		unset($filter1, $filter2, $filter3);

		if($this->arParams['DEBUG_FILTER']) {
			$ttfile = dirname(__FILE__) . '/1_elFilter.php';
			file_put_contents($ttfile, "<pre>" . print_r($arFilter, 1) . "</pre>\n");
		}

		return $arFilter;
	}

	protected function getElementSort()
	{
		return array('ID' => 'ASC');
	}

	protected function getIblockElements($elementIterator)
	{
		$iblockElements = array();

		if(!empty($elementIterator)) {

			/** @var \CIBlockResult $elementIterator */
			while($element = $elementIterator->GetNext(1, 0)) {

				$this->processElement($element);
				$iblockElements[ $element['ID'] ] = $element;

				$this->arResult['LAST_ELEMENT_ID'] = $element['ID'];
			}
		}

		return $iblockElements;
	}

	/**
	 * Process element data to set in $arResult.
	 *
	 * @param array &$element
	 *
	 * @return void
	 */
	protected function processElement(array &$element)
	{
		$this->modifyElementCommonData($element);
		$this->modifyElementPrices($element);
	}

	/**
	 * Fill various common fields for element.
	 *
	 * @param array &$element Element data.
	 *
	 * @return void
	 */
	protected function modifyElementCommonData(array &$element)
	{
		$element['ID']        = (int)$element['ID'];
		$element['IBLOCK_ID'] = (int)$element['IBLOCK_ID'];

		$ipropValues                 = new Iblock\InheritedProperty\ElementValues($element['IBLOCK_ID'], $element['ID']);
		$element['IPROPERTY_VALUES'] = $ipropValues->getValues();

		$picture = array();
		if($element['PREVIEW_PICTURE']) {
			$picture[] = 'PREVIEW_PICTURE';
		}
		if($element['DETAIL_PICTURE']) {
			$picture[] = 'DETAIL_PICTURE';
		}
		if($picture)
			Iblock\Component\Tools::getFieldImageData($element, $picture, Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT, 'IPROPERTY_VALUES');

		/* it is not the final version */
		$element['PRODUCT'] = array(
			 'TYPE'           => null,
			 'AVAILABLE'      => null,
			 'MEASURE'        => null,
			 'VAT_ID'         => null,
			 'VAT_RATE'       => null,
			 'VAT_INCLUDED'   => null,
			 'QUANTITY'       => null,
			 'QUANTITY_TRACE' => null,
			 'CAN_BUY_ZERO'   => null,
			 'SUBSCRIPTION'   => null,
			 'WEIGHT'         => null,
			 'LENGTH'         => null,
			 'WIDTH'          => null,
			 'HEIGHT'         => null,
			 'DIMENSIONS'     => null,
			 'GROUP_ID'       => null,
		);

		if(isset($element['CATALOG_TYPE'])) {
			$element['CATALOG_TYPE']    = (int)$element['CATALOG_TYPE']; // this key will be deprecated
			$element['PRODUCT']['TYPE'] = $element['CATALOG_TYPE'];
		}
		if(isset($element['CATALOG_MEASURE'])) {
			$element['CATALOG_MEASURE']    = (int)$element['CATALOG_MEASURE']; // this key will be deprecated
			$element['PRODUCT']['MEASURE'] = $element['CATALOG_MEASURE'];
		}

		/*
		 * this keys will be deprecated
		 * CATALOG_*
		 */
		if(isset($element['CATALOG_AVAILABLE']))
			$element['PRODUCT']['AVAILABLE'] = $element['CATALOG_AVAILABLE'];
		if(isset($element['CATALOG_VAT']))
			$element['PRODUCT']['VAT_RATE'] = $element['CATALOG_VAT'];
		if(isset($element['CATALOG_VAT_INCLUDED']))
			$element['PRODUCT']['VAT_INCLUDED'] = $element['CATALOG_VAT_INCLUDED'];
		if(isset($element['CATALOG_QUANTITY']))
			$element['PRODUCT']['QUANTITY'] = $element['CATALOG_QUANTITY'];
		if(isset($element['CATALOG_QUANTITY_TRACE']))
			$element['PRODUCT']['QUANTITY_TRACE'] = $element['CATALOG_QUANTITY_TRACE'];
		if(isset($element['CATALOG_CAN_BUY_ZERO']))
			$element['PRODUCT']['CAN_BUY_ZERO'] = $element['CATALOG_CAN_BUY_ZERO'];
		if(isset($element['CATALOG_SUBSCRIPTION']))
			$element['PRODUCT']['SUBSCRIPTION'] = $element['CATALOG_SUBSCRIPTION'];
		if(isset($element['CATALOG_WEIGHT']))
			$element['PRODUCT']['WEIGHT'] = $element['CATALOG_WEIGHT'];
		if(isset($element['CATALOG_LENGTH']))
			$element['PRODUCT']['LENGTH'] = $element['CATALOG_LENGTH'];
		if(isset($element['CATALOG_WIDTH']))
			$element['PRODUCT']['WIDTH'] = $element['CATALOG_WIDTH'];
		if(isset($element['CATALOG_HEIGHT']))
			$element['PRODUCT']['HEIGHT'] = $element['CATALOG_HEIGHT'];
		if(isset($element['ID']))
			$element['PRODUCT']['GROUP_ID'] = $element['ID'];

		$this->setDimensions($element['PRODUCT']);
		/* it is not the final version - end*/


		$element['PROPERTIES']              = array();
		$element['DISPLAY_PROPERTIES']      = array();
		$element['PRODUCT_PROPERTIES']      = array();
		$element['PRODUCT_PROPERTIES_FILL'] = array();
		$element['OFFERS']                  = array();
		$element['OFFER_ID_SELECTED']       = 0;

		if(!empty($this->storage['CATALOGS'][ $element['IBLOCK_ID'] ]))
			$element['CHECK_QUANTITY'] = $this->isNeedCheckQuantity($element['PRODUCT']);
	}

	/**
	 * Initialize and data process of iblock elements.
	 *
	 * @return void
	 */
	protected function initElementList()
	{
		$this->storage['CURRENCY_LIST']   = array();
		$this->storage['DEFAULT_MEASURE'] = $this->getDefaultMeasure();

		//$this->initPricesQuery();

		foreach($this->iblockProducts as $iblock => $products) {
			$elementIterator = $this->getElementList($iblock, $products);
			$iblockElements  = $this->getIblockElements($elementIterator);

			//$cnt = $elementIterator->SelectedRowsCount();

			if(!empty($iblockElements) && !$this->hasErrors()) {
				$iblockParams = $this->storage['IBLOCK_PARAMS'][ $iblock ];
				$propertyCode = $iblockParams['PROPERTY_CODE'];

				$oProperty = new Property(array(
					 'IBLOCK_ID'     => $iblock,
					 'PROPERTY_CODE' => $propertyCode
				));
				$oProperty->getPropertyList($iblockElements);

				$this->elements                  = array_merge($this->elements, array_values($iblockElements));
				$this->iblockProducts[ $iblock ] = array_keys($iblockElements);
			}
			unset($elementIterator, $iblockElements, $iblockParams, $propertyCode);
		}
	}

	/**
	 * Return \CIBlockResult iterator for current iblock ID.
	 *
	 * @param int       $iblockId
	 * @param array|int $products
	 *
	 * @return \CIBlockResult|int
	 */
	protected function getElementList($iblockId, $products)
	{
		$selectFields = $this->arParams['FIELD_CODE'];
		$arFilter     = $this->getElementFilter();

		if(!empty($products)) {
			$arFilter['ID'] = $products;
		}

		if($this->isIblockCatalog || $this->offerIblockExist($iblockId)) {
			$selectFields[] = 'CATALOG_TYPE';
		}

		$elementIterator = \CIBlockElement::GetList(
			 $this->getElementSort(),
			 $arFilter,
			 false,
			 $this->getElementNav(),
			 $selectFields
		);

		//Подсчет всего результата для прогресса
		if(!$this->arResult['ALL_ELEMENTS_COUNT']) {
			$countFilter = $arFilter;
			unset($countFilter['>ID']);
			$cnt = \CIBlockElement::GetList(array(), $countFilter, array(), false, array('ID'));

			$this->arResult['ALL_ELEMENTS_COUNT'] = $cnt;
		}


		if($this->arParams['DETAIL_URL'])
			$elementIterator->SetUrlTemplates($this->arParams['DETAIL_URL']);

		unset($selectFields, $arFilter, $countFilter);

		return $elementIterator;
	}

	protected function setDimensions(&$arFields)
	{
		$format = $this->profile['DIMENSIONS'] ? trim($this->profile['DIMENSIONS']) : '#LENGTH#/#WIDTH#/#HEIGHT#';

		$arFields['DIMENSIONS'] = str_replace(
			 array('#LENGTH#', '#WIDTH#', '#HEIGHT#'),
			 array($arFields['LENGTH'], $arFields['WIDTH'], $arFields['HEIGHT']),
			 $format
		);
		unset($format);
	}



	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// OFFERS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function getOfferFields()
	{
		return array_unique($this->offerFields);
	}

	protected function getOfferProps()
	{
		return array_unique($this->offerPropCodes);
	}

	/**
	 * Check offers iblock.
	 *
	 * @param int $iblockId Iblock Id.
	 *
	 * @return bool
	 */
	protected function offerIblockExist($iblockId)
	{
		if(empty($this->storage['CATALOGS'][ $iblockId ]))
			return false;

		$catalog = $this->storage['CATALOGS'][ $iblockId ];

		if(empty($catalog['CATALOG_TYPE']))
			return false;

		return $catalog['CATALOG_TYPE'] == \CCatalogSku::TYPE_FULL || $catalog['CATALOG_TYPE'] == \CCatalogSku::TYPE_PRODUCT;
	}

	/**
	 * Return offers array for current iblock.
	 *
	 * @param $iblockId
	 *
	 * @return array
	 */
	protected function getIblockOffers($iblockId)
	{
		$offers       = array();
		$iblockParams = $this->storage['IBLOCK_PARAMS'][ $iblockId ];

		if(
			 $this->useCatalog
			 && $this->offerIblockExist($iblockId)
			 && !empty($this->productWithOffers[ $iblockId ])
		) {
			$catalog = $this->storage['CATALOGS'][ $iblockId ];

			$productProperty      = 'PROPERTY_' . $catalog['SKU_PROPERTY_ID'];
			$productPropertyValue = $productProperty . '_VALUE';

			$offersFilter = $this->getOffersFilter($catalog['IBLOCK_ID']);

			$offersFilter[ $productProperty ] = $this->productWithOffers[ $iblockId ];

			$offersOrder = $this->getOffersSort();

			$offersSelect                     = $this->getOffersSelect();
			$offersSelect[ $productProperty ] = 1;

			if(!empty($iblockParams['OFFERS_FIELD_CODE'])) {
				foreach($iblockParams['OFFERS_FIELD_CODE'] as $code)
					$offersSelect[ $code ] = 1;
				unset($code);
			}

			$checkFields = array();
			foreach(array_keys($offersOrder) as $code) {

				$code = strtoupper($code);

				$offersSelect[ $code ] = 1;
				if($code == 'ID' || $code == 'CATALOG_AVAILABLE')
					continue;
				$checkFields[] = $code;
			}
			unset($code);

			$offersId    = array();
			$offersCount = array();
			$iterator    = \CIBlockElement::GetList(
				 $offersOrder,
				 $offersFilter,
				 false,
				 false,
				 array_keys($offersSelect)
			);
			while($row = $iterator->GetNext(1, 0)) {
				$row['ID']        = (int)$row['ID'];
				$row['IBLOCK_ID'] = (int)$row['IBLOCK_ID'];
				$productId        = (int)$row[ $productPropertyValue ];

				if($productId <= 0)
					continue;

				$row['SORT_HASH'] = 'ID';
				if(!empty($checkFields)) {
					$checkValues = '';
					foreach($checkFields as $code)
						$checkValues .= (isset($row[ $code ]) ? $row[ $code ] : '') . '|';
					unset($code);

					if($checkValues != '')
						$row['SORT_HASH'] = md5($checkValues);
					unset($checkValues);
				}
				$row['LINK_ELEMENT_ID']    = $productId;
				$row['PROPERTIES']         = array();
				$row['DISPLAY_PROPERTIES'] = array();


				/* it is not the final version */
				//CCatalogProduct::GetByID
				$row['PRODUCT'] = array(
					 'TYPE'           => null,
					 'AVAILABLE'      => null,
					 'MEASURE'        => null,
					 'VAT_ID'         => null,
					 'VAT_RATE'       => null,
					 'VAT_INCLUDED'   => null,
					 'QUANTITY'       => null,
					 'QUANTITY_TRACE' => null,
					 'CAN_BUY_ZERO'   => null,
					 'SUBSCRIPTION'   => null,
					 'WEIGHT'         => null,
					 'LENGTH'         => null,
					 'WIDTH'          => null,
					 'HEIGHT'         => null,
					 'DIMENSIONS'     => null,
				);

				if(isset($row['CATALOG_TYPE'])) {
					$row['CATALOG_TYPE']    = (int)$row['CATALOG_TYPE']; // this key will be deprecated
					$row['PRODUCT']['TYPE'] = $row['CATALOG_TYPE'];
				}
				if(isset($row['CATALOG_MEASURE'])) {
					$row['CATALOG_MEASURE']    = (int)$row['CATALOG_MEASURE']; // this key will be deprecated
					$row['PRODUCT']['MEASURE'] = $row['CATALOG_MEASURE'];
				}

				/*
				 * this keys will be deprecated
				 * CATALOG_*
				 */
				if(isset($row['CATALOG_AVAILABLE']))
					$row['PRODUCT']['AVAILABLE'] = $row['CATALOG_AVAILABLE'];
				if(isset($row['CATALOG_VAT']))
					$row['PRODUCT']['VAT_RATE'] = $row['CATALOG_VAT'];
				if(isset($row['CATALOG_VAT_INCLUDED']))
					$row['PRODUCT']['VAT_INCLUDED'] = $row['CATALOG_VAT_INCLUDED'];
				if(isset($row['CATALOG_QUANTITY']))
					$row['PRODUCT']['QUANTITY'] = $row['CATALOG_QUANTITY'];
				if(isset($row['CATALOG_QUANTITY_TRACE']))
					$row['PRODUCT']['QUANTITY_TRACE'] = $row['CATALOG_QUANTITY_TRACE'];
				if(isset($row['CATALOG_CAN_BUY_ZERO']))
					$row['PRODUCT']['CAN_BUY_ZERO'] = $row['CATALOG_CAN_BUY_ZERO'];
				if(isset($row['CATALOG_SUBSCRIPTION']))
					$row['PRODUCT']['SUBSCRIPTION'] = $row['CATALOG_SUBSCRIPTION'];
				if(isset($row['CATALOG_WEIGHT']))
					$row['PRODUCT']['WEIGHT'] = $row['CATALOG_WEIGHT'];
				if(isset($row['CATALOG_LENGTH']))
					$row['PRODUCT']['LENGTH'] = $row['CATALOG_LENGTH'];
				if(isset($row['CATALOG_WIDTH']))
					$row['PRODUCT']['WIDTH'] = $row['CATALOG_WIDTH'];
				if(isset($row['CATALOG_HEIGHT']))
					$row['PRODUCT']['HEIGHT'] = $row['CATALOG_HEIGHT'];
				if(isset($row['LINK_ELEMENT_ID']))
					$row['PRODUCT']['GROUP_ID'] = $row['LINK_ELEMENT_ID'];

				$this->setDimensions($row['PRODUCT']);
				/* it is not the final version - end*/

				if($row['PRODUCT']['TYPE'] == Catalog\ProductTable::TYPE_OFFER)
					$this->calculatePrices[ $row['ID'] ] = $row['ID'];

				$row['ITEM_PRICE_MODE']              = null;
				$row['ITEM_PRICES']                  = array();
				$row['ITEM_QUANTITY_RANGES']         = array();
				$row['ITEM_MEASURE_RATIOS']          = array();
				$row['ITEM_MEASURE']                 = array();
				$row['ITEM_MEASURE_RATIO_SELECTED']  = null;
				$row['ITEM_QUANTITY_RANGE_SELECTED'] = null;
				$row['ITEM_PRICE_SELECTED']          = null;
				$row['CHECK_QUANTITY']               = $this->isNeedCheckQuantity($row['PRODUCT']);

				if($row['PRODUCT']['MEASURE'] > 0) {
					$row['ITEM_MEASURE'] = array(
						 'ID'     => $row['PRODUCT']['MEASURE'],
						 'TITLE'  => '',
						 '~TITLE' => '',
					);
				}
				else {
					$row['ITEM_MEASURE'] = array(
						 'ID'     => null,
						 'TITLE'  => $this->storage['DEFAULT_MEASURE']['SYMBOL_RUS'],
						 '~TITLE' => $this->storage['DEFAULT_MEASURE']['~SYMBOL_RUS'],
					);
				}

				$ipropValues             = new Iblock\InheritedProperty\ElementValues($row['IBLOCK_ID'], $row['ID']);
				$row['IPROPERTY_VALUES'] = $ipropValues->getValues();

				$picture = array();
				if($row['PREVIEW_PICTURE']) {
					$picture[] = 'PREVIEW_PICTURE';
				}
				if($row['DETAIL_PICTURE']) {
					$picture[] = 'DETAIL_PICTURE';
				}
				if($picture)
					Iblock\Component\Tools::getFieldImageData($row, $picture, Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT, 'IPROPERTY_VALUES');


				$offersId[ $row['ID'] ] = $row['ID'];
				$offers[ $row['ID'] ]   = $row;
			}
			unset($row, $iterator);


			if(!empty($offersId)) {

				$propertyCode   = $iblockParams['OFFERS_PROPERTY_CODE'];
				$propertyCode[] = 'CML2_LINK';//!Required for get offer price

				//CML2_LINK - Required for get offer price
				/*$rsSkuProp = \CIBlockProperty::GetByID($catalog['SKU_PROPERTY_ID'], $catalog['IBLOCK_ID']);
				if($arSkuProp = $rsSkuProp->Fetch())
					$propertyCode[] = $arSkuProp['CODE'];*/

				$oProperty = new Property(array(
					 'IBLOCK_ID'     => $catalog['IBLOCK_ID'],
					 'PROPERTY_CODE' => $propertyCode
				));
				$oProperty->getPropertyList($offers);


				if($this->useDiscountCache) {
					if($this->storage['USE_SALE_DISCOUNTS']) {
						Catalog\Discount\DiscountManager::preloadPriceData($offersId, $this->storage['PRICES_ALLOW']);
						Catalog\Discount\DiscountManager::preloadProductDataToExtendOrder($offersId, $this->getUserGroups());
					}
					else {
						\CCatalogDiscount::SetProductSectionsCache($offersId);
						\CCatalogDiscount::SetDiscountProductCache($offersId, array('IBLOCK_ID' => $catalog['IBLOCK_ID'], 'GET_BY_ID' => 'Y'));
					}
				}
			}
			unset($offersId);
		}

		return $offers;
	}

	protected function modifyOffers($offers)
	{
		//$urls = $this->storage['URLS'];

		foreach($offers as &$offer) {
			$elementId = $offer['LINK_ELEMENT_ID'];

			if(!isset($this->elementLinks[ $elementId ]))
				continue;

			$curElement = $this->elementLinks[ $elementId ];

			$offer['CAN_BUY'] = $curElement['ACTIVE'] === 'Y' && $offer['CAN_BUY'];


			//Здесь подставим поля элемента в поля ТП, если в ТП их нет

			if(!$offer['IPROPERTY_VALUES']) {
				$offer['IPROPERTY_VALUES'] = $curElement['IPROPERTY_VALUES'];
			}
			if(!$offer['PREVIEW_PICTURE']) {
				$offer['PREVIEW_PICTURE'] = $curElement['PREVIEW_PICTURE'];
			}
			if(!$offer['DETAIL_PICTURE']) {
				$offer['DETAIL_PICTURE'] = $curElement['DETAIL_PICTURE'];
			}
			if(!$offer['PREVIEW_TEXT']) {
				$offer['PREVIEW_TEXT'] = $curElement['PREVIEW_TEXT'];
			}
			if(!$offer['DETAIL_TEXT']) {
				$offer['DETAIL_TEXT'] = $curElement['DETAIL_TEXT'];
			}

			$this->elementLinks[ $elementId ]['OFFERS'][] = $offer;

			unset($elementId, $offer);
		}
	}

	protected function chooseOffer($offers, $iblockId)
	{
		if(empty($offers) || empty($this->storage['CATALOGS'][ $iblockId ]))
			return;

		$uniqueSortHash     = array();
		$filteredOffers     = array();
		$filteredElements   = array();
		$filteredByProperty = $this->getFilteredOffersByProperty($iblockId);

		if(!$this->isMultiIblockMode() && !empty($this->storage['SUB_FILTER'])) {
			$catalog = $this->storage['CATALOGS'][ $iblockId ];

			$this->storage['SUB_FILTER'][ '=PROPERTY_' . $catalog['SKU_PROPERTY_ID'] ] = array_keys($this->elementLinks);

			$filteredOffers = Iblock\Component\Filters::getFilteredOffersByProduct(
				 $catalog['IBLOCK_ID'],
				 $catalog['SKU_PROPERTY_ID'],
				 $this->storage['SUB_FILTER']
			);
			unset($catalog);
		}

		foreach($offers as &$offer) {
			$elementId = $offer['LINK_ELEMENT_ID'];

			if(!isset($this->elementLinks[ $elementId ]))
				continue;

			if(!isset($uniqueSortHash[ $elementId ])) {
				$uniqueSortHash[ $elementId ] = array();
			}

			$uniqueSortHash[ $elementId ][ $offer['SORT_HASH'] ] = true;

			if($this->elementLinks[ $elementId ]['OFFER_ID_SELECTED'] == 0 && $offer['CAN_BUY']) {
				if(isset($filteredOffers[ $elementId ])) {
					if(isset($filteredOffers[ $elementId ][ $offer['ID'] ])) {
						$this->elementLinks[ $elementId ]['OFFER_ID_SELECTED'] = $offer['ID'];
						$filteredElements[ $elementId ]                        = true;
					}
				}
				elseif(isset($filteredByProperty[ $elementId ])) {
					if(isset($filteredByProperty[ $elementId ][ $offer['ID'] ])) {
						$this->elementLinks[ $elementId ]['OFFER_ID_SELECTED'] = $offer['ID'];
						$filteredElements[ $elementId ]                        = true;
					}
				}
				else {
					$this->elementLinks[ $elementId ]['OFFER_ID_SELECTED'] = $offer['ID'];
				}
			}
			unset($elementId);
		}

		if(!empty($filteredOffers)) {
			$this->arResult['FILTERED_OFFERS_ID'] = array();
		}

		foreach($this->elementLinks as &$element) {
			if(isset($filteredOffers[ $element['ID'] ])) {
				$this->arResult['FILTERED_OFFERS_ID'][ $element['ID'] ] = $filteredOffers[ $element['ID'] ];
			}

			if($element['OFFER_ID_SELECTED'] == 0 || isset($filteredElements[ $element['ID'] ]))
				continue;

			if(count($uniqueSortHash[ $element['ID'] ]) < 2) {
				$element['OFFER_ID_SELECTED'] = 0;
			}
		}
	}

	protected function getOffersFilter($iblockId)
	{
		$profile = $this->profile;

		$filter1 = (array)$profile['OFFERS_FILTER'];

		$offersFilter = array(
			 'IBLOCK_ID' => $iblockId,
			 //'ACTIVE'            => 'Y',
			 //'ACTIVE_DATE'       => 'Y',
			 //'CHECK_PERMISSIONS' => 'N',
		);

		$offersFilter = array_merge($offersFilter, $filter1);

		if($this->arParams['HIDE_NOT_AVAILABLE_OFFERS'] === 'Y') {
			$offersFilter['CATALOG_AVAILABLE'] = 'Y';
		}
		elseif($this->arParams['HIDE_NOT_AVAILABLE_OFFERS'] === 'L') {
			$offersFilter['CUSTOM_FILTER'] = array(
				 'LOGIC'             => 'OR',
				 'CATALOG_AVAILABLE' => 'Y',
				 'CATALOG_SUBSCRIBE' => 'Y',
			);
		}

		if(!$this->arParams['USE_PRICE_COUNT']) {
			$offersFilter['SHOW_PRICE_COUNT'] = $this->arParams['SHOW_PRICE_COUNT'];
		}

		if($this->useCatalog && is_array($profile['OFFERS_CONDITION']) && $profile['OFFERS_CONDITION']) {
			$condition = new Condition();
			$filter4   = $condition->parseCondition($profile['OFFERS_CONDITION'], $offersFilter);

			if($filter4) {
				$offersFilter[] = $filter4;
			}
			unset($filter4, $condition);
		}

		if($this->arParams['DEBUG_FILTER']) {
			$ttfile = dirname(__FILE__) . '/1_tpFilter.php';
			file_put_contents($ttfile, "<pre>" . print_r($offersFilter, 1) . "</pre>\n");
		}

		return $offersFilter;
	}

	/**
	 * Return offers sort fields to execute.
	 *
	 * @return array
	 */
	protected function getOffersSort()
	{
		/*$offersOrder = array(
			 $this->arParams['OFFERS_SORT_FIELD']  => $this->arParams['OFFERS_SORT_ORDER'],
			 $this->arParams['OFFERS_SORT_FIELD2'] => $this->arParams['OFFERS_SORT_ORDER2'],
		);*/
		//if(!isset($offersOrder['ID']))
		$offersOrder['ID'] = 'ASC';

		return $offersOrder;
	}

	protected function getOffersSelect()
	{
		$arSelect = array(
			 'ID'              => 1,
			 'IBLOCK_ID'       => 1,
			 'XML_ID'          => 1,
			 'NAME'            => 1,
			 'LID'             => 1,
			 'ACTIVE'          => 1,
			 'DETAIL_PAGE_URL' => 1,
			 'CATALOG_TYPE'    => 1,
		);

		return $arSelect;
	}

	protected function getFilteredOffersByProperty($iblockId)
	{
		$offers = array();
		if(empty($this->storage['CATALOGS'][ $iblockId ]))
			return $offers;

		if(!$this->isMultiIblockMode() && $this->arParams['CUSTOM_FILTER']) {
			$filter = $this->getOffersPropFilter((array)$this->arParams['CUSTOM_FILTER']);
			if(!empty($filter)) {
				$catalog = $this->storage['CATALOGS'][ $iblockId ];
				$offers  = Iblock\Component\Filters::getFilteredOffersByProduct(
					 $catalog['IBLOCK_ID'],
					 $catalog['SKU_PROPERTY_ID'],
					 array(
							'=PROPERTY_' . $catalog['SKU_PROPERTY_ID'] => array_keys($this->elementLinks),
							$filter,
					 )
				);
			}
		}

		return $offers;
	}

	protected function getOffersPropFilter(array $level)
	{
		$filter     = array();
		$checkLogic = true;

		if(!empty($level)) {
			foreach($level as $prop) {
				if(is_array($prop)) {
					$filter[] = $this->getOffersPropFilter($prop);
				}
				elseif($prop instanceOf \CIBlockElement) {
					$checkLogic = false;
					$filter     = $prop->arFilter;
				}
			}

			if($checkLogic && is_array($filter) && count($filter) > 1) {
				$filter['LOGIC'] = $level['LOGIC'];
			}
		}

		return $filter;
	}



	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// MEASURE
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Return default measure.
	 *
	 * @return array|null
	 */
	protected function getDefaultMeasure()
	{
		$defaultMeasure = array();

		if($this->useCatalog) {
			$defaultMeasure = \CCatalogMeasure::getDefaultMeasure(true, true);
		}

		return $defaultMeasure;
	}

	/**
	 * Init measure for items.
	 *
	 * @param array &$items Items list.
	 *
	 * @return void
	 */
	protected function initItemsMeasure(array &$items)
	{
		if(empty($items))
			return;

		foreach(array_keys($items) as $index) {
			if(!isset($items[ $index ]['PRODUCT']['MEASURE']))
				continue;

			if($items[ $index ]['PRODUCT']['MEASURE'] > 0) {
				$items[ $index ]['ITEM_MEASURE'] = array(
					 'ID'     => $items[ $index ]['PRODUCT']['MEASURE'],
					 'TITLE'  => '',
					 '~TITLE' => '',
				);
			}
			else {
				$items[ $index ]['ITEM_MEASURE'] = array(
					 'ID'     => null,
					 'TITLE'  => $this->storage['DEFAULT_MEASURE']['SYMBOL_RUS'],
					 '~TITLE' => $this->storage['DEFAULT_MEASURE']['~SYMBOL_RUS'],
				);
			}
		}
		unset($index);
	}

	/**
	 * Load measures data.
	 *
	 * @param array $measureIds
	 *
	 * @return void
	 */
	protected function loadMeasures(array $measureIds)
	{
		if(empty($measureIds))
			return;
		Main\Type\Collection::normalizeArrayValuesByInt($measureIds, true);
		if(empty($measureIds))
			return;

		$measureIterator = \CCatalogMeasure::getList(
			 array(),
			 array('@ID' => $measureIds),
			 false,
			 false,
			 array('ID', 'SYMBOL_RUS')
		);
		while($measure = $measureIterator->GetNext()) {
			$measure['ID']     = (int)$measure['ID'];
			$measure['TITLE']  = $measure['SYMBOL_RUS'];
			$measure['~TITLE'] = $measure['~SYMBOL_RUS'];
			unset($measure['SYMBOL_RUS'], $measure['~SYMBOL_RUS']);
			$this->measures[ $measure['ID'] ] = $measure;
		}
		unset($measure, $measureIterator);
	}

	/**
	 * Return measure ids for items.
	 *
	 * @param array $items Items data.
	 *
	 * @return array
	 */
	protected function getMeasureIds(array $items)
	{
		$result = array();

		if(!empty($items)) {
			foreach(array_keys($items) as $itemId) {
				if(!isset($items[ $itemId ]['ITEM_MEASURE']))
					continue;
				$measureId = (int)$items[ $itemId ]['ITEM_MEASURE']['ID'];
				if($measureId > 0)
					$result[ $measureId ] = $measureId;
				unset($measureId);
			}
			unset($itemId);
		}

		return $result;
	}

	/**
	 * Load measure ratios for items.
	 *
	 * @param array $itemIds Items id list.
	 *
	 * @return void
	 */
	protected function loadMeasureRatios(array $itemIds)
	{
		if(empty($itemIds))
			return;
		Main\Type\Collection::normalizeArrayValuesByInt($itemIds, true);
		if(empty($itemIds))
			return;
		$emptyRatioIds = array_fill_keys($itemIds, true);

		$iterator = Catalog\MeasureRatioTable::getList(array(
			 'select' => array('ID', 'RATIO', 'IS_DEFAULT', 'PRODUCT_ID'),
			 'filter' => array('@PRODUCT_ID' => $itemIds),
			 'order'  => array('PRODUCT_ID' => 'ASC')// not add 'RATIO' => 'ASC' - result will be resorted after load prices
		));
		while($row = $iterator->fetch()) {
			$ratio = ((float)$row['RATIO'] > (int)$row['RATIO'] ? (float)$row['RATIO'] : (int)$row['RATIO']);
			if($ratio > CATALOG_VALUE_EPSILON) {
				$row['RATIO'] = $ratio;
				$row['ID']    = (int)$row['ID'];
				$id           = (int)$row['PRODUCT_ID'];
				if(!isset($this->ratios[ $id ]))
					$this->ratios[ $id ] = array();
				$this->ratios[ $id ][ $row['ID'] ] = $row;
				unset($emptyRatioIds[ $id ]);
				unset($id);
			}
			unset($ratio);
		}
		unset($row, $iterator);
		if(!empty($emptyRatioIds)) {
			$emptyRatio = $this->getEmptyRatio();
			foreach(array_keys($emptyRatioIds) as $id) {
				$this->ratios[ $id ] = array(
					 $emptyRatio['ID'] => $emptyRatio,
				);
			}
			unset($id, $emptyRatio);
		}
		unset($emptyRatioIds);
	}



	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// RATIO
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
	 * Return default empty ratio (unexist in database).
	 *
	 * @return array
	 */
	protected function getEmptyRatio()
	{
		return array(
			 'ID'         => 0,
			 'RATIO'      => 1,
			 'IS_DEFAULT' => 'Y',
		);
	}

	protected function searchItemSelectedRatioId($id)
	{
		if(!isset($this->ratios[ $id ]))
			return null;

		$minimal      = null;
		$minimalRatio = null;
		$result       = null;
		foreach($this->ratios[ $id ] as $ratio) {
			if($minimalRatio === null || $minimalRatio > $ratio['RATIO']) {
				$minimalRatio = $ratio['RATIO'];
				$minimal      = $ratio['ID'];
			}
			if($ratio['IS_DEFAULT'] === 'Y') {
				$result = $ratio['ID'];
				break;
			}
		}
		unset($ratio);
		return ($result === null ? $minimal : $result);
	}

	protected function compactItemRatios($id)
	{
		$ratioId = $this->searchItemSelectedRatioId($id);
		if($ratioId === null)
			return;
		$this->ratios[ $id ] = array(
			 $ratioId => $this->ratios[ $id ][ $ratioId ],
		);
	}



	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// QUANTITY
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Return true, if enable quantity trace and disable make out-of-stock items available for purchase.
	 *
	 * @param array $product Product data.
	 *
	 * @return bool
	 */
	protected function isNeedCheckQuantity(array $product)
	{
		return (
			 $product['QUANTITY_TRACE'] === Catalog\ProductTable::STATUS_YES
			 && $product['CAN_BUY_ZERO'] === Catalog\ProductTable::STATUS_NO
		);
	}

	protected function searchItemSelectedQuantityRangeHash($id)
	{
		if(empty($this->quantityRanges[ $id ]))
			return null;
		foreach($this->quantityRanges[ $id ] as $range) {
			if($this->checkQuantityRange($range))
				return $range['HASH'];
		}
		reset($this->quantityRanges[ $id ]);
		$firsrRange = current($this->quantityRanges[ $id ]);
		return $firsrRange['HASH'];
	}

	/**
	 * Check quantity range for emulate CATALOG_SHOP_QUANTITY_* filter.
	 * Strict use only for catalog.element, .section, .top, etc in compatible mode.
	 *
	 * @param array $row Price row from database.
	 *
	 * @return bool
	 */
	protected function checkQuantityRange(array $row)
	{
		return (
			 ($row['QUANTITY_FROM'] === null || $row['QUANTITY_FROM'] <= $this->arParams['SHOW_PRICE_COUNT'])
			 && ($row['QUANTITY_TO'] === null || $row['QUANTITY_TO'] >= $this->arParams['SHOW_PRICE_COUNT'])
		);
	}

	protected function getQuantityRangeHash(array $range)
	{
		return ($range['QUANTITY_FROM'] === null ? 'ZERO' : $range['QUANTITY_FROM']) .
			 '-' . ($range['QUANTITY_TO'] === null ? 'INF' : $range['QUANTITY_TO']);
	}

	protected function getFullQuantityRange()
	{
		return array(
			 'HASH'          => $this->getQuantityRangeHash(array('QUANTITY_FROM' => null, 'QUANTITY_TO' => null)),
			 'QUANTITY_FROM' => null,
			 'QUANTITY_TO'   => null,
			 'SORT_FROM'     => 0,
			 'SORT_TO'       => INF,
		);
	}




	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// PROPERTIES
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// PRICE
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Process element prices.
	 *
	 * @param array &$element Item data.
	 *
	 * @return void
	 */
	protected
	function modifyElementPrices(&$element)
	{
		$id       = $element['ID'];
		$iblockId = $element['IBLOCK_ID'];
		$catalog  = !empty($this->storage['CATALOGS'][ $element['IBLOCK_ID'] ])
			 ? $this->storage['CATALOGS'][ $element['IBLOCK_ID'] ]
			 : array();

		$element['ITEM_PRICE_MODE']              = null;
		$element['ITEM_PRICES']                  = array();
		$element['ITEM_QUANTITY_RANGES']         = array();
		$element['ITEM_MEASURE_RATIOS']          = array();
		$element['ITEM_MEASURE']                 = array();
		$element['ITEM_MEASURE_RATIO_SELECTED']  = null;
		$element['ITEM_QUANTITY_RANGE_SELECTED'] = null;
		$element['ITEM_PRICE_SELECTED']          = null;

		if(!empty($catalog)) {
			if(!isset($this->productWithOffers[ $iblockId ]))
				$this->productWithOffers[ $iblockId ] = array();

			if($element['PRODUCT']['TYPE'] == Catalog\ProductTable::TYPE_SKU) {
				$this->productWithOffers[ $iblockId ][ $id ] = $id;
			}

			if(in_array(
				 $element['PRODUCT']['TYPE'],
				 array(
						Catalog\ProductTable::TYPE_PRODUCT,
						Catalog\ProductTable::TYPE_SET,
						Catalog\ProductTable::TYPE_OFFER,
				 )
			)) {
				$this->productWithPrices[ $id ] = $id;
				$this->calculatePrices[ $id ]   = $id;
			}

			if(isset($this->productWithPrices[ $id ])) {
				if($element['PRODUCT']['MEASURE'] > 0) {
					$element['ITEM_MEASURE'] = array(
						 'ID'     => $element['PRODUCT']['MEASURE'],
						 'TITLE'  => '',
						 '~TITLE' => '',
					);
				}
				else {
					$element['ITEM_MEASURE'] = array(
						 'ID'     => null,
						 'TITLE'  => $this->storage['DEFAULT_MEASURE']['SYMBOL_RUS'],
						 '~TITLE' => $this->storage['DEFAULT_MEASURE']['~SYMBOL_RUS'],
					);
				}
			}
		}
		else {
			$element['PRICES'] = \CIBlockPriceTools::GetItemPrices(
				 $element['IBLOCK_ID'],
				 $this->storage['PRICES'],
				 $element,
				 $this->arParams['PRICE_VAT_INCLUDE'],
				 $this->storage['CONVERT_CURRENCY']
			);
			if(!empty($element['PRICES'])) {
				$element['MIN_PRICE'] = \CIBlockPriceTools::getMinPriceFromList($element['PRICES']);
			}

			$element['CAN_BUY'] = \CIBlockPriceTools::CanBuy($element['IBLOCK_ID'], $this->storage['PRICES'], $element);
		}
	}

	//TODO: Проверить работу метода
	protected
	function initPricesQuery()
	{
		$selectFields = $this->arParams['FIELD_CODE'];

		foreach($selectFields as $fieldName) {
			$fieldName = strtoupper($fieldName);
			$priceId   = 0;

			if(strncmp($fieldName, 'CATALOG_PRICE_', 14) === 0) {
				$priceId = (int)substr($fieldName, 14);
			}
			elseif(strncmp($fieldName, 'CATALOG_CURRENCY_', 17) === 0) {
				$priceId = (int)substr($fieldName, 17);
			}
			elseif(strncmp($fieldName, 'CATALOG_PRICE_SCALE_', 20) === 0) {
				$priceId = (int)substr($fieldName, 20);
			}

			if($priceId <= 0)
				continue;

			if(!isset($this->elementFilter[ 'CATALOG_SHOP_QUANTITY_' . $priceId ])) {
				$this->elementFilter[ 'CATALOG_SHOP_QUANTITY_' . $priceId ] = $this->arParams['SHOW_PRICE_COUNT'];
			}
		}
	}

	/**
	 * Load prices for items.
	 *
	 * @param array $itemIds Item ids.
	 *
	 * @return void
	 */
	protected
	function loadPrices(array $itemIds)
	{
		if(empty($itemIds))
			return;

		Main\Type\Collection::normalizeArrayValuesByInt($itemIds, true);

		if(empty($itemIds))
			return;

		if(empty($this->storage['PRICES_ALLOW']))
			return;

		$ratioList    = array_fill_keys($itemIds, array());
		$quantityList = array_fill_keys($itemIds, array());

		$select = array(
			 'ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY',
			 'QUANTITY_FROM', 'QUANTITY_TO',
		);

		$pagedItemIds = array_chunk($itemIds, 500);
		foreach($pagedItemIds as $pageIds) {
			if(empty($pageIds))
				continue;

			$iterator = Catalog\PriceTable::getList(array(
				 'select' => $select,
				 'filter' => array('@PRODUCT_ID' => $pageIds, '@CATALOG_GROUP_ID' => $this->storage['PRICES_ALLOW']),
				 'order'  => array('PRODUCT_ID' => 'ASC', 'CATALOG_GROUP_ID' => 'ASC'),
			));
			while($row = $iterator->fetch()) {
				$id = (int)$row['PRODUCT_ID'];
				unset($row['PRODUCT_ID']);
				if(!isset($this->prices[ $id ])) {
					$this->prices[ $id ] = array(
						 'RATIO'    => array(),
						 'QUANTITY' => array(),
						 'SIMPLE'   => array(),
					);
				}

				if($row['QUANTITY_FROM'] !== null || $row['QUANTITY_TO'] !== null) {
					$hash = $this->getQuantityRangeHash($row);
					if(!isset($quantityList[ $id ][ $hash ])) {
						$quantityList[ $id ][ $hash ] = array(
							 'HASH'          => $hash,
							 'QUANTITY_FROM' => $row['QUANTITY_FROM'],
							 'QUANTITY_TO'   => $row['QUANTITY_TO'],
							 'SORT_FROM'     => (int)$row['QUANTITY_FROM'],
							 'SORT_TO'       => ($row['QUANTITY_TO'] === null ? INF : (int)$row['QUANTITY_TO']),
						);
					}
					if(!isset($this->prices[ $id ]['QUANTITY'][ $hash ])) {
						$this->prices[ $id ]['QUANTITY'][ $hash ] = array();
					}
					$this->prices[ $id ]['QUANTITY'][ $hash ][ $row['CATALOG_GROUP_ID'] ] = $row;
					unset($hash);
				}
				elseif($row['MEASURE_RATIO_ID'] === null && $row['QUANTITY_FROM'] === null && $row['QUANTITY_TO'] === null) {
					$this->prices[ $id ]['SIMPLE'][ $row['CATALOG_GROUP_ID'] ] = $row;
				}
				$this->storage['CURRENCY_LIST'][ $row['CURRENCY'] ] = $row['CURRENCY'];

				unset($id);
			}
			unset($row, $iterator);
		}
		unset($pageIds, $pagedItemIds);

		foreach($itemIds as $id) {
			if(isset($this->prices[ $id ])) {
				foreach($this->prices[ $id ] as $key => $data) {
					if(empty($data))
						unset($this->prices[ $id ][ $key ]);
				}
				unset($key, $data);

				if(count($this->prices[ $id ]) !== 1) {
					unset($this->prices[ $id ]);
				}
				else {
					if(!empty($this->prices[ $id ]['QUANTITY'])) {
						$productQuantity = $quantityList[ $id ];
						Main\Type\Collection::sortByColumn(
							 $productQuantity,
							 array('SORT_FROM' => SORT_ASC, 'SORT_TO' => SORT_ASC),
							 '', null, true
						);
						$this->quantityRanges[ $id ] = $productQuantity;
						unset($productQuantity);

						if(count($this->ratios[ $id ]) > 1)
							$this->compactItemRatios($id);
					}
					if(!empty($this->prices[ $id ]['SIMPLE'])) {
						$range                       = $this->getFullQuantityRange();
						$this->quantityRanges[ $id ] = array(
							 $range['HASH'] => $range,
						);
						unset($range);
						if(count($this->ratios[ $id ]) > 1)
							$this->compactItemRatios($id);
					}
				}
			}
		}
		unset($id);
		unset($quantityList, $ratioList);
	}

	protected
	function calculateItemPrices(array &$items)
	{
		if(empty($items))
			return;

		foreach(array_keys($items) as $index) {
			$id = $items[ $index ]['ID'];

			if(!isset($this->calculatePrices[ $id ]))
				continue;

			if(empty($this->prices[ $id ]))
				continue;

			$productPrices = $this->prices[ $id ];
			$result        = array(
				 'ITEM_PRICE_MODE' => null,
				 'ITEM_PRICES'     => array(),
			);
			if($this->arParams['FILL_ITEM_ALL_PRICES'])
				$result['ITEM_ALL_PRICES'] = array();

			$priceBlockIndex = 0;
			if(!empty($productPrices['QUANTITY'])) {
				$result['ITEM_PRICE_MODE'] = Catalog\ProductTable::PRICE_MODE_QUANTITY;
				$ratio                     = current($this->ratios[ $id ]);
				foreach($this->quantityRanges[ $id ] as $range) {
					$priceBlock = $this->calculatePriceBlock(
						 $items[ $index ],
						 $productPrices['QUANTITY'][ $range['HASH'] ],
						 $ratio['RATIO'],
						 $this->arParams['USE_PRICE_COUNT'] || $this->checkQuantityRange($range)
					);
					if(!empty($priceBlock)) {
						$minimalPrice = ($this->arParams['FILL_ITEM_ALL_PRICES']
							 ? $priceBlock['MINIMAL_PRICE']
							 : $priceBlock
						);
						if($minimalPrice['QUANTITY_FROM'] === null) {
							$minimalPrice['MIN_QUANTITY'] = $ratio['RATIO'];
						}
						else {
							$minimalPrice['MIN_QUANTITY'] = $ratio['RATIO'] * ((int)($minimalPrice['QUANTITY_FROM'] / $ratio['RATIO']));
							if($minimalPrice['MIN_QUANTITY'] < $minimalPrice['QUANTITY_FROM'])
								$minimalPrice['MIN_QUANTITY'] += $ratio['RATIO'];
						}
						$result['ITEM_PRICES'][ $priceBlockIndex ] = $minimalPrice;
						if($this->arParams['FILL_ITEM_ALL_PRICES']) {
							$priceBlock['ALL_PRICES']['MIN_QUANTITY']      = $minimalPrice['MIN_QUANTITY'];
							$result['ITEM_ALL_PRICES'][ $priceBlockIndex ] = $priceBlock['ALL_PRICES'];
						}
						unset($minimalPrice);
						$priceBlockIndex++;
					}
					unset($priceBlock);
				}
				unset($range);
				unset($ratio);
			}
			if(!empty($productPrices['SIMPLE'])) {
				$result['ITEM_PRICE_MODE'] = Catalog\ProductTable::PRICE_MODE_SIMPLE;
				$ratio                     = current($this->ratios[ $id ]);
				$priceBlock                = $this->calculatePriceBlock(
					 $items[ $index ],
					 $productPrices['SIMPLE'],
					 $ratio['RATIO'],
					 true
				);
				if(!empty($priceBlock)) {
					$minimalPrice                              = ($this->arParams['FILL_ITEM_ALL_PRICES']
						 ? $priceBlock['MINIMAL_PRICE']
						 : $priceBlock
					);
					$minimalPrice['MIN_QUANTITY']              = $ratio['RATIO'];
					$result['ITEM_PRICES'][ $priceBlockIndex ] = $minimalPrice;
					if($this->arParams['FILL_ITEM_ALL_PRICES']) {
						$priceBlock['ALL_PRICES']['MIN_QUANTITY']      = $minimalPrice['MIN_QUANTITY'];
						$result['ITEM_ALL_PRICES'][ $priceBlockIndex ] = $priceBlock['ALL_PRICES'];
					}
					unset($minimalPrice);
					$priceBlockIndex++;
				}
				unset($priceBlock);
				unset($ratio);
			}
			$this->prices[ $id ] = $result;

			if(isset($items[ $index ]['ACTIVE']) && $items[ $index ]['ACTIVE'] === 'N') {
				$items[ $index ]['CAN_BUY'] = false;
			}
			else {
				$items[ $index ]['CAN_BUY'] = !empty($result['ITEM_PRICES']) && $items[ $index ]['PRODUCT']['AVAILABLE'] === 'Y';
			}

			unset($priceBlockIndex, $result);
			unset($productPrices);
		}
		unset($index);
	}

	protected
	function transferItems(array &$items)
	{
		if(empty($items))
			return;

		$urls = $this->storage['URLS'];

		foreach(array_keys($items) as $index) {
			$itemId = $items[ $index ]['ID'];
			// measure
			if(!empty($items[ $index ]['ITEM_MEASURE'])) {
				$id = (int)$items[ $index ]['ITEM_MEASURE']['ID'];
				if(isset($this->measures[ $id ])) {
					$items[ $index ]['ITEM_MEASURE']['TITLE']  = $this->measures[ $id ]['TITLE'];
					$items[ $index ]['ITEM_MEASURE']['~TITLE'] = $this->measures[ $id ]['~TITLE'];
				}
				unset($id);
			}
			// prices
			$items[ $index ]['ITEM_MEASURE_RATIOS']          = $this->ratios[ $itemId ];
			$items[ $index ]['ITEM_MEASURE_RATIO_SELECTED']  = $this->searchItemSelectedRatioId($itemId);
			$items[ $index ]['ITEM_QUANTITY_RANGES']         = $this->quantityRanges[ $itemId ];
			$items[ $index ]['ITEM_QUANTITY_RANGE_SELECTED'] = $this->searchItemSelectedQuantityRangeHash($itemId);
			if(!empty($this->prices[ $itemId ])) {
				$items[ $index ] = array_merge($items[ $index ], $this->prices[ $itemId ]);
				if(!empty($items[ $index ]['ITEM_PRICES'])) {
					switch($items[ $index ]['ITEM_PRICE_MODE']) {
						case Catalog\ProductTable::PRICE_MODE_SIMPLE:
							$items[ $index ]['ITEM_PRICE_SELECTED'] = 0;
							break;
						case Catalog\ProductTable::PRICE_MODE_QUANTITY:
							foreach(array_keys($items[ $index ]['ITEM_PRICES']) as $priceIndex) {
								if($items[ $index ]['ITEM_PRICES'][ $priceIndex ]['QUANTITY_HASH'] == $items[ $index ]['ITEM_QUANTITY_RANGE_SELECTED']) {
									$items[ $index ]['ITEM_PRICE_SELECTED'] = $priceIndex;
									break;
								}
							}
							break;
						case Catalog\ProductTable::PRICE_MODE_RATIO:
							foreach(array_keys($items[ $index ]['ITEM_PRICES']) as $priceIndex) {
								if($items[ $index ]['ITEM_PRICES'][ $priceIndex ]['MEASURE_RATIO_ID'] == $items[ $index ]['ITEM_MEASURE_RATIO_SELECTED']) {
									$items[ $index ]['ITEM_PRICE_SELECTED'] = $priceIndex;
									break;
								}
							}
							break;
					}
				}
			}
			unset($itemId);
		}
		unset($index);
		unset($urls);
	}

	/**
	 * Calculate price block (simple price, quantity range, etc).
	 *
	 * @param array     $product      Product data.
	 * @param array     $priceBlock   Prices.
	 * @param int|float $ratio        Measure ratio value.
	 * @param bool      $defaultBlock Save result to old keys (PRICES, PRICE_MATRIX, MIN_PRICE).
	 *
	 * @return array|null
	 */
	protected
	function calculatePriceBlock(array $product, array $priceBlock, $ratio, $defaultBlock = false)
	{
		if(empty($product) || empty($priceBlock))
			return null;

		$userGroups = $this->getUserGroups();

		$baseCurrency = Currency\CurrencyManager::getBaseCurrency();
		/** @var null|array $minimalPrice */
		$minimalPrice = null;
		$fullPrices   = array();

		$currencyConvert = $this->arParams['CONVERT_CURRENCY'] === 'Y';
		$resultCurrency  = ($currencyConvert ? $this->storage['CONVERT_CURRENCY']['CURRENCY_ID'] : null);

		$vatRate             = (float)$product['PRODUCT']['VAT_RATE'];
		$percentVat          = $vatRate * 0.01;
		$percentPriceWithVat = 1 + $percentVat;
		$vatInclude          = $product['PRODUCT']['VAT_INCLUDED'] === 'Y';

		$oldPrices   = array();
		$oldMinPrice = false;
		$oldMatrix   = false;

		foreach($priceBlock as $rawPrice) {
			$priceType = (int)$rawPrice['CATALOG_GROUP_ID'];
			$price     = (float)$rawPrice['PRICE'];
			if(!$vatInclude)
				$price *= $percentPriceWithVat;
			$currency = $rawPrice['CURRENCY'];

			$changeCurrency = $currencyConvert && $currency !== $resultCurrency;
			if($changeCurrency) {
				$price    = \CCurrencyRates::ConvertCurrency($price, $currency, $resultCurrency);
				$currency = $resultCurrency;
			}

			$discounts = array();
			if(\CIBlockPriceTools::isEnabledCalculationDiscounts()) {
				\CCatalogDiscountSave::Disable();
				$discounts = \CCatalogDiscount::GetDiscount(
					 $product['ID'],
					 $product['IBLOCK_ID'],
					 array($priceType),
					 $userGroups,
					 'N',
					 $this->getSiteId(),
					 array()
				);
				\CCatalogDiscountSave::Enable();
			}
			$discountPrice = \CCatalogProduct::CountPriceWithDiscount(
				 $price,
				 $currency,
				 $discounts
			);
			unset($discounts);

			if($discountPrice !== false) {
				$priceWithVat = $price;
				$price        /= $percentPriceWithVat;

				$discountPriceWithVat = $discountPrice;
				$discountPrice        /= $percentPriceWithVat;

				$roundPriceWithVat = Catalog\Product\Price::roundPrice(
					 $priceType,
					 $discountPriceWithVat,
					 $currency
				);
				$roundPrice        = Catalog\Product\Price::roundPrice(
					 $priceType,
					 $discountPrice,
					 $currency
				);

				$priceRow = array(
					 'ID'            => $rawPrice['ID'],
					 'PRICE_TYPE_ID' => $rawPrice['CATALOG_GROUP_ID'],
					 'QUANTITY_FROM' => $rawPrice['QUANTITY_FROM'],
					 'QUANTITY_TO'   => $rawPrice['QUANTITY_TO'],
					 'QUANTITY_HASH' => $this->getQuantityRangeHash($rawPrice),
					 'CURRENCY'      => $currency,
				);
				if($this->arParams['PRICE_VAT_INCLUDE']) {
					$priceRow['BASE_PRICE']    = $priceWithVat;
					$priceRow['UNROUND_PRICE'] = $discountPriceWithVat;
					$priceRow['PRICE']         = $roundPriceWithVat;
				}
				else {
					$priceRow['BASE_PRICE']    = $price;
					$priceRow['UNROUND_PRICE'] = $discountPrice;
					$priceRow['PRICE']         = $roundPrice;
				}
				if($priceRow['BASE_PRICE'] > $priceRow['UNROUND_PRICE']) {
					$priceRow['DISCOUNT'] = $priceRow['BASE_PRICE'] - $priceRow['PRICE'];
					$priceRow['PERCENT']  = roundEx(100 * $priceRow['DISCOUNT'] / $priceRow['BASE_PRICE'], 0);
					if($priceRow['DISCOUNT'] < 0) {
						$priceRow['BASE_PRICE'] = $priceRow['PRICE'];
						$priceRow['DISCOUNT']   = 0;
						$priceRow['PERCENT']    = 0;
					}
				}
				else {
					$priceRow['BASE_PRICE'] = $priceRow['PRICE'];
					$priceRow['DISCOUNT']   = 0;
					$priceRow['PERCENT']    = 0;
				}
				if($this->arParams['PRICE_VAT_SHOW_VALUE'])
					$priceRow['VAT'] = ($vatRate > 0 ? $roundPriceWithVat - $roundPrice : 0);

				$priceRow['PRICE_SCALE'] = \CCurrencyRates::ConvertCurrency(
					 $priceRow['PRICE'],
					 $priceRow['CURRENCY'],
					 $baseCurrency
				);

				if($minimalPrice === null || $minimalPrice['PRICE_SCALE'] > $priceRow['PRICE_SCALE'])
					$minimalPrice = $priceRow;
				if($this->arParams['FILL_ITEM_ALL_PRICES']) {
					$fullPrices[ $priceType ] = array(
						 'ID'            => $priceRow['ID'],
						 'PRICE_TYPE_ID' => $priceRow['PRICE_TYPE_ID'],
						 'CURRENCY'      => $currency,
						 'BASE_PRICE'    => $priceRow['BASE_PRICE'],
						 'UNROUND_PRICE' => $priceRow['UNROUND_PRICE'],
						 'PRICE'         => $priceRow['PRICE'],
						 'DISCOUNT'      => $priceRow['DISCOUNT'],
						 'PERCENT'       => $priceRow['DISCOUNT'],
					);
					if(isset($priceRow['VAT']))
						$fullPrices[ $priceType ]['VAT'] = $priceRow['VAT'];
				}
			}
			unset($priceType);
		}
		unset($price);

		$minimalPriceId = null;
		if(is_array($minimalPrice)) {
			unset($minimalPrice['PRICE_SCALE']);
			$minimalPriceId = $minimalPrice['PRICE_TYPE_ID'];
			$prepareFields  = array(
				 'BASE_PRICE', 'PRICE', 'DISCOUNT',
			);
			if($this->arParams['PRICE_VAT_SHOW_VALUE'])
				$prepareFields[] = 'VAT';

			foreach($prepareFields as $fieldName) {
				$minimalPrice[ 'PRINT_' . $fieldName ]       = \CCurrencyLang::CurrencyFormat(
					 $minimalPrice[ $fieldName ],
					 $minimalPrice['CURRENCY'],
					 true
				);
				$minimalPrice[ 'RATIO_' . $fieldName ]       = $minimalPrice[ $fieldName ] * $ratio;
				$minimalPrice[ 'PRINT_RATIO_' . $fieldName ] = \CCurrencyLang::CurrencyFormat(
					 $minimalPrice[ 'RATIO_' . $fieldName ],
					 $minimalPrice['CURRENCY'],
					 true
				);
			}
			unset($fieldName);

			if($this->arParams['FILL_ITEM_ALL_PRICES']) {
				foreach(array_keys($fullPrices) as $priceType) {
					foreach($prepareFields as $fieldName) {
						$fullPrices[ $priceType ][ 'PRINT_' . $fieldName ]       = \CCurrencyLang::CurrencyFormat(
							 $fullPrices[ $priceType ][ $fieldName ],
							 $fullPrices[ $priceType ]['CURRENCY'],
							 true
						);
						$fullPrices[ $priceType ][ 'RATIO_' . $fieldName ]       = $fullPrices[ $priceType ][ $fieldName ] * $ratio;
						$fullPrices[ $priceType ][ 'PRINT_RATIO_' . $fieldName ] = \CCurrencyLang::CurrencyFormat(
							 $minimalPrice[ 'RATIO_' . $fieldName ],
							 $minimalPrice['CURRENCY'],
							 true
						);
					}
					unset($fieldName);
				}
				unset($priceType);
			}

			unset($prepareFields);
		}
		unset($oldMatrix, $oldMinPrice, $oldPrices);

		if(!$this->arParams['FILL_ITEM_ALL_PRICES'])
			return $minimalPrice;

		return array(
			 'MINIMAL_PRICE' => $minimalPrice,
			 'ALL_PRICES'    => array(
					'QUANTITY_FROM'    => $minimalPrice['QUANTITY_FROM'],
					'QUANTITY_TO'      => $minimalPrice['QUANTITY_TO'],
					'QUANTITY_HASH'    => $minimalPrice['QUANTITY_HASH'],
					'MEASURE_RATIO_ID' => $minimalPrice['MEASURE_RATIO_ID'],
					'PRICES'           => $fullPrices,
			 ),
		);
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// CURRENCY
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected
	function getTemplateCurrencies()
	{
		$currencies = array();

		if($this->useCurrency) {
			if(isset($this->arResult['CONVERT_CURRENCY']['CURRENCY_ID'])) {
				$currencyFormat = \CCurrencyLang::GetFormatDescription($this->arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
				$currencies     = array(
					 array(
							'CURRENCY' => $this->arResult['CONVERT_CURRENCY']['CURRENCY_ID'],
							'FORMAT'   => array(
								 'FORMAT_STRING'     => $currencyFormat['FORMAT_STRING'],
								 'DEC_POINT'         => $currencyFormat['DEC_POINT'],
								 'THOUSANDS_SEP'     => $currencyFormat['THOUSANDS_SEP'],
								 'DECIMALS'          => $currencyFormat['DECIMALS'],
								 'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
								 'HIDE_ZERO'         => $currencyFormat['HIDE_ZERO'],
							),
					 ),
				);
				unset($currencyFormat);
			}
			else {
				$currencyIterator = Currency\CurrencyTable::getList(array(
					 'select' => array('CURRENCY'),
				));
				while($currency = $currencyIterator->fetch()) {
					$currencyFormat = \CCurrencyLang::GetFormatDescription($currency['CURRENCY']);
					$currencies[]   = array(
						 'CURRENCY' => $currency['CURRENCY'],
						 'FORMAT'   => array(
								'FORMAT_STRING'     => $currencyFormat['FORMAT_STRING'],
								'DEC_POINT'         => $currencyFormat['DEC_POINT'],
								'THOUSANDS_SEP'     => $currencyFormat['THOUSANDS_SEP'],
								'DECIMALS'          => $currencyFormat['DECIMALS'],
								'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
								'HIDE_ZERO'         => $currencyFormat['HIDE_ZERO'],
						 ),
					);
				}
				unset($currencyFormat, $currency, $currencyIterator);
			}
		}

		return $currencies;
	}
}
