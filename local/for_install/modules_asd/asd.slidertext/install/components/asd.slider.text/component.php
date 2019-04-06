<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if ($arParams['TIME'] <= 0) {
	$arParams['TIME'] = 5;
}
$arParams['TIME'] = intval($arParams['TIME']);
$arParams['COUNT'] = intval($arParams['COUNT']);
$arParams['PIC_FROM'] = trim($arParams['PIC_FROM']);
$arParams['ONLY_MARKED'] = trim($arParams['ONLY_MARKED']);
$arParams['FIELD_WITH_LINK'] = trim($arParams['FIELD_WITH_LINK']);

if ($this->StartResultCache(false)) {
	if (!CModule::IncludeModule('iblock')) {
		$this->AbortResultCache();
		return;
	}

	$arResult = array();
	$arSort = array($arParams['SORT_BY'] => $arParams['SORT_ORDER']);
	$arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y');
	if (strlen($arParams['ONLY_MARKED'])) {
		$arFilter['!PROPERTY_' . $arParams['ONLY_MARKED']] = false;
	}

	$arSelect = array('NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL');
	if ($arParams['PIC_FROM']!='PREVIEW_PICTURE' && $arParams['PIC_FROM']!='DETAIL_PICTURE' && strlen($arParams['PIC_FROM'])) {
		$arSelect[] = 'PROPERTY_' . $arParams['PIC_FROM'];
	}
	if (strlen($arParams['FIELD_WITH_LINK'])) {
		if ($arParams['FIELD_WITH_LINK']!='CODE' && $arParams['FIELD_WITH_LINK']!='XML_ID') {
			$arParams['FIELD_WITH_LINK'] = 'PROPERTY_'.$arParams['FIELD_WITH_LINK'];
		}
		$arSelect[] = $arParams['FIELD_WITH_LINK'];
	}

	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, $arParams['COUNT']>0 ? array('nTopCount' => $arParams['COUNT']) : false, $arSelect);
	while ($arElements = $rsElements->GetNext(true, false)) {
		if (strlen($arParams['FIELD_WITH_LINK'])) {
			if ($arParams['FIELD_WITH_LINK']=='CODE' || $arParams['FIELD_WITH_LINK']=='XML_ID') {
				$arElements['DETAIL_PAGE_URL'] = $arElements[$arParams['FIELD_WITH_LINK']];
			} else {
				$arElements['DETAIL_PAGE_URL'] = $arElements[$arParams['FIELD_WITH_LINK'].'_VALUE'];
			}
		}
		if ($arParams['PIC_FROM'] == 'DETAIL_PICTURE') {
			$arElements['PICTURE'] = CFIle::GetFileArray($arElements['DETAIL_PICTURE']);
		} elseif ($arParams['PIC_FROM']=='PREVIEW_PICTURE' || !strlen($arParams['PIC_FROM'])) {
			$arElements['PICTURE'] = CFIle::GetFileArray($arElements['PREVIEW_PICTURE']);
		} else {
			$arElements['PICTURE'] = CFIle::GetFileArray($arElements['PROPERTY_' . $arParams['PIC_FROM'] . '_VALUE']);
		}
		$arResult[] = $arElements;
	}

	$this->SetResultCacheKeys(array('ID'));
	$this->IncludeComponentTemplate();
}
?>