<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $APPLICATION->IncludeComponent("novagr.shop:catalog.list", ".default", array(
	"CATALOG_IBLOCK_TYPE" => "catalog",
	"CATALOG_IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"OFFERS_IBLOCK_TYPE" => "offers",
	"OFFERS_IBLOCK_ID" => "#OFFERS_IBLOCK_ID#",
    "ROOT_PATH" => "#SITE_DIR#catalog/",
    "BRAND_ROOT" => "#SITE_DIR#brand/",
	"nPageSize" => (
			empty( $_REQUEST['nPageSize'] )
		) ? 16 : (int)$_REQUEST['nPageSize'],
	"USE_SEARCH_STATISTIC" => "Y",
	"SHOW_QUANTINY_NULL" => "N",
    "OPT_GROUP_ID" => "#GROUP_TRADE#",
    "OPT_PRICE_ID" => "#PRICE_TRADE#",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "2592000",
	"CACHE_GROUPS" => "N"
	),
	false
);?>