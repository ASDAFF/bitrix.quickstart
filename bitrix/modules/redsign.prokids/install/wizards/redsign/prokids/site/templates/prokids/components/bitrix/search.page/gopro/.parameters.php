<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arDFParamsCatalog = RSDevFuncParameters::GetTemplateParamsCatalog($arCurrentValues);
foreach($arDFParamsCatalog as $PNAME => $arParam)
{
	$arTemplateParameters[$PNAME] = $arParam;
}