<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult as $key => $val)
{
	$img = "";
	if ($val["DETAIL_PICTURE"] > 0)
		$img = $val["DETAIL_PICTURE"];
	elseif ($val["PREVIEW_PICTURE"] > 0)
		$img = $val["PREVIEW_PICTURE"];

	$file = CFile::ResizeImageGet($img, array('width'=>$arParams["VIEWED_IMG_WIDTH"], 'height'=>$arParams["VIEWED_IMG_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);

	$val["PICTURE"] = $file;
	$arResult[$key] = $val;
}
if (CModule::IncludeModule("catalog"))
{
	$basePriceType = CCatalogGroup::GetBaseGroup();
	$basePriceTypeName = $basePriceType["NAME"];
}
/*SKU -- */
$basePriceType = CCatalogGroup::GetBaseGroup();
$basePriceTypeName = $basePriceType["NAME"];

foreach($arResult as $cell=>$arElement)
{
	if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) //Product has offers
	{
		$arOffersIblock = CIBlockPriceTools::GetOffersIBlock($arElement["IBLOCK_ID"]);
		$OFFERS_IBLOCK_ID = is_array($arOffersIblock)? $arOffersIblock["OFFERS_IBLOCK_ID"]: 0;
		if ($OFFERS_IBLOCK_ID > 0)
		{
			$dbOfferProperties = CIBlock::GetProperties($OFFERS_IBLOCK_ID, Array(), Array("!XML_ID" => "CML2_LINK"));
			$arIblockOfferProps = array();
			$offerPropsExists = false;
			while($arOfferProperties = $dbOfferProperties->Fetch())
			{
				$arIblockOfferProps[] = array("CODE" => $arOfferProperties["CODE"], "NAME" => $arOfferProperties["NAME"]);
				$offerPropsExists = true;
			}
		}

		$arSku = array();
		$minItemPrice = 0;
		$minItemPriceFormat = "";
		foreach($arElement["OFFERS"] as $arOffer)
		{
			foreach($arOffer["PRICES"] as $code=>$arPrice)
			{
				if($arPrice["CAN_ACCESS"])
				{
					if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
					{
						$minOfferPrice = $arPrice["DISCOUNT_VALUE"];
						$minOfferPriceFormat = $arPrice["PRINT_DISCOUNT_VALUE"];
					}
					else
					{
						$minOfferPrice = $arPrice["VALUE"];
						$minOfferPriceFormat = $arPrice["PRINT_VALUE"];
					}

					if ($minItemPrice > 0 && $minOfferPrice < $minItemPrice)
					{
						$minItemPrice = $minOfferPrice;
						$minItemPriceFormat = $minOfferPriceFormat;
					}
					elseif ($minItemPrice == 0)
					{
						$minItemPrice = $minOfferPrice;
						$minItemPriceFormat = $minOfferPriceFormat;
					}
				}
			}
			/*SKU -- */
			$arSkuTmp = array();
			if ($offerPropsExists)
			{
				foreach($arIblockOfferProps as $key2 => $arCode)
				{
					if (!array_key_exists($arCode["CODE"], $arOffer["PROPERTIES"]))
					{
						$arSkuTmp[] = GetMessage("EMPTY_VALUE_SKU");
						continue;
					}
					if (empty($arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"]))
						$arSkuTmp[] = GetMessage("EMPTY_VALUE_SKU");
					elseif (is_array($arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"]))
						$arSkuTmp[] = implode("/", $arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"]);
					else
						$arSkuTmp[] = $arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"];
				}
			}
			else
			{
			   // if (in_array("NAME", $arParams["OFFERS_FIELD_CODE"]))
					$arSkuTmp[] = $arOffer["NAME"];
			   // else
				//    break;
			}
			$arSkuTmp["ID"] = $arOffer["ID"];
			if (is_array($arOffer["PRICES"][$basePriceTypeName]))
			{
				if ($arOffer["PRICES"][$basePriceTypeName]["DISCOUNT_VALUE"] < $arOffer["PRICES"][$basePriceTypeName]["VALUE"])
				{
					$arSkuTmp["PRICE"] = $arOffer["PRICES"][$basePriceTypeName]["PRINT_VALUE"];
					$arSkuTmp["DISCOUNT_PRICE"] = $arOffer["PRICES"][$basePriceTypeName]["PRINT_DISCOUNT_VALUE"];
				}
				else
				{
					$arSkuTmp["PRICE"] = $arOffer["PRICES"][$basePriceTypeName]["PRINT_VALUE"];
					$arSkuTmp["DISCOUNT_PRICE"] = "";
				}
			}
			if (CModule::IncludeModule('sale'))
			{
				$dbBasketItems = CSaleBasket::GetList(
					array(
						"ID" => "ASC"
					),
					array(
						"PRODUCT_ID" => $arOffer['ID'],
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL",
					),
					false,
					false,
					array()
				);
				$arSkuTmp["CART"] = "";
				if ($arBasket = $dbBasketItems->Fetch())
				{
					if($arBasket["DELAY"] == "Y")
						$arSkuTmp["CART"] = "delay";
					else
						$arSkuTmp["CART"] = "inCart";
				}
			}
			$arSkuTmp["CAN_BUY"] = $arOffer["CAN_BUY"];
			$arSkuTmp["ADD_URL"] = htmlspecialcharsback($arOffer["ADD_URL"]);
			$arSkuTmp["SUBSCRIBE_URL"] = htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]);
			$arSkuTmp["COMPARE"] = "";
			if (isset($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arOffer["ID"]]))
				$arSkuTmp["COMPARE"] = "inCompare";
			$arSkuTmp["COMPARE_URL"] = htmlspecialcharsback($arOffer["COMPARE_URL"]);
			$arSku[] = $arSkuTmp;
			/* -- SKU*/
		}
		if ($minItemPrice > 0)
		{
			$arResult[$cell]["MIN_PRODUCT_OFFER_PRICE"] = $minItemPrice;
			$arResult[$cell]["MIN_PRODUCT_OFFER_PRICE_PRINT"] = $minItemPriceFormat;
		}
		if ((!is_array($arIblockOfferProps) || empty($arIblockOfferProps)) && is_array($arSku) && !empty($arSku))
			$arIblockOfferProps[] = array("CODE" => "TITLE", "NAME" => GetMessage("CATALOG_OFFER_NAME"));
		$arResult[$cell]["SKU_ELEMENTS"] = $arSku;
		$arResult[$cell]["SKU_PROPERTIES"] = $arIblockOfferProps;
	}
	else
	{
		$arPrice = $arElement["PRICES"][$basePriceTypeName];
		if($arPrice["CAN_ACCESS"])
		{
			if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
			{
				$arResult[$cell]["MIN_PRODUCT_PRICE"] = $arPrice["VALUE"];
				$arResult[$cell]["MIN_PRODUCT_DISCOUNT_PRICE"] = $arPrice["DISCOUNT_VALUE"];
				$arResult[$cell]["MIN_PRODUCT_PRICE_PRINT"] = $arPrice["PRINT_VALUE"];
				$arResult[$cell]["MIN_PRODUCT_DISCOUNT_PRICE_PRINT"] = $arPrice["PRINT_DISCOUNT_VALUE"];
			}
			else
			{
				$arResult[$cell]["MIN_PRODUCT_PRICE"] = $arPrice["VALUE"];
				$arResult[$cell]["MIN_PRODUCT_PRICE_PRINT"] = $arPrice["PRINT_VALUE"];
			}
		}
	}
}
$arTmp = $arResult;
$arResult = array();
$arResult["ITEMS"] = $arTmp;

$arResult["POPUP_MESS"] = array(
	"addToCart" => GetMessage("CATALOG_ADD_TO_CART"),
	"inCart" => GetMessage("CATALOG_IN_CART"),
	"delayCart" => GetMessage("CATALOG_IN_CART_DELAY"),
	"subscribe" =>  GetMessage("CATALOG_SUBSCRIBE"),
	"notAvailable" =>  GetMessage("CATALOG_NOT_AVAILABLE"),
	"addCompare" => GetMessage("CATALOG_COMPARE"),
	"inCompare" => GetMessage("CATALOG_IN_COMPARE"),
	"chooseProp" => GetMessage("CATALOG_CHOOSE")
);

// cache hack to use items list in component_epilog.php
$this->__component->arResult["IDS"] = array();
foreach ($arResult["ITEMS"] as $key => $arElement)
{
	$this->__component->arResult["IDS"][] = $arElement["PRODUCT_ID"];
}
$this->__component->SetResultCacheKeys(array("IDS"));
?>