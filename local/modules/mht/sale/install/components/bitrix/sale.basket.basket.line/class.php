<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class SaleBasketLineComponent extends CBitrixComponent
{
	private $bUseCatalog = null;
	private $readyForOrderFilter = array("CAN_BUY" => "Y", "DELAY" => "N", "SUBSCRIBE" => "N");

	public function onPrepareComponentParams($arParams)
	{
		// common

		$arParams['PATH_TO_BASKET'] = htmlspecialcharsEx(Trim($arParams['PATH_TO_BASKET']));
		if ($arParams['PATH_TO_BASKET'] == '')
			$arParams['PATH_TO_BASKET'] = '={SITE_DIR."personal/cart/"}';

		if ($arParams['SHOW_NUM_PRODUCTS'] != 'N')
			$arParams['SHOW_NUM_PRODUCTS'] = 'Y';

		if ($arParams['SHOW_TOTAL_PRICE'] != 'N')
			$arParams['SHOW_TOTAL_PRICE'] = 'Y';

		if ($arParams['SHOW_EMPTY_VALUES'] != 'N')
			$arParams['SHOW_EMPTY_VALUES'] = 'Y';

		// personal

		if ($arParams['SHOW_PERSONAL_LINK'] != 'Y')
			$arParams['SHOW_PERSONAL_LINK'] = 'N';

		$arParams['PATH_TO_PERSONAL'] = htmlspecialcharsEx(Trim($arParams['PATH_TO_PERSONAL']));
		if ($arParams['PATH_TO_PERSONAL'] == '')
			$arParams['PATH_TO_PERSONAL'] = '={SITE_DIR."personal/"}';

		// authorization

		if ($arParams['SHOW_AUTHOR'] != 'Y')
			$arParams['SHOW_AUTHOR'] = 'N';

		$arParams['PATH_TO_REGISTER'] = htmlspecialcharsEx(Trim($arParams['PATH_TO_REGISTER']));
		if ($arParams['PATH_TO_REGISTER'] == '')
			$arParams['PATH_TO_REGISTER'] = '={SITE_DIR."login/"}';

		$arParams['PATH_TO_PROFILE'] = htmlspecialcharsEx(Trim($arParams['PATH_TO_PROFILE']));
		if ($arParams['PATH_TO_PROFILE'] == '')
			$arParams['PATH_TO_PROFILE'] = '={SITE_DIR."personal/"}';

		// list

		if ($arParams['SHOW_PRODUCTS'] != 'Y')
			$arParams['SHOW_PRODUCTS'] = 'N';

		$arParams['PATH_TO_ORDER'] = htmlspecialcharsEx(Trim($arParams['PATH_TO_ORDER']));
		if ($arParams['PATH_TO_ORDER'] == '')
			$arParams['PATH_TO_ORDER'] = '={SITE_DIR."personal/order/make/"}';

		if ($arParams['SHOW_DELAY'] != 'N')
			$arParams['SHOW_DELAY'] = 'Y';

		if ($arParams['SHOW_NOTAVAIL'] != 'N')
			$arParams['SHOW_NOTAVAIL'] = 'Y';

		if ($arParams['SHOW_SUBSCRIBE'] != 'N')
			$arParams['SHOW_SUBSCRIBE'] = 'Y';

		if ($arParams['SHOW_IMAGE'] != 'N')
			$arParams['SHOW_IMAGE'] = 'Y';

		if ($arParams['SHOW_PRICE'] != 'N')
			$arParams['SHOW_PRICE'] = 'Y';

		if ($arParams['SHOW_SUMMARY'] != 'N')
			$arParams['SHOW_SUMMARY'] = 'Y';

		// Visual

		if ($arParams['POSITION_FIXED'] != 'Y')
			$arParams['POSITION_FIXED'] = 'N';

		if ($arParams['POSITION_VERTICAL'] != 'bottom' && $arParams['POSITION_VERTICAL'] != 'vcenter')
			$arParams['POSITION_VERTICAL'] = 'top';

		if ($arParams['POSITION_HORIZONTAL'] != 'left' && $arParams['POSITION_HORIZONTAL'] != 'hcenter')
			$arParams['POSITION_HORIZONTAL'] = 'right';

		// ajax

		if ($arParams['AJAX'] != 'Y')
			$arParams['AJAX'] = 'N';

		return $arParams;
	}

	private function getUserFilter()
	{
		$fUserID = IntVal(CSaleBasket::GetBasketUserID(True));
		return ($fUserID > 0)
			? array("FUSER_ID" => $fUserID, "LID" => SITE_ID, "ORDER_ID" => "NULL")
			: null; // no basket for current user
	}

	protected function removeItemFromCart()
	{
		if (preg_match('/^[0-9]+$/', $_POST["sbblRemoveItemFromCart"]) !== 1)
			return;

		if (! ($userFilter = $this->getUserFilter()))
			return;

		$numProducts = CSaleBasket::GetList(
			array(),
			$userFilter + array("ID" => $_POST['sbblRemoveItemFromCart']),
			array()
		);

		if ($numProducts > 0)
			CSaleBasket::Delete($_POST['sbblRemoveItemFromCart']);
	}

	public function executeComponent()
	{
		if(! \Bitrix\Main\Loader::includeModule ('sale'))
		{
			ShowError(GetMessage('SALE_MODULE_NOT_INSTALL'));
			return;
		}

		if (isset($_POST['sbblRemoveItemFromCart']))
			$this->removeItemFromCart();

		// prepare result

		if(! \Bitrix\Main\Loader::includeModule("currency"))
		{
			ShowError(GetMessage("CURRENCY_MODULE_NOT_INSTALLED"));
			return;
		}

		$this->bUseCatalog = \Bitrix\Main\Loader::includeModule('catalog');

		$this->arResult = array(
			"TOTAL_PRICE" => 0,
			"NUM_PRODUCTS" => 0,
			"CATEGORIES" => array(),
			"ERROR_MESSAGE" => GetMessage("TSB1_EMPTY"), // deprecated
		);

		if($this->arParams["SHOW_PRODUCTS"] == "Y")
			$this->arResult = $this->getProducts() + $this->arResult;
		else
		{
			if($this->arParams["SHOW_TOTAL_PRICE"] == "Y")
				$this->arResult = $this->getTotalPrice() + $this->arResult;
			else
			{
				$this->arResult["NUM_PRODUCTS"] = isset($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]) // && $_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] !== 0)
					? $_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]
					: $this->getNumProducts();
			}
		}

		if($this->arParams["SHOW_TOTAL_PRICE"] == "Y")
			$this->arResult["TOTAL_PRICE"] = CCurrencyLang::CurrencyFormat($this->arResult["TOTAL_PRICE"], CSaleLang::GetLangCurrency(SITE_ID), true);

		$productS = BasketNumberWordEndings($this->arResult["NUM_PRODUCTS"]);
		$this->arResult["PRODUCT(S)"] = GetMessage("TSB1_PRODUCT") . $productS;

		// compatibility!
		$this->arResult["PRODUCTS"] = str_replace("#END#", $productS,
			str_replace("#NUM#", $this->arResult["NUM_PRODUCTS"], GetMessage("TSB1_BASKET_TEXT"))
		);

		// output
		if ($this->arParams['AJAX'] == 'Y')
			$this->includeComponentTemplate('ajax_template');
		else
			$this->includeComponentTemplate();
	}

	private static $nextNumber = 0;

	public static function getNextNumber()
	{
		return ++self::$nextNumber;
	}

	private function getNumProducts()
	{
		$numProducts = 0;

		if ($userFilter = $this->getUserFilter())
		{
			$rsBasket = CSaleBasket::GetList(
				array (),
				$userFilter + $this->readyForOrderFilter,
				false,
				false,
				array('ID', 'SET_PARENT_ID', 'TYPE')
			);
			while ($arItem = $rsBasket->Fetch())
			{
				if (CSaleBasketHelper::isSetItem($arItem))
					continue;
				$numProducts ++;
			}
		}

		return $numProducts;
	}

	private function getTotalPrice()
	{
		if (! ($userFilter = $this->getUserFilter()))
			return array();

		$rsBasket = CSaleBasket::GetList(
			array(),
			$userFilter + $this->readyForOrderFilter,
			false,
			false,
			array(
				"QUANTITY", "PRICE", "CURRENCY", "DISCOUNT_PRICE", "WEIGHT", "VAT_RATE",
				"ID", "SET_PARENT_ID", "PRODUCT_ID", "CATALOG_XML_ID", "PRODUCT_XML_ID",
				"PRODUCT_PROVIDER_CLASS", "TYPE"
			)
		);

		$arBasketItems = array();

		while ($arItem = $rsBasket->Fetch())
		{
			if (CSaleBasketHelper::isSetItem($arItem))
				continue;
			$arBasketItems[] = $arItem;
		}

		$totalPrice = 0;

		if ($arBasketItems)
		{
			$arOrder = $this->calculateOrder($arBasketItems);
			$totalPrice = $arOrder['ORDER_PRICE'];
		}

		return array(
			'NUM_PRODUCTS' => count($arBasketItems),
			'TOTAL_PRICE' => $totalPrice
		);
	}

	private function calculateOrder($arBasketItems)
	{
		$totalPrice = 0;
		$totalWeight = 0;

		foreach ($arBasketItems as $arItem)
		{
			$totalPrice += $arItem["PRICE"] * $arItem["QUANTITY"];
			$totalWeight += $arItem["WEIGHT"] * $arItem["QUANTITY"];
		}

		$arOrder = array(
			'SITE_ID' => SITE_ID,
			'ORDER_PRICE' => $totalPrice,
			'ORDER_WEIGHT' => $totalWeight,
			'BASKET_ITEMS' => $arBasketItems
		);

		if (is_object($GLOBALS["USER"]))
		{
			$arOrder['USER_ID'] = $GLOBALS["USER"]->GetID();
			$arErrors = array();
			CSaleDiscount::DoProcessOrder($arOrder, array(), $arErrors);
		}

		return $arOrder;
	}

	private function getProducts()
	{
		if (! ($arFilter = $this->getUserFilter()))
			return array();

		if ($this->arParams['SHOW_NOTAVAIL'] == 'N')
			$arFilter['CAN_BUY'] = 'Y';
		if ($this->arParams['SHOW_DELAY'] == 'N')
			$arFilter['DELAY'] = 'N';
		if ($this->arParams['SHOW_SUBSCRIBE'] == 'N')
			$arFilter["SUBSCRIBE"] = 'N';

		$dbItems = CSaleBasket::GetList(
			array("NAME" => "ASC", "ID" => "ASC"),
			$arFilter,
			false,
			false,
			array(
				"ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
				"PRICE", "WEIGHT", "DETAIL_PAGE_URL", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID", "MEASURE_NAME",
				"PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "TYPE", "SET_PARENT_ID"
			)
		);

		$arBasketItems = array();
		$arElementId = array();
		$arSku2Parent = array();

		while ($arItem = $dbItems->GetNext(true, false))
		{
			if (CSaleBasketHelper::isSetItem($arItem))
				continue;

			$arItem["PRICE_FMT"] = CCurrencyLang::CurrencyFormat($arItem["PRICE"], $arItem["CURRENCY"], true);
			$arItem["FULL_PRICE"] = CCurrencyLang::CurrencyFormat($arItem["PRICE"] + $arItem["DISCOUNT_PRICE"], $arItem["CURRENCY"], true);
			$arItem['QUANTITY'] += 0; // remove excessive zeros after period
			if (! $arItem['MEASURE_NAME'])
				$arItem['MEASURE_NAME'] = GetMessage('TSB1_MEASURE_NAME');

			if ($this->arParams['SHOW_IMAGE'] == 'Y' && $this->bUseCatalog && $arItem["MODULE"] == 'catalog')
			{
				$arElementId[] = $arItem["PRODUCT_ID"];
				$arParent = CCatalogSku::GetProductInfo($arItem["PRODUCT_ID"]);
				if ($arParent)
				{
					$arElementId[] = $arParent["ID"];
					$arSku2Parent[$arItem["PRODUCT_ID"]] = $arParent["ID"];
				}
			}

			$arBasketItems[] = $arItem;
		}

		$arResult = array(
			'CATEGORIES' => array(),
			'TOTAL_PRICE' => 0
		);

		if ($arBasketItems)
		{
			if ($this->arParams['SHOW_IMAGE'] == 'Y')
				$this->setImgSrc($arBasketItems, $arElementId, $arSku2Parent);

			$arResult["CATEGORIES"] = array(
				"READY" => array(),
				"DELAY" => array(),
				"SUBSCRIBE" => array(),
				"NOTAVAIL" => array()
			);

			// fill item arrays for templates
			foreach ($arBasketItems as $arItem)
			{
				if ($arItem["CAN_BUY"] == "Y")
				{
					if ($arItem["DELAY"] == "Y")
						$arResult["CATEGORIES"]["DELAY"][] = $arItem;
					else
						$arResult["CATEGORIES"]["READY"][] = $arItem;
				}
				else
				{
					if ($arItem["SUBSCRIBE"] == "Y")
						$arResult["CATEGORIES"]["SUBSCRIBE"][] = $arItem;
					else
						$arResult["CATEGORIES"]["NOTAVAIL"][] = $arItem;
				}
			}

			if ($this->arParams['SHOW_PRICE'] == 'Y' ||
				$this->arParams['SHOW_SUMMARY'] == 'Y' ||
				$this->arParams['SHOW_TOTAL_PRICE'] == 'Y')
			{
				$arOrder = $this->calculateOrder($arResult["CATEGORIES"]["READY"]);
				$arResult["CATEGORIES"]["READY"] = $arOrder['BASKET_ITEMS'];

				foreach ($arResult["CATEGORIES"]["READY"] as &$arItem)
				{
					$arItem["SUM"] = CCurrencyLang::CurrencyFormat($arItem["PRICE"] * $arItem["QUANTITY"], $arItem["CURRENCY"], true);
					$arItem["PRICE_FMT"] = CCurrencyLang::CurrencyFormat($arItem["PRICE"], $arItem["CURRENCY"], true);
				}

				$arResult["TOTAL_PRICE"] = $arOrder['ORDER_PRICE'];
			}
		}

		return array(
			'NUM_PRODUCTS' => count($arBasketItems),
			'TOTAL_PRICE'  => $arResult["TOTAL_PRICE"],
			'CATEGORIES'   => $arResult["CATEGORIES"],
		);
	}

	private function setImgSrc(&$arBasketItems, $arElementId, $arSku2Parent)
	{
		$arImgFields = array ("PREVIEW_PICTURE", "DETAIL_PICTURE");
		$arProductData = getProductProps($arElementId, array("ID") + $arImgFields);

		foreach ($arBasketItems as &$arItem)
		{
			if (array_key_exists($arItem["PRODUCT_ID"], $arProductData) && is_array($arProductData[$arItem["PRODUCT_ID"]]))
			{
				foreach ($arProductData[$arItem["PRODUCT_ID"]] as $key => $value)
				{
					if (strpos($key, "PROPERTY_") !== false || in_array($key, $arImgFields))
						$arItem[$key] = $value;
				}
			}

			if (array_key_exists($arItem["PRODUCT_ID"], $arSku2Parent)) // if sku element doesn't have value of some property - we'll show parent element value instead
			{
				foreach ($arImgFields as $field) // fields to be filled with parents' values if empty
				{
					$fieldVal = (in_array($field, $arImgFields)) ? $field : $field."_VALUE";
					$parentId = $arSku2Parent[$arItem["PRODUCT_ID"]];

					if ((!isset($arItem[$fieldVal]) || (isset($arItem[$fieldVal]) && strlen($arItem[$fieldVal]) == 0))
						&& (isset($arProductData[$parentId][$fieldVal]) && !empty($arProductData[$parentId][$fieldVal]))) // can be array or string
					{
						$arItem[$fieldVal] = $arProductData[$parentId][$fieldVal];
					}
				}
			}

			$arItem["PICTURE_SRC"] = "";
			$arImage = null;
			if (isset($arItem["PREVIEW_PICTURE"]) && intval($arItem["PREVIEW_PICTURE"]) > 0)
				$arImage = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
			elseif (isset($arItem["DETAIL_PICTURE"]) && intval($arItem["DETAIL_PICTURE"]) > 0)
				$arImage = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
			if ($arImage)
			{
				$arFileTmp = CFile::ResizeImageGet(
					$arImage,
					array("width" => "70", "height" =>"70"),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arItem["PICTURE_SRC"] = $arFileTmp["src"];
			}
		}
	}
}

// Compatibility
if (!function_exists('BasketNumberWordEndings'))
{
	function BasketNumberWordEndings($num, $lang = false, $arEnds = false)
	{
		if ($lang===false)
			$lang = LANGUAGE_ID;

		if ($arEnds===false)
			$arEnds = array(GetMessage("TSB1_WORD_OBNOVL_END1"), GetMessage("TSB1_WORD_OBNOVL_END2"), GetMessage("TSB1_WORD_OBNOVL_END3"), GetMessage("TSB1_WORD_OBNOVL_END4"));

		if ($lang=="ru")
		{
			if (strlen($num)>1 && substr($num, strlen($num)-2, 1)=="1")
			{
				return $arEnds[0];
			}
			else
			{
				$c = IntVal(substr($num, strlen($num)-1, 1));
				if ($c==0 || ($c>=5 && $c<=9))
					return $arEnds[1];
				elseif ($c==1)
					return $arEnds[2];
				else
					return $arEnds[3];
			}
		}
		elseif ($lang=="en")
		{
			if (IntVal($num)>1)
			{
				return "s";
			}
			return "";
		}
		else
		{
			return "";
		}
	}
}
