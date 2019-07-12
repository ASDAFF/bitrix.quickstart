<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

//получаем скидки
$discount = \Mlife\Asz\DiscountTable::getList(
	array(
		'select' => array('ID','NAME'),
		'filter' => array("ACTIVE"=>"Y"),
		'limit' => 200,
	)
);
$arDiscount = array();
while($arDiscountdb = $discount->Fetch()){
	$arDiscount[$arDiscountdb["ID"]] = $arDiscountdb["NAME"];
}

$arTemplateParameters["TOVAR_DAY"] = array(
			'NAME' => GetMessage("MLIFE_ASZ_CATALOG_TOVAR_DAY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arDiscount,
			"PARENT" => "ADDITIONAL_SETTINGS",
		);