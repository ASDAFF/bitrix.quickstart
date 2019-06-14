<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

if(!CModule::IncludeModule('api.search')) {
	ShowError(GetMessage('API_SEARCH_MODULE_ERROR'));
	return;
}

if(!CModule::IncludeModule('iblock')) {
	ShowError(GetMessage('IBLOCK_MODULE_ERROR'));
	return;
}

$bCatalog = CModule::IncludeModule('catalog');

$arIBlockType = CIBlockParameters::GetIBlockTypes(Array('-' => GetMessage('NOT_SET')));

$arIBlock = array();
$rsIBlock = CIBlock::GetList(
	 Array('sort' => 'asc'),
	 Array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
);
while($arr = $rsIBlock->Fetch()) {
	$arIBlock[ $arr['ID'] ] = $arr['NAME'];
}

$countIBlockType = (count($arIBlockType) < 10 ? count($arIBlockType) : 10);
$countIBlock     = (count($arIBlock) < 10 ? count($arIBlock) : 10);


$arSearchIn = array(
	 'NAME' => GetMessage('SEARCH_IN_FIELD_NAME'),
);

$arComponentParameters = array(
	 'GROUPS'     => array(
			'PRICES' => array(
				 'NAME' => GetMessage('GROUP_PRICES'),
				 'SORT' => 310,
			),
			'JQUERY' => array(
				 'NAME' => GetMessage('GROUP_JQUERY'),
				 'SORT' => 320,
			),
			'IBLOCK' => array(
				 'NAME' => GetMessage('GROUP_IBLOCK'),
				 'SORT' => 1100,
			),
	 ),
	 'PARAMETERS' => array(
			'SORT_BY1'          => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SORT_BY1'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'SORT',
				 'VALUES'            => GetMessage('SORT_FIELDS'),
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_ORDER1'       => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SORT_ORDER'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'ASC',
				 'VALUES'            => GetMessage('SORT_ORDERS'),
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_BY2'          => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SORT_BY2'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'SHOW_COUNTER',
				 'VALUES'            => GetMessage('SORT_FIELDS'),
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_ORDER2'       => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SORT_ORDER'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'DESC',
				 'VALUES'            => GetMessage('SORT_ORDERS'),
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_BY3'          => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SORT_BY3'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'NAME',
				 'VALUES'            => GetMessage('SORT_FIELDS'),
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'SORT_ORDER3'       => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SORT_ORDER'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'ASC',
				 'VALUES'            => GetMessage('SORT_ORDERS'),
				 'ADDITIONAL_VALUES' => 'Y',
			),
			'RESULT_PAGE'       => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => GetMessage('RESULT_PAGE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '/search/',
			),
			'SEARCH_MODE'       => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('SEARCH_MODE'),
				 'TYPE'              => 'LIST',
				 'DEFAULT'           => 'EXACT',
				 'VALUES'            => GetMessage('SEARCH_MODE_VALUES'),
				 'ADDITIONAL_VALUES' => 'N',
			),
			'ITEMS_LIMIT'       => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => GetMessage('ITEMS_LIMIT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '14',
			),
			'USE_TITLE_RANK'    => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => GetMessage('USE_TITLE_RANK'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'USE_SEARCH_QUERY'  => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => GetMessage('USE_SEARCH_QUERY'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),


			//VISUAL
			'INPUT_PLACEHOLDER' => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('INPUT_PLACEHOLDER'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('INPUT_PLACEHOLDER_DEFAULT'),
			),
			'BUTTON_TEXT'       => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('BUTTON_TEXT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('BUTTON_TEXT_DEFAULT'),
			),
			'RESULT_NOT_FOUND'  => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('RESULT_NOT_FOUND'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('RESULT_NOT_FOUND_DEFAULT'),
			),
			'RESULT_URL_TEXT'   => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('RESULT_URL_TEXT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('RESULT_URL_TEXT_DEFAULT'),
			),
			'PICTURE'           => array(
				 'PARENT'   => 'VISUAL',
				 'NAME'     => GetMessage('PICTURE'),
				 'TYPE'     => 'LIST',
				 'DEFAULT'  => '',
				 'MULTIPLE' => 'Y',
				 'VALUES'   => GetMessage('PICTURE_VALUES'),
			),

			'RESIZE_PICTURE'             => array(
				 'PARENT'  => 'VISUAL',
				 'NAME'    => GetMessage('RESIZE_PICTURE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('RESIZE_PICTURE_DEFAULT'),
			),

			//JQUERY
			'INCLUDE_JQUERY'             => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('INCLUDE_JQUERY'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			/*'USE_SCROLL'                 => array(
				'PARENT'  => 'JQUERY',
				'NAME'    => GetMessage('USE_SCROLL'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'N',
			),*/
			'JQUERY_BACKDROP_BACKGROUND' => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_BACKDROP_BACKGROUND'),
				 'TYPE'    => 'COLORPICKER',
				 'DEFAULT' => '#3879D9',
			),
			'JQUERY_BACKDROP_OPACITY'    => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_BACKDROP_OPACITY'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '0.1',
			),
			'JQUERY_BACKDROP_Z_INDEX'    => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_BACKDROP_Z_INDEX'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 900,
			),
			'JQUERY_WAIT_TIME'           => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_WAIT_TIME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 500,
			),
			'JQUERY_SEARCH_PARENT_ID'    => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_SEARCH_PARENT_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '.api-search-title',
			),
			'JQUERY_SCROLL_THEME'        => array(
				 'PARENT'  => 'JQUERY',
				 'NAME'    => GetMessage('JQUERY_SCROLL_THEME'),
				 'TYPE'    => 'LIST',
				 'VALUES'  => array(
						'_simple'   => 'simple',
						'_winxp'    => 'winxp',
						'_macosx'   => 'macosx',
						'_ubuntu12' => 'ubuntu12',
				 ),
				 'DEFAULT' => '_simple',
			),

			//ADDITIONAL_SETTINGS
			'INCLUDE_CSS' => array(
				 'PARENT'  => 'ADDITIONAL_SETTINGS',
				 'NAME'    => GetMessage('INCLUDE_CSS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),

			//IBLOCK
			'IBLOCK_TYPE' => array(
				 'PARENT'            => 'IBLOCK',
				 'NAME'              => GetMessage('IBLOCK_TYPE'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'N',
				 'VALUES'            => $arIBlockType,
				 'REFRESH'           => 'Y',
				 'MULTIPLE'          => 'Y',
				 'SIZE'              => $countIBlockType,
			),
			'IBLOCK_ID'   => array(
				 'PARENT'            => 'IBLOCK',
				 'NAME'              => GetMessage('IBLOCK_ID'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => $arIBlock,
				 'REFRESH'           => 'Y',
				 'MULTIPLE'          => 'Y',
				 'SIZE'              => $countIBlock,
			),
	 ),
);


if($arCurrentValues['IBLOCK_ID']) {
	foreach($arIBlock as $iblockId => $iblockName) {
		if(!in_array($iblockId, $arCurrentValues['IBLOCK_ID']))
			continue;

		$category = 'IBLOCK_' . $iblockId;

		$arComponentParameters['GROUPS'][ $category ]                = array(
			 'NAME' => GetMessage('CATEGORY_NAME', array('#NAME#' => $iblockName)),
			 'SORT' => 1200 + $iblockId,
		);
		$arComponentParameters['PARAMETERS'][ $category . '_TITLE' ] = array(
			 'PARENT'  => $category,
			 'NAME'    => GetMessage('CATEGORY_TITLE'),
			 'TYPE'    => 'STRING',
			 'DEFAULT' => $iblockName,
		);

		$arComponentParameters['PARAMETERS'][ $category . '_SECTION_URL' ] = CIBlockParameters::GetPathTemplateParam(
			 'SECTION',
			 $category . '_SECTION_URL',
			 GetMessage('IBLOCK_SECTION_URL'),
			 "",
			 $category
		);

		$arComponentParameters['PARAMETERS'][ $category . '_DETAIL_URL' ] = CIBlockParameters::GetPathTemplateParam(
			 'DETAIL',
			 $category . '_DETAIL_URL',
			 GetMessage('IBLOCK_DETAIL_URL'),
			 "",
			 $category
		);
		/*"SECTION_URL"                => CIBlockParameters::GetPathTemplateParam(
			 "SECTION",
			 "SECTION_URL",
			 GetMessage("IBLOCK_SECTION_URL"),
			 "",
			 "URL_TEMPLATES"
		),
		"DETAIL_URL"                 => CIBlockParameters::GetPathTemplateParam(
			 "DETAIL",
			 "DETAIL_URL",
			 GetMessage("IBLOCK_DETAIL_URL"),
			 "",
			 "URL_TEMPLATES"
		),*/

		//Get properties
		$arPropertyS  = array('' => GetMessage('NOT_SET'));
		$arPropertyNS = array('' => GetMessage('NOT_SET'));

		$rsProp = CIBlockProperty::GetList(
			 array('SORT' => 'ASC', 'NAME' => 'ASC'),
			 array('IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y')
		);
		while($arr = $rsProp->Fetch()) {
			if(in_array($arr['PROPERTY_TYPE'], array('L', 'N', 'S'))) {
				if($arr['PROPERTY_TYPE'] == 'S')
					$arPropertyS[ $arr['CODE'] ] = GetMessage('SEARCH_IN_PROPERTY_LABEL') . $arr['NAME'];

				$arPropertyNS[ $arr['CODE'] ] = GetMessage('SEARCH_IN_PROPERTY_LABEL') . $arr['NAME'];
			}
		}


		//Get sections
		$arSections = array();
		$rsSections = CIBlockSection::GetList(
			 array('left_margin' => 'asc'),
			 array('IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'),
			 false,
			 array('ID', 'DEPTH_LEVEL', 'NAME')
		);
		while($arSection = $rsSections->Fetch()) {
			$arSections[ $arSection['ID'] ] = str_repeat(' . ', $arSection['DEPTH_LEVEL']) . $arSection['NAME'];
		}



		// Set params
		$countPropertyS  = count($arPropertyS);
		$countPropertyNS = count($arPropertyNS);
		$countSections   = count($arSections);
		$arSeachFields   = GetMessage('SEARCH_IN_FIELD_DEFAULT');
		$arShowFields    = GetMessage('SHOW_FIELD_DEFAULT');



		// Search in
		$arComponentParameters['PARAMETERS'][ $category . '_FIELD' ] = array(
			 'PARENT'            => $category,
			 'NAME'              => GetMessage('SEARCH_IN_FIELD'),
			 'TYPE'              => 'LIST',
			 'MULTIPLE'          => 'Y',
			 'VALUES'            => $arSeachFields,
			 'ADDITIONAL_VALUES' => 'N',
			 'DEFAULT'           => 'NAME',
			 'SIZE'              => count($arSeachFields),
		);

		if($arPropertyS) {
			$arComponentParameters['PARAMETERS'][ $category . '_PROPERTY' ] = array(
				 'PARENT'            => $category,
				 'NAME'              => GetMessage('SEARCH_IN_PROPERTY'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => $arPropertyS,
				 'ADDITIONAL_VALUES' => 'N',
				 'DEFAULT'           => '',
				 'SIZE'              => ($countPropertyS < 10 ? $countPropertyS : 10),
			);
		}

		if($arSections) {
			$arComponentParameters['PARAMETERS'][ $category . '_SECTION' ] = array(
				 'PARENT'            => $category,
				 'NAME'              => GetMessage('SEARCH_IN_SECTION'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'ADDITIONAL_VALUES' => 'Y',
				 'DEFAULT'           => '',
				 'VALUES'            => $arSections,
				 'SIZE'              => ($countSections < 10 ? $countSections : 10),
			);
		}


		$arComponentParameters['PARAMETERS'][ $category . '_REGEX' ] = array(
			 'PARENT'            => $category,
			 'NAME'              => GetMessage('SEARCH_IN_REGEX'),
			 'TYPE'              => 'STRING',
			 'MULTIPLE'          => 'N',
			 'ADDITIONAL_VALUES' => 'N',
			 'DEFAULT'           => GetMessage('SEARCH_IN_REGEX_DEFAULT'),
		);


		// Show in
		$arComponentParameters['PARAMETERS'][ $category . '_SHOW_FIELD' ] = array(
			 'PARENT'            => $category,
			 'NAME'              => GetMessage('SHOW_FIELD'),
			 'TYPE'              => 'LIST',
			 'MULTIPLE'          => 'Y',
			 'VALUES'            => $arShowFields,
			 'ADDITIONAL_VALUES' => 'N',
			 'DEFAULT'           => '',
			 'SIZE'              => count($arShowFields),
		);

		//Offers fields
		/*if($bCatalog)
		{
			if($offers = CCatalogSku::GetInfoByProductIBlock($iblockId))
			{
				$arComponentParameters['PARAMETERS'][ $category . '_SHOW_OFFERS_FIELD' ] = array(
					'PARENT'            => $category,
					'NAME'              => GetMessage('SHOW_FIELD'),
					'TYPE'              => 'LIST',
					'MULTIPLE'          => 'Y',
					'VALUES'            => $arShowFields,
					'ADDITIONAL_VALUES' => 'N',
					'DEFAULT'           => '',
					'SIZE'              => count($arShowFields),
				);
			}
		}*/


		if($arPropertyNS) {
			$arComponentParameters['PARAMETERS'][ $category . '_SHOW_PROPERTY' ] = array(
				 'PARENT'            => $category,
				 'NAME'              => GetMessage('SHOW_PROPERTY'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => $arPropertyNS,
				 'ADDITIONAL_VALUES' => 'N',
				 'DEFAULT'           => '',
				 'SIZE'              => ($countPropertyNS < 10 ? $countPropertyNS : 10),
			);
		}
	}
}



if(CModule::IncludeModule('catalog')) {
	$arPrice = array('' => GetMessage('NOT_SET'));
	$rsPrice = CCatalogGroup::GetList($v1 = 'sort', $v2 = 'asc');
	while($arr = $rsPrice->Fetch())
		$arPrice[ $arr['NAME'] ] = '[' . $arr['NAME'] . '] ' . $arr['NAME_LANG'];


	$arComponentParameters['PARAMETERS']['PRICE_CODE']        = array(
		 'PARENT'   => 'PRICES',
		 'NAME'     => GetMessage('PRICE_CODE'),
		 'TYPE'     => 'LIST',
		 'MULTIPLE' => 'Y',
		 'VALUES'   => $arPrice,
	);
	$arComponentParameters['PARAMETERS']['PRICE_EXT']         = array(
		 'PARENT'  => 'PRICES',
		 'NAME'    => GetMessage('PRICE_EXT'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
	);
	$arComponentParameters['PARAMETERS']['PRICE_VAT_INCLUDE'] = array(
		 'PARENT'  => 'PRICES',
		 'NAME'    => GetMessage('PRICE_VAT_INCLUDE'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'Y',
	);
}

if(CModule::IncludeModule('currency')) {
	$arComponentParameters['PARAMETERS']['CONVERT_CURRENCY'] = array(
		 'PARENT'  => 'PRICES',
		 'NAME'    => GetMessage('CONVERT_CURRENCY'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'REFRESH' => 'Y',
	);

	if(isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY']) {
		$arCurrencyList = array();
		$rsCurrencies   = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
		while($arCurrency = $rsCurrencies->Fetch()) {
			$arCurrencyList[ $arCurrency['CURRENCY'] ] = $arCurrency['CURRENCY'];
		}
		$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
			 'PARENT'            => 'PRICES',
			 'NAME'              => GetMessage('CONVERT_CURRENCY_ID'),
			 'TYPE'              => 'LIST',
			 'VALUES'            => $arCurrencyList,
			 'DEFAULT'           => CCurrency::GetBaseCurrency(),
			 'ADDITIONAL_VALUES' => 'Y',
		);
	}


	$arComponentParameters['PARAMETERS']['USE_CURRENCY_SYMBOL'] = array(
		 'PARENT'  => 'PRICES',
		 'NAME'    => GetMessage('USE_CURRENCY_SYMBOL'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'REFRESH' => 'Y',
	);

	if($arCurrentValues['USE_CURRENCY_SYMBOL'] == 'Y') {
		$arComponentParameters['PARAMETERS']['CURRENCY_SYMBOL'] = array(
			 'PARENT'  => 'PRICES',
			 'NAME'    => GetMessage('CURRENCY_SYMBOL'),
			 'TYPE'    => 'STRING',
			 'DEFAULT' => GetMessage('CURRENCY_SYMBOL_DEFAULT'),
		);
	}
}