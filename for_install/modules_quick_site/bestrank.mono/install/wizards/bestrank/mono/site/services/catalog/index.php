<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$curSiteSubscribe = array("use" => "Y", "del_after" => "100");
$subscribe = COption::GetOptionString("sale", "subscribProd", "");
$arSubscribe = unserialize($subscribe);
$arSubscribe[WIZARD_SITE_ID] = $curSiteSubscribe;
COption::SetOptionString("sale", "subscribProd", serialize($arSubscribe));

	
COption::SetOptionString("catalog", "allow_negative_amount", "N");
COption::SetOptionString("catalog", "default_can_buy_zero", "Y");
COption::SetOptionString("catalog", "default_quantity_trace", "Y");

if(CModule::IncludeModule("currency")){
	$arFields = array(
		"FORMAT_STRING" => "# ".GetMessage("CURRENCY_RUB"),  
		"FULL_NAME" => GetMessage("CURRENCY_RUB_FULL"),  
		"DEC_POINT" => ".",
		"THOUSANDS_SEP" => "", 
		"DECIMALS" => 0,
		"CURRENCY" => "RUB",
		"LID" => "ru"
	);
	
	$db_result_lang = CCurrencyLang::GetByID("RUB", "ru");
	if ($db_result_lang)
		CCurrencyLang::Update("RUB", "ru", $arFields);
	else
		CCurrencyLang::Add($arFields);
}

 
?>