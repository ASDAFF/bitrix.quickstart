<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

if(!CModule::IncludeModule('api.reviews'))
{
	ShowError(GetMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

$arComponentParameters = array(
	'GROUPS'     => array(
		'LANG' => array(
			'NAME' => GetMessage('API_REVIEWS_STAT_GROUP_LANG'),
			'SORT' => 300,
		),
	),
	'PARAMETERS' => array(
		'INCLUDE_CSS' => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('INCLUDE_CSS'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'IBLOCK_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('IBLOCK_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),
		'SECTION_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('SECTION_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),
		'ELEMENT_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('ELEMENT_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),
		'ORDER_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('ORDER_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),
		'URL'                        => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),

		'MESS_TOTAL_RATING'          => array(
			'PARENT'  => 'LANG',
			'NAME'    => GetMessage('MESS_TOTAL_RATING'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_TOTAL_RATING_DEFAULT'),
		),
		'MESS_CUSTOMER_RATING'          => array(
			'PARENT'  => 'LANG',
			'NAME'    => GetMessage('MESS_CUSTOMER_RATING'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_CUSTOMER_RATING_DEFAULT'),
		),
		'MIN_AVERAGE_RATING'          => array(
			'PARENT'  => 'LANG',
			'NAME'    => GetMessage('MIN_AVERAGE_RATING'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 5,
		),

		'CACHE_TIME' => Array('DEFAULT' => 86400),
	),
);