<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!cmodule::includeModule('ipol.sdek'))
	return false;

$allCities = false;
if(!is_array($arParams['CITIES']))
	$arParams['CITIES'] = array();
if(count($arParams['CITIES'])==0)
	$allCities=true;

$propAddr = Coption::GetOptionString(CDeliverySDEK::$MODULE_ID,'pvzPicker','');//определяем инпуты, куда писать адреса
$props = CSaleOrderProps::GetList(array(),array('CODE' => $propAddr));
$propAddr='';
while($prop=$props->Fetch())
	$propAddr.=$prop['ID'].',';

$arResult['propAddr'] = $propAddr;
$arResult['Regions'] = array();

if($_SESSION['IPOLSDEK_city'] && !count($arParams['CITIES'])==1)
	$arResult['city']=$_SESSION['IPOLSDEK_city'];
elseif(count($arParams['CITIES'])==1)
	$arResult['city']=$arParams['CITIES'][0];
elseif(!$arParams['COUNTRIES'] || in_array('rus',$arParams['COUNTRIES']))
	$arResult['city']=GetMessage('IPOLSDEK_MOSCOW');

$countries = CDeliverySDEK::getActiveCountries();
$arExistedCities = array();
$cities = CDeliverySDEK::getCountryCities($countries);
foreach($cities as $city){
	if(!array_key_exists($city['NAME'],$arExistedCities))
		$arExistedCities[$city['NAME']] = $city['COUNTRY_NAME'];
	elseif(is_array($arExistedCities[$city['NAME']]))
		$arExistedCities[$city['NAME']][] = $city['COUNTRY_NAME'];
	elseif($arExistedCities[$city['NAME']] != $city['COUNTRY_NAME'])
		$arExistedCities[$city['NAME']] = array($arExistedCities[$city['NAME']],$city['COUNTRY_NAME']);
	if(!array_key_exists('city',$arResult) || !$arResult['city'])
		$arResult['city'] = $city['NAME'];
}

$countrySwitcher = array();
foreach($countries as $code)
	$countrySwitcher[GetMessage('IPOLSDEK_SYNCTY_'.$code)] = $code;

if($arParams['CNT_DELIV'] == 'Y'){
	$goods = ($arParams['CNT_BASKET'] == 'Y') ? CDeliverySDEK::setOrderGoods() : false;

	// Old templates
	$arResult['ORDER'] = array(
		'WEIGHT' => (CDeliverySDEK::$goods['W'])*1000,
		'PRICE'  => CDeliverySDEK::$orderPrice,
		'GOODS'  => array(array(
			"WEIGHT"     => (CDeliverySDEK::$goods['W'])*1000,
			"QUANTITY"   => 1,
			"DIMENSIONS" => array(
				"WIDTH"  => (CDeliverySDEK::$goods['D_W'])*10,
				"HEIGHT" => (CDeliverySDEK::$goods['D_H'])*10,
				"LENGTH" => (CDeliverySDEK::$goods['D_L'])*10
			),
		)),
	);
	$tmpShort = $arResult['ORDER']['GOODS'][0];
	$arResult['ORDER']['GOODS_js'] = "[{WEIGHT:'{$tmpShort['WEIGHT']}',QUANTITY:1,DIMENSIONS:{WIDTH:'{$tmpShort['DIMENSIONS']['WIDTH']}',HEIGHT:'{$tmpShort['DIMENSIONS']['HEIGHT']}',LENGTH:'{$tmpShort['DIMENSIONS']['LENGTH']}'}}]";

	// New templates
	if($goods)
		$arResult['ORDER']['GOODS'] = $goods;

	$arResult['DELIVERY'] = CDeliverySDEK::countDelivery(array(
		'CITY_TO'   => CDeliverySDEK::zajsonit($arResult['city']),
		'WEIGHT'    => (CDeliverySDEK::$goods['W'])*1000,
		'PRICE'     => CDeliverySDEK::$orderPrice,
		'FORBIDDEN' => $arParams['FORBIDDEN'],
		'GOODS'	    => $goods
	));
}

if(!(count($arParams['CITIES'])==1 && $arResult['DELIVERY']['pickup'] == 'no')){
	$arList = CDeliverySDEK::getListFile();
	$arList['PVZ'] = CDeliverySDEK::wegihtPVZ((CDeliverySDEK::$orderWeight)?false:COption::GetOptionString(CDeliverySDEK::$MODULE_ID,'weightD',1000),$arList['PVZ']);
}

if(count($arList)){
	foreach($arList as $mode => $arCities)
		foreach($arCities as $city => $arPVZ){
			if(array_key_exists($city,$arExistedCities) && (!$arParams['COUNTRIES'] || is_array($countrySwitcher[$arExistedCities[$city]]) || in_array($countrySwitcher[$arExistedCities[$city]],$arParams['COUNTRIES']))){
				if($allCities || in_array($city,$arParams['CITIES'])){
					$arResult[$mode][$city] = $arPVZ;
					if(!in_array($city,$arResult['Regions'])){
						$country = (!is_array($arExistedCities[$city])) ? $arExistedCities[$city] : ((in_array(GetMessage('IPOLSDEK_SYNCTY_rus'),$arExistedCities[$city])) ? GetMessage('IPOLSDEK_SYNCTY_rus') : $arExistedCities[$city][0]);
						$arResult['Regions'][]=$city;
						$arResult['Subjects'][$city]=$country;
					}
				}
			}
		}
}

$this->IncludeComponentTemplate();
?>