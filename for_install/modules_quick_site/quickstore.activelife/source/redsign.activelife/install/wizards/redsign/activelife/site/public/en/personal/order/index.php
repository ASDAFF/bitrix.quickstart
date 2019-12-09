<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "My orders");
$APPLICATION->SetTitle("My orders");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order",
	"al",
	array(
		"COMPONENT_TEMPLATE" => "al",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SEF_MODE" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_PAYMENT" => "#SITE_DIR#personal/order/payment/",
		"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "N",
		"NAV_TEMPLATE" => "al",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "F",
		),
		"ARTICLE_PROP_#CATALOG_IBLOCK_ID#" => "CML2_ARTICKLE",
		"ADDITIONAL_PICT_PROP_#CATALOG_IBLOCK_ID#" => "MORE_PHOTO",
		"ARTICLE_PROP_#OFFERS_IBLOCK_ID#" => "CML2_ARTICKLE",
		"ADDITIONAL_PICT_PROP_#OFFERS_IBLOCK_ID#" => "SKU_MORE_PHOTO",
		"OFFER_TREE_PROPS_#OFFERS_IBLOCK_ID#" => array(
			0 => "SKU_COLOR_2",
			1 => "SKU_COLOR_1",
			2 => "SKU_COLOR",
			3 => "SKU_SIZE",
			4 => "CML2_ARTICKLE",
			5 => "SKU_TKAN",
		),
		"SEF_FOLDER" => "#SITE_DIR#personal/order/",
		"SEF_URL_TEMPLATES" => array(
			"list" => "",
			"detail" => "detail/#ID#/",
			"cancel" => "cancel/#ID#/",
		)
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>