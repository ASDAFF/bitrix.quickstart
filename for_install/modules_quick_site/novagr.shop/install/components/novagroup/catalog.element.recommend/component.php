<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//deb($arParams["IDS"]);
if ($this->StartResultCache(false)) {

    $CIBlockElement = new CIBlockElement();
    $rsElement = $CIBlockElement->GetList(array(), array("ID" => $arParams['ELEMENT_ID']),false,false,array("ID","PROPERTY_RECOMMEND"));

    if ($obElement = $rsElement->Fetch()) {
        if (count($PROPERTY_CML2_LINK = $obElement["PROPERTY_RECOMMEND_VALUE"]) > 0) {
            $generalCatalogOffers = new Novagroup_Classes_General_CatalogRecommend($arParams['CATALOG_IBLOCK_ID'], $arParams['OFFERS_IBLOCK_ID']);
            $generalCatalogOffers->addOffersFilter(array(0 => array("PROPERTY_CML2_LINK" => $PROPERTY_CML2_LINK)));

            // сделаем выборку элементов
            $RECOMMEND_ELEMENTS = $generalCatalogOffers->getElementList();
            $arResult['RECOMMEND_ELEMENTS'] = $RECOMMEND_ELEMENTS;
        }
    }
    $this->IncludeComponentTemplate();
}
?>