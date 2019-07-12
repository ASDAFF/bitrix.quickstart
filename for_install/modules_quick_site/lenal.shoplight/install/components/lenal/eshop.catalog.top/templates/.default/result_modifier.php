<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach ($arResult['ITEMS'] as $key => $arElement)
{
	if(is_array($arElement["DETAIL_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}
		
		$arFileTmp = CFile::ResizeImageGet(
			$arElement['DETAIL_PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);
		
		$arResult["ITEMS"][$key]["PREVIEW_IMG"] = array(
			"SRC" => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
}

/*SKU -- */
$arOffersIblock = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffersIblock)? $arOffersIblock["OFFERS_IBLOCK_ID"]: 0;
if ($OFFERS_IBLOCK_ID > 0)
{
	$dbOfferProperties = CIBlock::GetProperties($OFFERS_IBLOCK_ID, Array("SORT"=>"ASC"), Array("!XML_ID" => "CML2_LINK"));
	$arIblockOfferProps = array();
	$offerPropsExists = false;
	while($arOfferProperties = $dbOfferProperties->Fetch())
	{
		if (!in_array($arOfferProperties["CODE"],$arParams["OFFERS_PROPERTY_CODE"]))
			continue;
		$arIblockOfferProps[] = array("CODE" => $arOfferProperties["CODE"], "NAME" => $arOfferProperties["NAME"]);
		$offerPropsExists = true;
	}
	$arResult["POPUP_MESS"] = array(
		"addToCart" => GetMessage("CATALOG_ADD_TO_CART"),
		"inCart" => GetMessage("CATALOG_IN_CART"),
		"delayCart" => GetMessage("CATALOG_IN_CART_DELAY"),
		"subscribe" =>  GetMessage("CATALOG_SUBSCRIBE"),
		"inSubscribe" =>  GetMessage("CATALOG_IN_SUBSCRIBE"),
		"notAvailable" =>  GetMessage("CATALOG_NOT_AVAILABLE"),
		"addCompare" => GetMessage("CATALOG_COMPARE"),
		"inCompare" => GetMessage("CATALOG_IN_COMPARE"),
		"chooseProp" => GetMessage("CATALOG_CHOOSE")
	);
}
/* -- SKU */
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
$pricesCount = count($arResult["PRICES"]);
foreach($arResult["ITEMS"] as $cell=>$arElement)
{
	$arResult["ITEMS"][$cell]["DELETE_COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_LIST&id=".$arElement["ID"], array("action", "id")));
	if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) //Product has offers
	{
		$arIblockOfferPropsTmp = $arIblockOfferProps;
		foreach($arIblockOfferPropsTmp as $key => $arCode)
		{
			$emptyProp = true;
			foreach($arElement["OFFERS"] as $key2=>$arOffer)
			{
				if (array_key_exists($arCode["CODE"], $arOffer["PROPERTIES"]) && !empty($arOffer["PROPERTIES"][$arCode["CODE"]]["VALUE"]))
					$emptyProp = false;
			}
			if ($emptyProp)
			{
				unset($arIblockOfferPropsTmp[$key]);
			}
		}
		$arIblockOfferPropsTmp = array_values($arIblockOfferPropsTmp);

		$arSku = array();
		$minItemPrice = 0;
		$minItemPriceFormat = "";
		$allSkuNotAvailable = true;
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
				foreach($arIblockOfferPropsTmp as $key2 => $arCode)
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
				if (in_array("NAME", $arParams["OFFERS_FIELD_CODE"]))
					$arSkuTmp[] = $arOffer["NAME"];
				else
					break;
			}
			$arSkuTmp["ID"] = $arOffer["ID"];
			foreach ($arOffer["PRICES"] as $code=>$arPrice)
			{
				if($arPrice["CAN_ACCESS"])
				{
					$arSkuTmp["PRICES"][$code]["TITLE"] = ($pricesCount > 1) ? $arResult["PRICES"][$code]["TITLE"] : "";
					if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
					{
						$arSkuTmp["PRICES"][$code]["PRICE"] = $arPrice["PRINT_VALUE"];
						$arSkuTmp["PRICES"][$code]["DISCOUNT_PRICE"] = $arPrice["PRINT_DISCOUNT_VALUE"];
					}
					else
					{
						$arSkuTmp["PRICES"][$code]["PRICE"] = $arPrice["PRINT_VALUE"];
						$arSkuTmp["PRICES"][$code]["DISCOUNT_PRICE"] = "";
					}
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
					elseif ($arBasket["SUBSCRIBE"] == "Y" &&  $arNotify[SITE_ID]['use'] == 'Y')
						$arSkuTmp["CART"] = "inSubscribe";
					else
						$arSkuTmp["CART"] = "inCart";
				}
			}
			$arSkuTmp["CAN_BUY"] = $arOffer["CAN_BUY"];
			if ($arOffer["CAN_BUY"])
				$allSkuNotAvailable = false;
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
			$arResult["ITEMS"][$cell]["MIN_OFFER_PRICE"] = $minItemPrice;
			$arResult["ITEMS"][$cell]["PRINT_MIN_OFFER_PRICE"] = $minItemPriceFormat;
		}
		if ((!is_array($arIblockOfferPropsTmp) || empty($arIblockOfferPropsTmp)) && is_array($arSku) && !empty($arSku))
			$arIblockOfferPropsTmp[] = array("CODE" => "TITLE", "NAME" => GetMessage("CATALOG_OFFER_NAME"));
		$arResult["ITEMS"][$cell]["SKU_ELEMENTS"] = $arSku;
		$arResult["ITEMS"][$cell]["SKU_PROPERTIES"] = $arIblockOfferPropsTmp;
		$arResult["ITEMS"][$cell]["ALL_SKU_NOT_AVAILABLE"] = $allSkuNotAvailable;
	}
	else
	{
		$arPrice = $arElement["PRICES"][$basePriceTypeName];
		if($arPrice["CAN_ACCESS"])
		{
			if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
			{
				$arResult["ITEMS"][$cell]["MIN_PRODUCT_PRICE"] = $arPrice["VALUE"];
				$arResult["ITEMS"][$cell]["MIN_PRODUCT_DISCOUNT_PRICE"] = $arPrice["DISCOUNT_VALUE"];
				$arResult["ITEMS"][$cell]["MIN_PRODUCT_PRICE_PRINT"] = $arPrice["PRINT_VALUE"];
				$arResult["ITEMS"][$cell]["MIN_PRODUCT_DISCOUNT_PRICE_PRINT"] = $arPrice["PRINT_DISCOUNT_VALUE"];
			}
			else
			{
				$arResult["ITEMS"][$cell]["MIN_PRODUCT_PRICE"] = $arPrice["VALUE"];
				$arResult["ITEMS"][$cell]["MIN_PRODUCT_PRICE_PRINT"] = $arPrice["PRINT_VALUE"];
			}
		}
	}
}

// cache hack to use items list in component_epilog.php
$this->__component->arResult["IDS"] = array();
$this->__component->arResult["DELETE_COMPARE_URLS"] = array();
//$this->__component->arResult["OFFERS_IDS"] = array();

if(isset($arParams["DETAIL_URL"]) && strlen($arParams["DETAIL_URL"]) > 0)
	$urlTemplate = $arParams["DETAIL_URL"];
else
	$urlTemplate = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "DETAIL_PAGE_URL");

