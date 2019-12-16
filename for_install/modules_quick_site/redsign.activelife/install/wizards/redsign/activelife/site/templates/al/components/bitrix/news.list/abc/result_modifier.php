<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

$arResult['CATALOG_LINKED_PROP'] = false;
if ('' != $arParams['CATALOG_LINKED_PROP'] && '-' != $arParams['CATALOG_LINKED_PROP']) {
	$arParams['CATALOG_IBLOCK_ID'] = intval($arParams['CATALOG_IBLOCK_ID']);
	if ($arParams['CATALOG_IBLOCK_ID'] > 0) {
		$propRes = CIBlockProperty::GetList(array('SORT' => 'ASC','ID' => 'DESC'), array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arParams['CATALOG_IBLOCK_ID'], 'CODE' => $arParams['CATALOG_LINKED_PROP']));
		if ($arFields = $propRes->GetNext()) {
			$arResult['CATALOG_LINKED_PROP'] = $arFields;
		}
	}
}
if (!isset($arParams['SECTIONS_PROP']) || '-' == $arParams['SECTIONS_PROP']) {
	$arParams['SECTIONS_PROP'] = false;
}


$arCharsDigital = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
$arCharsEn = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
$arCharsRu = explode(' ', getMessage('RS_SLINE.BNL_TILES.LETTERS_RU'));
foreach ($arResult['ITEMS'] as $iItemsKey => $arItem) {
    $cLetter = substr($arItem['NAME'], 0, 1);
    if (in_array($cLetter, $arCharsEn)) {
        $arResult['LETTERS']['LETTER_ENG'][$cLetter][] = $iItemsKey;
    } elseif (in_array($cLetter, $arCharsRu)) {
        $arResult['LETTERS']['LETTER_RUS'][$cLetter][] = $iItemsKey;
    } elseif (in_array($cLetter, $arCharsDigital)) {
        $arResult['LETTERS']['DIGITAL'][$cLetter][] = $iItemsKey;
    } else {
        $arResult['LETTERS']['OTHER'][$cLetter][] = $iItemsKey;
    }
}
if (is_array($arResult['LETTERS']['LETTER_ENG'])) {
    ksort($arResult['LETTERS']['LETTER_ENG']);
}
if (is_array($arResult['LETTERS']['LETTER_RUS'])) {
    ksort($arResult['LETTERS']['LETTER_RUS']);
}
if (is_array($arResult['LETTERS']['DIGITAL'])) {
    ksort($arResult['LETTERS']['DIGITAL']);
}