<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult["MESSAGE"] == "add") {
    $res = CIBlockElement::GetList(
        array(),
        array("IBLOCK_ID" => intval($arParams["IBLOCK_ID"]), "ACTIVE" => "Y", "PROPERTY_reviews_model" => intval($arParams["MODEL_ID"])),
        false,
        false,
        array("ID", "PROPERTY_reviews_rate")
    );
    
    $rate = $count = 0;
    while ($arFields = $res->GetNext()) {
        $rate += $arFields["PROPERTY_REVIEWS_RATE_VALUE"];
        $count++;
    }
    
    CIBlockElement::SetPropertyValuesEx(intval($arParams["MODEL_ID"]), intval($arParams["MODEL_IBLOCK_ID"]), array("models_rating" => round($rate / $count), "models_numvals" => $count));
}?>