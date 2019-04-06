<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		'VALUE' => array(
			'NAME' => GetMessage('WD_REVIEWS2_VALUE'),
			'TYPE' => 'TEXT',
		),
		'INTERFACE_ID' => array(
			'NAME' => GetMessage('WD_REVIEWS2_INTERFACE'),
			'TYPE' => 'TEXT',
		),
		'INPUT_NAME' => array(
			'NAME' => GetMessage('WD_REVIEWS2_INPUT_NAME'),
			'TYPE' => 'TEXT',
		),
		'READ_ONLY' => array(
			'NAME' => GetMessage('WD_REVIEWS2_READ_ONLY'),
			'TYPE' => 'CHECKBOX',
		),
		'UNIQ_ID' => array(
			'NAME' => GetMessage('WD_REVIEWS2_UNIQ_ID'),
			'TYPE' => 'TEXT',
		),
		'SCHEMA_ORG' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SCHEMA_ORG'),
			'TYPE' => 'CHECKBOX',
		),
		'COUNT' => array(
			'NAME' => GetMessage('WD_REVIEWS2_COUNT'),
			'TYPE' => 'TEXT',
		),
	),
);
?>