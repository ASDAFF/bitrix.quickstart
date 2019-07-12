<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//deb($arResult);

// массив элементов с нужными данными
$arResult["ELEMENTS"] = array();

//deb($arResult["SEARCH"]);
if (count($arResult["SEARCH"]) && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") )
{
	// получаем ID образов
	$arrIDSimageries = array();
	foreach ($arResult["SEARCH"] as $key => $value) {
		$arrIDSimageries[$key] = $value["ITEM_ID"];
		
	}
	//deb($arrIDSimageries);	
	// массив ID фоток
	$PREVIEW_PICTURE_ID = array();
	$arParams['PRODUCTS_PHOTOS_ID'] = array();
	
	$arFilter = array();
	$arFilter["ID"] = $arrIDSimageries;
	$arFilter["IBLOCK_ID"] = 18; // образы
	$arSelect = array(
		'IBLOCK_ID',
		'IBLOCK_NAME',
		'ID',
		'NAME', 'CODE',
		'PROPERTY_PRODUCTS',
		'PROPERTY_PHOTOS',
		//'PROPERTY_SELLER.PROPERTY_SELLER_TYPE'
	);

	//deb($arFilter);
	$rsElement = CIBlockElement::GetList(array('ID' => "DESC"), $arFilter, false, false, $arSelect);
	//deb($rsElement->SelectedRowsCount());
	while($data = $rsElement -> Fetch())
	{
		$arResult["ELEMENTS"][$data["ID"]] = $data;
		//deb($data);
		// соберём ID фотографий образа
		foreach( $data['PROPERTY_PHOTOS_VALUE'] as $subval)
			$PREVIEW_PICTURE_ID[ $subval ] = $subval;
		
		
		// лезем за суммами составных товаров
		$arFilter = array(
				'ACTIVE' => "Y",
				'PRODUCT_ID' => array_values($data['PROPERTY_PRODUCTS_VALUE'])
		);
		$arSelect = array( 'PRODUCT_ID', 'PRICE' );
		$rsSubElement = CPrice::GetList(false, $arFilter, false, false, $arSelect);
		$arResult['ELEMENTS'][ $data['ID'] ]['TOTALPRICE'] = 0;
		while($SubData = $rsSubElement -> GetNext())
		{
			$arResult['ELEMENTS'][ $data['ID'] ]['ITEMS'][$SubData['PRODUCT_ID']]['PRICE'] = number_format($SubData['PRICE'], 0, ".", " ");
			$arResult['ELEMENTS'][$data['ID']]['TOTALPRICE'] += $SubData['PRICE'];
		}
		$arResult['ELEMENTS'][$data['ID']]['TOTALPRICE'] = number_format($arResult['ELEMENTS'][$data['ID']]['TOTALPRICE'], 0, ".", " ");
		
		
		$arResult['ELEMENTS'][ $data['ID'] ]['PRICE'] = number_format($arResult['ELEMENTS'][ $data['ID'] ]['CATALOG_PRICE_1'], 0, ".", " ");
		//deb($data);	
		
	}
	
	
	
	// сделаем выборку фотографий, а так же получим пути к графическим файлам
	if( count($PREVIEW_PICTURE_ID) > 0 )
	{
	 	
		$arFilter = "";
		if(count($PREVIEW_PICTURE_ID) > 0)
			foreach($PREVIEW_PICTURE_ID as $val) $arFilter .= $val.",";
		
		$rsFile = CFile::GetList(false, array('@ID' => $arFilter));
		while($data = $rsFile -> GetNext())
		{
			
			$PICTURE_SRC[$data['ID']]
			= "/upload/".$data['SUBDIR']."/".$data['FILE_NAME'];
		}
	
		if(isset($PREVIEW_PICTURE_ID))
			foreach($PREVIEW_PICTURE_ID as $key => $val)
			$arResult['PREVIEW_PICTURE'][$key] = $PICTURE_SRC[$val];	
	
	}
}
//deb($arResult["ELEMENTS"]);

// присоединяем нужные данные к массиву поиска
?>