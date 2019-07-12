<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

//deb($_REQUEST);

if(!isset($arParams["CACHE_TIME"]))
$arParams["CACHE_TIME"] = 36000000;

if( !CModule::IncludeModule("iblock") ) exit;
if( !CModule::IncludeModule("catalog") ) exit;
if( !CModule::IncludeModule("sale") ) exit;
$cacheFlag = true;
// поиск по образам
if( !empty($_REQUEST['q']) )
{
    $search = new Novagroup_Classes_General_Search($_REQUEST['q']);
    $arElementsSearch = $search->searchByIblock($arParams['FASHION_IBLOCK_TYPE'],$arParams['FASHION_IBLOCK_ID'])->getPrepareArray();

	if ( empty( $arElementsSearch ) ) $arElementsSearch[0] = -1;
	$arFilter["ID"] = $arElementsSearch;
	$cacheFlag = false;	
}

/*
* инициализация начальных параметров
*/
if (isset($_REQUEST['elmid']))
	$arParams['ELEMENT_CODE'] = $_REQUEST['elmid'];

if ( isset($_REQUEST['nPageSize']) && ((int)$arParams['nPageSize'] > 0 ) )
	$arParams['nPageSize'] = (int)$_REQUEST['nPageSize'];
else $arParams['nPageSize'] = 16;
if ( isset($_REQUEST['iNumPage']) )
	$arParams['iNumPage'] = (int)$_REQUEST['iNumPage'];
else $arParams['iNumPage'] = 1;

if (isset($_REQUEST['arFilter']) && is_array($_REQUEST['arFilter'])) {
	foreach($_REQUEST['arFilter'] as $val)
		foreach($val as $subkey => $subval)
			$arParams['arFilterRequest'][$subkey][] = $subval;
}
// ищем формат валюты только для первого элемента
$currencyFoundFlag = false;
$CurrencyFormat = array();

$detailPage = false;
$arParams["OFFERS_FIELD_CODE"] = array("NAME");
$arParams["OFFERS_PROPERTY_CODE"] = array("STD_SIZE", "PHOTOS", "COLOR");
$arParams["PRICE_CODE"] = array('BASE');

