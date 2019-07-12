<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

if (CModule::IncludeModule("iblock")) {

    $iblock_id = '#INNET_IBLOCK_ID_CATALOG#';

    /*$arItems = array();

    $arOrder = Array("SORT" => "ASC");
    $arSelect = Array("NAME", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
    $arFilter = Array("IBLOCK_ID" => $iblock_id, "GLOBAL_ACTIVE" => "Y");

    $res = CIBlockElement::GetList($arOrder, $arFilter, false, array("nPageSize" => 9999), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();

        $arItems[$arFields['IBLOCK_SECTION_ID']][] = array(
            'NAME' => $arFields['NAME'],
            'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
            'IBLOCK_SECTION_ID' => $arFields['IBLOCK_SECTION_ID'],
        );
    }*/

    $sectionLinc = array();
    $arResult = array();
    $sectionLinc[0] = &$arResult;

    $arOrder = array('DEPTH_LEVEL' => 'ASC', 'SORT' => 'ASC');
    $arFilter = array('IBLOCK_ID' => $iblock_id, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y');
    $arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'DEPTH_LEVEL', 'IBLOCK_SECTION_ID', 'SECTION_PAGE_URL');

    $rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
    while ($arSection = $rsSections->GetNext()) {
        $arSection['IBLOCK_SECTION_ID'] = intval($arSection['IBLOCK_SECTION_ID']);

        /*if(!empty($arItems[$arSection['ID']])){
            $arSection['ELEMENTS'] = $arItems[$arSection['ID']];
        }*/

        $sectionLinc[$arSection['IBLOCK_SECTION_ID']]['SUB_SECTION'][$arSection['ID']] = $arSection;
        $sectionLinc[$arSection['ID']] = &$sectionLinc[$arSection['IBLOCK_SECTION_ID']]['SUB_SECTION'][$arSection['ID']];
    }
    unset($sectionLinc);
    $arResult = $arResult['SUB_SECTION'];

    foreach ($arResult as $section){
        $aMenuLinksExt[$section['ID']] = Array(
            $section['NAME'],
            $section['SECTION_PAGE_URL'],
            array(),
            $section['SUB_SECTION']
        );
    }
}

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
?>