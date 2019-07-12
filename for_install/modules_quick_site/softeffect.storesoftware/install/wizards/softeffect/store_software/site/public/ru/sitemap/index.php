<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
?>
<div class="content contenttext" id="helpfaq">
<?
$APPLICATION->IncludeComponent("bitrix:main.map", "site_map_sky", array(
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
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>