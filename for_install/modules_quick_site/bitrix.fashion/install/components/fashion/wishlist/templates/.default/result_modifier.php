<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["ITEMS"] as &$arItem)
{
    $arOffers = CIBlockPriceTools::GetOffersIBlock($arItem["FIELDS"]["IBLOCK_ID"]);
    $offers = CIBlockElement::GetList(
        array(),
        array(
            "IBLOCK_ID" => $arOffers["OFFERS_IBLOCK_ID"],
            "ACTIVE" => "Y",
            "PROPERTY_CML2_LINK" => $arItem["FIELDS"]["ID"]
        ),
        false, false);
    $offer = $offers->GetNextElement();
    $offerProperties = $offer->GetProperties();
    $colorId = $offerProperties["colors"]["VALUE"];
    foreach($arItem["PROPERTIES"]["PHOTOS"]["~VALUE"] as $imagesUnserialized)
    {
        $images = unserialize($imagesUnserialized);
        if($images["VALUE"] == $colorId) {
            $photos = $images["FILES"];
        }
    }
    $file = CFile::ResizeImageGet($photos[0]["FILE"], array("width" => 130, "height" => 176), BX_RESIZE_IMAGE_PROPORTIONAL, false);

    $arItem["FIELDS"]["PREVIEW_PICTURE"] = $file["src"];

}