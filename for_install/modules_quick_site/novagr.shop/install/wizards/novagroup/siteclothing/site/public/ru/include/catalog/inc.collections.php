<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$imagery = $APPLICATION->IncludeComponent("novagr.shop:fashion.list", ".default", array(
	"FASHION_IBLOCK_TYPE" => "catalog",
	"FASHION_IBLOCK_ID" => "#FASHION_IBLOCK_ID#",
	"CATALOG_IBLOCK_TYPE" => "catalog",
	"CATALOG_IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"OFFERS_IBLOCK_TYPE" => "offers",
	"OFFERS_IBLOCK_ID" => "#OFFERS_IBLOCK_ID#",
	"FASHION_ROOT_PATH" => SITE_DIR."imageries/",
	"CATALOG_ROOT_PATH" => SITE_DIR."catalog/",
	"VENDOR_ROOT_PATH" => "/brands/",
	"INET_MAGAZ_ADMIN_USER_GROUP_ID" => "#GROUP_SADMIN#",
    "OPT_GROUP_ID" => "#GROUP_TRADE#",
    "OPT_PRICE_ID" => "#PRICE_TRADE#",
	"CACHE_TYPE" => "A",
		"CACHE_TIME" => "2592000",
		"USE_SEARCH_STATISTIC" => "Y",
		"CACHE_GROUPS" => "Y"
	)
);

?>