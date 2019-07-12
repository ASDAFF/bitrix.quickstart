<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
					</div>
				</div>
			</div>
			<div class="col-left" id="sidebar">
<?$APPLICATION->IncludeComponent("bitrix:menu", "left", array(
	"ROOT_MENU_TYPE" => "left",
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
<?if($APPLICATION->GetCurDir()=='/'):?>
		<div class="col-full">
<?$APPLICATION->IncludeComponent("bitrix:news.list", "review", array(
	"IBLOCK_TYPE" => "clients",
	"IBLOCK_ID" => "#REVIEW_IBLOCK_ID#",
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
	"INCLUDE_SUBSECTIONS" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"BLOCK_TITLE" => "",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

<?$APPLICATION->IncludeComponent("bitrix:news.list", "clients", array(
	"IBLOCK_TYPE" => "clients",
	"IBLOCK_ID" => "#CLIENTS_IBLOCK_ID#",
	"NEWS_COUNT" => "20",
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
	"INCLUDE_SUBSECTIONS" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"BLOCK_TITLE" => "",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
		</div>
<?endif?>
		<div id="footer">
			<div class="col-full" id="footer-widgets">
				<div class="block contacts">
					<div class="widget">
						<?$APPLICATION->IncludeFile(
							"#SITE_ID#include/footer_address.php",
							Array(),
							Array("MODE"=>"html")
						);?>
						<div class="fix"></div>
					</div>
				</div>

				<div class="block">
<?$APPLICATION->IncludeComponent("dfgcorp:feedback.form", "simple_form", array(
	"IBLOCK_TYPE" => "requests",
	"IBLOCK_ID" => "#FEEDBACK_IBLOCK_ID#",
	"STATUS_NEW" => "N",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_EDIT" => "",
	"USER_MESSAGE_ADD" => "",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"EVENT_MESSAGE_ID" => "#DFGCORP_FEEDBACK_FORM_FULL#",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "#PROPERTY_PHONE#",
		2 => "#PROPERTY_REQUEST_SERVICE#",
		3 => "#PROPERTY_REQUEST#",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "NAME",
		1 => "#PROPERTY_PHONE#",
		2 => "#PROPERTY_REQUEST_SERVICE#",
		3 => "#PROPERTY_REQUEST#",
	),
	"HIDE_PROPERTY_ID" => "#PROPERTY_HIDE_ID#",
	"HIDE_PROPERTY_VALUE" => "#PROPERTY_HIDE_REQUEST_CONTACT_US#",
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
	"BUTTON_TITLE" => ""
	),
	false
);?>
				</div>

				<div class="block last map">
					<div class="widget" id="map">
						<?$APPLICATION->IncludeFile(
							"#SITE_ID#include/map.php",
							Array(),
							Array("MODE"=>"html")
						);?>
					</div>
				</div>

				<div class="fix"></div>
			</div>

			<div class="col-full">
				<div class="col-left">
					<div id="copyright">
						<?$APPLICATION->IncludeFile(
							"#SITE_ID#include/copyright.php",
							Array(),
							Array("MODE"=>"html")
						);?>
					</div>
				</div>

				<div class="col-right">
					<div id="credit">
						<?$APPLICATION->IncludeFile(
							"#SITE_ID#include/developers.php",
							Array(),
							Array("MODE"=>"html")
						);?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>