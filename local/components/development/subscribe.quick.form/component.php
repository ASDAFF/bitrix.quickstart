<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arResult = array();
$arParams['JS_KEY'] = md5(LICENSE_KEY);
$arParams['RUBRICS'] = is_array($arParams['RUBRICS']) ? $arParams['RUBRICS'] : array();
$arParams['RUBRICS_STR'] = implode('|', $arParams['RUBRICS']);
$arParams['USER_EMAIL'] = $USER->GetEmail();
$arParams['SHOW_RUBRICS'] = !isset($arParams['SHOW_RUBRICS'])||$arParams['SHOW_RUBRICS']!='Y' ? 'N' : 'Y';
$arParams['NOT_CONFIRM'] = !isset($arParams['NOT_CONFIRM'])||$arParams['NOT_CONFIRM']!='Y' ? 'N' : 'Y';
$arParams['FORMAT'] = isset($arParams['FORMAT']) ? $arParams['FORMAT'] : 'text';

if ($arParams['INC_JQUERY'] == 'Y') {
	CUtil::InitJSCore(array('jquery'));
}

if ($arParams['SHOW_RUBRICS']=='Y' && !empty($arParams['RUBRICS'])) {
	$cache = new CPHPCache;
	$cache_id = serialize($arParams['RUBRICS']);
	$cache_path = $GLOBALS['CACHE_MANAGER']->GetCompCachePath($this->__relativePath);
	if ($cache->InitCache(3600, $cache_id, $cache_path, 'cache')) {
		$Vars = $cache->GetVars();
		foreach ($Vars['arResult'] as $k => $v) {
			$arResult[$k] = $v;
		}
	} else {
		$arResult['RUBRICS'] = array();
		$cache->StartDataCache(3600, $cache_id);
		if (CModule::IncludeModule('subscribe')) {
			$rsRub = CRubric::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'));
			while ($arRub = $rsRub->GetNext()) {
				if (in_array($arRub['ID'], $arParams['RUBRICS'])) {
					$arResult['RUBRICS'][$arRub['ID']] = $arRub['NAME'];
				}
			}
		}
		$cache->EndDataCache(array('arResult' => $arResult));
	}
}

$arResult['ACTION'] = require_once 'action.php';

$this->IncludeComponentTemplate();