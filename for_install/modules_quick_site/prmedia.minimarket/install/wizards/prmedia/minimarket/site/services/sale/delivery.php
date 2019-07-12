<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!CModule::IncludeModule('sale'))
{
	return;
}
if (COption::GetOptionString($moduleId, 'wizard_installed', 'N', WIZARD_SITE_ID) == 'Y' && !WIZARD_INSTALL_DEMO_DATA)
{
	return;
}

$moduleId = 'prmedia.minimarket';

$defaultCurrency = 'RUB';
$lang = 'ru';
$rsSite = CSite::GetByID(WIZARD_SITE_ID);
if ($site = $rsSite->Fetch())
{
	$lang = $site['LANGUAGE_ID'];
}
if (empty($lang))
{
	$lang = 'ru';
}

WizardServices::IncludeServiceLang('step2.php', $lang);

$locationGroupId = 0;
$deliveryLocations = Array();
$locations = array();

$locationParams = array(
	'filter' => array(
		'LID' => $lang
	)
);
$rsLocation = CSaleLocation::GetList(false, $locationParams['filter']);
while ($location = $rsLocation->Fetch())
{
	$deliveryLocations[] = array(
		'LOCATION_ID' => $location['ID'],
		'LOCATION_TYPE' => 'L'
	);
	$locations[] = $location['ID'];
}


$rsLocationGroup = CSaleLocationGroup::GetList();
if ($locationGroup = $rsLocationGroup->Fetch())
{
	$deliveryLocations[] = array(
		'LOCATION_ID' => $locationGroup['ID'],
		'LOCATION_TYPE' => 'G'
	);
}
else
{
	$groupLang = array(
		array(
			'LID' => $lang,
			'NAME' => GetMessage('SALE_WIZARD_GROUP')
		)
	);
	$locationFields = array(
		'SORT' => 150,
		'LOCATION_ID' => $locations,
		'LANG' => $groupLang
	);
	$locationGroupId = CSaleLocationGroup::Add($locationFields);
}
if ($locationGroupId > 0)
{
	$deliveryLocations[] = array(
		'LOCATION_ID' => $locationGroupId,
		'LOCATION_TYPE' => 'G'
	);
}

$delivery = $wizard->GetVar('delivery');

// update deliveries if exists
$deliveryParams = array(
	'filter' => array(
		'LID' => WIZARD_SITE_ID
	)
);
$rsDelivery = CSaleDelivery::GetList(false, $deliveryParams['filter']);
while ($deliveryFields = $rsDelivery->Fetch())
{
	if ($deliveryFields['NAME'] == GetMessage('SALE_WIZARD_COUR'))
	{
		$deliveryFields['ACTIVE'] = $delivery['courier'] == 'Y' ? 'Y' : 'N';
		CSaleDelivery::Update($deliveryFields['ID'], $deliveryFields);
		$delivery['courier'] = 'installed';
	}
	if ($deliveryFields['NAME'] == GetMessage('SALE_WIZARD_COUR1'))
	{
		$deliveryFields['ACTIVE'] = $delivery['self'] == 'Y' ? 'Y' : 'N';
		CSaleDelivery::Update($deliveryFields['ID'], $deliveryFields);
		$delivery['self'] = 'installed';
	}
}

// add deliveries if not exists
if ($delivery['courier'] != 'installed')
{
	$deliveryFields = array(
		'NAME' => GetMessage('SALE_WIZARD_COUR'),
		'LID' => WIZARD_SITE_ID,
		'PERIOD_FROM' => 0,
		'PERIOD_TO' => 0,
		'PERIOD_TYPE' => 'D',
		'WEIGHT_FROM' => 0,
		'WEIGHT_TO' => 0,
		'ORDER_PRICE_FROM' => 0,
		'ORDER_PRICE_TO' => 0,
		'ORDER_CURRENCY' => $defaultCurrency,
		'ACTIVE' => 'Y',
		'PRICE' => '500',
		'CURRENCY' => $defaultCurrency,
		'SORT' => 100,
		'DESCRIPTION' => GetMessage('SALE_WIZARD_COUR_DESCR'),
		'LOCATIONS' => $deliveryLocations,
	);
	if ($delivery['courier'] != 'Y')
	{
		$deliveryFields['ACTIVE'] = 'N';
	}
	CSaleDelivery::Add($deliveryFields);
}
if ($delivery['self'] != 'installed')
{
	$deliveryFields = Array(
		'NAME' => GetMessage('SALE_WIZARD_COUR1'),
		'LID' => WIZARD_SITE_ID,
		'PERIOD_FROM' => 0,
		'PERIOD_TO' => 0,
		'PERIOD_TYPE' => 'D',
		'WEIGHT_FROM' => 0,
		'WEIGHT_TO' => 0,
		'ORDER_PRICE_FROM' => 0,
		'ORDER_PRICE_TO' => 0,
		'ORDER_CURRENCY' => $defaultCurrency,
		'ACTIVE' => 'Y',
		'PRICE' => 0,
		'CURRENCY' => $defaultCurrency,
		'SORT' => 200,
		'DESCRIPTION' => GetMessage('SALE_WIZARD_COUR1_DESCR'),
		'LOCATIONS' => $deliveryLocations,
	);
	if ($delivery['self'] != 'Y')
	{
		$arFields['ACTIVE'] = 'N';
	}
	CSaleDelivery::Add($deliveryFields);
}