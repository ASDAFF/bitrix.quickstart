<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowHead()?>
<link rel="icon" href="#SITE_DIR#favicon.ico" type="image/x-icon" /> 
<link rel="shortcut icon" href="#SITE_DIR#favicon.ico" type="image/x-icon" /> 
</head>

<body>
<div><?$APPLICATION->ShowPanel();?></div>

<div id="container">

<br />
<div id="header">

<div class="name_block">

<div class="box-c"> <em class="ctl"><b>&bull;</b></em> <em class="ctr"><b>&bull;</b></em></div> 
<div class="box-inner">

<table cellspacing="0" cellpadding="0">
<tr>
	<td>
	<div><a href="#SITE_DIR#"><img src="<?=SITE_TEMPLATE_PATH?>/img/logo.gif" width="110" height="110" alt="" border="0" /></a></div>
	</td>
	
	<td>
	<div class="clinic_name">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/clinic_name.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	</div>
	</td>
</tr>
</table>

</div>
<div class="box-c"><em class="cbl"><b>&bull;</b></em><em class="cbr"><b>&bull;</b></em></div>

</div>

<div class="worktime">
	<div class="worktime-head"><?=GetMessage("HEADER_WORKTIME")?></div>
	<div class="worktime-text">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/worktime.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	</div>
</div>

<div class="contacts">
	<div><?=GetMessage("HEADER_PHONE")?></div>
	<div class="contacts-phone">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/phone.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	</div>
	<div class="contacts-address">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/address.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	</div>
</div>

<div class="clear"></div>
</div>


<div id="content">

<div id="left-col">

<?$APPLICATION->IncludeComponent("bitrix:menu", "main", array(
	"ROOT_MENU_TYPE" => "main",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "N",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "sub",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "empty", array(
	"ROOT_MENU_TYPE" => "sub",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "N",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>

	<br />
	<div class="banner">
	
	<div class="box-c"> <em class="ctl"><b>&bull;</b></em> <em class="ctr"><b>&bull;</b></em></div> 
	<div class="box-inner">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/banner.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	</div>
	<div class="box-c"><em class="cbl"><b>&bull;</b></em><em class="cbr"><b>&bull;</b></em></div>
	
	</div>

</div>

<div id="right-col">
<?if($APPLICATION->GetCurPage()!="#SITE_DIR#"):?>
<h1><?$APPLICATION->ShowTitle(false)?></h1>
<?endif;?>