// выбираем конкретный образ
if( isset($arParams['ELEMENT_CODE']) )
{
	$detailPage = true;
	
	global $USER;
	$arrayGroupCanEdit = array(1);
	if (!empty($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"])) $arrayGroupCanEdit[] = $arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"];
	//deb($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"]);
	
	// если пользователь входит в группу Администраторы интернет-магазина [5]
	// то показываем карандашик для редактирования
	$arParams['SHOW_EDIT_BUTTON'] = "N";
	if ( CSite::InGroup( $arrayGroupCanEdit )) {
	
		$arParams['SHOW_EDIT_BUTTON'] = "Y";
	
	}
	
	// Если нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
	if ($this->StartResultCache(false, $arParams['SHOW_EDIT_BUTTON']))
	//if (1==1)
	{
			
		$arFilter['ACTIVE'] = "Y";
		$arFilter['IBLOCK_ID'] = $arParams['FASHION_IBLOCK_ID'];
		$arFilter['CODE'] = $arParams['ELEMENT_CODE'];
		$arSelect = array(
			'ID',
			'NAME',
			'IBLOCK_ID',
			'CODE',
			'PROPERTY_PHOTOS',
			'PROPERTY_EVENT.NAME',
			'PROPERTY_SEAZON.NAME',
			'PROPERTY_PRODUCTS',
			'PREVIEW_TEXT',
			'DETAIL_TEXT'
		);
		$rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
		$arPictureId = array(); // массив для ID фотографий
	
		if ($arElement = $rsElement -> Fetch())
		{
			//deb($arElement);
			$arElement['PROPERTY_PRODUCTS_VALUE'] = (count($arElement['PROPERTY_PRODUCTS_VALUE'])>0) ?
			$arElement['PROPERTY_PRODUCTS_VALUE'] : FALSE ;
				
			$arResult['ELEMENT'] = $arElement;
			$arResult["NAME"] = $arElement["NAME"];
			$arResult["ID"] = $arElement["ID"];
			$arResult["IBLOCK_ID"] = $arElement["IBLOCK_ID"];
			
			$arResult['SHOW_EDIT_BUTTON'] = $arParams['SHOW_EDIT_BUTTON'];
			
			// выберем фотки образа
			foreach($arElement['PROPERTY_PHOTOS_VALUE'] as $subkey => $subval)
				$arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'][$subkey] = CFile::GetPath($subval);
			//выберем итемы, входящие в образ и подсчитаем общую стоимость
			$arSubFilter = array(
				'IBLOCK_ID'	=> $arParams['CATALOG_IBLOCK_ID'],
				'ACTIVE'	=> "Y",
				'ID'		=> $arElement['PROPERTY_PRODUCTS_VALUE']
			);
			$arSubSelect = array(
				'ID',
				'NAME', 'IBLOCK_ID', 'DETAIL_PAGE_URL',
				'CODE',
				'IBLOCK_SECTION_ID',
				'PROPERTY_SKU',
				'PROPERTY_PHOTOS',
				'PROPERTY_VENDOR.NAME',
				'PROPERTY_VENDOR.CODE',
				'PROPERTY_VENDOR',
				'CATALOG_GROUP_1',
                "PROPERTY_KEYWORDS",
                "PROPERTY_META_DESCRIPTION",
				"PROPERTY_SPECIALOFFER", "PROPERTY_NEWPRODUCT", "PROPERTY_SALELEADER"
			);
			$rsSubElement = CIBlockElement::GetList(false, $arSubFilter, false, false, $arSubSelect);
			$arResult['ELEMENT'][ $arElement['ID'] ]['TOTAL'] = 0;
			//$countElem = $rsSubElement->SelectedRowsCount();
			//deb($countElem);
			$rsSubElement->SetUrlTemplates(SITE_DIR.'catalog/#SECTION_CODE#/#ELEMENT_CODE#/');
	
			
			$arResult["OFFERS"] = array();
			
			
			$arConvertParams = array();
			$arResult['ELEMENT']['TOTAL'] = 0;
			$i = 0;
			while ($obElement = $rsSubElement -> GetNextElement())
			{
				$dataElement = $obElement->GetFields();
				//deb($dataElement);
				if ($i<1) {
					// вычисляем только на первой итерации цикла
					$arResultPrices = CIBlockPriceTools::GetCatalogPrices($dataElement["IBLOCK_ID"], $arParams["PRICE_CODE"]);
					$arResult["CAT_PRICES"] = $arResultPrices;					
					
				}
				// перебираем тп - ищем наименьшую цену, когда находим - выбираем для этого предложения фoто
				$arOffers = CIBlockPriceTools::GetOffersArray(
						$dataElement["IBLOCK_ID"]
						,array($dataElement['ID'])
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

				$firstOfferFlag = true;// признак что обрабатываем первое тп
				$minPrice = ''; // минимальная цена
				$oldPrice = ''; // старая цена ( без скидки)
				$offerIndex = ''; // индекс в массиве тп
				
				foreach ($arOffers as $key => $arOffer)
				{
					// находим минимальную цену среди предложений
					if ($arOffer["CATALOG_QUANTITY"] > 0) {
						
						if ($currencyFoundFlag == false) {
							$CurrencyFormat = CCurrencyLang::GetCurrencyFormat($arOffer["PRICES"]["BASE"]["CURRENCY"]);
							$currencyFoundFlag = true;
							
							//$arResult["CURRENCY_FORMAT_STRING"] = trim(str_replace("#", "" , $CurrencyFormat["FORMAT_STRING"]));	
							
						}
						
						
						if ($arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] > 0 && $firstOfferFlag == true) {
								
							$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
							$oldPrice = $arOffer["PRICES"]["BASE"]["VALUE_NOVAT"];
							//$arResult["MIN_PRICE_OFFER_ID"] = $arOffer["ID"];
							$offerIndex = $key;
							$firstOfferFlag = false;
			
						} elseif ($arOffer["PRICES"]["Blk9ASE"]["DISCOUNT_VALUE_NOVAT"] > 0 && $arOffer["BASE"]["DISCOUNT_VALUE_NOVAT"] < $minPrice) {
							$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
							//$arResult["MIN_PRICE_OFFER_ID"] = $arOffer["ID"];
							$oldPrice = $arOffer["PRICES"]["BASE"]["VALUE_NOVAT"];
							$offerIndex = $key;
						}
					} // end if ($arOffer["CATALOG_QUANTITY"] > 0) {
			
				}
					
				if (empty($minPrice)) {
					// если значение пусто - значет цены нет ни у одного тп, у которого есть остатки
					// берем последний элемент
					$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
					$oldPrice = $arOffer["PRICES"]["BASE"]["VALUE_NOVAT"];
					$offerIndex = $key;
				}
				$arResult['ELEMENT']['TOTAL'] += $minPrice;
				$dataElement['PRICE'] = $minPrice;
				$dataElement['OLD_PRICE'] = $oldPrice;
                $dataElement['CURRENCY'] = $arOffers[$offerIndex]["PRICES"]["BASE"]["CURRENCY"];
				$dataElement['CURRENCY_DISPLAY'] = Novagroup_Classes_General_Main::getCurrencyAbbr($arOffers[$offerIndex]["PRICES"]["BASE"]["CURRENCY"]);
				
				
				// добавляем фотки для текущего элемента в массив фоток( берем только первую фотку)
				$currentPhotoArr = array();
			
				if (!empty($arOffers[$offerIndex]["PROPERTIES"]["PHOTOS"]["VALUE"][0])) {
			
					$arPictureId[] = $arOffers[$offerIndex]["PROPERTIES"]["PHOTOS"]["VALUE"][0];
					$currentPhotoArr[] = $arOffers[$offerIndex]["PROPERTIES"]["PHOTOS"]["VALUE"][0];
				}
				
				$dataElement["PROPERTIES"]['PHOTOS']["VALUE"] = $currentPhotoArr;
			
				$arResult['ITEMS'][] = $dataElement;
				$i++;
			} // end while
			
			
			// выберем фотографии
			$arFilter = array('IBLOCK_ID' => $arParams['PHOTO_IBLOCK_ID'], 'ID' => $arPictureId);
			$arSelect = array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');
			$rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
			$arFilter = "";
			while($arElement = $rsElement -> Fetch())
			{
				$arPhotoId[ $arElement['PREVIEW_PICTURE'] ]['TYPE'] = "PREVIEW_PICTURE";
				$arPhotoId[ $arElement['PREVIEW_PICTURE'] ]['ID'] = $arElement['ID'];
				$arPhotoId[ $arElement['DETAIL_PICTURE'] ]['TYPE'] = "DETAIL_PICTURE";
				$arPhotoId[ $arElement['DETAIL_PICTURE'] ]['ID'] = $arElement['ID'];
				
				$arFilter.= $arElement['PREVIEW_PICTURE'].",".$arElement['DETAIL_PICTURE'].",";
			}
			$rsFile = CFile::GetList(false, array('@ID' => $arFilter));
			while($arFile = $rsFile -> GetNext())
			{
				$arResult[ 'PHOTO' ][ $arPhotoId[ $arFile['ID'] ]['ID'] ][ $arPhotoId[ $arFile['ID'] ]['TYPE'] ]
					= "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
			}
			
		} // end if ($arElement = $rsElement -> Fetch())
		
		$this->SetResultCacheKeys(array(
				"IBLOCK_ID",
				"ID",				
				"NAME",
				"SHOW_EDIT_BUTTON",
                "SEARCH_NOT_FOUND"
		));
        if( count($arResult['ELEMENT']) == 0 )
        {
            @define("ERROR_404", "Y");
            $arResult['SEARCH_NOT_FOUND'] = "Y";
            $this -> IncludeComponentTemplate('notfound');
        } else {
            if( !empty($arParams['ELEMENT_CODE']) )
                $this -> IncludeComponentTemplate('element');
            else
                $this -> IncludeComponentTemplate('elements');
        }

	}
} else {

	// выбираем образы
	// Если нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
	if ($this->StartResultCache( !empty($_REQUEST['q']) )) {
		
		
		if ($cacheFlag == false) {
			$this->AbortResultCache();
		}	
		if ( isset($arParams['arFilterRequest']) )
			$arFilter = $arParams['arFilterRequest'];
		$arOrder = array('SORT' => "ASC");
		$arFilter['ACTIVE'] = "Y";
		$arFilter['IBLOCK_ID'] = $arParams['FASHION_IBLOCK_ID'];
		$arNavStartParams = array(
			'nPageSize'	=> $arParams['nPageSize'],
			'iNumPage'	=> $arParams['iNumPage']
		);
		$arSelect = array(
			'ID',
			'NAME',
			'CODE',
			'PROPERTY_PHOTOS',
			'PROPERTY_EVENT.NAME',
			'PROPERTY_SEAZON.NAME',
			'PROPERTY_PRODUCTS',
			'PREVIEW_TEXT',
			'DETAIL_TEXT'
		);
		$rsElement = CIBlockElement::GetList($arOrder, $arFilter, false , $arNavStartParams, $arSelect);
		$arResult['NAV_STRING'] = $rsElement -> GetPageNavStringEx($navComponentObject, "", "catalog");
		$arResult['NavPageCount'] = $rsElement -> NavPageCount;
		$arResult['NavPageNomer'] = $rsElement -> NavPageNomer;
		$arResult['NavRecordCount'] = $rsElement -> NavRecordCount;
		$arPictureId = array(); // массив для ID фотографий
		$arSectionId = array(); // массив для ID секций
		//$arProductId = array(); // массив для ID итемов, входящих в образ
		$basePriceFound = false;
		while ($arElement = $rsElement -> Fetch())
		{		
			$arElement['PROPERTY_PRODUCTS_VALUE'] = (count($arElement['PROPERTY_PRODUCTS_VALUE'])>0) ?
			$arElement['PROPERTY_PRODUCTS_VALUE'] : FALSE ;
			
			$arResult['ELEMENT'][ $arElement['ID'] ] = $arElement;
			// выберем фотки образа
			foreach($arElement['PROPERTY_PHOTOS_VALUE'] as $subkey => $subval)
				$arResult['ELEMENT'][ $arElement['ID'] ]['PROPERTY_PHOTOS_VALUE'][$subkey]
					= CFile::GetPath($subval);
			
			//выберем итемы, входящие в образ и подсчитаем общую стоимость
			$arSubFilter = array(
				'IBLOCK_ID'	=> $arParams['CATALOG_IBLOCK_ID'],
				'ACTIVE'	=> "Y",
				'ID'		=> $arElement['PROPERTY_PRODUCTS_VALUE']
			);
			$arSubSelect = array(
				'ID',
				'NAME'				
			);
						
			$rsSubElement = CIBlockElement::GetList(false, $arSubFilter, false, false, $arSubSelect);
			$arResult['ELEMENT'][ $arElement['ID'] ]['TOTAL'] = 0;
			while ( $arSubElement = $rsSubElement -> Fetch() )
			{

				if ($basePriceFound == false) {
					// вычисляем только на первой итерации цикла
					$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arSubElement["IBLOCK_ID"], $arParams["PRICE_CODE"]);
					$arResult["CAT_PRICES"] = $arResultPrices;
					$basePriceFound = true;
				}
				
				// перебираем тп - ищем наименьшую цену, когда находим - выбираем для этого предложения фoто
				$arOffers = CIBlockPriceTools::GetOffersArray(
						$arParams["CATALOG_IBLOCK_ID"]
						,array($arSubElement['ID'])
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
				$firstOfferFlag = true;
				$minPrice = '';
				$oldPrice = '';
				$offerIndex = '';
				
				$skipProductFlag = true;
				foreach ($arOffers as $key => $arOffer)
				{
					// находим минимальную цену среди предложений
					if ($arOffer["CATALOG_QUANTITY"] > 0) {
						//deb($arOffer["PRICES"]);
						$skipProductFlag = false;
						if ($arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] > 0 && $firstOfferFlag == true) {
								
							$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
							$oldPrice = $arOffer["PRICES"]["BASE"]["VALUE_NOVAT"];
							//$arResult["MIN_PRICE_OFFER_ID"] = $arOffer["ID"];
							$offerIndex = $key;
							$firstOfferFlag = false;
				
						} elseif ($arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] > 0 && $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"] < $minPrice) {
							$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
							//$arResult["MIN_PRICE_OFFER_ID"] = $arOffer["ID"];
							$oldPrice = $arOffer["PRICES"]["BASE"]["VALUE_NOVAT"];
							$offerIndex = $key;
						}
					} // end if ($arOffer["CATALOG_QUANTITY"] > 0) {
				
				}
				// если остатки у всех тп == 0 то не показываем такой товар
				//if ($skipProductFlag == true) continue;
				
				if (empty($minPrice)) {
					// если значение пусто - значет цены нет ни у одного тп, у которого есть остатки
					// берем последний элемент
					$minPrice = $arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE_NOVAT"];
					$oldPrice = $arOffer["PRICES"]["BASE"]["VALUE_NOVAT"];
					$offerIndex = $key;
				}
				
				$arResult['ELEMENT'][ $arElement['ID'] ]['TOTAL'] += $oldPrice;
				$arResult['ELEMENT'][ $arElement['ID'] ]['DISCN'] += $minPrice;
				
				
			} // end while ( $arSubElement = $rsSubElement -> Fetch() )
			$arResult['ELEMENT'][ $arElement['ID'] ]['CURRENCY_DISPLAY'] = Novagroup_Classes_General_Main::getCurrencyAbbr($arOffers[$offerIndex]["PRICES"]["BASE"]["CURRENCY"]);
		}

		// выберем фотографии
		$arFilter = array('IBLOCK_ID' => $arParams['PHOTO_IBLOCK_ID'], 'ID' => $arPictureId);
		$arSelect = array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');
		$rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
		$arFilter = "";
		while($arElement = $rsElement -> Fetch())
		{
			$arPhotoId[ $arElement['PREVIEW_PICTURE'] ]['TYPE'] = "PREVIEW_PICTURE";
			$arPhotoId[ $arElement['PREVIEW_PICTURE'] ]['ID'] = $arElement['ID'];
			$arPhotoId[ $arElement['DETAIL_PICTURE'] ]['TYPE'] = "DETAIL_PICTURE";
			$arPhotoId[ $arElement['DETAIL_PICTURE'] ]['ID'] = $arElement['ID'];
			
			$arFilter.= $arElement['PREVIEW_PICTURE'].",".$arElement['DETAIL_PICTURE'].",";
		}
		$rsFile = CFile::GetList(false, array('@ID' => $arFilter));
		while($arFile = $rsFile -> GetNext())
		{
			$arResult[ 'PHOTO' ][ $arPhotoId[ $arFile['ID'] ]['ID'] ][ $arPhotoId[ $arFile['ID'] ]['TYPE'] ]
				= "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
		}

        $this->SetResultCacheKeys(array(
            "SEARCH_NOT_FOUND"
        ));

        if( count($arResult['ELEMENT']) == 0 )
        {
            @define("ERROR_404", "Y");
            $arResult['SEARCH_NOT_FOUND'] = "Y";
            $this -> IncludeComponentTemplate('notfound');
        } else {
            if( !empty($arParams['ELEMENT_CODE']) )
                $this -> IncludeComponentTemplate('element');
            else
                $this -> IncludeComponentTemplate('elements');
        }
	}	
}

