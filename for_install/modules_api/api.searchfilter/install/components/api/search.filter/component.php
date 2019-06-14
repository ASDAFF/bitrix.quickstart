<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 * @var CCacheManager    $CACHE_MANAGER
 *
 * @var                  $JS_CONFIG
 */

if(!CModule::IncludeModule('api.searchfilter')) {
	ShowError(GetMessage('API_SF_MODULE_ERROR'));
	return;
}

if(!CModule::IncludeModule('iblock')) {
	ShowError(GetMessage('API_SF_IBLOCK_MODULE_ERROR'));
	return;
}

$MOD_CATALOG = CModule::IncludeModule('catalog');

$arParams['SECTION_ID']   = intval($arParams['SECTION_ID']);
$arParams['SECTION_CODE'] = trim($arParams['SECTION_CODE']);

$arParams['SECTIONS_FIELD_TITLE']             = strlen(trim($arParams['SECTIONS_FIELD_TITLE'])) ? trim($arParams['SECTIONS_FIELD_TITLE']) : GetMessage('CC_BCF_TOP_LEVEL');
$arParams['SECTIONS_FIELD_VALUE_TITLE']       = trim($arParams['SECTIONS_FIELD_VALUE_TITLE']);
$arParams['SECTIONS_DEPTH_LEVEL']             = strlen(trim($arParams['SECTIONS_DEPTH_LEVEL'])) ? explode(',', trim($arParams['SECTIONS_DEPTH_LEVEL'])) : array();
$arParams['INCLUDE_PLACEHOLDER']              = $arParams['INCLUDE_PLACEHOLDER'] == 'Y';
$arParams['INCLUDE_JQUERY']                   = $arParams['INCLUDE_JQUERY'] == 'Y';
$arParams['INCLUDE_CHOSEN_PLUGIN']            = $arParams['INCLUDE_CHOSEN_PLUGIN'] == 'Y';
$arParams['INCLUDE_FORMSTYLER_PLUGIN']        = $arParams['INCLUDE_FORMSTYLER_PLUGIN'] == 'Y';
$arParams['INCLUDE_AUTOCOMPLETE_PLUGIN']      = $arParams['INCLUDE_AUTOCOMPLETE_PLUGIN'] == 'Y';
$arParams['INCLUDE_JQUERY_UI']                = $arParams['INCLUDE_JQUERY_UI'] == 'Y';
$arParams['INCLUDE_JQUERY_UI_SLIDER']         = $arParams['INCLUDE_JQUERY_UI_SLIDER'] == 'Y';
$arParams['JQUERY_UI_SLIDER_BORDER_RADIUS']   = $arParams['JQUERY_UI_SLIDER_BORDER_RADIUS'] == 'Y';
$arParams['INCLUDE_JQUERY_UI_SLIDER_TOOLTIP'] = $arParams['INCLUDE_JQUERY_UI_SLIDER_TOOLTIP'] == 'Y';
$arParams['JQUERY_UI_THEME']                  = !empty($arParams['JQUERY_UI_THEME']) ? trim($arParams['JQUERY_UI_THEME']) : 'ui-lightness';
$arParams['JQUERY_UI_FONT_SIZE']              = !empty($arParams['JQUERY_UI_FONT_SIZE']) ? trim($arParams['JQUERY_UI_FONT_SIZE']) : '10px';
$arParams['REDIRECT_FOLDER']                  = !empty($arParams['REDIRECT_FOLDER']) ? $arParams['REDIRECT_FOLDER'] : '';


//START JS CONFIGS
$JS_CONFIG = "<script type=\"text/javascript\">\njQuery(document).ready(function($){\n";

//v1.1.7
if($arParams['REDIRECT_FOLDER'] && ($APPLICATION->GetCurPage() != $arParams['REDIRECT_FOLDER'])) {
	$JS_CONFIG .= '
        $(".ts-filter [name=del_filter]").click(function(){
            window.location = window.location;

            return false;
        });
    ';
}

if($arParams['INCLUDE_JQUERY_UI_SLIDER'])
	$APPLICATION->SetAdditionalCSS($this->__path . '/css/ui-theme/' . $arParams['JQUERY_UI_THEME'] . '/jquery-ui-1.10.3.custom.min.css');

if($arParams['INCLUDE_JQUERY_UI'])
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery-ui-1.10.3.custom.min.js');

if($arParams['INCLUDE_CHOSEN_PLUGIN']) {
	$arParams['CHOSEN_PLUGIN_PARAM__disable_search_threshold'] = intval($arParams['CHOSEN_PLUGIN_PARAM__disable_search_threshold']) ? intval($arParams['CHOSEN_PLUGIN_PARAM__disable_search_threshold']) : 30;

	$APPLICATION->SetAdditionalCSS($this->__path . '/css/chosen.min.css');
	$APPLICATION->AddHeadScript($this->__path . '/js/chosen.jquery.min.js');
	$JS_CONFIG .= '
        $(".ts-form .chosen-select").chosen({
            allow_single_deselect: true,
            disable_search_threshold: "' . $arParams['CHOSEN_PLUGIN_PARAM__disable_search_threshold'] . '",
            no_results_text: "' . GetMessageJS('PLG_CHOSEN_NO_FOUND') . '"
            //width: "95%",
            //placeholder_text_single: "Select an Option",
            //placeholder_text_multiple: "Select Some Options",
        });
        ';
}

if($arParams['INCLUDE_FORMSTYLER_PLUGIN']) {
	$APPLICATION->SetAdditionalCSS($this->__path . '/css/jquery.formstyler.css');
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery.formstyler.min.js');
}

if($arParams['INCLUDE_PLACEHOLDER'])
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery.placeholder.js');


if($arParams['INCLUDE_AUTOCOMPLETE_PLUGIN'])
	$APPLICATION->AddHeadScript($this->__path . '/js/jquery.autocomplete.min.js');


$JS_CONFIG .= "\n});\n</script>";
$APPLICATION->AddHeadString($JS_CONFIG);
//END JS CONFIGS


if($arParams['INCLUDE_JQUERY'])
	CJSCore::Init(array('jquery'));


//Styles from component options
if($arParams['INCLUDE_JQUERY_UI_SLIDER']) {
	$ui_widget_styles = '<style type="text/css">
			.ts-filter .ui-widget{margin: 5px 0;font-size:' . $arParams['JQUERY_UI_FONT_SIZE'] . '}' . "\n";

	if($arParams['JQUERY_UI_SLIDER_BORDER_RADIUS'])
		$ui_widget_styles .= '.ts-filter .ui-slider .ui-slider-handle{-webkit-border-radius: 100%;-moz-border-radius:100%;border-radius:100%;}' . "\n";

	$ui_widget_styles .= '</style>';

	$APPLICATION->AddHeadString($ui_widget_styles);
}


/*************************************************************************
 * Processing of received parameters
 *************************************************************************/

if(!isset($arParams['CACHE_TIME']))
	$arParams['CACHE_TIME'] = 36000000;

$arParams['ELEMENT_IN_ROW']        = !empty($arParams['ELEMENT_IN_ROW']) ? intval($arParams['ELEMENT_IN_ROW']) : 3;
$arParams['NAME_WIDTH']            = !empty($arParams['NAME_WIDTH']) ? intval($arParams['NAME_WIDTH']) : 130;
$arParams['BUTTON_ALIGN']          = !empty($arParams['BUTTON_ALIGN']) ? trim($arParams['BUTTON_ALIGN']) : 'left';
$arParams['FILTER_TITLE']          = !empty($arParams['FILTER_TITLE']) ? trim($arParams['FILTER_TITLE']) : '';
$arParams['SELECT_WIDTH']          = !empty($arParams['SELECT_WIDTH']) ? 'width:' . intval($arParams['SELECT_WIDTH']) . 'px;' : '';
$arParams['TEXT_WIDTH']            = !empty($arParams['TEXT_WIDTH']) ? 'width:' . intval($arParams['TEXT_WIDTH']) . 'px;' : '';
$arParams['NUMBER_WIDTH']          = !empty($arParams['NUMBER_WIDTH']) ? 'width:' . intval($arParams['NUMBER_WIDTH']) . 'px;' : '';
$arParams['CHECKBOX_NEW_STRING']   = $arParams['CHECKBOX_NEW_STRING'] === 'Y';
$arParams['CHECK_ACTIVE_SECTIONS'] = $arParams['CHECK_ACTIVE_SECTIONS'] === 'Y';
$arParams['REMOVE_POINTS']         = $arParams['REMOVE_POINTS'] === 'Y';


unset($arParams['IBLOCK_TYPE']); //was used only for IBLOCK_ID setup with Editor
$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

$arResult['IBLOCK_FIELD'] = array();
if($arIblockField = CIBlockParameters::GetFieldCode(GetMessage('IBLOCK_FIELD'), ''))
	$arResult['IBLOCK_FIELD'] = $arIblockField['VALUES'];


