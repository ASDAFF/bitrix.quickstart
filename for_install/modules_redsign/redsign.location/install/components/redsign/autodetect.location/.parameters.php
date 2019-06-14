<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	'PARAMETERS' => array(
		'RSLOC_INCLUDE_JQUERY' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('RSLOC_INCLUDE_JQUERY'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
		),
		'RSLOC_LOAD_LOCATIONS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('RSLOC_LOAD_LOCATIONS'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'REFRESH' => 'Y',
		),
	),
);

if( $arCurrentValues['RSLOC_LOAD_LOCATIONS']=="Y" )
{
	$arComponentParameters['PARAMETERS']['RSLOC_LOAD_LOCATIONS_CNT'] = array(
		'PARENT' => 'BASE',
		'NAME' => GetMessage('RSLOC_LOAD_LOCATIONS_CNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '20',
	);
}