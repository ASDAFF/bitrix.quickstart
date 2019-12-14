<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arList = array(
	'rsec_thistab_viewed' => GetMessage('TAB_IDENT_VIEWED'),
	'rsec_thistab_compare' => GetMessage('TAB_IDENT_COMPARE'),
	'rsec_thistab_favorite' => GetMessage('TAB_IDENT_FAVORITE'),
);

$arTemplateParameters = array(
	'RS_SECONDARY_ACTION_VARIABLE' => array(
		'NAME' => GetMessage('RS_SECONDARY_ACTION_VARIABLE'),
		'TYPE' => 'STRING',
	),
	'RS_TAB_IDENT' => array(
		'NAME' => GetMessage('RS_TAB_IDENT'),
		'TYPE' => 'LIST',
		'VALUES' => $arList,
	),
	'RS_TAB_NAME' => array(
		'NAME' => GetMessage('RS_TAB_NAME'),
		'TYPE' => 'STRING',
	),
);