<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")
	|| !CModule::IncludeModule("sale")
	|| !CModule::IncludeModule("catalog")
	|| !CModule::IncludeModule("currency"))
	return;

$arTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocks=Array();
if ($arCurrentValues["IBLOCK_TYPE"]!="-") {
	$db_iblock = CIBlock::GetList(
		Array("SORT"=>"ASC"),
		Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => $arCurrentValues["IBLOCK_TYPE"])
	);
	while ($ar_tmp = $db_iblock->Fetch())
		$arIBlocks[$ar_tmp["ID"]] = '[' . $ar_tmp["ID"] . ']' . $ar_tmp["NAME"];
}

$default_class = ".catalog-detail-buttons";
if(intval(substr(SM_VERSION, 0, 2)) > 11) {
    $default_class = "#ocb_intaro";
}

$arDeliveries = array('0' => GetMessage('1CB_NOT_SET'));
$db_deliveries = CSaleDelivery::GetList(
	array('SORT' => 'ASC'),
	array('ACTIVE' => 'Y', 'LID'=>$_REQUEST["site"]),
	false, false,
	array('ID', 'NAME')
);
while ($ar_tmp = $db_deliveries->Fetch())
	$arDeliveries[$ar_tmp["ID"]] = '[' . $ar_tmp["ID"] . ']' . $ar_tmp["NAME"];

$arPayments = array('0' => GetMessage('1CB_NOT_SET'));
$db_payments = CSalePaySystem::GetList(
	array('SORT' => 'ASC'),
	array('ACTIVE' => 'Y', 'LID'=>$_REQUEST["site"]),
	false, false,
	array('ID', 'NAME')
);
while ($ar_tmp = $db_payments->Fetch())
	$arPayments[$ar_tmp["ID"]] = '[' . $ar_tmp["ID"] . ']' . $ar_tmp["NAME"];

$arPersonTypes = array();
$db_person_types = CSalePersonType::GetList(
	array('ID'=>'ASC'),
	array('LID'=>$_REQUEST["site"])
);
while ($ar_tmp = $db_person_types->Fetch())
	$arPersonTypes[$ar_tmp['ID']] = '[' . $ar_tmp['ID'] . '] ' . $ar_tmp['NAME'];

$arPrices = array();
$db_prices = CCatalogGroup::GetList(array('SORT'=>'ASC'));
while ($ar_tmp = $db_prices->Fetch())
	$arPrices[$ar_tmp['ID']] = '[' . $ar_tmp['ID'] . '] ' . $ar_tmp['NAME'] . ($ar_tmp['BASE']=='Y'? GetMessage('1CB_BASE_PRICE') : '');

$arCurrencies = array();
$db_currencies = CCurrency::GetList(($b = 'name'), ($o = 'asc'));
while ($ar_tmp = $db_currencies->Fetch())
	$arCurrencies[$ar_tmp['CURRENCY']] = '[' . $ar_tmp['CURRENCY'] . '] ' . $ar_tmp['FULL_NAME'];
$default_currency = COption::GetOptionString('sale', 'default_currency', 'RUB');

$arOrderFields = array(
	"FIO" => GetMessage('1CB_FIELD_OPTION_FIO'),
    "PHONE" => GetMessage('1CB_FIELD_OPTION_PHONE'),
    "EMAIL" => GetMessage('1CB_FIELD_OPTION_EMAIL'),
);

$arBuyModes = array(
	'ONE' => GetMessage('1CB_BUY_MODE_ONE'),
	'ALL' => GetMessage('1CB_BUY_MODE_ALL'),
);

$arDubEmails = array();
$admin_email = COption::GetOptionString('main', 'email_from', '');
if (!empty($admin_email))
	$arDubEmails['a'] = GetMessage('1CB_DUB_ADMIN_EMAIL') . $admin_email;
$sales_email = COption::GetOptionString('sale', 'order_email', '');
if (!empty($sales_email))
	$arDubEmails['s'] = GetMessage('1CB_DUB_SALES_EMAIL') . $sales_email;
$dub_email = COption::GetOptionString('main', 'all_bcc', '');
if (!empty($dub_email))
	$arDubEmails['d'] = GetMessage('1CB_DUB_DUB_EMAIL') . $dub_email;

