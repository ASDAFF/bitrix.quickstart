<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$numPrices = count($arResult["PRICES"]);
foreach($arResult["ITEMS"] as $cell=>$arElement)
{
	if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) //Product has offers
	{
		$arOfferProps = array();

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

			$arPrices = array();
			foreach($arOffer["PRICES"] as $code=>$price)
			{
				//$arPrices[$code]["PRICE_NAME"] =
				$arPrices[$code]["PRICE"] = $price["PRINT_VALUE"];
				$arPrices[$code]["DISCOUNT_PRICE"] = ($price["DISCOUNT_VALUE"]<$price["VALUE"]) ? $price["PRINT_DISCOUNT_VALUE"] : "";
				if ($numPrices > 1)
					$arPrices[$code]["TITLE"] = $arResult["PRICES"][$code]["TITLE"];
			}
			$arOfferProps["sku".$arOffer["ID"]]["PRICES"] = $arPrices;
			$arOfferProps["sku".$arOffer["ID"]]["CAN_BUY"] = $arOffer["CAN_BUY"];
			$arOfferProps["sku".$arOffer["ID"]]["ADD_URL"] = $arOffer["ADD_URL"];
			$arOfferProps["sku".$arOffer["ID"]]["DISPLAY_COMPARE"] = $arParams["DISPLAY_COMPARE"];
			$arOfferProps["sku".$arOffer["ID"]]["COMPARE_URL"] = $arOffer["COMPARE_URL"];
			$arOfferProps["sku".$arOffer["ID"]]["COMPARE_PATH"] = SITE_DIR."catalog/compare/";
			$arOfferProps["sku".$arOffer["ID"]]["SUBSCRIBE_URL"] = $arOffer["SUBSCRIBE_URL"];
			$arOfferProps["sku".$arOffer["ID"]]["SORT"] = $arOffer["SORT"];

			foreach ($arOffer["DISPLAY_PROPERTIES"] as $arProp)
			{
				$arOfferProps["sku".$arOffer["ID"]]["PROPS"][] = array("PROP_NAME" => $arProp["NAME"], "PROP_VALUE" => $arProp["VALUE"]);
			}


		}
		if ($minItemPrice > 0)
		{
			$arResult["ITEMS"][$cell]["MIN_PRODUCT_OFFER_PRICE"] = $minItemPrice;
			$arResult["ITEMS"][$cell]["MIN_PRODUCT_OFFER_PRICE_PRINT"] = $minItemPriceFormat;
		}

		$arResult["ITEMS"][$cell]["SKU_OFFERS"] = $arOfferProps;

	}
}

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

	/*if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])){
		foreach($arElement["OFFERS"] as $arOffer){
			$this->__component->arResult["OFFERS_IDS"][] = $arOffer["ID"];
		}
	}                 */
	
	if(is_array($arElement["DETAIL_PICTURE"]))
	{
		$arFilter = '';
		if (empty($arParams["SHARPEN"])) $arParams["SHARPEN"] = "2";
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array("name" => "sharpen", "precision" => $arParams["SHARPEN"]);
		}
		if (empty($arParams["DISPLAY_IMG_WIDTH"])) $arParams["DISPLAY_IMG_WIDTH"] = "220";
		if (empty($arParams["DISPLAY_IMG_HEIGHT"])) $arParams["DISPLAY_IMG_HEIGHT"] = "280";
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
?>