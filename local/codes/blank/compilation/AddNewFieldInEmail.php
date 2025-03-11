<?php
$arID = array();
$arBasketItems = array();
$dbBasketItems = CSaleBasket::GetList(
     array(
                "NAME" => "ASC",
                "ID" => "ASC"
             ),
     array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => 941
             ),
     false,
     false,
     array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "PRODUCT_PROVIDER_CLASS")
             );
while ($arItems = $dbBasketItems->Fetch())
{
     if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"])
     {
          CSaleBasket::UpdatePrice($arItems["ID"],
                                 $arItems["CALLBACK_FUNC"],
                                 $arItems["MODULE"],
                                 $arItems["PRODUCT_ID"],
                                 $arItems["QUANTITY"],
                                 "N",
                                 $arItems["PRODUCT_PROVIDER_CLASS"]
                                 );
          $arID[] = $arItems["ID"];
     }
}
if (!empty($arID))
     {
	     $dbBasketItems = CSaleBasket::GetList(
	     array(
	          "NAME" => "ASC",
	          "ID" => "ASC"
	          ),
	     array(
	          "ID" => $arID,
	        "ORDER_ID" => 941
	          ),
	        false,
	        false,
	        array("NAME", "DETAIL_PAGE_URL")
	                );
	while ($arItems = $dbBasketItems->Fetch())
	{
	    $arBasketItems[] = $arItems;
	}
}
$orderItems = '';
foreach ($arBasketItems as $key => $value) 
{
	$orderItems .= '<a href="http://xxxx.com.ua/'.$value["DETAIL_PAGE_URL"].'">'.$value["NAME"].'</a><br/>';
}
	 
?>
