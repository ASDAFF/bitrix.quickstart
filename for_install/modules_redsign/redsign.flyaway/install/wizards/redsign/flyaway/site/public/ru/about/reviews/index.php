<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Отзывы");
?><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"reviews",
	array(
		"IBLOCK_TYPE" => "services",
		"IBLOCK_ID" => "#IBLOCK_ID_services_customerreviews#",
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
		"RSFLYAWAY_AUTHOR_NAME" => "AUTHOR_NAME",
		"RSFLYAWAY_AUTHOR_JOB" => "AUTHOR_JOB",
		"RSFLYAWAY_SHOW_BLOCK_NAME" => "N",
		"RSFLYAWAY_USE_OWL" => "N",
		"RSFLYAWAY_COLS_IN_ROW" => "12",
		"PAGER_TEMPLATE" => "flyaway",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"RSFLYAWAY_BLOCK_NAME_IS_LINK" => "N",
		"COMPONENT_TEMPLATE" => "reviews",
		"SET_LAST_MODIFIED" => "N",
		"RSFLYAWAY_PROP_MARKER_TEXT" => "-",
		"RSFLYAWAY_PROP_MARKER_COLOR" => "-",
		"RSFLYAWAY_PROP_ACTION_DATE" => "-",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => ""
	),
	false
);?>
<br/>
<div class="row">
<div class="col col-md-10">
	<a name="addreview"></a><?$APPLICATION->IncludeComponent(
		"rsflyaway:forms",
		"flyaway",
		array(
			"EVENT_TYPE" => "RS_FLYAWAY_REVIEWS",
			"FORM_TITLE" => "Оставить отзыв",
			"FORM_DESCRIPTION" => "Главная цель нашей компании - чтобы вы остались довольны. Используя форму ниже пожалуйста дайте оценку нашей работе.",
			"EMAIL_TO" => "",
			"SHOW_FIELDS" => array(
				0 => "RS_NAME",
				1 => "RS_PHONE",
				2 => "RS_ORGANISATION_NAME",
				3 => "RS_TEXTAREA",
			),
			"REQUIRED_FIELDS" => array(
				0 => "RS_NAME",
				1 => "RS_PHONE",
				2 => "RS_TEXTAREA",
			),
			"USE_CAPTCHA" => "Y",
			"MESSAGE_AGREE" => "Спасибо, ваша заявка принята!",
			"RS_FLYAWAY_EXT_FIELDS_COUNT" => "0",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600",
			"AJAX_OPTION_ADDITIONAL" => ""
		),
		false
	);?>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
