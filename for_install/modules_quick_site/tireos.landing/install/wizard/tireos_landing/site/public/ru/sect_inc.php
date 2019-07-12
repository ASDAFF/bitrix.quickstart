<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="information-block">
<?$APPLICATION->IncludeComponent(
	"bitrix:search.tags.cloud",
	"",
	Array(
		"FONT_MAX" => "25", 
		"FONT_MIN" => "12", 
		"COLOR_NEW" => "8FA4BA", 
		"COLOR_OLD" => "2775C7", 
		"PERIOD_NEW_TAGS" => "", 
		"SHOW_CHAIN" => "Y", 
		"COLOR_TYPE" => "Y", 
		"WIDTH" => "100%", 
		"SORT" => "NAME", 
		"PAGE_ELEMENTS" => "150", 
		"PERIOD" => "", 
		"URL_SEARCH" => "/search/index.php", 
		"TAGS_INHERIT" => "Y", 
		"CHECK_DATES" => "N", 
		"arrFILTER" => Array("iblock_articles", "iblock_news"), 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "3600", 
		"arrFILTER_iblock_articles" => "all" 
	)
);?>
</div>