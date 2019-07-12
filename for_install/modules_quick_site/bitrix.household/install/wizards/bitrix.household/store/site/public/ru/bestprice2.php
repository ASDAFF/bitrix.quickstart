<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Лучшая цена");
?>		 

 						<?$APPLICATION->IncludeComponent("bitrix:store.catalog.top", "template1", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => array(
		0 => "#REFRIGERATORS_IBLOCK_ID#",
		1 => "#WASHING_IBLOCK_ID#",
		2 => "#STOVES_IBLOCK_ID#",
		3 => "#APPLIANCE_IBLOCK_ID#",
		4 => "#HOME_IBLOCK_ID#",
		5 => "#BUILTIN_IBLOCK_ID#",
		6 => "",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "BESTPRICE",
		1 => "NOVELTY",
		2 => "HIT",
		3 => "PRODUSER",
		4 => "",
	),
	"FLAG_PROPERTY_CODE" => "BESTPRICE",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"SECTION_NAME" => "Лучшая цена",
	"SECTION_LINK" => "bestprice.php"
	),
	false
);?> 	 	
 				<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>