<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;
	$arTemplateParameters['TEMPLATE_SIZE'] = array(
		'NAME' => GetMessage('TEMPLATE_SIZE'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'big',
		'VALUES' => array('big' => GetMessage('TEMPLATE_SIZE_BIG'), 'medium' => GetMessage('TEMPLATE_SIZE_MEDIUM'), 'small' => GetMessage('TEMPLATE_SIZE_SMALL'))
	);