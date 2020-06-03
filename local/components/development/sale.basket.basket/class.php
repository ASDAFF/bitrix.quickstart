<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Highloadblock as HL;

class CBitrixBasketComponent extends CBitrixComponent
{
	public $arCustomSelectFields = array();
	public $arIblockProps = array();
	public $weightKoef = 0;
	public $weightUnit = 0;
	public $quantityFloat = "N";
	public $countDiscount4AllQuantity = "N";
	public $priceVatShowValue = "N";
	public $hideCoupon = "N";
	public $usePrepayment = "N";
	public $pathToOrder = "/personal/order.php";

	public function onPrepareComponentParams($arParams)
	{
		$arParams["PATH_TO_ORDER"] = trim($arParams["PATH_TO_ORDER"]);
		if (strlen($arParams["PATH_TO_ORDER"]) <= 0)
			$arParams["PATH_TO_ORDER"] = "order.php";

		if (!isset($arParams['QUANTITY_FLOAT']))
			$arParams['QUANTITY_FLOAT'] = 'N';

		$arParams["HIDE_COUPON"] = ($arParams["HIDE_COUPON"] == "Y" || !CModule::IncludeModule("catalog")) ? "Y" : "N";
		$arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] = ($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N";
		$arParams['PRICE_VAT_SHOW_VALUE'] = ($arParams['PRICE_VAT_SHOW_VALUE'] == 'N') ? 'N' : 'Y';
		$arParams["USE_PREPAYMENT"] = ($arParams["USE_PREPAYMENT"] == 'Y') ? 'Y' : 'N';

		$arParams["WEIGHT_KOEF"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));
		$arParams["WEIGHT_UNIT"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));

		// default columns
		if (!isset($arParams["COLUMNS_LIST"]) || !is_array($arParams["COLUMNS_LIST"]) || empty($arParams["COLUMNS_LIST"]))
			$arParams["COLUMNS_LIST"] = array("NAME", "DISCOUNT", "WEIGHT", "DELETE", "DELAY", "TYPE", "PRICE", "QUANTITY", "SUM");

		// required columns
		if (!in_array("NAME", $arParams["COLUMNS_LIST"]))
			$arParams["COLUMNS_LIST"] = array_merge(array("NAME"), $arParams["COLUMNS_LIST"]);

		if (!in_array("QUANTITY", $arParams["COLUMNS_LIST"]))
			$arParams["COLUMNS_LIST"][] = "QUANTITY";

		if (!in_array("PRICE", $arParams["COLUMNS_LIST"]))
		{
			if (!in_array("SUM", $arParams["COLUMNS_LIST"]))
			{
				$arParams["COLUMNS_LIST"][] = "PRICE";
			}
			else // make PRICE before SUM
			{
				$index = array_search("SUM", $arParams["COLUMNS_LIST"]);
				array_splice($arParams["COLUMNS_LIST"], $index, 0, "PRICE");
			}
		}

		if (!isset($arParams["OFFERS_PROPS"]) && !is_array($arParams["OFFERS_PROPS"]))
			$arParams["OFFERS_PROPS"] = array();

		if (!isset($arParams["ACTION_VARIABLE"])
			|| strlen(trim($arParams["ACTION_VARIABLE"])) <= 0
			|| !preg_match('/[a-zA-Z0-9_-~.!*\'(),]/', trim($arParams["ACTION_VARIABLE"]))
			)
			$arParams["ACTION_VARIABLE"] = "action";
		else
			$arParams["ACTION_VARIABLE"] = trim($arParams["ACTION_VARIABLE"]);

		return $arParams;
	}

	public function executeComponent()
	{
		parent::setFramemode(false);
		$this->weightKoef = $this->arParams["WEIGHT_KOEF"];
		$this->weightUnit = $this->arParams["WEIGHT_UNIT"];
		$this->columns = $this->arParams["COLUMNS_LIST"];
		$this->offersProps = $this->arParams["OFFERS_PROPS"];

		$this->quantityFloat = $this->arParams["QUANTITY_FLOAT"];

		$this->countDiscount4AllQuantity = $this->arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"];
		$this->priceVatShowValue = $this->arParams["PRICE_VAT_SHOW_VALUE"];
		$this->hideCoupon = $this->arParams["HIDE_COUPON"];
		$this->usePrepayment = $this->arParams["USE_PREPAYMENT"];
		$this->pathToOrder = $this->arParams["PATH_TO_ORDER"];

		return parent::executeComponent();
	}

	public function getCustomColumns()
	{
		$propertyCount = 0;
		define("PROPERTY_COUNT_LIMIT", 24); // too much properties cause sql join error

		foreach ($this->columns as $key => $value) // making grid headers array
		{
			if (strpos($value, "PROPERTY_") !== false)
			{
				$propertyCount++;
				if ($propertyCount > PROPERTY_COUNT_LIMIT)
					continue;

				$this->arCustomSelectFields[] = $value; // array of iblock properties to select
				$id = $value."_VALUE";

				if (CModule::IncludeModule("iblock"))
				{
					$dbres = CIBlockProperty::GetList(array(), array("CODE" => substr($value, 9)));
					if ($arres = $dbres->GetNext())
					{
						$name = $arres["NAME"];
						$this->arIblockProps[substr($value, 9)] = $arres;
					}
				}
			}
			else
			{
				$id = $value;
				$name = "";
			}

			$arColumn = array(
				"id" => $id,
				"name" => $name
			);

			$res[] = $arColumn;
		}

		return $res;
	}

	public function getBasketItems()
	{
		global $APPLICATION;
		$bUseCatalog = (CModule::IncludeModule("catalog")) ? true : false;
		$bUseIblock = (CModule::IncludeModule("iblock")) ? true : false;

		CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);

		$bShowReady = False;
		$bShowDelay = False;
		$bShowSubscribe = False;
		$bShowNotAvail = False;
		$allSum = 0;
		$allWeight = 0;
		$allCurrency = CSaleLang::GetLangCurrency(SITE_ID);
		$allVATSum = 0;
		$arParents = array();

		$arResult["ITEMS"]["AnDelCanBuy"] = array();
		$arResult["ITEMS"]["DelDelCanBuy"] = array();
		$arResult["ITEMS"]["nAnCanBuy"] = array();
		$arResult["ITEMS"]["ProdSubscribe"] = array();
		$DISCOUNT_PRICE_ALL = 0;

		// BASKET PRODUCTS (including measures, ratio, iblock properties data)

		$arImgFields = array("PREVIEW_PICTURE", "DETAIL_PICTURE");
		$arBasketItems = array();
		$arSku2Parent = array();
		$arSetParentWeight = array();
		$dbItems = CSaleBasket::GetList(
			array("ID" => "ASC"),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			array(
				"ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
				"PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID",
				"PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "TYPE", "SET_PARENT_ID"
			)
		);
		while ($arItem = $dbItems->GetNext())
		{
			$arBasketItems[] = $arItem;

			if (CSaleBasketHelper::isSetItem($arItem))
				continue;

			$arElementId[] = $arItem["PRODUCT_ID"];

			if ($bUseCatalog)
			{
				$arParent = CCatalogSku::GetProductInfo($arItem["PRODUCT_ID"]);

				if ($arParent)
				{
					$arElementId[] = $arParent["ID"];
					$arSku2Parent[$arItem["PRODUCT_ID"]] = $arParent["ID"];

					$arParents[$arItem["PRODUCT_ID"]]["PRODUCT_ID"] = $arParent["ID"];
					$arParents[$arItem["PRODUCT_ID"]]["IBLOCK_ID"] = $arParent["IBLOCK_ID"];
				}
			}
		}

		// get measures, ratio, sku props data and available quantity
		if (!empty($arBasketItems) && $bUseCatalog)
		{
			$arBasketItems = getMeasures($arBasketItems);
			$arBasketItems = getRatio($arBasketItems);
			$arBasketItems = $this->getAvailableQuantity($arBasketItems);
		}

		// get product properties data
		$arProductData = getProductProps($arElementId, array_merge(array("ID"), $arImgFields, $this->arCustomSelectFields));

		foreach ($arBasketItems as &$arItem)
		{

			$quantityIsFloat = false;
			if (number_format(doubleval($arItem['QUANTITY']), 2, '.', '') != intval($arItem['QUANTITY']))
			{
				$quantityIsFloat = true;
			}

			$arItem["QUANTITY"] = ($quantityIsFloat === false && $this->quantityFloat != "Y") ? intval($arItem['QUANTITY']) : number_format(doubleval($arItem['QUANTITY']), 2, '.', '');

			$arItem["PROPS"] = array();

			$dbProp = CSaleBasket::GetPropsList(
				array("SORT" => "ASC", "ID" => "ASC"),
				array("BASKET_ID" => $arItem["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID"))
			);
			while ($arProp = $dbProp->GetNext())
				$arItem["PROPS"][] = $arProp;

			$arItem["PRICE_VAT_VALUE"] = (($arItem["PRICE"] / ($arItem["VAT_RATE"] +1)) * $arItem["VAT_RATE"]);
			$arItem["PRICE_FORMATED"] = SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"]);

			$arItem["WEIGHT"] = doubleval($arItem["WEIGHT"]);
			$arItem["WEIGHT_FORMATED"] = roundEx(doubleval($arItem["WEIGHT"] / $this->weightKoef), SALE_WEIGHT_PRECISION)." ".$this->weightUnit;

			if (CSaleBasketHelper::isSetItem($arItem))
				$arSetParentWeight[$arItem["SET_PARENT_ID"]] += $arItem["WEIGHT"] * $arItem["QUANTITY"];

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
				$arFieldsToFill = array_merge($this->arCustomSelectFields, $arImgFields); // fields to be filled with parents' values if empty
				foreach ($arFieldsToFill as $field)
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

			foreach ($arItem as $key => $value) // format properties' values
			{
				if ((strpos($key, "PROPERTY_", 0) === 0) && (strrpos($key, "_VALUE") == strlen($key) - 6))
				{
					$code = str_replace(array("PROPERTY_", "_VALUE"), "", $key);
					$propData = $this->arIblockProps[$code];
					$arItem[$key] = CSaleHelper::getIblockPropInfo($value, $propData);
				}
			}

			$arItem["PREVIEW_PICTURE_SRC"] = "";
			if (isset($arItem["PREVIEW_PICTURE"]) && intval($arItem["PREVIEW_PICTURE"]) > 0)
			{
				$arImage = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
				if ($arImage)
				{
					$arFileTmp = CFile::ResizeImageGet(
						$arImage,
						array("width" => "110", "height" =>"110"),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);

					$arItem["PREVIEW_PICTURE_SRC"] = $arFileTmp["src"];
				}
			}

			$arItem["DETAIL_PICTURE_SRC"] = "";
			if (isset($arItem["DETAIL_PICTURE"]) && intval($arItem["DETAIL_PICTURE"]) > 0)
			{
				$arImage = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
				if ($arImage)
				{
					$arFileTmp = CFile::ResizeImageGet(
						$arImage,
						array("width" => "110", "height" =>"110"),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);

					$arItem["DETAIL_PICTURE_SRC"] = $arFileTmp["src"];
				}
			}
		}
		unset($arItem);

		// get sku props data
		if (!empty($arBasketItems) && $bUseCatalog && isset($this->offersProps) && !empty($this->offersProps))
		{
			$arBasketItems = $this->getSkuPropsData($arBasketItems, $arParents, $this->offersProps);
		}

		// count weight for set parent products
		foreach ($arBasketItems as &$arItem)
		{
			if (CSaleBasketHelper::isSetParent($arItem))
			{
				$arItem["WEIGHT"] = $arSetParentWeight[$arItem["ID"]] / $arItem["QUANTITY"];
				$arItem["WEIGHT_FORMATED"] = roundEx(doubleval($arItem["WEIGHT"] / $this->weightKoef), SALE_WEIGHT_PRECISION)." ".$this->weightUnit;
			}
		}

		if (isset($arItem))
		{
			unset($arItem);
		}
		// fill item arrays for old templates
		foreach ($arBasketItems as &$arItem)
		{
			if (CSaleBasketHelper::isSetItem($arItem))
				continue;

			if ($arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "N")
			{
				$allSum += ($arItem["PRICE"] * $arItem["QUANTITY"]);
				$allWeight += ($arItem["WEIGHT"] * $arItem["QUANTITY"]);
				$allVATSum += roundEx($arItem["PRICE_VAT_VALUE"] * $arItem["QUANTITY"], SALE_VALUE_PRECISION);

				$bShowReady = True;
				if(doubleval($arItem["DISCOUNT_PRICE"]) > 0)
				{
					if (0 < doubleval($arItem["DISCOUNT_PRICE"] + $arItem["PRICE"]))
					{
						$arItem["DISCOUNT_PRICE_PERCENT"] = $arItem["DISCOUNT_PRICE"]*100 / ($arItem["DISCOUNT_PRICE"] + $arItem["PRICE"]);
					}
					else
					{
						$arItem["DISCOUNT_PRICE_PERCENT"] = 0;
					}
					$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arItem["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
					$DISCOUNT_PRICE_ALL += $arItem["DISCOUNT_PRICE"] * $arItem["QUANTITY"];
				}

				$arResult["ITEMS"]["AnDelCanBuy"][] = $arItem;
			}
			elseif ($arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "Y")
			{
				$bShowDelay = True;

				$arResult["ITEMS"]["DelDelCanBuy"][] = $arItem;
			}
			elseif ($arItem["CAN_BUY"] == "N" && $arItem["SUBSCRIBE"] == "Y")
			{
				$bShowSubscribe = True;

				$arResult["ITEMS"]["ProdSubscribe"][] = $arItem;
			}
			else
			{
				$bShowNotAvail = True;
				$arItem["NOT_AVAILABLE"] = true;

				$arResult["ITEMS"]["nAnCanBuy"][] = $arItem;
			}
		}
		unset($arItem);

		$arResult["ShowReady"] = (($bShowReady)?"Y":"N");
		$arResult["ShowDelay"] = (($bShowDelay)?"Y":"N");
		$arResult["ShowNotAvail"] = (($bShowNotAvail)?"Y":"N");
		$arResult["ShowSubscribe"] = (($bShowSubscribe)?"Y":"N");

		$arOrder = array(
			'SITE_ID' => SITE_ID,
			'USER_ID' => $GLOBALS["USER"]->GetID(),
			'ORDER_PRICE' => $allSum,
			'ORDER_WEIGHT' => $allWeight,
			'BASKET_ITEMS' => $arResult["ITEMS"]["AnDelCanBuy"]
		);

		$arOptions = array(
			'COUNT_DISCOUNT_4_ALL_QUANTITY' => $this->countDiscount4AllQuantity,
		);

		$arErrors = array();

		CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

		$allSum = 0;
		$allWeight = 0;
		$allVATSum = 0;

		$DISCOUNT_PRICE_ALL = 0;
		$priceWithoutDiscount = 0;

		foreach ($arOrder["BASKET_ITEMS"] as &$arOneItem)
		{
			$allWeight += ($arOneItem["WEIGHT"] * $arOneItem["QUANTITY"]);
			$allSum += ($arOneItem["PRICE"] * $arOneItem["QUANTITY"]);

			if (array_key_exists('VAT_VALUE', $arOneItem))
				$arOneItem["PRICE_VAT_VALUE"] = $arOneItem["VAT_VALUE"];
			$allVATSum += roundEx($arOneItem["PRICE_VAT_VALUE"] * $arOneItem["QUANTITY"], SALE_VALUE_PRECISION);
			$arOneItem["PRICE_FORMATED"] = SaleFormatCurrency($arOneItem["PRICE"], $arOneItem["CURRENCY"]);

			$arOneItem["FULL_PRICE"] = $arOneItem["PRICE"] + $arOneItem["DISCOUNT_PRICE"];
			$arOneItem["FULL_PRICE_FORMATED"] = SaleFormatCurrency($arOneItem["FULL_PRICE"], $arOneItem["CURRENCY"]);

			$arOneItem["SUM"] = SaleFormatCurrency($arOneItem["PRICE"] * $arOneItem["QUANTITY"], $arOneItem["CURRENCY"]);

			if (0 < doubleval($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]))
			{
				$arOneItem["DISCOUNT_PRICE_PERCENT"] = $arOneItem["DISCOUNT_PRICE"]*100 / ($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]);
			}
			else
			{
				$arOneItem["DISCOUNT_PRICE_PERCENT"] = 0;
			}
			$arOneItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arOneItem["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
			$DISCOUNT_PRICE_ALL += $arOneItem["DISCOUNT_PRICE"] * $arOneItem["QUANTITY"];
		}
		unset($arOneItem);

		$arResult["ITEMS"]["AnDelCanBuy"] = $arOrder["BASKET_ITEMS"];

		// fill grid data (for new templates with custom columns)
		foreach ($arResult["ITEMS"] as $type => $arItems)
		{
			foreach ($arItems as $k => $arItem)
			{
				$arResult["GRID"]["ROWS"][$arItem["ID"]] = $arItem;
			}
		}

		$arResult["allSum"] = $allSum;
		$arResult["allWeight"] = $allWeight;
		$arResult["allWeight_FORMATED"] = roundEx(doubleval($allWeight/$this->weightKoef), SALE_WEIGHT_PRECISION)." ".$this->weightUnit;
		$arResult["allSum_FORMATED"] = SaleFormatCurrency($allSum, $allCurrency);
		$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $allCurrency);
		$arResult["PRICE_WITHOUT_DISCOUNT"] = SaleFormatCurrency($allSum + $DISCOUNT_PRICE_ALL, $allCurrency);

		if ($this->priceVatShowValue == 'Y')
		{
			$arResult["allVATSum"] = $allVATSum;
			$arResult["allVATSum_FORMATED"] = SaleFormatCurrency($allVATSum, $allCurrency);
			$arResult["allSum_wVAT_FORMATED"] = SaleFormatCurrency(doubleval($arResult["allSum"]-$allVATSum), $allCurrency);
		}

		if ($this->hideCoupon != "Y")
			$arCoupons = CCatalogDiscountCoupon::GetCoupons();

		if (count($arCoupons) > 0)
			$arResult["COUPON"] = htmlspecialcharsbx($arCoupons[0]);
		if(count($arBasketItems)<=0)
			$arResult["ERROR_MESSAGE"] = GetMessage("SALE_EMPTY_BASKET");

		$arResult["DISCOUNT_PRICE_ALL"] = $DISCOUNT_PRICE_ALL;
		$arResult["DISCOUNT_PRICE_ALL_FORMATED"] = SaleFormatCurrency($DISCOUNT_PRICE_ALL, $allCurrency);

		if($this->usePrepayment == "Y")
		{
			if(doubleval($arResult["allSum"]) > 0)
			{
				$personType = array();
				$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("LID" => SITE_ID, "ACTIVE" => "Y"));
				while($arPersonType = $dbPersonType->Fetch())
				{
					$personType[] = $arPersonType["ID"];
				}

				if(!empty($personType))
				{
					$dbPaySysAction = CSalePaySystemAction::GetList(
							array(),
							array(
									"PS_ACTIVE" => "Y",
									"HAVE_PREPAY" => "Y",
									"PERSON_TYPE_ID" => $personType,
								),
							false,
							false,
							array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "NAME", "ACTION_FILE", "RESULT_FILE", "NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP")
						);
					if ($arPaySysAction = $dbPaySysAction->Fetch())
					{
						CSalePaySystemAction::InitParamarrays(false, false, $arPaySysAction["PARAMS"]);

						$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

						$pathToAction = str_replace("\\", "/", $pathToAction);
						while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
							$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

						if (file_exists($pathToAction))
						{
							if (is_dir($pathToAction) && file_exists($pathToAction."/pre_payment.php"))
								$pathToAction .= "/pre_payment.php";

							include_once($pathToAction);
							$psPreAction = new CSalePaySystemPrePayment;

							if($psPreAction->init())
							{
								$orderData = array(
										"PATH_TO_ORDER" => $this->pathToOrder,
										"AMOUNT" => $arResult["allSum"],
										"BASKET_ITEMS" => $arResult["ITEMS"]["AnDelCanBuy"],
									);
								if(!$psPreAction->BasketButtonAction($orderData))
								{
									if($e = $APPLICATION->GetException())
										$arResult["WARNING_MESSAGE"][] = $e->GetString();
								}

								$arResult["PREPAY_BUTTON"] = $psPreAction->BasketButtonShow();
							}
						}
					}
				}
			}
		}

		return $arResult;
	}

	public function getSkuPropsData($arBasketItems, $arParents, $arSkuProps = array())
	{
		$bUseHLIblock = CModule::IncludeModule('highloadblock');

		$arRes = array();
		$arSkuIblockID = array();

		if (is_array($arParents))
		{
			foreach ($arBasketItems as &$arItem)
			{
				if (array_key_exists($arItem["PRODUCT_ID"], $arParents))
				{
					$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParents[$arItem["PRODUCT_ID"]]["IBLOCK_ID"]);

					if (!array_key_exists($arSKU["IBLOCK_ID"], $arSkuIblockID))
						$arSkuIblockID[$arSKU["IBLOCK_ID"]] = $arSKU;

					$arItem["IBLOCK_ID"] = $arSKU["IBLOCK_ID"];
					$arItem["SKU_PROPERTY_ID"] = $arSKU["SKU_PROPERTY_ID"];
				}
			}
			unset($arItem);

			foreach ($arSkuIblockID as $skuIblockID => $arSKU)
			{
				// possible props values
				$rsProps = CIBlockProperty::GetList(
					array('SORT' => 'ASC', 'ID' => 'ASC'),
					array('IBLOCK_ID' => $skuIblockID, 'ACTIVE' => 'Y')
				);

				while ($arProp = $rsProps->Fetch())
				{
					if ($arProp['PROPERTY_TYPE'] == 'L' || $arProp['PROPERTY_TYPE'] == 'E' || ($arProp['PROPERTY_TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory'))
					{
						if ($arProp['XML_ID'] == 'CML2_LINK')
							continue;

						if (!in_array($arProp['CODE'], $arSkuProps))
							continue;

						$arValues = array();

						if ($arProp['PROPERTY_TYPE'] == 'L')
						{
							$arValues = array();
							$rsPropEnums = CIBlockProperty::GetPropertyEnum($arProp['ID']);
							while ($arEnum = $rsPropEnums->Fetch())
							{
								$arValues['n'.$arEnum['ID']] = array(
									'ID' => $arEnum['ID'],
									'NAME' => $arEnum['VALUE'],
									'PICT' => false
								);
							}
						}
						elseif ($arProp['PROPERTY_TYPE'] == 'E')
						{

							$rsPropEnums = CIBlockElement::GetList(
								array('SORT' => 'ASC'),
								array('IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y'),
								false,
								false,
								array('ID', 'NAME', 'PREVIEW_PICTURE')
							);
							while ($arEnum = $rsPropEnums->Fetch())
							{
								$arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['PREVIEW_PICTURE']);

								if (!is_array($arEnum['PREVIEW_PICTURE']))
								{
									$arEnum['PREVIEW_PICTURE'] = false;
								}

								if ($arEnum['PREVIEW_PICTURE'] !== false)
								{
									$productImg = CFile::ResizeImageGet($arEnum['PREVIEW_PICTURE'], array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
									$arEnum['PREVIEW_PICTURE']['SRC'] = $productImg['src'];
								}

								$arValues['n'.$arEnum['ID']] = array(
									'ID' => $arEnum['ID'],
									'NAME' => $arEnum['NAME'],
									'SORT' => $arEnum['SORT'],
									'PICT' => $arEnum['PREVIEW_PICTURE']
								);
							}

						}
						elseif ($arProp['PROPERTY_TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory')
						{
							if ($bUseHLIblock)
							{
								$hlblock = HL\HighloadBlockTable::getList(array("filter" => array("TABLE_NAME" => $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"])))->fetch();
								if ($hlblock)
								{
									$entity = HL\HighloadBlockTable::compileEntity($hlblock);
									$entity_data_class = $entity->getDataClass();
									$rsData = $entity_data_class::getList();

									while ($arData = $rsData->fetch())
									{
										$arValues['n'.$arData['ID']] = array(
											'ID' => $arData['ID'],
											'NAME' => $arData['UF_NAME'],
											'SORT' => $arData['UF_SORT'],
											'FILE' => $arData['UF_FILE'],
											'PICT' => '',
											'XML_ID' => $arData['UF_XML_ID']
										);
									}

								}
							}
						}

						if (!empty($arValues) && is_array($arValues))
						{
							$arRes[$skuIblockID][$arProp['ID']] = array(
								'ID' => $arProp['ID'],
								'CODE' => $arProp['CODE'],
								'NAME' => $arProp['NAME'],
								'TYPE' => $arProp['PROPERTY_TYPE'],
								'VALUES' => $arValues
							);
						}
					}
				}
			}

			foreach ($arBasketItems as &$arItem)
			{

				if (isset($arItem["IBLOCK_ID"]) && intval($arItem["IBLOCK_ID"]) > 0 && array_key_exists($arItem["IBLOCK_ID"], $arRes))
				{
					$arItem["SKU_DATA"] = $arRes[$arItem["IBLOCK_ID"]];

					$arUsedValues = array();
					$arTmpRes = array();

					$arOfFilter = array(
						"IBLOCK_ID" => $arItem["IBLOCK_ID"],
						"PROPERTY_".$arSkuIblockID[$arItem["IBLOCK_ID"]]["SKU_PROPERTY_ID"] => $arParents[$arItem["PRODUCT_ID"]]["PRODUCT_ID"]
					);

					$rsOffers = CIBlockElement::GetList(
						array(),
						$arOfFilter,
						false,
						false,
						array("ID", "IBLOCK_ID")
					);
					while ($obOffer = $rsOffers->GetNextElement())
					{
						$arProps = $obOffer->GetProperties();

						foreach ($arProps as $propName => $propValue)
						{
							if (in_array($propName, $arSkuProps))
							{
								if (array_key_exists('VALUE', $propValue))
								{
									if (strlen($propValue['VALUE']) > 0 && (!is_array($arUsedValues[$arItem["PRODUCT_ID"]][$propName]) || !in_array($propValue['VALUE'], $arUsedValues[$arItem["PRODUCT_ID"]][$propName])))
									{
										$arUsedValues[$arItem["PRODUCT_ID"]][$propName][] = $propValue['VALUE'];
									}
								}
							}
						}
					}

					if (!empty($arUsedValues))
					{
						// add only used values to the item SKU_DATA
						foreach ($arRes[$arItem["IBLOCK_ID"]] as $propId => $arProp)
						{
							if (!array_key_exists($arProp["CODE"], $arUsedValues[$arItem["PRODUCT_ID"]]))
							{
								continue;
							}

							$arTmpRes['n'.$propId] = array();
							foreach ($arProp["VALUES"] as $valId => $arValue)
							{
								// properties of various type have different values in the used values data
								if (($arProp["TYPE"] == "L" && in_array($arValue["NAME"], $arUsedValues[$arItem["PRODUCT_ID"]][$arProp["CODE"]]))
									|| ($arProp["TYPE"] == "E" && in_array($arValue["ID"], $arUsedValues[$arItem["PRODUCT_ID"]][$arProp["CODE"]]))
									|| ($arProp["TYPE"] == "S" && in_array($arValue["XML_ID"], $arUsedValues[$arItem["PRODUCT_ID"]][$arProp["CODE"]]))
								)
								{
									if ($arProp["TYPE"] == "S")
									{
										$arTmpFile = CFile::GetFileArray($arValue["FILE"]);
										$tmpImg = CFile::ResizeImageGet($arTmpFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
										$arValue['PICT']['SRC'] = $tmpImg['src'];
									}

									$arTmpRes['n'.$propId]["CODE"] = $arProp["CODE"];
									$arTmpRes['n'.$propId]["NAME"] = $arProp["NAME"];
									$arTmpRes['n'.$propId]["VALUES"][$valId] = $arValue;
								}
							}
						}
					}

					$arItem["SKU_DATA"] = $arTmpRes;
				}
			}

			unset($arItem);
		}

		return $arBasketItems;
	}

	public function getAvailableQuantity($arBasketItems)
	{
		if (CModule::IncludeModule("catalog"))
		{
			$arElementId = array();
			foreach ($arBasketItems as $arItem)
				$arElementId[] = $arItem["PRODUCT_ID"];

			if (!empty($arElementId))
			{
				$dbres = CCatalogProduct::GetList(
					array(),
					array("ID" => array_unique($arElementId)),
					false,
					false,
					array("ID", "QUANTITY")
				);
				while ($arRes = $dbres->GetNext())
				{
					foreach ($arBasketItems as &$basketItem)
					{
						if ($basketItem["PRODUCT_ID"] == $arRes["ID"])
							$basketItem["AVAILABLE_QUANTITY"] = $arRes["QUANTITY"];
					}
					unset($basketItem);
				}
			}

			return $arBasketItems;
		}
		else
			return false;
	}

	public function recalculateBasket($arPost)
	{
		global $USER;
		$arRes = array();

		if ($this->hideCoupon != "Y")
		{
			if (strlen($arPost["coupon"]) > 0)
				$arRes["VALID_COUPON"] = CCatalogDiscountCoupon::SetCoupon($arPost["coupon"]);

			if (!isset($arRes["VALID_COUPON"]) || (isset($arRes["VALID_COUPON"]) && $arRes["VALID_COUPON"] === false))
			{
				CCatalogDiscountCoupon::ClearCoupon();
			}
		}

		$arTmpItems = array();
		$dbItems = CSaleBasket::GetList(
			array("PRICE" => "DESC"),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			array(
				"ID", "NAME", "PRODUCT_PROVIDER_CLASS", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID",
				"QUANTITY", "DELAY", "CAN_BUY", "CURRENCY", "SUBSCRIBE", "TYPE", "SET_PARENT_ID"
			)
		);
		while ($arItem = $dbItems->Fetch())
		{
			if (CSaleBasketHelper::isSetItem($arItem))
				continue;

			$arTmpItems[] = $arItem;
		}

		if (!empty($arTmpItems) && CModule::IncludeModule("catalog"))
			$arTmpItems = getRatio($arTmpItems);

		foreach ($arTmpItems as $arItem)
		{
			$isFloatQuantity = ((isset($arItem["MEASURE_RATIO"]) && floatval($arItem["MEASURE_RATIO"]) > 0 && $arItem["MEASURE_RATIO"] != 1)
				|| $this->quantityFloat == "Y") ? true : false;

			if (!isset($arPost["QUANTITY_".$arItem["ID"]]) || floatval($arPost["QUANTITY_".$arItem["ID"]]) <= 0)
			{
				$quantityTmp = ($isFloatQuantity === true) ? floatval($arItem["QUANTITY"]) : intval($arItem["QUANTITY"]);
			}
			else
			{
				$quantityTmp = ($isFloatQuantity === true) ? floatval($arPost["QUANTITY_".$arItem["ID"]]) : intval($arPost["QUANTITY_".$arItem["ID"]]);
			}

			$deleteTmp = ($arPost["DELETE_".$arItem["ID"]] == "Y") ? "Y" : "N";
			$delayTmp = ($arPost["DELAY_".$arItem["ID"]] == "Y") ? "Y" : "N";

			if ($arItem["CAN_BUY"] == "Y")
			{
				$res = $this->checkQuantity($arItem, $quantityTmp);

				if (!empty($res))
					$arRes["WARNING_MESSAGE"][] = $res["ERROR"];
			}

			if ($deleteTmp == "Y" && in_array("DELETE", $this->columns))
			{
				if ($arItem["SUBSCRIBE"] == "Y" && is_array($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()]))
					unset($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()][$arItem["PRODUCT_ID"]]);

				CSaleBasket::Delete($arItem["ID"]);
			}
			elseif ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y")
			{
				unset($arFields);
				$arFields = array();

				if (in_array("QUANTITY", $this->columns))
					$arFields["QUANTITY"] = $quantityTmp;
				if (in_array("DELAY", $this->columns))
					$arFields["DELAY"] = $delayTmp;

				if (count($arFields) > 0
					&&
						($arItem["QUANTITY"] != $arFields["QUANTITY"] && in_array("QUANTITY", $this->columns)
							|| $arItem["DELAY"] != $arFields["DELAY"] && in_array("DELAY", $this->columns))
					)
					CSaleBasket::Update($arItem["ID"], $arFields);
			}
			elseif ($arItem["DELAY"] == "Y" && $arItem["CAN_BUY"] == "Y")
			{
				unset($arFields);
				$arFields = array();

				if (in_array("DELAY", $this->columns))
					$arFields["DELAY"] = $delayTmp;

				if (count($arFields) > 0
					&&
						($arItem["DELAY"] != $arFields["DELAY"] && in_array("DELAY", $this->columns))
					)
					CSaleBasket::Update($arItem["ID"], $arFields);
			}
		}

		return $arRes;
	}

	public function checkQuantity($arBasketItem, $desiredQuantity)
	{
		global $USER;
		$arResult = array();

		/** @var $productProvider IBXSaleProductProvider */
		if ($productProvider = CSaleBasket::GetProductProvider($arBasketItem))
		{
			$arFieldsTmp = $productProvider::GetProductData(array(
				"PRODUCT_ID" => $arBasketItem["PRODUCT_ID"],
				"QUANTITY"   => $desiredQuantity,
				"RENEWAL"    => "N",
				"USER_ID"    => $USER->GetID(),
				"SITE_ID"    => SITE_ID,
				"CHECK_QUANTITY" => "Y",
				"CHECK_PRICE" => "N"
			));
		}
		elseif (isset($arBasketItem["CALLBACK_FUNC"]) && strlen($arBasketItem["CALLBACK_FUNC"]) > 0)
		{
			$arFieldsTmp = CSaleBasket::ExecuteCallbackFunction(
				$arBasketItem["CALLBACK_FUNC"],
				$arBasketItem["MODULE"],
				$arBasketItem["PRODUCT_ID"],
				$desiredQuantity,
				"N",
				$USER->GetID(),
				SITE_ID
			);
		}
		else
			return $arResult;

		if (empty($arFieldsTmp) || !isset($arFieldsTmp["QUANTITY"]))
		{
			$arResult["ERROR"] = GetMessage("SBB_PRODUCT_NOT_AVAILABLE", array("#PRODUCT#" => $arBasketItem["NAME"]));
		}
		else if ($desiredQuantity > doubleval($arFieldsTmp["QUANTITY"]))
		{
			$arResult["ERROR"] = GetMessage("SBB_PRODUCT_NOT_ENOUGH_QUANTITY", array("#PRODUCT#" => $arBasketItem["NAME"], "#NUMBER#" => $desiredQuantity));
		}

		return $arResult;
	}
}

?>