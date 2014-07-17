<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html>
<html lang="<?=LANG;?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?$APPLICATION->ShowTitle();?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />


	<!--
		# Require jQuery from CDN
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	-->

	<!-- 
		# Example of including javascript libraries or plugins 
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH;?>/js/plugin-name/plugin.js">
		<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH;?>/js/plugin-name/plugin.css" media="screen"/>
	-->

	<?$APPLICATION->ShowHead();?>
</head>
<body>
<?$APPLICATION->ShowPanel();?>