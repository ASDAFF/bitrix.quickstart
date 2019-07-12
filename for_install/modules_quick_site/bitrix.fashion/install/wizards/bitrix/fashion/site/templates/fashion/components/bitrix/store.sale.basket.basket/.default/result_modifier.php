<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$totalSum = 0;
foreach($arResult["ITEMS"]["AnDelCanBuy"] as &$item){
    $resGetByID = CIBlockElement::GetByID($item["PRODUCT_ID"]);
    
    if ($arGetByID = $resGetByID->GetNext()) {
        $item["TOTAL"] = $item["PRICE"] * $item["QUANTITY"];
        $item["BASE_PRICE"] = CPrice::GetBasePrice($item["PRODUCT_ID"]);
        
        $totalSum += $item["BASE_PRICE"]["PRICE"] * $item["QUANTITY"];
        
        $resGetList = CIBlockElement::GetList(
            array(),
            array("IBLOCK_ID"=>$arGetByID["IBLOCK_ID"], "ID"=>$item["PRODUCT_ID"], "ACTIVE"=>"Y"),
            false,
            array("nTopCount"=>1),
            array("ID", "IBLOCK_ID", "PROPERTY_item_color.NAME", "PROPERTY_item_color.CODE", "PROPERTY_item_color.DETAIL_PICTURE", "PROPERTY_item_color.PROPERTY_hex", "PROPERTY_item_size.NAME")
        );
        if ($ob = $resGetList->GetNextElement()) {
            $arFields = $ob->GetFields();
            $item["OFFER"] = $arFields;
            
            $arProps = $ob->GetProperties();
            $item["OFFER"]["PROPERTY_ITEM_MORE_PHOTO_VALUE"] = $arProps["item_more_photo"]["VALUE"][0];
            
            $arResGetByIDEx = CCatalogProduct::GetByIDEx($arProps["model"]["VALUE"]);
            $item["OFFER"]["models_hit"]  = (strlen($arResGetByIDEx["PROPERTIES"]["models_hit"]["VALUE"]) > 0 ? 1 : 0);
            $item["OFFER"]["models_new"]  = (strlen($arResGetByIDEx["PROPERTIES"]["models_new"]["VALUE"]) > 0 ? 1 : 0);
            $item["OFFER"]["models_sale"] = ($item["BASE_PRICE"]["PRICE"] > $item["PRICE"] ? 1 : 0);
            
            $item["DETAIL_PAGE_URL"] = rtrim($arResGetByIDEx["DETAIL_PAGE_URL"], "/") . "/" . $arFields["PROPERTY_ITEM_COLOR_CODE"] . "/";
        }
    }
}
$arResult["TOTAL_SUM"] = $totalSum;
?>