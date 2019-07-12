<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<?$APPLICATION->IncludeComponent("softeffect:catalog.element", ".default", array(
	"IBLOCK_CATALOG_TYPE" => "sw_catalog",
	"IBLOCK_CATALOG_ID" => "#sw_software#",
	"IBLOCK_REVIEWS_GOODS_TYPE" => "sw_services",
	"IBLOCK_REVIEWS_GOODS_ID" => "#sw_reviews_goods#",
	"IBLOCK_COMPARE_TYPE" => "sw_catalog",
	"IBLOCK_COMPARE_ID" => "#sw_compare#",
	"CATALOG_ELEMENT_CODE" => $_REQUEST['ELEMENT']
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>