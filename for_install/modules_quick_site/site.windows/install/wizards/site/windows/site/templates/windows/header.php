<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<meta name="description" content=" | Real estate company"/>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/css/bootstrap.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_TEMPLATE_PATH?>/bootstrap/css/responsive.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_TEMPLATE_PATH?>/css/camera.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_TEMPLATE_PATH?>/css/style.css"/>
	<link rel='stylesheet' id='font-awesome-css' href='<?=SITE_TEMPLATE_PATH?>/css/font-awesome.css' type='text/css' media='all'/>
	<link rel='stylesheet' id='magnific-popup-css' href='<?=SITE_TEMPLATE_PATH?>/css/magnific-popup.css' type='text/css' media='all'/>
	<link rel='stylesheet' id='options_typography_PT+Sans-css' href='../../fonts.googleapis.com/css@family=PT+Sans&subset=latin' type='text/css' media='all'/>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/comment-reply.min.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery-1.7.2.min.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/swfobject.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/modernizr.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.elastislide.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jflickrfeed.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/custom.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/bootstrap/js/bootstrap.min.js'></script>
	<?$APPLICATION->ShowHead();?>
	<script>
		CHILD_URL ='<?=SITE_TEMPLATE_PATH?>',
	PARENT_URL = '<?=SITE_TEMPLATE_PATH?>'</script>
	<style type='text/css'>
		h1{font:bold 60px/72px PT Sans,sans-serif;color:#1e1e1e;}
		h2{font:normal 30px/35px PT Sans,sans-serif;color:#1e1e1e;}
		h3{font:normal 30px/35px PT Sans,sans-serif;color:#1e1e1e;}
		h4{font:normal 19px/25px PT Sans,sans-serif;color:#1e1e1e;}
		h5{font:normal 14px/20px PT Sans,sans-serif;color:#1e1e1e;}
		h6{font:normal 12px/18px Arial,Helvetica,sans-serif;color:#333333;}
		.main-holder{font:normal 14px/22px PT Sans,sans-serif;color:#b1b1b1;}
		.logo_h__txt,.logo_link{font:bold 70px/70px PT Sans,sans-serif;color:#1e1e1e;}
		.sf-menu>li>a{font:bold 19px/36px PT Sans,sans-serif;color:#d9d8d8;}
		.nav.footer-nav a{font:bold 19px/36px PT Sans,sans-serif;color:#d9d8d8;}
	</style>
	<!--[if lt IE 8]>
	<div style=' clear: both; text-align:center; position: relative;'>
	<a href="http://www.microsoft.com/windows/internet-explorer/default.aspx@ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" alt="" /></a>
	</div>
	<![endif]-->
	<!--[if (gt IE 9)|!(IE)]><!-->
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.mobile.customized.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.sf-menu').mobileMenu({defaultText: "Navigate to..."});
		});
	</script>
	<!--<![endif]-->
	<script type="text/javascript">
		// Init navigation menu
		jQuery(function(){
			// main navigation init
			jQuery('ul.sf-menu').superfish({
				delay: 1000, // the delay in milliseconds that the mouse can remain outside a sub-menu without it closing
				animation: {
					opacity: "show",
					height: "show"
				}, // used to animate the sub-menu open
				speed: "normal", // animation speed
				autoArrows: false, // generation of arrow mark-up (for submenu)
				disableHI: true // to disable hoverIntent detection
			});

			//Zoom fix
			//IPad/IPhone
			var viewportmeta = document.querySelector && document.querySelector('meta[name="viewport"]'),
			ua = navigator.userAgent,
			gestureStart = function () {
				viewportmeta.content = "width=device-width, minimum-scale=0.25, maximum-scale=1.6";
			},
			scaleFix = function () {
				if (viewportmeta && /iPhone|iPad/.test(ua) && !/Opera Mini/.test(ua)) {
					viewportmeta.content = "width=device-width, minimum-scale=1.0, maximum-scale=1.0";
					document.addEventListener("gesturestart", gestureStart, false);
				}
			};
			scaleFix();
		})
	</script>
</head>
<body class="home page page-id-203 page-template page-template-page-home-php">
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="main-holder">

<header class="header">
	<div class="container">
		<div class="row">
			<div class="span12">
				<div class="row">
					<div class="span12 row-fluid">

						<div class="logo span9">
							<a href="<?=SITE_DIR?>"  class="logo_h logo_h__img" title="<?=GetMessage('LK_MAIN')?>"><?
								$APPLICATION->IncludeFile(
									SITE_DIR."include/company_name.php",
									Array(),
									Array("MODE"=>"html")
								);
							?></a>
							<div>
							<?
								$APPLICATION->IncludeFile(
									SITE_DIR."include/company_slogan.php",
									Array(),
									Array("MODE"=>"html")
								);
							?>
							</div>
						</div>

<? $arDir = explode('/', $APPLICATION->GetCurDir())?>
<? if ($arDir[1] != 'contacts'):?>
<div class="span3">
<div class="row">
<button type="button" class="btn btn-primary btn-lg btn-feedback" onclick="window.location.href='<?=SITE_DIR?>contacts/feedback/';"><?=GetMessage('LK_FEEDBACK')?></button>
</div>
</div>
<? endif;?>
					</div>
				</div>
				<div class="row">
					<div class="span12 menu-holder">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
								"ROOT_MENU_TYPE" => "top",
								"MAX_LEVEL" => "2",
								"CHILD_MENU_TYPE" => "left",
								"USE_EXT" => "Y",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "36000000",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"MENU_CACHE_GET_VARS" => ""
								),
								false,
								array(
									"ACTIVE_COMPONENT" => "Y"
								)
							);?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<div class="content-holder clearfix">
<div class="container">
<?if($APPLICATION->GetCurDir() != "/"):?>
<div class="row">
	<div class="span12">
		<section class="title-section"><h1 class="title-header"><?$APPLICATION->ShowTitle(false);?></h1> </section>
	</div>
</div>
<?endif;?>
<div id="content" class="row">

