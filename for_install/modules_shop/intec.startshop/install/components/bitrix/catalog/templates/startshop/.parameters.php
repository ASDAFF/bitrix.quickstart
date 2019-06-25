<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?if (!\Bitrix\Main\Loader::includeModule('iblock')) return;?>
<?
    $arTemplateParameters = array();

    $arPropertiesMorePictures = array("" => GetMessage("SH_C_PROPERTY_EMPTY"));
    $arPropertiesMorePicturesOffers = array("" => GetMessage("SH_C_PROPERTY_EMPTY"));
    $arPropertiesArticle = array("" => GetMessage("SH_C_PROPERTY_EMPTY"));

    if (isset($arCurrentValues['IBLOCK_ID']))
    {
        $dbTempProperties = CIBlockProperty::GetList(
            array(),
            array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'])
        );

        while($arTempProperty = $dbTempProperties->Fetch()) {
            if ($arTempProperty['PROPERTY_TYPE'] == 'F' && $arTempProperty['MULTIPLE'] == 'Y')
                $arPropertiesMorePictures[$arTempProperty['CODE']] = '['.$arTempProperty['CODE'].'] '.$arTempProperty['NAME'];

            if ($arTempProperty['PROPERTY_TYPE'] == 'S' && $arTempProperty['MULTIPLE'] == 'N' && empty($arTempProperty['USER_TYPE']))
                $arPropertiesArticle[$arTempProperty['CODE']] = '['.$arTempProperty['CODE'].'] '.$arTempProperty['NAME'];
        }

        $arCatalog = CStartShopCatalog::GetByIBlock($arCurrentValues['IBLOCK_ID'])->Fetch();

        if (!empty($arCatalog) && !empty($arCatalog['OFFERS_IBLOCK'])) {
            $dbTempProperties = CIBlockProperty::GetList(
                array(),
                array('IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK'])
            );

            while($arTempProperty = $dbTempProperties->Fetch()) {
                if ($arTempProperty['PROPERTY_TYPE'] == 'F' && $arTempProperty['MULTIPLE'] == 'Y')
                    $arPropertiesMorePicturesOffers[$arTempProperty['CODE']] = '['.$arTempProperty['CODE'].'] '.$arTempProperty['NAME'];
            }
        }
    }

    $arCurrencies = array();
    $dbCurrencies = CStartShopCurrency::GetList();

    while ($arCurrency = $dbCurrencies->Fetch())
        $arCurrencies[$arCurrency['CODE']] = '['.$arCurrency['CODE'].'] '.$arCurrency['LANG'][LANGUAGE_ID]['NAME'];

    unset($dbCurrencies, $arCurrency);

    $arPricesTypes = array();
    $dbPricesTypes = CStartShopPrice::GetList(array('SORT' => 'ASC'));

    while ($arPriceType = $dbPricesTypes->Fetch())
        $arPricesTypes[$arPriceType['CODE']] = '['.$arPriceType['CODE'].'] '.$arPriceType['LANG'][LANGUAGE_ID]['NAME'];

    unset($dbPricesTypes, $arPriceType);

    $arTemplateParameters['ADAPTABLE'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('SH_C_ADAPTABLE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => "N"
    );

    $arTemplateParameters['PROPERTY_ARTICLE'] = array(
        'NAME' => GetMessage('SH_C_PROPERTY_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesArticle
    );

    $arTemplateParameters['PRICE_CODE'] = array(
        "PARENT" => "PRICES",
        "NAME" => "PRice cODe",
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arPricesTypes
    );

    $arTemplateParameters['PROPERTY_MORE_PICTURES'] = array(
        'NAME' => GetMessage('SH_C_PROPERTY_MORE_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesMorePictures
    );

    $arTemplateParameters['PROPERTY_MORE_PICTURES_OFFERS'] = array(
        'NAME' => GetMessage('SH_C_PROPERTY_MORE_PICTURES_OFFERS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesMorePicturesOffers
    );

    $arTemplateParameters['CATALOG_MENU'] = array(
        'NAME' => GetMessage('SH_C_CATALOG_MENU'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'catalog'
    );

    $arTemplateParameters['SHOW_LEFT_MENU_IN_ELEMENT'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('SH_C_SHOW_LEFT_MENU_IN_ELEMENT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    );

    $arTemplateParameters['SHOW_CUT_PROPS_OF_ELEMENT'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('SH_C_SHOW_CUT_PROPS_OF_ELEMENT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    );

    $arTemplateParameters['SHOW_SLIDER_IN_ELEMENT'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('SH_C_SHOW_SLIDER_IN_ELEMENT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    );

    $arTemplateParameters['USE_COMMON_CURRENCY'] = array(
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('SH_C_USE_COMMON_CURRENCY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    );

    if ($arCurrentValues['USE_COMMON_CURRENCY'] == 'Y') {
        $arTemplateParameters['CURRENCY'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => GetMessage('SH_C_CURRENCY'),
            'TYPE' => 'LIST',
            'VALUES' => $arCurrencies
        );
    }

    $arTemplateParameters['GRID_CATALOG_ROOT_SECTIONS_COUNT'] = array(
        "PARENT" => "SECTIONS_SETTINGS",
        "NAME" => GetMessage('SH_C_GRID_CATALOG_ROOT_SECTIONS_COUNT'),
        "TYPE" => "STRING",
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "5"
    );

    $arTemplateParameters['GRID_CATALOG_SECTIONS_COUNT'] = array(
        "PARENT" => "SECTIONS_SETTINGS",
        "NAME" => GetMessage('SH_C_GRID_CATALOG_SECTIONS_COUNT'),
        "TYPE" => "STRING",
        "ADDITIONAL_VALUES" => "Y",
        "DEFAULT" => "4"
    );
	
	/* Îòçûâû */
	$arIBlockType = CIBlockParameters::GetIBlockTypes();

	$arIBlock = array();
	$iblockFilter = (
		!empty($arCurrentValues['REVIEWS_IBLOCK_TYPE'])
		? array('TYPE' => $arCurrentValues['REVIEWS_IBLOCK_TYPE'], 'ACTIVE' => 'Y')
		: array('ACTIVE' => 'Y')
	);
	$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
	while ($arr = $rsIBlock->Fetch())
		$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
	unset($arr, $rsIBlock, $iblockFilter);

	$arTemplateParameters['REVIEWS_IBLOCK_TYPE'] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y"
	);

	$arTemplateParameters['REVIEWS_IBLOCK_ID'] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("REVIEWS_IBLOCK_IBLOCK"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y"
	);

	/* - Îòçûâû - */
?>