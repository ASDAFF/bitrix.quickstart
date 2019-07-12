<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// к результату добавляем превью картинок
$currentPhotoArr = array();
$sizesIDS = array();
$colorIDS = array();
$productIDS = array();

foreach($arResult["ITEMS"]["AnDelCanBuy"] as $key => $arBasketItems)
{

	$productIDS[] = $arBasketItems["PRODUCT_ID"];
	$res = CIBlockElement::GetByID($arBasketItems["PRODUCT_ID"]);
	$arResult["ITEMS"]["AnDelCanBuy"][$key]["PREVIEW_PICTURE"] = "";
	if($obRes = $res->GetNextElement())
	{

		$arRes = $obRes->GetProperties();

        $mxResult = CCatalogSku::GetProductInfo(
            $arBasketItems["PRODUCT_ID"]
        );

		$arResult["ITEMS"]["AnDelCanBuy"][$key]["ELEMENT_ID"] = $mxResult;
        
        $sizesIDS[] = $arRes["STD_SIZE"]["VALUE"];
        $arResult["ITEMS"]["AnDelCanBuy"][$key]["SIZE"] = $arRes["STD_SIZE"]["VALUE"];
		
		$colorIDS[] =  $arRes["COLOR"]["VALUE"];
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["COLOR"] = $arRes["COLOR"]["VALUE"];
		
		$currentPhotoArr[] = $arRes["PHOTOS"]["VALUE"][0];
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["PREVIEW_PICTURE"] = $arRes["PHOTOS"]["VALUE"][0];
	}
}

// получаем число товаров на складе

$db_res = CCatalogProduct::GetList(
		array("QUANTITY" => "DESC"),
		array("ID" => $productIDS),
		false
);
$arResult["PRODUCTS_QUANTITIES"] = array();
while (($data = $db_res->Fetch()) )
{
    $arResult["PRODUCTS_QUANTITIES"][$data["ID"]] = $data["QUANTITY"];
    $mxResult = CCatalogSku::GetProductInfo(
        $data["ID"]
    );
    if (is_array($mxResult)) {
        $action = new Novagroup_Classes_General_TimeToBuy($mxResult['ID'], $mxResult['IBLOCK_ID']);
        if ($action->checkAction()) {
            $getAction = $action->getAction();
            $arResult["PRODUCTS_QUANTITIES"][$data["ID"]] = $getAction['PROPERTY_QUANTITY_VALUE'];
        }
    }
}

// для отложенных
foreach($arResult["ITEMS"]["DelDelCanBuy"] as $key => $arBasketItems)
{

	$res = CIBlockElement::GetByID($arBasketItems["PRODUCT_ID"]);
	$arResult["ITEMS"]["DelDelCanBuy"][$key]["PREVIEW_PICTURE"] = "";
	if($obRes = $res->GetNextElement())
	{
		$arRes = $obRes->GetProperties();
        
        $mxResult = CCatalogSku::GetProductInfo(
            $arBasketItems["PRODUCT_ID"]
        );

        $arResult["ITEMS"]["DelDelCanBuy"][$key]["ELEMENT_ID"] = $mxResult;
        
		$sizesIDS[] = $arRes["STD_SIZE"]["VALUE"];
		$arResult["ITEMS"]["DelDelCanBuy"][$key]["SIZE"] = $arRes["STD_SIZE"]["VALUE"];
		
		$colorIDS[] =  $arRes["COLOR"]["VALUE"];
		$arResult["ITEMS"]["DelDelCanBuy"][$key]["COLOR"] = $arRes["COLOR"]["VALUE"];
		
		$currentPhotoArr[] = $arRes["PHOTOS"]["VALUE"][0];
		$arResult["ITEMS"]["DelDelCanBuy"][$key]["PREVIEW_PICTURE"] = $arRes["PHOTOS"]["VALUE"][0];
	}
}

