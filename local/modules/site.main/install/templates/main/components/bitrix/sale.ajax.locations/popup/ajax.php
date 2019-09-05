<?
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$response = array();

if (\Bitrix\Main\Loader::includeModule('sale')
	&& !empty($_REQUEST['search']) && is_string($_REQUEST['search'])
) {
	$search = array_shift(explode(', ', $APPLICATION->UnJSEscape($_REQUEST['search'])));
	$siteId = trim($_REQUEST['SITE_ID']);
	
	$filter = \Bitrix\Sale\SalesZone::makeSearchFilter('city', $siteId);
	$filter['~CITY_NAME'] = $search . '%';
	$filter['LID'] = LANGUAGE_ID;
	$cities = CSaleLocation::GetList(
		array(
			'CITY_NAME_LANG' => 'ASC',
			'COUNTRY_NAME_LANG' => 'ASC',
			'SORT' => 'ASC',
		),
		$filter,
		false,
		array(
			'nTopCount' => 10
		),
		array(
			'ID', 'CITY_ID', 'CITY_NAME', 'COUNTRY_NAME_LANG', 'REGION_NAME_LANG'
		)
	);
	while ($city = $cities->GetNext()) {
		$response[] = array(
			'ID' => $city['ID'],
			'NAME' => $city['CITY_NAME'],
			'REGION_NAME' => $city['REGION_NAME_LANG'],
			'COUNTRY_NAME' => $city['COUNTRY_NAME_LANG'],
		);
	}
	
	$filter = \Bitrix\Sale\SalesZone::makeSearchFilter('region', $siteId);
	$filter['~REGION_NAME'] = $search . '%';
	$filter['LID'] = LANGUAGE_ID;
	$filter['CITY_ID'] = false;
	$regions = CSaleLocation::GetList(
		array(
			'CITY_NAME_LANG' => 'ASC',
			'COUNTRY_NAME_LANG' => 'ASC',
			'SORT' => 'ASC',
		),
		$filter,
		false,
		array(
			'nTopCount' => 10
		),
		array(
			'ID', 'CITY_ID', 'CITY_NAME', 'COUNTRY_NAME_LANG', 'REGION_NAME_LANG'
		)
	);
	while ($region = $regions->GetNext()) {
		$response[] = array(
			'ID' => $region['ID'],
			'NAME' => '',
			'REGION_NAME' => $region['REGION_NAME_LANG'],
			'COUNTRY_NAME' => $region['COUNTRY_NAME_LANG'],
		);
	}
	
	$filter = \Bitrix\Sale\SalesZone::makeSearchFilter('country', $siteId);
	$filter['~COUNTRY_NAME'] = $search . '%';
	$filter['LID'] = LANGUAGE_ID;
	$filter['CITY_ID'] = false;
	$filter['REGION_ID'] = false;
	$countries = CSaleLocation::GetList(
		array(
			'COUNTRY_NAME_LANG' => 'ASC',
			'SORT' => 'ASC',
		),
		$filter,
		false,
		array(
			'nTopCount' => 10
		),
		array(
			'ID', 'COUNTRY_NAME_LANG'
		)
	);
	while ($country = $countries->GetNext()) {
		$response[] = array(
			'ID' => $country['ID'],
			'NAME' => '',
			'REGION_NAME' => '',
			'COUNTRY_NAME' => $country['COUNTRY_NAME_LANG'],
		);
	}
}

$view = new \Site\Main\Mvc\View\Json($response);
$view->sendHeaders();
print $view->render();

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';