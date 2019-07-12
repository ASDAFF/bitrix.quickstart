<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

if (count($arResult['IDS']) > 0 && CModule::IncludeModule('sale'))
{
	$dbBasketItems = CSaleBasket::GetList(
		array(
			"ID" => "ASC"
		),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
			),
		false,
		false,
		array()
	);

	$arPageItems = array();
	$arPageItemsDelay = array();
	
	while ($arItem = $dbBasketItems->Fetch())
	{		
		$arCurVals=array();
		$db_res = CSaleBasket::GetPropsList(
		        array(
		                "SORT" => "ASC",
		                "NAME" => "ASC"
		            ),
		        array("BASKET_ID" => $arItem['ID'])
		    );
		while ($ar_res = $db_res->Fetch())
		{
			if(in_array($ar_res['CODE'], $arParams["PRODUCT_PROPERTIES"]))			
				$arCurVals[$ar_res['CODE']]=$ar_res['VALUE'];			
		}
		$countEqual=0;	
		
		foreach($arCurVals as $pid=>$val)
		{			
			if(is_array($arResult['PROPS'][$arItem['PRODUCT_ID']][$pid]['VALUES']) && $arResult['PROPS'][$arItem['PRODUCT_ID']][$pid]['VALUES'][$arResult['PROPS'][$arItem['PRODUCT_ID']][$pid]['SELECTED']]==$val)
				$countEqual++;
			elseif($arResult['PROPS'][$arItem['PRODUCT_ID']][$pid]['VALUES']==$val)
				$countEqual++;
		}			
		if (in_array($arItem['PRODUCT_ID'], $arResult['IDS']) && $countEqual==count($arCurVals))
		{
			if($arItem["DELAY"] == "Y")
				$arPageItemsDelay[] = $arItem['PRODUCT_ID'];
			else
				$arPageItems[] = $arItem['PRODUCT_ID'];
		}
	}
	
	if (count($arPageItems) > 0 || count($arPageItemsDelay) > 0)
	{
		echo '<script type="text/javascript">$(function(){'."\r\n";
		foreach ($arPageItems as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART")."');\r\n";
		}
		foreach ($arPageItemsDelay as $id) 
		{
			echo "disableAddToCart('catalog_add2cart_link_".$id."', 'list', '".GetMessage("CATALOG_IN_CART_DELAY")."');\r\n";
		}
		echo '})</script>';
	}	
}
?>