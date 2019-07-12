<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
CUtil::InitJSCore();
$curPage = $APPLICATION->GetCurPage(true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.8.2.min.js"></script>
	<?$APPLICATION->ShowHead();?>
	<?if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") && !strpos($_SERVER['HTTP_USER_AGENT'], "MSIE 10.0")):?>
		<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/ie.css"/>
	<?endif?>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/slides.min.jquery.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/script.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.carouFredSel-5.6.4-packed.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.cookie.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.jcarousel.min.js" type="text/javascript"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.timers.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/jcarousel.css"/>


	<title><?$APPLICATION->ShowTitle()?></title>



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
<?
function ShowTitleOrHeader()
{
	global $APPLICATION;
	if ($APPLICATION->GetPageProperty("ADDITIONAL_TITLE"))
		return $APPLICATION->GetPageProperty("ADDITIONAL_TITLE");
	else
		return $APPLICATION->GetTitle(false);
}

?>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>

<?$APPLICATION->ShowProperty("CATALOG_COMPARE_LIST", "");?>
<div class="wrap">


	<div class="body">

        <div class="content_box">
	<div class="header">


		<div class="header-brandzone">

				<div class="cart">
					<span id="cart_line">
					<?
						$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", ".default", array(
						"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
						"PATH_TO_PERSONAL" => SITE_DIR."personal/",
						"SHOW_PERSONAL_LINK" => "N"
						),
						false,
						Array('')
						);
					?>
					</span><div style="clear: both;"></div>
					 
				</div>


			<div class="contactsdata">
				<div class="tel" ><a href="<?=SITE_DIR?>about/contacts/"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></a></div>
				<div class="address">
						<p><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/address.php"), false);?></p>
						
				</div>
			</div>

			<div class="socials">
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/contact.php"), false, Array("HIDE_ICONS" => "Y"));?>
			</div>

			<div class="brand">
				<a href="<?=SITE_DIR?>"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => SITE_DIR."include/company_logo.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?></a>
			</div>
		</div>
	 

	 
 

			<div class="content_section_menu">
				<div class="content_section_menu_inner">
					<div class="content_section_menu_left"><?$APPLICATION->IncludeComponent("bitrix:menu", "tree_horizontal_menu", array(
	"ROOT_MENU_TYPE" => "left",
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
);?></div>


					<div class="content_section_menu_right" id="search-form">

 							<?$APPLICATION->IncludeComponent("bitrix:search.title", "best", array(
								"NUM_CATEGORIES" => "1",
								"TOP_COUNT" => "5",
								"CHECK_DATES" => "N",
								"SHOW_OTHERS" => "Y",
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
								"CONTAINER_ID" => "search-form"
							),
							false
						);?>

				</div>
				

				</div>



			</div>



	</div><!-- // .header -->


	<div id="smart-filter" ></div>

            <div class="centralarea" id="central">
 

			<div class="workarea">
			<?
		if ($curPage != SITE_DIR."index.php"):?>
			<div id="headers">
				<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
					"START_FROM" => "0",
					"PATH" => "",
					"SITE_ID" => "-"
					),
					false
				);?>
				<div id="headers1">
					<h1 id="header" class="main_header"><?$APPLICATION->AddBufferContent("ShowTitleOrHeader");?></h1>	
					<?$APPLICATION->AddBufferContent("ShowArticle");?>
				</div>
			</div>

				
		<?endif?>


				