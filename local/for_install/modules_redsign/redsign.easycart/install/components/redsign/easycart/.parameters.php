<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	'GROUPS' => array(
		'VIEWED_SETTINGS' => array(
			'NAME' => GetMessage('VIEWED_SETTINGS'),
		),
		'COMPARE_SETTINGS' => array(
			'NAME' => GetMessage('COMPARE_SETTINGS'),
		),
		'BASKET_SETTINGS' => array(
			'NAME' => GetMessage('BASKET_SETTINGS'),
		),
		'FAVORITE_SETTINGS' => array(
			'NAME' => GetMessage('FAVORITE_SETTINGS'),
		),
		'BASKET_SETTINGS' => array(
			'NAME' => GetMessage('BASKET_SETTINGS'),
		),
	),
	'PARAMETERS' => array(
		'USE_VIEWED' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('USE_VIEWED'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		),
		'USE_COMPARE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('USE_COMPARE'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		),
		'USE_BASKET' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('USE_BASKET'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		),
		
		/*'CACHE_TIME' => array(
			'PARENT' => 'CACHE_SETTINGS',
			'DEFAULT' => 3600
		),*/
	),
);

///////////////////////////////////////////////////////////////////////////////////////////// VIEWED
if( $arCurrentValues['USE_VIEWED']=='Y' )
{
	$arComponentParameters['PARAMETERS']['VIEWED_COUNT'] = array(
		'PARENT' => 'VIEWED_SETTINGS',
		'NAME' => GetMessage('VIEWED_COUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '10',
	);
}

///////////////////////////////////////////////////////////////////////////////////////////// COMPARE
if( $arCurrentValues['USE_COMPARE']=='Y' )
{
	$arComponentParameters['PARAMETERS']['COMPARE_NAME'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('COMPARE_NAME'),
		'TYPE' => 'STRING',
	);
}

///////////////////////////////////////////////////////////////////////////////////////////// BASKET
if( $arCurrentValues['USE_BASKET']=='Y' )
{
	
}

///////////////////////////////////////////////////////////////////////////////////////////// FAVORITE
if( IsModuleInstalled('redsign.favorite') )
{
	$arComponentParameters['PARAMETERS']['USE_FAVORITE'] = array(
		'PARENT' => 'BASE',
		'NAME' => GetMessage('USE_FAVORITE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);
}
if( $arCurrentValues['USE_FAVORITE']=='Y' )
{
	$arComponentParameters['PARAMETERS']['FAVORITE_COUNT'] = array(
		'PARENT' => 'FAVORITE_SETTINGS',
		'NAME' => GetMessage('FAVORITE_COUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => '10',
	);
}