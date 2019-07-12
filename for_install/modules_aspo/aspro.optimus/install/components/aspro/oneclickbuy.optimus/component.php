<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if(intval($arParams['CACHE_TIME']) < 0){
	$arParams['CACHE_TIME'] = 36000;
}
if(!strlen($arParams['DEFAULT_CURRENCY'])){
	$arParams['DEFAULT_CURRENCY'] = COption::GetOptionString('sale', 'default_currency', 'RUB');
}
if(empty($arParams['PROPERTIES'])){
	$arParams['PROPERTIES'] = array('FIO', 'PHONE');
}
if(empty($arParams['REQUIRED'])){
	$arParams['REQUIRED'] = array('FIO', 'PHONE');
}
$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);

$arResult = array(
	'ERRORS' => array(),
	'SCRIPT_PATH' => $this->{'__path'},
	'USER_ID' => false,
	'USER_FIO' => '',
	'USER_EMAIL' => '',
	'USER_PHONE' => '',
);

global $USER;
if($arResult['USER_ID'] = $USER->GetID()){
	$dbUser = $USER->GetByID($arResult['USER_ID']);
	$arUser = $dbUser->Fetch();
	$arResult['USER_FIO'] = $USER->GetFullName();
	$arResult['USER_EMAIL'] = $USER->GetEmail();
	$arResult['USER_PHONE'] = $arUser['PERSONAL_PHONE'];
}

if($this->StartResultCache()){
	CModule::IncludeModule('sale');
	$arProps = array();
	$rsProps = CSaleOrderProps::GetList(array(), array("CODE" => $arParams["PROPERTIES"], "PERSON_TYPE_ID" => $arParams["DEFAULT_PERSON_TYPE"]));
    while($arProp = $rsProps->Fetch()){
    	if(strlen($arProp["CODE"]) && $arProp["TYPE"] === "TEXT"){
	    	$arProps[$arProp["CODE"]] = $arProp["NAME"];
		}
    }
    $arResult["PROPS"] = $arProps;

	$this->IncludeComponentTemplate();
}
?>