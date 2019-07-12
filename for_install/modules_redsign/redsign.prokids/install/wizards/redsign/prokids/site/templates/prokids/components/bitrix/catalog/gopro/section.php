<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

global $APPLICATION,$JSON;

if( \Bitrix\Main\Loader::includeModule('iblock') )
{
	// take data about curent section
	if(\Bitrix\Main\Loader::includeModule('iblock'))
	{
		$arFilter = array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'ACTIVE' => 'Y',
			'GLOBAL_ACTIVE' => 'Y',
		);
		if(IntVal($arResult['VARIABLES']['SECTION_ID'])>0)
		{
			$arFilter['ID'] = $arResult['VARIABLES']['SECTION_ID'];
		} elseif($arResult['VARIABLES']['SECTION_CODE']!='') {
			$arFilter['=CODE'] = $arResult['VARIABLES']['SECTION_CODE'];
		}
		$obCache = new CPHPCache();
		if($obCache->InitCache(36000, serialize($arFilter) ,'/iblock/catalog'))
		{
			$arCurSection = $obCache->GetVars();
		} elseif($obCache->StartDataCache()) {
			$arCurSection = array();
			$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array('ID','LEFT_MARGIN','RIGHT_MARGIN'));
			if(defined('BX_COMP_MANAGED_CACHE'))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache('/iblock/catalog');
				if ($arCurSection = $dbRes->GetNext())
				{
					$CACHE_MANAGER->RegisterTag('iblock_id_'.$arParams['IBLOCK_ID']);
				}
				$CACHE_MANAGER->EndTagCache();
			} else {
				if(!$arCurSection = $dbRes->GetNext())
				{
					$arCurSection = array();
				}
			}
			$obCache->EndDataCache($arCurSection);
		}
	}
	// /take data about curent section
}


?><div class="catalog clearfix" id="catalog"><?
	if( $arParams['SECTIONS_VIEW_MODE']=='VIEW_SECTIONS' && ( ($arCurSection['RIGHT_MARGIN']-$arCurSection['LEFT_MARGIN'])>1 ) )
	{
		
		$APPLICATION->IncludeComponent(
			'bitrix:catalog.section.list',
			'gopro',
			array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
				'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'COUNT_ELEMENTS' => $arParams['SECTION_COUNT_ELEMENTS'],
				'TOP_DEPTH' => $arParams['SECTION_TOP_DEPTH'],
				'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
				'SET_TITLE' => $arParams['SET_TITLE'],
			),
			$component,
			array('HIDE_ICONS'=>'Y')
		);
		
	} else { // VIEW_MODE
		
		$IS_AJAX_CATALOG = false;
		// isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		if( $_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['get']=='catalog' )
		{
			$APPLICATION->RestartBuffer();
			$IS_AJAX_CATALOG = true;
		}
		?><div class="sidebar"><?
		$APPLICATION->IncludeComponent(
			'bitrix:catalog.section.list',
			'lines',
			Array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
				'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'COUNT_ELEMENTS' => $arParams['SECTION_COUNT_ELEMENTS'],
				'TOP_DEPTH' => '1',
				'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
			),
			$component
		);
		if($arParams['USE_FILTER']=='Y') {
			?><?$APPLICATION->IncludeComponent(
				'bitrix:catalog.smart.filter',
				( $arParams['FILTER_TEMPLATE']!='' ? $arParams['FILTER_TEMPLATE'] : 'gopro' ),
				array(
					'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'SECTION_ID' => $arCurSection['ID'],
					'FILTER_NAME' => $arParams['FILTER_NAME'],
					'PRICE_CODE' => $arParams['FILTER_PRICE_CODE'],
					'CACHE_TYPE' => $arParams['CACHE_TYPE'],
					'CACHE_TIME' => $arParams['CACHE_TIME'],
					'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
					'SAVE_IN_SESSION' => 'N',
					'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
					// simple
					'PROPS_FILTER_COLORS' => $arParams['PROPS_FILTER_COLORS'],
					'FILTER_PRICE_GROUPED' => $arParams['FILTER_PRICE_GROUPED'],
					'FILTER_PRICE_GROUPED_FOR' => $arParams['FILTER_PRICE_GROUPED_FOR'],
					'FILTER_PROP_SCROLL' => $arParams['FILTER_PROP_SCROLL'],
					'FILTER_PROP_SEARCH' => $arParams['FILTER_PROP_SEARCH'],
					'FILTER_FIXED' => $arParams['FILTER_FIXED'],
					'FILTER_USE_AJAX' => $arParams['FILTER_USE_AJAX'],
					'FILTER_DISABLED_PIC_EFFECT' => $arParams['FILTER_DISABLED_PIC_EFFECT'],
					// offers
					'PROPS_SKU_FILTER_COLORS' => $arParams['PROPS_SKU_FILTER_COLORS'],
					'FILTER_SKU_PROP_SCROLL' => $arParams['FILTER_SKU_PROP_SCROLL'],
					'FILTER_SKU_PROP_SEARCH' => $arParams['FILTER_SKU_PROP_SEARCH'],
					// compare
					'USE_COMPARE' => $arParams['USE_COMPARE'],
				),
				$component
			);?><?
		}
		?></div><?
		
		?><div class="prods" id="prods"><?
