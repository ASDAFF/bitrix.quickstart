<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("ќкна дл€ всех");
?>
<div class="span12">
<div class="row">
	<div class="span12">
		<div class="row">
			<div class="span12" >
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "slider", array(
	"IBLOCK_TYPE" => "content",
	"IBLOCK_ID" => "#SLIDER_IBLOCK_ID#",
	"NEWS_COUNT" => "10",
	"SORT_BY1" => "SORT",
	"SORT_ORDER1" => "ASC",
	"SORT_BY2" => "ID",
	"SORT_ORDER2" => "DESC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "DETAIL_PICTURE",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "LINK",
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
	"ACTIVE_DATE_FORMAT" => "",
	"SET_STATUS_404" => "N",
	"SET_TITLE" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"INCLUDE_SUBSECTIONS" => "Y",
	"PAGER_TEMPLATE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Ќовости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
				</div>
			</div>
			<div class="row">
				<div class="span12">
					<div id="post-203" class="post-203 page type-page status-publish hentry page">
						<div class="row">
							<div class="span6"><p><img class="alignnone size-full wp-image-1937" alt="page1_pic1" src="<?=SITE_TEMPLATE_PATH?>/images/img1.png" width="570" height="415"/></p></div>
							<div class="span6"><h1 class="big_title">найди</h2>
								<h2>свои окна и взгл€ни на мир по-новому!</h2>
								<p>ћы предлагаем широкий выбор окон хорошего качества по отличным ценам с индивидуальным подходом к каждому покупателю.</p></div>
							<div class="span12"><div class="sm_hr"></div>
								<ul class="posts-grid row-fluid unstyled title-marker">
									<li class="span4">
										<figure class="featured-thumbnail thumbnail">
											<a href="<?=SITE_TEMPLATE_PATH?>/images/img2-original.jpg" rel="prettyPhoto-256151780" ><img src="<?=SITE_TEMPLATE_PATH?>/images/img2-small.jpg">
												<span class="zoom-icon"></span>
											</a>
										</figure>
										<div class="clear"></div>
										<h5><a href="<?=SITE_DIR?>services/" >¬ыезд на дом дл€ замера</a></h5>
										<div class="price">Ѕесплатно</div>
										<p class="excerpt">Ќаш сотрудник приедет к вам дамой дл€ замеров оконных проемов</p><a class="btn btn-primary" href="<?=SITE_DIR?>services/" >подробнее</a>
									</li>

									<li class="span4">
										<figure class="featured-thumbnail thumbnail">
											<a href="<?=SITE_TEMPLATE_PATH?>/images/img3-original.jpg" rel="prettyPhoto-256151780" ><img src="<?=SITE_TEMPLATE_PATH?>/images/img3-small.jpg">
												<span class="zoom-icon"></span>
											</a>
										</figure>
										<div class="clear"></div>
										<h5><a href="<?=SITE_DIR?>calc/" >ќнлайн расчет примерной стоимости окон</a></h5>
										<div class="price">от 2000 рублей</div>
										<p class="excerpt">Ќаш калькул€тор позволит вам узнать приблизительную стоимость окна и его установки</p><a class="btn btn-primary" href="<?=SITE_DIR?>calc/">подробнее</a>
									</li>

									<li class="span4">
										<figure class="featured-thumbnail thumbnail">
											<a href="<?=SITE_TEMPLATE_PATH?>/images/img4-original.png" rel="prettyPhoto-256151780" ><img src="<?=SITE_TEMPLATE_PATH?>/images/img4-small.png">
												<span class="zoom-icon"></span>
											</a>
										</figure>
										<div class="clear"></div>
										<h5><a href="<?=SITE_DIR?>services/" >”становка окон</a></h5>
										<div class="price">от 500 рублей</div>
										<p class="excerpt">¬ы сможете заказать установку окон у наших специалистов</p><a class="btn btn-primary" href="<?=SITE_DIR?>services/">подробнее</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clear"></div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>