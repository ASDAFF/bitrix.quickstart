<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Спецпредложения");
?> 
<h1> Спецпредложения</h1>
 <?php $APPLICATION->IncludeComponent("smartrealt:catalog.top", "table", array(
    "SHOW_TITLE" => "Y",
    "TITLE" => "",
    "COUNT" => "3",
    "TYPE" => array(
        0 => "2",
    ),
    "TRANSACTION_TYPE" => "SALE",
    "CATALOG_TOP_LIST_URL" => "",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "3600"
    ),
    false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
