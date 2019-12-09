<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
	
if(!CModule::IncludeModule('sale'))
	return;

// ****************************************************************************************** //
// **************************************** DELIVERY **************************************** //
// ****************************************************************************************** //
$deliver = $wizard->GetVar('delivery');

// get location id
$LOCATION_GROUP_ID = 1;
$db_vars = CSaleLocationGroup::GetList(array('NAME' => 'ASC'), array(), LANGUAGE_ID);
while ($vars = $db_vars->Fetch())
{
	if($vars['SORT']==111)
		$LOCATION_GROUP_ID = $vars['ID'];
}

// Po pochte
if($deliver['pochta'] == 'Y')
{
	$arFields = array(
		'NAME' => GetMessage('DELIVERY_NAME_1'),
		'LID' => WIZARD_SITE_ID,
		'PERIOD_FROM' => 9,
		'PERIOD_TO' => 15,
		'PERIOD_TYPE' => 'D',
		'WEIGHT_FROM' => 0,
		'WEIGHT_TO' => 4999,
		'ORDER_PRICE_FROM' => 0,
		'ORDER_PRICE_TO' => 0,
		'ORDER_CURRENCY' => 'RUB',
		'ACTIVE' => 'Y',
		'PRICE' => 75,
		'CURRENCY' => 'RUB',
		'SORT' => 101,
		'DESCRIPTION' => GetMessage('DELIVERY_DISCRIPTION_1'),
		'LOCATIONS' => array(
			array('LOCATION_ID' => $LOCATION_GROUP_ID, 'LOCATION_TYPE' => 'G')
		)
	);
	$ID = CSaleDelivery::Add($arFields);
}

// ___________________________________________________________________________________________ //

// Kurier
if($deliver['kurier'] == 'Y')
{
	$arFields = array(
		'NAME' => GetMessage('DELIVERY_NAME_2'),
		'LID' => WIZARD_SITE_ID,
		'PERIOD_FROM' => 3,
		'PERIOD_TO' => 7,
		'PERIOD_TYPE' => 'D',
		'WEIGHT_FROM' => 0,
		'WEIGHT_TO' => 1999,
		'ORDER_PRICE_FROM' => 0,
		'ORDER_PRICE_TO' => 999,
		'ORDER_CURRENCY' => 'RUB',
		'ACTIVE' => 'Y',
		'PRICE' => 15,
		'CURRENCY' => 'RUB',
		'SORT' => 151,
		'DESCRIPTION' => GetMessage('DELIVERY_DISCRIPTION_2'),
		'LOCATIONS' => array(
			array('LOCATION_ID' => $LOCATION_GROUP_ID, 'LOCATION_TYPE' => 'G')
		)
	);
	$ID = CSaleDelivery::Add($arFields);
}

// ___________________________________________________________________________________________ //

// Kurier 2
if($deliver['kurier'] == 'Y')
{
	$arFields = array(
		'NAME' => GetMessage('DELIVERY_NAME_3'),
		'LID' => WIZARD_SITE_ID,
		'PERIOD_FROM' => 7,
		'PERIOD_TO' => 15,
		'PERIOD_TYPE' => 'D',
		'WEIGHT_FROM' => 2000,
		'WEIGHT_TO' => 0,
		'ORDER_PRICE_FROM' => 0,
		'ORDER_PRICE_TO' => 0,
		'ORDER_CURRENCY' => 'RUB',
		'ACTIVE' => 'Y',
		'PRICE' => 55,
		'CURRENCY' => 'RUB',
		'SORT' => 201,
		'DESCRIPTION' => GetMessage('DELIVERY_DISCRIPTION_3'),
		'LOCATIONS' => array(
			array('LOCATION_ID' => $LOCATION_GROUP_ID, 'LOCATION_TYPE' => 'G')
		)
	);
	$ID = CSaleDelivery::Add($arFields);
}

// ___________________________________________________________________________________________ //

