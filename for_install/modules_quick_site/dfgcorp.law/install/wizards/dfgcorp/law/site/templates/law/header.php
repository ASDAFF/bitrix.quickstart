<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();
?>
<!doctype html>
<html>
<head>
<?$APPLICATION->ShowHead()?>
<title><?$APPLICATION->ShowTitle()?> - <?=$arSite["NAME"]?></title>
	<script src="<?=SITE_TEMPLATE_PATH?>/jquery.js" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/jquery.ui.core.min.js?ver=1.8.20" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/jquery.ui.widget.min.js?ver=1.8.20" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/jquery.ui.tabs.min.js?ver=1.8.20" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/shortcodes.js?ver=1" type="text/javascript"></script>
	<!-- <script src="<?=SITE_TEMPLATE_PATH?>/comment-reply.js?ver=3.4" type="text/javascript"></script> -->
	<script src="<?=SITE_TEMPLATE_PATH?>/superfish.js?ver=3.4" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/slides.min.jquery.js?ver=3.4" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/jcarousel.js" type="text/javascript"></script>
	<link href="http://fonts.googleapis.com/css?family=Droid+Sans:r,b" rel="stylesheet" type="text/css" />
	<style type="text/css">
/*<![CDATA[*/
	h1, h2, h3, h4, h5, h6, .widget h3, .post .title, .slide-nav li span.title, .slide a.btn, #copyright span, .nav-entries {
	  font-family: 'Droid Sans', arial, sans-serif;
	}
	/*]]>*/
	</style>
	<link href="<?=SITE_TEMPLATE_PATH?>/shortcodes.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="panel"><?$APPLICATION->ShowPanel();?></div>
	<div id="wrapper">
		<div id="header-out">
			<div id="header">
				<div class="col-full" id="top">
					<div class="col-left" id="logo">
							<?$APPLICATION->IncludeFile(
								"#SITE_ID#include/logo.php",
								Array(),
								Array("MODE"=>"html")
							);?>
					</div><!-- /#logo -->
					
					<div class="col-right" id="phones">
						<?$APPLICATION->IncludeFile(
							"#SITE_ID#include/phones.php",
							Array(),
							Array("MODE"=>"html")
						);?>
						<div class="address">
							<?$APPLICATION->IncludeFile(
								"#SITE_ID#include/address.php",
								Array(),
								Array("MODE"=>"html")
							);?>
						</div>
					</div>
					
					<div id="call-us">
<?$APPLICATION->IncludeComponent("dfgcorp:feedback.form", "one_button", array(
	"IBLOCK_TYPE" => "requests",
	"IBLOCK_ID" => "#FEEDBACK_IBLOCK_ID#",
	"STATUS_NEW" => "N",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_EDIT" => "",
	"USER_MESSAGE_ADD" => "",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"EVENT_MESSAGE_ID" => "#DFGCORP_FEEDBACK_FORM_CALL#",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "#PROPERTY_PHONE#",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "NAME",
		1 => "#PROPERTY_PHONE#",
	),
	"HIDE_PROPERTY_ID" => "#PROPERTY_HIDE_ID#",
	"HIDE_PROPERTY_VALUE" => "#PROPERTY_HIDE_REQUEST_CALL_BACK#",
	"GROUPS" => array(
		0 => "2",
	),
	"STATUS" => "ANY",
	"ELEMENT_ASSOC" => "CREATED_BY",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"CUSTOM_TITLE_NAME" => "",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => "",
	"BUTTON_TITLE" => "",
	"FORM_TITLE" => ""
	),
	false
);?>
					</div>
					
					<div class="fix"></div>
					
					<div class="col-right" id="navigation">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
					</div>
				</div>
<?if($APPLICATION->GetCurDir()==SITE_DIR):?>
				<?$APPLICATION->IncludeComponent("bitrix:news.list", "slider", array(
	"IBLOCK_TYPE" => "slider",
	"IBLOCK_ID" => "#SLIDER_IBLOCK_ID#",
	"NEWS_COUNT" => "3",
	"SORT_BY1" => "SORT",
	"SORT_ORDER1" => "ASC",
	"SORT_BY2" => "ID",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "description",
		1 => "link",
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
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"INCLUDE_SUBSECTIONS" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<?endif?>	
			</div>
		</div>
			<div class="page col-full" id="content">
				<div class="col-right" id="main">
					<div class="post-272 page type-page status-publish hentry">
						<h1 id="pagetitle" class="title"><?$APPLICATION->ShowTitle(false)?></h1>
						<div class="entry">