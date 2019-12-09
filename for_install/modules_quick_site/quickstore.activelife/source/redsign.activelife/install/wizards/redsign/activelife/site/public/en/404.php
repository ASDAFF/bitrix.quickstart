<?
###############sdf#
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus('404 Not Found');
@define('ERROR_404','Y');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Page not found');

$APPLICATION->IncludeComponent(
	"bitrix:main.map",
	".default",
	array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"SET_TITLE" => "Y",
		"LEVEL" => "0",
		"COL_NUM" => "1",
		"SHOW_DESCRIPTION" => "Y",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