// Samovizov
if($deliver['samovizov'] == 'Y')
{
	$arFields = array(
		'NAME' => GetMessage('DELIVERY_NAME_4'),
		'LID' => WIZARD_SITE_ID,
		'PERIOD_FROM' => 0,
		'PERIOD_TO' => 0,
		'PERIOD_TYPE' => 'D',
		'WEIGHT_FROM' => 0,
		'WEIGHT_TO' => 0,
		'ORDER_PRICE_FROM' => 0,
		'ORDER_PRICE_TO' => 0,
		'ORDER_CURRENCY' => 'RUB',
		'ACTIVE' => 'Y',
		'PRICE' => 0,
		'CURRENCY' => 'RUB',
		'SORT' => 251,
		'DESCRIPTION' => GetMessage('DELIVERY_DISCRIPTION_4'),
		'LOCATIONS' => array(
			array('LOCATION_ID' => $LOCATION_GROUP_ID, 'LOCATION_TYPE' => 'G')
		)
	);
	$ID = CSaleDelivery::Add($arFields);
}

// ****************************************************************************************** //
// ******************************************* PAY ****************************************** //
// ****************************************************************************************** //

$personType = $wizard->GetVar('personType');

if($personType['fiz'] == 'Y' )
{
	// Fiz person
	$arFields = array(
		'LID' => WIZARD_SITE_ID,
		'NAME' => GetMessage('PAY_TYPES_PERSON_NAME_1'),
		'SORT' => 101,
	);
	CSalePersonType::Add($arFields);
}

// _________________________________________________________________________ //

// Ur person
if($personType['ur'] == 'Y' )
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,
		'NAME' => GetMessage('PAY_TYPES_PERSON_NAME_2'),
		'SORT' => 201,
	);
	CSalePersonType::Add($arFields);
}

// Загребаем плательщиков (физ. и юр. лицо)
$db_ppers = CSalePersonType::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 101), false, false, array('ID'));
$ppers = $db_ppers->GetNext();
$idPayPers1 = $ppers['ID'];//Физическое лицо
$db_ppers = CSalePersonType::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 201), false, false, array('ID'));
$ppers = $db_ppers->GetNext();
$idPayPers2 = $ppers['ID'];//Юридическое лицо

$personType = $wizard->GetVar('personType');
$paySystems = $wizard->GetVar('paysystem');
$arActionParamWiz = array(
	'shopOfName' => $wizard->GetVar('shopOfName'),
	'shopINN' => $wizard->GetVar('shopINN'),
	'shopAddress' => $wizard->GetVar('shopLocation').', '.$wizard->GetVar('shopAdr'),
	'shopPhone' => $wizard->GetVar('siteTelephoneCode').', '.$wizard->GetVar('siteTelephone'),
	'shopKPP' => $wizard->GetVar('shopKPP'),
	'shopNS' => $wizard->GetVar('shopNS'),
	'shopBANK' => $wizard->GetVar('shopBANK'),
	'shopBANKREKV' => $wizard->GetVar('shopBANKREKV'),
	'shopKS' => $wizard->GetVar('shopKS')
);
// Наличный расчет
if($paySystems['cash'] == 'Y')
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,												// сайт платежной системы;
		'CURRENCY' => '',														// валюта платежной системы;
		'NAME' => GetMessage('PAY_SYSTEMS_NAME_1'),								// название платежной системы;
		'ACTIVE' => 'Y',														// флаг (Y/N) активности платежной системы;
		'SORT' => 101,															// индекс сортировки;
		'DESCRIPTION' => GetMessage('PAY_SYSTEMS_DISCRIPTION_1')				// описание.
	);
	$idPaySys = CSalePaySystem::Add($arFields);

	if($personType['fiz'] == 'Y')
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers1,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_1'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/cash',				// скрипт платежной системы;
			'PARAMS' => '',														// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'N',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}

	if($personType['ur'] == 'Y')
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers2,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_1'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/cash',				// скрипт платежной системы;
			'PARAMS' => '',														// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'N',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}
}
// __________________________________________________________________________________________________________________ //

