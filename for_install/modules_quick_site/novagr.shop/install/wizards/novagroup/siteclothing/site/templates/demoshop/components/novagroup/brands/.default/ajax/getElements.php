<?
/**
 * script returns a list of brands
 * 
 * 
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
//deb($_REQUEST);
?>
<?$APPLICATION->IncludeComponent("novagroup:brands", ".default", array(
	"SORT_FIELD" => "NAME",
	"SORT_BY" => "ASC",
	"BRANDS_IBLOCK_CODE" => "vendor",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"COUNT_RECORDS" => ""
	),
	false
);?>