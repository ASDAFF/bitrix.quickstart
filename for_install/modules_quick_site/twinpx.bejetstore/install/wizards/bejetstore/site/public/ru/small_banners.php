<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($_COOKIE["mobile"] != "mobile"):?>
<?if(CModule::IncludeModule("advertising")):?>
<?$APPLICATION->IncludeComponent(
      "bitrix:advertising.banner",
      "index",
      Array(
          "TYPE" => "index",
          "NOINDEX" => "N",
          "CACHE_TYPE" => "N",
          "CACHE_TIME" => "0"
      ),
  false
);?>
<?else:?>
<?$APPLICATION->IncludeComponent(
	"bejetstore:banner", 
	"small", 
	array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "banners",
		"IBLOCK_ID" => BEJET_SELLER_BANNERS,
		"NEWS_COUNT" => "2",
		"SORT_BY1" => "rand",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "arrBannerFilter",
		"GROUP_XML_ID" => "SMALL",
		"FIELD_CODE" => array(
			0 => "DETAIL_PICTURE",
			1 => "DETAIL_TEXT",
		),
		"PROPERTY_CODE" => array(
			0 => "LINK",
			1 => "BANNER_TYPE",
			2 => "LINK_TARGET",
			3 => "LINK_ALT",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<?endif;?>
<?endif;?>