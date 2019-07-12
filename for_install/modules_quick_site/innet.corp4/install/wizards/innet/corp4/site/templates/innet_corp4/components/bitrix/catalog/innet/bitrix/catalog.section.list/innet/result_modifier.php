<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
/*$arItems = array();

$arOrder = Array("SORT" => "ASC");
$arSelect = Array("NAME", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
$arFilter = Array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "GLOBAL_ACTIVE" => "Y");

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
$arResult['INNET_SECTIONS'] = array();
$sectionLinc[0] = &$arResult['INNET_SECTIONS'];
foreach ($arResult['SECTIONS'] as $arSection){
    $arSection['IBLOCK_SECTION_ID'] = intval($arSection['IBLOCK_SECTION_ID']);

    /*if(!empty($arItems[$arSection['ID']])){
        $arSection['ELEMENTS'] = $arItems[$arSection['ID']];
    }*/

    $sectionLinc[$arSection['IBLOCK_SECTION_ID']]['SUB_SECTION'][$arSection['ID']] = $arSection;
    $sectionLinc[$arSection['ID']] = &$sectionLinc[$arSection['IBLOCK_SECTION_ID']]['SUB_SECTION'][$arSection['ID']];
}
unset($sectionLinc);
?>