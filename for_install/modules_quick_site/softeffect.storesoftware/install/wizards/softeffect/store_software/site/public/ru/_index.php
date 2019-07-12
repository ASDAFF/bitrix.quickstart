<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("СофтМагазин");

global $IB_ACTIONS;
?>
<div id="featuredblock">
	<?$APPLICATION->IncludeComponent("softeffect:banner_flash_new", "banner", array(
	"IBLOCK_TYPE" => "sw_content",
	"IBLOCKS" => "#sw_banner#",
	"PIC_COUNT" => "20",
	"DELAY" => "5000",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC"
	),
	false
);?>
	<br /><br />
	<!-- the tabs -->
	<ul class="tabs">
		<li><a href="#goodsday">Товары дня</a></li>
		<li><a href="#actions">Акции</a></li>
		<li><a href="#blogs">Блог</a></li>
	</ul>
	<!-- tab "panes" -->
	<div class="panes">
		<div>
			<?$APPLICATION->IncludeComponent("softeffect:catalog.goodsday", "", array(
	"IBLOCK_TYPE" => "sw_catalog",
	"IBLOCK" => "#sw_goodsmain#",
	"BLOCK_COUNT" => "8",
	),
	false
);?>
		</div>
		<div>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "news_se", array(
	"IBLOCK_TYPE" => "sw_content",
	"IBLOCK_ID" => $IB_ACTIONS,
	"NEWS_COUNT" => "12",
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
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
		</div>
		<div>
			<br />
			<?$APPLICATION->IncludeComponent("bitrix:blog.new_posts.list", ".default", array(
	"GROUP_ID" => "",
	"BLOG_URL" => "",
	"MESSAGE_PER_PAGE" => "3",
	"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
	"NAV_TEMPLATE" => "",
	"IMAGE_MAX_WIDTH" => "100",
	"IMAGE_MAX_HEIGHT" => "600",
	"PATH_TO_BLOG" => "#SITE_DIR#blog/#blog#/",
	"PATH_TO_POST" => "#SITE_DIR#blog/#blog#/#post_id#/",
	"PATH_TO_USER" => "#SITE_DIR#blog/user/#user_id#/",
	"PATH_TO_GROUP_BLOG_POST" => "#SITE_DIR#blog/#blog#/#post_id#/",
	"PATH_TO_BLOG_CATEGORY" => "#SITE_DIR#blog/",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "86400",
	"PATH_TO_SMILE" => "",
	"SET_TITLE" => "N",
	"POST_PROPERTY_LIST" => array(
	),
	"SHOW_RATING" => "N",
	"NAME_TEMPLATE" => "#NOBR##LAST_NAME# #NAME##/NOBR#",
	"SHOW_LOGIN" => "Y",
	"BLOG_VAR" => "",
	"POST_VAR" => "",
	"USER_VAR" => "",
	"PAGE_VAR" => "",
	"SEO_USER" => "N"
	),
	false
);?>
			<br />
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>