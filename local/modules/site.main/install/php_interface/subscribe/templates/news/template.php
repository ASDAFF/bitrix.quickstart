<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

try {
	list($iblockCode, $sectionCode) = explode('|', $arRubric['CODE']);
	
	$itemsCount = 0;
	$filter = array();
	if ($sectionCode) {
		$filter['SECTION_CODE'] = $sectionCode;
		$filter['INCLUDE_SUBSECTIONS'] = 'Y';
	}
	
	$itemsCount = $GLOBALS['APPLICATION']->IncludeComponent(
		'site:subscribe.news',
		'.default',
		array(
			'SITE_ID' => $arRubric['LID'],
			'IBLOCK_ID' => constant('\Site\Main\Iblock\ID_' . $iblockCode),
			'DATE_ACTIVE_FROM' => $arRubric['START_TIME'],
			'DATE_ACTIVE_TO' => $arRubric['END_TIME'],
			'SORT_BY' => 'ACTIVE_FROM',
			'SORT_ORDER' => 'DESC',
			'FILTER' => $filter,
		)
	);
	
	if ($itemsCount == 0) {
		return false;
	}
} catch (Exception $e) {
	ShowError($e->getMessage());
}

return array(
	'SUBJECT' => $arRubric['NAME'],
	'BODY_TYPE' => 'html',
	'CHARSET' => SITE_CHARSET,
	'DIRECT_SEND' => 'Y',
	'FROM_FIELD' => $arRubric['FROM_FIELD'],
);