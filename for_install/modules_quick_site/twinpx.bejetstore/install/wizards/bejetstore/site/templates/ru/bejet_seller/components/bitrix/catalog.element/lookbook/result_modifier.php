<?
///получение цен товаров или торговых предложений
$elementsIds=$arResult['PROPERTIES']['ELEMENTS']['VALUE'];
$offersExist = CCatalogSKU::getExistOffers($elementsIds);
CModule::IncludeModule("currency");
$prices=array();

$arSelect = Array("ID", "NAME", "CODE", "DETAIL_PAGE_URL");
$arFilter = Array("IBLOCK_ID"=>$arParams["PRODUCTS_BLOCK"], "ACTIVE"=>"Y", 'ID'=>$elementsIds);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$res->SetUrlTemplates("/catalog/#SECTION_CODE#/#ELEMENT_CODE#/");
while($ob = $res->GetNextElement())
{
	$arFields = $ob->GetFields();
	$prices[$arFields['ID']]['NAME']=$arFields['NAME'];
	$prices[$arFields['ID']]['DETAIL_PAGE_URL']=$arFields['DETAIL_PAGE_URL'];
}

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
			
			$prices[$elemId]['price']=CCurrencyRates::ConvertCurrency($ar_res["PRICE"], $ar_res['CURRENCY'], $arParams["CURRENCY_ID"]);//$ar_res["PRICE"];//CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
			$prices[$elemId]['DiscountPrice']=$DiscountPrice;
			$prices[$elemId]['hasTP']=$hasTP;
			$prices[$elemId]['id']=$elemId;
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
					
					$prices[$elemId]['price']=CCurrencyRates::ConvertCurrency($ar_res["PRICE"], $ar_res['CURRENCY'], $arParams["CURRENCY_ID"]);//$ar_res["PRICE"];//CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
					$prices[$elemId]['DiscountPrice']=$DiscountPrice;
					$prices[$elemId]['hasTP']=$hasTP;
					$prices[$elemId]['id']= $arOffer['ID'];
					$prices[$elemId]['CURRENCY']=$ar_res["CURRENCY"];
					$prices[$elemId]['ConvertPrice']=CCurrencyRates::ConvertCurrency($DiscountPrice, $ar_res['CURRENCY'], $arParams["CURRENCY_ID"]);
					
				}
			} 
		}
	}
}

$arResult['PROPERTIES']['ELEMENTS_DATA']=$prices;
$this->__component->SetResultCacheKeys(array("TAGS"));
?>