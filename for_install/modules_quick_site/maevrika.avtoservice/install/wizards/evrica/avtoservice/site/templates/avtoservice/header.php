<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
IncludeTemplateLangFile(__FILE__);
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<?$APPLICATION->ShowHead();?>
<link href="<?=SITE_TEMPLATE_PATH?>/common.css" type="text/css" rel="stylesheet" />
<link href="<?=SITE_TEMPLATE_PATH?>/colors.css" type="text/css" rel="stylesheet" />

	<!--[if lte IE 6]>
	<style type="text/css">
		
		#banner-overlay { 
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>images/overlay.png', sizingMethod = 'crop'); 
		}
		
		div.product-overlay {
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=SITE_TEMPLATE_PATH?>images/product-overlay.png', sizingMethod = 'crop');
		}
		
	</style>
	<![endif]-->

	<title><?$APPLICATION->ShowTitle()?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>

<div id="page-wrapper">
  <table border="0" width="878" align="center" height="10">
    <tbody>
      <tr><td>&nbsp;</td></tr>
    </tbody>
  </table>

  <table border="0" width="878" align="center" height="10">
    <tbody>
      <tr><td>&nbsp;</td></tr>
    </tbody>
  </table>

  <table width="847">
    <tbody>
      <tr><td>
          <div align="right"><?$APPLICATION->IncludeFile(
		SITE_DIR."include/ulica.php",
		Array(),
		Array("MODE"=>"html")
	);?>/ <a href="#SITE_DIR#contacts/" ><?=$MESS["SHEMA_TEMPLATE_HEADER_NAME"]?></a>/ <?=$MESS["TEL_TEMPLATE_HEADER_NAME"]?><?$APPLICATION->IncludeFile(
		SITE_DIR."include/telefon.php",
		Array(),
		Array("MODE"=>"html")
	);?> / <?$APPLICATION->IncludeFile(
		SITE_DIR."include/regim.php",
		Array(),
		Array("MODE"=>"html")
	);?></div>
        </td><td>
          <div align="right"><img src="#SITE_DIR#images/logo_150.jpg" title="<?=$MESS["TITLE_TEMPLATE_HEADER_IMG"]?>"  /></div>
        </td></tr>
    </tbody>
  </table>

  <table width="100%" height="50">
    <tbody>
      <tr><td valign="middle" align="center">
          <div id="top-menu">
            <div id="top-menu-inner"><?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"horizontal_multilevel",
	Array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => ""
	),
false,
Array(
	'ACTIVE_COMPONENT' => 'Y'
)
);?> </div>
          </div>
        </td></tr>
    </tbody>
  </table>

  <table border="0" width="878" align="center" height="10">
    <tbody>
      <tr><td></td></tr>
    </tbody>
  </table>
<?$APPLICATION->IncludeFile(
		SITE_DIR."include/menusha.php",
		Array(),
		Array("MODE"=>"html")
	);?>
  

  <table border="0" width="878" align="center" height="10">
    <tbody>
      <tr><td><hr /></td></tr>
    </tbody>
  </table>

  <div id="workarea">
    <h1 id="pagetitle"><?$APPLICATION->ShowTitle(false);?></h1>
  