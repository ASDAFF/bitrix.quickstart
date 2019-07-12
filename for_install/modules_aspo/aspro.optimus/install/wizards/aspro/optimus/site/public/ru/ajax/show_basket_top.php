<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent( "bitrix:sale.basket.basket.line", "normal", Array(
	"PATH_TO_BASKET" => SITE_DIR."basket/", 
	"PATH_TO_ORDER" => SITE_DIR."order/", 
	"SHOW_DELAY" => "Y", 
	"SHOW_PRODUCTS"=>"Y",
	"SHOW_EMPTY_VALUES" => "Y",
	"SHOW_NOTAVAIL" => "N",
	"SHOW_SUBSCRIBE" => "N",
	"SHOW_IMAGE" => "Y",
	"SHOW_PRICE" => "Y",
	"SHOW_SUMMARY" => "Y",
	"SHOW_NUM_PRODUCTS" => "Y",
	"SHOW_TOTAL_PRICE" => "Y",
	"HIDE_ON_BASKET_PAGES" => "Y"
) );?>