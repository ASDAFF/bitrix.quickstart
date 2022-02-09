<?
IncludeModuleLangFile(__FILE__);

class CALLSaleProduct
{
	static function GetProductSkuProps($ID, $IBLOCK_ID = '')
	{
		$arSkuProps = array();
		
		if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog'))
		{

			if (IntVal($IBLOCK_ID) <= 0)
			{
				$res = CIBlockElement::GetList(array(), array("ID" => $ID), false, false, array("IBLOCK_ID"));
				$arElement = $res->Fetch();
				$IBLOCK_ID = $arElement["IBLOCK_ID"];
			}

			$arOfferProperties = array();
			$arOfferPropsValue = array();
			$arFilter = array("ID" => $ID, "IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y");
			$arSelect = array("ID" => 1, "IBLOCK_ID" => 1,);

			$arParent = CCatalogSku::GetProductInfo($ID);
			if ($arParent)
			{
				if (!is_array($arOfferProperties[$IBLOCK_ID]) || count($arOfferProperties[$IBLOCK_ID]) <= 0)
				{
					$dbOfferProperties = CIBlock::GetProperties($IBLOCK_ID, Array(), Array("!XML_ID" => "CML2_LINK"));
					while($arTmp = $dbOfferProperties->Fetch())
					{
						$arOfferProperties[$IBLOCK_ID][] = $arTmp;
						$arSelect["PROPERTY_".$arTmp["CODE"]] = 1;
					}
				}

				$rsOffers = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array_keys($arSelect));
				while($obOffer = $rsOffers->GetNextElement())
					$arOfferPropsValue[$ID] = $obOffer->fields;

				if (is_array($arOfferProperties[$IBLOCK_ID]) && count($arOfferProperties[$IBLOCK_ID]) > 0)
				{
					foreach ($arOfferProperties[$IBLOCK_ID] as $val)
						$arSkuProps[$val["NAME"]] = $arOfferPropsValue[$ID]["PROPERTY_".$val["CODE"]."_VALUE"];
				}
			}
		}

