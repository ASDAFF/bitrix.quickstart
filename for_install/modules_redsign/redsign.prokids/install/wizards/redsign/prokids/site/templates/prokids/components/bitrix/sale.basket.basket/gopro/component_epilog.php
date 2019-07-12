<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!function_exists('RSGoPro_GetAccessoriesIDs'))
{
	function RSGoPro_GetAccessoriesIDs($IBLOCK_ID,$arIDs=array(),$params=array())
	{
		$arrEccessoriesIDs = array();
		if( IntVal($IBLOCK_ID)>0 && is_array($arIDs) && count($arIDs)>0 && CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog') && $params['PROP_ACCESSORIES']!='' )
		{
			$arCatalog = CCatalog::GetByIDExt($IBLOCK_ID);
			if( is_array($arCatalog) )
			{
				if( $arCatalog['CATALOG_TYPE']=='O' )
				{
					$OFFERS_IBLOCK_ID = $IBLOCK_ID;
					$IBLOCK_ID = $arCatalog['PRODUCT_IBLOCK_ID'];
				} elseif( $arCatalog['CATALOG_TYPE']=='X' && $arCatalog['OFFERS_IBLOCK_ID']>0 )
				{
					$OFFERS_IBLOCK_ID = $arCatalog['OFFERS_IBLOCK_ID'];
				}
				// get CML2_LINK values
				if( $OFFERS_IBLOCK_ID>0 )
				{
					$arSelect = Array('ID','IBLOCK_ID','PROPERTY_'.$arCatalog['SKU_PROPERTY_ID']);
					$arFilter = Array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID,'ID'=>$arIDs,'ACTIVE_DATE'=>'Y','ACTIVE'=>'Y');
					$res = CIBlockElement::GetList(array('sort'=>'asc'),$arFilter,false,array('nPageSize'=>100),$arSelect);
					$arrElementIDs = array();
					while($ob = $res->GetNextElement())
					{
						$arProps = $ob->GetProperties();
						foreach($arProps as $CODE => $arProp)
						{
							if( $arProp['ID'] == $arCatalog['SKU_PROPERTY_ID'] )
							{
								$arrElementIDs[] = $arProp['VALUE'];
								break;
							}
						}
					}
					$arIDs = array_merge($arIDs,$arrElementIDs);
					$arIDs = array_unique($arIDs);
				}
				// get accessories IDs
				if( $IBLOCK_ID>0 )
				{
					$arSelect = Array('ID','IBLOCK_ID','PROPERTY_'.$params['PROP_ACCESSORIES']);
					$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID,'ID'=>$arIDs,'ACTIVE_DATE'=>'Y','ACTIVE'=>'Y');
					$res = CIBlockElement::GetList(array('sort'=>'asc'),$arFilter,false,array('nPageSize'=>100),$arSelect);
					while($ob = $res->GetNextElement())
					{
						$arProps = $ob->GetProperties();
						foreach($arProps as $CODE => $arProp)
						{
							if( $arProp['CODE'] == $params['PROP_ACCESSORIES'] )
							{
								if( is_array($arProp['VALUE']) && count($arProp['VALUE'])>0 )
								{
									$arrEccessoriesIDs = array_merge($arrEccessoriesIDs,$arProp['VALUE']);
								} else {
									$arrEccessoriesIDs[] = $arProp['VALUE'];
								}
								break;
							}
						}
					}
					$arrEccessoriesIDs = array_unique($arrEccessoriesIDs);
				}
			}
		}
		return $arrEccessoriesIDs;
	}
}

if( IntVal($arParams['ACCESSORIES_IBLOCK'])>0 && $arParams['PROP_ACCESSORIES']!='' && is_array($arrAccFilter['ID']) && count($arrAccFilter['ID'])>0 )
{
	$arProducts = array();
	foreach($arResult['GRID']['ROWS'] as $k => $arItem)
	{
		// arrAccFilter
		$arProducts[] = $arItem['PRODUCT_ID'];
		$IBLOCK_ID = $arItem['IBLOCK_ID'];
	}

	global $arrAccFilter;
	$arrAccFilter = array();
	$params = array(
		'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
	);
	$arrAccFilter['ID'] = RSGoPro_GetAccessoriesIDs($IBLOCK_ID,$arProducts,$params);
}
?>

<?if( IntVal($arParams['ACCESSORIES_IBLOCK'])>0 && $arParams['PROP_ACCESSORIES']!='' && is_array($arrAccFilter['ID']) && count($arrAccFilter['ID'])>0 ):?>