// Кредитная карта
if($paySystems['cred'] == 'Y')
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,											// сайт платежной системы;
		'CURRENCY' => '',													// валюта платежной системы;
		'NAME' => GetMessage('PAY_SYSTEMS_NAME_2'),							// название платежной системы;
		'ACTIVE' => 'Y',													// флаг (Y/N) активности платежной системы;
		'SORT' => 201,														// индекс сортировки;
		'DESCRIPTION' => GetMessage('PAY_SYSTEMS_DISCRIPTION_2')			// описание.
	);
	$idPaySys = CSalePaySystem::Add($arFields);
	$arActionParams = array(
		'SHOP_IDP' => array('TYPE' => '', 'VALUE' => ''),
		'SHOP_LOGIN' => array('TYPE' => '', 'VALUE' => ''),
		'SHOP_PASSWORD' => array('TYPE' => '', 'VALUE' => ''),
		'SHOP_SECRET_WORLD' => array('TYPE' => '', 'VALUE' => ''),
		'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'SHOULD_PAY'),
		'CURRENCY' => array('TYPE' => 'ORDER', 'VALUE' => 'CURRENCY'),
		'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
		'DATE_INSERT' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
		'SUCCESS_URL' => array('TYPE' => 'ORDER', 'VALUE' => ''),
		'FAIL_URL' => array('TYPE' => 'ORDER', 'VALUE' => ''),
		'FIRST_NAME' => array('TYPE' => 'USER', 'VALUE' => 'NAME'),
		'MIDDLE_NAME' => array('TYPE' => 'USER', 'VALUE' => 'SECOND_NAME'),
		'LAST_NAME' => array('TYPE' => 'USER', 'VALUE' => 'LAST_NAME'),
		'EMAIL' => array('TYPE' => 'PROPERTY', 'VALUE' => 'EMAIL'),
		'ADDRESS' => array('TYPE' => 'PROPERTY', 'VALUE' => 'ADDRESS'),
		'PHONE' => array('TYPE' => 'USER', 'VALUE' => 'PERSONAL_MOBILE'),
		'PAYMENT_CardPayment' => array('TYPE' => '', 'VALUE' => ''),
		'PAYMENT_YMPayment' => array('TYPE' => '', 'VALUE' => ''),
		'PAYMENT_WebMoneyPayment' => array('TYPE' => '', 'VALUE' => ''),
		'PAYMENT_QIWIPayment' => array('TYPE' => '', 'VALUE' => ''),
		'PAYMENT_AssistIDCCPayment' => array('TYPE' => '', 'VALUE' => ''),
		'AUTOPAY' => array('TYPE' => '', 'VALUE' => ''),
		'DEMO' => array('TYPE' => '', 'VALUE' => '')
	);
	if($personType['fiz'] == 'Y')
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers1,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_2'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/assist',				// скрипт платежной системы;
			'PARAMS' => serialize($arActionParams),								// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'N',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}

	// Для юр. лица отсутствует
}
// __________________________________________________________________________________________________________________ //

// Оплата в платежной системе Web Money
if($paySystems['webm'] == 'Y')
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,											// сайт платежной системы;
		'CURRENCY' => '',													// валюта платежной системы;
		'NAME' => GetMessage('PAY_SYSTEMS_NAME_3'),							// название платежной системы;
		'ACTIVE' => 'Y',													// флаг (Y/N) активности платежной системы;
		'SORT' => 301,														// индекс сортировки;
		'DESCRIPTION' => GetMessage('PAY_SYSTEMS_DISCRIPTION_3')			// описание.
	);
	$idPaySys = CSalePaySystem::Add($arFields);
	$arActionParams = array(
		'SHOP_ACCT' => array('TYPE' => '', 'VALUE' => ''),
		'TEST_MODE' => array('TYPE' => '', 'VALUE' => ''),
		'CNST_SECRET_KEY' => array('TYPE' => '', 'VALUE' => ''),
		'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
		'DATE_INSERT' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
		'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'SHOULD_PAY'),
		'RESULT_URL' => array('TYPE' => 'ORDER', 'VALUE' => ''),
		'SUCCESS_URL' => array('TYPE' => 'ORDER', 'VALUE' => ''),
		'FAIL_URL' => array('TYPE' => 'ORDER', 'VALUE' => ''),
		'LMI_PAYER_PHONE_NUMBER' => array('TYPE' => 'USER', 'VALUE' => 'PERSONAL_MOBILE'),
		'LMI_PAYER_EMAIL' => array('TYPE' => 'PROPERTY', 'VALUE' => 'EMAIL'),
		'CHANGE_STATUS_PAY' => array('TYPE' => '', 'VALUE' => '')

	);
	if($personType['fiz'] == 'Y')
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers1,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_3'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/webmoney_web',		// скрипт платежной системы;
			'PARAMS' => serialize($arActionParams),								// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'N',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}
	// Для юр. лица отсутствует
}
// __________________________________________________________________________________________________________________ //

