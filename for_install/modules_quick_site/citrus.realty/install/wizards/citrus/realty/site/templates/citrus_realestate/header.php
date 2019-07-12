<?
IncludeTemplateLangFile(__FILE__);
require_once(__DIR__ . '/plugins.php');

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/colors.css', true);

function __CitrusTemplateShowHeadTitle()
{
	$arSite = $GLOBALS['APPLICATION']->GetSiteByDir();
	$siteName = strlen($arSite['SITE_NAME']) > 0 ? $arSite['SITE_NAME'] : $arSite['NAME'];
	$title = $GLOBALS['APPLICATION']->GetTitle('title');
	
	if (stripos($title, $siteName) === false)
		return strlen($title) > 0 ? "$title &mdash; $siteName" : $siteName;
	else
		return $title;
}

global $bCitrusTemplateIndex;
$bCitrusTemplateIndex = $APPLICATION->GetCurPage(false) == SITE_DIR;

CJsCore::Init(array('jquery', 'modernizr', 'fancybox', 'citrus_realty'));

?><!DOCTYPE html>
<html>
<head>
	<?$APPLICATION->ShowHead()?>
	<title><?$APPLICATION->AddBufferContent('__CitrusTemplateShowHeadTitle')?></title>
	<!--[if lte IE 7]><link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/ie7.css" type="text/css"  /><![endif]-->
	<link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon.ico">
	<link rel="apple-touch-icon" sizes="57x57" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?=SITE_TEMPLATE_PATH?>/favicons/apple-touch-icon-152x152.png">
	<link rel="icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon-196x196.png" sizes="196x196">
	<link rel="icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon-160x160.png" sizes="160x160">
	<link rel="icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/favicons/favicon-32x32.png" sizes="32x32">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="<?=SITE_TEMPLATE_PATH?>/favicons/mstile-144x144.png">
</head>

<body>
<?$APPLICATION->ShowPanel()?>
<div id="wrapper">
	<header id="header">
		<div class="header-top">
			<div class="block">
				<div class="header-top-menu">
					<ul>
						<li><a class="home" href="<?=SITE_DIR?>" title="<?=GetMessage("CITRUS_REALTY_HOME_PAGE")?>"></a></li>
						<li><a class="site_map" href="<?=SITE_DIR?>search/map.php" title="<?=GetMessage("CITRUS_REALTY_SITE_MAP")?>"></a></li>
						<li><a class="letter ajax-popup" href="<?=SITE_DIR?>ajax/request.php" title="<?=GetMessage("CITRUS_REALTY_FEEDBACK")?>"></a></li>
					</ul>
				</div>
				<div class="search">
					<form action="<?=SITE_DIR?>search/" method="get">
						<input class="field" type="text" placeholder="<?=GetMessage("SEARCH_TEXT")?>" value="" name="q"/>
						<input type="submit" value="<?=GetMessage("SEARCH_BUTTON")?>"/>
					</form>
				</div>
			</div>
		</div>
		<div class="header-menu">
			<div class="block">
				<div id="header-menu-container">
					<?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
							"ROOT_MENU_TYPE" => "top",
							"MENU_CACHE_TYPE" => "N",
							"MENU_CACHE_TIME" => "36000000",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => array(
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
				<div class="logo">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						Array(
							"AREA_FILE_SHOW" => "file", // page | sect - area to include
							"AREA_FILE_SUFFIX" => "inc", // suffix of file to seek
							"AREA_FILE_RECURSIVE" => "Y",
							"PATH" => SITE_DIR . "include/header_logo.php",
							"EDIT_TEMPLATE" => "page_inc.php",
							"EDIT_MODE" => "php",
						),
					false
					);?>
				</div>
				<?if (!$bCitrusTemplateIndex):?><div class="favorites"><?$APPLICATION->IncludeComponent(
						"citrus:realty.favourites",
						"block",
						array(),
					false
					);?></div><?endif;?>
			</div>
		</div>
	</header><!-- #header-->
	<div class="main">
		<div class="block">
			<div id="container"<?=$bCitrusTemplateIndex ? ' class="container-index"' : ''?>>
				<?$APPLICATION->IncludeComponent(
					"bitrix:breadcrumb",
					".default",
					Array(
						"START_FROM"    => "0",
						"PATH"          => "",
						"SITE_ID"       => "-",
					)
				);?>
				<div id="content" class="content">
					<h1><?=$APPLICATION->ShowTitle(false)?><?=$APPLICATION->ShowViewContent('addTitle')?></h1>