// операции которые не попадают в кэш
if($arResult['SEARCH_NOT_FOUND']=="Y")
{
    @define("SEARCH_NOT_FOUND", "Y");
}

if ($detailPage == true) {

    if(trim($arResult['NAME'])<>"")
    {
        $APPLICATION->AddChainItem($arResult['NAME']);
        $APPLICATION->SetTitle($arResult["NAME"]);
    }
}
$arReturnUrl = array(

		"add_element" => (
				strlen($arParams["SECTION_URL"])?
				$arParams["SECTION_URL"]:
				CIBlock::GetArrayByID($arParams["FASHION_IBLOCK_ID"], "SECTION_PAGE_URL")
		),

);
$arButtons = CIBlock::GetPanelButtons(
		$arParams["FASHION_IBLOCK_ID"],
		0,
		$arResult["SECTION"]["ID"],
		array("RETURN_URL" =>  $arReturnUrl, "CATALOG"=>true)
);

foreach ($arButtons as $key => $item) {
	// удаляем кнопку Добавить секцию
	if (isset($arButtons[$key]["add_section"])) unset($arButtons[$key]["add_section"]);
}

if(!empty($arResult['NAME'])){
    $arFields = array(
        "PRODUCT_ID" => $arResult['ID'],
        "LID" => SITE_ID,
        "NAME" => $arResult['NAME'],
        "IBLOCK_ID" => $arResult["IBLOCK_ID"]
    );
    $result = CSaleViewedProduct::Add($arFields);
}

$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
?>