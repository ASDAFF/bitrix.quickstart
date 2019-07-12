<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="elementPage">
<div class="wrapleft">
<?$ElementID = $APPLICATION->IncludeComponent(
	"mlife:asz.multicatalog.element",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "Y",
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"PRICE" => $arParams["PRICE"],
		"HIDE_BY" => "Y",
		"HIDE_QUANT" => "Y",
		"ZAKAZ" => $arParams["ZAKAZ"],
		"PROPERTY_CODE_LABEL" => $arParams["PROPERTY_CODE_LABEL"],
		"TOVAR_DAY" => $arParams["TOVAR_DAY"],
	),
$component
);?>
</div>
</div>
