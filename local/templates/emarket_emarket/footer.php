<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>
			</main><!-- .content -->
		</div><!-- .container-->
	
	<?
	if( ($cur_page_arr[1] != "catalog") &&
		($cur_page_arr[1] != "personal") &&
        ($cur_page_arr[1] != "news"))        
	{
	?>
		<aside class="left-sidebar">
			<?if($cur_page == SITE_DIR."index.php") {?>
				<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog-menu", array(
					"ROOT_MENU_TYPE" => "catalog",
					"MENU_CACHE_TYPE" => "N",
					"MENU_CACHE_TIME" => "3600",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "3",
					"CHILD_MENU_TYPE" => "catalog",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
					),
					false
				);?>
			<?} else {?>

			<?}?>
			
			<div class="advertisement">
				<?$APPLICATION->IncludeComponent("bitrix:main.include","", Array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/advertisement.inc.php","EDIT_TEMPLATE" => ""));?>
			</div>
			
			<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	".default", 
	array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "1",
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
		"ACTIVE_DATE_FORMAT" => "j M Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"PAGER_TEMPLATE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
		</aside><!-- .left-sidebar -->
	<?}?>
	</div><!-- .middle-->

</div><!-- .wrapper -->

<footer class="footer">
	<div class="wrapper">
        <div class="copyright">
			<?$APPLICATION->IncludeComponent("bitrix:main.include","", Array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/copyright.inc.php","EDIT_TEMPLATE" => ""));?>
		</div>
        <div  id="bx-composite-banner">
        </div>
		<?$APPLICATION->IncludeComponent(
			"bitrix:menu",
			"footer-menu",
			Array(
				"ROOT_MENU_TYPE" => "top",
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "",
				"USE_EXT" => "N",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array()
			),
		false
		);?>		
	</div>
</footer><!-- .footer -->

</body>
</html>