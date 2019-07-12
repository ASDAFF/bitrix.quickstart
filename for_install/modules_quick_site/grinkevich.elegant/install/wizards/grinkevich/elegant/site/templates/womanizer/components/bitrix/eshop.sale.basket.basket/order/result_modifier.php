<?
CModule::IncludeModule("iblock");

foreach($arResult[ITEMS][AnDelCanBuy] as $key=>$arBasket){
	
	$arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_PRICE_ENUM"] = $arBasket[PRICE] * $arBasket[QUANTITY];
	if($arBasket[DISCOUNT_PRICE] > 0){
		$arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_DISCOUNT_ENUM"] = ($arBasket[PRICE] * $arBasket[QUANTITY]) - ($arBasket[DISCOUNT_PRICE] * $arBasket[QUANTITY]);
	}else{
		$arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_DISCOUNT_ENUM"] = 0;
	}
	
	$arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_DISCOUNT"] = FormatCurrency($arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_DISCOUNT_ENUM"], "RUB");
	$arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_PRICE"] = FormatCurrency($arResult[ITEMS][AnDelCanBuy][$key]["TOTAL_PRICE_ENUM"], "RUB");
}

?>
