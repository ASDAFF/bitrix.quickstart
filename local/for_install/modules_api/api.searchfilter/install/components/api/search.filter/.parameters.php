<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

if(!CModule::IncludeModule('iblock'))
	return;

global $APPLICATION;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array('sort' => 'asc'), Array(
	 'TYPE'   => $arCurrentValues['IBLOCK_TYPE'],
	 'ACTIVE' => 'Y',
));
while($arr = $rsIBlock->Fetch())
	$arIBlock[ $arr['ID'] ] = '[' . $arr['ID'] . '] ' . $arr['NAME'];


$arProperty   = array();
$arProperty_N = $arProperty_L = array();
$rsProp       = CIBlockProperty::GetList(Array(
	 'sort' => 'asc',
	 'name' => 'asc',
), Array(
	 'ACTIVE'    => 'Y',
	 'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
));
while($arr = $rsProp->Fetch()) {
	if($arr['PROPERTY_TYPE'] != 'F')
		$arProperty[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];

	if($arr['PROPERTY_TYPE'] == 'N')
		$arProperty_N[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];

	if($arr['PROPERTY_TYPE'] == 'L' || $arr['PROPERTY_TYPE'] == 'E' || $arr['PROPERTY_TYPE'] == 'G')
		$arProperty_L[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
}

$arOffers          = CIBlockPriceTools::GetOffersIBlock($arCurrentValues['IBLOCK_ID']);
$OFFERS_IBLOCK_ID  = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID'] : 0;
$arProperty_Offers = array();
if($OFFERS_IBLOCK_ID) {
	$rsProp = CIBlockProperty::GetList(Array(
		 'sort' => 'asc',
		 'name' => 'asc',
	), Array(
		 'ACTIVE'    => 'Y',
		 'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
	));
	while($arr = $rsProp->Fetch()) {
		if($arr['PROPERTY_TYPE'] != 'F')
			$arProperty_Offers[ $arr['CODE'] ] = '[' . $arr['CODE'] . '] ' . $arr['NAME'];
	}
}

$arPrice = array();
if(CModule::IncludeModule('catalog')) {
	$rsPrice = CCatalogGroup::GetList($v1 = 'sort', $v2 = 'asc');
	while($arr = $rsPrice->Fetch())
		$arPrice[ $arr['NAME'] ] = '[' . $arr['NAME'] . '] ' . $arr['NAME_LANG'];
}
else {
	$arPrice = $arProperty_N;
}

$arComponentParameters = array(
	 'GROUPS'     => array(
			'PRICES' => array(
				 'NAME' => GetMessage('IBLOCK_PRICES'),
			),
			'JQUERY' => array(
				 'NAME' => GetMessage('JQUERY_GROUP'),
			),
	 ),
	 'PARAMETERS' => array(
			'IBLOCK_TYPE'                                   => array(
				 'PARENT'            => 'DATA_SOURCE',
				 'NAME'              => GetMessage('IBLOCK_TYPE'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => $arIBlockType,
				 'REFRESH'           => 'Y',
			),
			'IBLOCK_ID'                                     => array(
				 'PARENT'            => 'DATA_SOURCE',
				 'NAME'              => GetMessage('IBLOCK_IBLOCK'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => $arIBlock,
				 'REFRESH'           => 'Y',
			),
			'FILTER_NAME'                                   => array(
				 'PARENT'  => 'DATA_SOURCE',
				 'NAME'    => GetMessage('IBLOCK_FILTER_NAME_OUT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 'arrFilter',
			),
			'REDIRECT_FOLDER'                               => array(
				 'PARENT'  => 'DATA_SOURCE',
				 'NAME'    => GetMessage('REDIRECT_FOLDER'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'FIELD_CODE'                                    => CIBlockParameters::GetFieldCode(GetMessage('IBLOCK_FIELD'), 'DATA_SOURCE', array('SECTION_ID' => true)),
			'PROPERTY_CODE'                                 => array(
				 'PARENT'            => 'DATA_SOURCE',
				 'NAME'              => GetMessage('IBLOCK_PROPERTY'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => $arProperty,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'OFFERS_FIELD_CODE'                             => CIBlockParameters::GetFieldCode(GetMessage('CP_BCF_OFFERS_FIELD_CODE'), 'DATA_SOURCE'),
			'OFFERS_PROPERTY_CODE'                          => array(
				 'PARENT'            => 'DATA_SOURCE',
				 'NAME'              => GetMessage('CP_BCF_OFFERS_PROPERTY_CODE'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => $arProperty_Offers,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'CHECK_ACTIVE_SECTIONS'                         => array(
				 'PARENT'  => 'DATA_SOURCE',
				 'NAME'    => GetMessage('CHECK_ACTIVE_SECTIONS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'SECTION_ID'                                    => array(
				 'PARENT'  => 'DATA_SOURCE',
				 'NAME'    => GetMessage('IBLOCK_SECTION_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '={$_REQUEST["SECTION_ID"]}',
			),
			'SECTION_CODE'                                  => array(
				 'PARENT'  => 'DATA_SOURCE',
				 'NAME'    => GetMessage('IBLOCK_SECTION_CODE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '={$_REQUEST["SECTION_CODE"]}',
			),
			'PRICE_CODE'                                    => array(
				 'PARENT'   => 'PRICES',
				 'NAME'     => GetMessage('IBLOCK_PRICE_CODE'),
				 'TYPE'     => 'LIST',
				 'MULTIPLE' => 'Y',
				 'VALUES'   => $arPrice,
			),
			'CACHE_TIME'                                    => Array('DEFAULT' => 36000000),
			'CACHE_GROUPS'                                  => array(
				 'PARENT'  => 'CACHE_SETTINGS',
				 'NAME'    => GetMessage('CP_BCF_CACHE_GROUPS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'LIST_HEIGHT'                                   => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('IBLOCK_LIST_HEIGHT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '5',
			),
			'TEXT_WIDTH'                                    => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('IBLOCK_TEXT_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '209',
			),
			'NUMBER_WIDTH'                                  => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('IBLOCK_NUMBER_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '85',
			),
			'SELECT_WIDTH'                                  => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('IBLOCK_SELECT_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '220',
			),
			'ELEMENT_IN_ROW'                                => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('ELEMENT_IN_ROW'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '3',
			),
			'NAME_WIDTH'                                    => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('NAME_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '130',
			),
			'FILTER_TITLE'                                  => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('FILTER_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage("API_SEARCHFILTER_FILQTR"),
			),
			'BUTTON_ALIGN'                                  => array(
				 'PARENT'            => 'VISUAL',
				 'NAME'              => GetMessage('BUTTON_ALIGN'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'N',
				 'VALUES'            => array(
						'left'   => GetMessage("API_SEARCHFILTER_SLEVA"),
						'right'  => GetMessage("API_SEARCHFILTER_SPRAVA"),
						'center' => GetMessage("API_SEARCHFILTER_PO_CENTRU"),
				 ),
				 'REFRESH'           => 'N',
			),
			'NUMBER_TO_STRING'                              => array(
				 'PARENT'            => 'VISUAL',
				 'NAME'              => GetMessage('NUMBER_TO_STRING'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => $arProperty_N,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SELECT_IN_CHECKBOX'                            => array(
				 'PARENT'            => 'VISUAL',
				 'NAME'              => GetMessage('SELECT_IN_CHECKBOX'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => $arProperty_L,
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'CHECKBOX_NEW_STRING'                           => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('CHECKBOX_NEW_STRING'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'REPLACE_ALL_LABEL'                             => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('REPLACE_ALL_LABEL'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'REMOVE_POINTS'                                 => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('REMOVE_POINTS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'SECTIONS_DEPTH_LEVEL'                          => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('SECTIONS_DEPTH_LEVEL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'SECTIONS_FIELD_TITLE'                          => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('SECTIONS_FIELD_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage("SECTIONS_FIELD_TITLE_VALUE"),
			),
			'SECTIONS_FIELD_VALUE_TITLE'                    => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('SECTIONS_FIELD_VALUE_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage("SECTIONS_FIELD_VALUE_TITLE_DEFAULT"),
			),
			'INCLUDE_JQUERY'                                => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_JQUERY'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'INCLUDE_PLACEHOLDER'                           => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_PLACEHOLDER'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'INCLUDE_CHOSEN_PLUGIN'                         => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_CHOSEN_PLUGIN'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'CHOSEN_PLUGIN_PARAM__disable_search_threshold' => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('CHOSEN_PLUGIN_PARAM__disable_search_threshold'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '30',
			),
			'INCLUDE_FORMSTYLER_PLUGIN'                     => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_FORMSTYLER_PLUGIN'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'INCLUDE_AUTOCOMPLETE_PLUGIN'                   => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_AUTOCOMPLETE_PLUGIN'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'INCLUDE_JQUERY_UI'                             => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_JQUERY_UI'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'INCLUDE_JQUERY_UI_SLIDER'                      => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_JQUERY_UI_SLIDER'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'JQUERY_UI_SLIDER_BORDER_RADIUS'                => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_UI_SLIDER_BORDER_RADIUS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'INCLUDE_JQUERY_UI_SLIDER_TOOLTIP'              => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_JQUERY_UI_SLIDER_TOOLTIP'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'JQUERY_UI_THEME'                               => array(
				 'PARENT'            => 'JQUERY',
				 'NAME'              => GetMessage('JQUERY_UI_THEME'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => array(
						'aristo'         => 'aristo',
						'black-tie'      => 'black-tie',
						'blitzer'        => 'blitzer',
						'cupertino'      => 'cupertino',
						'dark-hive'      => 'dark-hive',
						'delta'          => 'delta',
						'dot-luv'        => 'dot-luv',
						'eggplant'       => 'eggplant',
						'excite-bike'    => 'excite-bike',
						'flick'          => 'flick',
						'hot-sneaks'     => 'hot-sneaks',
						'humanity'       => 'humanity',
						'le-frog'        => 'le-frog',
						'mint-choc'      => 'mint-choc',
						'overcast'       => 'overcast',
						'pepper-grinder' => 'pepper-grinder',
						'redmond'        => 'redmond',
						'smoothness'     => 'smoothness',
						'south-street'   => 'south-street',
						'start'          => 'start',
						'sunny'          => 'sunny',
						'swanky-purse'   => 'swanky-purse',
						'trontastic'     => 'trontastic',
						'ts-red'         => 'ts-red',
						'ui-darkness'    => 'ui-darkness',
						'ui-lightness'   => 'ui-lightness',
						'vader'          => 'vader',
				 ),
				 'REFRESH'           => 'Y',
				 'DEFAULT'           => array('ts-red'),
			),
			'JQUERY_UI_FONT_SIZE'                           => array(
				 'PARENT'            => 'JQUERY',
				 'NAME'              => GetMessage('JQUERY_UI_FONT_SIZE'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => array(
						'8px'  => '8px',
						'9px'  => '9px',
						'10px' => '10px',
						'11px' => '11px',
						'12px' => '12px',
						'13px' => '13px',
						'14px' => '14px',
						'15px' => '15px',
						'16px' => '16px',
				 ),
				 'REFRESH'           => 'Y',
				 'DEFAULT'           => array('10px'),
			),
			'SAVE_IN_SESSION'                               => array(
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
				 'NAME'    => GetMessage('IBLOCK_SAVE_IN_SESSION'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
	 ),
);

if(!$OFFERS_IBLOCK_ID) {
	unset($arComponentParameters['PARAMETERS']['OFFERS_FIELD_CODE']);
	unset($arComponentParameters['PARAMETERS']['OFFERS_PROPERTY_CODE']);
}