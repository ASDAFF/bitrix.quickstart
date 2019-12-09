<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$arDetParams = array(
	"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
	"PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
	"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
	"SET_TITLE" =>$arParams["SET_TITLE"],
	"ID" => $arResult["VARIABLES"]["ID"],
	"ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],

	"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

	"CUSTOM_SELECT_PROPS" => $arParams["CUSTOM_SELECT_PROPS"]
);
foreach($arParams as $key => $val)
{
	if(strpos($key, "PROP_") !== false
		|| strpos($key, 'ADDITIONAL_PICT_PROP_') !== false && strpos($key, '~') !== 0
		|| strpos($key, 'ARTICLE_PROP_') !== false && strpos($key, '~') !== 0
		|| strpos($key, 'OFFER_TREE_PROPS_') !== false && strpos($key, '~') !== 0)
	{
		$arDetParams[$key] = $val;
	}
}

$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.detail",
	"al",
	$arDetParams,
	$component
);
