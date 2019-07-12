<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.mediamart'))
	return;

$arTemplateParameters = array(
	// more photo
	'PROPCODE_MORE_PHOTO' => array(
		'NAME' => GetMessage('MSG_PROPCODE_MORE_PHOTO'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'MORE_PHOTO',
	),
	'PROPCODE_SKU_MORE_PHOTO' => array(
		'NAME' => GetMessage('MSG_PROPCODE_SKU_MORE_PHOTO'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'SKU_MORE_PHOTO',
	),
);