// для отсуствующих
foreach($arResult["ITEMS"]["nAnCanBuy"] as $key => $arBasketItems)
{

    $res = CIBlockElement::GetByID($arBasketItems["PRODUCT_ID"]);
    $arResult["ITEMS"]["nAnCanBuy"][$key]["PREVIEW_PICTURE"] = "";
    if($obRes = $res->GetNextElement())
    {
        $arRes = $obRes->GetProperties();

        $mxResult = CCatalogSku::GetProductInfo(
            $arBasketItems["PRODUCT_ID"]
        );

        $arResult["ITEMS"]["nAnCanBuy"][$key]["ELEMENT_ID"] = $mxResult;

        $sizesIDS[] = $arRes["STD_SIZE"]["VALUE"];
        $arResult["ITEMS"]["nAnCanBuy"][$key]["SIZE"] = $arRes["STD_SIZE"]["VALUE"];

        $colorIDS[] =  $arRes["COLOR"]["VALUE"];
        $arResult["ITEMS"]["nAnCanBuy"][$key]["COLOR"] = $arRes["COLOR"]["VALUE"];

        $currentPhotoArr[] = $arRes["PHOTOS"]["VALUE"][0];
        $arResult["ITEMS"]["nAnCanBuy"][$key]["PREVIEW_PICTURE"] = $arRes["PHOTOS"]["VALUE"][0];
    }
}

// для ожидаемых
foreach($arResult["ITEMS"]["ProdSubscribe"] as $key => $arBasketItems)
{

	$rsSubElement = CIBlockElement::GetList(
		array(),
		array('ID' => $arBasketItems["PRODUCT_ID"]),
		false,
		false,
		array('LANG_DIR', 'IBLOCK_ID', 'ID', 'CODE','DETAIL_PAGE_URL')
	);
	while($arSubElement = $rsSubElement -> GetNext())
		$arResult['mixData']['DETAIL_PAGE_URL'][$arSubElement['ID']] = $arSubElement['DETAIL_PAGE_URL'];

	$res = CIBlockElement::GetByID($arBasketItems["PRODUCT_ID"]);
	//$arResult["ITEMS"]["DelDelCanBuy"][$key]["PREVIEW_PICTURE"] = "";
	if ($obRes = $res->GetNextElement())
	{
		$arRes = $obRes->GetProperties();

		$mxResult = CCatalogSku::GetProductInfo($arBasketItems["PRODUCT_ID"]);

		$arResult["ITEMS"]["ProdSubscribe"][$key]["ELEMENT_ID"] = $mxResult;

		//$sizesIDS[] = $arRes["STD_SIZE"]["VALUE"];
		//$arResult["ITEMS"]["ProdSubscribe"][$key]["SIZE"] = $arRes["STD_SIZE"]["VALUE"];

		$colorIDS[] =  $arRes["COLOR"]["VALUE"];
		$arResult["ITEMS"]["ProdSubscribe"][$key]["COLOR"] = $arRes["COLOR"]["VALUE"];
		
	}
}

if( count($currentPhotoArr) > 0 )
{ 
	$arFilter = array(
			'ACTIVE' => "Y",
			'ID' => $currentPhotoArr,
	);

	$arSelect = array( 'ID', 'PREVIEW_PICTURE' );
	$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
	while($data = $rsElement -> GetNext())
	{
		$PREVIEW_PICTURE_ID[$data['ID']] = $data['PREVIEW_PICTURE'];
		//$DETAIL_PICTURE_ID[$data['ID']] = $data['DETAIL_PICTURE'];
	}
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
// находим размеры
$arResult['SIZES'] = array();
$arSelect = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID' );
$arFilter = array("ID" => $sizesIDS);
$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
$count = $rsElement->SelectedRowsCount();
while($data = $rsElement -> Fetch())
{
	$arResult['SIZES'][$data["ID"]] = $data["NAME"];
}
// находим цвета
$arResult['COLORS'] = array();
$arSelect = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID', 'PREVIEW_PICTURE' );
$arFilter = array("ID" => $colorIDS);
$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
$count = $rsElement->SelectedRowsCount();
while($data = $rsElement -> Fetch())
{
	$arResult['COLORS'][$data["ID"]]["PIC"] = $data["PREVIEW_PICTURE"];
	$arResult['COLORS'][$data["ID"]]["NAME"] = $data["NAME"];
}
?>