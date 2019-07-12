<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (CModule::IncludeModule("iblock")) {
    if ($this->StartResultCache(false)) {
        $arFilter = array(
            'ACTIVE' => "Y",
            'IBLOCK_CODE' => $arParams['BRANDS_IBLOCK_CODE'],
            "ID" => $arParams['BRAID_ID'],
        );
        $rsElement = CIBlockElement::GetList(
            array(),
            $arFilter
        );
        $arResult['BRAND'] = $rsElement->GetNext();

        /**
         * get seo templates
         */
        $rsSeoData = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult['BRAND']["IBLOCK_ID"], $arResult['BRAND']['ID']);
        $arResult["IPROPERTY_VALUES"] = $rsSeoData->getValues();

        $this->IncludeComponentTemplate();
    }
}

?>