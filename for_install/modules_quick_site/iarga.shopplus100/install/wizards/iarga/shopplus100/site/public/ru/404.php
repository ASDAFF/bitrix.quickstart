<?
define('ERROR_404','Y');
header("HTTP/1.0 404 Not Found");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("404 — страница не найдена");
?>

<h1>404 — страница не найдена</h1>
<p>К сожалению, запрошенной страницы не существует. Возпользуйтесь меню, чтобы найти нужный раздел.</p>
<br><br>
<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "main", array(
	"IBLOCK_TYPE" => "iarga_shopplus100",
	"IBLOCK_ID" => "1",
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"COUNT_ELEMENTS" => "N",
	"TOP_DEPTH" => "2",
	"SECTION_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>