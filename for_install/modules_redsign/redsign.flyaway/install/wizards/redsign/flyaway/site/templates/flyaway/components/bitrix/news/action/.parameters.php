<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.flyaway'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arNewsListTemplates = RSFLYAWAY_GetComponentTemplateList('bitrix:news.list');

$arValues = array(
    '12' => '1',
    '6' => '2',
    '4' => '3',
    '3' => '4',
    '2' => '6',
);

$arTemplateParameters = array(
	'RSFLYAWAY_LIST_TEMPLATES_LIST' => array(
		'NAME' => GetMessage('RS.FLYAWAY.LIST_TEMPLATES'),
		'TYPE' => 'LIST',
		'VALUES' => $arNewsListTemplates,
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	),
	'RSFLYAWAY_DETAIL_TEMPLATES' => array(
		'NAME' => GetMessage('RS.FLYAWAY.DETAIL_TEMPLATES'),
		'TYPE' => 'LIST',
		'VALUES' => RSFLYAWAY_GetComponentTemplateList('bitrix:news.detail'),
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	),
	'RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE' => array(
		'NAME' => GetMessage('RS.FLYAWAY.DETAIL_LIST_TEMPLATES_USE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	),
);

if( IsModuleInstalled('subscribe') ) {
	$arTemplateParameters['RSFLYAWAY_DETAIL_USE_SUBSCRIBE'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.DETAIL_USE_SUBSCRIBE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	if( $arCurrentValues['RSFLYAWAY_DETAIL_USE_SUBSCRIBE']=='Y' ) {
		$arTemplateParameters['RSFLYAWAY_DETAIL_SUBSCRIBE_PAGE'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.DETAIL_SUBSCRIBE_PAGE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_DETAIL_SUBSCRIBE_NOTE'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.DETAIL_SUBSCRIBE_NOTE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'DETAIL_SETTINGS',
		);
	}
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' ) {
	$arTemplateParameters['RSFLYAWAY_SHOW_BLOCK_NAME_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_BLOCK_NAME'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_BLOCK_NAME_IS_LINK_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.BLOCK_NAME_IS_LINK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_LIST_TEMPLATES_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.DETAIL_LIST_TEMPLATES'),
		'TYPE' => 'LIST',
		'VALUES' => $arNewsListTemplates,
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

$arTemplateParameters['RSFLYAWAY_SHOW_BLOCK_NAME_LIST'] = array(
	'NAME' => GetMessage('RS.FLYAWAY.SHOW_BLOCK_NAME'),
	'TYPE' => 'CHECKBOX',
	'VALUE' => 'Y',
	'DEFAULT' => 'Y',
	'PARENT' => 'LIST_SETTINGS',
);
$arTemplateParameters['RSFLYAWAY_BLOCK_NAME_IS_LINK_LIST'] = array(
	'NAME' => GetMessage('RS.FLYAWAY.BLOCK_NAME_IS_LINK'),
	'TYPE' => 'CHECKBOX',
	'VALUE' => 'Y',
	'DEFAULT' => 'N',
	'PARENT' => 'LIST_SETTINGS',
);

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']!='news' ) {
	$arTemplateParameters['RSFLYAWAY_USE_OWL_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.USE_OWL'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	if( $arCurrentValues['RSFLYAWAY_USE_OWL_DETAIL']=='Y' ) {
		$arTemplateParameters['RSFLYAWAY_OWL_CHANGE_SPEED_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_CHANGE_SPEED'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2000',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_CHANGE_DELAY_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_CHANGE_DELAY'),
			'TYPE' => 'STRING',
			'DEFAULT' => '8000',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_PHONE_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_PHONE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '1',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_TABLET_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_TABLET'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_PC_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_PC'),
			'TYPE' => 'STRING',
			'DEFAULT' => '3',
			'PARENT' => 'DETAIL_SETTINGS',
		);
	} else {
        $arTemplateParameters['RSFLYAWAY_COLS_IN_ROW_DETAIL'] = array(
            'NAME' => GetMessage('RS.FLYAWAY.COLS_IN_ROW'),
            'TYPE' => 'LIST',
            'VALUES' => $arValues,
            'DEFAULT' => '3',
            'PARENT' => 'DETAIL_SETTINGS',
        );
	}
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']!='news' ) {
	$arTemplateParameters['RSFLYAWAY_USE_OWL_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.USE_OWL'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
	if( $arCurrentValues['RSFLYAWAY_USE_OWL_LIST']=='Y' ) {
		$arTemplateParameters['RSFLYAWAY_OWL_CHANGE_SPEED_LIST'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_CHANGE_SPEED'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2000',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_CHANGE_DELAY_LIST'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_CHANGE_DELAY'),
			'TYPE' => 'STRING',
			'DEFAULT' => '8000',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_PHONE_LIST'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_PHONE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '1',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_TABLET_LIST'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_TABLET'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_OWL_PC_LIST'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.OWL_PC'),
			'TYPE' => 'STRING',
			'DEFAULT' => '3',
			'PARENT' => 'LIST_SETTINGS',
		);
	} else {
        $arTemplateParameters['RSFLYAWAY_COLS_IN_ROW_LIST'] = array(
            'NAME' => GetMessage('RS.FLYAWAY.COLS_IN_ROW'),
            'TYPE' => 'LIST',
            'VALUES' => $arValues,
            'DEFAULT' => '3',
            'PARENT' => 'LIST_SETTINGS',
        );
	}
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='about_us' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_PUBLISHER_NAME_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_PUBLISHER_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_PUBLISHER_BLANK_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_PUBLISHER_BLANK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_PUBLISHER_DESCR_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_PUBLISHER_DESCR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='about_us' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_PUBLISHER_NAME_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_PUBLISHER_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_PUBLISHER_BLANK_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_PUBLISHER_BLANK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_PUBLISHER_DESCR_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_PUBLISHER_DESCR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='action' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_MARKER_TEXT_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_MARKER_TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_MARKER_COLOR_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_MARKER_COLOR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_ACTION_DATE_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_ACTION_DATE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='action' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_MARKER_TEXT_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_MARKER_TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_MARKER_COLOR_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_MARKER_COLOR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_ACTION_DATE_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_ACTION_DATE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='docs' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_FILE_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_FILE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='docs' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_FILE_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_FILE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' ) {
	if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='features1' || $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='features2' ) {
		$arTemplateParameters['RSFLYAWAY_LINK_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.LINK'),
			'TYPE' => 'LIST',
			'VALUES' => $listProp['SNL'],
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSFLYAWAY_BLANK_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.BLANK'),
			'TYPE' => 'LIST',
			'VALUES' => $listProp['SNL'],
			'PARENT' => 'DETAIL_SETTINGS',
		);
	}
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='features1' || $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='features2' ) {
	$arTemplateParameters['RSFLYAWAY_LINK_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.LINK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_BLANK_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.BLANK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && ($arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='newslistcol' || $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='honors') ) {
	$arTemplateParameters['RSFLYAWAY_SHOW_DATE_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_DATE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='newslistcol' || $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='honors' ) {
	$arTemplateParameters['RSFLYAWAY_SHOW_DATE_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_DATE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='reviews' ) {
	$arTemplateParameters['RSFLYAWAY_AUTHOR_NAME_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.AUTHOR_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_AUTHOR_JOB_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.AUTHOR_JOB'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='reviews' ) {
	$arTemplateParameters['RSFLYAWAY_AUTHOR_NAME_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.AUTHOR_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSFLYAWAY_AUTHOR_JOB_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.AUTHOR_JOB'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='staff' ) {
	$arTemplateParameters['RSFLYAWAY_SHOW_BUTTON_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_BUTTON'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	);
	if( $arCurrentValues['RSFLYAWAY_SHOW_BUTTON']=='Y' ) {
		$arTemplateParameters['RSFLYAWAY_BUTTON_CAPTION_DETAIL'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.BUTTON_CAPTION'),
			'TYPE' => 'STRING',
		);
	}
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='staff' ) {
	$arTemplateParameters['RSFLYAWAY_SHOW_BUTTON_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.SHOW_BUTTON'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	);
	if( $arCurrentValues['RSFLYAWAY_SHOW_BUTTON']=='Y' ) {
		$arTemplateParameters['RSFLYAWAY_BUTTON_CAPTION_LIST'] = array(
			'NAME' => GetMessage('RS.FLYAWAY.BUTTON_CAPTION'),
			'TYPE' => 'STRING',
		);
	}
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_DETAIL']=='vacancies' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_VACANCY_TYPE_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_VACANCY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_SIGNATURE_DETAIL'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_SIGNATURE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'DEFAULT' => '',
	);
}

if( $arCurrentValues['RSFLYAWAY_LIST_TEMPLATES_LIST']=='vacancies' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_VACANCY_TYPE_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_VACANCY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	);
	$arTemplateParameters['RSFLYAWAY_PROP_SIGNATURE_LIST'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_SIGNATURE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'DEFAULT' => '',
	);
}


/**
/**************************************************
/************* detail template params *************
/**************************************************
**/


if( $arCurrentValues['RSFLYAWAY_DETAIL_TEMPLATES']=='gallery' ) {
	$arTemplateParameters['RSFLYAWAY_PROP_MORE_PHOTO'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}