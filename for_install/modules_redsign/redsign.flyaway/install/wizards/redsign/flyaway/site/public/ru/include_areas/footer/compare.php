<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.compare.list",
		"popup",
		array(
			"IBLOCK_TYPE" => "catalog",
			"IBLOCK_ID" => "#IBLOCK_ID_catalog_catalog#",
			"NAME" => "CATALOG_COMPARE_LIST",
			"COMPONENT_TEMPLATE" => "popup",
			"AJAX_MODE" => "N",
			"DETAIL_URL" => "",
			"COMPARE_URL" => "#SITE_DIR#catalog/compare.php?action=#ACTION_CODE#",
			"ACTION_VARIABLE" => "",
			"PRODUCT_ID_VARIABLE" => "",
			"PRICE_CODE" => array(
				0 => "BASE",
			)
		),
		false
	);
?>
