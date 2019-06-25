<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $arTemplateParameters['USE_ADAPTABILITY'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_ADAPTABILITY'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_ITEMS_PICTURES'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_ITEMS_PICTURES'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
    );

    $arTemplateParameters['USE_BUTTON_CLEAR'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_BUTTON_CLEAR'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_BUTTON_BASKET'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_BUTTON_BASKET'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_SUM_FIELD'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_SUM_FIELD'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );
	$arTemplateParameters['COMPARE_NAME'] = array(
        "NAME" => GetMessage("IBLOCK_COMPARE_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "CATALOG_COMPARE_LIST"
    );
	
	if (CModule::IncludeModule('intec.startshop')) {
	/* Быстрый заказ */
		$arOrderProps = array();
		$dbOrderProps = CStartShopOrderProperty::GetList();

		while ($arOrderProp = $dbOrderProps->Fetch())
			$arOrderProps[$arOrderProp['CODE']] = '['.$arOrderProp['CODE'].'] '.$arOrderProp['LANG'][LANGUAGE_ID]['NAME'];

		unset($dbOrderProp, $arOrderProp);
		
		$arTemplateParameters['CFO_USE_FASTORDER'] = array(
			'NAME' => GetMessage('CFO_USE_FASTORDER'),
			'PARENT' => 'VISUAL',
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y'
		);
		
		if ($arCurrentValues['CFO_USE_FASTORDER'] == 'Y') {
			
			$arTemplateParameters['CFO_PROP_NAME'] = array(
				"NAME" => GetMessage('CFO_PROP_NAME'),
				'PARENT' => 'VISUAL',
				"TYPE" => "LIST",
				"VALUES" => $arOrderProps,
				'DEFAULT' => 'NAME',
				"REFRESH" => "N"
			);
			$arTemplateParameters['CFO_PROP_PHONE'] = array(
				"NAME" => GetMessage('CFO_PROP_PHONE'),
				'PARENT' => 'VISUAL',
				"TYPE" => "LIST",
				"VALUES" => $arOrderProps,
				'DEFAULT' => 'PHONE',
				"REFRESH" => "N"
			);
			$arTemplateParameters['CFO_PROP_COMMENT'] = array(
				"NAME" => GetMessage('CFO_PROP_COMMENT'),
				'PARENT' => 'VISUAL',
				"TYPE" => "LIST",
				"VALUES" => $arOrderProps,
				'DEFAULT' => 'COMMENT',
				"REFRESH" => "N"
			);
		}
	/* Быстрый заказ */
	}
?>