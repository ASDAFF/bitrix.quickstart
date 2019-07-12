<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

//deb($_REQUEST);

if(!isset($arParams["CACHE_TIME"]))
$arParams["CACHE_TIME"] = 36000000;

if( !CModule::IncludeModule("iblock") ) exit;
if( !CModule::IncludeModule("catalog") ) exit;
if( !CModule::IncludeModule("sale") ) exit;
if( !CModule::IncludeModule("search") ) exit;

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
global $CACHE_MANAGER;
/*
* инициализаци€ начальных параметров
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
// ищем формат валюты только дл€ первого элемента
$currencyFoundFlag = false;
$CurrencyFormat = array();

$detailPage = false;
$arParams["OFFERS_FIELD_CODE"] = array("NAME");
$arParams["OFFERS_PROPERTY_CODE"] = array("STD_SIZE", "PHOTOS", "COLOR");


global $USER;
$arrayGroupCanEdit = array(1);
if (!empty($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"])) $arrayGroupCanEdit[] = $arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"];
//deb($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"]);

// если пользователь входит в группу јдминистраторы интернет-магазина [5]
// то показываем карандашик дл€ редактировани€
$arParams['SHOW_EDIT_BUTTON'] = "N";
/*if ( CSite::InGroup( $arrayGroupCanEdit )) {

    $arParams['SHOW_EDIT_BUTTON'] = "Y";

}*/
$arUserGroups = $USER->GetUserGroupArray();

if (count(array_intersect($arUserGroups, $arrayGroupCanEdit))>0)
    $arParams['SHOW_EDIT_BUTTON'] = "Y";

$arResult['OPT_USER'] = 0;
if (!empty($arParams["OPT_GROUP_ID"])) {
    if (in_array($arParams["OPT_GROUP_ID"], $arUserGroups)) {
        $arResult['OPT_USER'] = 1;
    }
}

$additionalCacheID = $USER->GetGroups().$arParams['SHOW_EDIT_BUTTON'];

