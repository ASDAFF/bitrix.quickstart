<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

CModule::IncludeModule('iblock');
if ($this->StartResultCache(3600)) {
    $iblock_id = $arParams['CODE'];
    
    $arFilter = array(
        'IBLOCK_CODE' => $iblock_id,
        'SECTION_ID' => $arParams['SECTION_ID'],
        "INCLUDE_SUBSECTIONS" => "Y"
    );
    
    $db_list = CIBlockElement::GetList(array('SORT' => 'ASC'), $arFilter, false, false, array(
                "ID",
                "CODE",
                "NAME",
                "PREVIEW_TEXT",
                "PREVIEW_PICTURE"));
    while ($ar_result = $db_list->GetNext()) {
       
        $arResult[] = array(
            "ID" => $ar_result['ID'],
            "NAME" => $ar_result['NAME'],
            "PREVIEW_TEXT" => $ar_result['PREVIEW_TEXT'],
            "PREVIEW_PICTURE" => CFile::GetPath($ar_result['PREVIEW_PICTURE'])
        );
    }

    $this->IncludeComponentTemplate();
}
?>
