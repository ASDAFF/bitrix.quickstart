<?
	$arTemplateParameters = array(
		"USE_BUTTON_BUY" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('SBBS_FLYING_USE_BUTTON_BUY'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"URL_ORDER" => array(
			"PARENT" => "URL",
			"NAME" => GetMessage('SBBS_FLYING_URL_ORDER'),
			"TYPE" => "STRING",
		)
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