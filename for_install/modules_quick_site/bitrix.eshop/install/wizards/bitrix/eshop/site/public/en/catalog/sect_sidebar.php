<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", "sidebar", array(
	"VIEWED_COUNT" => "5",
	"VIEWED_NAME" => "Y",
	"VIEWED_IMAGE" => "Y",
	"VIEWED_PRICE" => "Y",
	"VIEWED_CANBUY" => "N",
	"VIEWED_CANBUSKET" => "Y",
	"VIEWED_IMG_HEIGHT" => "100",
	"VIEWED_IMG_WIDTH" => "100",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id_viewed",
	"SET_TITLE" => "N"
	),
	false
);?>