// Оплата в платежной системе Яндекс.Деньги
if($paySystems['yand'] == 'Y')
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,											// сайт платежной системы;
		'CURRENCY' => '',													// валюта платежной системы;
		'NAME' => GetMessage('PAY_SYSTEMS_NAME_4'),							// название платежной системы;
		'ACTIVE' => 'Y',													// флаг (Y/N) активности платежной системы;
		'SORT' => 401,														// индекс сортировки;
		'DESCRIPTION' => GetMessage('PAY_SYSTEMS_DISCRIPTION_4')			// описание.
	);
	$idPaySys = CSalePaySystem::Add($arFields);
	$arActionParams = array(
		'SHOP_ID' => array('TYPE' => '', 'VALUE' => ''),
		'SCID' => array('TYPE' => '', 'VALUE' => ''),
		'SHOP_KEY' => array('TYPE' => '', 'VALUE' => ''),
		'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
		'ORDER_DATE' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
		'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'SHOULD_PAY'),
		'CHANGE_STATUS_PAY' => array('TYPE' => '', 'VALUE' => ''),
		'IS_TEST' => array('TYPE' => '', 'VALUE' => '')

	);
	if($personType['fiz'] == 'Y' )
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers1,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_4'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/yandex',				// скрипт платежной системы;
			'PARAMS' => serialize($arActionParams),								// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'N',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}
	// Для юр. лица отсутствует
}

// __________________________________________________________________________________________________________________ //

// Сбербанк
if($paySystems['sber'] == 'Y')
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,											// сайт платежной системы;
		'CURRENCY' => '',													// валюта платежной системы;
		'NAME' => GetMessage('PAY_SYSTEMS_NAME_5'),							// название платежной системы;
		'ACTIVE' => 'Y',													// флаг (Y/N) активности платежной системы;
		'SORT' => 501,														// индекс сортировки;
		'DESCRIPTION' => GetMessage('PAY_SYSTEMS_DISCRIPTION_5')			// описание.
	);
	$idPaySys = CSalePaySystem::Add($arFields);
	$arActionParams = array(
		'COMPANY_NAME' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopOfName']),
		'INN' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopINN']),
		'KPP' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopKPP']),
		'SETTLEMENT_ACCOUNT' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopNS']),
		'BANK_NAME' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopBANK']),
		'BANK_BIC' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopBANKREKV']),
		'BANK_COR_ACCOUNT' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopKS']),
		'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
		'DATE_INSERT' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
		'PAYER_CONTACT_PERSON' => array('TYPE' => 'PROPERTY', 'VALUE' => 'CONTACT_PERSON'),
		'PAYER_ZIP_CODE' => array('TYPE' => 'PROPERTY', 'VALUE' => 'INDEX'),
		'PAYER_COUNTRY' => array('TYPE' => 'PROPERTY', 'VALUE' => 'LOCATION_COUNTRY'),
		'PAYER_CITY' => array('TYPE' => 'PROPERTY', 'VALUE' => 'LOCATION_CITY'),
		'PAYER_ADDRESS_FACT' => array('TYPE' => 'PROPERTY', 'VALUE' => 'ADDRESS'),
		'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'SHOULD_PAY'),
	);
	if($personType['fiz'] == 'Y')
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers1,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_5'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/sberbank_new',		// скрипт платежной системы;
			'PARAMS' => serialize($arActionParams),														// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'Y',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}

	// Для юр. лица отсутствует
}

// __________________________________________________________________________________________________________________ //

