<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock")) {
    ShowError(GetMessage("INNET_SECTION_LIST_ERROR_NOT_MODULE_IBLOCK"));
    return;
}

if (IntVal($arParams["IBLOCK_ID"]) < 1) {
    ShowError(GetMessage("INNET_SECTION_LIST_ERROR_IBLOCK_ID"));
    return;
}

if ($this->StartResultCache()) {

    $count_sections = IntVal($arParams['COUNT_SECTION']) > 0 ? $arParams['COUNT_SECTION'] : 8;
    $count_nested_sections = IntVal($arParams['COUNT_SECTION_NESTED']) > 0 ? $arParams['COUNT_SECTION_NESTED'] : 5;
    $section_id = array();
    $section = array();
    $sort = !empty($arParams['SORT']) && strlen($arParams['SORT']) > 0 ? $arParams['SORT'] : "asc";

    $arOrder = Array("SORT" => $sort);
    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y");
    $arFilter[$arParams['UF_CODE']] = true;
    $bIncCnt = array("Y");
    $arSelect = array("ID", "NAME", "SECTION_PAGE_URL", "DETAIL_PICTURE", "SORT", "ELEMENT_CNT", "DESCRIPTION", "UF_DESCRIPTION_INDEX");
    $arNavStartParams = array("nPageSize" => $count_nested_sections);

    $section_list = CIBlockSection::GetList($arOrder, $arFilter, $bIncCnt, $arSelect);
    $section_list->NavStart($count_sections);
    while ($sec = $section_list->GetNext()) {
        $section_id[] = $sec['ID'];

        $sec["DETAIL_PICTURE"] = CFile::GetFileArray($sec["DETAIL_PICTURE"]);

        if (IntVal($arParams["WIDTH"]) > 0 && IntVal($arParams["HEIGHT"]) > 0) {
            $resizeimg = CFile::ResizeImageGet($sec["DETAIL_PICTURE"], array("width" => $arParams["WIDTH"], "height" => $arParams["HEIGHT"]), BX_RESIZE_IMAGE_EXACT, true);
            $sec["DETAIL_PICTURE"] = array_change_key_case($resizeimg, CASE_UPPER);
        }

        $section[$sec['ID']] = $sec;
    }

    if ($section_id) {
        $sortSubSection = !empty($arParams['SORT_SUB_SECTION']) && strlen($arParams['SORT_SUB_SECTION']) > 0 ? $arParams['SORT_SUB_SECTION'] : "asc";
        $arFilter = array('IBLOCK_ID' => $arParams["IBLOCK_ID"], 'SECTION_ID' => $section_id, "ACTIVE" => "Y");
        $section_list = CIBlockSection::GetList(Array("SORT" => $sortSubSection), $arFilter, true, array("ID", "IBLOCK_SECTION_ID", "NAME", "SORT", "CODE", "SECTION_PAGE_URL", "ELEMENT_CNT"));
        while ($section_result = $section_list->GetNext()) {
            if (count($section[$section_result['IBLOCK_SECTION_ID']]['NESTED_SECTION']) == $count_nested_sections) {
                continue;
            }
            $section[$section_result['IBLOCK_SECTION_ID']]['NESTED_SECTION'][$section_result['ID']] = $section_result;
        }
    }

    $arResult = $section;

    $this->IncludeComponentTemplate();
}
?>