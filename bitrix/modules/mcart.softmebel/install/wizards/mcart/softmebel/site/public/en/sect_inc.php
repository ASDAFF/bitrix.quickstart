<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$APPLICATION->IncludeComponent("bitrix:store.catalog.random", ".default", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => array(
		0 => "-",
	),
	"PROPERTY_CODE" => array(
		0 => "MINIMUM_PRICE",
		1 => "MAXIMUM_PRICE",
	),
	"DETAIL_URL" => "",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"PARENT_SECTION" => "",
	"DISPLAY_IMG_WIDTH" => "75",
	"DISPLAY_IMG_HEIGHT" => "225",
	"SHARPEN" => "30",
	),
	false
);
?>

<?
$APPLICATION->IncludeComponent("bitrix:news.list", "sidebar", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
	"NEWS_COUNT" => "3",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "M j, Y",
	"DISPLAY_PANEL" => "N",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "News",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);
?>

<div class="content-block content-block-subscribe">
	<h3>Subscribe To News</h3>
	
	<form action="#SITE_DIR#personal/subscribe/" method="post">

	<div id="subscribe" class="form-box">
		<div class="form-textbox">
			<div class="form-textbox-border"><input type="text" value="enter your E-mail" onblur="if (this.value=='')this.value='enter your E-mail'" onclick="if (this.value=='enter your E-mail')this.value=''" name="sf_EMAIL" /></div>
		</div>
		<div class="form-button">
			<input type="submit" value="Subscribe" />
		</div>
	</div>

	</form>
	
</div>