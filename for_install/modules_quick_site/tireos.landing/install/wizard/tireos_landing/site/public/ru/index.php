<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Landing Page");
?>

<?php /*?><?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
"AREA_FILE_SHOW" => "file", 
"AREA_FILE_RECURSIVE" => "Y",  
"PATH" => SITE_DIR."include/big-slider.php"
)
);?><?php */?>

	<section class="relative big-slider">
		<div id="form_slider" class='big-bxslider row' data-anchor="form_slider">
        <?$APPLICATION->IncludeComponent("bitrix:news.list","big-slider",Array(
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "content",
                    "IBLOCK_ID" => COption::GetOptionString("tireos.landing", "IB_SLIDER", "0"),
                    "NEWS_COUNT" => "3",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => Array("ID"),
                    "PROPERTY_CODE" => Array("PICTURE_D,HEADER"),
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
	</section>
    
    
 	<section class='dark-blue'>
		<div class="container make-row">
			<div class="row">
				<div class="col-md-3 col-sm-6 make-md">
					<h4 class='division-h sem-h' id='animIt1'>Контактная <br />информация</h4>
				</div>

				<ul class='list-unstyled seminars'>
					<li id='animIt2' class='col-md-3 col-sm-6'>
						<div class="wr-item">
							<i class="fa fa-calendar"></i>
							<div class="media">
								<h4 class='division-h'>Время работы</h4> 
								<span>
                                    <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
                                    "AREA_FILE_SHOW" => "file", 
                                    "AREA_FILE_RECURSIVE" => "Y",  
                                    "PATH" => SITE_DIR."include/worktime.php"
                                    )
                                    );?>
								</span>
							</div>
						</div>
					</li>
					<li id='animIt3' class='col-md-3 col-sm-6'>
						<div class="wr-item">
							<i class="fa fa-clock-o"></i>
							<div class="media">
								<h4 class='division-h'>Телефоны</h4>
                                <span>
                                    <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
                                    "AREA_FILE_SHOW" => "file", 
                                    "AREA_FILE_RECURSIVE" => "Y",  
                                    "PATH" => SITE_DIR."include/phones.php"
                                    )
                                    );?>
                                </span>
							</div>
						</div>
					</li>
					<li id='animIt4' class='col-md-3 col-sm-6'>
						<div class="wr-item">
							<i class="fa fa-map-marker"></i>
							<div class="media">
								<h4 class='division-h'>Адрес</h4>
								<span>
                                    <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
                                    "AREA_FILE_SHOW" => "file", 
                                    "AREA_FILE_RECURSIVE" => "Y",  
                                    "PATH" => SITE_DIR."include/address.php"
                                    )
                                    );?>
								</span>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>
    
    
    
	<section class="container" id="features" data-anchor="features">
		<div class="spacer6"></div>
			<h2 class='text-center xxh-Bold'>Наши преимущества</h2>
			<h3 class='text-center xmedium-h'>Почему люди выбирают нас?</h3>
<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
"AREA_FILE_SHOW" => "file", 
"AREA_FILE_RECURSIVE" => "Y",  
"PATH" => SITE_DIR."include/tacitus.php"
)
);?>

			<?php /*?></div><?php */?>
		<div class="offsetY-4"></div>
	</section>

<?php /*?><?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
"AREA_FILE_SHOW" => "file", 
"AREA_FILE_RECURSIVE" => "Y",  
"PATH" => SITE_DIR."include/about.php"
)
);?><?php */?>

	<section class="bg-darkblue" id="information">
                <?$APPLICATION->IncludeComponent("bitrix:news.list","about",Array(
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "content",
                    "IBLOCK_ID" => COption::GetOptionString("tireos.landing", "IB_INFO", "0"),
                    "NEWS_COUNT" => "3",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => Array("ID"),
                    "PROPERTY_CODE" => Array("PICTURE_D,HEADER"),
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
	</section>

	<section class="container tables" id='about'>
		<h2 class='text-center xxh-Bold bold'>Наши услуги</h2>
		<h3 class='text-center xmedium-h'>Выберите свой пакет услуг</h3>

		<div class='auto-x'>
			<div class="row">

                <?$APPLICATION->IncludeComponent("bitrix:news.list","services",Array(
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "content",
                    "IBLOCK_ID" => COption::GetOptionString("tireos.landing", "IB_SPECIALS", "0"),
                    "NEWS_COUNT" => "3",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => Array("ID"),
                    "PROPERTY_CODE" => Array("PRICE", "ADVANTAGES"),
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
	</section>
		

      <section id="aboutUs_slider" data-anchor="testimonials">
		<h3 class='slide-title text-center'>Что говорят о нас?</h3>
		<h4 class='xxmedium-h text-center'>Отзывы о нашей компании</h4>
        
        <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
        "AREA_FILE_SHOW" => "file", 
        "AREA_FILE_RECURSIVE" => "Y",  
        "PATH" => SITE_DIR."include/reviews.php"
        )
        );?>
	</section>

	<section class="container make-row" data-anchor="gallery">
		<h2 class='text-center xxh-Bold bold'>Галерея</h2>
		<h3 class='text-center xmedium-h'>Это галерея наших работ</h3>

        <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
        "AREA_FILE_SHOW" => "file", 
        "AREA_FILE_RECURSIVE" => "Y",  
        "PATH" => SITE_DIR."include/gallery.php"
        )
        );?>

		<? /*<div class="spacer5"></div>*/?>
	</section>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>