<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach ($arResult["DISPLAY_PROPERTIES"] as $key=>$prop)
{
	if ($prop["PROPERTY_TYPE"] == "F")
		unset($arResult["DISPLAY_PROPERTIES"][$key]);
}

if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]))
{
	$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);
	foreach($arResult["OFFERS"] as $arOffer)
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

				if ($minItemPrice > 0 && $minOfferPrice < $minItemPrice || $minItemPrice == 0)
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
		}
		$arOfferProps["sku".$arOffer["ID"]]["PRICES"] = $arPrices;

		$arOfferProps["sku".$arOffer["ID"]]["CAN_BUY"] = $arOffer["CAN_BUY"];
		$arOfferProps["sku".$arOffer["ID"]]["ADD_URL"] = htmlspecialcharsback($arOffer["ADD_URL"]);
		$arOfferProps["sku".$arOffer["ID"]]["SUBSCRIBE_URL"] = htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]);
	/*	$arOfferProps[$arOffer["ID"]]["COMPARE"] = "";
		if (isset($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arOffer["ID"]]))
			$arOfferProps[$arOffer["ID"]]["COMPARE"] = "inCompare";*/
		$arOfferProps["sku".$arOffer["ID"]]["COMPARE_URL"] = htmlspecialcharsback($arOffer["COMPARE_URL"]);

		foreach ($arOffer["DISPLAY_PROPERTIES"] as $arProp)
		{
			$arOfferProps["sku".$arOffer["ID"]]["PROPS"][] = array("PROP_NAME" => $arProp["NAME"], "PROP_VALUE" => $arProp["VALUE"]);
		}
	}
	$arResult["SKU_OFFERS"] = $arOfferProps;
    $arResult["MIN_PRODUCT_OFFER_PRICE"] = $minItemPrice;
    $arResult["MIN_PRODUCT_OFFER_PRICE_PRINT"] = $minItemPriceFormat;
}

if ($arParams['USE_COMPARE'])
{
	$arResult['COMPARE_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arResult['ID'], array($arParams["ACTION_VARIABLE"], "id")));
}

//recommend
$this->__component->arResult["ACCESSORIES"] = $arResult["DISPLAY_PROPERTIES"]["ACCESSORIES"]["VALUE"];
$this->__component->SetResultCacheKeys(array("ACCESSORIES"));
$this->__component->arResult["SAME_GOODS"] = $arResult["DISPLAY_PROPERTIES"]["SAME_GOODS"]["VALUE"];
$this->__component->SetResultCacheKeys(array("SAME_GOODS"));
?>