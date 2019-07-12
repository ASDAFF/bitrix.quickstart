<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	'GROUPS' => array(
	),
	'PARAMETERS' => array(
		'NAME' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_NAME'),
			'TYPE' => 'STRING',
			'DEFAULT' => "",
			'PARENT' => 'BASE',
		),
		'BODY' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_BODY'),
			'TYPE' => 'STRING',
			'DEFAULT' => "",
			'PARENT' => 'BASE',
		),
		'ADDRESS' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_ADDRESS'),
			'TYPE' => 'STRING',
			'DEFAULT' => GetMessage('CITRUS_MYMS_PARAM_ADDRESS_DEFAULT'),
			'PARENT' => 'BASE',
		),
		'OPEN_BALOON' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_OPEN_BALOON'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => "Y",
			'PARENT' => 'BASE',
			"VALUE" => "Y",
		),
		'MAP_WIDTH' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_MAP_WIDTH'),
			'TYPE' => 'STRING',
			'DEFAULT' => '600',
			'PARENT' => 'BASE',
		),
		'MAP_HEIGHT' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_MAP_HEIGHT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '500',
			'PARENT' => 'BASE',
		),
		'MAP_ID' => array(
			'NAME' => GetMessage('CITRUS_MYMS_PARAM_MAP_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
	),
);
?>