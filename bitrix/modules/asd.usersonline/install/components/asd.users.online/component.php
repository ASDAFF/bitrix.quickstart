<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arParams['USER_PATH'] = trim($arParams['USER_PATH']);

if (!isset($arParams['POPUP'])) {
	$arParams['POPUP'] = 'N';
}

if ($arParams['POPUP'] == 'Y') {
	$ajaxPage = $APPLICATION->GetCurPageParam('', array('bxajaxid', 'logout'));
	CUtil::InitJSCore(array('ajax', 'window', 'tooltip', 'popup'));
}

if (!strlen($arParams['SITE_ID']) && (!defined('ADMIN_SECTION') || ADMIN_SECTION!==true)) {
	$arParams['SITE_ID'] = SITE_ID;
} elseif (strlen($arParams['SITE_ID'])) {
	$arParams['SITE_ID'] = $arParams['SITE_ID'];
} else {
	$arParams['SITE_ID'] = '';
}

if ($this->StartResultCache()) {

	if (!CModule::IncludeModule('statistic')) {
		$this->AbortResultCache();
		ShowError(GetMessage('ASD_CMP_NOT_SERVICE'));
		return;
	}

	$arShowed = array(-1);
	$arResult['USERS'] = array();
	if ($arParams['GET_ALL_USERS'] == 'Y') {
		$arResult['ALL_USERS'] = array();
	}
	$arResult['GUESTS'] = 0;

	$rsUsers = CUserOnline::GetList($guest_counter, $session_counter);
	while ($arUsers = $rsUsers->Fetch()) {
		if (strlen($arParams['SITE_ID']) && $arUsers['LAST_SITE_ID']!=$arParams['SITE_ID']) {
			continue;
		}
		if ($arUsers['LAST_USER_ID']>0 && !in_array($arUsers['LAST_USER_ID'], $arShowed)) {
			$arShowed[] = $arUsers['LAST_USER_ID'];
			$arResult['USERS'][$arUsers['LAST_USER_ID']] = array(
																'URL_LAST' => $arUsers['URL_LAST'],
																'GUEST_ID' => $arUsers['GUEST_ID'],
																);
		} elseif ($arUsers['LAST_USER_ID'] <= 0) {
			$arResult['GUESTS']++;
		}
		if ($arParams['GET_ALL_USERS'] == 'Y') {
			$arResult['ALL_USERS'][$arUsers['GUEST_ID']] = array('URL_LAST' => $arUsers['URL_LAST']);
		}
	}

	if (!empty($arResult['USERS'])) {
		$rsUsers = CUser::GetList($by = 'id', $order = 'asc', array('ID' => implode('|', array_keys($arResult['USERS']))));
		while ($arUser = $rsUsers->GetNext()) {
			$uName = trim($arUser['NAME'] . ' ' . $arUser['LAST_NAME']);
			if (!strlen($uName))
				$uName = $arUser['LOGIN'];
			if ($arParams['POPUP'] == 'Y') {
				$popupStr = '<script type="text/javascript">BX.tooltip(' . $arUser['ID'] . ', \'anchor_' . $arUser['ID'] . '\', \'' . CUtil::JSEscape($ajaxPage) . '\');</script>';
			} else {
				$popupStr = '';
			}
			$arResult['USERS'][$arUser['ID']] = array(
				'ID' => $arUser['ID'],
				'URL_LAST' => $arResult['USERS'][$arUser['ID']]['URL_LAST'],
				'GUEST_ID' => $arResult['USERS'][$arUser['ID']]['GUEST_ID'],
				'NAME' => $uName,
				'POPUP' => $popupStr,
				'PATH' => strlen($arParams['USER_PATH']) ? str_replace('#ID#', $arUser['ID'], $arParams['USER_PATH']) : '');
		}
	}

	$this->IncludeComponentTemplate();
}