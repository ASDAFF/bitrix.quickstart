<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {
    foreach ($arResult["BASKET"] as &$item) {
        $resGetByID = CIBlockElement::GetByID($item["PRODUCT_ID"]);
        
        if ($arGetByID = $resGetByID->GetNext()) {
            $resGetList = CIBlockElement::GetList(
                array(),
                array("IBLOCK_ID"=>$arGetByID["IBLOCK_ID"], "ID" => $item["PRODUCT_ID"], "ACTIVE" => "Y"),
                false,
                array("nTopCount"=>1),
                array("ID", "IBLOCK_ID", "PROPERTY_item_color.NAME", "PROPERTY_item_color.CODE", "PROPERTY_item_color.DETAIL_PICTURE", "PROPERTY_item_color.PROPERTY_hex", "PROPERTY_item_size.NAME")
            );
            if ($ob = $resGetList->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                
                $arResGetByIDEx = CCatalogProduct::GetByIDEx($arProps["model"]["VALUE"]);
                
                $item["DETAIL_PAGE_URL"] = rtrim($arResGetByIDEx["DETAIL_PAGE_URL"], "/") . "/" . $arFields["PROPERTY_ITEM_COLOR_CODE"] . "/";
            }
        }
    }
}?>