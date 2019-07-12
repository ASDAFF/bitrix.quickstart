<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
	<head>
		<?$APPLICATION->ShowHead()?>
		<title><?$APPLICATION->ShowTitle()?></title>
		<!--[if lt IE 7]>
			<link href="<?=SITE_TEMPLATE_PATH?>/style_ie6.css" type="text/css" rel="stylesheet" media="all"/>
			<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/DD_belatedPNG.js"></script>
			<script>
				DD_belatedPNG.fix('*');
			</script>
		<![endif]-->
		<!--[if IE 7]>
			<link href="<?=SITE_TEMPLATE_PATH?>/style_ie7.css" type="text/css" rel="stylesheet" media="all"/>
		<![endif]-->
		<!--[if IE 8]>
			<link href="<?=SITE_TEMPLATE_PATH?>/style_ie8.css" type="text/css" rel="stylesheet" media="all"/>
		<![endif]-->
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jcarousellite_1.0.1.pack.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/wGallery.0.1.js"></script>
		<link type="text/css" rel="stylesheet" media="all" href="<?=SITE_TEMPLATE_PATH?>/wGallery.0.1.css"/>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
	</head>
	<body>
		<? $APPLICATION->ShowPanel() ?>
		<div id="index">
			<div id="top">
				<div id="maxwidther">
					<div id="header">
						<div id="toplogo">
							<? if ($_SERVER['PHP_SELF'] == SITE_DIR . 'index.php'): ?>
								<img class='pngfix' src='<?=SITE_TEMPLATE_PATH?>/images/logo.png' />
							<? else: ?>
								<a href="<?=SITE_DIR?>" title="На главную" ><img class='pngfix' src='<?=SITE_TEMPLATE_PATH?>/images/logo.png' /></a>
							<? endif ?>
						</div>
						<?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel", array(
							"ROOT_MENU_TYPE" => "top",
							"MENU_CACHE_TYPE" => "A",
							"MENU_CACHE_TIME" => "3600",
							"MENU_CACHE_USE_GROUPS" => "N",
							"MENU_CACHE_GET_VARS" => "",
							"MAX_LEVEL" => "2",
							"CHILD_MENU_TYPE" => "left",
							"USE_EXT" => "N",
							"DELAY" => "N",
							"ALLOW_MULTI_SELECT" => "N"
							),
							false,
							array(
							"ACTIVE_COMPONENT" => "Y"
							)
						);?>
						<?$APPLICATION->IncludeComponent("bitrix:search.form", "top_search", Array(
							"PAGE" => "#SITE_DIR#search/",
							),
							false
						);?>
						<div class="ca"></div>
					</div>
					<div id="main">
						<div id="main-round-top" class="round">
							<div class="round-left"><div class="round-right"><div class="round-repeat"></div></div></div>
						</div>
						<div id="main-bg">
							<?$APPLICATION->IncludeComponent("bitrix:news.list", "services", array(
								"IBLOCK_TYPE" => "content",
								"IBLOCK_ID" => "#S_GALLERY_ID#",
								"NEWS_COUNT" => "50",
								"SORT_BY1" => "SORT",
								"SORT_ORDER1" => "ASC",
								"SORT_BY2" => "SORT",
								"SORT_ORDER2" => "ASC",
								"FILTER_NAME" => "",
								"FIELD_CODE" => array(
									0 => "",
									1 => "",
								),
								"PROPERTY_CODE" => array(
									0 => "LINK",
									1 => "",
								),
								"CHECK_DATES" => "Y",
								"DETAIL_URL" => "",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_SHADOW" => "Y",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"AJAX_OPTION_HISTORY" => "N",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "3600",
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
								"PAGER_TITLE" => "Новости",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => "",
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
							<div id="content" class="column">
								<div class="column-left">
									<?$APPLICATION->IncludeComponent(
										"bitrix:main.include",
										"",
										Array(
											"AREA_FILE_SHOW" => "sect",
											"AREA_FILE_SUFFIX" => "left",
											"AREA_FILE_RECURSIVE" => "Y",
											"EDIT_TEMPLATE" => ""
										),
									false
									);?>
									<?$APPLICATION->IncludeComponent("bitrix:menu", "services_menu", array(
										"ROOT_MENU_TYPE" => "services",
										"MENU_CACHE_TYPE" => "A",
										"MENU_CACHE_TIME" => "3600",
										"MENU_CACHE_USE_GROUPS" => "Y",
										"MENU_CACHE_GET_VARS" => array(
											0 => "SECTION_ID",
											1 => "page",
											2 => "",
										),
										"MAX_LEVEL" => "2",
										"CHILD_MENU_TYPE" => "left",
										"USE_EXT" => "Y",
										"DELAY" => "N",
										"ALLOW_MULTI_SELECT" => "N"
										),
										false
									);?>
								</div>
								<div class="column-right">
									<?$APPLICATION->IncludeComponent("bitrix:news.list", "discounts", array(
										"IBLOCK_TYPE" => "content",
										"IBLOCK_ID" => "#DISCOUNTS_ID#",
										"NEWS_COUNT" => "2",
										"SORT_BY1" => "SORT",
										"SORT_ORDER1" => "ASC",
										"SORT_BY2" => "SORT",
										"SORT_ORDER2" => "ASC",
										"FILTER_NAME" => "",
										"FIELD_CODE" => array(
											0 => "",
											1 => "",
										),
										"PROPERTY_CODE" => array(
											0 => "DISCOUNT",
											1 => "LINK",
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
										"CACHE_TIME" => "3600",
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
										"PAGER_TITLE" => "Новости",
										"PAGER_SHOW_ALWAYS" => "N",
										"PAGER_TEMPLATE" => "",
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
									<?$APPLICATION->IncludeComponent(
										"bitrix:main.include",
										"",
										Array(
											"AREA_FILE_SHOW" => "sect",
											"AREA_FILE_SUFFIX" => "inc",
											"AREA_FILE_RECURSIVE" => "Y",
											"EDIT_TEMPLATE" => ""
										),
									false
									);?>
								</div>
								<div class="column-center">
									<div class="content">
										<h1><? $APPLICATION->ShowTitle() ?></h1>
