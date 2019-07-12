<?
//////Получение элементов для каждого разделаITEMS

$c=count($arResult['ITEMS']);
$elementsIds=array();
for($i=0; $i<$c;$i++)
{
	if($arResult['ITEMS'][$i]['PROPERTIES']['ELEMENTS']['VALUE'][0])
	{
		$elementsIds=array_merge($elementsIds, $arResult['ITEMS'][$i]['PROPERTIES']['ELEMENTS']['VALUE']);
	}
	$tmpElems[]=$arResult['ITEMS'][$i];	
}

///получение цен товаров или торговых предложений
$offersExist = CCatalogSKU::getExistOffers($elementsIds);
CModule::IncludeModule("currency");
$prices=array();

foreach($offersExist as $elemId=>$hasTP)
{
	if(!$hasTP)
	{
		$db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $elemId));
		if ($ar_res = $db_res->Fetch())
		{
			$DiscountPrice=$ar_res["PRICE"];
		    $arDiscounts = CCatalogDiscount::GetDiscountByProduct($elemId, $USER->GetUserGroupArray(), "N", 2);
			if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
				$DiscountPrice = CCatalogProduct::CountPriceWithDiscount($ar_res["PRICE"], $ar_res["CURRENCY"], $arDiscounts);
			}
			
			$prices[$elemId]['price']=$ar_res["PRICE"];//CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
			$prices[$elemId]['DiscountPrice']=$DiscountPrice;
			$prices[$elemId]['hasTP']=$hasTP;
			$prices[$elemId]['CURRENCY']=$ar_res["CURRENCY"];
			$prices[$elemId]['ConvertPrice']=CCurrencyRates::ConvertCurrency($DiscountPrice, $ar_res['CURRENCY'], $arParams["CURRENCY_ID"]);
		}
		else
		{
			$prices[$elemId]['price']=0;
			$prices[$elemId]['hasTP']=$hasTP;
			$prices[$elemId]['CURRENCY']=$ar_res["CURRENCY"];
			$prices[$elemId]['ConvertPrice']=CCurrencyRates::ConvertCurrency($ar_res["PRICE"], $ar_res['CURRENCY'], $arParams["CURRENCY_ID"]);
		}
	}
	else
	{
		$IBLOCK_ID = $arParams["PRODUCTS_BLOCK"]; 
		$ID = $elemId; 
		$arInfo = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID); 
		if (is_array($arInfo)) 
		{ 
			 $rsOffers = CIBlockElement::GetList(array('SORT'=>'ASC'),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $ID), false, false); 
			 if ($arOffer = $rsOffers->GetNext()) 
			{
				$db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $arOffer['ID']));
				if ($ar_res = $db_res->Fetch())
				{
					$DiscountPrice=$ar_res["PRICE"];
					$arDiscounts = CCatalogDiscount::GetDiscountByProduct($elemId, $USER->GetUserGroupArray(), "N", 2);
					if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
						$DiscountPrice = CCatalogProduct::CountPriceWithDiscount($ar_res["PRICE"], $ar_res["CURRENCY"], $arDiscounts);
					}
					
					$prices[$elemId]['price']=$ar_res["PRICE"];//CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
					$prices[$elemId]['DiscountPrice']=$DiscountPrice;
					$prices[$elemId]['hasTP']=$hasTP;
					$prices[$elemId]['CURRENCY']=$ar_res["CURRENCY"];
					$prices[$elemId]['ConvertPrice']=CCurrencyRates::ConvertCurrency($DiscountPrice, $ar_res['CURRENCY'], $arParams["CURRENCY_ID"]);
					
				}
			} 
		}
	}
}

$arResult['ELEMENTS']=$prices;




?>