<?
//////Получение элементов для каждого раздела

$c=count($arResult['SECTIONS']);
global $elementsIds;
$elementsIds=array();
for($i=0; $i<$c;$i++)
{
	$tmpElems=array();
	////получить первые $arParams["LOOKS_COUNT"] элементов раздела
	$arSelectElems = Array("ID", "IBLOCK_ID", "NAME", "CODE", "PREVIEW_PICTURE", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
	$arFilterElems = Array(
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"ACTIVE"=>"Y",
		"SECTION_ID"=>$arResult['SECTIONS'][$i]['ID'],
		);
	$res = CIBlockElement::GetList(Array('SORT'=>'ASC'), $arFilterElems, false, Array("nPageSize"=>$arParams["LOOKS_COUNT"]), $arSelectElems );
	$res->SetUrlTemplates();
	while($ob = $res->GetNextElement())
	{
		$arFields = $ob->GetFields();  
		$arProps = $ob->GetProperties();
		$arFields['PROPERTIES']=$arProps;		
		//echo "----------------2-----------".count($arProps['ELEMENTS']['VALUE'])."-------".$arProps['ELEMENTS']['VALUE'].'-';
		//print_R($arProps['ELEMENTS']['VALUE']);
		//echo "\r\n";
		if(count($arProps['ELEMENTS']['VALUE'][0]))
		{
			//echo "----------------2-----------".count($arProps['ELEMENTS']['VALUE'])."-------";
			$elementsIds=array_merge($elementsIds, $arProps['ELEMENTS']['VALUE']);
		}
		$tmpElems[]=$arFields;
		//print_r($elementsIds);
	}
	$arResult['SECTIONS'][$i]['ELEMENTS']=$tmpElems;
	//echo "----------------1------------------";
	//print_r($elementsIds);
}
///получение цен товаров или торговых предложений
//echo "----------------0------------------";
//print_r($elementsIds);
//die;
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
			 $rsOffers = CIBlockElement::GetList(array('SORT'=>'ASC'),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $ID), false, Array("nPageSize"=>1)); 
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