<div class="block">
	When rendering services we guarantee professional and ethical standards accepted in the professional community.
</div>
<div class="block">
<?$APPLICATION->IncludeComponent("bitrix:search.tags.cloud", (!empty($_SESSION["QUICK_THEME"][SITE_ID]["TAGS_VER"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["TAGS_VER"] : COption::GetOptionString("effortless", "QUICK_THEME_TAGS_VER", "articles-ver-1", SITE_ID)), 
	array(
		"SORT" => "CNT",
		"PAGE_ELEMENTS" => "150",
		"PERIOD" => "",
		"URL_SEARCH" => "#SITE_DIR#search/",
		"TAGS_INHERIT" => "Y",
		"CHECK_DATES" => "Y",
		"FILTER_NAME" => "",
		"arrFILTER" => array(0 => "iblock_#IBLOCK_TYPE_ARTICLES#",),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"FONT_MAX" => "42",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "555555",
		"COLOR_OLD" => "555555",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "N",
		"COLOR_TYPE" => "N",
		"WIDTH" => "100%",
		"arrFILTER_iblock_articles" => array(0 => "all",),
		"arrFILTER_iblock_#IBLOCK_TYPE_ARTICLES#" => array(0 => "#IBLOCK_ID_ARTICLES#",)
	),
	false
);?>
</div>