$arParams['FIELD_CODE'] = (array)$arParams['FIELD_CODE'];
foreach($arParams['FIELD_CODE'] as $k => $v) {
	if($v === '')
		unset($arParams['FIELD_CODE'][ $k ]);
}

$arParams['PROPERTY_CODE'] = (array)$arParams['PROPERTY_CODE'];
foreach($arParams['PROPERTY_CODE'] as $k => $v) {
	if($v === '')
		unset($arParams['PROPERTY_CODE'][ $k ]);
}

$arParams['SELECT_IN_CHECKBOX'] = (array)$arParams['SELECT_IN_CHECKBOX'];
foreach($arParams['SELECT_IN_CHECKBOX'] as $k => $v) {
	if($v === '')
		unset($arParams['SELECT_IN_CHECKBOX'][ $k ]);
}

$arParams['NUMBER_TO_STRING'] = (array)$arParams['NUMBER_TO_STRING'];
foreach($arParams['NUMBER_TO_STRING'] as $k => $v) {
	if($v === '')
		unset($arParams['NUMBER_TO_STRING'][ $k ]);
}

$arParams['PRICE_CODE'] = (array)$arParams['PRICE_CODE'];

$arParams['OFFERS_FIELD_CODE'] = (array)$arParams['OFFERS_FIELD_CODE'];
foreach($arParams['OFFERS_FIELD_CODE'] as $k => $v) {
	if($v === '')
		unset($arParams['OFFERS_FIELD_CODE'][ $k ]);
}

$arParams['OFFERS_PROPERTY_CODE'] = (array)$arParams['OFFERS_PROPERTY_CODE'];
foreach($arParams['OFFERS_PROPERTY_CODE'] as $k => $v) {
	if($v === '')
		unset($arParams['OFFERS_PROPERTY_CODE'][ $k ]);
}

$arParams['SAVE_IN_SESSION'] = $arParams['SAVE_IN_SESSION'] == 'Y';

if(strlen($arParams['FILTER_NAME']) <= 0 || !preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['FILTER_NAME']))
	$arParams['FILTER_NAME'] = 'arrFilter';

$FILTER_NAME = trim($arParams['FILTER_NAME']);

global $$FILTER_NAME;
$$FILTER_NAME = array();

$arParams['LIST_HEIGHT'] = intval($arParams['LIST_HEIGHT']);

if($arParams['LIST_HEIGHT'] <= 0)
	$arParams['LIST_HEIGHT'] = 5;

$arParams['REPLACE_ALL_LABEL'] = $arParams['REPLACE_ALL_LABEL'] == 'Y';
$labelAll                      = ($arParams['REPLACE_ALL_LABEL']) ? '' : GetMessage('CC_BCF_ALL');


$slider_range_all               = $slider_range_price_all = '';
$arNumMaxMin                    = $arPriceMaxMin = $arAclCodes = array();
$arResult['AutocompleteInitJS'] = $sAutocompleteInit = $sAutocompleteInitJS = $sAutocompleteInitCSS = '';

$arUIFilter = array(
	 'IBLOCK_ID' => $arParams['IBLOCK_ID'],
	 'ACTIVE'    => 'Y',
);
if($arParams['CHECK_ACTIVE_SECTIONS']) {
	$arUIFilter['SECTION_ACTIVE']        = 'Y';
	$arUIFilter['SECTION_GLOBAL_ACTIVE'] = 'Y';
}


/*************************************************************************
 * Processing the  'Filter' and 'Reset' button actions
 *************************************************************************/
$arDateFields = array(
	 'ACTIVE_DATE'      => array(
			'from'         => '_ACTIVE_DATE_1',
			'to'           => '_ACTIVE_DATE_2',
			'days_to_back' => '_ACTIVE_DATE_1_DAYS_TO_BACK',
			'filter_from'  => '>=DATE_ACTIVE_FROM',
			'filter_to'    => '<=DATE_ACTIVE_TO',
	 ),
	 'DATE_ACTIVE_FROM' => array(
			'from'         => '_DATE_ACTIVE_FROM_1',
			'to'           => '_DATE_ACTIVE_FROM_2',
			'days_to_back' => '_DATE_ACTIVE_FROM_1_DAYS_TO_BACK',
			'filter_from'  => '>=DATE_ACTIVE_FROM',
			'filter_to'    => '<=DATE_ACTIVE_FROM',
	 ),
	 'DATE_ACTIVE_TO'   => array(
			'from'         => '_DATE_ACTIVE_TO_1',
			'to'           => '_DATE_ACTIVE_TO_2',
			'days_to_back' => '_DATE_ACTIVE_TO_1_DAYS_TO_BACK',
			'filter_from'  => '>=DATE_ACTIVE_TO',
			'filter_to'    => '<=DATE_ACTIVE_TO',
	 ),
	 'DATE_CREATE'      => array(
			'from'         => '_DATE_CREATE_1',
			'to'           => '_DATE_CREATE_2',
			'days_to_back' => '_DATE_CREATE_1_DAYS_TO_BACK',
			'filter_from'  => '>=DATE_CREATE',
			'filter_to'    => '<=DATE_CREATE',
	 ),
);
/*Init filter values*/
$arrPFV  = array();
$arrCFV  = array();
$arrFFV  = array(); //Element fields value
$arrDFV  = array(); //Element date fields
$arrOFV  = array(); //Offer fields values
$arrODFV = array(); //Offer date fields
$arrOPFV = array(); //Offer properties fields

foreach($arDateFields as $id => $arField) {
	$arField['from']                 = array(
		 'name'  => $FILTER_NAME . $arField['from'],
		 'value' => '',
	);
	$arField['to']                   = array(
		 'name'  => $FILTER_NAME . $arField['to'],
		 'value' => '',
	);
	$arField['days_to_back']         = array(
		 'name'  => $FILTER_NAME . $arField['days_to_back'],
		 'value' => '',
	);
	$arrDFV[ $id ]                   = $arField;
	$arField['from']['name']         = 'OF_' . $arField['from']['name'];
	$arField['to']['name']           = 'OF_' . $arField['to']['name'];
	$arField['days_to_back']['name'] = 'OF_' . $arField['days_to_back']['name'];
	$arrODFV[ $id ]                  = $arField;
}

/*Leave filter values empty*/
if(strlen($_REQUEST['del_filter']) > 0) {
	//LocalRedirect($APPLICATION->GetCurPage());

	foreach($arrDFV as $id => $arField) {
		$GLOBALS[ $arField['days_to_back']['name'] ] = '';
	}
	foreach($arrODFV as $id => $arField) {
		$GLOBALS[ $arField['days_to_back']['name'] ] = '';
	}
}/*Read filter values from request*/
elseif(strlen($_REQUEST['set_filter']) > 0) {
	if(isset($_REQUEST[ $FILTER_NAME . '_pf' ]))
		$arrPFV = $_REQUEST[ $FILTER_NAME . '_pf' ];
	if(isset($_REQUEST[ $FILTER_NAME . '_cf' ]))
		$arrCFV = $_REQUEST[ $FILTER_NAME . '_cf' ];
	if(isset($_REQUEST[ $FILTER_NAME . '_ff' ]))
		$arrFFV = $_REQUEST[ $FILTER_NAME . '_ff' ];
	if(isset($_REQUEST[ $FILTER_NAME . '_of' ]))
		$arrOFV = $_REQUEST[ $FILTER_NAME . '_of' ];
	if(isset($_REQUEST[ $FILTER_NAME . '_op' ]))
		$arrOPFV = $_REQUEST[ $FILTER_NAME . '_op' ];
	$now = time();
	foreach($arrDFV as $id => $arField) {
		$name = $arField['from']['name'];
		if(isset($_REQUEST[ $name ]))
			$arrDFV[ $id ]['from']['value'] = $_REQUEST[ $name ];
		$name = $arField['to']['name'];
		if(isset($_REQUEST[ $name ]))
			$arrDFV[ $id ]['to']['value'] = $_REQUEST[ $name ];
		$name = $arField['days_to_back']['name'];
		if(isset($_REQUEST[ $name ])) {
			$value = $arrDFV[ $id ]['days_to_back']['value'] = $_REQUEST[ $name ];
			if(strlen($value) > 0)
				$arrDFV[ $id ]['from']['value'] = GetTime($now - 86400 * intval($value));
		}
	}
	foreach($arrODFV as $id => $arField) {
		$name = $arField['from']['name'];
		if(isset($_REQUEST[ $name ]))
			$arrODFV[ $id ]['from']['value'] = $_REQUEST[ $name ];
		$name = $arField['to']['name'];
		if(isset($_REQUEST[ $name ]))
			$arrODFV[ $id ]['to']['value'] = $_REQUEST[ $name ];
		$name = $arField['days_to_back']['name'];
		if(isset($_REQUEST[ $name ])) {
			$value = $arrODFV[ $id ]['days_to_back']['value'] = $_REQUEST[ $name ];
			if(strlen($value) > 0)
				$arrODFV[ $id ]['from']['value'] = GetTime($now - 86400 * intval($value));
		}
	}
} /*No action specified, so read from the session (if parameter is set)*/
elseif($arParams['SAVE_IN_SESSION']) {
	if(isset($_SESSION[ $FILTER_NAME . 'arrPFV' ]))
		$arrPFV = $_SESSION[ $FILTER_NAME . 'arrPFV' ];
	if(isset($_SESSION[ $FILTER_NAME . 'arrCFV' ]))
		$arrCFV = $_SESSION[ $FILTER_NAME . 'arrCFV' ];
	if(isset($_SESSION[ $FILTER_NAME . 'arrFFV' ]))
		$arrFFV = $_SESSION[ $FILTER_NAME . 'arrFFV' ];
	if(isset($_SESSION[ $FILTER_NAME . 'arrOFV' ]))
		$arrOFV = $_SESSION[ $FILTER_NAME . 'arrOFV' ];
	if(isset($_SESSION[ $FILTER_NAME . 'arrOPFV' ]))
		$arrOPFV = $_SESSION[ $FILTER_NAME . 'arrOPFV' ];
	if(isset($_SESSION[ $FILTER_NAME . 'arrDFV' ]) && is_array($_SESSION[ $FILTER_NAME . 'arrDFV' ])) {
		foreach($_SESSION[ $FILTER_NAME . 'arrDFV' ] as $id => $arField) {
			$arrDFV[ $id ]['from']['value']         = $arField['from']['value'];
			$arrDFV[ $id ]['to']['value']           = $arField['to']['value'];
			$arrDFV[ $id ]['days_to_back']['value'] = $arField['days_to_back']['value'];
		}
	}
	if(isset($_SESSION[ $FILTER_NAME . 'arrODFV' ]) && is_array($_SESSION[ $FILTER_NAME . 'arrODFV' ])) {
		foreach($_SESSION[ $FILTER_NAME . 'arrODFV' ] as $id => $arField) {
			$arrODFV[ $id ]['from']['value']         = $arField['from']['value'];
			$arrODFV[ $id ]['to']['value']           = $arField['to']['value'];
			$arrODFV[ $id ]['days_to_back']['value'] = $arField['days_to_back']['value'];
		}
	}
}

