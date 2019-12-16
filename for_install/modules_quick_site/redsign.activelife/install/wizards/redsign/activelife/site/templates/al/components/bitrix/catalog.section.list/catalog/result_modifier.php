<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (is_array($arResult['SECTIONS']) && count($arResult['SECTIONS'])) {
	foreach ($arResult['SECTIONS'] as $iSectionKey => $arSection) {
		$arResult['SECTIONS'][$iSectionKey]['IS_PARENT'] = false;
		$arResult['SECTIONS'][$iSectionKey]['HAVE_SUBSECTIONS'] = false;
		if (intval($arSection['RIGHT_MARGIN']) - IntVal($arSection['LEFT_MARGIN']) > 1) {
			$arResult['SECTIONS'][$iSectionKey]['HAVE_SUBSECTIONS'] = true;
			if ($arResult['SECTIONS'][$iSectionKey]['DEPTH_LEVEL'] == 1) {
				$arResult['SECTIONS'][$iSectionKey]['IS_PARENT'] = true;
			}
		}
	}
}
if ($arParams['SHOW_SECTION_PICTURE'] == 'Y' && $arResult['SECTION']['PICTURE'] > 0){
	if (!is_array($arResult['SECTION']['PICTURE'])) {
		$arResult['SECTION']['PICTURE'] = CFile::GetFileArray($arResult['SECTION']['PICTURE']);
	}
	$arResult['SECTION']['PICTURE']['RESIZE'] = CFile::ResizeImageGet(
        $arResult['SECTION']['PICTURE'],
        array('width' => $arParams['PICTURE_MAX_WIDTH'], 'height' => $arParams['PICTURE_MAX_HEIGHT']),
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );
}
