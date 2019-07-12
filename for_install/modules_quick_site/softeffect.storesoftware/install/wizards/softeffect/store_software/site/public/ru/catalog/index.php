<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<?$APPLICATION->IncludeComponent("softeffect:catalog.section", ".default", array(
	"IBLOCK_TYPEG_TYPE" => "sw_catalog",
	"IBLOCK_TYPEG_ID" => "#sw_category#",
	"IBLOCK_CATALOG_TYPE" => "sw_catalog",
	"IBLOCK_CATALOG_ID" => "#sw_software#",
	"IBLOCK_TOPPROD_TYPE" => "sw_catalog",
	"IBLOCK_TOPPROD_ID" => "#sw_bestgoods#",
	"IBLOCK_ACTIONS_TYPE" => "sw_services",
	"IBLOCK_ACTIONS_ID" => "#sw_actions#",
	"CATALOG_SECTION_CODE" => $_REQUEST['SECTION']
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>