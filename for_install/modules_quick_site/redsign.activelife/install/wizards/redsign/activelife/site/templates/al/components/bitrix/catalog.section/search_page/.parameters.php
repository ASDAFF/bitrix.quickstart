<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.activelife'))
	return;

$arrPopupDetailVariable = array(
	'ON_IMAGE' => GetMessage('POPUP_DETAIL_VARIABLE_IMAGE'),
	'ON_LUPA' => GetMessage('POPUP_DETAIL_VARIABLE_LUPA'),
	'ON_NONE' => GetMessage('POPUP_DETAIL_VARIABLE_NONE'),
);

$arTemplateParameters = array(
	// ajaxpages id
	'AJAXPAGESID' => array(
		'NAME' => GetMessage('MSG_AJAXPAGESID'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'IS_AJAXPAGES' => array(
		'NAME' => GetMessage('MSG_IS_AJAXPAGES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	// man & women
	'ICON_MEN_PROP' => array(
		'NAME' => GetMessage('MSG_ICON_MEN_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'FOR_MEN',
	),
	'ICON_WOMEN_PROP' => array(
		'NAME' => GetMessage('MSG_ICON_WOMEN_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'FOR_WOMEN',
	),
	// new & discount
	'ICON_NOVELTY_PROP' => array(
		'NAME' => GetMessage('MSG_ICON_NOVELTY_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'NEW_ICON',
	),
	'ICON_DISCOUNT_PROP' => array(
		'NAME' => GetMessage('MSG_ICON_DISCOUNT_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'DISCOUNT_ICON',
	),
	'ICON_DEALS_PROP' => array(
		'NAME' => GetMessage('MSG_ICON_DEALS_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'DISCOUNT_ICON',
	),
	// use compare 
	'USE_COMPARE' => array(
		'NAME' => GetMessage('MSG_USE_COMPARE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	// use delete link
	'USE_DELETE' => array(
		'NAME' => GetMessage('MSG_USE_DELETE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => '',
	),
	'DELETE_FROM' => array(
		'NAME' => GetMessage('MSG_DELETE_FROM'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	// use likes
	'USE_LIKES' => array(
		'NAME' => GetMessage('MSG_USE_LIKES'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
	// use share buttons
	'USE_SHARE' => array(
		'NAME' => GetMessage('MSG_USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	),
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
	// popup detail opening
	'POPUP_DETAIL_VARIABLE' => array(
		'NAME' => GetMessage('MSG_POPUP_DETAIL_VARIABLE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'VALUES' => $arrPopupDetailVariable,
		'REFRESH' => 'N',
	),
	// show empty products error
	'ERROR_EMPTY_ITEMS' => array(
		'NAME' => GetMessage('MSG_ERROR_EMPTY_ITEMS'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
);