		return $arSkuProps;
	}


	/**
	 * get sku for product
	 *
	 * @param integer $USER_ID
	 * @param string  $LID
	 * @param integer $PRODUCT_ID
	 * @param string  $PRODUCT_NAME
	 * @return array
	 */
	function GetProductSku($USER_ID, $LID, $PRODUCT_ID, $PRODUCT_NAME = '', $CURRENCY = '')
	{
		$USER_ID = IntVal($USER_ID);

		$PRODUCT_ID = IntVal($PRODUCT_ID);
		if ($PRODUCT_ID <= 0)
			return false;

		$LID = trim($LID);
		if (strlen($LID) <= 0)
			return false;

		$PRODUCT_NAME = trim($PRODUCT_NAME);
		$arResult = array();
		$arOffers = array();

		$arGroups = CUser::GetUserGroup($USER_ID);

		$dbProduct = CIBlockElement::GetList(array(), array("ID" => $PRODUCT_ID), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
		$arProduct = $dbProduct->Fetch();

		static $arOffersIblock = array();
		if (!is_set($arOffersIblock[$arProduct["IBLOCK_ID"]]))
		{
			$mxResult = CCatalogSKU::GetInfoByProductIBlock($arProduct["IBLOCK_ID"]);
			if (is_array($mxResult))
				$arOffersIblock[$arProduct["IBLOCK_ID"]] = $mxResult["IBLOCK_ID"];
		}

		if ($arOffersIblock[$arProduct["IBLOCK_ID"]] > 0)
		{

			static $arCacheOfferProperties = array();
			if (!is_set($arCacheOfferProperties[$arOffersIblock[$arProduct["IBLOCK_ID"]]]))
			{
				$dbOfferProperties = CIBlock::GetProperties($arOffersIblock[$arProduct["IBLOCK_ID"]], array(), array("!XML_ID" => "CML2_LINK"));
				while($arOfferProperties = $dbOfferProperties->Fetch())
					$arCacheOfferProperties[$arOffersIblock[$arProduct["IBLOCK_ID"]]][] = $arOfferProperties;
			}
			$arOfferProperties = $arCacheOfferProperties[$arOffersIblock[$arProduct["IBLOCK_ID"]]];


			$arIblockOfferProps = array();
			$arIblockOfferPropsFilter = array();
			if (is_array($arOfferProperties))
			{
				foreach ($arOfferProperties as $val)
				{
					$arIblockOfferProps[] = array("CODE" => $val["CODE"], "NAME" => $val["NAME"]);
					$arIblockOfferPropsFilter[] = $val["CODE"];
				}
			}

			$arOffers = CIBlockPriceTools::GetOffersArray(
						$arProduct["IBLOCK_ID"],
						$PRODUCT_ID,
						array("ID" => "DESC"),
						array("NAME"),
						$arIblockOfferPropsFilter,
						0,
						array(),
						1,
						array(),
						$USER_ID,
						$LID
			);
			$arSku = array();
			$minItemPrice = 0;
			$minItemPriceFormat = "";

			$arSkuId = array();
			$arImgSku = array();
			foreach($arOffers as $arOffer)
				$arSkuId[] = $arOffer['ID'];

			if (count($arSkuId) > 0)
			{
				$res = CIBlockElement::GetList(array(), array("ID" => $arSkuId), false, false, array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "IBLOCK_TYPE_ID"));
				while($arOfferImg = $res->Fetch())
					$arImgSku[$arOfferImg["ID"]] = $arOfferImg;
			}		

			foreach($arOffers as $arOffer)
			{
				$arPrice = CCatalogProduct::GetOptimalPrice($arOffer['ID'], 1, $arGroups, "N", array(), $LID);
				if (count($arPrice) <= 0)
				{
					break;
				}
				elseif (strlen($CURRENCY) > 0)
				{
					$arPrice["PRICE"]["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"]["PRICE"], $arPrice["PRICE"]["CURRENCY"], $CURRENCY);
					if ($arPrice["DISCOUNT_PRICE"] > 0)
						$arPrice["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT_PRICE"], $arPrice["PRICE"]["CURRENCY"], $CURRENCY);

					$arPrice["PRICE"]["CURRENCY"] = $CURRENCY;
				}
				$arSkuTmp = array();

				$arOffer["CAN_BUY"] = "N";
				if ($arCatalogProduct = CCatalogProduct::GetByID($arOffer['ID']))
				{
					if ($arCatalogProduct["CAN_BUY_ZERO"]!="Y" && ($arCatalogProduct["QUANTITY_TRACE"]=="Y" && doubleval($arCatalogProduct["QUANTITY"])<=0))
						$arOffer["CAN_BUY"] = "N";
					else
						$arOffer["CAN_BUY"] = "Y";
				}

				$arSkuTmp["ImageUrl"] = '';
				if ($arOffer["CAN_BUY"] == "Y")
				{
					$productImg = "";
					if (isset($arImgSku[$arOffer['ID']]) && count($arImgSku[$arOffer['ID']]) > 0)
					{
						if (strlen($PRODUCT_NAME) <= 0)
							$PRODUCT_NAME = $arImgSku[$arOffer['ID']]["NAME"];

						if($arImgSku[$arOffer['ID']]["PREVIEW_PICTURE"] != "")
							$productImg = $arImgSku[$arOffer['ID']]["PREVIEW_PICTURE"];
						elseif($arImgSku[$arOffer['ID']]["DETAIL_PICTURE"] != "")
							$productImg = $arImgSku[$arOffer['ID']]["DETAIL_PICTURE"];

						if ($productImg == "")
						{
							if($arProduct["PREVIEW_PICTURE"] != "")
								$productImg = $arProduct["PREVIEW_PICTURE"];
							elseif($arProduct["DETAIL_PICTURE"] != "")
								$productImg = $arProduct["DETAIL_PICTURE"];
						}

						if ($productImg != "")
						{
							$arFile = CFile::GetFileArray($productImg);
							$productImg = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
							$arSkuTmp["ImageUrl"] = $productImg["src"];
						}
					}
				}

				if (($minItemPrice === 0) || ($arPrice["DISCOUNT_PRICE"] < $minItemPrice))
				{
					$minItemPrice = $arPrice["DISCOUNT_PRICE"];
					$minItemPriceFormat = SaleFormatCurrency($arPrice["DISCOUNT_PRICE"], $arPrice["PRICE"]["CURRENCY"]);
				}

				foreach($arIblockOfferProps as $arCode)
				{
					if (array_key_exists($arCode["CODE"], $arOffer["PROPERTIES"]))
					{
						if (is_array($arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"]))
							$arSkuTmp[] = implode("/", $arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"]);
						else
							$arSkuTmp[] = $arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"];
					}
				}

				$arCatalogProduct = CCatalogProduct::GetByID($arOffer['ID']);
				$arSkuTmp["BALANCE"] = FloatVal($arCatalogProduct["QUANTITY"]);

				$discountPercent = 0;
				$arSkuTmp["USER_ID"] = $USER_ID;
				$arSkuTmp["ID"] = $arOffer["ID"];
				$arSkuTmp["NAME"] = CUtil::JSEscape($arOffer["NAME"]);
				$arSkuTmp["PRODUCT_NAME"] = CUtil::JSEscape($PRODUCT_NAME);
				$arSkuTmp["PRODUCT_ID"] = $PRODUCT_ID;
				$arSkuTmp["LID"] = CUtil::JSEscape($LID);
				$arSkuTmp["MIN_PRICE"] = $minItemPriceFormat;
				$arSkuTmp["URL_EDIT"] = CUtil::JSEscape("/bitrix/admin/iblock_element_edit.php?ID=".$PRODUCT_ID."&type=".$arImgSku[$arOffer['ID']]["IBLOCK_TYPE_ID"]."&lang=".LANG."&IBLOCK_ID=".$arProduct["IBLOCK_ID"]."&find_section_section=".$arProduct["IBLOCK_SECTION_ID"]);
				$arSkuTmp["DISCOUNT_PRICE"] = '';
				$arSkuTmp["DISCOUNT_PRICE_FORMATED"] = '';
				$arSkuTmp["PRICE"] = $arPrice["PRICE"]["PRICE"];
				$arSkuTmp["PRICE_FORMATED"] = CurrencyFormatNumber($arPrice["PRICE"]["PRICE"], $arPrice["PRICE"]["CURRENCY"]);

				$arPriceType = GetCatalogGroup($arPrice["PRICE"]["CATALOG_GROUP_ID"]);
				$arSkuTmp["PRICE_TYPE"] = $arPriceType["NAME_LANG"];
				$arSkuTmp["VAT_RATE"] = $arPrice["PRICE"]["VAT_RATE"];

				if (count($arPrice["DISCOUNT"]) > 0)
				{
					$discountPercent = IntVal($arPrice["DISCOUNT"]["VALUE"]);

					$arSkuTmp["DISCOUNT_PRICE"] = $arPrice["DISCOUNT_PRICE"];
					$arSkuTmp["DISCOUNT_PRICE_FORMATED"] = CurrencyFormatNumber($arPrice["DISCOUNT_PRICE"], $arPrice["PRICE"]["CURRENCY"]);
				}

				$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPrice["PRICE"]["CURRENCY"]);
				$arSkuTmp["VALUTA_FORMAT"] = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);
				$arSkuTmp["DISCOUNT_PERCENT"] = $discountPercent;
				$arSkuTmp["CURRENCY"] = $arPrice["PRICE"]["CURRENCY"];
				$arSkuTmp["CAN_BUY"] = $arOffer["CAN_BUY"];

				$arSku[] = $arSkuTmp;
			}
			if ((!is_array($arIblockOfferProps) || empty($arIblockOfferProps)) && is_array($arSku) && !empty($arSku))
			{
				$arIblockOfferProps[0] = array("CODE" => "TITLE", "NAME" => GetMessage("SKU_TITLE"));
				foreach ($arSku as $key => $val)
					$arSku[$key][0] = $val["NAME"];
			}

			$arResult["SKU_ELEMENTS"] = $arSku;
			$arResult["SKU_PROPERTIES"] = $arIblockOfferProps;
			$arResult["OFFERS_IBLOCK_ID"] = $arOffersIblock[$arProduct["IBLOCK_ID"]];
		}//if OFFERS_IBLOCK_ID > 0

		return $arResult;
	}



	function RefreshProductList()
	{
		global $DB;
		$strSql = "truncate table b_sale_product2product";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$strSql = "INSERT INTO b_sale_product2product (PRODUCT_ID, PARENT_PRODUCT_ID, CNT)
			select b.PRODUCT_ID as PRODUCT_ID, b1.PRODUCT_ID as PARENT_PRODUCT_ID, COUNT(b1.PRODUCT_ID) as CNT
			from b_sale_basket b
			left join b_sale_basket b1 on (b.ORDER_ID = b1.ORDER_ID)
			inner join b_sale_order o on (o.ID = b.ORDER_ID)
			where
				o.ALLOW_DELIVERY = 'Y'
				AND b.ID <> b1.ID
			GROUP BY b.PRODUCT_ID, b1.PRODUCT_ID";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return "CSaleProduct::RefreshProductList();";
	}

	/**
	 * get recommendet product for product
	 *
	 * @param integer $USER_ID
	 * @param string $LID
	 * @param array $arFilterRecomendet
	 * @param bool $recomMore
	 * @param integer $cntProductDefault
	 * @return array
	 */
	function GetRecommendetProduct($USER_ID, $LID, $arFilterRecomendet = array(), $recomMore = 'N', $cntProductDefault = 2)
	{
		$arRecomendetResult = array();

		if (CModule::IncludeModule('catalog') && CModule::IncludeModule('iblock') && count($arFilterRecomendet) > 0)
		{
			$rsIblock = CIBlock::GetList(array(), array('TYPE'=>'catalog', 'SITE_ID' => $LID, 'ACTIVE'=>'Y', 'CODE' => 'furniture'));
			$arIblock = $rsIblock->Fetch();

			$arFilter = array("ACTIVE"=>"Y", "IBLOCK_ID" => $arIblock["ID"], "ID" => $arFilterRecomendet);
			$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false);
			while ($obElement = $rsElement->GetNextElement())
			{
				$arElementProps = $obElement->GetProperties();
				if (isset($arElementProps["RECOMMEND"]) && is_array($arElementProps["RECOMMEND"]["VALUE"]) > 0)
				{
					foreach($arElementProps["RECOMMEND"]["VALUE"] as $val)
						$arRecomendet[$val] = $val;
				}
			}

			if (count($arRecomendet) > 0)
			{
				$arBuyerGroups = CUser::GetUserGroup($USER_ID);

				$arFilter = array("ACTIVE"=>"Y", "ID" => $arRecomendet);
				$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, array("NAME", "ID", "LID", 'IBLOCK_ID', 'IBLOCK_SECTION_ID', "DETAIL_PICTURE", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));

				while ($arElement = $rsElement->GetNext())
				{
					if (!in_array($arElement["ID"], $arFilterRecomendet))
					{
						if (($recomMore == "N" && $i < $cntProductDefault) || $recomMore == "Y")
						{
							$arElement["MODULE"] = "catalog";
							$arElement["PRODUCT_ID"] = $arElement["ID"];

							$arPrice = CCatalogProduct::GetOptimalPrice($arElement["ID"], 1, $arBuyerGroups, "N", array(), $LID);

							$currentPrice = $arPrice["DISCOUNT_PRICE"];
							$arElement["PRICE"] = $currentPrice;
							$arElement["CURRENCY"] = $arPrice["PRICE"]["CURRENCY"];
							$arElement["DISCOUNT_PRICE"] = $arPrice["PRICE"]["PRICE"] - $arPrice["DISCOUNT_PRICE"];

							if ($arElement["IBLOCK_ID"] > 0 && $arElement["IBLOCK_SECTION_ID"] > 0)
								$arElement["EDIT_PAGE_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$arElement["PRODUCT_ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arElement["IBLOCK_ID"]."&find_section_section=".$arElement["IBLOCK_SECTION_ID"];

							$arRecomendetResult[] = $arElement;
						}
					}
				}
			}

			return $arRecomendetResult;
		}
	}
}

