<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) )
{
	$ELEMENT_ID = IntVal($_REQUEST['element_id']);
	if($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['action']=='rsgppopup' && $ELEMENT_ID>0)
	{
		// +++++++++++++++++++++++++++++++ get element popup +++++++++++++++++++++++++++++++ //
		global $APPLICATION;
		$APPLICATION->RestartBuffer();
		if($ELEMENT_ID<1)
		{
			$arJson = array( 'TYPE' => 'ERROR', 'MESSAGE' => 'Element id is empty' );
			echo json_encode($arJson);
			die();
		}
		$ElementID = $APPLICATION->IncludeComponent(
			'bitrix:catalog.element',
			'popup',
			array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
				'META_KEYWORDS' => $arParams['DETAIL_META_KEYWORDS'],
				'META_DESCRIPTION' => $arParams['DETAIL_META_DESCRIPTION'],
				'BROWSER_TITLE' => $arParams['DETAIL_BROWSER_TITLE'],
				'BASKET_URL' => $arParams['BASKET_URL'],
				'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
				'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
				'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
				'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
				'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'SET_TITLE' => $arParams['SET_TITLE'],
				'SET_STATUS_404' => $arParams['SET_STATUS_404'],
				'PRICE_CODE' => $arParams['PRICE_CODE'],
				'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
				'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
				'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
				'PRICE_VAT_SHOW_VALUE' => $arParams['PRICE_VAT_SHOW_VALUE'],
				'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
				'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
				'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
				'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
				'LINK_IBLOCK_TYPE' => $arParams['LINK_IBLOCK_TYPE'],
				'LINK_IBLOCK_ID' => $arParams['LINK_IBLOCK_ID'],
				'LINK_PROPERTY_SID' => $arParams['LINK_PROPERTY_SID'],
				'LINK_ELEMENTS_URL' => $arParams['LINK_ELEMENTS_URL'],
				'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
				'OFFERS_FIELD_CODE' => $arParams['DETAIL_OFFERS_FIELD_CODE'],
				'OFFERS_PROPERTY_CODE' => $arParams['DETAIL_OFFERS_PROPERTY_CODE'],
				'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
				'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
				'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
				'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
				'ELEMENT_ID' => $ELEMENT_ID,
				'ELEMENT_CODE' => '',
				'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
				'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
				'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
				'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
				'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
				'CURRENCY_ID' => $arParams['CURRENCY_ID'],
				'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
				'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
				// fix
				'USE_COMPARE' => $arParams['USE_COMPARE'],
				// goPro params
				'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
				'PROP_ARTICLE' => $arParams['PROP_ARTICLE'],
				'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
				'USE_FAVORITE' => $arParams['USE_FAVORITE'],
				'USE_SHARE' => $arParams['USE_SHARE'],
				'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
				'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
				'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
				'PROPS_ATTRIBUTES_COLOR' => $arParams['PROPS_ATTRIBUTES_COLOR'],
				// store
				'USE_STORE' => $arParams['USE_STORE'],
				'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
				'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
				'MAIN_TITLE' => $arParams['MAIN_TITLE'],
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
		die();
	} elseif($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['action']=='get_element_json' && $ELEMENT_ID>0)
	{
		// +++++++++++++++++++++++++++++++ get element json +++++++++++++++++++++++++++++++ //
		global $APPLICATION,$JSON;
		$APPLICATION->RestartBuffer();
		if($ELEMENT_ID<1)
		{
			$arJson = array( 'TYPE' => 'ERROR', 'MESSAGE' => 'Element id is empty' );
			echo json_encode($arJson);
			die();
		}
		$ElementID=$APPLICATION->IncludeComponent(
			'bitrix:catalog.element',
			'json',
			Array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
				'META_KEYWORDS' => $arParams['DETAIL_META_KEYWORDS'],
				'META_DESCRIPTION' => $arParams['DETAIL_META_DESCRIPTION'],
				'BROWSER_TITLE' => $arParams['DETAIL_BROWSER_TITLE'],
				'BASKET_URL' => $arParams['BASKET_URL'],
				'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
				'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
				'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
				'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
				'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'SET_TITLE' => $arParams['SET_TITLE'],
				'SET_STATUS_404' => $arParams['SET_STATUS_404'],
				'PRICE_CODE' => $arParams['PRICE_CODE'],
				'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
				'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
				'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
				'PRICE_VAT_SHOW_VALUE' => $arParams['PRICE_VAT_SHOW_VALUE'],
				'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
				'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
				'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
				'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
				'LINK_IBLOCK_TYPE' => $arParams['LINK_IBLOCK_TYPE'],
				'LINK_IBLOCK_ID' => $arParams['LINK_IBLOCK_ID'],
				'LINK_PROPERTY_SID' => $arParams['LINK_PROPERTY_SID'],
				'LINK_ELEMENTS_URL' => $arParams['LINK_ELEMENTS_URL'],
				'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
				'OFFERS_FIELD_CODE' => $arParams['DETAIL_OFFERS_FIELD_CODE'],
				'OFFERS_PROPERTY_CODE' => $arParams['DETAIL_OFFERS_PROPERTY_CODE'],
				'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
				'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
				'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
				'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
				'ELEMENT_ID' => $ELEMENT_ID,
				'ELEMENT_CODE' => '',
				'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
				'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
				'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
				'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
				'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
				'CURRENCY_ID' => $arParams['CURRENCY_ID'],
				'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
				'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
				// goPro params
				'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
				'PROP_ARTICLE' => $arParams['PROP_ARTICLE'],
				'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
				'USE_FAVORITE' => $arParams['USE_FAVORITE'],
				'USE_SHARE' => $arParams['USE_SHARE'],
				'SHOW_ERROR_EMPTY_ITEMS' => $arParams['SHOW_ERROR_EMPTY_ITEMS'],
				'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
				'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
				'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
				// store
				'USE_STORE' => $arParams['USE_STORE'],
				'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
				'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
				'MAIN_TITLE' => $arParams['MAIN_TITLE'],
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
		$APPLICATION->RestartBuffer();
		if (SITE_CHARSET != 'utf-8')
		{
			$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
			$json_str_utf = json_encode($data);
			$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
			echo $json_str;
		} else {
			echo json_encode( $JSON );
		}
		die();
	} elseif($arParams['USE_COMPARE']=='Y' && $_REQUEST['AJAX_CALL']=='Y' && ($_REQUEST['action']=='ADD_TO_COMPARE_LIST' || $_REQUEST['action']=='DELETE_FROM_COMPARE_LIST') )
	{
		// +++++++++++++++++++++++++++++++ add2compare +++++++++++++++++++++++++++++++ //
		global $APPLICATION,$JSON;
		$APPLICATION->IncludeComponent(
			'bitrix:catalog.compare.list',
			'json',
			Array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'NAME' => $arParams['COMPARE_NAME'],
				'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
				'COMPARE_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
				'IS_AJAX_REQUEST' => 'Y',
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
		$APPLICATION->RestartBuffer();
		if(SITE_CHARSET != 'utf-8')
		{
			$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
			$json_str_utf = json_encode($data);
			$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
			echo $json_str;
		} else {
			echo json_encode( $JSON );
		}
		die();
	} elseif($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['action']=='add2basket')
	{
		// +++++++++++++++++++++++++++++++ add2basket +++++++++++++++++++++++++++++++ //
		global $APPLICATION,$JSON;
		$ProductID = IntVal($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
		$QUANTITY = doubleval($_REQUEST[$arParams["PRODUCT_QUANTITY_VARIABLE"]]);
		$params = Array(
			'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
			'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
			'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
			'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
			'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
		);
		$restat = RSDF_EasyAdd2Basket($ProductID,$QUANTITY,$params);
		$APPLICATION->RestartBuffer();
		$APPLICATION->IncludeComponent('bitrix:sale.basket.basket.small','json',array());
		$APPLICATION->RestartBuffer();
		if(SITE_CHARSET != 'utf-8')
		{
			$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
			$json_str_utf = json_encode($data);
			$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
			echo $json_str;
		} else {
			echo json_encode( $JSON );
		}
		die();
	} elseif($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['action']=='add2favorite' && $ELEMENT_ID>0)
	{
		// +++++++++++++++++++++++++++++++ add2favorite +++++++++++++++++++++++++++++++ //
		global $APPLICATION,$JSON;
		$res = RSFavoriteAddDel($ELEMENT_ID);
		$APPLICATION->RestartBuffer();
		$APPLICATION->IncludeComponent('redsign:favorite.list','json',array());
		$APPLICATION->RestartBuffer();
		if($res==2)
		{
			$arJson = array('TYPE'=>'OK','MESSAGE'=>'Element add2favorite','ACTION'=>'ADD','HTMLBYID'=>$JSON['HTMLBYID']);
		} elseif($res==1) {
			$arJson = array('TYPE'=>'OK','MESSAGE'=>'Element removed from favorite','ACTION'=>'REMOVE','HTMLBYID'=>$JSON['HTMLBYID']);
		} else {
			$arJson = array('TYPE'=>'ERROR','MESSAGE'=>'Bad request');
		}
		echo json_encode($arJson);
		die();
	}
}


$APPLICATION->IncludeComponent(
	'bitrix:catalog.section.list',
	'gopro',
	array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'COUNT_ELEMENTS' => $arParams['SECTION_COUNT_ELEMENTS'],
		'TOP_DEPTH' => $arParams['SECTION_TOP_DEPTH'],
		'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
	),
	$component,
	array('HIDE_ICONS'=>'Y')
);