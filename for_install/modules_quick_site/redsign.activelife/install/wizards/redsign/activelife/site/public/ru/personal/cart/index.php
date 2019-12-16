<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Корзина");
$APPLICATION->SetTitle("Корзина");

global $CountBasket;
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket",
	"basket",
	array(
		"COMPONENT_TEMPLATE" => "basket",
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "WEIGHT",
			3 => "PROPS",
			4 => "DELETE",
			5 => "DELAY",
			6 => "TYPE",
			7 => "PRICE",
			8 => "QUANTITY",
			9 => "SUM",
			10 => "PROPERTY_CML2_ARTICKLE",
			11 => "PROPERTY_BRANDS",
			12 => "PROPERTY_YEARS_LIMIT",
			13 => "PROPERTY_YEAR",
			14 => "PROPERTY_RAMA",
			15 => "PROPERTY_VILKA",
			16 => "PROPERTY_OBODA",
			17 => "PROPERTY_TORMOZA",
			18 => "PROPERTY_POSHIV",
			19 => "PROPERTY_VOZRASTNAYA_GRUPPA",
			20 => "PROPERTY_DOSTAVKA_INFO",
			21 => "PROPERTY_SKU_COLOR_ZERO",
		),
		"ADDITIONAL_PICT_PROP_#CATALOG_IBLOCK_ID#" => "MORE_PHOTO",
		"ARTICLE_PROP_#CATALOG_IBLOCK_ID#" => "CML2_ARTICKLE",
		"PATH_TO_ORDER" => "#SITE_DIR#personal/order/make/",
		"HIDE_COUPON" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"QUANTITY_FLOAT" => "N",
		"AUTO_CALCULATION" => "Y",
		"SET_TITLE" => "Y",
		"ACTION_VARIABLE" => "basketAction",
		"USE_BUY1CLICK" => "Y",
		"ADDITIONAL_PICT_PROP_#OFFERS_IBLOCK_ID#" => "SKU_MORE_PHOTO",
		"OFFERS_PROPS" => array(
			0 => "SKU_COLOR_2",
			1 => "SKU_COLOR_1",
			2 => "SKU_COLOR",
			3 => "SKU_SIZE",
			4 => "SKU_TKAN",
			5 => "SKU_COLOR_ZERO",
		),
		"OFFER_TREE_COLOR_PROPS" => array(
			0 => "SKU_COLOR_2",
			1 => "SKU_COLOR_1",
			2 => "SKU_COLOR",
			3 => "SKU_COLOR_ZERO",
		),
		"OFFER_TREE_BTN_PROPS" => array(
			0 => "SKU_SIZE",
		),
		"USE_GIFTS" => "Y",
		"GIFTS_PLACE" => "BOTTOM",
		"GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_PRODUCT_QUANTITY_VARIABLE" => "undefined",
		"GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
		"GIFTS_SHOW_OLD_PRICE" => "N",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MESS_BTN_DETAIL" => "Подробнее",
		"GIFTS_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_CONVERT_CURRENCY" => "N",
		"GIFTS_HIDE_NOT_AVAILABLE" => "N",
		"ARTICLE_PROP_#OFFERS_IBLOCK_ID#" => "CML2_ARTICKLE",
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>