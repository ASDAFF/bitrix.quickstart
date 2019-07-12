<div class="row">
	<div class="col col-md-6 about">
		<h2>О компании</h2>
		<div class="row">
			<div class="col col-sm-3 about-img">
				<?$APPLICATION->IncludeFile("#SITE_DIR#include_areas/mainaboutpic.php",Array(),Array("MODE"=>"html"));?>
			</div>
			<div class="col col-sm-9 col-md-8">
				<?$APPLICATION->IncludeFile("#SITE_DIR#include_areas/mainabouttext.php",Array(),Array("MODE"=>"html"));?>
			</div>
		</div>
	</div>
	<div class="col col-md-6">
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"reviews",
		array(
			"IBLOCK_TYPE" => "services",
			"IBLOCK_ID" => "#IBLOCK_ID_services_customerreviews#",
			"NEWS_COUNT" => "20",
			"SORT_BY1" => "SORT",
			"SORT_ORDER1" => "ASC",
			"SORT_BY2" => "ACTIVE_FROM",
			"SORT_ORDER2" => "DESC",
			"FILTER_NAME" => "",
			"FIELD_CODE" => array(
				0 => "",
				1 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "AUTHOR_NAME",
				1 => "AUTHOR_JOB",
				2 => "",
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
			"SET_BROWSER_TITLE" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"PAGER_TEMPLATE" => "flyaway",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"PAGER_TITLE" => "Отзывы",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"RSFLYAWAY_LINK" => "-",
			"RSFLYAWAY_BLANK" => "-",
			"RSFLYAWAY_CHANGE_SPEED" => "2000",
			"RSFLYAWAY_CHANGE_DELAY" => "80000000",
			"AJAX_OPTION_ADDITIONAL" => "",
			"RSFLYAWAY_AUTHOR_NAME" => "AUTHOR_NAME",
			"RSFLYAWAY_AUTHOR_JOB" => "AUTHOR_JOB",
			"RSFLYAWAY_SHOW_BLOCK_NAME" => "Y",
			"RSFLYAWAY_USE_OWL" => "Y",
			"RSFLYAWAY_COLS_IN_ROW" => "4",
			"RSFLYAWAY_BLOCK_NAME_IS_LINK" => "N",
			"RSFLYAWAY_OWL_CHANGE_SPEED" => "500",
			"RSFLYAWAY_OWL_CHANGE_DELAY" => "8000",
			"RSFLYAWAY_OWL_PHONE" => "1",
			"RSFLYAWAY_OWL_TABLET" => "1",
			"RSFLYAWAY_OWL_PC" => "1"
		),
		false
	);?>
	</div>
</div>
