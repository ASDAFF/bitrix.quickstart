<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE HTML>
<html>
  <head>
  <?$APPLICATION->ShowHead();?>
   <title><?$APPLICATION->ShowTitle()?></title>
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/jquery.fancybox.css" media="screen" />
   <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.fancybox.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.fancybox-media.js"></script>	
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.mixitup.min.js"></script>   
	
	<script type="text/javascript">
	/******** Fancy Light Box*********/
		$(document).ready(function() {
			/*
			 *  Simple image gallery. Uses default settings
			 */

			$('.fancybox').fancybox();
			
			/*
			 *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
			*/
			$('.fancybox-media')
				.attr('rel', 'media-gallery')
				.fancybox({
					openEffect : 'none',
					closeEffect : 'none',
					prevEffect : 'none',
					nextEffect : 'none',

					arrows : false,
					helpers : {
						media : {},
						buttons : {}
					}
				});
		});
	</script>

 
   <script>
   jQuery(document).ready(function() {

	$(window).scroll(function(){
		var znach_scroll = $(document).scrollTop();
		var box_header = $('.header_top');
	
		if (znach_scroll > 80) {
			box_header.addClass('header_top_fixed');
		}
		else {
			box_header.removeClass('header_top_fixed');
		}
	});
	});	
   </script>
	<script type="text/javascript">
	$(function () {
		
		var filterList = {
		
			init: function () {
			
				// MixItUp plugin
				// http://mixitup.io
				$('#portfoliolist').mixitup({
					targetSelector: '.portfolio',
					filterSelector: '.filter',
					effects: ['fade'],
					easing: 'snap',
					// call the hover effect
					onMixEnd: filterList.hoverEffect()
				});				
			
			},
			
			hoverEffect: function () {
			
			}

		};
		
		// Run the show!
		filterList.init();
			
	});	
 </script>
</head>
<body>
<?$APPLICATION->ShowPanel()?>
        <div class="header" id="home">  	   
	        <div class="header_top">
	        	<div class="wrap">	      	   
		 	     <div class="logo">
						<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/header_logo.php",
					"EDIT_TEMPLATE" => ""
					),false);?>
					</div>	
						<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"top_menu", 
	array(
		"ROOT_MENU_TYPE" => "top",
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
);?>						    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/responsive-nav.js"></script>

	    		 <div class="clear"></div>
	          </div>
	       </div>
	          <div class="header_desc">
	          	<div class="wrap">	   
	          	   <?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/header_title.php",
					"EDIT_TEMPLATE" => ""
					),false);?>	
	          	   <div class="button"><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/header_button.php",
					"EDIT_TEMPLATE" => ""
					),false);?>	</div>          	
	           </div>
	         </div>
	   </div>			      	
     <div class="main">
	 	<div class="content">	
	 	   <!---- Services ------>
	 		  <div class="services" id="services">
	 		 	<div class="wrap">
	 		 	    <h2><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/title_services.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h2>
	 		 	      <div class="line green"><span> </span></div>
	 		            <h4><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/description_services.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h4>
					
					
	 		 	             <div class="services_grids">
	 		 	             <?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/services_include.php",
					"EDIT_TEMPLATE" => ""
					),false);?>
					            </div>
					        </div>
	 		           </div>	
	 		            		           
	  <!---- Team ------>
	     <div class="team" id="team">
	 		 	<div class="wrap">
	 		 	    <h2><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/our_team_title.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h2>
	 		 	      <div class="line skyblue"><span> </span></div>
	 		            <h4><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/our_team_desc.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h4>
	 		 	             <div class="services_grids">
	 		 	             	 		 	             <?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/our_team_include.php",
					"EDIT_TEMPLATE" => ""
					),false);?>
							   
					            </div>
					        </div>
	 		           </div>
	            <!-----Portfolio-------->
	            <div class="portfolio-grid" id="works">
	 		 	  <div class="wrap">
	 		 	    <h2><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/gallery_title.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h2>
	 		 	      <div class="line yellow"><span> </span></div>
	 		            <h4><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/gallery_desc.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h4>
	 		          <div class="container">
						     