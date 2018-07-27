<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arRubrics = array();
if (CModule::IncludeModule('subscribe')) {
	$rsRub = CRubric::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'));
	while ($arRub = $rsRub->Fetch()) {
		$arRubrics[$arRub['ID']] = $arRub['NAME'];
	}
}

$arComponentParameters = array(
	'PARAMETERS' => array(
		'RUBRICS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_SUBSCRIBEQUICK_PODPISYVATQ_NA_RUBRI'),
			'TYPE' => 'LIST',
			'VALUES' => $arRubrics,
			'MULTIPLE' => 'Y'
		),
		'SHOW_RUBRICS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_SUBSCRIBEQUICK_SHOW_RUBRICS'),
			'TYPE' => 'CHECKBOX',
		),
		'INC_JQUERY' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_SUBSCRIBEQUICK_PODKLUCITQ').' jQuery',
			'TYPE' => 'CHECKBOX',
		),
		'NOT_CONFIRM' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_SUBSCRIBEQUICK_NOT_CONFIRM'),
			'TYPE' => 'CHECKBOX',
		),
		'FORMAT' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_SUBSCRIBEQUICK_FORMAT'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'text'=>'text',
				'html'=>'html'
			),
		),
	),
);