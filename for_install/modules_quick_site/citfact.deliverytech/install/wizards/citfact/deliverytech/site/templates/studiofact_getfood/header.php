<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
include ($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/functions.php");
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID;?>" lang="<?=LANGUAGE_ID;?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
		<meta name="viewport" content="width = device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, target-densitydpi = device-dpi">
		<meta name="format-detection" content="telephone=no">
		<meta http-equiv="cleartype" content="on">
		<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR;?>favicon.ico">
		<!-- saved from url=(0014)about:internet -->
		<title><?$APPLICATION->ShowTitle();?></title>
		<?
		$APPLICATION->ShowMeta("robots", false, true);
		$APPLICATION->ShowMeta("keywords", false, true);
		$APPLICATION->ShowMeta("description", false, true);
		$APPLICATION->ShowCSS(true, true);
		$APPLICATION->ShowHeadStrings();
		$APPLICATION->ShowHeadScripts();

		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.8.2.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/modernizr.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.easing.1.3.js");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/owl.carousel.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/owl.carousel.min.js");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/jquery.scrollbar/jquery.scrollbar.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.scrollbar/jquery.scrollbar.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.flexslider-min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.inputmask.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/pvk.js");
		?>
		<? $color = COption::GetOptionString("main", "sf_template_color", "orange");
		global $sf_template_color;
		if (strlen($sf_template_color) > 1) { $color = $sf_template_color; } ?>
		<link href="<?=SITE_TEMPLATE_PATH;?>/css/colors_<?=$color?>.css" type="text/css"  rel="stylesheet" />
		<link rel="stylesheet" media="all and (max-width: 1280px)" href="<?=SITE_TEMPLATE_PATH;?>/css/resize1280.css" />
		<link rel="stylesheet" media="all and (max-width: 980px)" href="<?=SITE_TEMPLATE_PATH;?>/css/resize980.css" />
		<link rel="stylesheet" media="all and (max-width: 768px)" href="<?=SITE_TEMPLATE_PATH;?>/css/resize768.css" />
		<link rel="stylesheet" media="all and (max-width: 480px)" href="<?=SITE_TEMPLATE_PATH;?>/css/resize480.css" />
		<link rel="stylesheet" media="all and (max-width: 320px)" href="<?=SITE_TEMPLATE_PATH;?>/css/resize320.css" />
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,700,700italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
		<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/tech.css"); ?>
		<!--[if lt IE 9]>
        <script type='text/javascript' src="<?=SITE_TEMPLATE_PATH;?>/js/html5.js"></script>
        <script type='text/javascript' src="<?=SITE_TEMPLATE_PATH;?>/js/css3-mediaqueries.js"></script>
        <![endif]-->
	</head>
	<body itemscope itemtype="http://schema.org/LocalBusiness">
		<? if ($_REQUEST["open_popup"] != "Y") { ?>
		<div id="panel"><?$APPLICATION->ShowPanel();?></div>
		<div class="wrapper">
			<header id="header">
				<div class="header_menu">
					<div class="container">
						<div class="fl">
							<?$APPLICATION->IncludeComponent('bitrix:menu', "top_menu", array(
									"ROOT_MENU_TYPE" => "top",
									"MENU_CACHE_TYPE" => "Y",
									"MENU_CACHE_TIME" => "36000000",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_CACHE_GET_VARS" => array(),
									"MAX_LEVEL" => "1",
									"USE_EXT" => "N",
									"ALLOW_MULTI_SELECT" => "N"
								)
							);?>
						</div>
						<div class="fr user_auth">
							<?$APPLICATION->IncludeComponent("studiofact:auth", "", Array());?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="header">
					<div class="container">
						<div class="fl">
							<span class="inline mobile mobile_menu"></span>
							<a href="<?=SITE_DIR;?>" class="logo inline" title="<?=GetMessage("STUDIOFACT_MAIN");?>"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/header_logo.php"), false);?></a>
							<div class="phone inline">
								<span itemprop="telephone"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/header_phone.php"), false);?></span>
								<?=GetMessage("STUDIOFACT_FEEDBACK1");?> <a href="#feedback_form" class="open_feedback javascript"><?=GetMessage("STUDIOFACT_FEEDBACK2");?></a>
							</div>
						</div>
						<div class="fr">
							<div class="search_box radius5 fl">
								<?$APPLICATION->IncludeComponent("bitrix:search.title", "visual", array(
										"NUM_CATEGORIES" => "1",
										"TOP_COUNT" => "5",
										"CHECK_DATES" => "N",
										"SHOW_OTHERS" => "N",
										"PAGE" => SITE_DIR."catalog/",
										"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS") ,
										"CATEGORY_0" => array(
											0 => "iblock_catalog",
										),
										"CATEGORY_0_iblock_catalog" => array(
											0 => "all",
										),
										"CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
										"SHOW_INPUT" => "Y",
										"INPUT_ID" => "title-search-input",
										"CONTAINER_ID" => "search",
										"PRICE_CODE" => array(
											0 => "BASE",
										),
										"SHOW_PREVIEW" => "Y",
										"PREVIEW_WIDTH" => "75",
										"PREVIEW_HEIGHT" => "75",
										"CONVERT_CURRENCY" => "Y"
									),
									false
								);?>
							</div>
							<div id="small_basket_box" class="fr" data-path="<?=SITE_DIR."include/small_basket.php";?>">
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/small_basket.php"), false);?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					</div>						
			</header>
			<? if ($APPLICATION->GetCurPage(true) == SITE_DIR."index.php" && ERROR_404 != "Y") { ?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/main_banner_big.php"), false);?>
			<? } ?>
			<div class="main">
				<div class="container main_container">
					<div class="mobile">
						<div id="mobile_menu_list" class="radius5">
							<?$APPLICATION->IncludeComponent("bitrix:menu", "left_menu", array(
									"ROOT_MENU_TYPE" => "left",
									"MENU_CACHE_TYPE" => "A",
									"MENU_CACHE_TIME" => "36000000",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_THEME" => "site",
									"CACHE_SELECTED_ITEMS" => "N",
									"MENU_CACHE_GET_VARS" => array(
									),
									"MAX_LEVEL" => "2",
									"CHILD_MENU_TYPE" => "left",
									"USE_EXT" => "Y",
									"DELAY" => "N",
									"ALLOW_MULTI_SELECT" => "N",
								),
								false
							);?>
							<div class="mobile_dop_menu_list">
								<hr />
								<?$APPLICATION->IncludeComponent('bitrix:menu', "left_menu", array(
										"ROOT_MENU_TYPE" => "top",
										"MENU_CACHE_TYPE" => "Y",
										"MENU_CACHE_TIME" => "36000000",
										"MENU_CACHE_USE_GROUPS" => "Y",
										"MENU_CACHE_GET_VARS" => array(),
										"MAX_LEVEL" => "1",
										"USE_EXT" => "N",
										"ALLOW_MULTI_SELECT" => "N"
									)
								);?>
								<hr />
								<div class="user_auth"><?$APPLICATION->IncludeComponent("studiofact:auth", "", Array());?></div>
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/dop_left_menu.php"), false);?>
							</div>
						</div>
					</div>
					<div id="left_side" class="radius5">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "left_menu", array(
								"ROOT_MENU_TYPE" => "left",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "36000000",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"MENU_THEME" => "site",
								"CACHE_SELECTED_ITEMS" => "N",
								"MENU_CACHE_GET_VARS" => array(
								),
								"MAX_LEVEL" => "2",
								"CHILD_MENU_TYPE" => "left",
								"USE_EXT" => "Y",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "N",
							),
							false
						);?>	
					</div>
					<div id="left_sideApp" class="radius5">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/dop_left_menu.php"), false);?>
					</div>
					<div class="content">
						<div id="main_block_page">
							<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
									"START_FROM" => "0",
									"PATH" => "",
									"SITE_ID" => "-"
								),
								false,
								Array('HIDE_ICONS' => 'Y')
							);?>
							<? if ($APPLICATION->GetCurPage(true) != SITE_DIR."index.php" && ERROR_404 != "Y") { ?>
								<h1><?=$APPLICATION->ShowTitle(false);?></h1>
							<? } ?>
		<? } ?>
