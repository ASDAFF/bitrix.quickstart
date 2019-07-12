<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "basket_line", array(
	"PATH_TO_BASKET" => SITE_DIR."personal/basket/",
	"PATH_TO_PERSONAL" => SITE_DIR."personal/",
	"SHOW_PERSONAL_LINK" => "Y"
	),
	false
);?>