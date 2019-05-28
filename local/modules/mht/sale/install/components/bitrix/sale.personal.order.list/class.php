<?php

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */

use Bitrix\Main;
use Bitrix\Main\Config;
use Bitrix\Main\Localization;
use Bitrix\Main\Loader;
use Bitrix\Main\Data;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBitrixPersonalOrderListComponent extends CBitrixComponent
{
	const E_SALE_MODULE_NOT_INSTALLED 		= 10000;
	const E_CANNOT_COPY_ORDER_NOT_FOUND 	= 10001;
	const E_CANNOT_COPY_CANT_ADD_BASKET		= 10002;
	const E_CATALOG_MODULE_NOT_INSTALLED	= 10003;

	/**
	 * Fatal error list. Any fatal error makes useless further execution of a component code.
	 * In most cases, there will be only one error in a list according to the scheme "one shot - one dead body"
	 *
	 * @var string[] Array of fatal errors.
	 */

	protected $errorsFatal = array();
	/**
	 * Non-fatal error list. Some non-fatal errors may occur during component execution, so certain functions of the component
	 * may became defunct. Still, user should stay informed.
	 * There may be several non-fatal errors in a list.
	 *
	 * @var string[] Array of non-fatal errors.
	 */
	protected $errorsNonFatal = array();

	/**
	 * Contains some valuable info from $_REQUEST
	 *
	 * @var object request info
	 */
	protected $requestData = array();

	/**
	 * Gathered options that are required
	 *
	 * @var string[] options
	 */
	protected $options = array();

	/**
	 * Variable remains true if there is 'catalog' module installed
	 *
	 * @var bool flag
	 */
	protected $useCatalog = true;

	/**
	 * A value of current date format
	 *
	 * @var string format
	 */
	private $dateFormat = '';

	/**
	 * Filter used when select orders
	 *
	 * @var mixed[] filter
	 */
	protected $filter = array();

	/**
	 * Sort field for query
	 *
	 * @var string field
	 */
	protected $sortBy = false;

	/**
	 * Sort direction for query
	 *
	 * @var string order: asc or desc
	 */
	protected $sortOrder = false;

	protected $dbResult = array();
	private $dbQueryResult = array();

	protected $currentCache = null;

	/**
	 * A convert map for method self::formatDate()
	 *
	 * @var string[] keys
	 */
	protected $orderDateFields2Convert = array(
		'DATE_INSERT',
		'DATE_STATUS',
		'PAY_VOUCHER_DATE',
		'DATE_DEDUCTED',
		'DATE_UPDATE',
		'PS_RESPONSE_DATE',
		'DATE_PAY_BEFORE',
		'DATE_BILL',
		'DATE_CANCELED'
	);

	/**
	 * A convert map for method self::formatDate()
	 *
	 * @var string[] keys
	 */
	protected $basketDateFields2Convert = array(
		'DATE_INSERT',
		'DATE_UPDATE'
	);

	public function __construct($component = null)
	{
		parent::__construct($component);

		CPageOption::SetOptionString("main", "nav_page_in_session", "N");

		$this->dateFormat = CSite::GetDateFormat("FULL", SITE_ID);

		Localization\Loc::loadMessages(__FILE__);
	}

	/**
	 * Function checks if required modules installed. If not, throws an exception
	 * @throws Main\SystemException
	 * @return void
	 */
	protected function checkRequiredModules()
	{
		if (!Loader::includeModule('sale'))
			throw new Main\SystemException(Localization\Loc::getMessage("SPOL_SALE_MODULE_NOT_INSTALL"), self::E_SALE_MODULE_NOT_INSTALLED);
		if (!Loader::includeModule('catalog'))
			$this->useCatalog = false;
	}

	/**
	 * Function checks if user is authorized or not. If not, auth form will be shown.
	 * @return void
	 */
	protected function checkAuthorized()
	{
		global $USER;
		global $APPLICATION;

		if (!$USER->IsAuthorized())
			$APPLICATION->AuthForm(Localization\Loc::getMessage("SPOL_ACCESS_DENIED"));
	}

	/**
	 * Function checks and prepares all the parameters passed. Everything about $arParam modification is here.
	 * @param mixed[] $arParams List of unchecked parameters
	 * @return mixed[] Checked and valid parameters
	 */
	public function onPrepareComponentParams($arParams)
	{
		global $APPLICATION;

		$this->tryParseInt($arParams["CACHE_TIME"], 3600, true);

		$arParams['CACHE_GROUPS'] = trim($arParams['CACHE_GROUPS']);
		if ('N' != $arParams['CACHE_GROUPS'])
			$arParams['CACHE_GROUPS'] = 'Y';

		$this->tryParseString($arParams["PATH_TO_DETAIL"], $APPLICATION->GetCurPage()."?"."ID=#ID#");
		$this->tryParseString($arParams["PATH_TO_COPY"], $APPLICATION->GetCurPage()."?"."ID=#ID#");
		$this->tryParseString($arParams["PATH_TO_CANCEL"], $APPLICATION->GetCurPage()."?"."ID=#ID#");
		$this->tryParseString($arParams["PATH_TO_BASKET"], "basket.php");

		if ($arParams["SAVE_IN_SESSION"] != "N")
			$arParams["SAVE_IN_SESSION"] = "Y";

		if (!is_array($arParams['HISTORIC_STATUSES']) || empty($arParams['HISTORIC_STATUSES']))
			$arParams['HISTORIC_STATUSES'] = array('F');

		$arParams["NAV_TEMPLATE"] = (strlen($arParams["NAV_TEMPLATE"]) ? $arParams["NAV_TEMPLATE"] : "");

		$this->tryParseInt($arParams["ORDERS_PER_PAGE"], 20);
		$this->tryParseString($arParams["ACTIVE_DATE_FORMAT"], "d.m.Y");

		return $arParams;
	}

	/**
	 * Function reduces input value to integer type, and, if gets null, passes the default value
	 * @param mixed $fld Field value
	 * @param int $default Default value
	 * @param int $allowZero Allows zero-value of the parameter
	 * @return int Parsed value
	 */
	public static function tryParseInt(&$fld, $default, $allowZero = false)
	{
		$fld = intval($fld);
		if(!$allowZero && !$fld && isset($default))
			$fld = $default;

		return $fld;
	}

	/**
	 * Function processes string value and, if gets null, passes the default value to it
	 * @param mixed $fld Field value
	 * @param string $default Default value
	 * @return string parsed value
	 */
	public static function tryParseString(&$fld, $default)
	{
		$fld = trim((string)$fld);
		if(!strlen($fld) && isset($default))
			$fld = htmlspecialcharsbx($default);

		return $fld;
	}

	/**
	 * Function sets page title, if required
	 * @return void
	 */
	protected function setTitle()
	{
		global $APPLICATION;

		if ($this->arParams["SET_TITLE"] == 'Y')
			$APPLICATION->SetTitle(Localization\Loc::getMessage("SPOL_DEFAULT_TITLE"));
	}

	/**
	 * Function gets all options required for component
	 * @return void
	 */
	protected function getOptions()
	{
		$this->options['USE_ACCOUNT_NUMBER'] = (Config\Option::get("sale", "account_number_template", "") !== "") ? true : false;
	}

	/**
	 * Function processes and corrects $_REQUEST. Everyting about $_REQUEST lies here.
	 * @return void
	 */
	protected function processRequest()
	{
		$this->requestData["COPY_ORDER"] = ($_REQUEST["COPY_ORDER"] == "Y");
		$this->requestData["ID"] = urldecode(urldecode($this->arParams["ID"]));

		if (strlen($_REQUEST["del_filter"]))
		{
			unset($_REQUEST["filter_id"]);
			unset($_REQUEST["filter_date_from"]);
			unset($_REQUEST["filter_date_to"]);
			unset($_REQUEST["filter_status"]);
			unset($_REQUEST["filter_payed"]);
			unset($_REQUEST["filter_canceled"]);
			$_REQUEST["filter_history"] = "Y";
			if ($this->arParams["SAVE_IN_SESSION"] == "Y")
			{
				unset($_SESSION["spo_filter_id"]);
				unset($_SESSION["spo_filter_date_from"]);
				unset($_SESSION["spo_filter_date_to"]);
				unset($_SESSION["spo_filter_status"]);
				unset($_SESSION["spo_filter_payed"]);
				unset($_SESSION["spo_filter_canceled"]);
				$_SESSION["spo_filter_history"] = "Y";
			}
		}

		$this->filterRestore();
		$this->filterStore();

		$this->sortBy = (strlen($_REQUEST["by"]) ? $_REQUEST["by"]: "ID");
		$this->sortOrder = (strlen($_REQUEST["order"]) ? $_REQUEST["order"]: "DESC");

		$this->prepareFilter();
	}

	/**
	 * Read filter from session (or anywhere else), if required
	 * @return void
	 */
	protected function filterRestore()
	{
		if ($this->arParams["SAVE_IN_SESSION"] == "Y" && !strlen($_REQUEST["filter"]))
		{
			if (intval($_SESSION["spo_filter_id"]))
				$_REQUEST["filter_id"] = $_SESSION["spo_filter_id"];
			if (strlen($_SESSION["spo_filter_date_from"]))
				$_REQUEST["filter_date_from"] = $_SESSION["spo_filter_date_from"];
			if (strlen($_SESSION["spo_filter_date_to"]))
				$_REQUEST["filter_date_to"] = $_SESSION["spo_filter_date_to"];
			if (strlen($_SESSION["spo_filter_status"]))
				$_REQUEST["filter_status"] = $_SESSION["spo_filter_status"];
			if (strlen($_SESSION["spo_filter_payed"]))
				$_REQUEST["filter_payed"] = $_SESSION["spo_filter_payed"];
			if (strlen($_SESSION["spo_filter_canceled"]))
				$_REQUEST["filter_canceled"] = $_SESSION["spo_filter_canceled"];
			if ($_SESSION["spo_filter_history"] == "Y")
				$_REQUEST["filter_history"] = "Y";
		}
	}

	/**
	 * Store filter in session (or anywhere else), if required.
	 * @return void
	 */
	protected function filterStore()
	{
		if ($this->arParams["SAVE_IN_SESSION"] == "Y" && strlen($_REQUEST["filter"]))
		{
			$_SESSION["spo_filter_id"] = $_REQUEST["filter_id"];
			$_SESSION["spo_filter_date_from"] = $_REQUEST["filter_date_from"];
			$_SESSION["spo_filter_date_to"] = $_REQUEST["filter_date_to"];
			$_SESSION["spo_filter_status"] = $_REQUEST["filter_status"];
			$_SESSION["spo_filter_payed"] = $_REQUEST["filter_payed"];
			$_SESSION["spo_filter_history"] = $_REQUEST["filter_history"];
		}
	}

	/**
	 * Creates filter for CSaleOrder::GetList() based on $_REQUEST and other parameters
	 * @return void
	 */
	protected function prepareFilter()
	{
		global $USER;
		global $DB;

		$arFilter = array();
		$arFilter["USER_ID"] = $USER->GetID();
		$arFilter["LID"] = SITE_ID;

		if (strlen($_REQUEST["filter_id"]))
		{
			if ($this->options['USE_ACCOUNT_NUMBER'])
				$arFilter["ACCOUNT_NUMBER"] = $_REQUEST["filter_id"];
			else
				$arFilter["ID"] = intval($_REQUEST["filter_id"]);
		}

		if (strlen($_REQUEST["filter_date_from"]))
			$arFilter["DATE_FROM"] = trim($_REQUEST["filter_date_from"]);
		if (strlen($_REQUEST["filter_date_to"]))
		{
			$arFilter["DATE_TO"] = trim($_REQUEST["filter_date_to"]);

			if ($arDate = ParseDateTime(trim($_REQUEST["filter_date_to"]), $this->dateFormat))
			{
				if (StrLen(trim($_REQUEST["filter_date_to"])) < 11)
				{
					$arDate["HH"] = 23;
					$arDate["MI"] = 59;
					$arDate["SS"] = 59;
				}

				$arFilter["DATE_TO"] = date($DB->DateFormatToPHP($this->dateFormat), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
			}
		}

		if (strlen($_REQUEST["filter_status"]))
			$arFilter["STATUS_ID"] = trim($_REQUEST["filter_status"]);

		if (strlen($_REQUEST["filter_payed"]))
			$arFilter["PAYED"] = trim($_REQUEST["filter_payed"]);

		if(!isset($_REQUEST['show_all']) || $_REQUEST['show_all'] == 'N')
		{
			if($_REQUEST["filter_history"]!="Y")
				$arFilter["!@COMPLETE_ORDERS"] = $this->arParams['HISTORIC_STATUSES'];

			if(isset($_REQUEST["filter_history"]) && $_REQUEST["filter_history"] == "Y")
				$arFilter["@COMPLETE_ORDERS"] = $this->arParams['HISTORIC_STATUSES'];
		}

		if (strlen($_REQUEST["filter_canceled"]))
			$arFilter["CANCELED"] = trim($_REQUEST["filter_canceled"]);

		$this->filter = $arFilter;
	}

	/**
	 * Function wraps action list evaluation into try-catch block.
	 * @return void
	 */
	private function performActions()
	{
		try
		{
			$this->performActionList();
		}
		catch (Exception $e)
		{
			$this->errorsNonFatal[htmlspecialcharsEx($e->getCode())] = htmlspecialcharsEx($e->getMessage());
		}
	}

	/**
	 * Function perform pre-defined list of actions based on current state of $_REQUEST and parameters.
	 * @return void
	 */
	protected function performActionList()
	{
		// copy order
		$this->performActionCopyOrder();

		// some other ...
	}

	/**
	 * Function checks if order with supplied id is really exists.
	 * @param int|string $id Order id
	 * @return int Order id
	 */
	private function getRealId($id)
	{
		global $USER;

		$arOrder = false;
		$saleOrder = new CSaleOrder();

		if ($this->options['USE_ACCOUNT_NUMBER'])
		{
			$dbOrder = $saleOrder->GetList(
				array("ID"=>"DESC"),
				array("ACCOUNT_NUMBER" => $id, "USER_ID" => $USER->GetID(), "LID" => SITE_ID),
				false,
				false,
				array("ID")
			);
			$arOrder = $dbOrder->Fetch();
		}

		if (!$arOrder)
		{
			$dbOrder = $saleOrder->GetList(
				array("ID"=>"DESC"),
				array("ID" => $id, "USER_ID"=>$USER->GetID(), "LID" => SITE_ID),
				false,
				false,
				array("ID")
			);
			$arOrder = $dbOrder->Fetch();
		}

		if (empty($arOrder))
			return false;

		return $arOrder['ID'];
	}

	/**
	 * Perform the following action: copy order
	 * @throws Main\SystemException
	 * @return void
	 */
	protected function performActionCopyOrder()
	{
		if (strlen($this->requestData["ID"]) && $this->requestData["COPY_ORDER"])
		{
			if($id = $this->getRealId($this->requestData["ID"]))
				$this->copyOrder2CustomerBasket($id);
			else
				throw new Main\SystemException(Localization\Loc::getMessage('SPOL_CANNOT_COPY_ORDER'), self::E_CANNOT_COPY_ORDER_NOT_FOUND);
		}
	}

	/**
	 * Function obtains all properties of a basket item
	 * @param int $id Basket item Id to search for
	 * @return mixed[] List of basket item properties
	 */
	protected function getBasketItemProps($id)
	{
		$arProps = array();
		$dbBasketProps = CSaleBasket::GetPropsList(
				array("SORT" => "ASC"),
				array("BASKET_ID" => $id),
				false,
				false,
				array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
		);

		if ($arBasketProps = $dbBasketProps->Fetch())
		{
			do
			{
				$arProps[] = array(
					"NAME" => $arBasketProps["NAME"],
					"CODE" => $arBasketProps["CODE"],
					"VALUE" => $arBasketProps["VALUE"]
				);
			}
			while ($arBasketProps = $dbBasketProps->Fetch());
		}

		return $arProps;
	}

	/**
	 * The default action in case of success copying order
	 * @return void
	 */
	protected function doAfterOrderCopyed()
	{
		LocalRedirect($this->arParams["PATH_TO_BASKET"]);
	}

	/**
	 * Function performs moving entire basket content of a certain order into client`s basket. It implements "copy order" action.
	 * @param int $id Order id
	 * @throws Main\SystemException
	 * @return void
	 */
	protected function copyOrder2CustomerBasket($id)
	{
		if ($id)
		{
			$dbBasket = CSaleBasket::GetList(
				array("ID" => "ASC"),
				array("ORDER_ID" => $id),
				false,
				false,
				array(
					'SET_PARENT_ID', 'TYPE', 'ID',
					'PRODUCT_ID', 'PRODUCT_PRICE_ID', 'PRICE', 'CURRENCY', 'WEIGHT', 'QUANTITY', 'LID',
					'NAME', 'CALLBACK_FUNC', 'MODULE', 'NOTES', 'PRODUCT_PROVIDER_CLASS', 'CANCEL_CALLBACK_FUNC',
					'ORDER_CALLBACK_FUNC', 'PAY_CALLBACK_FUNC', 'DETAIL_PAGE_URL', 'CATALOG_XML_ID', 'PRODUCT_XML_ID', 'VAT_RATE'
				)
			);
			$success = false;
			$item = new CSaleBasket;
			while ($arBasket = $dbBasket->Fetch())
			{
				if (CSaleBasketHelper::isSetItem($arBasket))
					continue;

				$arFields = array(
					"PRODUCT_ID"			=> $arBasket["PRODUCT_ID"],
					"PRODUCT_PRICE_ID"		=> $arBasket["PRODUCT_PRICE_ID"],
					"PRICE"					=> $arBasket["PRICE"],
					"CURRENCY"				=> $arBasket["CURRENCY"],
					"WEIGHT"				=> $arBasket["WEIGHT"],
					"QUANTITY"				=> $arBasket["QUANTITY"],
					"LID"					=> $arBasket["LID"],
					"NAME"					=> $arBasket["NAME"],
					"CALLBACK_FUNC"			=> $arBasket["CALLBACK_FUNC"],
					"MODULE"				=> $arBasket["MODULE"],
					"NOTES"					=> $arBasket["NOTES"],
					"PRODUCT_PROVIDER_CLASS"	=> $arBasket["PRODUCT_PROVIDER_CLASS"],
					"CANCEL_CALLBACK_FUNC"	=> $arBasket["CANCEL_CALLBACK_FUNC"],
					"ORDER_CALLBACK_FUNC"	=> $arBasket["ORDER_CALLBACK_FUNC"],
					"PAY_CALLBACK_FUNC"		=> $arBasket["PAY_CALLBACK_FUNC"],
					"DETAIL_PAGE_URL"		=> $arBasket["DETAIL_PAGE_URL"],
					"CATALOG_XML_ID" 		=> $arBasket["CATALOG_XML_ID"],
					"PRODUCT_XML_ID" 		=> $arBasket["PRODUCT_XML_ID"],
					"VAT_RATE" 				=> $arBasket["VAT_RATE"],
					"PROPS"					=> $this->getBasketItemProps($arBasket["ID"]),
					"TYPE"                  => $arBasket["TYPE"]
				);
				$newID = (int)$item->Add($arFields);
				if ($newID > 0)
					$success = true;
			}
			if (!$success)
				throw new Main\SystemException(Localization\Loc::getMessage('SPOL_CANNOT_COPY_ORDER'), self::E_CANNOT_COPY_CANT_ADD_BASKET);
			$this->doAfterOrderCopyed();
		}
	}

	/**
	 * Read some data from database, using cache. Under some info we mean status list, delivery system list and so on.
	 * This will be a shared cache between sale.personal.order.list and sale.personal.order.detail, so beware of collisions.
	 * @throws Main\SystemException
	 * @return void
	 */
	protected function obtainDataReferences()
	{
		if ($this->startCache(array('spo-shared')))
		{
			try
			{
				$cachedData = array();

				/////////////////////
				/////////////////////

				// Person type
				$cachedData['PERSON_TYPE'] = array();
				$dbPType = CSalePersonType::GetList(array("SORT"=>"ASC"));
				while ($arPType = $dbPType->Fetch())
					$cachedData['PERSON_TYPE'][$arPType["ID"]] = $arPType;

				// Save statuses for Filter form
				$cachedData['STATUS'] = array();
				$dbStatus = CSaleStatus::GetList(array("SORT"=>"ASC"), array("LID"=>LANGUAGE_ID));
				while ($arStatus = $dbStatus->Fetch())
					$cachedData['STATUS'][$arStatus["ID"]] = $arStatus;

				$cachedData['PAYSYS'] = array();
				$dbPaySystem = CSalePaySystem::GetList(array("SORT"=>"ASC"));
				while ($arPaySystem = $dbPaySystem->Fetch())
					$cachedData['PAYSYS'][$arPaySystem["ID"]] = $arPaySystem;

				$cachedData['DELIVERY'] = array();
				$dbDelivery = CSaleDelivery::GetList(array("SORT"=>"ASC"));
				while ($arDelivery = $dbDelivery->Fetch())
					$cachedData['DELIVERY'][$arDelivery["ID"]] = $arDelivery;

				$cachedData['DELIVERY_HANDLERS'] = array();
				$dbDelivery = CSaleDeliveryHandler::GetList(array(), array(array("SITE_ID" => SITE_ID)));
				while ($arDeliveryHandler = $dbDelivery->Fetch())
					$cachedData['DELIVERY_HANDLERS'][$arDeliveryHandler["SID"]] = $arDeliveryHandler;

				/////////////////////
				/////////////////////

			}
			catch (Exception $e)
			{
				$this->abortCache();
				throw $e;
			}

			$this->endCache($cachedData);

		}
		else
			$cachedData = $this->getCacheData();

		$this->dbResult = array_merge($this->dbResult, $cachedData);
	}

	/**
	 * Perform reading main data from database, no cache is used
	 * @return void
	 */
	protected function obtainDataOrders()
	{
		$this->dbQueryResult['ORDERS'] = CSaleOrder::GetList(array($this->sortBy => $this->sortOrder), $this->filter);
		$this->dbQueryResult['ORDERS']->NavStart($this->arParams["ORDERS_PER_PAGE"], false);

		if(empty($this->dbQueryResult['ORDERS']))
			return;

		while ($arOrder = $this->dbQueryResult['ORDERS']->GetNext())
		{
			$arOBasket = array();
			$dbBasket = CSaleBasket::GetList(array('NAME' => 'asc'), array("ORDER_ID"=>$arOrder["ID"]), false, false, array('*'));
			while ($arBasket = $dbBasket->Fetch())
			{
				if (CSaleBasketHelper::isSetItem($arBasket))
					continue;

				$arOBasket[] = $arBasket;
			}

			$this->dbResult['ORDERS'][] = array(
				"ORDER" => $arOrder,
				"BASKET_ITEMS" => $arOBasket,
			);
		}
	}

	/**
	 * Fetches all required data from database. Everyting that connected with data fetch lies here.
	 * @return void
	 */
	protected function obtainData()
	{
		$this->obtainDataReferences();
		$this->obtainDataOrders();
	}

	/**
	 * Move data read from database to a specially formatted $arResult
	 * @return void
	 */
	protected function formatResult()
	{
		global $APPLICATION;

		$arResult = array();

		// references
		$arResult["INFO"]["STATUS"] = $this->dbResult['STATUS'];
		$arResult["INFO"]["PAY_SYSTEM"] = $this->dbResult['PAYSYS'];
		$arResult["INFO"]["DELIVERY"] = $this->dbResult['DELIVERY'];
		$arResult["INFO"]["DELIVERY_HANDLERS"] = $this->dbResult['DELIVERY_HANDLERS'];

		$arResult["CURRENT_PAGE"] = $APPLICATION->GetCurPage();
		$arResult["NAV_STRING"] = $this->dbQueryResult['ORDERS']->GetPageNavString(Localization\Loc::getMessage("SPOL_PAGES"), $this->arParams["NAV_TEMPLATE"]);

		// bug walkaround
		$this->arParams["PATH_TO_COPY"] .= (strpos($this->arParams["PATH_TO_COPY"], "?") === false ? "?" : "&amp;");
		$this->arParams["PATH_TO_CANCEL"] .= (strpos($this->arParams["PATH_TO_CANCEL"], "?") === false ? "?" : "&amp;");

		if(self::isNonemptyArray($this->dbResult['ORDERS']))
		{
			foreach ($this->dbResult['ORDERS'] as $k => $orderInfo)
			{
				$arOrder =& $this->dbResult['ORDERS'][$k]['ORDER'];
				$arOBasket =& $this->dbResult['ORDERS'][$k]['BASKET_ITEMS'];

				$arOrder["FORMATED_PRICE"] = SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]);

				$this->formatDate($arOrder, $this->orderDateFields2Convert);

				$arOrder["CAN_CANCEL"] = (($arOrder["CANCELED"] != "Y" && $arOrder["STATUS_ID"] != "F" && $arOrder["PAYED"] != "Y") ? "Y" : "N");

				$arOrder["URL_TO_DETAIL"] = CComponentEngine::MakePathFromTemplate($this->arParams["PATH_TO_DETAIL"], array("ID" => urlencode(urlencode($arOrder["ACCOUNT_NUMBER"]))));
				$arOrder["URL_TO_COPY"] = CComponentEngine::MakePathFromTemplate($this->arParams["PATH_TO_COPY"], array("ID" => urlencode(urlencode($arOrder["ACCOUNT_NUMBER"]))))."COPY_ORDER=Y";
				$arOrder["URL_TO_CANCEL"] = CComponentEngine::MakePathFromTemplate($this->arParams["PATH_TO_CANCEL"], array("ID" => urlencode(urlencode($arOrder["ACCOUNT_NUMBER"]))))."CANCEL=Y";

				if(self::isNonemptyArray($arOBasket))
				{
					foreach ($arOBasket as $n => $basketInfo)
					{
						$arBasket =& $arOBasket[$n];

						$arBasket["NAME~"] = $arBasket["NAME"];
						$arBasket["NOTES~"] = $arBasket["NOTES"];
						$arBasket["NAME"] = htmlspecialcharsEx($arBasket["NAME"]);
						$arBasket["NOTES"] = htmlspecialcharsEx($arBasket["NOTES"]);
						$arBasket["QUANTITY"] = doubleval($arBasket["QUANTITY"]);

						// backward compatibility
						$arBasket["MEASURE_TEXT"] = $arBasket["MEASURE_NAME"];

						$this->formatDate($arBasket, $this->basketDateFields2Convert);
					}
				}
			}

			$arResult["ORDERS"] = $this->dbResult['ORDERS'];
		}
		else
			$arResult["ORDERS"] = array();

		$this->arResult = $arResult;
	}

	/**
	 * Move all errors to $arResult, if there were any
	 * @return void
	 */
	protected function formatResultErrors()
	{
		$errors = array();
		if (!empty($this->errorsFatal))
			$errors['FATAL'] = $this->errorsFatal;
		if (!empty($this->errorsNonFatal))
			$errors['NONFATAL'] = $this->errorsNonFatal;

		if (!empty($errors))
			$this->arResult['ERRORS'] = $errors;

		// backward compatiblity
		$error = each($this->errorsFatal);
		if (!empty($error['value']))
			$this->arResult['ERROR_MESSAGE'] = $error['value'];
	}

	/**
	 * Function implements all the life cycle of our component
	 * @return void
	 */
	public function executeComponent()
	{
		try
		{
			$this->checkRequiredModules();
			$this->checkAuthorized();
			$this->setTitle();
			$this->getOptions();
			$this->processRequest();

			$this->performActions();

			$this->obtainData();
			$this->formatResult();
		}
		catch (Exception $e)
		{
			$this->errorsFatal[htmlspecialcharsEx($e->getCode())] = htmlspecialcharsEx($e->getMessage());
		}

		$this->formatResultErrors();

		$this->includeComponentTemplate();
	}

	/**
	 * Convert dates if date template set
	 * @param mixed[] $arr data array to be converted
	 * @param string[] $conversion contains sublist of keys of $arr, that will be converted
	 * @return void
	 */
	protected function formatDate(&$arr, $conversion)
	{
		if (strlen($this->arParams['ACTIVE_DATE_FORMAT']) && self::isNonemptyArray($conversion))
			foreach ($conversion as $fld)
			{
				if (!empty($arr[$fld]))
					$arr[$fld."_FORMATED"] = CIBlockFormatProperties::DateFormat($this->arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arr[$fld]));
			}
	}

	/**
	 * Function checks if it`s argument is a legal array for foreach() construction
	 * @param mixed $arr data to check
	 * @return boolean
	 */
	protected static function isNonemptyArray($arr)
	{
		return is_array($arr) && !empty($arr);
	}

	////////////////////////
	// Cache functions
	////////////////////////
	/**
	 * Function checks if cacheing is enabled in component parameters
	 * @return boolean
	 */
	final protected function getCacheNeed()
	{
		return	intval($this->arParams['CACHE_TIME']) > 0 &&
				$this->arParams['CACHE_TYPE'] != 'N' &&
				Config\Option::get("main", "component_cache_on", "Y") == "Y";
	}

	/**
	 * Function perform start of cache process, if needed
	 * @param mixed[]|string $cacheId An optional addition for cache key
	 * @return boolean True, if cache content needs to be generated, false if cache is valid and can be read
	 */
	final protected function startCache($cacheId = array())
	{
		if(!$this->getCacheNeed())
			return true;

		$this->currentCache = Data\Cache::createInstance();

		return $this->currentCache->startDataCache(intval($this->arParams['CACHE_TIME']), $this->getCacheKey($cacheId));
	}

	/**
	 * Function perform start of cache process, if needed
	 * @throws Main\SystemException
	 * @param mixed[] $data Data to be stored in the cache
	 * @return void
	 */
	final protected function endCache($data = false)
	{
		if(!$this->getCacheNeed())
			return;

		if($this->currentCache == 'null')
			throw new Main\SystemException('Cache were not started');

		$this->currentCache->endDataCache($data);
		$this->currentCache = null;
	}

	/**
	 * Function discard cache generation
	 * @throws Main\SystemException
	 * @return void
	 */
	final protected function abortCache()
	{
		if(!$this->getCacheNeed())
			return;

		if($this->currentCache == 'null')
			throw new Main\SystemException('Cache were not started');

		$this->currentCache->abortDataCache();
		$this->currentCache = null;
	}

	/**
	 * Function return data stored in cache
	 * @throws Main\SystemException
	 * @return void|mixed[] Data from cache
	 */
	final protected function getCacheData()
	{
		if(!$this->getCacheNeed())
			return;

		if($this->currentCache == 'null')
			throw new Main\SystemException('Cache were not started');

		return $this->currentCache->getVars();
	}

	/**
	 * Function leaves the ability to modify cache key in future.
	 * @return string Cache key to be used in CPHPCache()
	 */
	final protected function getCacheKey($cacheId = array())
	{
		if(!is_array($cacheId))
			$cacheId = array((string) $cacheId);

		$cacheId['SITE_ID'] = SITE_ID;
		$cacheId['LANGUAGE_ID'] = LANGUAGE_ID;
		// if there are two or more caches with the same id, but with different cache_time, make them separate
		$cacheId['CACHE_TIME'] = intval($this->arResult['CACHE_TIME']);

		if(defined("SITE_TEMPLATE_ID"))
			$cacheId['SITE_TEMPLATE_ID'] = SITE_TEMPLATE_ID;

		return implode('|', $cacheId);
	}
}