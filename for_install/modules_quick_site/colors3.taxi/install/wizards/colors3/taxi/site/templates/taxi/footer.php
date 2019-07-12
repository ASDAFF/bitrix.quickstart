						<?if ($curPage != SITE_DIR."index.php"):?>                  
			                </div>
			            </div>
			            <aside class="clearfix sidebar span4">
			                <div class="clearfix aside_cont">
<style type="text/css">
@media (min-width: 768px) and (max-width: 1199px){
	.aside_cont img.banner-zakaz {
		width: 100%;
	}
}			                	
</style>
<a href="<?=SITE_DIR;?>"><img class="banner-zakaz" src="<?=SITE_TEMPLATE_PATH?>/i/thm_#COLOR#/src/banner_#COLOR#.jpg" /></a><hr />								
							<?if ($APPLICATION->GetProperty("REVIEWS_PAGE_TPL") == 'YES'):?>
							<?$APPLICATION->IncludeComponent("bitrix:iblock.element.add.form", "reviews", array(
	"IBLOCK_TYPE" => "reviews",
	"IBLOCK_ID" => "#REVIEWS_IBLOCK_ID#",
	"STATUS_NEW" => "NEW",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_EDIT" => GetMessage("COLORS3_TAXI_SPASIBO_VAS_OTZYV_P"),
	"USER_MESSAGE_ADD" => GetMessage("COLORS3_TAXI_SPASIBO_VAS_OTZYV_P"),
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "PREVIEW_TEXT",
		2 => "#PROP_1#",
		3 => "#PROP_2#",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "NAME",
		1 => "PREVIEW_TEXT",
		2 => "#PROP_1#",
	),
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
	"SEF_MODE" => "N",
	"SEF_FOLDER" => SITE_DIR."reviews/",
	"CUSTOM_TITLE_NAME" => GetMessage("COLORS3_TAXI_FIO"),
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => GetMessage("COLORS3_TAXI_OTZYV"),
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => ""
	),
	false
);?>
							<?endif;?>
							
							<?if ($APPLICATION->GetProperty("CONTACTS_PAGE_TPL") == 'YES'):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.feedback", "contacts", array(
	"USE_CAPTCHA" => "N",
	"OK_TEXT" => GetMessage("COLORS3_TAXI_SPASIBO_ZA_OBRASENI").".<br />".GetMessage("COLORS3_TAXI_MY_OTVETIM_VAM_V_TEC"),
	"EMAIL_TO" => "m@3colors.ru",
	"REQUIRED_FIELDS" => array(
		0 => "NAME",
		1 => "EMAIL",
		2 => "MESSAGE",
	),
	"EVENT_MESSAGE_ID" => array(
		0 => "7",
	)
	),
	false
);?>
							<?endif;?>

								<?if ($APPLICATION->GetProperty("NEWS_PAGE_TPL") != 'YES'):?>
				                    <div class="widget">
				                        <?$APPLICATION->IncludeComponent("bitrix:news.list", "news", array(
											"IBLOCK_TYPE" => "news",
											"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
											"NEWS_COUNT" => "1",
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
											"DETAIL_URL" => SITE_DIR."news/#ELEMENT_ID#/",
											"AJAX_MODE" => "N",
											"AJAX_OPTION_JUMP" => "N",
											"AJAX_OPTION_STYLE" => "N",
											"AJAX_OPTION_HISTORY" => "N",
											"CACHE_TYPE" => "A",
											"CACHE_TIME" => "36000000",
											"CACHE_FILTER" => "N",
											"CACHE_GROUPS" => "Y",
											"PREVIEW_TRUNCATE_LEN" => "",
											"ACTIVE_DATE_FORMAT" => "d.m.Y",
											"SET_TITLE" => "Y",
											"SET_STATUS_404" => "Y",
											"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
											"ADD_SECTIONS_CHAIN" => "N",
											"HIDE_LINK_WHEN_NO_DETAIL" => "N",
											"PARENT_SECTION" => "",
											"PARENT_SECTION_CODE" => "",
											"DISPLAY_TOP_PAGER" => "N",
											"DISPLAY_BOTTOM_PAGER" => "N",
											"PAGER_TITLE" => GetMessage("COLORS3_TAXI_NOVOSTI"),
											"PAGER_SHOW_ALWAYS" => "N",
											"PAGER_TEMPLATE" => "",
											"PAGER_DESC_NUMBERING" => "N",
											"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
											"PAGER_SHOW_ALL" => "N",
											"DISPLAY_DATE" => "Y",
											"DISPLAY_NAME" => "Y",
											"DISPLAY_PICTURE" => "N",
											"DISPLAY_PREVIEW_TEXT" => "Y",
											"AJAX_OPTION_ADDITIONAL" => ""
											),
											false
										);?>
				                    </div>

			                    <?endif;?>
			                    <?if ($APPLICATION->GetProperty("SERVICES_PAGE_TPL") != 'YES' && $APPLICATION->GetProperty("NEWS_PAGE_TPL") != 'YES'):?>
			                    	<hr />
			                    <?endif;?>

								<?if ($APPLICATION->GetProperty("SERVICES_PAGE_TPL") != 'YES'):?>
			                    <div class="widget">
			                        <div class="clearfix cont">
			                            <?$APPLICATION->IncludeComponent(
			                                "bitrix:main.include",
			                                "",
			                                array("AREA_FILE_SHOW" => "file",
			                                    "PATH" => SITE_DIR."include/services.php"),
			                                false);
			                            ?>
			                        </div>
			                    </div>
								<?endif;?>
			                </div>
			            </aside>
			        </div>
			    </div>

			<?endif?>

			<div class="footer">
                <div class="top_border"></div>
				<div class="container">
                    <div class="row">
    					<div class="span12">
    						<p class="copyright">
    							<?=GetMessage("COLORS3_TAXI_")?><?php echo date('Y'); ?>, <?$APPLICATION->IncludeComponent(
	                                "bitrix:main.include",
	                                "",
	                                array("AREA_FILE_SHOW" => "file",
	                                    "PATH" => SITE_DIR."include/name.php"),
	                                false);
	                            ?>
    						</p>
    					</div>
    					<div class="span12 yashare-bl">
	    					<?$APPLICATION->IncludeComponent(
							    "bitrix:main.include",
							    "",
							    array("AREA_FILE_SHOW" => "file",
							        "PATH" => SITE_DIR."include/soc.php"),
							    false);
							?>
						</div>
                        <div class="span12">
                            <p><a target="_blank" href="http://www.3colors.ru/"><?=GetMessage("COLORS3_CREATE_SITE")?></a>&nbsp;&mdash;&nbsp;<?=GetMessage("COLORS3_TAXI_STUDII_TRI")?>&nbsp;<?=GetMessage("COLORS3_TAXI_CVETA")?></p>
                        </div>
    				</div>
                </div>
		    </div>
		<!-- /container -->
		
		<!-- javascript-->

		<script src="http://yandex.st/json2/2011-10-19/json2.min.js" type="text/javascript"></script>		
		<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.min.js" type="text/javascript"></script>
		
	    
	    <script type="text/javascript">
	        window.city = "<?$APPLICATION->IncludeFile(SITE_DIR."include/city.php", Array(), Array(
						    "MODE"      => "text",
						    "SHOW_BORDER" => false
						    ));?>";
	    </script>
		
		<?$car_hide = true;?>
		<script>
			window.autocomplete = true;
		</script>
		
		<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/bootstrap-tab.js"></script>
		
		
		<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/default.js" type="text/javascript"></script>
		<?if ($car_hide):?>
			<?if ($APPLICATION->GetProperty("MAIN_INDEX_PAGE") == 'YES'):?>				
				<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/default_base.js" type="text/javascript"></script>	
			<?endif;?>	
			<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/init.js" type="text/javascript"></script>
		<?else:?>				
			<?if ($APPLICATION->GetProperty("MAIN_INDEX_PAGE") == 'YES'):?>				
				<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/default_base.car.js" type="text/javascript"></script>	
			<?endif;?>	
			<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/init.car.js" type="text/javascript"></script>
		<?endif;?>
		
		<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/other.js" type="text/javascript"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/js/ru/jquery.maskedinput.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(function($){
			   $("#FIELD_TEL").mask("8 (999) 999-99-99");
			});			
		</script>
		

		<script type="text/javascript">
		jQuery(document).ready(function($){
			
			$('#myTab a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
			})
		})
		</script>
	</body>
</html>