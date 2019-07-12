<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//deb($arResult["ORDERS"]);
$productIDS = array();
foreach ($arResult["ORDERS"] as $key => $order) {

    foreach ($order["BASKET_ITEMS"] as $k => $item) {
        //deb($item);
        if (!in_array($item["PRODUCT_ID"], $productIDS)) {
            $productIDS[] = $item["PRODUCT_ID"];
        }
    }
}
//deb($productIDS);
$arResult["PRODUCTS"] = array();

if (count($productIDS) > 0) {

    $referenceIDS = array();

    $arFilter = array("ID" => $productIDS);
    $arSelect = array( "ID", "NAME", "CODE", "PROPERTY_COLOR", "PROPERTY_STD_SIZE" );
    $rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
    while ($data = $rsElement -> Fetch())
    {
      // deb($data);
        $arResult["PRODUCTS"][$data["ID"]] = $data;
        if (!empty($data["PROPERTY_COLOR_VALUE"])) $referenceIDS[] = $data["PROPERTY_COLOR_VALUE"];
        if (!empty($data["PROPERTY_STD_SIZE_VALUE"])) $referenceIDS[] = $data["PROPERTY_STD_SIZE_VALUE"];

    }

    $referenceArr = array();
    if (count($referenceIDS) >0) {
        $arFilter = array("ID" => $referenceIDS);
        $arSelect = array( "ID", "NAME" );
        $rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
        while ($data = $rsElement -> Fetch())
        {
           // deb($data);
            $referenceArr[$data["ID"]] = $data["NAME"];
        }
    }
    foreach ($arResult["PRODUCTS"] as $key => $data) {
        if (!empty($data["PROPERTY_COLOR_VALUE"])) {
            $arResult["PRODUCTS"][$key]["COLOR"] = $referenceArr[$data["PROPERTY_COLOR_VALUE"]];

        } else {
            $arResult["PRODUCTS"][$key]["COLOR"] = '';
        }
        if (!empty($data["PROPERTY_STD_SIZE_VALUE"])) {

            $arResult["PRODUCTS"][$key]["STD_SIZE"] = $referenceArr[$data["PROPERTY_STD_SIZE_VALUE"]];
        } else {
            $arResult["PRODUCTS"][$key]["STD_SIZE"] = '';
        }
    }
   // deb($referenceArr);
}
//deb($arResult["PRODUCTS"]);
?>