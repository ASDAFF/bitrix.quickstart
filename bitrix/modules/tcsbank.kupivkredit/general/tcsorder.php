<?
	class CTCSOrder extends CTCSOrderAll
	{
		var $LAST_ERROR;
		var $bDebug;
		function CTCSOrder()
		{
			$this->LAST_ERROR = "";
			$this->bDebug=true;
		}
		function GetDefaultStatus()
		{
			return "new";
		}
	
		function GetItemsArray($iOrderID)
		{
			$arReturn = Array(
				"ID"=>$iOrderID,
				"TOTAL_SUMM_RUB"=>0,
				"TOTAL_SUMM_RUB_FORMATTED"=>0,
				"ITEMS"=>Array()
			);
			$obBank = new CTCSBank;
			if($iOrderID = IntVal($iOrderID))
			{
				$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>$iOrderID),false,false,Array("DELIVERY_ID","PRICE_DELIVERY","CURRENCY","LID"));
				if($arOrder = $obOrder->Fetch())
				{
					$arReturn["LID"]=$arOrder["LID"];
					$obItems = CSaleBasket::GetList(Array("ID"=>"ASC"),Array("ORDER_ID"=>$iOrderID),false,false,Array("ID","NAME","QUANTITY","PRICE","PRODUCT_ID","CURRENCY"));
					$arItems = Array();
					$iTotalSumm = 0;
					$iTotalTCSSumm = 0;
					while($arItem = $obItems->Fetch())
					{
						$arItem["PRICE_RUB"] = CCurrencyRates::ConvertCurrency($arItem["PRICE"], $arItem["CURRENCY"],"RUB");
						$arItem["PRICE_RUB_FORMATTED"] = CurrencyFormat($arItem["PRICE_RUB"], "RUB");
						$arItem["TCS_PRICE_RUB"] = $obBank->Round($arItem["PRICE_RUB"],$arOrder["LID"]);
						$arItem["TCS_QUANTITY"] = ceil($arItem["QUANTITY"]);
						
						$iTotalSumm+=$arItem["PRICE_TOTAL_RUB"]=$arItem["PRICE_RUB"]*$arItem["QUANTITY"];
						$iTotalTCSSumm+=$arItem["TCS_PRICE_TOTAL_RUB"] = $arItem["TCS_PRICE_RUB"]*$arItem["TCS_QUANTITY"];
						$arItem["TCS_PRICE_TOTAL_RUB_FORMATTED"] = CurrencyFormat($arItem["PRICE_TOTAL_RUB"], "RUB");
						$arItem["PRICE_TOTAL_RUB_FORMATTED"] = CurrencyFormat($arItem["PRICE_TOTAL_RUB"], "RUB");
						$arItems[] = $arItem;
					}
					if($arOrder["DELIVERY_ID"] && FloatVal($arOrder["PRICE_DELIVERY"])>0)
					{
						$sDeliveryName = "";
						if(IntVal($arOrder["DELIVERY_ID"])<=0)
						{
							$exp = explode(":",$arOrder["DELIVERY_ID"]);
							$rsDelivery = CSaleDeliveryHandler::GetBySID($exp[0]);
							$arDelivery = $rsDelivery->Fetch();
							$sDeliveryName = $arDelivery["NAME"]." - ".$arDelivery["PROFILES"][$exp[1]]["TITLE"];
						}
						else
						{
							$arDelivery = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
							$sDeliveryName = $arDelivery["NAME"];
						}					
						$arItem["NAME"] = $sDeliveryName;
						$arItem["QUANTITY"] = "1.00";
						
						$arItem["PRICE_RUB"] = CCurrencyRates::ConvertCurrency($arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"],"RUB"); 
						$arItem["PRICE_RUB_FORMATTED"] = CurrencyFormat($arItem["PRICE_RUB"], "RUB");
						$arItem["TCS_PRICE_RUB"] = $obBank->Round($arItem["PRICE_RUB"],$arOrder["LID"]);
						$arItem["TCS_QUANTITY"] = ceil($arItem["QUANTITY"]);						
						$iTotalTCSSumm+=$arItem["TCS_PRICE_TOTAL_RUB"] = $arItem["TCS_PRICE_RUB"]*$arItem["TCS_QUANTITY"];
						$iTotalSumm+=$arItem["PRICE_TOTAL_RUB"]=$arItem["PRICE_RUB"]*$arItem["QUANTITY"];
						$arItem["PRICE_TOTAL_RUB_FORMATTED"] = CurrencyFormat($arItem["PRICE_TOTAL_RUB"], "RUB");
						$arItem["TCS_PRICE_TOTAL_RUB_FORMATTED"] = CurrencyFormat($arItem["PRICE_TOTAL_RUB"], "RUB");						
						$arItems[] = $arItem;						
					}
					$arReturn["ITEMS"] = $arItems;
				}
				$arReturn["TOTAL_SUMM_RUB"] = $iTotalSumm;
				$arReturn["TOTAL_SUMM_RUB_FORMATTED"] = CurrencyFormat($arReturn["TOTAL_SUMM_RUB"], "RUB");
				$arReturn["TOTAL_TCS_SUMM_RUB"] = $iTotalTCSSumm;
				$arReturn["TOTAL_TCS_SUMM_RUB_FORMATTED"] = CurrencyFormat($arReturn["TOTAL_TCS_SUMM_RUB"], "RUB");
				return $arReturn;
		
			}
			else return false;
		
		}
	}
	
	

?>