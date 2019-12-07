<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html>
<!--[if IE 9]> <html class="ie9"> <![endif]-->
<!--[if IE 8]> <html class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html>
<!--<![endif]-->
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<meta name="robots" content="all">
	<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>">
	<?$APPLICATION->ShowMeta("keywords")?>
	<?$APPLICATION->ShowMeta("description")?>
	
	<link type="image/x-icon" rel="shortcut icon" href="<?=SITE_DIR?>favicon.ico" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic,300,300italic&subset=latin,cyrillic,latin-ext,cyrillic-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=PT+Serif:400,400italic,700,700italic&subset=latin,cyrillic,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
	<link href="<?=SITE_TEMPLATE_PATH?>/fonts/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/fonts/fontello/css/fontello.css" rel="stylesheet">

	<link href="<?=SITE_TEMPLATE_PATH?>/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/rs-plugin/css/settings.css" media="screen" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/rs-plugin/css/extralayers.css" media="screen" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/magnific-popup/magnific-popup.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/css/animations.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/owl-carousel/owl.carousel.css" rel="stylesheet">	
	
	<?$APPLICATION->ShowCSS();?>

	<!-- Theme CSS -->
	<link href="<?=SITE_TEMPLATE_PATH?>/css/theme/color/<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["COLOR"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["COLOR"] : COption::GetOptionString("businesscard", "QUICK_THEME_COLOR", "red", SITE_ID))?>.css" rel="stylesheet" type="text/css">	
	<link href="<?=SITE_TEMPLATE_PATH?>/css/theme/background/<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["BACKGROUND"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["BACKGROUND"] : COption::GetOptionString("businesscard", "QUICK_THEME_BACKGROUND", "background0", SITE_ID))?>.css" rel="stylesheet" type="text/css">	
	<link href="<?=SITE_TEMPLATE_PATH?>/css/theme/boxed/<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["BOXED"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["BOXED"] : COption::GetOptionString("businesscard", "QUICK_THEME_BOXED", "standard", SITE_ID))?>.css" rel="stylesheet" type="text/css">	

	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/modernizr.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/isotope/isotope.pkgd.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/owl-carousel/owl.carousel.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.appear.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.parallax-1.1.3.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.browser.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/SmoothScroll.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/plugins.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/template.js"></script>

	<?$APPLICATION->ShowHeadStrings();?>
	<?$APPLICATION->ShowHeadScripts();?>
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body><div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="page-wrapper">
	<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => "#SITE_DIR#include/line.php", 
		),
		false,
		array("ACTIVE_COMPONENT" => (!empty($_SESSION["QUICK_THEME"][SITE_ID]["LINE"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["LINE"] : COption::GetOptionString("businesscard", "QUICK_THEME_LINE", "N", SITE_ID)))
	);?>
	<div class="header-top <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["HEADER_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["HEADER_BG"] : COption::GetOptionString("businesscard", "QUICK_THEME_HEADER_BG", "white", SITE_ID))?>">
		<div class="container">
			<div class="row">
				<div class="col-xs-8 col-sm-4 col-md-4">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "#SITE_DIR#include/phone.php",
						)
					);?>
				</div>
				<div class="hidden-xs col-sm-5 col-md-4">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "#SITE_DIR#include/email.php",
						)
					);?>
				</div>
				<div class="col-xs-4 col-sm-3 col-md-4">
					<div class="header-top-dropdown">
						<div class="btn-group dropdown">
							<button type="button" class="btn dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown"><i class="fa fa-phone text-default"></i> <?=GetMessage("QUICK_BUSINESSCARD_HEADER_CALLBACK")?></button>
							<button type="button" class="btn dropdown-toggle hidden-lg hidden-md" data-toggle="dropdown"><i class="fa fa-phone text-default"></i></button>									
							<ul class="dropdown-menu dropdown-menu-right dropdown-animation">
								<li>
									<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
											"AREA_FILE_SHOW" => "file",
											"PATH" => "#SITE_DIR#include/form-callback-modal.php",
										)
									);?>
								</li>
							</ul>
						</div>
						<div class="btn-group dropdown">
							<button type="button" class="btn dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown"><i class="fa fa-search text-default"></i> <?=GetMessage("QUICK_BUSINESSCARD_HEADER_SEARCH")?></button>
							<button type="button" class="btn dropdown-toggle hidden-lg hidden-md" data-toggle="dropdown"><i class="fa fa-search text-default"></i></button>
							<ul class="dropdown-menu dropdown-menu-right dropdown-animation">
								<li>
									<?$APPLICATION->IncludeComponent("bitrix:search.form","flat",Array(
											"USE_SUGGEST" => "N",
											"PAGE" => "/search/"
										)
									);?>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
	jQuery(function(){
		var	headerTopHeight = $(".header-top").outerHeight(),
		headerHeight = $("header.header.float").outerHeight();
		$(window).scroll(function() {
			if (($(".header.float").length > 0)) { 
				if(($(this).scrollTop() > headerTopHeight+headerHeight) && ($(window).width() > 767)) {
					$("body").addClass("fixed-header-on");
					$(".header.float").addClass('animated object-visible fadeInDown');
					if (!($(".header.transparent").length>0)) {
						if ($(".banner:not(.header-top)").length>0) {
							$(".banner").css("marginTop", (headerHeight)+"px");
						} else if ($(".page-intro").length>0) {
							$(".page-intro").css("marginTop", (headerHeight)+"px");
						} else if ($(".page-top").length>0) {
							$(".page-top").css("marginTop", (headerHeight)+"px");
						} else {
							$("section.main-container").css("marginTop", (headerHeight)+"px");
						}
					}
				} else {
					$("body").removeClass("fixed-header-on");
					$("section.main-container").css("marginTop", (0)+"px");
					$(".banner").css("marginTop", (0)+"px");
					$(".page-intro").css("marginTop", (0)+"px");
					$(".page-top").css("marginTop", (0)+"px");
					$(".header.float").removeClass('animated object-visible fadeInDown');
				}
			};
		});		
	});
	</script>
	<header class="header <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU_TRANSPARENT"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU_TRANSPARENT"] : COption::GetOptionString("businesscard", "QUICK_THEME_MENU_TRANSPARENT", "menu-transparent", SITE_ID))?> <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU"] : COption::GetOptionString("businesscard", "QUICK_THEME_MENU", "float", SITE_ID))?> <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("businesscard", "QUICK_THEME_HEADER_MENU_BG", "white", SITE_ID))?> clearfix">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<div class="header-left clearfix">
						<div class="logo">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "#SITE_DIR#include/company-logo.php", 
								)
							);?>
						</div>
					</div>
				</div>
				<div class="col-md-10">
					<div class="header-right clearfix">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
								"ROOT_MENU_TYPE" => "top",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"MENU_CACHE_GET_VARS" => "",
								"MAX_LEVEL" => "4",
								"CHILD_MENU_TYPE" => "podmenu",
								"USE_EXT" => "Y",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "N",
							),
							false
						);?>
					</div>
				</div>
			</div>
		</div>
	</header>
	<div class="page-intro">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?$APPLICATION->IncludeComponent("bitrix:breadcrumb","breadcrumb",Array(
								"START_FROM" => "0",
								"PATH" => "",
								"SITE_ID" => SITE_ID,
							),
							false
					);?>
				</div>
			</div>
		</div>
	</div>
	<section class="main-container">
		<div class="container">
			<div class="row">
				<?if((!empty($_SESSION["QUICK_THEME"][SITE_ID]["SIDEBAR"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SIDEBAR"] : COption::GetOptionString("businesscard", "QUICK_THEME_SIDEBAR", "left", SITE_ID)) == "left"):?>
				<div class="col-md-3 hidden-xs hidden-sm">
					<div class="sidebar">
						<div class="block">
							<?$APPLICATION->IncludeComponent("bitrix:menu", "left", Array(
									"ROOT_MENU_TYPE" => "left",
									"MENU_CACHE_TYPE" => "A",
									"MENU_CACHE_TIME" => "3600",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_CACHE_GET_VARS" => "",
									"MAX_LEVEL" => "2",
									"CHILD_MENU_TYPE" => "podmenu",
									"USE_EXT" => "Y",
									"DELAY" => "N",
									"ALLOW_MULTI_SELECT" => "N",
									"MENU_THEME" => "site"
								),
								false
							);?>
						</div>
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
								"AREA_FILE_SHOW" => "sect",
								"AREA_FILE_SUFFIX" => "description",
							)
						);?>
					</div>
				</div>
				<?endif?>
				<div class="main col-md-9">
<?global $arrFilter; $arrFilter=array("!PROPERTY_TOP"=>false);?>