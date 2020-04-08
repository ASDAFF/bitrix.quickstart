<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (defined('\Indi\Main\IS_AJAX') && \Indi\Main\IS_AJAX) {
	return;
}

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/bootstrap/js/bootstrap.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/jquery.maskedinput.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/jquery.mousewheel.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/jquery.placeholder.min.js");
//Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/sisyphus.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/cropper.min.js');

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/indi.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/template.js");



/* Add additional stylesheets */
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/bootstrap/css/bootstrap.min.css');
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slick.css');
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/bootstrap/css/bootstrap-theme.min.css');
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/cropper.min.css');

//Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . 'css/style.css');

/* Add stylesheets by requested path */
//\Indi\Main\Util::addCSSLinksByPath();
?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
	<head>
		<title><?$APPLICATION->ShowTitle()?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<?/*<meta name="viewport" content="width=1024, maximum-scale=1"/>*/?>
		<?/* <link rel="stylesheet" href="//fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext,cyrillic"/> */?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		
		<?$APPLICATION->ShowHead()?>
		
		<?/*<script rel="bx-no-check">indi.utils.apply(indi.app.locale, <?=\Indi\Main\Locale::getInstance()->toJSON()?>);</script>*/?>
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries. Selectivizr IE8 support of CSS3-selectors -->
		<!--[if lt IE 9]>
			<link data-skip-moving="true" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/ie.css"/>
			<script data-skip-moving="true" src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script data-skip-moving="true" src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
			<script data-skip-moving="true" src="<?=SITE_TEMPLATE_PATH?>/js/selectivizr-min.js"></script>
		<![endif]-->
	</head>
	<body role="document">
		<?$APPLICATION->ShowPanel()?>
		
		<div id="header" class="navbar navbar-default navbar-static-top js-custom-indicator" role="navigation">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<div class="navbar-toolbar">
							

							
							<?$APPLICATION->IncludeFile(
								'/includes/header-contacts.php',
								array(),
								array(
									'MODE' => 'php',
								)
							)?>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="navbar-toolbar">
							<?$APPLICATION->IncludeFile(
								'/includes/header-links.php',
								array(),
								array(
									'MODE' => 'php',
								)
							)?>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="navbar-toolbar clearfix">
							<?$APPLICATION->IncludeComponent(
								'bitrix:main.site.selector',
								'',
								array(
									'SITE_LIST' => array(
										0 => '*all*',
									),
									'CACHE_TYPE' => 'A',
									'CACHE_TIME' => 3600000,
								),
								false
							)?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
					</div>
					<div class="col-sm-4">
							<div class="navbar-toolbar">
								<?$APPLICATION->IncludeComponent(
									'bitrix:system.auth.form',
									'.default',
									array(
										'REGISTER_URL' => '/personal/',
										'FORGOT_PASSWORD_URL' => '',
										'PROFILE_URL' => '/personal/profile/',
										'SHOW_ERRORS' => 'Y',
									),
									false
								)?>
							</div>
					</div>
					<div class="col-sm-4">
							<div class="navbar-toolbar">

							</div>
					</div>
				</div>
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only"><?=GetMessage('TEMPLATE_TOGGLE_NAV')?></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand"<?=\Indi\Main\IS_INDEX ? '' : ' href="/"'?>>
						<img src="<?=\Indi\Main\TEMPLATE_IMG?>/logo.png" alt="<?=SITE_SERVER_NAME?>"/>
					</a>
				</div>
				<div class="navbar-collapse collapse">
					<?$APPLICATION->IncludeComponent(
						'bitrix:menu',
						'navbar',
						array(
							'ROOT_MENU_TYPE' => 'top',
							'MAX_LEVEL' => '2',
							'CHILD_MENU_TYPE' => 'left',
							'USE_EXT' => 'Y',
							'DELAY' => 'N',
							'ALLOW_MULTI_SELECT' => 'N',
							'MENU_CACHE_TYPE' => 'A',
							'MENU_CACHE_TIME' => 3600,
							'MENU_CACHE_USE_GROUPS' => 'N',
							'MENU_CACHE_GET_VARS' => array(),
						)
					)?>
					
					<?$APPLICATION->IncludeComponent(
						'bitrix:search.form',
						'navbar',
						array(
							'PAGE' => '/search/',
							'USE_SUGGEST' => 'N',
							'USE_PLACEHOLDER' => 'Y',
							'USE_REQUIRED' => 'Y',
						),
						false
					)?>
				</div>
			</div>
		</div>
		
		<div id="content" class="container">
			<div class="page-header">
				<h1><?=$APPLICATION->ShowTitle()?></h1>
			</div>