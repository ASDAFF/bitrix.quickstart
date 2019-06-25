<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
	$arCurrencies = array();
	$dbCurrencies = CStartShopCurrency::GetList();

	while ($arCurrency = $dbCurrencies->Fetch())
		$arCurrencies[$arCurrency['CODE']] = '['.$arCurrency['CODE'].'] '.$arCurrency['LANG'][LANGUAGE_ID]['NAME'];

	unset($dbCurrencies, $arCurrency);

	$arComponentParameters = array(
		"GROUPS" => array(
			"URL" => array("NAME" => GetMessage("SBBS_GROUP_URL"))
		),
		"PARAMETERS" => array(
			"URL_BASKET" => array(
					"PARENT" => "URL",
					"NAME" => GetMessage("SBBS_URL_BASKET"),
					"TYPE" => "STRING",
			),
			"CURRENCY" => array(
					"PARENT" => "BASE",
					"NAME" => GetMessage("SBBS_CURRENCY"),
					"TYPE" => "LIST",
					"VALUES" => $arCurrencies
			),
			"REQUEST_VARIABLE_ACTION" => array(
					"PARENT" => "ADDITIONAL",
					"NAME" => GetMessage("SBBS_REQUEST_VARIABLE_ACTION"),
					"TYPE" => "STRING",
					"DEFAULT" => "action"
			),
			"REQUEST_VARIABLE_ITEM" => array(
					"PARENT" => "ADDITIONAL",
					"NAME" => GetMessage("SBBS_REQUEST_VARIABLE_ITEM"),
					"TYPE" => "STRING",
					"DEFAULT" => "item"
			),
			"REQUEST_VARIABLE_QUANTITY" => array(
					"PARENT" => "ADDITIONAL",
					"NAME" => GetMessage("SBBS_REQUEST_VARIABLE_QUANTITY"),
					"TYPE" => "STRING",
					"DEFAULT" => "quantity"
			)
		)
	);
?>