//2 Sections subtree
$arSections = array();
$rsSections = CIBlockSection::GetList(
	array(),
	array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"LEFT_MARGIN" => $arResult["LEFT_MARGIN"],
		"RIGHT_MARGIN" => $arResult["RIGHT_MARGIN"],
	),
	false,
	array("ID", "DEPTH_LEVEL", "SECTION_PAGE_URL")
);

while($arSection = $rsSections->Fetch())
	$arSections[$arSection["ID"]] = $arSection;

foreach ($arResult["ITEMS"] as $key => $arElement)
{
	$this->__component->arResult["IDS"][] = $arElement["ID"];
	$this->__component->arResult["DELETE_COMPARE_URLS"][$arElement["ID"]] = $arElement["DELETE_COMPARE_URL"];
	/*if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])){
		foreach($arElement["OFFERS"] as $arOffer){
			$this->__component->arResult["OFFERS_IDS"][] = $arOffer["ID"];
		}
	}    */

	if(is_array($arElement["DETAIL_PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}
		$arFileTmp = CFile::ResizeImageGet(
			$arElement["DETAIL_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arResult["ITEMS"][$key]["PREVIEW_IMG"] = array(
			"SRC" => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}

	$section_id = $arElement["~IBLOCK_SECTION_ID"];

	if(array_key_exists($section_id, $arSections))
	{
		$urlSection = str_replace(
			array("#SECTION_ID#", "#SECTION_CODE#"),
			array($arSections[$section_id]["ID"], $arSections[$section_id]["CODE"]),
			$urlTemplate
		);

		$arResult["ITEMS"][$key]["DETAIL_PAGE_URL"] = CIBlock::ReplaceDetailUrl(
			$urlSection,
			$arElement,
			true,
			"E"
		);
	}

}

$this->__component->SetResultCacheKeys(array("IDS"));
$this->__component->SetResultCacheKeys(array("DELETE_COMPARE_URLS"));
//$this->__component->SetResultCacheKeys(array("OFFERS_IDS"));
?>