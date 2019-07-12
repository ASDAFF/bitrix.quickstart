<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<?
	IncludeTemplateLangFile(__FILE__);
	global $APPLICATION;
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.1.10.2.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.timer.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.placeholder.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/templates.js');
	$APPLICATION->ShowHead();
	?>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div id="mlfSite">
	<div class="mlfShap"><div class="wrap980">
		<div class="logo">
			<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/logo3.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
		</div>
		<div class="phoneTop">
			<div class="phone">
			<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/phone.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
			</div>
			<div class="zvonok">
			<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/zvonok.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
			</div>
		</div>
	</div></div>
	<div class="mlfContent"><div class="wrap980">
		<div class="text">
		<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/text3.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
		</div>
<div class="share">
				<?
			$APPLICATION->IncludeComponent("bitrix:main.include", "html_custom", Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include_areas/share3.php",
				"EDIT_TEMPLATE" => "",
				),
				false,
				array(
				"HIDE_ICONS" => "N"
				)
			);
			?>
				
			</div>
	</div></div>
	