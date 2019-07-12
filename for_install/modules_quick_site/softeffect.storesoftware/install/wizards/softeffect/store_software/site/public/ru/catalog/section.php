<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<?$APPLICATION->IncludeComponent("softeffect:catalog.section.list", ".default", array(
	"IBLOCK_CATALOG_TYPE" => "sw_catalog",
	"IBLOCK_CATALOG_ID" => "#sw_software#",
	"CATALOG_SECTION_CODE" => $_REQUEST['SECTION'],
	"CATALOG_SECTION_L2_CODE" => $_REQUEST['SECTION_L2']
	),
	false
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>