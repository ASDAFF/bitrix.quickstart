<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.activelife'))
	return;

$arTemplateParameters = array(
	// more photo
	'ADDITIONAL_PICT_PROP' => array(
		'NAME' => GetMessage('MSG_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'MORE_PHOTO',
	),
	'OFFER_ADDITIONAL_PICT_PROP' => array(
		'NAME' => GetMessage('MSG_OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'SKU_MORE_PHOTO',
	),
);