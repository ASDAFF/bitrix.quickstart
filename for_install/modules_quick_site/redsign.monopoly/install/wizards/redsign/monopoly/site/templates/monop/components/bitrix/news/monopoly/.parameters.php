<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arNewsListTemplates = RSMONOPOLY_GetComponentTemplateList('bitrix:news.list');

$arValues = array(
    '12' => '1',
    '6' => '2',
    '4' => '3',
    '3' => '4',
    '2' => '6',
);

$arTemplateParameters = array(
	'RSMONOPOLY_LIST_TEMPLATES_LIST' => array(
		'NAME' => GetMessage('RS.MONOPOLY.LIST_TEMPLATES'),
		'TYPE' => 'LIST',
		'VALUES' => $arNewsListTemplates,
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	),
	'RSMONOPOLY_DETAIL_TEMPLATES' => array(
		'NAME' => GetMessage('RS.MONOPOLY.DETAIL_TEMPLATES'),
		'TYPE' => 'LIST',
		'VALUES' => RSMONOPOLY_GetComponentTemplateList('bitrix:news.detail'),
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	),
	'RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.DETAIL_LIST_TEMPLATES_USE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	),
);

if( IsModuleInstalled('subscribe') ) {
	$arTemplateParameters['RSMONOPOLY_DETAIL_USE_SUBSCRIBE'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.DETAIL_USE_SUBSCRIBE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	if( $arCurrentValues['RSMONOPOLY_DETAIL_USE_SUBSCRIBE']=='Y' ) {
		$arTemplateParameters['RSMONOPOLY_DETAIL_SUBSCRIBE_PAGE'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.DETAIL_SUBSCRIBE_PAGE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_DETAIL_SUBSCRIBE_NOTE'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.DETAIL_SUBSCRIBE_NOTE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'DETAIL_SETTINGS',
		);
	}
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' ) {
	$arTemplateParameters['RSMONOPOLY_SHOW_BLOCK_NAME_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_BLOCK_NAME'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_BLOCK_NAME_IS_LINK_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.BLOCK_NAME_IS_LINK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_LIST_TEMPLATES_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.DETAIL_LIST_TEMPLATES'),
		'TYPE' => 'LIST',
		'VALUES' => $arNewsListTemplates,
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

$arTemplateParameters['RSMONOPOLY_SHOW_BLOCK_NAME_LIST'] = array(
	'NAME' => GetMessage('RS.MONOPOLY.SHOW_BLOCK_NAME'),
	'TYPE' => 'CHECKBOX',
	'VALUE' => 'Y',
	'DEFAULT' => 'Y',
	'PARENT' => 'LIST_SETTINGS',
);
$arTemplateParameters['RSMONOPOLY_BLOCK_NAME_IS_LINK_LIST'] = array(
	'NAME' => GetMessage('RS.MONOPOLY.BLOCK_NAME_IS_LINK'),
	'TYPE' => 'CHECKBOX',
	'VALUE' => 'Y',
	'DEFAULT' => 'N',
	'PARENT' => 'LIST_SETTINGS',
);

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']!='news' ) {
	$arTemplateParameters['RSMONOPOLY_USE_OWL_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.USE_OWL'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	if( $arCurrentValues['RSMONOPOLY_USE_OWL_DETAIL']=='Y' ) {
		$arTemplateParameters['RSMONOPOLY_OWL_CHANGE_SPEED_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_CHANGE_SPEED'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2000',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_CHANGE_DELAY_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_CHANGE_DELAY'),
			'TYPE' => 'STRING',
			'DEFAULT' => '8000',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_PHONE_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_PHONE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '1',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_TABLET_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_TABLET'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2',
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_PC_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_PC'),
			'TYPE' => 'STRING',
			'DEFAULT' => '3',
			'PARENT' => 'DETAIL_SETTINGS',
		);
	} else {
        $arTemplateParameters['RSMONOPOLY_COLS_IN_ROW_DETAIL'] = array(
            'NAME' => GetMessage('RS.MONOPOLY.COLS_IN_ROW'),
            'TYPE' => 'LIST',
            'VALUES' => $arValues,
            'DEFAULT' => '3',
            'PARENT' => 'DETAIL_SETTINGS',
        );
	}
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']!='news' ) {
	$arTemplateParameters['RSMONOPOLY_USE_OWL_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.USE_OWL'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => '',
		'REFRESH' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
	if( $arCurrentValues['RSMONOPOLY_USE_OWL_LIST']=='Y' ) {
		$arTemplateParameters['RSMONOPOLY_OWL_CHANGE_SPEED_LIST'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_CHANGE_SPEED'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2000',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_CHANGE_DELAY_LIST'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_CHANGE_DELAY'),
			'TYPE' => 'STRING',
			'DEFAULT' => '8000',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_PHONE_LIST'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_PHONE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '1',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_TABLET_LIST'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_TABLET'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_OWL_PC_LIST'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.OWL_PC'),
			'TYPE' => 'STRING',
			'DEFAULT' => '3',
			'PARENT' => 'LIST_SETTINGS',
		);
	} else {
        $arTemplateParameters['RSMONOPOLY_COLS_IN_ROW_LIST'] = array(
            'NAME' => GetMessage('RS.MONOPOLY.COLS_IN_ROW'),
            'TYPE' => 'LIST',
            'VALUES' => $arValues,
            'DEFAULT' => '3',
            'PARENT' => 'LIST_SETTINGS',
        );
	}
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='about_us' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_PUBLISHER_NAME_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PUBLISHER_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_PUBLISHER_BLANK_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PUBLISHER_BLANK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_PUBLISHER_DESCR_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PUBLISHER_DESCR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='about_us' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_PUBLISHER_NAME_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PUBLISHER_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_PUBLISHER_BLANK_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PUBLISHER_BLANK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_PUBLISHER_DESCR_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PUBLISHER_DESCR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='action' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_MARKER_TEXT_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MARKER_TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_MARKER_COLOR_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MARKER_COLOR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_ACTION_DATE_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_ACTION_DATE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='action' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_MARKER_TEXT_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MARKER_TEXT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_MARKER_COLOR_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MARKER_COLOR'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_ACTION_DATE_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_ACTION_DATE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='docs' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_FILE_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_FILE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='docs' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_FILE_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_FILE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' ) {
	if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='features1' || $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='features2' ) {
		$arTemplateParameters['RSMONOPOLY_LINK_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.LINK'),
			'TYPE' => 'LIST',
			'VALUES' => $listProp['SNL'],
			'PARENT' => 'DETAIL_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_BLANK_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.BLANK'),
			'TYPE' => 'LIST',
			'VALUES' => $listProp['SNL'],
			'PARENT' => 'DETAIL_SETTINGS',
		);
	}
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='features1' || $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='features2' ) {
	$arTemplateParameters['RSMONOPOLY_LINK_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.LINK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_BLANK_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.BLANK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && ($arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='newslistcol' || $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='honors') ) {
	$arTemplateParameters['RSMONOPOLY_SHOW_DATE_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_DATE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='newslistcol' || $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='honors' ) {
	$arTemplateParameters['RSMONOPOLY_SHOW_DATE_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_DATE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='reviews' ) {
	$arTemplateParameters['RSMONOPOLY_AUTHOR_NAME_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.AUTHOR_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_AUTHOR_JOB_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.AUTHOR_JOB'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='reviews' ) {
	$arTemplateParameters['RSMONOPOLY_AUTHOR_NAME_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.AUTHOR_NAME'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
	$arTemplateParameters['RSMONOPOLY_AUTHOR_JOB_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.AUTHOR_JOB'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'PARENT' => 'LIST_SETTINGS',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='staff' ) {
	$arTemplateParameters['RSMONOPOLY_SHOW_BUTTON_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_BUTTON'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	);
	if( $arCurrentValues['RSMONOPOLY_SHOW_BUTTON']=='Y' ) {
		$arTemplateParameters['RSMONOPOLY_BUTTON_CAPTION_DETAIL'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.BUTTON_CAPTION'),
			'TYPE' => 'STRING',
		);
	}
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='staff' ) {
	$arTemplateParameters['RSMONOPOLY_SHOW_BUTTON_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_BUTTON'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	);
	if( $arCurrentValues['RSMONOPOLY_SHOW_BUTTON']=='Y' ) {
		$arTemplateParameters['RSMONOPOLY_BUTTON_CAPTION_LIST'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.BUTTON_CAPTION'),
			'TYPE' => 'STRING',
		);
	}
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' && $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_DETAIL']=='vacancies' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_VACANCY_TYPE_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_VACANCY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_SIGNATURE_DETAIL'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_SIGNATURE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
		'DEFAULT' => '',
	);
}

if( $arCurrentValues['RSMONOPOLY_LIST_TEMPLATES_LIST']=='vacancies' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_VACANCY_TYPE_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_VACANCY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['L'],
		'DEFAULT' => '',
	);
	$arTemplateParameters['RSMONOPOLY_PROP_SIGNATURE_LIST'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_SIGNATURE'),
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


if( $arCurrentValues['RSMONOPOLY_DETAIL_TEMPLATES']=='gallery' ) {
	$arTemplateParameters['RSMONOPOLY_PROP_MORE_PHOTO'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
		'PARENT' => 'DETAIL_SETTINGS',
	);
}