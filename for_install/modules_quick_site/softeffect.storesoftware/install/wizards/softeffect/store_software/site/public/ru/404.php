<?
define("P404", true);
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");
$APPLICATION->AddChainItem('404 - Страница не найдена');
?>
<div class="content contenttext" id="helpfaq">
	<? $APPLICATION->IncludeFile('#SITE_DIR#include/404.php', array(), array('MODE'=>'html')); ?>
	<h2>Карта сайта</h2>
	<br /><br />
	<?$APPLICATION->IncludeComponent("bitrix:main.map", "site_map_sky", array(
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"SET_TITLE" => "Y",
	"LEVEL"	=>	"2",
	"COL_NUM"	=>	"1",
	"SHOW_DESCRIPTION" => "N"
	),
	false
);
?>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>