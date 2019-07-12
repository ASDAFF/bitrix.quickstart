<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")){
    $dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array("BASE" => "Y")
    );
    
    if ($arPriceType = $dbPriceType->Fetch()) {
        $basePriceType  = $arPriceType["NAME"];
        $arResultPrices = CIBlockPriceTools::GetCatalogPrices(intval($arResult["ITEMS"][0]["PROPERTIES"]["products"]["LINK_IBLOCK_ID"]), array($basePriceType));
    }
    
    foreach ($arResult["ITEMS"] as $key => &$arElement) {
        if (empty($arElement["DISPLAY_PROPERTIES"])) {
            $arElement["OFFERS"] = array();
            continue;
        }
        
        $res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>intval($arElement["PROPERTIES"]["products"]["LINK_IBLOCK_ID"]), "ACTIVE"=>"Y", "ID"=>$arElement["PROPERTIES"]["products"]["VALUE"]), false, false, array("ID", "NAME", "DETAIL_PAGE_URL"));
        while ($arFields = $res->GetNext()) {
            $arOffers = CIBlockPriceTools::GetOffersArray(
               intval($arElement["PROPERTIES"]["products"]["LINK_IBLOCK_ID"])
               ,array($arFields["ID"])
               ,array("sort" => "asc")
               ,array($arFields["NAME"])
               ,array()
               ,array()
               ,$arResultPrices
               ,array()
            );
            
            $minPrice = $arOffers[0]["PRICES"][$basePriceType]["VALUE"];
            
            foreach ($arOffers as $offer) {
                if ($offer["PRICES"][$basePriceType]["VALUE"] < $minPrice) {
                    $minPrice = $offer["PRICES"][$basePriceType]["VALUE"];
                }
            }
            
            $arElement["OFFERS"][] = array_merge($arFields, array("PRICE" => $minPrice, "CURRENCY" => $arOffers[0]["PRICES"][$basePriceType]["CURRENCY"]));
        }
    }
}
?>