<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo LANG_CHARSET;?>" />
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle();?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings();?>
<?$APPLICATION->ShowHeadScripts();?>
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/ie_styles.css" type="text/css" />
<![endif]-->
<link rel="shortcut icon" href="<?=SITE_DIR?>favicon.ico" />
</head>
<body>
<div><?$APPLICATION->ShowPanel();?></div>
<div id = "wrapper">
<div id = "header">
<div id = "logo"><div class = "vert_align_block" style = "width: 106px; text-align: right;"><span><a href = "<?=SITE_DIR?>"><img src = "<?=SITE_TEMPLATE_PATH?>/images/logo.gif" alt = "" title = "" /></a></span></div></div>
<div id = "company_name" class = "color2"><div class = "vert_align_block"><span>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/company_name.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</span></div></div>
<div id = "slogan" class = "color3"><div class = "vert_align_block"><span>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/slogan.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</span></div></div>
<div id = "contacts">
<div id = "meta">
<a href = "/" title = "#MAIN_PAGE_TITLE#"><img src = "<?=SITE_TEMPLATE_PATH?>/images/home.gif" alt = "#MAIN_PAGE_TITLE#" title = "#MAIN_PAGE_TITLE#" height = "10px" width = "11px" /></a>
<span class = "sep">&nbsp;</span>
<a href = "mailto:<?echo COption::GetOptionString("main", "email_from", "admin@".$_SERVER["SERVER_NAME"], SITE_ID);?>" title = "#EMAIL_TITLE#"><img style = "position: relative; top: -1px;" src = "<?=SITE_TEMPLATE_PATH?>/images/mail.gif" alt = "#EMAIL_TITLE#" title = "#EMAIL_TITLE#" height = "8px" width = "12px" /></a>
<span class = "sep">&nbsp;</span>
<a href = "/map.php" title = "#SITEMAP_TITLE#"><img src = "<?=SITE_TEMPLATE_PATH?>/images/map.gif" alt = "#SITEMAP_TITLE#" title = "#SITEMAP_TITLE#" height = "10px" width = "10px" /></a>
</div>
<div id = "text_over_phone" class = "color2">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/text_over_phone.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</div>
<div id = "phone" class = "color2">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/phone.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</div>
<div id = "text_under_phone" class = "color2">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/text_under_phone.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</div>
</div>
</div>
<div id = "menu">
<?$APPLICATION->IncludeComponent("bitrix:menu", "top_menu", Array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => "",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N",
	),
	false
);?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "section_menu", Array(
	"ROOT_MENU_TYPE" => "left",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => "",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N",
	),
	false
);?>
</div>
<div id = "services">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/services.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</div>
<div id = "main" class = "border_color">
<div id = "container">
<div id = "left">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/news.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
<div id = "distance">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/distance.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</div>
</div>
<div id = "content">
<div class = "col_title">
<h1 class = "color1"><?$APPLICATION->ShowTitle(false);?></h1>
</div>
