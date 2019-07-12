<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?> <?$APPLICATION->IncludeComponent(
    "smartrealt:catalog.top",
    ".default",
    Array(
        "SHOW_TITLE" => "Y",
        "TITLE" => "Спецпредложения",
        "COUNT" => "2",
        "TYPE" => array(0=>"2",1=>"1",2=>"6",3=>"13",4=>"7",),
        "TRANSACTION_TYPE" => "SALE",
        "CATALOG_TOP_LIST_URL" => "#SITE_DIR#special_offers/",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600"
    )
);?> 