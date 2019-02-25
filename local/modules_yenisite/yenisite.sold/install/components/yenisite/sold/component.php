<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ? intval($arParams['CACHE_TIME']) : 3600000;
	
	if($this->StartResultCache(false, $USER->GetGroups()))
	{
		if(CModule::IncludeModule("sale"))
		{
			$arResult = array();
			$arResult["GOODS"] = 0;
			$arResult["PRICE"] = 0;
			$arResult["ORDERS"] = 0;

			$orders = CSaleOrder::GetList();
			while($arOrders = $orders->Fetch())
			{
				$arResult["PRICE"] += $arOrders["PRICE"];
				$arResult["ORDERS"] += 1;
				$arResult["GOODS"] += CSaleBasket::GetList(
				   array(),
				   array( 
					  "LID" => SITE_ID,
					  "ORDER_ID" => $arOrders["ID"]
				   ), 
				   array()
				);
			}
		}
		
		if(CModule::IncludeModule("currency"))
		{
			$curr = CCurrency::GetBaseCurrency();
			$arResult["PRICE"] = CCurrencyRates::ConvertCurrency($arResult["PRICE"], $curr, $arParams["CURRENCY"]);			
			$arResult["PRICE"] = CCurrencyLang::CurrencyFormat($arResult["PRICE"], $arParams["CURRENCY"], true);
		}
			
		$this->IncludeComponentTemplate();
	}
?>