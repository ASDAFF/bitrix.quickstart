<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock")) {
    ShowError(GetMessage("INNET_MAIN_SLIDER_ERROR_NOT_MODULE_IBLOCK"));
    return;
}

if (IntVal($arParams["IBLOCK_ID"]) < 1) {
    ShowError(GetMessage("INNET_MAIN_SLIDER_ERROR_IBLOCK_ID"));
    return;
}

if ($this->StartResultCache()) {

    $arResult = array();

    $arOrder = Array("SORT" => "ASC");
    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y");
    $arGroupBy = false;
    $arNavStartParams = array("nTopCount" => $arParams["COUNT_ELEMENTS"]);
    $arSelect = array("ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_SLIDER_LINK", "PROPERTY_SLIDER_LINK_2", "PROPERTY_SLIDER_LINK_NAME", "PROPERTY_SLIDER_LINK_NAME_2");

    $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
    while ($ob = $res->Fetch()) {
        $ob["PREVIEW_PICTURE"] = CFile::GetPath($ob["PREVIEW_PICTURE"]);
        $arResult[] = $ob;
    }

    $this->IncludeComponentTemplate();
}
?>
