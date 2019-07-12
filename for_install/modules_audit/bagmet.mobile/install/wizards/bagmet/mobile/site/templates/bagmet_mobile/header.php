<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
//$wizTemplateId = COption::GetOptionString("main", "wizard_template_id", "shoes_full", SITE_ID);
CUtil::InitJSCore();
CJSCore::Init(array("jquery"));
$curPage = $APPLICATION->GetCurPage(true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
	<?//$APPLICATION->ShowHead();
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
	$APPLICATION->ShowMeta("robots", false, true);
	$APPLICATION->ShowMeta("keywords", false, true);
	$APPLICATION->ShowMeta("description", false, true);
	$APPLICATION->ShowCSS(true, true);
	?>
	<link rel="stylesheet" type="text/css" href="<?=CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH."/colors.css")?>" />
	<?
	if (file_exists($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/custom_styles.css"))
	{
	?>
	<link rel="stylesheet" type="text/css" href="<?=CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH."/custom_styles.css")?>" />
	<?
	}
	$APPLICATION->ShowHeadStrings();
	$APPLICATION->ShowHeadScripts();

	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/messages/css/gritter.css');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/messages/js/jquery.gritter.js');

	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/script.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/carouFredSel/jquery.carouFredSel-6.1.0.js');
	?>

	<title><?$APPLICATION->ShowTitle()?></title>
	<!--[if IE]>
	<style type="text/css">
		#fancybox-loading.fancybox-ie div	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_loading.png', sizingMethod='scale'); }
		.fancybox-ie #fancybox-close		{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_close.png', sizingMethod='scale'); }
		.fancybox-ie #fancybox-title-over	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_title_over.png', sizingMethod='scale'); zoom: 1; }
		.fancybox-ie #fancybox-title-left	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_title_left.png', sizingMethod='scale'); }
		.fancybox-ie #fancybox-title-main	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_title_main.png', sizingMethod='scale'); }
		.fancybox-ie #fancybox-title-right	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_title_right.png', sizingMethod='scale'); }
		.fancybox-ie #fancybox-left-ico		{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_nav_left.png', sizingMethod='scale'); }
		.fancybox-ie #fancybox-right-ico	{ background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_nav_right.png', sizingMethod='scale'); }
		.fancybox-ie .fancy-bg { background: transparent !important; }
		.fancybox-ie #fancy-bg-n	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_n.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-ne	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_ne.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-e	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_e.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-se	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_se.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-s	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_s.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-sw	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_sw.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-w	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_w.png', sizingMethod='scale'); }
		.fancybox-ie #fancy-bg-nw	{ filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>/js/fancybox/fancy_shadow_nw.png', sizingMethod='scale'); }
	</style>
	<![endif]-->

</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<?//$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/feedback.php"), false);?>

<div id='wrapper'>
	<div class="header">
		<div class="top_menu">
			<ul class="top_menu_container">
				<li class="top_menu_main">
					<?
					$APPLICATION->IncludeComponent('bitrix:menu', "top_main", array(
							"ROOT_MENU_TYPE" => "top_main",
							"MENU_CACHE_TYPE" => "Y",
							"MENU_CACHE_TIME" => "36000000",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => array(),
							"MAX_LEVEL" => "1",
							"USE_EXT" => "N",
							"ALLOW_MULTI_SELECT" => "N"
						)
					);
					?>
				</li>
				<li class="top_menu_auth">
					<ul>
						<li>
							<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "short", array(
								"REGISTER_URL" => SITE_DIR."login/",
								"PROFILE_URL" => SITE_DIR."personal/",
								"SHOW_ERRORS" => "N"
								),
								false,
								Array()
							);?>
						</li>
						<li class="compare_link" id="compare">
							<?$APPLICATION->ShowProperty("CATALOG_COMPARE_LIST", "");?>
						</li>
					</ul>
				</li>
				<li id="cart">
					<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", ".default", array(
							"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
							"PATH_TO_PERSONAL" => SITE_DIR."personal/",
							"SHOW_PERSONAL_LINK" => "N"
						),
						false,
						Array('')
					);?>
				</li>
			</ul>
		</div>
		<div class="header_content">
			<div class="name">
				<a href='<?=SITE_DIR?>'>
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?>
				</a>
			</div>
			<div class="phone">
				<a href="<?=SITE_DIR?>about/contacts/"><span><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></span></a>
				<p><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></p>
			</div>
		</div>
	</div>
    <div class="top_wrapper">
		<div class="main_menu_horizontal">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog_horizontal", array(
				"ROOT_MENU_TYPE" => "top",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_TIME" => "36000000",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "3",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?>
		</div>

		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "sect",
				"AREA_FILE_SUFFIX" => "banner",
				"AREA_FILE_RECURSIVE" => "N",
				"EDIT_MODE" => "html",
			),
			false,
			Array('HIDE_ICONS' => 'Y')
		);?>
		<div class="search_line">
			<div class="search_block">
				<?$APPLICATION->IncludeComponent("bitrix:search.title", ".default", array(
					"NUM_CATEGORIES" => "1",
					"TOP_COUNT" => "5",
					"ORDER" => "date",
					"USE_LANGUAGE_GUESS" => "Y",
					"CHECK_DATES" => "N",
					"SHOW_OTHERS" => "N",
					"PAGE" => SITE_DIR."catalog/",
					"CATEGORY_0_TITLE" => GetMessage("SHOES_SEARCH_GOODS"),
					"CATEGORY_0" => array(
						0 => "iblock_catalog",
					),
					"CATEGORY_0_iblock_catalog" => array(
						0 => "all",
					),
					"SHOW_INPUT" => "Y",
					"INPUT_ID" => "title-search-input",
					"CONTAINER_ID" => "search",
					"PRICE_CODE" => array(
						0 => "BASE",
					),
					"PREVIEW_TRUNCATE_LEN" => "",
					"SHOW_PREVIEW" => "Y",
					"PREVIEW_WIDTH" => "75",
					"PREVIEW_HEIGHT" => "75",
					"CONVERT_CURRENCY" => "N"
					),
					false
				);?>
			</div>
		</div><!-- // search_line -->
	</div> <!-- // top_wrapper -->
	<div class="page">
		<?if ($curPage != SITE_DIR."index.php"):?>
			<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
					"START_FROM" => "1",
					"PATH" => "",
					"SITE_ID" => SITE_ID
				),
				false,
				Array('HIDE_ICONS' => 'Y')
			);?>

			<div class="for_page_title">
				<h1><?$APPLICATION->ShowTitle(false);?></h1>
			</div>
		<?endif?>
		<div class="workarea">