/*Save filter values to the session*/
if($arParams['SAVE_IN_SESSION']) {
	$_SESSION[ $FILTER_NAME . 'arrPFV' ]  = $arrPFV;
	$_SESSION[ $FILTER_NAME . 'arrCFV' ]  = $arrCFV;
	$_SESSION[ $FILTER_NAME . 'arrFFV' ]  = $arrFFV;
	$_SESSION[ $FILTER_NAME . 'arrOFV' ]  = $arrOFV;
	$_SESSION[ $FILTER_NAME . 'arrDFV' ]  = $arrDFV;
	$_SESSION[ $FILTER_NAME . 'arrODFV' ] = $arrODFV;
	$_SESSION[ $FILTER_NAME . 'arrOPFV' ] = $arrOPFV;
}

if($this->StartResultCache(false, ($arParams['CACHE_GROUPS'] === 'N' ? false : $USER->GetGroups()))) {
	$arResult['arrProp']      = array();
	$arResult['arrPrice']     = array();
	$arResult['arrSection']   = array();
	$arResult['arrOfferProp'] = array();

	// simple fields
	if(in_array('SECTION_ID', $arParams['FIELD_CODE'])) {
		if(!empty($arParams['SECTIONS_FIELD_VALUE_TITLE']))
			$arResult['arrSection'][] = $arParams['SECTIONS_FIELD_VALUE_TITLE'];

		$arSectionSort   = Array('left_margin' => 'asc');
		$arSectionSelect = Array('ID', 'DEPTH_LEVEL', 'NAME');
		$arSectionFilter = Array(
			 'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			 'ACTIVE'    => 'Y',
		);

		if(!empty($arParams['SECTIONS_DEPTH_LEVEL']) && is_array($arParams['SECTIONS_DEPTH_LEVEL']))
			$arSectionFilter['DEPTH_LEVEL'] = $arParams['SECTIONS_DEPTH_LEVEL'];

		$rsSection = CIBlockSection::GetList($arSectionSort, $arSectionFilter, false, $arSectionSelect);
		while($arSection = $rsSection->Fetch()) {
			if($arParams['REMOVE_POINTS'])
				$arResult['arrSection'][ $arSection['ID'] ] = $arSection['NAME'];
			else
				$arResult['arrSection'][ $arSection['ID'] ] = str_repeat(' . ', $arSection['DEPTH_LEVEL']) . $arSection['NAME'];
		}
	}

	// Get prices
	if($MOD_CATALOG) {
		$rsPrice = CCatalogGroup::GetList($v1, $v2);
		while($arPrice = $rsPrice->Fetch()) {
			if(($arPrice['CAN_ACCESS'] == 'Y' || $arPrice['CAN_BUY'] == 'Y') && in_array($arPrice['NAME'], $arParams['PRICE_CODE']))
				$arResult['arrPrice'][ $arPrice['NAME'] ] = array(
					 'ID'    => $arPrice['ID'],
					 'TITLE' => $arPrice['NAME_LANG'],
				);
		}
	}
	else {
		//Get prop price
		if($arParams['PRICE_CODE']) {
			$rsProp = CIBlockProperty::GetList(
				 Array(
						'sort' => 'asc',
						'name' => 'asc',
				 ),
				 Array(
						'ACTIVE'    => 'Y',
						'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				 )
			);
			while($arProp = $rsProp->Fetch()) {
				if(in_array($arProp['CODE'], $arParams['PRICE_CODE']) && in_array($arProp['PROPERTY_TYPE'], array('N'))) {
					$arResult['arrPrice'][ $arProp['CODE'] ] = array(
						 'ID'    => $arProp['ID'],
						 'TITLE' => $arProp['NAME'],
					);
				}
			}
		}
	}

	//Prepare PRICE CODE to select MIN and MAX values
	if(!empty($arResult['arrPrice'])) {
		if($MOD_CATALOG) {
			foreach($arResult['arrPrice'] as $priceK => $priceV) {
				$dbPrices = CPrice::GetList(array('PRICE' => 'ASC'), array('!PRICE' => false, 'CATALOG_GROUP_ID' => $priceV['ID']), false, array("nTopCount" => 1), array());
				if($arPriceRes = $dbPrices->Fetch()) {
					$arPriceMaxMin[ $priceK ]['MIN'] = $arPriceRes['PRICE'];
				}

				$dbPrices = CPrice::GetList(array('PRICE' => 'DESC'), array('!PRICE' => false, 'CATALOG_GROUP_ID' => $priceV['ID']), false, array("nTopCount" => 1), array());
				if($arPriceRes = $dbPrices->Fetch()) {
					$arPriceMaxMin[ $priceK ]['MAX'] = $arPriceRes['PRICE'];
				}

				//Generate jQuery UI range slider for all inputs
				/*$slider_range_price_all .='
					$("#PRICE_'. $priceK .'_RANGE" ).slider({
				        range: true,
				        min: '. intval($arPriceMaxMin[$priceK]['MIN']) .',
				        max: '. intval($arPriceMaxMin[$priceK]['MAX']) .',
				        values: [ '. intval($arPriceMaxMin[$priceK]['MIN']) .', '. intval($arPriceMaxMin[$priceK]['MAX']) .' ],
				        slide: function( event, ui ) {
				            $("#PRICE_'. $priceK .'_LEFT").val(ui.values[ 0 ]);
				            $("#PRICE_'. $priceK .'_RIGHT").val(ui.values[ 1 ]);
				        }
				    });' . "\n";*/
			}
		}
		else {
			foreach($arResult['arrPrice'] as $priceK => $priceV) {
				$arPriceSelect                           = array('PROPERTY_' . $priceK);
				$arPriceFilter[ '!PROPERTY_' . $priceK ] = false;

				$dbPrices = CIBlockElement::GetList(
					 array('PROPERTY_' . $priceK => 'ASC'),
					 array_merge($arUIFilter, $arPriceFilter),
					 false,
					 array("nTopCount" => 1),
					 $arPriceSelect
				);
				while($arPriceRes = $dbPrices->Fetch()) {
					$arPriceMaxMin[ $priceK ]['MIN'] = $arPriceRes[ 'PROPERTY_' . $priceK . '_VALUE' ];
				}

				$dbPrices = CIBlockElement::GetList(
					 array('PROPERTY_' . $priceK => 'DESC'),
					 array_merge($arUIFilter, $arPriceFilter),
					 false,
					 array("nTopCount" => 1),
					 $arPriceSelect
				);
				while($arPriceRes = $dbPrices->Fetch()) {
					$arPriceMaxMin[ $priceK ]['MAX'] = $arPriceRes[ 'PROPERTY_' . $priceK . '_VALUE' ];
				}

				//Generate jQuery UI range slider for all inputs
				/*$slider_range_price_all .='
					$("#PRICE_'. $priceK .'_RANGE" ).slider({
				        range: true,
				        min: '. intval($arPriceMaxMin[$priceK]['MIN']) .',
				        max: '. intval($arPriceMaxMin[$priceK]['MAX']) .',
				        values: [ '. intval($arPriceMaxMin[$priceK]['MIN']) .', '. intval($arPriceMaxMin[$priceK]['MAX']) .' ],
				        slide: function( event, ui ) {
				            $("#PRICE_'. $priceK .'_LEFT").val(ui.values[ 0 ]);
				            $("#PRICE_'. $priceK .'_RIGHT").val(ui.values[ 1 ]);
				        }
				    });' . "\n";*/
			}
		}

		//include MAX and MIN values to cache
		$arResult['arPriceMaxMin'] = $arPriceMaxMin;

		//Generate jQuery UI range slider script
		/*if(!empty($slider_range_price_all))
		{
			$slider_range_prices_script = '<script type="text/javascript">
				jQuery(document).ready(function($){
					' . $slider_range_price_all .'
					}); //END Ready
					</script>';

			$APPLICATION->AddHeadString($slider_range_prices_script);
		}*/
	}


	// properties
	$rsProp = CIBlockProperty::GetList(
		 Array(
				'sort' => 'asc',
				'name' => 'asc',
		 ),
		 Array(
				'ACTIVE'    => 'Y',
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		 )
	);
	while($arProp = $rsProp->Fetch()) {
		if(in_array($arProp['CODE'], $arParams['PROPERTY_CODE'])){

			if($arParams['NUMBER_TO_STRING'] && in_array($arProp['CODE'], $arParams['NUMBER_TO_STRING'])){
				$arProp['PROPERTY_TYPE'] = 'S';
			}

			if($arProp['PROPERTY_TYPE'] == 'N') {
				$arNumProps[ $arProp['CODE'] ] = $arProp;
			}

			if($arProp['PROPERTY_TYPE'] != 'F') {
				$arTemp = array(
					 'CODE'          => $arProp['CODE'],
					 'NAME'          => $arProp['NAME'],
					 'PROPERTY_TYPE' => $arProp['PROPERTY_TYPE'],
					 'USER_TYPE'     => $arProp['USER_TYPE'],
					 'MULTIPLE'      => $arProp['MULTIPLE'],
					 'HINT'          => $arProp['HINT'],
				);

				if($arProp['PROPERTY_TYPE'] == 'L')//Lists
				{
					$arrEnum = array();
					$rsEnum  = CIBlockProperty::GetPropertyEnum($arProp['ID'], array(
						 'SORT'  => 'ASC',
						 'VALUE' => 'ASC',
					));
					while($arEnum = $rsEnum->Fetch()) {
						$arrEnum[ $arEnum['ID'] ] = $arEnum['VALUE'];
					}
					$arTemp['VALUE_LIST'] = $arrEnum;
				}
				elseif($arProp['PROPERTY_TYPE'] == 'S')//Autocomplit for string values
				{
					//Date/Time iblock property
					if($arProp['USER_TYPE'] == 'DateTime') {
						//no actions
					}
					else
						//Property codes only for Autocomplite module
						$arAclCodes[] = $arProp['CODE'];
				}
				elseif($arProp['PROPERTY_TYPE'] == 'E')//Link to Elements
				{
					$arrEnum = array();
					if(!empty($arProp['LINK_IBLOCK_ID'])) {
						//EXECUTE ELEMENT
						$rsElement = CIBlockElement::GetList(
							 array('NAME' => 'ASC', 'SORT' => 'ASC'),
							 array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']),
							 false,
							 false,
							 array('ID', 'NAME')
						);
						while($arElement = $rsElement->Fetch()) {
							$arrEnum[ $arElement['ID'] ] = $arElement['NAME'];
						}
						unset($arElement);
					}
					$arTemp['VALUE_LIST'] = $arrEnum;
				}
				elseif($arProp['PROPERTY_TYPE'] == 'G') //Link to Sections
				{
					$arrEnum = array();
					if(!empty($arProp['LINK_IBLOCK_ID'])) {
						$rsSections               = CIBlockSection::GetList(
							 array('left_margin' => 'asc'),
							 array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']),
							 false,
							 array('ID', 'NAME', 'DEPTH_LEVEL')
						);
						$arResult["SECTION_LIST"] = array();
						while($arSection = $rsSections->Fetch()) {
							if($arParams['REMOVE_POINTS'])
								$arSection["NAME"] = $arSection["NAME"];
							else
								$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"];

							$arrEnum[ $arSection['ID'] ] = $arSection["NAME"];
						}
						unset($arSection);
					}
					$arTemp['VALUE_LIST'] = $arrEnum;
				}

				$arResult['arrProp'][ $arProp['ID'] ] = $arTemp;
			}
		}
	}

	//Get MAX & MIN values for properties type NUMBER
	if(!empty($arNumProps) && $arParams['INCLUDE_JQUERY_UI_SLIDER']) {
		foreach($arNumProps as $arNumProp) {
			$arSliderSelect = array('PROPERTY_' . $arNumProp['CODE']);
			$arSliderFilter = array('!PROPERTY_' . $arNumProp['CODE'] => false);

			$rsNumPropValues = CIBlockElement::GetList(
				 array('PROPERTY_' . $arNumProp['CODE'] => 'ASC'),
				 array_merge($arUIFilter, $arSliderFilter),
				 false,
				 array("nTopCount" => 1),
				 $arSliderSelect
			);
			while($arNumElement = $rsNumPropValues->Fetch()) {
				$arNumMaxMin[ $arNumProp['CODE'] ]['MIN'] = $arNumElement[ 'PROPERTY_' . ToUpper($arNumProp['CODE']) . '_VALUE' ];
			}

			$rsNumPropValues = CIBlockElement::GetList(
				 array('PROPERTY_' . $arNumProp['CODE'] => 'DESC'),
				 array_merge($arUIFilter, $arSliderFilter),
				 false,
				 array("nTopCount" => 1),
				 $arSliderSelect
			);
			while($arNumElement = $rsNumPropValues->Fetch()) {
				$arNumMaxMin[ $arNumProp['CODE'] ]['MAX'] = $arNumElement[ 'PROPERTY_' . ToUpper($arNumProp['CODE']) . '_VALUE' ];
			}
		}


		//include MAX and MIN values to cache
		$arResult['arNumMaxMin'] = $arNumMaxMin;
	}

	// offer properties
	$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
	if(is_array($arOffersIBlock)) {
		$rsProp = CIBlockProperty::GetList(Array(
			 'sort' => 'asc',
			 'name' => 'asc',
		), Array(
			 'ACTIVE'    => 'Y',
			 'IBLOCK_ID' => $arOffersIBlock['OFFERS_IBLOCK_ID'],
		));
		while($arProp = $rsProp->Fetch()) {
			if(in_array($arProp['CODE'], $arParams['OFFERS_PROPERTY_CODE']) && $arProp['PROPERTY_TYPE'] != 'F') {
				$arTemp = array(
					 'CODE'          => $arProp['CODE'],
					 'NAME'          => $arProp['NAME'],
					 'PROPERTY_TYPE' => $arProp['PROPERTY_TYPE'],
					 'MULTIPLE'      => $arProp['MULTIPLE'],
				);
				if($arProp['PROPERTY_TYPE'] == 'L') {
					$arrEnum = array();
					$rsEnum  = CIBlockProperty::GetPropertyEnum($arProp['ID']);
					while($arEnum = $rsEnum->Fetch()) {
						$arrEnum[ $arEnum['ID'] ] = $arEnum['VALUE'];
					}
					$arTemp['VALUE_LIST'] = $arrEnum;
				}
				$arResult['arrOfferProp'][ $arProp['ID'] ] = $arTemp;
			}
		}
	}


	//Autocomplite module
	if((!empty($arAclCodes) || in_array('NAME', $arParams['FIELD_CODE'])) && $arParams['IBLOCK_ID'] && $arParams['INCLUDE_AUTOCOMPLETE_PLUGIN']) {
		$arAclItems = array();

		$arAclSelect = array('ID', 'NAME');
		foreach($arAclCodes as $sAclCode)
			$arAclSelect[] = 'PROPERTY_' . $sAclCode;

		//Filter
		$arAclFilter = array(
			 'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			 'ACTIVE'    => 'Y',
		);
		if(!empty($arParams['SECTION_ID']))
			$arAclFilter['SECTION_ID'] = $arParams['SECTION_ID'];
		elseif(!empty($arParams['SECTION_CODE']))
			$arAclFilter['SECTION_CODE'] = $arParams['SECTION_CODE'];

		if(!empty($arParams['SECTION_ID']) || !empty($arParams['SECTION_CODE'])) {
			$arAclFilter['SECTION_ACTIVE']        = 'Y';
			$arAclFilter['SECTION_GLOBAL_ACTIVE'] = 'Y';
			$arAclFilter['INCLUDE_SUBSECTIONS']   = 'Y';
		}

		$res = CIBlockElement::GetList(
			 array(),
			 $arAclFilter,
			 false,
			 false,
			 $arAclSelect
		);
		while($ar_res = $res->Fetch())
			$arAclItems[] = $ar_res;


		$arPrepareAclItems = array();
		if(!empty($arAclItems)) {
			foreach($arAclItems as $arAclItem) {
				foreach($arAclCodes as $sAclPropCode) {
					$curVal = $arAclItem[ 'PROPERTY_' . ToUpper($sAclPropCode) . '_VALUE' ];

					//For multiple rows properties
					if(!empty($curVal))
						$arPrepareAclItems[ ToUpper($sAclPropCode) ][] = str_replace("\r\n", ' ', $curVal);
				}

				if(!empty($arAclItem['NAME']) && in_array('NAME', $arParams['FIELD_CODE']))
					$arPrepareAclItems['NAME'][] = $arAclItem['NAME'];
			}


			if(!empty($arPrepareAclItems)) {
				foreach($arPrepareAclItems as $sCode => $v) {
					//Only unique values
					$arPrepareAclItems[ $sCode ] = array_unique($arPrepareAclItems[ $sCode ], SORT_REGULAR);

					//Only after unique fn we can sort values, else autocomplite script return JSON Error,
					//because array keys wrong
					sort($arPrepareAclItems[ $sCode ]);

					$arResult['AutocompleteInitJS'] .= '
                        var ' . $sCode . '_acl = ' . CUtil::PhpToJSObject($arPrepareAclItems[ $sCode ], false, true) . ';
                        $("#' . $sCode . '_acl").autocomplete({
                            lookup: ' . $sCode . '_acl,
                            minChars:0,
                            noCache: false,
                            minLength: 1,
                            triggerSelectOnValidInput: false,
                            //delimiter: /(,|;)\s*/,
                        });
                    ';
				}

				unset($arPrepareAclItems);
				unset($ar_res);
				unset($res);
				unset($v);
				unset($arAclItem);
				unset($arAclItems);
			}
		}
	}


	$this->EndResultCache();
}
$arResult['FORM_ACTION'] = (isset($_SERVER['REQUEST_URI']) && @strpos($_SERVER['REQUEST_URI'], $arParams['REDIRECT_FOLDER']) !== false) ? htmlspecialcharsbx($_SERVER['REQUEST_URI']) : $arParams['REDIRECT_FOLDER'];
$arResult['FILTER_NAME'] = $FILTER_NAME;

/*************************************************************************
 * Adding the titles and input fields
 *************************************************************************/
$arResult['arrInputNames'] = array(); // array of the input field names; is being used in the function $APPLICATION->GetCurPageParam
// simple fields
$arResult['ITEMS'] = array();
foreach($arParams['FIELD_CODE'] as $field_code) {
	$arResult['arrInputNames'][ $FILTER_NAME . '_ff' ] = true;

	$field_res = '';
	$name      = $FILTER_NAME . '_ff[' . $field_code . ']';
	$value     = $arrFFV[ $field_code ];
	$title     = $arResult['IBLOCK_FIELD'][ $field_code ];

	switch($field_code) {
		case 'CODE':
		case 'XML_ID':
		case 'NAME':
		case 'PREVIEW_TEXT':
		case 'DETAIL_TEXT':
		case 'IBLOCK_TYPE_ID':
		case 'IBLOCK_ID':
		case 'IBLOCK_CODE':
		case 'IBLOCK_NAME':
		case 'IBLOCK_EXTERNAL_ID':
		case 'SEARCHABLE_CONTENT':
		case 'TAGS':
			if(!is_array($value)) {
				$field_res = '<input id="' . $field_code . '_acl" class="autocomplite"  type="text" name="' . $name . '" style="' . $arParams['TEXT_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $title . '"' : '') . ' />';

				if(strlen($value) > 0) {
					if($field_code == 'NAME')
						${$FILTER_NAME}[ '?' . $field_code ] = CAPISearchFilter::getWords($value);
					else
						${$FILTER_NAME}[ '?' . $field_code ] = $value;
				}
			}
			break;
		case 'ID':
		case 'SORT':
		case 'SHOW_COUNTER':
			$name = $FILTER_NAME . '_ff[' . $field_code . '][LEFT]';
			if(is_array($value) && isset($value['LEFT']))
				$value_left = $value['LEFT'];
			else
				$value_left = '';

			$field_res = '<span class="ts-ot-do">';
			$field_res .= '<input type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value_left) . '" ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $title . GetMessage('PH_OT') . '"' : '') . ' />&nbsp;' . GetMessage('CC_BCF_TILL') . '&nbsp;';
			if(strlen($value_left) > 0)
				${$FILTER_NAME}[ '>=' . $field_code ] = intval($value_left);
			$name = $FILTER_NAME . '_ff[' . $field_code . '][RIGHT]';
			if(is_array($value) && isset($value['RIGHT']))
				$value_right = $value['RIGHT'];
			else
				$value_right = '';
			$field_res .= '<input type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value_right) . '"  ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $title . GetMessage('PH_DO') . '"' : '') . ' />';
			if(strlen($value_right) > 0)
				${$FILTER_NAME}[ '<=' . $field_code ] = intval($value_right);

			$field_res .= '</span>';
			break;
		case 'SECTION_ID':
			$arrRef = array(
				 'reference'    => array_values($arResult['arrSection']),
				 'reference_id' => array_keys($arResult['arrSection']),
			);

			$strDetText = '';
			if($arParams['INCLUDE_CHOSEN_PLUGIN'])
				$strDetText = ' ';

			$field_res = SelectBoxFromArray($name, $arrRef, $value, $strDetText, 'data-placeholder="' . $arParams['SECTIONS_FIELD_TITLE'] . '" class="chosen-select chosen-select-deselect" style="' . $arParams['SELECT_WIDTH'] . '" ');
			if(!is_array($value) && $value != 'NOT_REF' && strlen($value) > 0)
				${$FILTER_NAME}[ $field_code ] = intval($value);

			//$_name  = $FILTER_NAME . '_ff[INCLUDE_SUBSECTIONS]';
			//$_value = $arrFFV['INCLUDE_SUBSECTIONS'];

			//$field_res .= '<br>' . InputType('checkbox', $_name, 'Y', $_value, false, '', '') . '&nbsp;' . GetMessage('CC_BCF_INCLUDE_SUBSECTIONS');
			if(${$FILTER_NAME}[ $field_code ])
				${$FILTER_NAME}['INCLUDE_SUBSECTIONS'] = 'Y';
			else
				unset(${$FILTER_NAME}[ $field_code ]);

			break;
		case 'ACTIVE_DATE':
		case 'DATE_ACTIVE_FROM':
		case 'DATE_ACTIVE_TO':
		case 'DATE_CREATE':
			$arDateField                                                       = $arrDFV[ $field_code ];
			$arResult['arrInputNames'][ $arDateField['from']['name'] ]         = true;
			$arResult['arrInputNames'][ $arDateField['to']['name'] ]           = true;
			$arResult['arrInputNames'][ $arDateField['days_to_back']['name'] ] = true;
			ob_start();
			$APPLICATION->IncludeComponent('bitrix:main.calendar', '', array(
				 'FORM_NAME'             => $FILTER_NAME . '_form',
				 'SHOW_INPUT'            => 'Y',
				 'INPUT_NAME'            => $arDateField['from']['name'],
				 'INPUT_VALUE'           => $arDateField['from']['value'],
				 'INPUT_NAME_FINISH'     => $arDateField['to']['name'],
				 'INPUT_VALUE_FINISH'    => $arDateField['to']['value'],
				 'INPUT_ADDITIONAL_ATTR' => 'size="10" class="inputselect inputfield"',
			), null, array('HIDE_ICONS' => 'Y'));
			$field_res = ob_get_contents();
			ob_end_clean();
			if(strlen($arDateField['from']['value']) > 0)
				${$FILTER_NAME}[ $arDateField['filter_from'] ] = $arDateField['from']['value'];
			if(strlen($arDateField['to']['value']) > 0)
				${$FILTER_NAME}[ $arDateField['filter_to'] ] = $arDateField['to']['value'];
			break;
	}
	if($field_res) {
		$arResult['ITEMS'][ $field_code ] = array(
			 'NAME'         => htmlspecialcharsbx(GetMessage('IBLOCK_FIELD_' . $field_code)),
			 'INPUT'        => $field_res,
			 'INPUT_NAME'   => $name,
			 'INPUT_VALUE'  => is_array($value) ? array_map('htmlspecialcharsbx', $value) : htmlspecialcharsbx($value),
			 '~INPUT_VALUE' => $value,
		);
	}
}

foreach($arResult['arrProp'] as $prop_id => $arProp) {

	$res                                               = '';
	$name                                              = '';
	$value                                             = '';
	$arResult['arrInputNames'][ $FILTER_NAME . '_pf' ] = true;

	switch($arProp['PROPERTY_TYPE']) {
		case 'E':
		case 'L':
		case 'G':
			$name  = $FILTER_NAME . '_pf[' . $arProp['CODE'] . ']';
			$value = $arrPFV[ $arProp['CODE'] ];

			//SELECT OR CHECKBOX in params
			if(in_array($arProp['CODE'], $arParams['SELECT_IN_CHECKBOX'])) {
				$res .= '<span class="ts-select-in-checkbox">';
				foreach($arProp['VALUE_LIST'] as $key => $val) {
					$res .= '<label for="' . $arProp['CODE'] . '_' . $key . '"';
					$arParams['CHECKBOX_NEW_STRING'] ? $res .= ' style="display:block;">' : $res .= '>';

					$res .= '<input type="checkbox" name="' . $name . '[]" id="' . $arProp['CODE'] . '_' . $key . '"';

					if(is_array($value)) {
						if(in_array($key, $value))
							$res .= ' checked="checked"';
					}
					else {
						if($key == $value)
							$res .= ' checked="checked"';
					}
					$res .= ' value="' . htmlspecialcharsbx($key) . '"> ' . htmlspecialcharsbx($val) . '</label>';
				}
				$res .= '</span>';
			}
			else {
				if($arProp['MULTIPLE'] == 'Y')
					$res .= '<select multiple name="' . $name . '[]" size="' . $arParams['LIST_HEIGHT'] . '" class="chosen-select chosen-select-deselect" data-placeholder="' . $arProp['NAME'] . '" style="' . $arParams['SELECT_WIDTH'] . '">';
				else
					$res .= '<select name="' . $name . '" class="chosen-select chosen-select-deselect" data-placeholder="' . $arProp['NAME'] . '" style="' . $arParams['SELECT_WIDTH'] . '">';

				$res .= '<option value="">' . $labelAll . '</option>';

				foreach($arProp['VALUE_LIST'] as $key => $val) {
					$res .= '<option';
					if(($arProp['MULTIPLE'] == 'Y') && is_array($value)) {
						if(in_array($key, $value))
							$res .= ' selected';
					}
					else {
						if($key == $value)
							$res .= ' selected';
					}
					$res .= ' value="' . htmlspecialcharsbx($key) . '">' . htmlspecialcharsbx($val) . '</option>';
				}
				$res .= '</select>';
			}

			if(is_array($value) && count($value) > 0)
				${$FILTER_NAME}['PROPERTY'][ $arProp['CODE'] ] = $value;
			elseif(!is_array($value) && strlen($value) > 0)
				${$FILTER_NAME}['PROPERTY'][ $arProp['CODE'] ] = $value;
			break;
		case 'N':

			$value = $arrPFV[ $arProp['CODE'] ];
			$name  = $FILTER_NAME . '_pf[' . $arProp['CODE'] . '][LEFT]';

			if(is_array($value) && isset($value['LEFT']))
				$value_left = $value['LEFT'];
			else
				$value_left = '';

			if(is_array($value) && isset($value['RIGHT']))
				$value_right = $value['RIGHT'];
			else
				$value_right = '';

			//if isset del_filter then we must replace values to default max and min
			$min_value = !empty($value_left) ? intval($value_left) : intval($arResult['arNumMaxMin'][ $arProp['CODE'] ]['MIN']);
			$max_value = !empty($value_right) ? intval($value_right) : intval($arResult['arNumMaxMin'][ $arProp['CODE'] ]['MAX']);

			$min_value_placeholder = strlen($arProp['HINT']) ? $arProp['HINT'] : $min_value;
			$max_value_placeholder = strlen($arProp['HINT']) ? $arProp['HINT'] : $max_value;

			$res .= '<span class="ts-ot-do">';
			$res .= '<input type="text" id="' . $arProp['CODE'] . '_LEFT"  name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value_left) . '" ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $min_value_placeholder . '"' : '') . '  />&nbsp;' . GetMessage('CC_BCF_TILL') . '&nbsp;';

			if(strlen($value_left) > 0)
				${$FILTER_NAME}['PROPERTY'][ '>=' . $arProp['CODE'] ] = doubleval($value_left);
			$name = $FILTER_NAME . '_pf[' . $arProp['CODE'] . '][RIGHT]';

			$res .= '<input type="text" id="' . $arProp['CODE'] . '_RIGHT" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value_right) . '"  ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $max_value_placeholder . '"' : '') . ' />';

			//Include range slider for property
			if($arParams['INCLUDE_JQUERY_UI_SLIDER']) {
				//include range-slider to input
				$res .= '<div id="' . $arProp['CODE'] . '_RANGE"></div>';

				//It's work when user change input values
				$ui_tooltip = $ui_tooltip_slide_function = '';
				if($arParams['INCLUDE_JQUERY_UI_SLIDER_TOOLTIP']) {
					$ui_tooltip_slide_function = '
						$("#' . $arProp['CODE'] . '_RANGE .ui-slider-handle:first .ui-tooltip span").html(ui.values[0]);
						$("#' . $arProp['CODE'] . '_RANGE .ui-slider-handle:last .ui-tooltip span").html(ui.values[1]);
					';

					$ui_tooltip = '
					    $("#' . $arProp['CODE'] . '_RANGE .ui-slider-handle").html(\'<div class="ui-tooltip"><span></span></div>\');
			            $("#' . $arProp['CODE'] . '_RANGE .ui-slider-handle:first .ui-tooltip span").html(' . intval($min_value) . ');
			            $("#' . $arProp['CODE'] . '_RANGE .ui-slider-handle:last .ui-tooltip span").html(' . intval($max_value) . ');
					';
				}

				$slider_range_all .= '
					$("#' . $arProp['CODE'] . '_RANGE").slider({
					    range: true,
					    min: ' . intval($arResult['arNumMaxMin'][ $arProp['CODE'] ]['MIN']) . ',
		                max: ' . intval($arResult['arNumMaxMin'][ $arProp['CODE'] ]['MAX']) . ',
				        values: [ ' . intval($min_value) . ', ' . intval($max_value) . ' ],
				        slide: function( event, ui ) {
				            $("#' . $arProp['CODE'] . '_LEFT").val(ui.values[ 0 ]);
				            $("#' . $arProp['CODE'] . '_RIGHT").val(ui.values[ 1 ]);
				            ' . $ui_tooltip_slide_function . '
				        }
				    });
                    ' . $ui_tooltip . '
				    ' . "\n";
			}

			$res .= '</span>';

			if(strlen($value_right) > 0)
				${$FILTER_NAME}['PROPERTY'][ '<=' . $arProp['CODE'] ] = doubleval($value_right);
			break;

		case 'S':

			if($arProp['USER_TYPE'] == 'DateTime') {
				$name  = $FILTER_NAME . '_pf[' . $arProp['CODE'] . '][LEFT]';
				$value = $arrPFV[ $arProp['CODE'] ];

				if(is_array($value) && isset($value['LEFT']))
					$value_left = $value['LEFT'];
				else
					$value_left = '';

				if(is_array($value) && isset($value['RIGHT']))
					$value_right = $value['RIGHT'];
				else
					$value_right = '';

				//if isset del_filter then we must replace values to default max and min
				$min_value = !empty($value_left) ? trim($value_left) : '';
				$max_value = !empty($value_right) ? trim($value_right) : '';

				$min_value_placeholder = strlen($arProp['HINT']) ? $arProp['HINT'] : $min_value;
				$max_value_placeholder = strlen($arProp['HINT']) ? $arProp['HINT'] : $max_value;

				$res .= '<span class="ts-ot-do" style="' . $arParams['TEXT_WIDTH'] . '">';

				//$res .= '<input type="text" id="'. $arProp['CODE'] .'_LEFT"  name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value_left) . '" '. ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="'. $min_value_placeholder .'"':  '') . '  />&nbsp;' . GetMessage('CC_BCF_TILL') . '&nbsp;';
				$res .= '<span class="ts-ot">';
				ob_start();
				$APPLICATION->IncludeComponent('bitrix:main.calendar', '', array(
					 'FORM_NAME'             => $FILTER_NAME . '_form',
					 'SHOW_INPUT'            => 'Y',
					 'INPUT_NAME'            => $name,
					 'INPUT_VALUE'           => $min_value,
					 'INPUT_NAME_FINISH'     => '',
					 'INPUT_VALUE_FINISH'    => '',
					 'INPUT_ADDITIONAL_ATTR' => 'size="10" class="ts-datetime"' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $min_value_placeholder . '"' : ''),
					 //'SHOW_TIME'             => "Y",
					 //'HIDE_TIMEBAR'          => "Y",
				),
					 null,
					 array('HIDE_ICONS' => 'Y')
				);
				$res .= ob_get_contents();
				ob_end_clean();
				$res .= '</span>';

				$res .= '<span class="ts-po">&nbsp;' . trim(GetMessage('PH_DO')) . '&nbsp;</span>';

				if(strlen($value_left) > 0)
					${$FILTER_NAME}['PROPERTY'][ '>=' . $arProp['CODE'] ] = date('Y-m-d H:i:s', MakeTimeStamp($value_left));

				$name = $FILTER_NAME . '_pf[' . $arProp['CODE'] . '][RIGHT]';


				//$res .= '<input type="text" id="'. $arProp['CODE'] .'_RIGHT" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value_right) . '"  '. ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="'. $max_value_placeholder .'"':  '') . ' />';
				$res .= '<span class="ts-do">';
				ob_start();
				$APPLICATION->IncludeComponent('bitrix:main.calendar', '', array(
					 'FORM_NAME'             => $FILTER_NAME . '_form',
					 'SHOW_INPUT'            => 'Y',
					 'INPUT_NAME'            => $name,
					 'INPUT_VALUE'           => $value_right,
					 'INPUT_NAME_FINISH'     => '',
					 'INPUT_VALUE_FINISH'    => '',
					 'INPUT_ADDITIONAL_ATTR' => 'size="10" class="ts-datetime" ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $max_value_placeholder . '"' : ''),
					 //'SHOW_TIME'             => "Y",
					 //'HIDE_TIMEBAR'          => "Y",
				),
					 null,
					 array('HIDE_ICONS' => 'Y')
				);
				$res .= ob_get_contents();
				ob_end_clean();
				$res .= '</span>';


				$res .= '</span>';

				if(strlen($value_right) > 0)
					${$FILTER_NAME}['PROPERTY'][ '<=' . $arProp['CODE'] ] = date('Y-m-d H:i:s', MakeTimeStamp($value_right));
			}
			else {
				$name  = $FILTER_NAME . '_pf[' . $arProp['CODE'] . ']';
				$value = $arrPFV[ $arProp['CODE'] ];

				if(!is_array($value)) {
					$res .= '<input id="' . $arProp['CODE'] . '_acl" class="autocomplite" type="text" name="' . $name . '" style="' . $arParams['TEXT_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $arProp['NAME'] . '"' : '') . ' />';

					if(strlen($value) > 0){
						if($arParams['NUMBER_TO_STRING'] && in_array($arProp['CODE'], $arParams['NUMBER_TO_STRING'])){
							${$FILTER_NAME}['PROPERTY'][ '=' . $arProp['CODE'] ] = $value;
						}
						else{
							${$FILTER_NAME}['PROPERTY'][ '?' . $arProp['CODE'] ] = CAPISearchFilter::getWords($value);
						}
					}
				}
			}

			break;
	}
	if($res) {
		$arResult['ITEMS'][ 'PROPERTY_' . $prop_id ] = array(
			 'NAME'         => htmlspecialcharsbx($arProp['NAME']),
			 'INPUT'        => $res,
			 'INPUT_NAME'   => $name,
			 'INPUT_VALUE'  => is_array($value) ? array_map('htmlspecialcharsbx', $value) : htmlspecialcharsbx($value),
			 '~INPUT_VALUE' => $value,
		);
	}
}

$bHasOffersFilter = false;
foreach($arParams['OFFERS_FIELD_CODE'] as $field_code) {
	$field_res                                         = '';
	$arResult['arrInputNames'][ $FILTER_NAME . '_of' ] = true;
	$name                                              = $FILTER_NAME . '_of[' . $field_code . ']';
	$value                                             = $arrOFV[ $field_code ];
	switch($field_code) {
		case 'CODE':
		case 'XML_ID':
		case 'NAME':
		case 'PREVIEW_TEXT':
		case 'DETAIL_TEXT':
		case 'IBLOCK_TYPE_ID':
		case 'IBLOCK_ID':
		case 'IBLOCK_CODE':
		case 'IBLOCK_NAME':
		case 'IBLOCK_EXTERNAL_ID':
		case 'SEARCHABLE_CONTENT':
			$field_res = '<input type="text" name="' . $name . '" style="' . $arParams['TEXT_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" />';
			if(strlen($value) > 0)
				${$FILTER_NAME}['OFFERS'][ '?' . $field_code ] = $value;
			break;
		case 'ID':
		case 'SORT':
		case 'SHOW_COUNTER':
			$name      = $FILTER_NAME . '_of[' . $field_code . '][LEFT]';
			$value     = $arrOFV[ $field_code ]['LEFT'];
			$field_res = '<input type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" />&nbsp;' . GetMessage('CC_BCF_TILL') . '&nbsp;';
			if(strlen($value) > 0)
				${$FILTER_NAME}['OFFERS'][ '>=' . $field_code ] = intval($value);
			$name      = $FILTER_NAME . '_of[' . $field_code . '][RIGHT]';
			$value     = $arrOFV[ $field_code ]['RIGHT'];
			$field_res .= '<input type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" />';
			if(strlen($value) > 0)
				${$FILTER_NAME}['OFFERS'][ '<=' . $field_code ] = intval($value);
			break;
		case 'ACTIVE_DATE':
		case 'DATE_ACTIVE_FROM':
		case 'DATE_ACTIVE_TO':
		case 'DATE_CREATE':
			$arDateField                                                       = $arrODFV[ $field_code ];
			$arResult['arrInputNames'][ $arDateField['from']['name'] ]         = true;
			$arResult['arrInputNames'][ $arDateField['to']['name'] ]           = true;
			$arResult['arrInputNames'][ $arDateField['days_to_back']['name'] ] = true;
			ob_start();
			$APPLICATION->IncludeComponent('bitrix:main.calendar', '', array(
				 'FORM_NAME'             => $FILTER_NAME . '_form',
				 'SHOW_INPUT'            => 'Y',
				 'INPUT_NAME'            => $arDateField['from']['name'],
				 'INPUT_VALUE'           => $arDateField['from']['value'],
				 'INPUT_NAME_FINISH'     => $arDateField['to']['name'],
				 'INPUT_VALUE_FINISH'    => $arDateField['to']['value'],
				 'INPUT_ADDITIONAL_ATTR' => 'size="10" class="inputselect inputfield"',
			), null, array('HIDE_ICONS' => 'Y'));
			$field_res = ob_get_contents();
			ob_end_clean();
			if(strlen($arDateField['from']['value']) > 0)
				${$FILTER_NAME}['OFFERS'][ $arDateField['filter_from'] ] = $arDateField['from']['value'];
			if(strlen($arDateField['to']['value']) > 0)
				${$FILTER_NAME}['OFFERS'][ $arDateField['filter_to'] ] = $arDateField['to']['value'];
			break;
	}
	if($field_res) {
		$bHasOffersFilter                            = true;
		$arResult['ITEMS'][ 'OFFER_' . $field_code ] = array(
			 'NAME'         => htmlspecialcharsbx(GetMessage('IBLOCK_FIELD_' . $field_code)),
			 'INPUT'        => $field_res,
			 'INPUT_NAME'   => $name,
			 'INPUT_VALUE'  => htmlspecialcharsbx($value),
			 '~INPUT_VALUE' => $value,
		);
	}
}
foreach($arResult['arrOfferProp'] as $prop_id => $arProp) {
	$res                                               = '';
	$name                                              = '';
	$value                                             = '';
	$arResult['arrInputNames'][ $FILTER_NAME . '_op' ] = true;
	switch($arProp['PROPERTY_TYPE']) {
		case 'L':
			$name  = $FILTER_NAME . '_op[' . $arProp['CODE'] . ']';
			$value = $arrOPFV[ $arProp['CODE'] ];
			if($arProp['MULTIPLE'] == 'Y')
				$res .= '<select multiple name="' . $name . '[]" size="' . $arParams['LIST_HEIGHT'] . '" class="chosen-select    chosen-select-deselect" data-placeholder="' . $arProp['NAME'] . '" style="' . $arParams['SELECT_WIDTH'] . '">';
			else
				$res .= '<select name="' . $name . '" class="chosen-select chosen-select-deselect" data-placeholder="' . $arProp['NAME'] . '" style="' . $arParams['SELECT_WIDTH'] . '">';
			$res .= '<option value="">' . $labelAll . '</option>';
			foreach($arProp['VALUE_LIST'] as $key => $val) {
				$res .= '<option';
				if(($arProp['MULTIPLE'] == 'Y') && is_array($value)) {
					if(in_array($key, $value))
						$res .= ' selected';
				}
				else {
					if($key == $value)
						$res .= ' selected';
				}
				$res .= ' value="' . htmlspecialcharsbx($key) . '">' . htmlspecialcharsbx($val) . '</option>';
			}
			$res .= '</select>';
			if($arProp['MULTIPLE'] == 'Y') {
				if(is_array($value) && count($value) > 0)
					${$FILTER_NAME}['OFFERS']['PROPERTY'][ $arProp['CODE'] ] = $value;
			}
			else {
				if(strlen($value) > 0)
					${$FILTER_NAME}['OFFERS']['PROPERTY'][ $arProp['CODE'] ] = $value;
			}
			break;
		case 'N':
			$name  = $FILTER_NAME . '_op[' . $arProp['CODE'] . '][LEFT]';
			$value = $arrOPFV[ $arProp['CODE'] ]['LEFT'];
			$res   .= '<input type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" />&nbsp;' . GetMessage('CC_BCF_TILL') . '&nbsp;';
			if(strlen($value) > 0)
				${$FILTER_NAME}['OFFERS']['PROPERTY'][ '>=' . $arProp['CODE'] ] = intval($value);
			$name  = $FILTER_NAME . '_op[' . $arProp['CODE'] . '][RIGHT]';
			$value = $arrOPFV[ $arProp['CODE'] ]['RIGHT'];
			$res   .= '<input type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" />';
			if(strlen($value) > 0)
				${$FILTER_NAME}['OFFERS']['PROPERTY'][ '<=' . $arProp['CODE'] ] = doubleval($value);
			break;
		case 'S':
		case 'E':
		case 'G':
			$name  = $FILTER_NAME . '_op[' . $arProp['CODE'] . ']';
			$value = $arrOPFV[ $arProp['CODE'] ];
			$res   .= '<input type="text" name="' . $name . '" style="' . $arParams['TEXT_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '" />';
			if(strlen($value) > 0)
				${$FILTER_NAME}['OFFERS']['PROPERTY'][ '?' . $arProp['CODE'] ] = $value;
			break;
	}
	if($res) {
		$bHasOffersFilter                                  = true;
		$arResult['ITEMS'][ 'OFFER_PROPERTY_' . $prop_id ] = array(
			 'NAME'         => htmlspecialcharsbx($arProp['NAME']),
			 'INPUT'        => $res,
			 'INPUT_NAME'   => $name,
			 'INPUT_VALUE'  => htmlspecialcharsbx($value),
			 '~INPUT_VALUE' => $value,
		);
	}
}
if($bHasOffersFilter) {
	//This will force to use catalog.section offers price filter
	if(!isset(${$FILTER_NAME}['OFFERS']))
		${$FILTER_NAME}['OFFERS'] = array();
}


foreach($arResult['arrPrice'] as $price_code => $arPrice) {
	$arResult['arrInputNames'][ $FILTER_NAME . '_cf' ] = true;

	$res_price = '';
	$name      = $FILTER_NAME . '_cf[' . $arPrice['ID'] . '][LEFT]';
	$value     = $arrCFV[ $arPrice['ID'] ]['LEFT'];
	if(strlen($value) > 0) {
		if($MOD_CATALOG)
			${$FILTER_NAME}[ '>=CATALOG_PRICE_' . $arPrice['ID'] ] = $value;
		else
			${$FILTER_NAME}[ '>=PROPERTY_' . $arPrice['ID'] ] = $value;
	}
	$value_left_price = $value;

	$min_value = intval($arResult['arPriceMaxMin'][ $price_code ]['MIN']);
	$max_value = intval($arResult['arPriceMaxMin'][ $price_code ]['MAX']);

	$res_price .= '<span class="ts-ot-do">';
	$res_price .= '<input  id="PRICE_' . $price_code . '_LEFT"  type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '"  ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $min_value . '"' : '') . ' />&nbsp;' . GetMessage('CC_BCF_TILL') . '&nbsp;';
	$name      = $FILTER_NAME . '_cf[' . $arPrice['ID'] . '][RIGHT]';


	$value = $arrCFV[ $arPrice['ID'] ]['RIGHT'];
	if(strlen($value) > 0) {
		if($MOD_CATALOG)
			${$FILTER_NAME}[ '<=CATALOG_PRICE_' . $arPrice['ID'] ] = $value;
		else
			${$FILTER_NAME}[ '<=PROPERTY_' . $arPrice['ID'] ] = $value;
	}
	$value_right_price = $value;

	$res_price .= '<input id="PRICE_' . $price_code . '_RIGHT" type="text" name="' . $name . '" style="' . $arParams['NUMBER_WIDTH'] . '" value="' . htmlspecialcharsbx($value) . '"  ' . ($arParams['INCLUDE_PLACEHOLDER'] ? ' placeholder="' . $max_value . '"' : '') . '  />';

	//Include range slider for prices
	if($arParams['INCLUDE_JQUERY_UI_SLIDER']) {
		//include range-slider to input
		$res_price .= '<div id="PRICE_' . $price_code . '_RANGE"></div>';

		//if isset del_filter then we must replace values to default max and min
		$min_price_value = !empty($value_left_price) ? intval($value_left_price) : $min_value;
		$max_price_value = !empty($value_right_price) ? intval($value_right_price) : $max_value;

		//It's work when user change input values
		$ui_tooltip = $ui_tooltip_slide_function = '';
		if($arParams['INCLUDE_JQUERY_UI_SLIDER_TOOLTIP']) {
			$ui_tooltip_slide_function = '
				$("#PRICE_' . $price_code . '_RANGE .ui-slider-handle:first .ui-tooltip span").html(ui.values[0]);
				$("#PRICE_' . $price_code . '_RANGE .ui-slider-handle:last .ui-tooltip span").html(ui.values[1]);
			';

			$ui_tooltip = '
			    $("#PRICE_' . $price_code . '_RANGE .ui-slider-handle").html(\'<div class="ui-tooltip"><span></span></div>\');
	            $("#PRICE_' . $price_code . '_RANGE .ui-slider-handle:first .ui-tooltip span").html(' . $min_price_value . ');
	            $("#PRICE_' . $price_code . '_RANGE .ui-slider-handle:last .ui-tooltip span").html(' . $max_price_value . ');
			';
		}

		$slider_range_price_all .= '
		            $("#PRICE_' . $price_code . '_RANGE").slider({
					    range: true,
                        min: ' . $min_value . ',
				        max: ' . $max_value . ',
				        values: [ ' . $min_price_value . ', ' . $max_price_value . ' ],
				        slide: function( event, ui ) {
				            $("#PRICE_' . $price_code . '_LEFT").val(ui.values[0]);
				            $("#PRICE_' . $price_code . '_RIGHT").val(ui.values[1]);
							' . $ui_tooltip_slide_function . '
			            }
				    });
				    ' . $ui_tooltip . '
				    ' . "\n";
	}
	$res_price .= '</span>';

	$arResult['ITEMS'][ 'PRICE_' . $price_code ] = array(
		 'NAME'  => htmlspecialcharsbx($arPrice['TITLE']),
		 'INPUT' => $res_price,
	);
}
$arResult['arrInputNames']['set_filter'] = true;
$arResult['arrInputNames']['del_filter'] = true;
$arSkip                                  = array(
	 "AUTH_FORM"             => true,
	 "TYPE"                  => true,
	 "USER_LOGIN"            => true,
	 "USER_CHECKWORD"        => true,
	 "USER_PASSWORD"         => true,
	 "USER_CONFIRM_PASSWORD" => true,
	 "USER_EMAIL"            => true,
	 "captcha_word"          => true,
	 "captcha_sid"           => true,
	 "login"                 => true,
	 "Login"                 => true,
	 "backurl"               => true,
);

foreach(array_merge($_GET, $_POST) as $key => $value) {
	if(!array_key_exists($key, $arResult["arrInputNames"]) && !array_key_exists($key, $arSkip) && is_string($value)) {
		$arResult["ITEMS"][ "HIDDEN_" . htmlspecialcharsEx($key) ] = array(
			 "HIDDEN" => true,
			 "INPUT"  => '<input type="hidden" name="' . htmlspecialcharsbx($key) . '" value="' . htmlspecialcharsbx($value) . '" />',
		);
	}
}

if(!empty($slider_range_all) && $arParams['INCLUDE_JQUERY_UI_SLIDER']) {
	$slider_range_replace_script = '<script type="text/javascript">
										jQuery(document).ready(function($){
											' . $slider_range_all . '
										}); //END Ready
									</script>';

	$APPLICATION->AddHeadString($slider_range_replace_script);
}

if(!empty($slider_range_price_all) && $arParams['INCLUDE_JQUERY_UI_SLIDER']) {
	$slider_range_price_replace_script = '<script type="text/javascript">
											jQuery(document).ready(function($){
												' . $slider_range_price_all . '
											}); //END Ready
										</script>';

	$APPLICATION->AddHeadString($slider_range_price_replace_script);
}


//Autocomplite module
if(!empty($arResult['AutocompleteInitJS'])) {
	$sAutocompleteInitCSS = "
	<style>
		.autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
		.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
		.autocomplete-selected { background: #F0F0F0; }
		.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
	</style>
	";

	$sAutocompleteInitJS = "
	<script>
        $(function(){
            " . $arResult['AutocompleteInitJS'] . "
        });
	</script>
	";
	$sAutocompleteInit   = $sAutocompleteInitCSS . $sAutocompleteInitJS;
	$APPLICATION->AddHeadString($sAutocompleteInit);
}

$this->IncludeComponentTemplate();