// Счет
if($paySystems['scht'] == 'Y')
{
	$arFields = array(
		'LID' => WIZARD_SITE_ID,											// сайт платежной системы;
		'CURRENCY' => '',													// валюта платежной системы;
		'NAME' => GetMessage('PAY_SYSTEMS_NAME_6'),							// название платежной системы;
		'ACTIVE' => 'Y',													// флаг (Y/N) активности платежной системы;
		'SORT' => 601,														// индекс сортировки;
		'DESCRIPTION' => GetMessage('PAY_SYSTEMS_DISCRIPTION_6')			// описание.
	);
	$idPaySys = CSalePaySystem::Add($arFields);
	$arActionParams = array(
		'DATE_INSERT' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
		'SELLER_NAME' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopOfName']),
		'SELLER_ADDRESS' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopAddress']),
		'SELLER_PHONE' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopPhone']),
		'SELLER_INN' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopINN']),
		'SELLER_KPP' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopKPP']),
		'SELLER_RS' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopNS']),
		'SELLER_KS' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopKS']),
		'SELLER_BIK' => array('TYPE' => '', 'VALUE' => $arActionParamWiz['shopBANKREKV']),
		'BUYER_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => 'F_COMPANY_NAME'),
		'BUYER_INN' => array('TYPE' => 'PROPERTY', 'VALUE' => 'UR_INN'),
		'BUYER_ADDRESS' => array('TYPE' => 'PROPERTY', 'VALUE' => 'F_ADDRESS_FULL'),
		'BUYER_PHONE' => array('TYPE' => 'PROPERTY', 'VALUE' => 'F_PHONE'),
		'BUYER_FAX' => array('TYPE' => 'PROPERTY', 'VALUE' => 'F_FAX'),
		'BUYER_PAYER_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => 'F_NAME'),
		'PATH_TO_STAMP' => array('TYPE' =>  '', 'VALUE' => '')
	);

	// Для физ. лица отсутствует
	if($personType['ur'] == 'Y')
	{
		$arActionFields = array(
			'LID' => WIZARD_SITE_ID,											// Код сайта
			'PAY_SYSTEM_ID' => $idPaySys,										// код платежной системы;
			'PERSON_TYPE_ID' => $idPayPers2,									// код типа плательщика;
			'NAME' => GetMessage('PAY_SYSTEMS_NAME_6'),							// название платежной системы;
			'ACTION_FILE' => '/bitrix/modules/sale/payment/bill',				// скрипт платежной системы;
			'PARAMS' => serialize($arActionParams),								// параметры платежной системы
			'HAVE_PAYMENT' => 'Y',
			'RESULT_FILE' => '',												// скрипт получения результатов;
			'NEW_WINDOW' => 'Y',												// флаг (Y/N) открывать ли скрипт платежной системы в новом окне
		);
		CSalePaySystemAction::Add($arActionFields);
	}
}

// ****************************************************************************************** //
// ************************************** SALE PROPERTIES *********************************** //
// ****************************************************************************************** //

// Загребаем плательщиков (физ. и юр. лицо)
$db_ppers = CSalePersonType::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 101), false, false, array('ID'));
$ppers = $db_ppers->GetNext();
$idPayPers1 = $ppers['ID'];//Физическое лицо
$db_ppers = CSalePersonType::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 201), false, false, array('ID'));
$ppers = $db_ppers->GetNext();
$idPayPers2 = $ppers['ID'];//Юридическое лицо

$personType = $wizard->GetVar('personType');

if($personType['fiz'] == 'Y' )
{
	// add props group
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_GROUP_NAME1'),			// название группы (группа привязывается к типу плательщика, тип плательщика привязывается к сайту, сайт привязывается к языку, название задается на этом языке);
		'SORT' => 101,											// индекс сортировки.
	);
	$idPropsGroup1 = CSaleOrderPropsGroup::Add($arFields);
}

// __________________________________________________________________________________________________________________ //

if($personType['fiz'] == 'Y' )
{
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_GROUP_NAME2'),			// название группы (группа привязывается к типу плательщика, тип плательщика привязывается к сайту, сайт привязывается к языку, название задается на этом языке);
		'SORT' => 201,											// индекс сортировки.
	);
	$idPropsGroup2 = CSaleOrderPropsGroup::Add($arFields);
}

// __________________________________________________________________________________________________________________ //

if($personType['ur'] == 'Y' )
{
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_GROUP_NAME3'),			// название группы (группа привязывается к типу плательщика, тип плательщика привязывается к сайту, сайт привязывается к языку, название задается на этом языке);
		'SORT' => 301,											// индекс сортировки.
	);
	$idPropsGroup3 = CSaleOrderPropsGroup::Add($arFields);
}

// __________________________________________________________________________________________________________________ //
// __________________________________________________________________________________________________________________ //
// __________________________________________________________________________________________________________________ //

