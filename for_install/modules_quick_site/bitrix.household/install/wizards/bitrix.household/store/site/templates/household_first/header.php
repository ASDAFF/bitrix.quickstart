<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/common.css" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.6.1.min.js"></script>
<?$APPLICATION->ShowHead();?>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/colors.css" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/styles_ie6.css" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/styles_addition.css" />

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/script.js"></script>

<title><?$APPLICATION->ShowTitle()?></title>
<!--[if IE 6]>
<link href="<?=SITE_TEMPLATE_PATH?>/styles_ie6.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/DD_belatedPNG.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('form, div, li, p, strong, span, img, a');
</script>
<![endif]-->

<!--[if lt IE 7]>
<style type="text/css">
	#compare {bottom:-1px; }
	div.catalog-admin-links { right: -1px; }
	div.catalog-item-card .item-desc-overlay {background-image:none;}
</style>
<![endif]-->

<!--[if IE]>
<style type="text/css">
	#fancybox-loading.fancybox-ie div	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_loading.png', sizingMethod='scale'); }
	.fancybox-ie #fancybox-close		{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_close.png', sizingMethod='scale'); }
	.fancybox-ie #fancybox-title-over	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_title_over.png', sizingMethod='scale'); zoom: 1; }
	.fancybox-ie #fancybox-title-left	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_title_left.png', sizingMethod='scale'); }
	.fancybox-ie #fancybox-title-main	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_title_main.png', sizingMethod='scale'); }
	.fancybox-ie #fancybox-title-right	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_title_right.png', sizingMethod='scale'); }
	.fancybox-ie #fancybox-left-ico		{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_nav_left.png', sizingMethod='scale'); }
	.fancybox-ie #fancybox-right-ico	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_nav_right.png', sizingMethod='scale'); }
	.fancybox-ie .fancy-bg { background: transparent !important; }
	.fancybox-ie #fancy-bg-n	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_n.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-ne	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_ne.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-e	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_e.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-se	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_se.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-s	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_s.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-sw	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_sw.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-w	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_w.png', sizingMethod='scale'); }
	.fancybox-ie #fancy-bg-nw	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/fancy_shadow_nw.png', sizingMethod='scale'); }
</style>
<![endif]-->

<script type="text/javascript">if (document.documentElement) { document.documentElement.id = "js" }</script>
</head>
<body>
	<div id="panel"><?$APPLICATION->ShowPanel();?></div>
	
	
<div id="layout">
	<table id="main" cellpadding="0" cellspacing="0" border="0">
		<tr class="main_content">
			<td class="sidebar">
				<div id="sidebar">
                	<div class="logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></div>
                	<div class="block">
					<?$APPLICATION->IncludeComponent("bitrix:search.title", "template1", array(
	"NUM_CATEGORIES" => "3",
	"TOP_COUNT" => "5",
	"ORDER" => "rank",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "N",
	"SHOW_OTHERS" => "Y",
	"PAGE" => "#SITE_DIR#search/",
	"CATEGORY_OTHERS_TITLE" => "",
	"CATEGORY_0_TITLE" => GetMessage("SEARCH_NEWS"),
	"CATEGORY_0" => array(
		0 => "iblock_news",
	),
	"CATEGORY_0_iblock_news" => array(
		0 => "1",
		1 => "4",
	),
	"CATEGORY_1_TITLE" => GetMessage("SEARCH_CATALOG"),
	"CATEGORY_1" => array(
		0 => "iblock_catalog",
		1 => "iblock_offers",
	),
	"CATEGORY_1_iblock_catalog" => array(
		0 => "all",
	),
	"CATEGORY_1_iblock_offers" => array(
		0 => "all",
	),
	"CATEGORY_2_TITLE" =>  GetMessage("SEARCH_ARTICLES"),
	"CATEGORY_2" => array(
		0 => "main",
	),
	"CATEGORY_2_main" => array(
	),
	"SHOW_INPUT" => "Y",
	"INPUT_ID" => "title-search-input",
	"CONTAINER_ID" => "search"
	),
	false
);?>


						<?
						$APPLICATION->IncludeComponent("bitrix:menu", "catalog", array(
	"ROOT_MENU_TYPE" => "left",
	"IBLOCK_IDS" => array(
		0 => "#PRODUCER_IBLOCK_ID#",
		1 => "",
	),
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "4",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);
						?>
                	</div>
					<div class="block">
						<?
						$APPLICATION->IncludeComponent("bitrix:menu", "left2", Array(
							"ROOT_MENU_TYPE" => "left2",	// Тип меню для первого уровня
							"MENU_CACHE_TYPE" => "A",	// Тип кеширования
							"MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
							"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
							"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
							"MAX_LEVEL" => "4",	// Уровень вложенности меню
							"CHILD_MENU_TYPE" => "left2",	// Тип меню для остальных уровней
							"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
							"DELAY" => "N",	// Откладывать выполнение шаблона меню
							"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
							),
							false
						);
						?>
					</div>
					<div class="block">
							<?$APPLICATION->IncludeComponent("bitrix:news.list", "store", array(
								"IBLOCK_TYPE" => "-",
								"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
								"NEWS_COUNT" => "4",
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
									1 => "DIRECTION",
									2 => "",
								),
								"CHECK_DATES" => "Y",
								"DETAIL_URL" => "",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_SHADOW" => "Y",
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
								"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_TITLE" => "",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "N",
								"DISPLAY_DATE" => "Y",
								"DISPLAY_NAME" => "Y",
								"DISPLAY_PICTURE" => "N",
								"DISPLAY_PREVIEW_TEXT" => "N",
								"AJAX_OPTION_ADDITIONAL" => ""
								),
								false
							);?>
					</div>
				</div>
			</td>
			<td class="content">
				<div id="content">
					<div class="header">
						<div class="header_main">
							<div class="header_body">
								<table class="grad" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td class="signup">
										<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "store1", array(
											"REGISTER_URL" => SITE_DIR."login/",
											"PROFILE_URL" => SITE_DIR."personal/profile/",
											"SHOW_ERRORS" => "N"
											),
											false
										);?>
										</td>
										<td class="basket">
											<span id="cart_line">
												<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "template1", array(
													"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
													"PATH_TO_ORDER" => SITE_DIR."personal/order/make/"
													),
													false
												);?>
											</span>
										</td>
										<td class="contacts">
                                        	<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/icq.php"), false);?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?>
										</td>
										<td class="techmenu">
											<ul>
												<li><a href="<?=SITE_DIR?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/icon_home_on.gif" width="11px" height="10px" alt="" /></a></li>
												<li><a href="<?=SITE_DIR?>about/contacts/"><img src="<?=SITE_TEMPLATE_PATH?>/images/icon_mail.gif" width="11px" height="8px" alt="" /></a></li>
											</ul>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);
					?>
					<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "store", array(
							"START_FROM" => "1",
							"PATH" => "",
							"SITE_ID" => "-"
							),
							false,
							Array('HIDE_ICONS' => 'Y')
						);
					?>
					<?if ($APPLICATION->GetCurDir()!="/" && $APPLICATION->GetCurPage()!="/404.php") {?>
					<div class="main_content">
					<h1><?$APPLICATION->ShowTitle();?></h1>
					<?}?>