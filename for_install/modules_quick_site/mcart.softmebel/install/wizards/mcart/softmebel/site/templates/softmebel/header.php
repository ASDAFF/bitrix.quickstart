<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<html>
<head>


	<?$APPLICATION->ShowHead()?>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/lib/lightbox/js/prototype.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/lib/lightbox/js/scriptaculous.js?load=effects,builder"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/lib/lightbox/js/lightbox.js"></script>
	<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/lib/lightbox/css/lightbox.css" type="text/css" media="screen" />	
	<title><?$APPLICATION->ShowTitle()?></title>
	
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.4.4.min.js"></script> 
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jq.js"></script>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-275816-16']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>
<body><?$APPLICATION->ShowPanel();?>

<table cellspacing=0 cellpadding=0 width=100% height=100%>
<tr valign=top>
<td width=267 align=center>                                                
	<a href="<?=SITE_DIR?>"><img src="http://swmebel.ru/i/logo_test4.jpg" alt="Мягкая мебель - логотип" title="Мягкая мебель" border=0 style="margin-top: 5px;"></a>
	<br><br>
	<span style='font-size: 13px; color: #336699; font-weight: bold;'>
	<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "left1_inc",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => "sect_inc.php"
							),
							false
						);?></span>
	<br><br>
	<img src="http://swmebel.ru/i/bg_test2.jpg" alt="Мягкая мебель" title="Мягкая мебель">
	<br><br><br><br><br><br>
</td>
<td style="padding-top: 0px;"><?php /*
	<table cellspacing="0" cellpadding="0" width="100%" height="50" border="0">
		<tr>
			<td align="right" style="padding-right: 34px;"><a href="/discount_coupon/"><img src="<?=SITE_TEMPLATE_PATH?>/images/kupon.gif" border="0" title="купон на 5% скидку"></a>
				</td>
			<td width="220"  align="left">
				<font size="1" face="Verdana"><b>Мягкая мебель в Санкт-Петербурге</b></font><br><b><font face="Verdana" color="#E96F16">+7 (812)</font></b> <b><font size="5" face="Verdana" color="#E96F16">0000-000</font></b>
			</td>
		</tr>
	</table>*/?>
	<table  width="100%" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td align="right">
					<a href="<?=SITE_DIR?>useful/how_to_order.php"><img src="<?=SITE_TEMPLATE_PATH?>/images/rasr.gif" alt="" /></a>
				</td>
				<td align="right" width="224px;">
					<font face="Verdana" size="1"><b>Мягкая мебель в Санкт-Петербурге</b></font><br>
					<b><font face="Verdana" color="#E96F16">+7 (812)</font></b> 
					<b><font face="Verdana" size="5" color="#E96F16">0000-000</font></b>
				</td>
				<td width="40px;"> </td>
				<td width="70px;"><a href="<?=SITE_DIR?>discount_coupon/"><img src="<?=SITE_TEMPLATE_PATH?>/images/kupon.gif" 
					title="купон на 5% скидку" border="0"></a></td>
			</tr>
		</tbody>
	</table>
	<?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "N",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "top2",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
	<?php /*?>
	<table cellspacing="0" cellpadding="0" width="100%" height="82" border="0"
	style="background: url(<?=SITE_TEMPLATE_PATH?>/images/menubg2.gif) repeat-x top; margin-top: 0px;">
		<tr>
			<td>
				<table cellspacing=0 cellpadding=0 width=100% height=82 
				style="background: url(<?=SITE_TEMPLATE_PATH?>/images/menuleft2.gif) no-repeat left top;">
					<tr>
						<td>	
							<table cellspacing="0" cellpadding="0" width="100%" height="82" border="0"
							style="background: url(<?=SITE_TEMPLATE_PATH?>/images/menuright2.gif) no-repeat right top;">
								<tr>
									<td valign=top style="padding-left: 12px; padding-top: 7px;">											
										<table border=0 id="#ololo"><tr><td valign="top" id="#ololo">                                                                                                       



                                                        <p>       <a href="/" class=c1><img src="<?=SITE_TEMPLATE_PATH?>/images/p.gif" border="0"> </a><b><a href="/" class=c1>Главная</a></b><br>
</p>
                                                    </td><td valign="top" id="#ololo">&nbsp;<img src="<?=SITE_TEMPLATE_PATH?>/images/r.gif" border="0"></td><td valign="top" id="#ololo">                                                                                                        
                                                        <a href="/catalog/" class=c1><img src="<?=SITE_TEMPLATE_PATH?>/images/p.gif" border="0"> <b><font color="yellow">Каталог мягкой мебели</font></b></a>                                                    
</td>

<td valign="top">&nbsp;<img src="<?=SITE_TEMPLATE_PATH?>/images/r.gif" border="0"></td><td valign="top"><a href="/contacts/#1" class=c1><img src="<?=SITE_TEMPLATE_PATH?>/images/p.gif" border="0">  </a><b><a href="/contacts/1.php" class=c1>Где купить нашу мебель</a></b>

<li class=li><a href="/contacts/" class=c1>Как купить?</a></li><li><a href="/contacts/useful/" class=c1>Покупателям</a></li>                                                
                                                  
</td>

<td valign="top">&nbsp;<img src="<?=SITE_TEMPLATE_PATH?>/images/r.gif" border="0"></td>
<td valign="top">                                                    
<a href="#" class=c1><img src="<?=SITE_TEMPLATE_PATH?>/images/p.gif" border="0"> </a><b><a href="/delivery/" class=c1>Доставка</a></b>
                                                    
</td><td valign="top">&nbsp;<img src="<?=SITE_TEMPLATE_PATH?>/images/r.gif" border="0"></td><td valign="top">                                                    
<a href="/company/" class=c1><img src="<?=SITE_TEMPLATE_PATH?>/images/p.gif" border="0"> <b>О компании</b></a><li><a href="/company/news" class=c1>Новости</a></li><li><a href="/company/discount.php" class=c1>Акции</a></li><li><a href="/company/design.php" class=c1>Дизайнерам</a></li>
                                                    
</td><td valign="top" id="#table">&nbsp;<img src="<?=SITE_TEMPLATE_PATH?>/images/r.gif" border="0"></td><td valign="top">                                                   
<a href="/catalog/stock.php" class=c1><img src="<?=SITE_TEMPLATE_PATH?>/images/p.gif" border="0"> <b>Продажа со склада</b></a><li><a href="/price/" class=c1>Прайс</a></li>
                                                    
</td></tr></table>
									</td>									
								</tr>
							</table>
						</td>	
					</tr>
				</table>
			</td>	
		</tr>
	</table>
	
	<?
	*/
		/*
	$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "top1_inc",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => "sect_inc.php"
							),
							false
	);
		*/
	?>	
	
	<br clear="all">
	
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
		<tr valign="top">
			<td style="padding: 0px 20px 20px 20px;">									
					<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "main", array(
	"START_FROM" => "0",
	"PATH" => "",
	"SITE_ID" => "s1"
	),
	false
);?>
				<br />

				<div style="font-size: 15pt; padding-top: 8px; padding-bottom: 2px; color:#5982AC; ">
					<img src="<?=SITE_TEMPLATE_PATH?>/images/bullet.gif" style="margin-bottom: 3px; vertical-align: middle;"> 
					<?$APPLICATION->ShowTitle(false)?></div><br>
					
					
				<table width="100%">
					<tr>
						<td style="padding-left: 16px; padding-right: 4px;  vertical-align: top;">