$arComponentParameters = array(
	"GROUPS" => array(
		"JSOPTIONS" => array(
			"NAME" => GetMessage("1CB_GROUP_JSOPTIONS"),
		),
		"SKU_PROPERTIES" => array(
			"NAME" => GetMessage("1CB_GROUP_SKU_PROPERTIES"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"USE_QUANTITY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_USE_QUANTITY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SEF_FOLDERIX" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_SEF_FOLDER"),
			"TYPE" => "STRING",
			"DEFAULT" => '/catalog/',
		),
		"ORDER_FIELDS" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_ORDER_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $arOrderFields,
			"DEFAULT" => array('FIO', 'PHONE', 'EMAIL'),
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"MULTIPLE" => "Y",
		),
		"REQUIRED_ORDER_FIELDS" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_REQUIRED_ORDER_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $arOrderFields,
			"DEFAULT" => array('FIO', 'PHONE'),
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"MULTIPLE" => "Y",
		),
		"DEFAULT_PERSON_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_DEFAULT_PERSON_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arPersonTypes,
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"MULTIPLE" => "N",
			"DEFAULT" => 1,
		),
		"DEFAULT_DELIVERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_DEFAULT_DELIVERY"),
			"TYPE" => "LIST",
			"VALUES" => $arDeliveries,
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"MULTIPLE" => "N",
		),
		"DEFAULT_PAYMENT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_DEFAULT_PAYMENT"),
			"TYPE" => "LIST",
			"VALUES" => $arPayments,
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"MULTIPLE" => "N",
		),
		"DEFAULT_CURRENCY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_DEFAULT_CURRENCY"),
			"TYPE" => "LIST",
			"VALUES" => $arCurrencies,
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => $default_currency,
			"REFRESH" => "N",
			"MULTIPLE" => "N",
		),
		"BUY_MODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_BUY_MODE"),
			"TYPE" => "LIST",
			"VALUES" => $arBuyModes,
			"DEFAULT" => "ONE",
		),
		"PRICE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_PRICE_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arPrices,
			"DEFAULT" => "",
		),
		"DUPLICATE_LETTER_TO_EMAILS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_DUPLICATE_LETTER_TO_EMAILS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arDubEmails,
			"REFRESH" => "N",
		),
		"USE_DEBUG_MESSAGES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_USE_DEBUG_MESSAGES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"USE_SKU" => array(
			"PARENT" => "SKU_PROPERTIES",
			"NAME" => GetMessage("1CB_PARAMETER_USE_SKU"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"SORT" => 10,
		),
		"USE_JQUERY" => array(
			"PARENT" => "JSOPTIONS",
			"NAME" => GetMessage("1CB_PARAMETER_USE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"SORT" => 10,
		),
		"INSERT_ELEMENT" => array(
			"PARENT" => "JSOPTIONS",
			"NAME" => GetMessage("1CB_PARAMETER_INSERT_ELEMENT"),
			"DEFAULT" => $default_class,
			"SORT" => 20,
		),
		"USE_ANTISPAM" => array(
			"PARENT" => "JSOPTIONS",
			"NAME" => GetMessage("1CB_PARAMETER_USE_ANTISPAM"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"SORT" => 30,
		),
		"CACHE_TIME" => array(
			"DEFAULT" => 864000
		),
	),
);

if ($arCurrentValues['USE_SKU'] == 'Y') {
	$arSKUProps = array();
	$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
	if (is_array($arOffersIBlock)) {
		$dbProps = CIBlockProperty::GetList(
			array('SORT'=>'ASC', 'NAME'=>'ASC'),
			array('ACTIVE'=>'Y', 'IBLOCK_ID'=>$arOffersIBlock['OFFERS_IBLOCK_ID'])
		);
		while ($ar_tmp = $dbProps->Fetch())
			if ($ar_tmp['CODE'] != 'CML2_LINK' || $ar_tmp["PROPERTY_TYPE"] != "F")
				$arSKUProps[$ar_tmp['CODE']] = '[' . $ar_tmp['CODE'] . ']' . $ar_tmp['NAME'];
		$arComponentParameters["PARAMETERS"]["SKU_PROPERTIES_CODES"] = array(
			"PARENT" => "SKU_PROPERTIES",
			"NAME" => GetMessage("1CB_PARAMETER_SKU_PROPERTIES_CODES"),
			"TYPE" => "LIST",
			"VALUES" => $arSKUProps,
			"MULTIPLE" => "Y",
			"SORT" => 20,
		);
		$arComponentParameters["PARAMETERS"]["SKU_COUNT"] = array(
			"PARENT" => "SKU_PROPERTIES",
			"NAME" => GetMessage("1CB_PARAMETER_SKU_COUNT"),
			"DEFAULT" => '10',
			"SORT" => 30,
		);
	}
}
?>
