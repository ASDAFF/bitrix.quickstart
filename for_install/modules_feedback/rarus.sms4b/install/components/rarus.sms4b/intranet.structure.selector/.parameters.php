<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'GROUPS' => array(
		'FILTER' => array(
			'NAME' => GetMessage(''),
		),
	),
	
	'PARAMETERS' => array(
		'LIST_URL' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '',
			'PARENT' => 'BASE',
			'NAME' => GetMessage('INTR_ISS_PARAM_LIST_URL'),
		),

		'FILTER_NAME' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'users',
			'PARENT' => 'FILTER',
			'NAME' => GetMessage('INTR_ISS_PARAM_FILTER_NAME'),
		),
		
		'FILTER_DEPARTMENT_SINGLE' => array(
			'TYPE' => 'LIST',
			'VALUES' => array('Y' => GetMessage('INTR_ISS_PARAM_FILTER_DEPARTMENT_SINGLE_VALUE_Y'), 'N' => GetMessage('INTR_ISS_PARAM_FILTER_DEPARTMENT_SINGLE_VALUE_N')),
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'PARENT' => 'FILTER',
			'NAME' => GetMessage('INTR_ISS_PARAM_FILTER_DEPARTMENT_SINGLE'),
		),

		'FILTER_SESSION' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'PARENT' => 'FILTER',
			'NAME' => GetMessage('INTR_ISS_PARAM_FILTER_SESSION'),
		),
	),
);

?>