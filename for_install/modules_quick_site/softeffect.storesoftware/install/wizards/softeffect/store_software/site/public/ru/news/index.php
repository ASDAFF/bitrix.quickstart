<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER['DOCUMENT_ROOT'].'#SITE_DIR#admin-config/config.php');
CModule::IncludeModule('iblock');
global $IB_ACTIONS;

preg_match_all("|#SITE_DIR#news/([0-9A-Za-z-_ ^\/]+)/|", $_SERVER['REQUEST_URI'], $matches);
$code = $matches[1][0];

$dbEl = CIBlockElement::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>$IB_ACTIONS, 'ACTIVE'=>'Y', 'CODE'=>$code), FALSE, FALSE, array('IBLOCK_ID', 'ID'));
$arEl = $dbEl->GetNext();
if (!$arEl) {
	require $_SERVER["DOCUMENT_ROOT"].'#SITE_DIR#404_false.php';
	die();
	//LocalRedirect('/404.php');
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Официальная информация");
$APPLICATION->SetAdditionalCSS('http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/countdown/jquery.countdown.css');

?><?$APPLICATION->IncludeComponent("bitrix:news", "news_se_tmp", array(
	"IBLOCK_TYPE" => "sw_content",
	"IBLOCK_ID" => "#sw_actions#",
	"NEWS_COUNT" => "10",
	"USE_SEARCH" => "N",
	"TAGS_CLOUD_ELEMENTS" => "150",
	"PERIOD_NEW_TAGS" => "",
	"USE_RSS" => "Y",
	"NUM_NEWS" => "20",
	"NUM_DAYS" => "180",
	"YANDEX" => "N",
	"USE_RATING" => "N",
	"USE_CATEGORIES" => "N",
	"USE_REVIEW" => "N",
	"USE_FILTER" => "N",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"CHECK_DATES" => "Y",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "#SITE_DIR#news/",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "0",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "Y",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"USE_PERMISSIONS" => "N",
	"PREVIEW_TRUNCATE_LEN" => "",
	"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
	"LIST_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"LIST_PROPERTY_CODE" => array(
		0 => "ELEMENT",
		1 => "",
		2 => "",
	),
	"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
	"DISPLAY_NAME" => "N",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
	"DETAIL_FIELD_CODE" => array(
		0 => "DATE_ACTIVE_TO",
		1 => "ACTIVE_TO",
		2 => "",
	),
	"DETAIL_PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"DETAIL_DISPLAY_TOP_PAGER" => "N",
	"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
	"DETAIL_PAGER_TITLE" => "Страница",
	"DETAIL_PAGER_TEMPLATE" => "arrows",
	"DETAIL_PAGER_SHOW_ALL" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "arrows",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"DISPLAY_AS_RATING" => "rating",
	"FONT_MAX" => "50",
	"FONT_MIN" => "10",
	"COLOR_NEW" => "3E74E6",
	"COLOR_OLD" => "C0C0C0",
	"TAGS_CLOUD_WIDTH" => "100%",
	"USE_SHARE" => "N",
	"AJAX_OPTION_ADDITIONAL" => "",
	"SEF_URL_TEMPLATES" => array(
		"news" => "",
		"section" => "",
		"detail" => "#ELEMENT_CODE#/",
		"rss" => "rss/",
		"rss_section" => "#SECTION_ID#/rss/",
	)
	),
	false
);?>
<br /><br />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/countdown/jquery.countdown.js"></script>
<script type="text/javascript">
	var activeTo = $('#ACTIVE_TO').attr('value');
	if (activeTo!='') {
		activeTMP = activeTo.toString().split(" ");
		dateSplit = activeTMP[0].toString().split(".");
		if (activeTMP[1].length>0) {
			timeSplit = activeTMP[1].toString().split(":");
		} else {
			timeSplit = [0, 0, 0];
		}
		var note = $('#note'), ts = new Date(dateSplit[2], dateSplit[1]-1, dateSplit[0], timeSplit[0], timeSplit[1], timeSplit[2], 0);
		$('#countdown').countdown({
			timestamp: ts,
			callback: function(days, hours, minutes, seconds) {
				
			}
		});
	}
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>