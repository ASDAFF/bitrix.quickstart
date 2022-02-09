<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 

if (IntVal($arResult["NUM_PRODUCTS"])>0 && CModule::IncludeModule("sale")) {
   
         $arBasketItems = array();
         $dbBasketItems = CSaleBasket::GetList(
                 array(
                         "NAME" => "ASC",
                         "ID" => "ASC"
                     ),
                 array(
                         "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                         "LID" => SITE_ID,
                         "ORDER_ID" => "NULL"
                     ),
                 false,
                 false,
                 array("ID", "QUANTITY", "PRICE")
             );
         while ($arItems = $dbBasketItems->Fetch())
         {
             if (strlen($arItems["CALLBACK_FUNC"]) > 0)
             {
                 CSaleBasket::UpdatePrice($arItems["ID"], 
                                          $arItems["QUANTITY"]);
                 $arItems = CSaleBasket::GetByID($arItems["ID"]);
             }
             $arBasketItems[] = $arItems;
         }
         $summ = 0;
         for ($i=0;$i<=$arResult["NUM_PRODUCTS"];$i++)  
            $summ = $summ + $arBasketItems[$i]["PRICE"]*$arBasketItems[$i]["QUANTITY"];
	 
     
   }
   $arResult['SUMM'] = FormatCurrency($summ, "RUB");
    