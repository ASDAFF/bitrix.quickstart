<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RSMONOPOLY_PROP_PRICE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PRICE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['N'],
	),
	'RSMONOPOLY_PROP_DISCOUNT' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_DISCOUNT'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['N'],
	),
	'RSMONOPOLY_PROP_CURRENCY' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_CURRENCY'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_PROP_PRICE_DECIMALS' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_PRICE_DECIMALS'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'0' => '0',
			'1' => '1',
			'2' => '2',
		),
		'DEFAULT' => '0',
	),
	'RSMONOPOLY_PROP_QUANTITY' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_QUANTITY'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['N'],
	),
	'RSMONOPOLY_PROP_MORE_PHOTO' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
	'RSMONOPOLY_PROP_ARTICLE' => array(
		'NAME' => GetMessage('RS.MONOPOLY.PROP_ARTICLE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
    'SORTER_USE_AJAX' => array(
		'NAME' => GetMessage('RS.MONOPOLY.SORTER_USE_AJAX'),
		'TYPE' => 'LIST',
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
    
    // Filter
    'FILTER_USE_AJAX' => array(
		'PARENT' => 'FILTER_SETTINGS',
		'NAME' => GetMessage('RS.MONOPOLY.FILTER_USE_AJAX'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
);

if( \Bitrix\Main\Loader::includeModule("redsign.devcom") ) {
	$arTemplateParameters['RSMONOPOLY_SHOW_SORTER'] = array(
		'NAME' => GetMessage('RS.MONOPOLY.SHOW_SORTER'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y',
		'PARENT' => 'LIST_SETTINGS',
	);
	if( $arCurrentValues['RSMONOPOLY_SHOW_SORTER']=='Y' ) {
		$arTemplateParameters['RSMONOPOLY_SORTER_SHOW_TEMPLATE'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.SORTER_SHOW_TEMPLATE'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_SORTER_SHOW_SORTING'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.SORTER_SHOW_SORTING'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
			'PARENT' => 'LIST_SETTINGS',
		);
		$arTemplateParameters['RSMONOPOLY_SORTER_SHOW_PAGE_COUNT'] = array(
			'NAME' => GetMessage('RS.MONOPOLY.SORTER_SHOW_PAGE_COUNT'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
			'PARENT' => 'LIST_SETTINGS',
		);
		if( $arCurrentValues['RSMONOPOLY_SORTER_SHOW_TEMPLATE']=='Y' ) {
			$arTemplateParameters['RSMONOPOLY_SORTER_TEMPLATE_DEFAULT'] = array(
				'NAME' => GetMessage('RS.MONOPOLY.SORTER_TEMPLATE_DEFAULT'),
				'TYPE' => 'STRING',
				'VALUE' => '',
				'DEFAULT' => 'showcase',
				'PARENT' => 'LIST_SETTINGS',
			);
		}
	}
}