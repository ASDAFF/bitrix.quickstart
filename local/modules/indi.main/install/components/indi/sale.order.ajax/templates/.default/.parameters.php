<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arTemplateParameters = array(
	'FEEDBACK_PHONE' => array(
		'NAME' => GetMessage('SOA_FEEDBACK_PHONE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '495 000-00-00',
		'PARENT' => 'ADDITIONAL_SETTINGS',
	),
	'PATH_TO_FEEDBACK_FORM' => array(
		'NAME' => GetMessage('SOA_PATH_TO_FEEDBACK_FORM'),
		'TYPE' => 'STRING',
		'DEFAULT' => '/',
		'PARENT' => 'ADDITIONAL_SETTINGS',
	),
);