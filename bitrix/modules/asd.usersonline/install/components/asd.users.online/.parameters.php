<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();


$arComponentParameters = array(
	'GROUPS' => array(
		'DATA_SORT' => array(
			'NAME' => GetMessage('ASD_CMP_PARAM_SORT_BLOCK'),
		),
	),
	'PARAMETERS' => array(
		'USER_PATH' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_CMP_PARAM_USER_PATH'),
			'TYPE' => 'STRING',
			'DEFAULT' => '/users/#ID#/'
		),
		'POPUP' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ASD_CMP_PARAM_POPUP'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N'
		),
		'CACHE_TIME' => array('DEFAULT' => 300),
	),
);