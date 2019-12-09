<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;
	
$arResult = RSDevFuncResultModifier::SearchPage($arResult);

//echo"<pre>";print_r($arResult);echo"</pre>";