class CAllSaleViewedProduct
{
	/**
	* The function updated viewed product for user
	*
	* @param int $ID - code field for update
	* @param array $arFields - parameters for update
	* @return true false
	*/
	public function Update($ID, $arFields)
	{
		global $DB;

		foreach(GetModuleEvents("sale", "OnBeforeViewedUpdate", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, array($ID, &$arFields))===false)
				return false;

		if (isset($arFields["ID"]))
			unset($arFields["ID"]);

		$strUpdateSql = "";
		if (isset($arFields["DATE_VISIT"]))
		{
			$strUpdateSql .= ", DATE_VISIT = ".$DB->GetNowFunction()." ";
			unset($arFields["DATE_VISIT"]);
		}

		$ID = IntVal($ID);
		$strUpdate = $DB->PrepareUpdate("b_sale_viewed_product", $arFields);

		$strSql = "UPDATE b_sale_viewed_product SET ".
						" ".$strUpdate.$strUpdateSql.
						" WHERE ID = ".$ID." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		foreach(GetModuleEvents("sale", "OnViewedUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return $ID;
	}

	/**
	* The agent function delete old viewed
	*
	* @param
	* @return true false
	*/
	public function ClearViewed()
	{
		CSaleViewedProduct::_ClearViewed();

		return "CSaleViewedProduct::ClearViewed();";
	}
}