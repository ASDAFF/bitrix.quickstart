<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");
?>
<?$APPLICATION->IncludeComponent("fenixit:sale.basket.basket.small", "wishlist", array(
	"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
	"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
	"SHOW_DELAY" => "Y",
	"SHOW_NOTAVAIL" => "N",
	"SHOW_SUBSCRIBE" => "N",
	"DISPLAY_IMG_WIDTH" => "155",
	"DISPLAY_IMG_HEIGHT" => "198",
	"DISPLAY_IMG_PROP" => "Y"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>