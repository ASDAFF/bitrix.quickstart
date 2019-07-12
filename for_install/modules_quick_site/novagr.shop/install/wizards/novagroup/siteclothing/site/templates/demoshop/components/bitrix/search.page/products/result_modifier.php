<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// массив продавцов
$arSellers = array();
$arResult["SELLER_NAMES"] = array();

// массив элементов с нужными данными
$arResult["ELEMENTS"] = array();
// миссив для избранного
$arResult['FAVORITE_PRODUCTS'] = array();


//deb($arResult["SEARCH"]);
if (count($arResult["SEARCH"])) {
	// получаем ID товаров
	$arrIDSproducts = array();
	foreach ($arResult["SEARCH"] as $key => $value) {
		$arrIDSproducts[$key] = $value["ITEM_ID"];
		
	}
	//deb($arrIDSproducts);	
	// массив ID фоток
	$arParams['PRODUCTS_PHOTOS_ID'] = array();
	
	$arFilter = array();
	$arFilter["ID"] = $arrIDSproducts;
	$arSelect = array(
		'IBLOCK_ID',
		'IBLOCK_NAME',
		'IBLOCK_SECTION_ID',
		'ID',
		'NAME',
		'CATALOG_GROUP_1',
		//'PROPERTY_SELLER.PROPERTY_SELLER_TYPE'
	);

	//deb($arFilter);
	$rsElement = CIBlockElement::GetList(array('ID' => "DESC"), $arFilter, false, false, $arSelect);
	//deb($rsElement->SelectedRowsCount());
	while($data = $rsElement -> Fetch())
	{
		$arResult["ELEMENTS"][$data["ID"]] = $data;
		// фотки
		$rsProps = CIBlockElement::GetProperty(
				$data['IBLOCK_ID'],
				$data['ID'],
				"sort",
				"asc",
				array('CODE' => "PHOTOS")
		);
		
		while ($arProps = $rsProps -> GetNext()) {
			
			$arResult["ELEMENTS"][ $data['ID'] ]['PROPERTY_PHOTOS_VALUE'][] = $arProps['VALUE'];
		}	
		
		// собираем ID фотографий
		$arParams['PRODUCTS_PHOTOS_ID'] = array_merge(
				$arParams['PRODUCTS_PHOTOS_ID'],
				$arResult['ELEMENTS'][ $data['ID'] ]['PROPERTY_PHOTOS_VALUE']
		);
		
		// продавец
		$rsProps = CIBlockElement::GetProperty(
				$data['IBLOCK_ID'],
				$data['ID'],
				"sort",
				"asc",
				array('CODE' => "SELLER")
		);
		if  ($arProps = $rsProps -> GetNext()) {
			
			$arSellers[$data['ID']] = $arProps["VALUE"];
			$arResult['ELEMENTS'][ $data['ID'] ]['SELLER_ID'] = $arProps["VALUE"];
			//deb($arProps);
		}
		$arResult['ELEMENTS'][ $data['ID'] ]['PRICE'] = number_format($arResult['ELEMENTS'][ $data['ID'] ]['CATALOG_PRICE_1'], 0, ".", " ");
		//deb($data);	
		
	}
	//deb($arSellers);
	$arSellerNames = array();
	// находим продавцов
	$arFilterSellers = array( "ID" => $arSellers);
	$arSelectSellers = array("ID", "NAME");
	//deb($arFilterSellers);
	$rsElementSellers = CIBlockElement::GetList(false, $arFilterSellers, false, false, $arSelectSellers);
	while($data = $rsElementSellers -> GetNext())
	{
		//deb($data);
		$arResult["SELLER_NAMES"][$data["ID"]]  = $data["NAME"];
	
	}
	//deb($arSellerNames);
	//deb($arParams['PRODUCTS_PHOTOS_ID']);
	
	// сделаем выборку фотографий, а так же получим пути к графическим файлам
	if( count($arParams['PRODUCTS_PHOTOS_ID']) > 0 )
	{
	 	$arFilter = array(
				'ACTIVE' => "Y",
				//'IBLOCK_TYPE' => $arParams['PHOTOS_IBLOCK_TYPE'],
				'ID' => $arParams['PRODUCTS_PHOTOS_ID'],
		);
	
		$arSelect = array( 'ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE' );
		$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
		while($data = $rsElement -> GetNext())
		{
			$PREVIEW_PICTURE_ID[$data['ID']] = $data['PREVIEW_PICTURE'];
			//$DETAIL_PICTURE_ID[$data['ID']] = $data['DETAIL_PICTURE'];
		}
		$arFilter = "";
		if(count($PREVIEW_PICTURE_ID) > 0)
			foreach($PREVIEW_PICTURE_ID as $val) $arFilter .= $val.",";
	
		/*if(count($DETAIL_PICTURE_ID) > 0)
			foreach($DETAIL_PICTURE_ID as $val) $arFilter .= $val.",";
		*/
		$rsFile = CFile::GetList(false, array('@ID' => $arFilter));
		while($data = $rsFile -> GetNext())
		{
			$PICTURE_SRC[$data['ID']]
			= "/upload/".$data['SUBDIR']."/".$data['FILE_NAME'];
		}
	
		if(isset($PREVIEW_PICTURE_ID))
			foreach($PREVIEW_PICTURE_ID as $key => $val)
			$arResult['PREVIEW_PICTURE'][$key] = $PICTURE_SRC[$val];
	
		/*if(isset($DETAIL_PICTURE_ID))
			foreach($DETAIL_PICTURE_ID as $key => $val)
			$arResult['DETAIL_PICTURE'][$key] = $PICTURE_SRC[$val];
		*/	
	
	
	}
	
	// заполняем избранное
	if(CUser::IsAuthorized()) {
		global $USER;		
		$arResult['FAVORITE_PRODUCTS'] = getFavorites("product", $USER->GetID());
		$arResult['USER']['ID'] = $USER->GetID();
		
		// выбираем данные по кнопкам мое/не моё

		foreach($arResult['ELEMENTS'] as $val) $arIdList[] = $val['ID'];
		$arFilter = array(
				'IBLOCK_ID'	=> 155,
				'PROPERTY_USER_ID'	=> $arResult['USER']['ID'],
				'PROPERTY_MY_STYLE_PRODUCT_ID' => $arIdList
		);
		$arSelect = array(
				'ID',
				'ACTIVE',
				'PROPERTY_MY_STYLE_PRODUCT_ID'
		);
		//deb($arFilter);
		$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
		while($arElement = $rsElement -> Fetch()) {
			
			//deb($arElement);
			$arResult['ELEMENTS'][ $arElement['PROPERTY_MY_STYLE_PRODUCT_ID_VALUE'] ]['MY_STYLE'] = $arElement['ACTIVE'];
			
		}	
	
		$arFilter = array(
				'IBLOCK_ID'	=> 157,
				'PROPERTY_USER_ID'	=> $arResult['USER']['ID'],
				'PROPERTY_NOT_MY_STYLE_PRODUCT' => $arIdList
		);
		$arSelect = array(
				'ID',
				'ACTIVE',
				'PROPERTY_NOT_MY_STYLE_PRODUCT'
		);
		$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
		while($arElement = $rsElement -> Fetch()) {
			$arResult['ELEMENTS'][ $arElement['PROPERTY_NOT_MY_STYLE_PRODUCT_VALUE'] ]['NOT_MY'] = $arElement['ACTIVE'];
			
		}	
	}	
}
//deb($arResult["ELEMENTS"]);

// присоединяем нужные данные к массиву поиска
?>