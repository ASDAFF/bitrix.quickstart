<?php
define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");
//CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());

if (CModule::IncludeModule("catalog"))
{
    foreach($_REQUEST['order'] as $PRODUCT_ID )
    {
        Add2BasketByProductID($PRODUCT_ID);
    }
}
LocalRedirect("/personal/cart/");


?>