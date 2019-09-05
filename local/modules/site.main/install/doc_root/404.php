<?
include_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php';

$sapi_type = php_sapi_name();
if ($sapi_type == 'cgi') {
	header('Status: 404');
} else {
	header('HTTP/1.0 404 Not Found');
}

@define('ERROR_404', 'Y');
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$title = LANG == 'en' ? 'Page\'s not found' : 'Страница не найдена';
$APPLICATION->SetTitle($title);
$APPLICATION->AddChainItem($title);

$APPLICATION->IncludeComponent("bitrix:main.map", ".default", array(
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"SET_TITLE" => "Y",
	"LEVEL" => "3",
	"COL_NUM" => "3",
	"SHOW_DESCRIPTION" => "N"
	),
	false
);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';