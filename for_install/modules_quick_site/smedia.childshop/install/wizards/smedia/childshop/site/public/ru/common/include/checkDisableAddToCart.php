<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$disable=false;
if (CModule::IncludeModule('sale'))
{		
	if((int)$_GET['ID']>0)
	{
		$parPRODUCT_PROPERTIES=array();
		$resPROPERTIES=array();
		foreach($_GET as $pid=>$val)
		{
			if($pid=='ID')
				continue;	
			$parPRODUCT_PROPERTIES[]=$pid;
			$resPROPERTIES[$pid]=$val;
		}		
		$dbBasketItems = CSaleBasket::GetList(
			array(
				"ID" => "ASC"
			),
			array(
				"PRODUCT_ID" => (int)$_GET['ID'],
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL",
			),
			false,
			false,
			array()
		);
	
		while ($arBasket = $dbBasketItems->Fetch())
		{
			$arCurVals=array();
			$db_res = CSaleBasket::GetPropsList(
			        array(
			                "SORT" => "ASC",
			                "NAME" => "ASC"
			            ),
			        array("BASKET_ID" => $arBasket['ID'])
			    );
			while ($ar_res = $db_res->Fetch())
			{
				if(in_array($ar_res['CODE'], $parPRODUCT_PROPERTIES))			
					$arCurVals[$ar_res['CODE']]=$ar_res['VALUE'];			
			}
			$countEqual=0;			
			foreach($arCurVals as $pid=>$val)
			{				
				if($resPROPERTIES[$pid]==$val)
					$countEqual++;
			}			
			if($arBasket["DELAY"] == "Y" && $countEqual==count($arCurVals))
			{
				echo '{"title":"Уже отложено", "disable": "Y"}';
				$disable=true;
			}
			elseif($countEqual>0 && $countEqual==count($arCurVals))
			{
				echo '{"title":"Уже в корзине", "disable": "Y"}';
				$disable=true;
			}			
		}
	}
}
if(!$disable)
{
	echo '{"title":"В корзину", "disable": "N"}';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>