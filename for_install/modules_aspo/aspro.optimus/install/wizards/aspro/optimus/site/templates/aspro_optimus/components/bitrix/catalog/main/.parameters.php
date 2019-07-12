<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	/** @var array $arCurrentValues */
	/** @global CUserTypeManager $USER_FIELD_MANAGER */
	global $USER_FIELD_MANAGER;
	use Bitrix\Main\Loader;
	use Bitrix\Main\ModuleManager;
	Loader::includeModule('iblock');
	$arSKU = false;
	$boolSKU = false;
	

	$arSort = CIBlockParameters::GetElementSortFields(
		array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
		array('KEY_LOWERCASE' => 'Y')
	);

	$arIBlocks=Array();
	$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_BANNERS_TYPE"]!="-"?$arCurrentValues["IBLOCK_BANNERS_TYPE"]:"")));
	while($arRes = $db_iblock->Fetch()) $arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	
	$arTypes = array();
	if ($arCurrentValues["IBLOCK_BANNERS_TYPE_ID"])
	{
		$rsTypes=CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_BANNERS_TYPE_ID"], "ACTIVE" =>"Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "CODE"));
		while($arr=$rsTypes->Fetch()) $arTypes[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	}
	$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));
	
	
	$arPrice = array();
	if (Loader::includeModule("catalog"))
	{
		$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
		$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
		while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
		if ((isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID']) > 0)
		{
			$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
			$boolSKU = !empty($arSKU) && is_array($arSKU);
		}
	} else {$arPrice = $arProperty_N;}
	$arPrice  = array_merge(array("MINIMUM_PRICE"=>GetMessage("SORT_PRICES_MINIMUM_PRICE"), "MAXIMUM_PRICE"=>GetMessage("SORT_PRICES_MAXIMUM_PRICE")), $arPrice);

	$arProperty_S = array();
	if (0 < intval($arCurrentValues['IBLOCK_ID']))
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
		while ($arr=$rsProp->Fetch())
		{
			if($arr["PROPERTY_TYPE"]=="S")
				$arProperty_S[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}


	$arUserFields_S = array();
	$arUserFields_E = array();
	$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
	foreach($arUserFields as $FIELD_NAME=>$arUserField) {
		if($arUserField["USER_TYPE"]["BASE_TYPE"]=="enum")
			{ $arUserFields_E[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME; }
		if($arUserField["USER_TYPE"]["BASE_TYPE"]=="string")
			{ $arUserFields_S[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME; }
	}	

	$arTemplateParametersParts = array();

	$arTemplateParametersParts[] = array(
		"IBLOCK_STOCK_ID" => Array(
			"NAME" => GetMessage("IBLOCK_STOCK_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),	
		"SHOW_MEASURE" => Array(
				"NAME" => GetMessage("SHOW_MEASURE"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
		),
		"SORT_BUTTONS" => Array(
			"SORT" => 100,
			"NAME" => GetMessage("SORT_BUTTONS"),
			"TYPE" => "LIST",
			"VALUES" => array("POPULARITY"=>GetMessage("SORT_BUTTONS_POPULARITY"), "NAME"=>GetMessage("SORT_BUTTONS_NAME"), "PRICE"=>GetMessage("SORT_BUTTONS_PRICE"), "QUANTITY"=>GetMessage("SORT_BUTTONS_QUANTITY")),
			"DEFAULT" => array("POPULARITY", "NAME", "PRICE"),
			"PARENT" => "LIST_SETTINGS",
			"TYPE" => "LIST",
			"REFRESH" => "Y",
			"MULTIPLE" => "Y",
		),
	);
		
		
	if(is_array($arCurrentValues["SORT_BUTTONS"])){
		if (in_array("PRICE", $arCurrentValues["SORT_BUTTONS"])){
			$arTemplateParametersParts[]["SORT_PRICES"] = Array(
				"SORT"=>200,
				"NAME" => GetMessage("SORT_PRICES"),
				"TYPE" => "LIST",
				"VALUES" => $arPrice,
				"DEFAULT" => array("MINIMUM_PRICE"),
				"PARENT" => "LIST_SETTINGS",
				"MULTIPLE" => "N",
			);
		}	
	}

	$detailPictMode = array(
		'IMG' => GetMessage('DETAIL_PICTURE_MODE_IMG'),
		'POPUP' => GetMessage('DETAIL_PICTURE_MODE_POPUP'),
		'MAGNIFIER' => GetMessage('DETAIL_PICTURE_MODE_MAGNIFIER')
	);

	$arTemplateParametersParts[] = array(	
		"DEFAULT_LIST_TEMPLATE" => Array(
				"NAME" => GetMessage("DEFAULT_LIST_TEMPLATE"),
				"TYPE" => "LIST",
				"VALUES" => array("block"=>GetMessage("DEFAULT_LIST_TEMPLATE_BLOCK"), "list"=>GetMessage("DEFAULT_LIST_TEMPLATE_LIST"), "table"=>GetMessage("DEFAULT_LIST_TEMPLATE_TABLE")),
				"DEFAULT" => "list",
				"PARENT" => "LIST_SETTINGS",
		),
		"SECTION_DISPLAY_PROPERTY" => Array(
				"NAME" => GetMessage("SECTION_DISPLAY_PROPERTY"),
				"TYPE" => "LIST",
				"VALUES" => $arUserFields_E,
				"DEFAULT" => "list",
				"MULTIPLE" => "N",
				"PARENT" => "LIST_SETTINGS",
		),
		"SECTION_TOP_BLOCK_TITLE" => Array(
				"NAME" => GetMessage("SECTION_TOP_BLOCK_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("SECTION_TOP_BLOCK_TITLE_VALUE"),
				"PARENT" => "TOP_SETTINGS",
		),
		"USE_RATING" => array(
				"NAME" => GetMessage("USE_RATING"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
		),
		"LIST_DISPLAY_POPUP_IMAGE" => array(
			"NAME" => GetMessage("LIST_DISPLAY_POPUP_IMAGE"),
			"PARENT" => "LIST_SETTINGS",
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
		),
		"DISPLAY_WISH_BUTTONS" => array(
			"NAME" => GetMessage("DISPLAY_WISH_BUTTONS"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
		),
		"DEFAULT_COUNT" => array(
			"NAME" => GetMessage("DEFAULT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"DISPLAY_ELEMENT_SLIDER" => Array(
			"NAME" => GetMessage("DISPLAY_ELEMENT_SLIDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "10",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"PROPERTIES_DISPLAY_LOCATION" => Array(
			"NAME" => GetMessage("PROPERTIES_DISPLAY_LOCATION"),
			"TYPE" => "LIST",
			"VALUES" => array("DESCRIPTION"=>GetMessage("PROPERTIES_DISPLAY_LOCATION_DESCRIPTION"), "TAB"=>GetMessage("PROPERTIES_DISPLAY_LOCATION_TAB")),
			"DEFAULT" => "DESCRIPTION",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_ADD_DETAIL_TO_SLIDER" => array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_DETAIL_TO_SLIDER'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N'
		),
		"SHOW_BRAND_PICTURE" => Array(
				"NAME" => GetMessage("SHOW_BRAND_PICTURE"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_ASK_BLOCK" => Array(
				"NAME" => GetMessage("SHOW_ASK_BLOCK"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"ASK_FORM_ID" => Array(
				"NAME" => GetMessage("ASK_FORM_ID"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_OFFERS_LIMIT" => Array(
				"NAME" => GetMessage("DETAIL_OFFERS_LIMIT"),
				"TYPE" => "STRING",
				"DEFAULT" => "0",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_EXPANDABLES_TITLE" => Array(
				"NAME" => GetMessage("DETAIL_EXPANDABLES_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("DETAIL_EXPANDABLES_VALUE"),
				"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_ASSOCIATED_TITLE" => Array(
				"NAME" => GetMessage("DETAIL_ASSOCIATED_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("DETAIL_ASSOCIATED_VALUE"),
				"PARENT" => "DETAIL_SETTINGS",
		),
		"SALE_STIKER" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SALE_STIKER"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arProperty_S),
		),
		"SHOW_ADDITIONAL_TAB" => Array(
			"NAME" => GetMessage("SHOW_ADDITIONAL_TAB"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_HINTS" => Array(
			"NAME" => GetMessage("SHOW_HINTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PROPERTIES_DISPLAY_TYPE" => Array(
			"NAME" => GetMessage("PROPERTIES_DISPLAY_TYPE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array("BLOCK"=>GetMessage("PROPERTIES_DISPLAY_TYPE_BLOCK"), "TABLE"=>GetMessage("PROPERTIES_DISPLAY_TYPE_TABLE")),
			"DEFAULT" => "BLOCK",
			"PARENT" => "DETAIL_SETTINGS",
		),
		/*"SHOW_DISCOUNT_PERCENT" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),*/
		/*"DETAIL_PICTURE_MODE" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BCE_TPL_DETAIL_PICTURE_MODE'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'POPUP',
			'VALUES' => $detailPictMode
		),*/
		"SHOW_DISCOUNT_TIME" => Array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("SHOW_DISCOUNT_TIME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_RATING" => Array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("SHOW_RATING"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_OLD_PRICE" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
	);

	$arTemplateParametersParts[]["SECTIONS_LIST_PREVIEW_PROPERTY"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_PREVIEW_PROPERTY"),
		"VALUES" => array_merge(array("DESCRIPTION"=>GetMessage("SHOW_SECTION_PREVIEW_PROPERTY_DESCRIPTION")), $arUserFields_S),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "DESCRIPTION",
		"PARENT" => "SECTIONS_SETTINGS",
	);	

	$arTemplateParametersParts[]["SECTION_PREVIEW_PROPERTY"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_PREVIEW_PROPERTY"),
		"VALUES" => array_merge(array("DESCRIPTION"=>GetMessage("SHOW_SECTION_PREVIEW_PROPERTY_DESCRIPTION")), $arUserFields_S),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "DESCRIPTION",
		"PARENT" => "LIST_SETTINGS");
	$arTemplateParametersParts[]["SECTIONS_LIST_PREVIEW_DESCRIPTION"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_ROOT_PREVIEW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "SECTIONS_SETTINGS");
	$arTemplateParametersParts[]["SECTION_PREVIEW_DESCRIPTION"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_ROOT_PREVIEW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "LIST_SETTINGS");
	
	
	$arTemplateParametersParts[] = Array(
		"SHOW_SECTION_LIST_PICTURES" => Array(
			"NAME" => GetMessage("SHOW_SECTION_PICTURES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "SECTIONS_SETTINGS",
		),
		"SHOW_SECTION_PICTURES" => Array(
			"NAME" => GetMessage("SHOW_SECTION_PICTURES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "LIST_SETTINGS",
		),
		/*"SHOW_SECTION_SIBLINGS" => Array(
			"NAME" => GetMessage("SHOW_SECTION_SIBLINGS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "LIST_SETTINGS",
		),*/
		"SHOW_KIT_PARTS" => Array(
			"NAME" => GetMessage("SHOW_KIT_PARTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_KIT_PARTS_PRICES" => Array(
			"NAME" => GetMessage("SHOW_KIT_PARTS_PRICES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_ONE_CLICK_BUY" => Array(
			"NAME" => GetMessage("SHOW_ONE_CLICK_BUY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SKU_DETAIL_ID" => Array(
			"NAME" => GetMessage("SKU_DETAIL_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "oid",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"AJAX_FILTER_CATALOG" => Array(
			"NAME" => GetMessage("AJAX_FILTER_CATALOG_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "FILTER_SETTINGS",
		),
	);
	
	$arAllPropList = array();
	$arFilePropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arListPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arHighloadPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch())
	{
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		if ('' == $arProp['CODE'])
			$arProp['CODE'] = $arProp['ID'];
		$arAllPropList[$arProp['CODE']] = $strPropName;
		if ('F' == $arProp['PROPERTY_TYPE'])
			$arFilePropList[$arProp['CODE']] = $strPropName;
		if ('L' == $arProp['PROPERTY_TYPE'])
			$arListPropList[$arProp['CODE']] = $strPropName;
		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			$arHighloadPropList[$arProp['CODE']] = $strPropName;
	}
	
	$arTemplateParametersParts[] = array(
		'ADD_PICT_PROP' => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_ADD_PICT_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFilePropList
		)
	);
	
	if ($boolSKU)
	{
		$arAllOfferPropList = array();
		$arFileOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$arTreeOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch())
		{
			if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
				continue;
			$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
			$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
			if ('' == $arProp['CODE'])
				$arProp['CODE'] = $arProp['ID'];
			if ('F' == $arProp['PROPERTY_TYPE'])
				$arFileOfferPropList[$arProp['CODE']] = $strPropName;
			if ('N' != $arProp['MULTIPLE'])
				continue;
			if (
				'L' == $arProp['PROPERTY_TYPE']
				|| 'E' == $arProp['PROPERTY_TYPE']
				|| ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			)
				$arTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}
		$arTemplateParametersParts[] = array(
			'OFFER_ADD_PICT_PROP' => array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_OFFER_ADD_PICT_PROP'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'N',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arFileOfferPropList
			)
		);
		$arTemplateParametersParts[]=array(
			'OFFER_TREE_PROPS' => array(
				'PARENT' => 'OFFERS_SETTINGS',
				'NAME' => GetMessage('OFFERS_SETTINGS'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'Y',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arTreeOfferPropList
			)
		);
		$arTemplateParametersParts[]=array(
			'OFFER_HIDE_NAME_PROPS' => array(
				'PARENT' => 'OFFERS_SETTINGS',
				'NAME' => GetMessage('OFFER_HIDE_NAME_PROPS_TITLE'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
			)
		);
	}
	if (ModuleManager::isModuleInstalled("sale"))
	{
		$arTemplateParametersParts[]=array(
			'USE_BIG_DATA' => array(
				'PARENT' => 'BIG_DATA_SETTINGS',
				'NAME' => GetMessage('CP_BC_TPL_USE_BIG_DATA'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'Y',
				'REFRESH' => 'Y'
			)
		);
		if (!isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] == 'Y')
		{
			$rcmTypeList = array(
				'bestsell' => GetMessage('CP_BC_TPL_RCM_BESTSELLERS'),
				'personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL'),
				'similar_sell' => GetMessage('CP_BC_TPL_RCM_SOLD_WITH'),
				'similar_view' => GetMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
				'similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR'),
				'any_similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
				'any_personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
				'any' => GetMessage('CP_BC_TPL_RCM_RAND')
			);
			$arTemplateParametersParts[]=array(
				'BIG_DATA_RCM_TYPE' => array(
					'PARENT' => 'BIG_DATA_SETTINGS',
					'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
					'TYPE' => 'LIST',
					'VALUES' => $rcmTypeList
				)
			);
			unset($rcmTypeList);
		}
	}
		

	//merge parameters to one array 
	$arTemplateParameters = array();
	foreach($arTemplateParametersParts as $i => $part) { $arTemplateParameters = array_merge($arTemplateParameters, $part); }
?>
