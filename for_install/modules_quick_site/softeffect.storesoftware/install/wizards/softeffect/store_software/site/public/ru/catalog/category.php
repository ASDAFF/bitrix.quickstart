<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<?$APPLICATION->IncludeComponent("softeffect:catalog.category", ".default", array(
	"IBLOCK_TYPEG_TYPE" => "sw_catalog",
	"IBLOCK_TYPEG_ID" => "#sw_category#",
	"IBLOCK_CATALOG_TYPE" => "sw_catalog",
	"IBLOCK_CATALOG_ID" => "#sw_software#",
	"CATALOG_CATEGORY_CODE" => $_REQUEST['CATEGORY']
	),
	false
);?>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>