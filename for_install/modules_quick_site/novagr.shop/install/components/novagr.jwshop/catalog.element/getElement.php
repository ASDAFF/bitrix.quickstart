<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// main query
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
	//$arResult = $data = $obElement->GetFields();
    $data = $obElement->GetFields();
    $arResult["ID"] = $data["ID"];
    $arResult["DETAIL_PAGE_URL"] = $data["DETAIL_PAGE_URL"];
    $arResult["IBLOCK_ID"] = $data["IBLOCK_ID"];
    $arResult["IBLOCK_SECTION_ID"] = $data["IBLOCK_SECTION_ID"];
    $data["PROPERTIES"] = $obElement->GetProperties();

    $arResult["COLLECTION_NAME"] = $data["PROPERTY_COLLECTION_NAME"];

    //$arResult["SHOW_EDIT_BUTTON"] = $arParams["SHOW_EDIT_BUTTON"];

	// working with photos
	// maximum 10 colors
	$arResult["USE_MORE_PHOTO"] = true;
	for ($i=1; $i<11; $i++) {
		if (!empty($data["PROPERTIES"]["PHOTONAME_COLOR_".$i]["VALUE"])) {
			
			$arResult["USE_MORE_PHOTO"] = false;
			$curColor = $data["PROPERTIES"]["PHOTONAME_COLOR_".$i]["VALUE"];
			
			foreach ($data["PROPERTIES"]["PHOTO_COLOR_".$i]["VALUE"] as $photoId) {

				$FILE = CFile::GetFileArray($photoId);
				if (is_array($FILE)) {
					$arResult["ELEMENT_PHOTO"][$photoId]=$FILE;
					$arResult["ELEMENT_COLORS_PHOTOS"][$curColor][] = $photoId;
				}

				$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($photoId);
				$arResult['PREVIEW_PICTURE'][$photoId] = $arFileTmp["src"];
			}
		}
	}
	
	if (!empty($arResult["DETAIL_PICTURE"]) && $arResult["USE_MORE_PHOTO"] == true) {
		$FILE = CFile::GetFileArray($arResult["DETAIL_PICTURE"]);
		if (is_array($FILE)) {
			$arResult["DETAIL_PICTURE_ARR"] = $FILE;
			$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($arResult["DETAIL_PICTURE"]);
			$arResult["DETAIL_PICTURE_MIN_SRC"] = $arFileTmp["src"];
		}		
	}

	// check MORE_PHOTO
    $arResult["ELEMENT_MORE_PHOTO"] = array();
	if ($arResult["USE_MORE_PHOTO"] == true && !empty($data["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0])) {
	
		foreach ($data["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $photoId) {
	
			$FILE = CFile::GetFileArray($photoId);
			if (is_array($FILE)) {
				$arResult["ELEMENT_MORE_PHOTO"][$photoId] = $FILE;
			}
	
			$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($photoId);
			$arResult['PREVIEW_PICTURE'][$photoId] = $arFileTmp["src"];
		}
	}

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
			
	// если нет тп то отдаем 404
	if (!count($arOffers)) {
        $returnFlag = true;
        /*
		@define("ERROR_404", "Y");
		$returnFlag = true;
		$arResult['SEARCH_NOT_FOUND'] = "N";
		@define("SEARCH_NOT_FOUND", "N");
		$this -> IncludeComponentTemplate('notfound');
		*/
		return;
	} 
		
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
		if (empty($arOffer['DISPLAY_PROPERTIES']['STD_SIZE']))
		{
			$STD_SIZE_DEFAULT = $arOffer['PROPERTIES']['STD_SIZE'];
			$STD_SIZE_DEFAULT['VALUE'] = "-1";
			$STD_SIZE_DEFAULT['~VALUE'] = "-1";
			$STD_SIZE_DEFAULT['SORT'] = "10000";
			$STD_SIZE_DEFAULT['DISPLAY_VALUE'] = GetMessage("NO");
			
			$arOffer['PROPERTIES']['STD_SIZE'] = $STD_SIZE_DEFAULT;
			$arOffer['DISPLAY_PROPERTIES']['STD_SIZE'] = $STD_SIZE_DEFAULT;
		}
		
		$arOffer['DISPLAY_PROPERTIES']['COLOR']=$arOffer['DISPLAY_PROPERTIES']['COLOR_STONE'];
		$arOffer['DISPLAY_PROPERTIES']['COLOR']['CODE'] = 'COLOR';
		
		$COLOR_STONE = $arOffer['PROPERTIES']['COLOR']=$arOffer['PROPERTIES']['COLOR_STONE'];
		$arOffer['PROPERTIES']['COLOR']['CODE'] = 'COLOR';
		
		unset($arOffer['DISPLAY_PROPERTIES']['COLOR_STONE']);
		unset($arOffer['PROPERTIES']['COLOR_STONE']);
		
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

		// добавляем цвет в массив цветов для товара
		if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) &&
				!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arResult["CURRENT_ELEMENT"]["COLORS"])) {
			$arResult["CURRENT_ELEMENT"]["COLORS"][] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
		}
		// добавляем размер в массив размеров для товара
		if (!empty($arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]) &&
				!in_array($arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"], $arResult["CURRENT_ELEMENT"]["STD_SIZE"])) {
			$arResult["CURRENT_ELEMENT"]["STD_SIZE"][$arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]]["SIZE"] = $arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"];
			//	deb($arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]);
			// обмеры для этого размера
			foreach ($arParams["SIZES_CODES"] as $code => $name) {

				if ($arOffer["PROPERTIES"][$code]["VALUE"]) {
					$arResult["CURRENT_ELEMENT"]["STD_SIZE"][$arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]]["REAL_SIZES"][$code] = $arOffer["PROPERTIES"][$code]["VALUE"];
				}
			}					
		}
		$tmpOffers[] = $arOffer;
	}


	if (is_array($arResult["ELEMENT_COLORS_PHOTOS"]) and count($arResult["ELEMENT_COLORS_PHOTOS"]) > 0 )
	{
		/*nothing*/
	} 
	elseif (is_array($arResult["CURRENT_ELEMENT"]["COLORS"]) and count($arResult["CURRENT_ELEMENT"]["COLORS"]) > 0)
	{
		for ($i = 1; $i < 31; $i++) {
			foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $curColor) {
				if (is_array($data["PROPERTIES"]["PHOTO_COLOR_" . $i]["VALUE"])) {
					
					foreach ($data["PROPERTIES"]["PHOTO_COLOR_" . $i]["VALUE"] as $photoId) {
						$FILE = CFile::GetFileArray($photoId);
						if (is_array($FILE)) {
							$arResult["ELEMENT_PHOTO"][$photoId] = $FILE;
							$arResult["ELEMENT_COLORS_PHOTOS"][$curColor][] = $photoId;
						}
						$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($photoId);
						$arResult['PREVIEW_PICTURE'][$photoId] = $arFileTmp["src"];
					}
				}
			}
		}
	}

	//deb($arResult["MIN_PRICE_OFFER_ID"]);
				
	// ставим элемент с минимальной ценой на первое место в массиве ТП
	$arResult["OFFERS"] = array();
	foreach ($tmpOffers as $key => $item) {
		
		if ($arResult["MIN_PRICE_OFFER_ID"] == $item["ID"]) {
			$arResult["OFFERS"][] = $item;
			//deb($item);
			unset($tmpOffers[$key]);
			break;				
		}
	}
	$arResult["OFFERS"] = array_merge($arResult["OFFERS"], $tmpOffers);

	// получаем образцы
	if (count($data["PROPERTY_SAMPLES_VALUE"])) {
		$arSelectS = array( 'ID', 'NAME', 'IBLOCK_ID' );
		$arFilterS = array("IBLOCK_CODE" => $arParams["SAMPLES_IBLOCK_CODE"], "ID" => $data["PROPERTY_SAMPLES_VALUE"]);
			
		$rsElementS = CIBlockElement::GetList(false, $arFilterS, false, false, $arSelectS);
		while ($dataS = $rsElementS -> GetNext())
		{
			$data["SAMPLES"][$dataS["ID"]] = $dataS["NAME"];
		}
	}

    $arElement = $data;

} else {
    $returnFlag = true;
    /*
    @define("ERROR_404", "Y");
	@define("SEARCH_NOT_FOUND", "N");
    $arResult['SEARCH_NOT_FOUND'] = "N";
    $this -> IncludeComponentTemplate('notfound');*/
	return;
}

?>