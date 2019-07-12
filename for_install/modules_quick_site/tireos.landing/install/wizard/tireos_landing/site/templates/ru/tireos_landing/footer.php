<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

					</div>
                 </div>

<footer class='main-wrapper footer'>
	<div class="partners" id='partners'>
		<div class="container make-row">
			<div class="row">
				<? /*<h4 class='division-h col-md-2 dark-text'>Наши партнеры</h4>*/?>
				    <?$APPLICATION->IncludeComponent("bitrix:news.list","partners",Array(
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "content",
                    "IBLOCK_ID" => COption::GetOptionString("tireos.landing", "IB_PARTNERS", "0"),
                    "NEWS_COUNT" => "12",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => Array("ID"),
                    "PROPERTY_CODE" => Array("PICTURE"),
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "SET_TITLE" => "Y",
                    "SET_STATUS_404" => "Y",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
                    "ADD_SECTIONS_CHAIN" => "Y",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "Y",
                    "DISPLAY_TOP_PAGER" => "Y",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "Y",
                    "PAGER_TEMPLATE" => "",
                    "PAGER_DESC_NUMBERING" => "Y",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "Y",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_ADDITIONAL" => ""
                )
            );?>

			</div>
		</div>
	</div>
<?php /*?>	<div class="container">
		<a href="#" data-scroll="form_slider" class='btn submit a-trig reg-footer'>Register Now</a>
	</div>
<?php */?>	<div class="container bottom">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
                            "AREA_FILE_SHOW" => "file", 
                            "AREA_FILE_RECURSIVE" => "Y",  
                            "PATH" => SITE_DIR."include/social-footer.php"
                            )
                            );?>
		<div class="clearifx"></div>
		<span class="copyright">
			&#169; <?php echo date('Y'); ?> tireos.ru
		</span>
		<div class="container-fluid responsive-switcher hidden-md hidden-lg">
			<i class="fa fa-mobile"></i>
			Mobile version: Enabled
		</div>
	</div>
</footer>

<!-- Top -->
<div id="back-top-wrapper" class="visible-lg">
	<p id="back-top" class='bounceOut'>
		<a href="#top">
			<span></span>
		</a>
	</p>
</div>



<!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" aria-hidden="true" file-src="<?=SITE_DIR?>ajax/contact.php">
	<div class="modal-wr">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		
        <div>
        </div>
	</div>
</div>






	</body>
</html>