<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">

<head>
<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="<?=SITE_DIR?>js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?=SITE_DIR?>js/fancybox/jquery.fancybox-1.3.1.css" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/store_styles.css" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/colors.css" />
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/common.css" />
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>

<body><?$APPLICATION->ShowPanel();?>
<script>
$(document).ready(function(){
	if($.browser.opera){$("body").css("background", "#9eeafc url(images/bg-top.jpg) left top repeat-x");}
});
</script>

<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "aero-basket", array(
	"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
	"PATH_TO_ORDER" => SITE_DIR."personal/order/make/"
	),
	false
);?>
<div class="wrap">
<div class="top2">&nbsp;</div>
<div class="top1">&nbsp;</div>
<div class="top3">&nbsp;</div>
<div class="top4">&nbsp;</div>
<div class="top5">&nbsp;</div>
<div id="conteiner">
<div id="subconteiner">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr><td>
    <table width="100%" cellpadding="0" cellspacing="8" border="0">
	<tr valign="top">
		<td width="350px" height="100px"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => SITE_DIR."includes/logo.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?></td>
		<td valign="top" style="color:#2c93e6;"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => SITE_DIR."includes/header-phone.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?></td>
		<td width="200px">
			<?$APPLICATION->IncludeComponent("bitrix:main.feedback", "popup1", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => "g-tech@bk.ru",
	"REQUIRED_FIELDS" => array(
		0 => "NAME",
		1 => "EMAIL",
		2 => "MESSAGE",
	),
	"EVENT_MESSAGE_ID" => array(
		0 => "5",
	)
	),
	false
);?><br/>
			<?$APPLICATION->IncludeComponent("bitrix:main.feedback", "auth_form", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => "info@example.com",
	"REQUIRED_FIELDS" => array(
		0 => "NAME",
		1 => "EMAIL",
		2 => "MESSAGE",
	),
	"EVENT_MESSAGE_ID" => array(
		0 => "5",
	)
	),
	false
);?>
		</td>
	</tr>
	<tr valign="bottom"><td colspan="3">
<table width="100%" style="float: left; margin-top: -40px; margin-bottom: 5px;" cellspacing="0" cellpadding="0">
<tr valign="bottom">
      <td valign="bottom">
        <div style="width: 100%; margin-top: 0px;">
        <?$APPLICATION->IncludeComponent("bitrix:menu", "grey_tabs1", array(
	"ROOT_MENU_TYPE" => "main",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
      </div>

		</td>
	</tr>
	<tr><td>
       <?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel3", Array(
	"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
	"MENU_CACHE_TYPE" => "N",	// Тип кеширования
	"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
	"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
	"MAX_LEVEL" => "3",	// Уровень вложенности меню
	"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
	"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	"DELAY" => "N",	// Откладывать выполнение шаблона меню
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);?>
</td>
</tr></table>
		</td></tr>
	</table>
  </td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr valign="top"><td align="left" width="695px">