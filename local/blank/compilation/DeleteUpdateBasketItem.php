<?php
//edit order
$mode = $request->getPost('mode');
if( $mode == 'deleteItemBasketOrder' )
{
	$orderid   = $request->getPost('orderid');
	$prodbsid  = $request->getPost('prodbsid');
	$productid = $request->getPost('productid');
	
	if( isset($orderid) && !empty($orderid) && isset($prodbsid) && !empty($prodbsid) && isset($productid) && !empty($productid) )
	{
		CSaleBasket::Delete($prodbsid);
		
		$contents = array();
		$dbBasketItems = CSaleBasket::GetList(
		            array(
		               "NAME" => "ASC",
		               "ID" => "ASC"
		            ),
		            array(
		              //"LID" => SITE_ID,
		              "ORDER_ID" => $orderid,
		            )
		         );
		while ($arItems = $dbBasketItems->Fetch())
		{
			$contents[] = $arItems;
		}
		$sum = 0;
		foreach($contents as $basket_item){
		            if($basket_item['DISCOUNT_PRICE']>0){
		               $sum += $basket_item['DISCOUNT_PRICE']*$basket_item['QUANTITY'];
		            }else{
		               $sum += $basket_item['PRICE']*$basket_item['QUANTITY'];
		            }
		         }
		$arFields = array(
		            "PRICE" => $sum,
		         );
		$resUpdate = CSaleOrder::Update($orderid, $arFields);
		echo $resUpdate;	
	}
	else
	{
		throw new SystemException('Error with deleting products');	
	}	
}
?>
