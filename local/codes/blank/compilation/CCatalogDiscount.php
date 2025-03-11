<?php
AddEventHandler("sale", "OnBasketAdd", "OnBasketAddHandler");
function OnBasketAddHandler( $ID,$arFields)
{
  global $USER;
  \Bitrix\Main\Loader::includeModule('catalog');
  \Bitrix\Main\Loader::includeModule('sale');
  
  if($USER->IsAuthorized() )
  {
  	$arFilter = Array( "USER_ID" => $USER->GetID() );
	$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
	$arrCount = [];
	while ($ar_sales = $db_sales->Fetch())
	{
		$arrCount[] = $ar_sales;
	}
	
	if( is_array($arrCount) == false )
	{		
		$res= CCatalogDiscount::SetCoupon('SL-CDUEA-HI8MMPY');	//3%
	}
	else
	{
		if( count($arrCount)>=1 )
		{
			$resClean=CCatalogDiscount::ClearCoupon('SL-CDUEA-HI8MMPY');
			$res5= CCatalogDiscount::SetCoupon('SL-0JG2C-XIHBEYZ');	//5%
		}
		else
		{			
		}	
	}
  }
  else
  {
	$res = CCatalogDiscountCoupon::SetCoupon('SL-CDUEA-HI8MMPY');//3%
  }	 
}
?>
