<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

include $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include_areas/catalog_iblock_id.php';

/**
 * @global $APPLICATION;
 */
global $APPLICATION;

// additional scripts
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-1.8.3.min.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery.cusel.min.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/script.js');
?>
<!doctype html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php $APPLICATION->ShowHead(); ?>
	<title><?php $APPLICATION->ShowTitle() ?></title>
	<!--[if lt IE 9]>
		<script src="<?= SITE_TEMPLATE_PATH ?>/js/html5shiv.js"></script>
		<script src="<?= SITE_TEMPLATE_PATH ?>/js/respond.js"></script>
	<![endif]-->
</head>
<body>
	<div><?php $APPLICATION->ShowPanel(); ?></div>
	<div class="site">
		<header id="header" class="header">
			<div class="brand">
				<div id="mob_header">
					<a href="<?= SITE_DIR ?>" class="home-link"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include_areas/logo.php"
						), false);?></a>
				</div>
				<p id="slogan"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include_areas/slogan.php"
						), false);?></p>
			</div>
			<div class="hright">
				<div class="schedule">
					<p class="fst"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include_areas/header_contact.php"
						), false);?></p>
					<p class="snd"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include_areas/phone.php"
						), false);?></p>
					<p class="trd"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include_areas/schedule.php"
						), false);?></p>
				</div>
				<div class="acts">
					<?$APPLICATION->IncludeComponent(
	"prmedia:minimarket.profile.links", 
	".default", 
	array(
		"PATH_TO_PROFILE" => SITE_DIR."profile/",
		"NAME_TEMPLATE" => "#LAST_NAME# #NAME#",
		"NAME_TEMPLATE_LOGIN" => "Y",
		"PATH_TO_AUTH" => SITE_DIR."auth/",
		"PATH_TO_REGISTER" => SITE_DIR."registration/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7200"
	),
	false
);?><?$APPLICATION->IncludeComponent(
						"bitrix:sale.basket.basket.line", "header", Array(
						"PATH_TO_BASKET" => SITE_DIR."basket/",
						"PATH_TO_PERSONAL" => SITE_DIR."profile/",
						"SHOW_PERSONAL_LINK" => "N"
						)
					);?>
				</div>
			</div>
		</header>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "header", array(
		"ROOT_MENU_TYPE" => "top",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "Y",
		"ALLOW_MULTI_SELECT" => "N",
		"SHOW_CATALOG" => "Y",
		"CATALOG_MENU_ITEM" => SITE_DIR."catalog/",
		"SHOW_SEARCH" => "Y"
		),
		false
	);?>
	<table id="content">
			<tbody>
				<tr>
					<?php
					$leftColumn = $APPLICATION->GetFileRecursive('.include.left_column.php');
					?>
					<?php if ($leftColumn !== false): ?>
						<td class="left-column"><?php include $_SERVER['DOCUMENT_ROOT'] . $leftColumn; ?></td>
					<?php endif; ?>
					<td class="right-column">
