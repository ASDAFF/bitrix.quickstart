<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

error_reporting(0);
header('Content-Type: text/html; charset='.SITE_CHARSET);

 
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
	
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.compare.list",
		"",
		Array(
			"AJAX_MODE" => "N",
			"IBLOCK_TYPE" => "catalog",
			"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
			"DETAIL_URL" => "",
			"COMPARE_URL" => SITE_DIR."catalog/compare.php",
			"NAME" => "CATALOG_COMPARE_LIST",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N"
		),
	false
	);
}
?>