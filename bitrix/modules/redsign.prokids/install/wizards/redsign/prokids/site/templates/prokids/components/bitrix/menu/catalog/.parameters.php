<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arTemplateParameters = array(
	"RSGOPRO_CATALOG_PATH" => array(
		"NAME" => GetMessage("RSGOPRO_CATALOG_PATH"),
		"TYPE" => "STRING",
		"DEFAULT" => "/catalog/",
	),
	"RSGOPRO_MAX_ITEM" => array(
		"NAME" => GetMessage("RSGOPRO_MAX_ITEM"),
		"TYPE" => "STRING",
		"DEFAULT" => "9",
	),
	"RSGOPRO_IS_MAIN" => array(
		"NAME" => GetMessage("RSGOPRO_IS_MAIN"),
		"TYPE" => "STRING",
		"DEFAULT" => "N",
	),
	"RSGOPRO_PROPCODE_ELEMENT_IN_MENU" => array(
		"NAME" => GetMessage("RSGOPRO_PROPCODE_ELEMENT_IN_MENU"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
);

$arDFParamsCatalog = RSDevFuncParameters::GetTemplateParamsCatalog($arCurrentValues);
foreach($arDFParamsCatalog as $PNAME => $arParam)
{
	$arTemplateParameters[$PNAME] = $arParam;
}