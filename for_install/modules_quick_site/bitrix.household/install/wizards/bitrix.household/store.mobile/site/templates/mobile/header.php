<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html> 
<html>
<head>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/jquery.mobile-1.0a3.css" /> 
<script src="<?=SITE_TEMPLATE_PATH?>/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/jquery.mobile-1.0a3.js"></script> 
<script>
$(document).bind("mobileinit", function(){
      $.mobile.defaultTransition = 'slide';
});
</script>
<meta http-equiv="Content-Type" content="text/html; charset=<?=SITE_CHARSET?>" />
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0"/>
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<?$APPLICATION->ShowCSS()?>
<?$APPLICATION->ShowHead();?>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/colors.css" />
<title><?$APPLICATION->ShowTitle()?></title>
</head>

<body>
<?//$APPLICATION->ShowPanel()?>
<div data-role="page" data-theme="d" id="jqm-home">
<div data-role="header" data-theme="a">
	<h1><?$APPLICATION->ShowTitle()?></h1>
	<a href="#SITE_DIR#" data-ajax="false" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-right jqm-home"></a>
</div>
<div data-role="content">