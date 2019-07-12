<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

                <?$APPLICATION->IncludeComponent("bitrix:news.list","advantages",Array(
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "content",
                    "IBLOCK_ID" => COption::GetOptionString("tireos.landing", "IB_ADVANTAGES", "0"),
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

			<? /*<div class="row trainings" id='trainings'>
				<div class="col-md-3 col-xs-6 hov1">
					<figure class='thumbnails'>
						<i class='fa fa-shield' style='background-image:url("<?=SITE_TEMPLATE_PATH?>/images/icon1.png")'></i>
					</figure>
					<h4 class='xxsmall-h text-center transition-h'>Yearly Programs Upgrades</h4>
					<div class="full-text">
						Nulla ornare tortor quis rhoncus vulputate. Suspendisse commodo fringilla tellus vitae facilisis.
					</div>
				</div>

				<div class="col-md-3 col-xs-6 hov2">
					<figure class='thumbnails'>
						<i class='fa fa-heart-o' style='background-image:url("<?=SITE_TEMPLATE_PATH?>/images/icon2.png")'></i>
					</figure>
					<h4 class='xxsmall-h text-center transition-h'>Best Learning Programs</h4>
					<div class="full-text">
						Nulla ornare tortor quis rhoncus vulputate. Quisque vehicula quis sapien a accumsan
					</div>
				</div>

				<div class="col-md-3 col-xs-6 hov3">
					<figure class='thumbnails'>
						<i class='fa fa-refresh' style='background-image:url("<?=SITE_TEMPLATE_PATH?>/images/icon3.png")'></i>
					</figure>
					<h4 class='xxsmall-h text-center transition-h'>100% Money Back</h4>
					<div class="full-text">
						Nulla ornare tortor quis rhoncus vulputate. Fusce enim erat, volutpat id nisi quis, blandit sodales est
					</div>
				</div>

				<div class="col-md-3 col-xs-6 hov4">
					<figure class='thumbnails'>
						<i class='fa fa-book' style='background-image:url("<?=SITE_TEMPLATE_PATH?>/images/icon4.png")'></i>
					</figure>
					<h4 class='xxsmall-h text-center transition-h'>Small groups, Individual Learning</h4>
					<div class="full-text">
						Nulla ornare tortor quis rhoncus vulputate. Vivamus a enim
					</div>
				</div>
</div>*/?>