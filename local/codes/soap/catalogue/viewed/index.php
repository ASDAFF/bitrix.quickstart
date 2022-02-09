<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Просмотренные товары");
?><?$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", "viewed_goods", array(
	"VIEWED_COUNT" => "1000",
	"VIEWED_NAME" => "Y",
	"VIEWED_IMAGE" => "Y",
	"VIEWED_PRICE" => "Y",
	"VIEWED_CANBUY" => "N",
	"VIEWED_CANBUSKET" => "N",
	"VIEWED_IMG_HEIGHT" => "150",
	"VIEWED_IMG_WIDTH" => "150",
	"BASKET_URL" => "/basket/",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"SET_TITLE" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>