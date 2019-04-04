<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?

$OPTION_ADD_CART  = COption::GetOptionString("catalog", "default_can_buy_zero");
$OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
$OPTION_CURRENCY  = CCurrency::GetBaseCurrency();

$newFields = array();
$newProperty = array();

foreach($arResult as $key => $val){
	$img = "";
	if ($val["DETAIL_PICTURE"] > 0)
		$img = $val["DETAIL_PICTURE"];
	elseif ($val["PREVIEW_PICTURE"] > 0)
		$img = $val["PREVIEW_PICTURE"];

	$file = CFile::ResizeImageGet($img, array('width'=>$arParams["VIEWED_IMG_WIDTH"], 'height'=>$arParams["VIEWED_IMG_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);

	$val["PICTURE"] = $file;

	$arSelect = Array("ID", "IBLOCK_ID","PROPERTY_*", "CATALOG_QUANTITY");
	$arFilter = Array("ID" => $val["PRODUCT_ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	$arRes = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->GetNextElement();;
	if(!empty($arRes)){
		$newFields   = $arRes->GetFields();
		$newProperty = $arRes->GetProperties();
	}
	$val = array_merge($val, $newFields);
	$val["PROPERTIES"] = $newProperty;
	$val["TMP_PRICE"] = CCatalogProduct::GetOptimalPrice($val["PRODUCT_ID"], 1, $USER->GetUserGroupArray());

	if(empty($val["TMP_PRICE"])){
		$val["PRICE"] = null;
		$val["SKU"] = CCatalogSKU::IsExistOffers($val["PRODUCT_ID"]);
		if($val["SKU"]){ 
			$SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($val["IBLOCK_ID"]);
			if (is_array($SKU_INFO)){
				$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_".$SKU_INFO["SKU_PROPERTY_ID"] => $val["PRODUCT_ID"]), false, array(), array("ID","IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY")); 
				while($arSku = $rsOffers->GetNext()){ 
					$arSkuPrice = CCatalogProduct::GetOptimalPrice($arSku["ID"], 1, $USER->GetUserGroupArray());
					if(!empty($arSkuPrice)){
						$val["SKU_PRODUCT"][] = $arSku + $arSkuPrice;
					}
					
					$val["PRICE"] = ($val["PRICE"] > $arSkuPrice["DISCOUNT_PRICE"] || empty($val["PRICE"])) ? $arSkuPrice["DISCOUNT_PRICE"] : $val["PRICE"];
					$arResult["SKU_PRICES"][] = $arSkuPrice["DISCOUNT_PRICE"];
						
					if($arSku["CATALOG_QUANTITY"] > 0){
						$val["CATALOG_QUANTITY"] = $arSku["CATALOG_QUANTITY"];
					}
				}
				
				$val["SKU_PRICE"] = CurrencyFormat($val["PRICE"], $OPTION_CURRENCY);
			
				if(min($arResult["SKU_PRICES"]) != max($arResult["SKU_PRICES"])){
					$val["SKU_SHOW_FROM"] = true;
				}

				$val["ADDSKU"] = $OPTION_ADD_CART === "Y" ? true : $val["CATALOG_QUANTITY"] > 0;

			}
		}
	}else{

		if($val["TMP_PRICE"]["PRICE"]["CURRENCY"] != $OPTION_CURRENCY){
			$val["TMP_PRICE"]["PRICE"]["PRICE"] = CCurrencyRates::ConvertCurrency($val["TMP_PRICE"]["PRICE"]["PRICE"], $val["TMP_PRICE"]["PRICE"]["CURRENCY"], $OPTION_CURRENCY);
		}

		$val["OLD_PRICE"] = (($val["TMP_PRICE"]["PRICE"]["PRICE"] != $val["TMP_PRICE"]["DISCOUNT_PRICE"]) ? CurrencyFormat($val["TMP_PRICE"]["PRICE"]["PRICE"], $OPTION_CURRENCY) : "");
		$val["PRICE_FORMATED"] = CurrencyFormat($val["TMP_PRICE"]["DISCOUNT_PRICE"], $OPTION_CURRENCY);
	}

	$val["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $val["CATALOG_QUANTITY"] > 0;
	$val["COMPARE"] = false; //!empty
	$arResult[$key] = $val;
}
?>