//$frame = $this->createFrame('prods',false)->begin('<img class="ajax_loader" src="'.SITE_TEMPLATE_PATH.'/img/ajax-loader.gif" />');
//\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID('prods');
$APPLICATION->ShowViewContent('catalog_section_list_descr');
			?><div class="mix clearfix"><?
				?><div class="compareandpaginator clearfix"><?
					if($arParams['USE_COMPARE']=='Y')
					{
						?><div id="compare" class="compare"><?
						$APPLICATION->IncludeComponent('bitrix:catalog.compare.list', 'gopro', array(
							'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
							'IBLOCK_ID' => $arParams['IBLOCK_ID'],
							'NAME' => $arParams['COMPARE_NAME'],
							'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
							'COMPARE_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
							'PROPCODE_MORE_PHOTO' => $arParams['PROPCODE_MORE_PHOTO'],
							'PROPCODE_SKU_MORE_PHOTO' => $arParams['PROPCODE_SKU_MORE_PHOTO'],
							),
							null,
							array('HIDE_ICONS'=>'Y')
						);
						?></div><?
					}
				?></div><?
global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;
$arOutupVariables = array(0 => "10",1 => "15",2 => "20");
if(is_array($arParams['SORTER_OUTPUT_OF']) && count($arParams['SORTER_OUTPUT_OF'])>0){
	$arOutupVariables = $arParams['SORTER_OUTPUT_OF'];
}
$APPLICATION->IncludeComponent(
	"redsign:catalog.sorter", 
	"gopro", 
	array(
		"ALFA_ACTION_PARAM_NAME" => "alfaction",
		"ALFA_ACTION_PARAM_VALUE" => "alfavalue",
		"ALFA_CHOSE_TEMPLATES_SHOW" => "Y",
		"ALFA_CNT_TEMPLATES" => ( isset($arParams["SORTER_CNT_TEMPLATES"]) ? $arParams["SORTER_CNT_TEMPLATES"] : '3' ),
		"ALFA_DEFAULT_TEMPLATE" => $arParams["SORTER_DEFAULT_TEMPLATE"],
		"ALFA_CNT_TEMPLATES_0" => $arParams["SORTER_TEMPLATE_NAME_3"],
		"ALFA_CNT_TEMPLATES_NAME_0" => "table",
		"ALFA_CNT_TEMPLATES_1" => $arParams["SORTER_TEMPLATE_NAME_2"],
		"ALFA_CNT_TEMPLATES_NAME_1" => "gallery",
		"ALFA_CNT_TEMPLATES_2" => $arParams["SORTER_TEMPLATE_NAME_1"],
		"ALFA_CNT_TEMPLATES_NAME_2" => "showcase",
		"ALFA_SORT_BY_SHOW" => ( $arParams['SORTER_OFF_SORT_BY']=='Y' ? 'N' : 'Y' ),
		"ALFA_SHORT_SORTER" => "N",
		"ALFA_SORT_BY_NAME" => array(
			0 => "sort",
			1 => "name",
			2 => "PROPERTY_PROD_PRICE_FALSE",
			3 => "",
		),
		"ALFA_SORT_BY_DEFAULT" => "sort_asc",
		"ALFA_OUTPUT_OF_SHOW" => ( $arParams['SORTER_OFF_OUTPUT_OF_SHOW']=='Y' ? 'N' : 'Y' ),
		"ALFA_OUTPUT_OF" => $arOutupVariables,
		"ALFA_OUTPUT_OF_DEFAULT" => ( isset($arParams["SORTER_OUTPUT_OF_DEFAULT"]) ? $arParams["SORTER_OUTPUT_OF_DEFAULT"] : '15' ),
		"ALFA_OUTPUT_OF_SHOW_ALL" => "N",
		"ALFA_DONT_REDIRECT" => "Y",
		"AJAXPAGESID" => "ajaxpages_gmci",
		"COMPONENT_TEMPLATE" => "gopro"
	),
	false
);
			?></div><?
			?><div id="ajaxpages_gmci" class="ajaxpages_gmci"><?
