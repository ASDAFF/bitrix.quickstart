<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.loopedslider.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/cufon-yui.js"></script>
<?$APPLICATION->ShowHead();?>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/colors.css" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/styles_addition.css" />


<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/script.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#slider').loopedSlider({
	    	autoStart: 3000
	    });
		
    });
    $(window).resize(function() {
    	$('#header .slider .item .item_body').css("width", $('#layer').width());
    });
</script>

<title><?$APPLICATION->ShowTitle()?></title>

<!--[if IE 6]>
<link href="styles_ie6.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/DD_belatedPNG.js"></script>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/styles_ie6.css" />
<script type="text/javascript">
DD_belatedPNG.fix('div, ul, li, span, img, a');
</script>
<![endif]-->

<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/styles_ie.css" />
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

</head>

<body>

	<div id="panel"><?$APPLICATION->ShowPanel();?></div>
	
	
<div id="layout">
	<div id="content">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td class="td1" width="22%">
			<div class="logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></div>
		</td>
		<td class="td2" width="22%">
			<div class="contacts">
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/icq.php"), false);?>
			</div>
		</td>
		<td class="td3" width="46%" >
			<?
				$APPLICATION->IncludeComponent("bitrix:menu", "top1", array(
					"ROOT_MENU_TYPE" => "left2",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "4",
					"CHILD_MENU_TYPE" => "left2",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
					),
					false
				);
			?>
		</td>
		</tr>
	</table>
		
	<div id="menu1">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="50%">
				
				
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
					
					
				</td>
		
				<td width="20%">
						<div class="kabinet"><a href="<?=SITE_DIR?>personal/"><?=GetMessage("PERSONAL")?></a></div>
				</td>
				<td width="15%">
					<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "store1", array(
											"REGISTER_URL" => SITE_DIR."login/",
											"PROFILE_URL" => SITE_DIR."personal/profile/",
											"SHOW_ERRORS" => "N"
											),
											false
					);?>
				</td>
			
				<td width="15%">
					<span id="cart_line">
						<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "template1", Array(
							"PATH_TO_BASKET" => SITE_DIR."personal/cart/",	// Страница корзины
							"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",	// Страница оформления заказа
							),
							false
						);?>
					</span>
				</td>
			</tr>
		</table>
		<div id="mainmenu_wrapper">
            <div id="mainmenu_wrapper_left">
			
				<?
								$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel1", array(
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
		</div>

<?if ($APPLICATION->GetCurDir()!=SITE_DIR && $APPLICATION->GetCurPage()!=SITE_DIR."404.php") {?>					
        
		<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "store", array(
							"START_FROM" => "0",
							"PATH" => "",
							"SITE_ID" => "-"
							),
							false,
							Array('HIDE_ICONS' => 'Y')
						);
		?>
		
<?//if (strpos($APPLICATION->GetCurDir(), "/catalog/")===false) {?>
		<table width="100%" class="pr" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="pr_lv" width="1%" >
				</td>
				<td class="pr_v" width="98%">
				</td>
				<td class="pr_rv" width="1%">
				</td>
			</tr>
			<tr>
				<td class="pr_lc" width="1%">
				</td>
				<td width="98%">					
					<table width="100%">
						<tr class="item">
							<td colspan="4" style="padding-left:10px;  width: 100%;">
								<div class="maintext">
									<?$APPLICATION->ShowTitle();?>
								</div>
							</td>			
						</tr>
						<tr>
						<?if (strpos($APPLICATION->GetCurDir(), "/catalog/")===false) {?>
							<td style="text-align:left;">
							<div id="main_content">
						<?}?>
						<?if (strpos($APPLICATION->GetCurDir(), "/catalog/")!==false) {?>
							<td style="padding-left:10px;" colspan="4" >
						<?}?>
<?}?>

