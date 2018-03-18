<?
Class CKlondikePslider
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while(false !== $item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$MODULE_ID.'_'.$item))
						file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');

					$arFiles[] = $item;
				}

				sort($arFiles);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => $item,
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		$aModuleMenu[] = $aMenu;
	}



	static function getData($arParams, $arrFilter){



		global $APPLICATION;

		$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);

		$arConvertParams = array();
		if ('Y' == $arParams['CONVERT_CURRENCY'])
		{
			if (!CModule::IncludeModule('currency'))
			{
				$arParams['CONVERT_CURRENCY'] = 'N';
				$arParams['CURRENCY_ID'] = '';
			}
			else
			{
				$arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
				if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo)))
				{
					$arParams['CONVERT_CURRENCY'] = 'N';
					$arParams['CURRENCY_ID'] = '';
				}
				else
				{
					$arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
					$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
				}
			}
		}
		/************************************
		Elements
		 ************************************/
		//SELECT
		$arSelect = array(
			"ID",
			"NAME",
			"CODE",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"DETAIL_PAGE_URL",
			"PREVIEW_PICTURE",
			"DETAIL_PICTURE",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PROPERTY_*",
		);
		//WHERE
		$arrFilter["ACTIVE"] = "Y";

		$arrFilter["IBLOCK_LID"] = SITE_ID;
		$arrFilter["IBLOCK_ACTIVE"] = "Y";
		$arrFilter["ACTIVE_DATE"] = "Y";
		$arrFilter["ACTIVE"] = "Y";
		$arrFilter["CHECK_PERMISSIONS"] = "Y";

		//ORDER BY
		$arSort = array(
			$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
			"ID" => "DESC",
		);
		//PRICES

		foreach($arResult["PRICES"] as $key => $value)
		{
			$arSelect[] = $value["SELECT"];
			$arrFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
		}

		$arResult['CONVERT_CURRENCY'] = $arConvertParams;

		if ($arParams['FLAG_PROPERTY_CODE'])
		{
			$arrFilter['!PROPERTY_'.$arParams['FLAG_PROPERTY_CODE']] = false;
		}

		$obParser       = new CTextParser;
		$arImgMaxSizes  = array('width'=> 200, 'height'=>200);

		$arResult["ITEMS"] = array();
		$arResult["IDS"] = array();
		$rsElements = CIBlockElement::GetList($arSort, $arrFilter, false, array("nTopCount" => $arParams["ELEMENT_COUNT"]), $arSelect);
		$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);

		while($obElement = $rsElements->GetNextElement())
		{
			$arItem = $obElement->GetFields();

			//$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

			if($arParams["PREVIEW_TRUNCATE_LEN"] > 0){
				$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);
			}

			if ($arItem["PREVIEW_PICTURE"]){
				$arItem["IMG"] = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], $arImgMaxSizes, BX_RESIZE_IMAGE_PROPORTIONAL, true);
			}else if($arItem["DETAIL_PICTURE"]){
				$arItem["IMG"] = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], $arImgMaxSizes, BX_RESIZE_IMAGE_PROPORTIONAL, true);
			}else{
				$arItem["IMG"] = false;
			}

			$arItem["PROPERTIES"] = $obElement->GetProperties();

			$arItem["DISPLAY_PROPERTIES"] = array();


			$arItem["PRODUCT_PROPERTIES"] = CIBlockPriceTools::GetProductProperties(
				$arParams["IBLOCK_ID"],
				$arItem["ID"],
				$arParams["PRODUCT_PROPERTIES"],
				$arItem["PROPERTIES"]
			);


			$arItem["PRICE_MATRIX"] = false;
			$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);

			$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem);
			$arItem["BUY_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
			$arItem["ADD_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
			$arItem["COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
			$arItem["DELETE_COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arItem['ID'],array("action", "IBLOCK_ID", "ID")));
			$arItem["SUBSCRIBE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=SUBSCRIBE_PRODUCT&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

			$arResult["ITEMS"][]=$arItem;
			$arResult['IDS'][] = $arItem['ID'];
		}
		$arResult["RESULT"] = $rsElements;

		if(!isset($arParams["OFFERS_FIELD_CODE"]))
			$arParams["OFFERS_FIELD_CODE"] = array();
		foreach($arParams["OFFERS_FIELD_CODE"] as $key => $value)
			if($value === "")
				unset($arParams["OFFERS_FIELD_CODE"][$key]);

		if(!isset($arParams["OFFERS_PROPERTY_CODE"]))
			$arParams["OFFERS_PROPERTY_CODE"] = array();
		foreach($arParams["OFFERS_PROPERTY_CODE"] as $key => $value)
			if($value === "")
				unset($arParams["OFFERS_PROPERTY_CODE"][$key]);

		$baseCurrency = CCurrency::GetBaseCurrency();
		if(
			!empty($arResult["IDS"])
			&& (
				!empty($arParams["OFFERS_FIELD_CODE"])
				|| !empty($arParams["OFFERS_PROPERTY_CODE"])
			)
		)
		{
			$arOffers = array();

			$arOffersIblock = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
			$OFFERS_IBLOCK_ID = is_array($arOffersIblock)? $arOffersIblock["OFFERS_IBLOCK_ID"]: 0;

			$arElementsOffer = array();
			foreach($arResult["ITEMS"] as $key2=>$arElement)
				if ($arElement["IBLOCK_ID"] == $arParams['IBLOCK_ID'])
					$arElementsOffer[$key2] = $arElement["ID"];



			$arOffers = CIBlockPriceTools::GetOffersArray(
				$arParams['IBLOCK_ID']
				,$arElementsOffer
				,array(
					$arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
					"ID" => "DESC",
				)
				,$arParams["OFFERS_FIELD_CODE"]
				,$arParams["OFFERS_PROPERTY_CODE"]
				,$arParams["OFFERS_LIMIT"]
				,$arResult["PRICES"]
				,$arParams['PRICE_VAT_INCLUDE']
				,$arConvertParams
			);

			if(!empty($arOffers))
			{
				$arElementOffer = array();
				foreach($arElementsOffer as $i => $id)
				{
					$arResult["ITEMS"][$i]["OFFERS"] = array();
					$arElementOffer[$id] = &$arResult["ITEMS"][$i]["OFFERS"];
				}
				foreach($arOffers as $key=>$arOffer)
				{
					if(array_key_exists($arOffer["LINK_ELEMENT_ID"], $arElementOffer))
					{
						$arOffer["BUY_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
						$arOffer["ADD_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
						$arOffer["COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
						$arOffer["DELETE_COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arOffer["ID"],array("action", "IBLOCK_ID", "ID")));
						$arOffer["SUBSCRIBE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=SUBSCRIBE_PRODUCT&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

						$arElementOffer[$arOffer["LINK_ELEMENT_ID"]][] = $arOffer;
					}
				}
			}

			foreach($arResult["ITEMS"] as $key => $arElement){

				$minItemPrice = 0;
				$minItemPriceFormat = "";
				foreach($arElement["OFFERS"] as $arOffer){
					foreach($arOffer["PRICES"] as $code=>$arPrice){
						if($arPrice["CAN_ACCESS"]){
							if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]){
								$minOfferPrice = $arPrice["DISCOUNT_VALUE"];
								$minOfferPriceFormat = $arPrice["PRINT_DISCOUNT_VALUE"];
							}else{
								$minOfferPrice = $arPrice["VALUE"];
								$minOfferPriceFormat = $arPrice["PRINT_VALUE"];
							}

							if ($minItemPrice > 0 && $minOfferPrice < $minItemPrice){
								$minItemPrice = $minOfferPrice;
								$minItemPriceFormat = $minOfferPriceFormat;
							}elseif ($minItemPrice == 0){
								$minItemPrice = $minOfferPrice;
								$minItemPriceFormat = $minOfferPriceFormat;
							}
						}
					}

				}

				if ($minItemPrice > 0){
					$arResult["ITEMS"][$key]["MIN_OFFER_PRICE"] = $minItemPrice;
					$arResult["ITEMS"][$key]["PRINT_MIN_OFFER_PRICE"] = $minItemPriceFormat;
				}


			}

		}


		return $arResult;
	}


	static function includeLibs($arParams){
		global $APPLICATION;

		$APPLICATION->AddHeadString('<link href="http://fonts.googleapis.com/css?family=Economica:700,400italic" rel="stylesheet" type="text/css">');
		$APPLICATION->AddHeadString('<link href="http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=latin,cyrillic" rel="stylesheet" type="text/css">');
		$APPLICATION->AddHeadString('<link href="/bitrix/components/klondike/pslider/src/style.min.css" rel="stylesheet" type="text/css">');


		if('Y' == $arParams['INCLUDE_JQUERY']){
			$APPLICATION->AddHeadScript('http://yandex.st/jquery/1.7.1/jquery.min.js');
		}

		if('Y' == $arParams['INCLUDE_MODERNIZER']){
			$APPLICATION->AddHeadScript('http://yandex.st/modernizr/2.6.2/modernizr.min.js');
		}

		$APPLICATION->AddHeadScript('/bitrix/components/klondike/pslider/src/script.js');

		return;
	}

}
?>