$IS_SORTERCHANGE = 'N';
if($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['sorterchange']=='ajaxpages_gmci')
{
	$IS_SORTERCHANGE = 'Y';
	$JSON['TYPE'] = 'OK';
}
if($_REQUEST['ajaxpages']=='Y' && $_REQUEST['ajaxpagesid']=='ajaxpages_gmci')
{
	$IS_AJAXPAGES = 'Y';
	$JSON['TYPE'] = 'OK';
}
				$intSectionID = 0;
				?><?$intSectionID = $APPLICATION->IncludeComponent(
					'bitrix:catalog.section',
					'gopro',
					array(
						'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
						'IBLOCK_ID' => $arParams['IBLOCK_ID'],
						'ELEMENT_SORT_FIELD' => $alfaCSortType,//$arParams['ELEMENT_SORT_FIELD'],
						'ELEMENT_SORT_ORDER' => $alfaCSortToo,//$arParams['ELEMENT_SORT_ORDER'],
						'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
						'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
						'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
						'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
						'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
						'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
						'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
						'BASKET_URL' => $arParams['BASKET_URL'],
						'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
						'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
						'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
						'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
						'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
						'FILTER_NAME' => $arParams['FILTER_NAME'],
						'CACHE_TYPE' => $arParams['CACHE_TYPE'],
						'CACHE_TIME' => $arParams['CACHE_TIME'],
						'CACHE_FILTER' => $arParams['CACHE_FILTER'],
						'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
						'SET_TITLE' => $arParams['SET_TITLE'],
						'SET_STATUS_404' => $arParams['SET_STATUS_404'],
						'DISPLAY_COMPARE' => $arParams['USE_COMPARE'],
						'PAGE_ELEMENT_COUNT' => $alfaCOutput,//$arParams['PAGE_ELEMENT_COUNT'],
						'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],
						'PRICE_CODE' => $arParams['PRICE_CODE'],
						'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
						'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

						'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
						'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
						'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
						'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
						'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],

						'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
						'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
						'PAGER_TITLE' => $arParams['PAGER_TITLE'],
						'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
						'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
						'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
						'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
						'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],

						'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
						'OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
						'OFFERS_PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'],
						'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
						'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
						'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
						'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
						'OFFERS_LIMIT' => $arParams['LIST_OFFERS_LIMIT'],

						'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
						'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
						'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
						'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
						'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
						'CURRENCY_ID' => $arParams['CURRENCY_ID'],
						'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
						// ajaxpages
						'AJAXPAGESID' => 'ajaxpages_gmci',
						'IS_AJAXPAGES' => $IS_AJAXPAGES,
						'IS_SORTERCHANGE' => $IS_SORTERCHANGE,
						// goPro params
						'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
						'PROP_ARTICLE' => $arParams['PROP_ARTICLE'],
						'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
						'USE_FAVORITE' => $arParams['USE_FAVORITE'],
						'USE_SHARE' => $arParams['USE_SHARE'],
						'SHOW_ERROR_EMPTY_ITEMS' => $arParams['SHOW_ERROR_EMPTY_ITEMS'],
						'EMPTY_ITEMS_HIDE_FIL_SORT' => 'Y',
						'USE_AUTO_AJAXPAGES' => $arParams['USE_AUTO_AJAXPAGES'],
						'OFF_MEASURE_RATION' => $arParams['OFF_MEASURE_RATION'],
						// showcase
						'OFF_SMALLPOPUP' => $arParams['OFF_SMALLPOPUP'],
						// SKU
						'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
						'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
						'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
						'PROPS_ATTRIBUTES_COLOR' => $arParams['PROPS_ATTRIBUTES_COLOR'],
						// store
						'USE_STORE' => $arParams['USE_STORE'],
						'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
						'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
						'MAIN_TITLE' => $arParams['MAIN_TITLE'],
						// view
						'VIEW' => $alfaCTemplate,
						// columns
						'COLUMNS5' => 'N',
					),
					$component,
					array('HIDE_ICONS'=>'Y')
				);?><?
if($IS_AJAXPAGES=='Y' || $IS_SORTERCHANGE=='Y')
{
	$APPLICATION->RestartBuffer();
	if(SITE_CHARSET!='utf-8')
	{
		$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
		$json_str_utf = json_encode($data);
		$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
		echo $json_str;
	} else {
		echo json_encode($JSON);
	}
	die();
}
			?></div><?
			?><div id="paginator"><?
				$APPLICATION->ShowViewContent('paginator');
			?></div><?
				?><div class="bottom"><?
$APPLICATION->IncludeComponent('redsign:catalog.sorter', 'gopro', array(
	'ALFA_ACTION_PARAM_NAME' => 'alfaction',
	'ALFA_ACTION_PARAM_VALUE' => 'alfavalue',
	'ALFA_CHOSE_TEMPLATES_SHOW' => 'N',
	'ALFA_SORT_BY_SHOW' => 'N',
	'ALFA_OUTPUT_OF_SHOW' => 'Y',
	'ALFA_OUTPUT_OF' => array(
		0 => '10',
		1 => '15',
		2 => '20',
	),
	'ALFA_OUTPUT_OF_DEFAULT' => '15',
	'ALFA_OUTPUT_OF_SHOW_ALL' => 'N',
	'ALFA_DONT_REDIRECT' => 'Y',
	'AJAXPAGESID' => 'ajaxpages_gmci',
	),
	false
);
			?></div><?
		?></div><?
if( $IS_AJAX_CATALOG )
{
	die();
}
	} //VIEW_MODE
	
?></div><?