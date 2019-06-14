<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if ($arParams['ELEMENT_ID'] <= 0) {
	ShowError(GetMessage('ASD_ERROR_NOT_ID'));
	return;
}
if (!CModule::IncludeModule('asd.favorite')) {
	return;
}

$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);
$arParams['FAV_TYPE'] = trim($arParams['FAV_TYPE']);
$arParams['BUTTON_TYPE'] = trim($arParams['BUTTON_TYPE']);
if (!strlen($arParams['FAV_TYPE'])) {
	$arParams['FAV_TYPE'] = 'unknown';
}
if (!strlen($arParams['BUTTON_TYPE'])) {
	$arParams['BUTTON_TYPE'] = 'fav';
}
$arParams['BUTTON_TYPE_UPP'] = strtoupper($arParams['BUTTON_TYPE']);

if ($arParams['GET_COUNT_AFTER_LOAD'] != 'Y') {
	if (empty($arParams['COUNT'])) {
		$rsLikes = CASDfavorite::GetLikes(array('ELEMENT_ID' => $arParams['ELEMENT_ID'], 'CODE' => $arParams['FAV_TYPE']));
		$arResult = array('COUNT' => $rsLikes->SelectedRowsCount(), 'FAVED' => 'N');
	} else {
		$arResult = array('COUNT' => $arParams['COUNT'], 'FAVED' => 'N');
	}
	if ($USER->IsAuthorized()) {
		if ($arParams['FAVED'] != 'Y') {
			if (CASDfavorite::GetLikes(array('ELEMENT_ID' => $arParams['ELEMENT_ID'], 'CODE' => $arParams['FAV_TYPE'], 'USER_ID' => $USER->GetID()))->Fetch()) {
				$arResult['FAVED'] = 'Y';
			}
		} else {
			$arResult['FAVED'] = 'Y';
		}
	}
}

if ($arParams['GET_COUNT_AFTER_LOAD'] == 'Y') {
	$arResult['STYLES'] = '<link href="'.$this->__path.'/templates/.default/style.css" type="text/css" rel="stylesheet" />';
	if (version_compare(SM_VERSION, '12.0.9')>=0) {
		$arResult['STYLES'] .= '<script type="text/javascript" src="'.$this->__path.'/templates/.default/script.js"></script>';
	}
}

$this->IncludeComponentTemplate();
$GLOBALS['ASD_FAV_SHOWED'] = true;