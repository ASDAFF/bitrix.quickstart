<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arInterfaces = array();
if (CModule::IncludeModule('webdebug.reviews')) {
	$resInterfaces = CWD_Reviews2_Interface::GetList(array('SORT'=>'ASC','ID'=>'ASC'));
	while ($arInterface = $resInterfaces->GetNext(false,false)) {
		$arInterfaces[$arInterface['ID']] = $arInterface['NAME'];
	}
}

$arSortFields = array(
	'ID' => 'ID',
	'DATE_CREATED' => GetMessage('WD_REVIEWS2_DATE_CREATED'),
	'DATE_MODIFIED' => GetMessage('WD_REVIEWS2_DATE_MODIFIED'),
	'DATE_VOTING' => GetMessage('WD_REVIEWS2_DATE_VOTING'),
	'DATE_ANSWER' => GetMessage('WD_REVIEWS2_DATE_ANSWER'),
	'VOTES_Y' => GetMessage('WD_REVIEWS2_VOTES_Y'),
	'VOTES_N' => GetMessage('WD_REVIEWS2_VOTES_N'),
	'VOTE_RESULT' => GetMessage('WD_REVIEWS2_VOTE_RESULT'),
);
$arSortOrders = array(
	'ASC' => GetMessage('WD_REVIEWS2_SORT_ASC'),
	'DESC' => GetMessage('WD_REVIEWS2_SORT_DESC'),
);

$arComponentParameters = array(
	'GROUPS' => array(),
	'PARAMETERS' => array(
		'CACHE_TIME' => array('DEFAULT'=>3600),
		'INTERFACE_ID' => array(
			'NAME' => GetMessage('WD_REVIEWS2_INTERFACE'),
			'TYPE' => 'LIST',
			'VALUES' => $arInterfaces,
			"PARENT" => "BASE",
		),
		'TARGET_SUFFIX' => array(
			'NAME' => GetMessage('WD_REVIEWS2_TARGET_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'' => GetMessage('WD_REVIEWS2_TARGET_TYPE_DEFAULT'),
				'E_' => GetMessage('WD_REVIEWS2_TARGET_TYPE_ELEMENT'),
			),
			'PARENT' => 'BASE',
		),
		'TARGET' => array(
			'NAME' => GetMessage('WD_REVIEWS2_TARGET'),
			'TYPE' => 'TEXT',
			'PARENT' => 'BASE',
		),
		'COUNT' => array(
			'NAME' => GetMessage('WD_REVIEWS2_COUNT'),
			'TYPE' => 'TEXT',
			'PARENT' => 'PAGER_SETTINGS',
			'DEFAULT' => '10',
		),
		'SORT_BY_1' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SORT_BY_1'),
			'TYPE' => 'LIST',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'VALUES' => $arSortFields,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'ID',
		),
		'SORT_ORDER_1' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SORT_ORDER_1'),
			'TYPE' => 'LIST',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'VALUES' => $arSortOrders,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'DESC',
		),
		'SORT_BY_2' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SORT_BY_2'),
			'TYPE' => 'LIST',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'VALUES' => $arSortFields,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'ID',
		),
		'SORT_ORDER_2' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SORT_ORDER_2'),
			'TYPE' => 'LIST',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'VALUES' => $arSortOrders,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'DESC',
		),
		'FILTER_NAME' => array(
			'NAME' => GetMessage('WD_REVIEWS2_FILTER_NAME'),
			'TYPE' => 'TEXT',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		'DATE_FORMAT' => array(),
		'SHOW_AVATARS' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SHOW_AVATARS'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'SHOW_ANSWERS' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SHOW_ANSWERS'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'SHOW_ANSWER_DATE' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SHOW_ANSWER_DATE'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'USER_ANSWER_NAME' => array(
			'NAME' => GetMessage('WD_REVIEWS2_USER_ANSWER_NAME'),
			'TYPE' => 'LIST',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'VALUES' => array(
				'#NAME#' => GetMessage('WD_REVIEWS2_USER_ANSWER_NAME_1'),
				'#NAME# #LAST_NAME#' => GetMessage('WD_REVIEWS2_USER_ANSWER_NAME_2'),
				'#LAST_NAME# #NAME#' => GetMessage('WD_REVIEWS2_USER_ANSWER_NAME_3'),
				'#NAME# #SECOND_NAME#' => GetMessage('WD_REVIEWS2_USER_ANSWER_NAME_4'),
				'#LOGIN#' => GetMessage('WD_REVIEWS2_USER_ANSWER_NAME_0'),
			),
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => '#NAME# #LAST_NAME#',
		),
		'SHOW_ANSWER_AVATAR' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SHOW_ANSWER_AVATAR'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'ALLOW_VOTE' => array(
			'NAME' => GetMessage('WD_REVIEWS2_ALLOW_VOTE'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'MANUAL_CSS_INCLUDE' => array(
			'NAME' => GetMessage('WD_REVIEWS2_MANUAL_CSS_INCLUDE'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'N',
		),
		'AUTO_LOADING' => array(
			'NAME' => GetMessage('WD_REVIEWS2_AUTO_LOADING'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'N',
		),
		'SHOW_ALL_IF_ADMIN' => array(
			'NAME' => GetMessage('WD_REVIEWS2_SHOW_ALL_IF_ADMIN'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'MINIMIZE_FORM' => array(
			'NAME' => GetMessage('WD_REVIEWS2_MINIMIZE_FORM'),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ADDITIONAL_SETTINGS',
			'DEFAULT' => 'Y',
		),
		'JS' => array(
			'NAME' => GetMessage('WD_REVIEWS2_JS'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'none' => GetMessage('WD_REVIEWS2_JS_NONE'),
				'all' => GetMessage('WD_REVIEWS2_JS_ALL'),
				'raty' => GetMessage('WD_REVIEWS2_JS_RATY'),
			),
			'DEFAULT' => 'raty',
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	),
);

if (CModule::IncludeModule('iblock')) {
	$arComponentParameters['PARAMETERS']['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(GetMessage('WD_REVIEWS2_DATE_FORMAT'), 'ADDITIONAL_SETTINGS');
	$arComponentParameters['PARAMETERS']['DATE_FORMAT']['VALUES']['j M Y, G:i'] = FormatDate('j M Y, G:i', mktime(7,30,0,02,22,2007));
	$arComponentParameters['PARAMETERS']['DATE_FORMAT']['VALUES']['j F Y, G:i'] = FormatDate('j F Y, G:i', mktime(7,30,0,02,22,2007));
	CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage('WD_REVIEWS2_PAGER_TITLE_VALUE'), true, true);
}

?>