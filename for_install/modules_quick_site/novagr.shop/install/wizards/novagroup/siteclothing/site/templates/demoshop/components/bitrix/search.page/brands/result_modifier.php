<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// массив элементов с нужными данными
$arResult["ELEMENTS"] = array();


if (count($arResult["SEARCH"]) && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") )
{
	// получаем ID Брендов
	$arrIDSbrands = array();
	foreach ($arResult["SEARCH"] as $key => $value) {
		$arrIDSbrands[$key] = $value["ITEM_ID"];
		
	}
	//deb($arrIDSbrands);	
	// массив ID фоток
	$PREVIEW_PICTURE_ID = array();
	$arParams['PRODUCTS_PHOTOS_ID'] = array();
	
	$arFilter = array();
	$arFilter["ID"] = $arrIDSbrands;
	$arSelect = array(
		'IBLOCK_ID',
		'IBLOCK_NAME',
		'ID',
		'NAME',
		'PREVIEW_PICTURE', 'DETAIL_TEXT'
	);

	//deb($arFilter);
	$rsElement = CIBlockElement::GetList(array('ID' => "DESC"), $arFilter, false, false, $arSelect);
	//deb($rsElement->SelectedRowsCount());
	while($data = $rsElement -> Fetch())
	{
		$arResult["ELEMENTS"][$data["ID"]] = $data;
		// соберём ID фотографий
		$PREVIEW_PICTURE_ID[ $data["ID"] ] = $data["PREVIEW_PICTURE"];
		
		
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
//deb($arResult['PREVIEW_PICTURE']);
// присоединяем нужные данные к массиву поиска
?>