<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule('intranet');

$arComponentParameters = array(
	'GROUPS' => array(
		'FILTER' => array(
			'NAME' => GetMessage('INTR_ISL_GROUP_FILTER'),
		),
	),
	
	'PARAMETERS' => array(
		'FILTER_NAME' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'users',
			'PARENT' => 'FILTER',
			'NAME' => GetMessage('INTR_ISL_PARAM_FILTER_NAME'),
		),
		
		'FILTER_1C_USERS' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'NAME' => GetMessage('INTR_ISL_PARAM_FILTER_1C_USERS'),
			'PARENT' => 'BASE'
		),
		'FILTER_SECTION_CURONLY' => array(
			'TYPE' => 'LIST',
			'VALUES' => array('Y' => GetMessage('INTR_ISL_PARAM_FILTER_SECTION_CURONLY_VALUE_Y'), 'N' => GetMessage('INTR_ISL_PARAM_FILTER_SECTION_CURONLY_VALYE_N')),
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			'NAME' => GetMessage('INTR_ISL_PARAM_FILTER_SECTION_CURONLY'),
			'PARENT' => 'BASE'
		),
		
		'NAME_TEMPLATE' => array(
			'TYPE' => 'LIST',
			'NAME' => GetMessage('INTR_ISL_PARAM_NAME_TEMPLATE'),
			'VALUES' => CIntranetUtils::GetDefaultNameTemplates(),
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => "#NOBR##LAST_NAME# #NAME##/NOBR#",
			'PARENT' => 'BASE',
		),
		
		'SHOW_ERROR_ON_NULL' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'NAME' => GetMessage('INTR_ISL_PARAM_SHOW_ERROR_ON_NULL'),
			'PARENT' => 'BASE'
		),
		
		'USERS_PER_PAGE' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '10',
			'NAME' => GetMessage('INTR_ISL_PARAM_USERS_PER_PAGE'),
			'PARENT' => 'BASE'
		),
		'NAV_TITLE' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => GetMessage('INTR_ISL_PARAM_NAV_TITLE_DEFAULT'),
			'NAME' => GetMessage('INTR_ISL_PARAM_NAV_TITLE'),
			'PARENT' => 'BASE'
		),
		'SHOW_UNFILTERED_LIST' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			'NAME' => GetMessage('INTR_ISL_PARAM_SHOW_UNFILTERED_LIST'),
			'PARENT' => 'BASE'
		),
	),
);

?>