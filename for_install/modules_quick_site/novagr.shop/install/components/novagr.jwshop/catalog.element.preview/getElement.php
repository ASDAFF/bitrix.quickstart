<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//deb($arParams);
//deb($_REQUEST);


//deb($arFilter);
//deb($arSelect);
// Главная выборка
$rsElement = CIBlockElement::GetList(
	array($arParams['SORT_DIELD'] => $arParams['SORT_BY']),
	$arFilter,
	false,
	$arNavStartParams,
	$arSelect
);

$arElements = array();

$rsElement->SetUrlTemplates(SITE_DIR.'catalog/#SECTION_CODE#/#ELEMENT_CODE#/');

if ($obElement = $rsElement->GetNextElement())
{
	$arResult = $data = $obElement->GetFields();	
	$data["PROPERTIES"] = $obElement->GetProperties();

	// если в дет. карточке ставим в крошки посл. пункт
	if ($elemFlag == true) {

		
		
		// TODO в настройки параметры
		$arParams["PRODUCT_ID_VARIABLE"] = 'id';
		$arParams["ACTION_VARIABLE"] = 'action';
		
		$arParams['ELEMENT_ID'] =  $data['ID'];
		$arParams['ELEMENT_NAME'] = $data['NAME'];
		
		
		// обрабатываем товарные предложения
		// TODO настройки вынести  CML2_LINK
		$arParams["OFFERS_FIELD_CODE"] = array("NAME");
		$arParams["OFFERS_PROPERTY_CODE"] = array("STD_SIZE", "COLOR_STONE");
		
		$arResult["CURRENT_ELEMENT"]["COLORS"] = array();
		$arResult["CURRENT_ELEMENT"]["STD_SIZE"] = array();
		$arResult["OFFERS"] = array();
		
		// получаем код базовой цены
		$basePrice = CCatalogGroup::GetBaseGroup();
		$arResult["BASE_PRICE_CODE"] = $basePrice["NAME"];
		
		$arParams["PRICE_CODE"] = array($arResult["BASE_PRICE_CODE"]);
		//This function returns array with prices description and access rights
		//in case catalog module n/a prices get values from element properties
		$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["CATALOG_IBLOCK_ID"], $arParams["PRICE_CODE"]);
		$arResult["CAT_PRICES"] = $arResultPrices;
		$arConvertParams = array();

		$arOffers = CIBlockPriceTools::GetOffersArray(
				$arParams["CATALOG_IBLOCK_ID"]
				,array($arParams['ELEMENT_ID'])
				,array(
						$arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
						"ID" => "DESC",
				)
				,$arParams["OFFERS_FIELD_CODE"]
				,$arParams["OFFERS_PROPERTY_CODE"]
				,0 // $arParams["OFFERS_LIMIT"]
				,$arResult["CAT_PRICES"]
				,1 // $arParams['PRICE_VAT_INCLUDE']
				,$arConvertParams
		);
		
		// ID предложения с минимальной ценой
		$arResult["MIN_PRICE_OFFER_ID"] = '';
		$firstOfferFlag = true;
		$minPrice = '';
		
		$showNullOffers = false;
		if ($_REQUEST["showoptions"] == 'all') {
			$showNullOffers = true;
			
		}
		// проверим, если все тп с остатками меньше 0, то считаем, что параметр showoptions == all
		// показываем тп с нулевыми остатками
		if ($showNullOffers == false) {
			$showNullOffers = true;
			foreach($arOffers as $arOffer)
			{
				/*if (is_array($arOffer["DISPLAY_PROPERTIES"]["MORE_PHOTO"]["FILE_VALUE"]))
				foreach ($arOffer["DISPLAY_PROPERTIES"]["MORE_PHOTO"]["FILE_VALUE"] as $photo) {
				    if (!empty($photo["SRC"])) {
				    
				    $photoId = $photo["ID"];
				    $arResult['DETAIL_PICTURE'][$photoId] = $photo["SRC"];
				  //  $arResult['DETAIL_PICTURE_HEIGHT'][$photoId] = $photo["HEIGHT"];
				  }
				}*/
				
				
				if ($arOffer["CATALOG_QUANTITY"] > 0) {
					$showNullOffers = false;
					break;
				}
			}
		}
		
		foreach($arOffers as $arOffer)
		{
			// если параметр showoptions == all то учитываем предложения без остатков
			if ($showNullOffers == false && $arOffer["CATALOG_QUANTITY"] <= 0) continue;
			// находим минимальную цену среди предложений
			if ($arOffer["CATALOG_QUANTITY"] > 0) {
				if ($arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] > 0 && $firstOfferFlag == true) {
					$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
					$arResult["MIN_PRICE_OFFER_ID"] = $arOffer["ID"];
					
					$firstOfferFlag = false;
					
				} elseif ($arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] > 0 && $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] < $minPrice) {
					$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
					$arResult["MIN_PRICE_OFFER_ID"] = $arOffer["ID"];
					
				}	
			}
			//deb($arOffer["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE_VAT"]);
			//deb($arOffer["PRICES"]["BASE"]);
			// добавляем цвет в массив цветов для товара
			if (!empty($arOffer["PROPERTIES"]["COLOR_STONE"]["VALUE"]) &&
					!in_array($arOffer["PROPERTIES"]["COLOR_STONE"]["VALUE"], $arResult["CURRENT_ELEMENT"]["COLORS"])) {
				$arResult["CURRENT_ELEMENT"]["COLORS"][] = $arOffer["PROPERTIES"]["COLOR_STONE"]["VALUE"];
			}

			$tmpOffers[] = $arOffer;

		}
		//deb($arResult["MIN_PRICE_OFFER_ID"]);
				
		// ставим элемент с минимальной ценой на первое место в массиве ТП
		$arResult["OFFERS"] = array();
        if(is_array($tmpOffers))
        {
		foreach ($tmpOffers as $key => $item) {

			if ($arResult["MIN_PRICE_OFFER_ID"] == $item["ID"]) {
				$arResult["OFFERS"][] = $item;
				//deb($item);
				unset($tmpOffers[$key]);
				break;
			}
		}
		$arResult["OFFERS"] = array_merge($arResult["OFFERS"], $tmpOffers);
        }


	}
	
	$arElements[ $data['ID'] ] = $data;
}

?>