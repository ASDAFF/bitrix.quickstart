<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Сайт сервисной компании");
?>

<section id="index-block" class="i-page-block i-page-content">
	<a name="index" class="b-anchor"></a>
	<div class="i-page-content-pad">
		<div class="b-index-block__ill">
		<? //Компонент главной картинки
		$APPLICATION->IncludeComponent("serv:news.detail","onlypreiewpic",Array(
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"AJAX_MODE" => "N",
			"IBLOCK_TYPE" => "bserv_mainimg",
			"IBLOCK_ID" => #TEMAIMG_IBLOCK_ID#,
			//"ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
			"ELEMENT_CODE" => "",
			"CHECK_DATES" => "N",
			"FIELD_CODE" => Array("ID"),
			"PROPERTY_CODE" => Array("DESCRIPTION"),
			"IBLOCK_URL" => "news.php?ID=#IBLOCK_ID#\"",
			"META_KEYWORDS" => "KEYWORDS",
			"META_DESCRIPTION" => "DESCRIPTION",
			"BROWSER_TITLE" => "BROWSER_TITLE",
			"DISPLAY_PANEL" => "Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "Y",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"USE_PERMISSIONS" => "N",
			"GROUP_PERMISSIONS" => Array("1"),
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600",
			"CACHE_GROUPS" => "Y",
			"DISPLAY_TOP_PAGER" => "Y",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"PAGER_TITLE" => "Страница",
			"PAGER_TEMPLATE" => "",
			"PAGER_SHOW_ALL" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N"
		)
		);?>		
		</div>
		<div class="b-index-block__bg"></div>
		<div class="b-index-block__logo">
			<? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/logo.php', Array(), Array("MODE"      => "html"));?>
			<div class="b-index-block__slogan"><? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/slogan.php', Array(), Array("MODE"      => "html"));?></div>
		</div>
		<div class="b-index-block__order-button">
			<a href="#form" class="b-button">Заказать</a>
		</div>
	</div>
</section>

<div id="page-blocks">
	
	<section id="about-block" class="i-page-block i-page-content">
	<a name="about" class="b-anchor"></a>
	<div class="i-page-content-pad">
		<h2 class="b-h1"><? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/header2.php', Array(), Array("MODE"      => "html"));?></h2>
		<? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/text1.php', Array(), Array("MODE"      => "html"));?>
		<? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/text2.php', Array(), Array("MODE"      => "html"));?>
		<div class="b-phone-block">
			<div class="b-icon i-phone"></div>
			<div class="b-phone-block__text"><? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/tel.php', Array(), Array("MODE"      => "html"));?></div>
		</div>
	</div>
</section>

<?$APPLICATION->IncludeComponent("serv:news.list", ".default", array(
	"IBLOCK_TYPE" => "servises",
	"IBLOCK_ID" => #SERVICES_IBLOCK_ID#,
	"NEWS_COUNT" => "9999",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "ID",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "DESCRIPTION",
		2 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "Y",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "Y",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "Y",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"INCLUDE_SUBSECTIONS" => "Y",
	"PAGER_TEMPLATE" => "",
	"DISPLAY_TOP_PAGER" => "Y",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_DESC_NUMBERING" => "Y",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
	
	
	<section id="form-block" class="i-page-block i-page-content">
	<a name="form" class="b-anchor"></a>
	<div class="i-page-content-pad">
		<div class="b-icon i-form"></div>
		<div id="contact-form">
			<form action="<?=SITE_DIR?>developer/send.php" method="GET">
				<div class="b-form-field">
					<input type="email" name="email" value="" data-placeholder="Ваш Email" required class="b-input-text" />
				</div>
				<div class="b-form-field">
					<textarea name="text" cols="10" rows="10" data-placeholder="Ваш вопрос или заказ" required class="b-textarea"></textarea>
				</div>
				<div class="b-form-submit">
					<button type="submit" class="b-button">Отправить</button>
				</div>
			</form>
		</div>
		<div id="b-contact-form-message"></div>
	</div>
</section>

	
	<section id="address-block" class="i-page-block">
	<a name="address" class="b-anchor"></a>
	<div class="i-page-content-pad">
		<div class="b-icon i-address"></div>
		<div class="b-address__text"><? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/address.php', Array(), Array("MODE"      => "html"));?></div>
		<?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
	"INIT_MAP_TYPE" => "MAP",
	"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.7732688070937;s:10:\"yandex_lon\";d:37.498520032;s:12:\"yandex_scale\";i:18;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.498341382606924;s:3:\"LAT\";d:55.77335343809291;s:4:\"TEXT\";s:25:\"Офис «Сервисной компании»\";}}}",
	"MAP_WIDTH" => "100%",
	"MAP_HEIGHT" => "523",
	"CONTROLS" => array(
		0 => "ZOOM",
		1 => "SMALLZOOM",
	),
	"OPTIONS" => array(
		0 => "ENABLE_DRAGGING",
	),
	"MAP_ID" => ""
	),
	false
);?>

	
	</div>
	
	<footer id="b-footer" class="i-wide-content">
	<div class="i-page-content">
		<div class="b-bejet-block">
			<div class="b-bejet-block__text"><? $APPLICATION->IncludeFile(SITE_DIR.'include_areas/footer.php', Array(), Array("MODE"      => "html"));?></div>
			<a href="http://bejet.ru" target="_blank"><img src="<?=SITE_DIR?>images/icons/bejet.gif" width="32" height="32" alt="Bejet - готовый сайт компании" class="b-bejet-block__icon" /></a>
		</div>
	</div>
</footer>
		
</section>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>