<h2 class="acc_title"><?=GetMessage('ACC')?></h2>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"light", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => $arParams['ACCESSORIES_IBLOCK'],
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "timestamp_x",
		"ELEMENT_SORT_ORDER2" => "asc",
		"FILTER_NAME" => "arrAccFilter",
		"INCLUDE_SUBSECTIONS" => "A",
		"SHOW_ALL_WO_SECTION" => "Y",
		"HIDE_NOT_AVAILABLE" => "N",
		"PAGE_ELEMENT_COUNT" => "5",
		"LINE_ELEMENT_COUNT" => "3",
		"PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "BRAND",
			2 => "YEAR",
			3 => "OS",
			4 => "WEIGHT",
			5 => "FORUM_MESSAGE_CNT",
			6 => "RSFAVORITE_COUNTER",
			7 => "FORUM_TOPIC_ID",
			8 => "HEIGHT",
			9 => "TICKNESS",
			10 => "WIDTH",
			11 => "DIAGONAL",
			12 => "SOLUTION",
			13 => "INTERNET_ACCESS",
			14 => "INTERFACES",
			15 => "NAVI",
			16 => "CARD",
			17 => "VIDEO",
			18 => "ACCESSORIES",
			19 => "POHOZHIE",
			20 => "BUY_WITH_THIS",
			21 => "YEARS",
			22 => "",
		),
		"OFFERS_LIMIT" => "0",
		"TEMPLATE_THEME" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"SET_META_KEYWORDS" => "N",
		"META_KEYWORDS" => "",
		"SET_META_DESCRIPTION" => "N",
		"META_DESCRIPTION" => "",
		"BROWSER_TITLE" => "-",
		"ADD_SECTIONS_CHAIN" => "N",
		"DISPLAY_COMPARE" => "Y",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "N",
		"CACHE_FILTER" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "WHOLE",
			2 => "RETAIL",
			3 => "EXTPRICE",
			4 => "EXTPRICE2",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => "Y",
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"PAGER_TEMPLATE" => "gopro",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "CODE",
			2 => "XML_ID",
			3 => "NAME",
			4 => "TAGS",
			5 => "SORT",
			6 => "PREVIEW_TEXT",
			7 => "PREVIEW_PICTURE",
			8 => "DETAIL_TEXT",
			9 => "DETAIL_PICTURE",
			10 => "DATE_ACTIVE_FROM",
			11 => "ACTIVE_FROM",
			12 => "DATE_ACTIVE_TO",
			13 => "ACTIVE_TO",
			14 => "SHOW_COUNTER",
			15 => "SHOW_COUNTER_START",
			16 => "IBLOCK_TYPE_ID",
			17 => "IBLOCK_ID",
			18 => "IBLOCK_CODE",
			19 => "IBLOCK_NAME",
			20 => "IBLOCK_EXTERNAL_ID",
			21 => "DATE_CREATE",
			22 => "CREATED_BY",
			23 => "CREATED_USER_NAME",
			24 => "TIMESTAMP_X",
			25 => "MODIFIED_BY",
			26 => "USER_NAME",
			27 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "COLOR_DIRECTORY",
			2 => "COLOR2_DIRECTORY",
			3 => "STORAGE",
			4 => "MORE_PHOTO",
			5 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "asc",
		"PROP_MORE_PHOTO" => "MORE_PHOTO",
		"PROP_ARTICLE" => "CML2_ARTICLE",
		"PROP_ACCESSORIES" => "ACCESSORIES",
		"USE_FAVORITE" => "Y",
		"USE_SHARE" => "Y",
		"SHOW_ERROR_EMPTY_ITEMS" => "Y",
		"DONT_SHOW_LINKS" => "N",
		"USE_STORE" => "Y",
		"USE_MIN_AMOUNT" => "Y",
		"MIN_AMOUNT" => "10",
		"MAIN_TITLE" => "Наличие на складах",
		"PROP_SKU_MORE_PHOTO" => "MORE_PHOTO",
		"PROP_SKU_ARTICLE" => "CML2_ARTICLE",
		"PROPS_ATTRIBUTES" => array(
			0 => "COLOR_DIRECTORY",
			1 => "COLOR2_DIRECTORY",
			2 => "STORAGE",
		),
		"OFFERS_CART_PROPERTIES" => array(
		),
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"AJAXPAGESID" => "ajaxpages_main",
		"IS_AJAXPAGES" => $IS_AJAXPAGES,
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

<?endif;?>