// загребаем коды групп свойств
if(empty($idPropsGroup1) || $idPropsGroup1<1)
{
	$db_PropsGr = CSaleOrderPropsGroup::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 101), false, false, array('ID'));
	$PrGr = $db_PropsGr->GetNext();
	$idPropsGroup1 = $PrGr['ID'];//Адрес доставки (физ. лицо)
}
if(empty($idPropsGroup2) || $idPropsGroup2<1)
{
	$db_PropsGr = CSaleOrderPropsGroup::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 201), false, false, array('ID'));
	$PrGr = $db_PropsGr->GetNext();
	$idPropsGroup2 = $PrGr['ID'];//Комплектация (физ. лицо)
}
if(empty($idPropsGroup3) || $idPropsGroup3<1)
{
	$db_PropsGr = CSaleOrderPropsGroup::GetList(array(), array('LID' => WIZARD_SITE_ID, 'SORT' => 301), false, false, array('ID'));
	$PrGr = $db_PropsGr->GetNext();
	$idPropsGroup3 = $PrGr['ID'];//Адрес доставки (юр. лицо)
}

//add props

if($personType['fiz'] == 'Y' )
{
	// Физическое лицо - Местоположение
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_2'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'LOCATION',									// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 200,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup1,						// код группы свойств;
		'SIZE1' => 3,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'Y',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'LOCATION',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Физическое лицо - Индекс
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_4'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 300,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup1,						// код группы свойств;
		'SIZE1' => 8,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'INDEX',										// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Физическое лицо - Адрес (без города)
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_5'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXTAREA',									// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 400,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup1,						// код группы свойств;
		'SIZE1' => 30,											// ширина поля (размер по горизонтали);
		'SIZE2' => 2,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'ADDRESS',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Физическое лицо - E-Mail
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_6'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 500,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup1,						// код группы свойств;
		'SIZE1' => 40,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'Y',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'EMAIL',										// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Физическое лицо - E-Mail
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers1,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_7'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 600,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup1,						// код группы свойств;
		'SIZE1' => 40,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'Y',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'Y',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'CONTACT_PERSON',								// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);
} // end if($personType['fiz'] == 'Y' )
// __________________________________________________________________________________________________________________ //

// Юридическое лицо - Местоположение
if($personType['ur'] == 'Y' )
{
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_3'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'LOCATION',									// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 700,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 3,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'Y',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'Y',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_LOCATION',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - Юридический адрес
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_8'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXTAREA',									// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 800,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 40,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_ADDRESS_FULL',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - E-Mail
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_9'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 900,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 40,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'Y',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_EMAIL',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - Название компании
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_10'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1000,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 40,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'Y',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_COMPANY_NAME',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - Телефон
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_11'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1100,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 0,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_PHONE',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - Контактное лицо
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_12'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1200,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 0,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'Y',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_NAME',										// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - Факс
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_13'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1300,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 0,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_FAX',										// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - Адрес доставки
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_14'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1400,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 0,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_ADDRESS',									// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - ИНН
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_15'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1500,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 0,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'UR_INN',										// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);

	// Юридическое лицо - КПП
	$arFields = array(
		'PERSON_TYPE_ID' => $idPayPers2,						// тип плательщика;
		'NAME' => GetMessage('SALE_PROPS_NAME_16'),				// название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
		'TYPE' => 'TEXT',										// тип свойства.
		'REQUIED' => 'Y',										// флаг (Y/N) обязательное ли поле;
		'DEFAULT_VALUE' => '',									// значение по умолчанию;
		'SORT' => 1600,											// индекс сортировки;
		'USER_PROPS' => 'Y',									// флаг (Y/N) входит ли это свойство в профиль покупателя;
		'PROPS_GROUP_ID' => $idPropsGroup3,						// код группы свойств;
		'SIZE1' => 0,											// ширина поля (размер по горизонтали);
		'SIZE2' => 0,											// высота поля (размер по вертикали);
		'DESCRIPTION' => '',									// описание свойства;
		'IS_LOCATION' => 'N',									// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
		'IS_EMAIL' => 'N',										// флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
		'IS_PROFILE_NAME' => 'N',								// флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
		'IS_PAYER' => 'N',										// флаг (Y/N) использовать ли значение свойства как имя плательщика;
		'IS_LOCATION4TAX' => 'N',								// флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);
		'CODE' => 'F_KPP',										// мнемонический код свойства.
		'IS_FILTERED' => 'N',									// свойство доступно в фильтре по заказам.
	);
	CSaleOrderProps::Add($arFields);
}// end if($personType['ur'] == 'Y' )