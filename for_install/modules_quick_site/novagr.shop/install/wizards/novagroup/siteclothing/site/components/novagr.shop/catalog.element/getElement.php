<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// main gelist
$rsElement = CIBlockElement::GetList(
	array($arParams['SORT_FIELD'] => $arParams['SORT_BY']),
	$arFilter,
	false,
	$arNavStartParams,
	$arSelect
);

$arElements = array();

$rsElement->SetUrlTemplates(SITE_DIR.'catalog/#SECTION_CODE#/#ELEMENT_CODE#/');

if ($obElement = $rsElement->GetNextElement())
{
    $data = $obElement->GetFields();
    $arResult['ID'] = $data["ID"];
    $arResult['DETAIL_PICTURE'] = $data["DETAIL_PICTURE"];
    $arResult['IBLOCK_ID'] = $data["IBLOCK_ID"];
    $arResult['IBLOCK_SECTION_ID'] = $data["IBLOCK_SECTION_ID"];

	// for landing get current section
    if ($arParams["LANDING_PAGE"] == "Y") {
       $arSelectSections = array( 'ID', 'NAME', 'SORT', 'CODE' );
        $arFilterSections = array(
            "ID" => $data['IBLOCK_SECTION_ID'],
            "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"]
        );

        $rsSection = CIBlockSection::GetList(
            Array("SORT"=>"ASC"), $arFilterSections, false, $arSelectSections
        );
        if ($currentSection = $rsSection -> Fetch()) {
            $arResult['PRODUCT_SECTION'] = $currentSection;
        }
    }

    if (!empty($arParams["LANDING_IBLOCK_ID"])) {

    }

    $data["PROPERTIES"] = $obElement->GetProperties();

	// working with photos
	// maximum 10 colors
	
	$arResult["USE_MORE_PHOTO"] = true;
	for ($i=1; $i<11; $i++) {
		if (!empty($data["PROPERTIES"]["PHOTONAME_COLOR_".$i]["VALUE"])) {
			
			$arResult["USE_MORE_PHOTO"] = false;
			$curColor = $data["PROPERTIES"]["PHOTONAME_COLOR_".$i]["VALUE"];

            if(is_array($data["PROPERTIES"]["PHOTO_COLOR_".$i]["VALUE"]))
			foreach ($data["PROPERTIES"]["PHOTO_COLOR_".$i]["VALUE"] as $photoId) {
				
				//$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($photoId,array('WIDTH'=>'450', 'HEIGHT'=>'580'));
				//$arResult["ELEMENT_PHOTO_MIDDLE"][$photoId] = $arFileTmp;
				$arResult["ELEMENT_PHOTO_MIDDLE"][$photoId] = array('src' => CFile::GetPath($photoId));
				
				$arResult["ELEMENT_COLORS_PHOTOS"][$curColor][] = $photoId;
				
				$FILE = CFile::GetFileArray($photoId);
				if (is_array($FILE)) {
					$FILE["src"] = $FILE["SRC"];
					$FILE["height"] = $FILE["HEIGHT"];
					$arResult["ELEMENT_PHOTO"][$photoId] = $FILE;
					
				}
				
				//$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($photoId,array('WIDTH'=>'86', 'HEIGHT'=>'114'));
				//$arResult['PREVIEW_PICTURE'][$photoId] = $arFileTmp["src"];
				$arResult['PREVIEW_PICTURE'][$photoId] = CFile::GetPath($photoId);
			}
		}
	}

	// check detail picture
	if (!empty($arResult["DETAIL_PICTURE"]) && $arResult["USE_MORE_PHOTO"] == true) {
		$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture(
				$arResult["DETAIL_PICTURE"],
				array('WIDTH'=>'450', 'HEIGHT'=>'580')
		);
			
		$arResult["DETAIL_PICTURE_ARR_MIDDLE"] = $arFileTmp;
		
		$FILE = CFile::GetFileArray($arResult["DETAIL_PICTURE"]);
		
		if (is_array($FILE)) {
			$arResult["DETAIL_PICTURE_ARR"] = $FILE;
			$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($arResult["DETAIL_PICTURE"],array('WIDTH'=>'86', 'HEIGHT'=>'114'));
			$arResult["DETAIL_PICTURE_MIN_SRC"] = $arFileTmp["src"];
		}
	}
	
	// check MORE_PHOTO 
	if ($arResult["USE_MORE_PHOTO"] == true && !empty($data["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0])) {
		
		foreach ($data["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $photoId) {
		
			$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture(
					$photoId,
					array('WIDTH'=>'450', 'HEIGHT'=>'580')
			);
			
			$arResult["ELEMENT_MORE_PHOTO_MIDDLE"][$photoId] = $arFileTmp;
						
			$FILE = CFile::GetFileArray($photoId);
			if (is_array($FILE)) {
				$FILE["src"] = $FILE["SRC"];
				$FILE["height"] = $FILE["HEIGHT"];
				$arResult["ELEMENT_MORE_PHOTO"][$photoId] = $FILE;				
			}
		
			$arFileTmp = MakeResizePicture($photoId,array('WIDTH'=>'86', 'HEIGHT'=>'114'));
			$arResult['PREVIEW_PICTURE'][$photoId] = $arFileTmp["src"];
		}
	}

	$arParams["PRODUCT_ID_VARIABLE"] = 'id';
	$arParams["ACTION_VARIABLE"] = 'action';
	
	$arParams['ELEMENT_ID'] =  $data['ID'];
	$arParams['ELEMENT_NAME'] = $data['NAME'];
			
	// working with TP
	$arParams["OFFERS_FIELD_CODE"] = array("NAME","PROPERTY_COLOR.ID");
	$arParams["OFFERS_PROPERTY_CODE"] = array("STD_SIZE", "COLOR");
	
	$arResult["CURRENT_ELEMENT"]["COLORS"] = array();
	$arResult["CURRENT_ELEMENT"]["STD_SIZE"] = array();
	$arResult["OFFERS"] = array();
	
	// get the code price
    if (!empty($arParams["OPT_PRICE_ID"]) && $arResult['OPT_USER'] == 1) {
        $pr = CCatalogGroup::GetByID($arParams["OPT_PRICE_ID"]);

        $arParams["PRICE_CODE"] = $pr["NAME"];
        $arResult["CUR_PRICE_CODE"] = $arParams["PRICE_CODE"];
    } else {
        $pr = CCatalogGroup::GetBaseGroup();
        $arResult["CUR_PRICE_CODE"] = $pr["NAME"];
    }

    $arResult["CUR_PRICE_ARR"] = $pr;
	$arParams["PRICE_CODE"] = array($arResult["CUR_PRICE_CODE"]);

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

	// return 404 
	if (!count($arOffers)) {
        $returnFlag = true;
		return;
	} 
		
	// ID TP with the lowest price
	$arResult["MIN_PRICE_OFFER_ID"] = '';
	$firstOfferFlag = true;
	$minPrice = '';
	
	//$showNullOffers = false;
	//if ($_REQUEST["showoptions"] == 'all') {
	$showNullOffers = true;
	//}
	// check - if all the TP with quantity<0 , then we assume that the parameter showoptions == all
	
	// show TP with quantitiy==0
	/*
	if ($showNullOffers == false) {
		$showNullOffers = true;
		foreach($arOffers as $arOffer)
		{			
			
			if ($arOffer["CATALOG_QUANTITY"] > 0) {
				$showNullOffers = false;
				break;
			}
		}
	}
	*/

	//$maxCurrentSizeCount = 0;
	$j = 0;
	//$arResult["MAX_COUNT_SIZE"] = 0;
	foreach($arOffers as $arOffer)
	{
        if($arOffer['PROPERTY_COLOR_ID'] <= 0) continue;
		// if  showoptions == all then consider proposals without residues
		if ($showNullOffers == false && $arOffer["CATALOG_QUANTITY"] <= 0) continue;
		// find the lowest price among the proposals
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

		// add color in an array of colors for a product
		if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) &&
				!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arResult["CURRENT_ELEMENT"]["COLORS"])) {
			$arResult["CURRENT_ELEMENT"]["COLORS"][] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
		}
		// add size to the array size for the product
		if (!empty($arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]) &&
				!in_array($arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"], $arResult["CURRENT_ELEMENT"]["STD_SIZE"]))
		{
			$arResult["CURRENT_ELEMENT"]["STD_SIZE"][$arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]]["SIZE"] = $arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"];

			// real sizes for this size
			foreach ($arParams["SIZES_CODES"] as $code => $name) {

				if ($arOffer["PROPERTIES"][$code]["VALUE"]) {
					$arResult["CURRENT_ELEMENT"]["STD_SIZE"][$arOffer["PROPERTIES"]["STD_SIZE"]["VALUE"]]["REAL_SIZES"][$code] = $arOffer["PROPERTIES"][$code]["VALUE"];
				}
			}					
		}

		$tmpOffers[] = $arOffer;
		$j++;
	}
	
    if (!is_array($tmpOffers)) {
        $returnFlag = true;
        return;
    }
	
    // put the item with the lowest price in the first place in the array of TP
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

	// getting samples
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
}
?>