// выбираем конкретный образ
if (isset($arParams['ELEMENT_CODE']))
{
	$detailPage = true;

	// ≈сли нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
	if ($this->StartResultCache(false, $additionalCacheID))
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
		$arPictureId = array(); // массив дл€ ID фотографий

		if ($arElement = $rsElement -> Fetch())
		{
			//deb($arElement);
			$arElement['PROPERTY_PRODUCTS_VALUE'] = (count($arElement['PROPERTY_PRODUCTS_VALUE'])>0) ?
			$arElement['PROPERTY_PRODUCTS_VALUE'] : FALSE ;

            $arResult['LIST'] = array();
			$arResult['ELEMENT'] = $arElement;
			$arResult["NAME"] = $arElement["NAME"];
			$arResult["ID"] = $arElement["ID"];
			$arResult["IBLOCK_ID"] = $arElement["IBLOCK_ID"];
			
			//$arResult['SHOW_EDIT_BUTTON'] = $arParams['SHOW_EDIT_BUTTON'];
            // выберем фотки образа
            foreach ($arElement['PROPERTY_PHOTOS_VALUE'] as $subkey => $subval) {
                $MakeResizePicture = Novagroup_Classes_General_Main::MakeResizePicture($subval, array("WIDTH" => "97", "HEIGHT" => "128"));
                $arResult['LIST']['PROPERTY_PHOTOS_VALUE'][$subkey]
                    = $MakeResizePicture['src'];

                $MakeResizePicture = Novagroup_Classes_General_Main::MakeResizePicture($subval, array("WIDTH" => "450", "HEIGHT" => "580"));
                $arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'][$subkey]
                    = $MakeResizePicture['src'];

                $arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'][$subkey] = CFile::GetPath($subval);
            }

			//выберем итемы, вход€щие в образ и подсчитаем общую стоимость
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
				/*'CATALOG_GROUP_1',*/
                "PROPERTY_KEYWORDS",
                "PROPERTY_META_DESCRIPTION"
			);
			$rsSubElement = CIBlockElement::GetList(false, $arSubFilter, false, false, $arSubSelect);
			$arResult['ELEMENT'][ $arElement['ID'] ]['TOTAL'] = 0;
			$rsSubElement->SetUrlTemplates(SITE_DIR.'catalog/#SECTION_CODE#/#ELEMENT_CODE#/');

			$arResult["OFFERS"] = array();

			$arConvertParams = array();
			$arResult['ELEMENT']['TOTAL'] = 0;
			$i = 0;
			while ($obElement = $rsSubElement -> GetNextElement())
			{
				$dataElement = $obElement->GetFields();
				// добавл€ем фотки дл€ текущего элемента в массив фоток( берем только первую фотку)
				$currentPhotoArr = array();
			
				if (!empty($arOffers[$offerIndex]["PROPERTIES"]["PHOTOS"]["VALUE"][0])) {
			
					$arPictureId[] = $arOffers[$offerIndex]["PROPERTIES"]["PHOTOS"]["VALUE"][0];
					$currentPhotoArr[] = $arOffers[$offerIndex]["PROPERTIES"]["PHOTOS"]["VALUE"][0];
				}
				$dataElement["PROPERTIES"]['PHOTOS']["VALUE"] = $currentPhotoArr;
				$arResult['ITEMS'][] = $dataElement;
				$i++;
			} // end while
			
		} // end if ($arElement = $rsElement -> Fetch())

        $arResult["DETAIL_CARD_VIEW"] = COption::GetOptionString("main", "detail_card", "1");

        $CACHE_MANAGER->StartTagCache($this->getCachePath());
        $CACHE_MANAGER->RegisterTag("imageries.details");
        $CACHE_MANAGER->EndTagCache();

		$this->SetResultCacheKeys(array(
                "DETAIL_CARD_VIEW",
				"IBLOCK_ID",
				"ID",				
				"NAME",
                "OPT_USER",
				"SHOW_EDIT_BUTTON",
                "SEARCH_NOT_FOUND"
		));
        if ( count($arResult['ELEMENT']) == 0 )
        {
            @define("ERROR_404", "Y");
            $arResult['SEARCH_NOT_FOUND'] = "Y";
            $detailPage = false;
            $this -> IncludeComponentTemplate('notfound');
        } else {

            $this -> IncludeComponentTemplate('element');

        }
	}

} else {

	// выбираем образы
	// ≈сли нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
	if ($this->StartResultCache(!empty($_REQUEST['q']), $additionalCacheID)) {

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
		$arPictureId = array(); // массив дл€ ID фотографий
		$arSectionId = array(); // массив дл€ ID секций
		//$arProductId = array(); // массив дл€ ID итемов, вход€щих в образ
		$basePriceFound = false;
		while ($arElement = $rsElement -> Fetch())
		{		
			$arElement['PROPERTY_PRODUCTS_VALUE'] = (count($arElement['PROPERTY_PRODUCTS_VALUE'])>0) ?
			$arElement['PROPERTY_PRODUCTS_VALUE'] : FALSE ;
			
			$arResult['ELEMENT'][ $arElement['ID'] ] = $arElement;
			// выберем фотки образа
			foreach($arElement['PROPERTY_PHOTOS_VALUE'] as $subkey => $subval)
            {
                $MakeResizePicture = Novagroup_Classes_General_Main::MakeResizePicture($subval, array("WIDTH"=>"177","HEIGHT"=>"240"));
                $arResult['ELEMENT'][ $arElement['ID'] ]['PROPERTY_PHOTOS_VALUE'][$subkey]
                    =  $MakeResizePicture['src'];
            }
			
			//выберем итемы, вход€щие в образ и подсчитаем общую стоимость
			$arSubFilter = array(
				'IBLOCK_ID'	=> $arParams['CATALOG_IBLOCK_ID'],
				'ACTIVE'	=> "Y",
				'ID'		=> $arElement['PROPERTY_PRODUCTS_VALUE']
			);
			$arSubSelect = array(
				'ID',
				'NAME'				
			);
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
	
	//$APPLICATION->SetTitle($arResult["NAME"]);
	
	$rsSeoData = new \Bitrix\Iblock\InheritedProperty\ElementValues($arParams["FASHION_IBLOCK_ID"], $arResult['ID']);
	$arResult["IPROPERTY_VALUES"] = $rsSeoData-> getValues();
	
	$browserTitle = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult["PROPERTIES"],
		array($arParams["BROWSER_TITLE"], "VALUE"),
		$arResult["IPROPERTY_VALUES"],
		"SECTION_META_TITLE"
	);
	
	if (is_array($browserTitle))
		$APPLICATION -> SetPageProperty("title", implode(" ", $browserTitle), $arTitleOptions);
	elseif ($browserTitle != "")
		$APPLICATION -> SetPageProperty("title", $browserTitle, $arTitleOptions);
	
	$metaKeywords = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult["PROPERTIES"],
		array($arParams["META_KEYWORDS"], "VALUE"),
		$arResult["IPROPERTY_VALUES"],
		"SECTION_META_KEYWORDS"
	);
	if (is_array($metaKeywords))
		$APPLICATION->SetPageProperty("keywords", implode(" ", $metaKeywords), $arTitleOptions);
	elseif ($metaKeywords != "")
		$APPLICATION->SetPageProperty("keywords", $metaKeywords, $arTitleOptions);
	
	$metaDescription = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult["PROPERTIES"],
		array($arParams["META_DESCRIPTION"], "VALUE"),
		$arResult["IPROPERTY_VALUES"],
		"SECTION_META_DESCRIPTION"
	);
	if (is_array($metaDescription))
		$APPLICATION->SetPageProperty("description", implode(" ", $metaDescription), $arTitleOptions);
	elseif ($metaDescription != "")
		$APPLICATION->SetPageProperty("description", $metaDescription, $arTitleOptions);
	
	$APPLICATION->AddChainItem($arResult["NAME"]);
	// заносим в список посещенных образов
    if(!empty($arResult['NAME'])){
        $arFields = array(
            "PRODUCT_ID" => $arResult['ID'],
            "LID" => SITE_ID,
            "NAME" => $arResult['NAME'],
            "IBLOCK_ID" => $arResult["IBLOCK_ID"]
        );
        $result = CSaleViewedProduct::Add($arFields);
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
	// удал€ем кнопку ƒобавить секцию
	if (isset($arButtons[$key]["add_section"])) unset($arButtons[$key]["add_section"]);
}

$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
?>