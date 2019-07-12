<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
IncludeTemplateLangFile(__FILE__);
?>

<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID;?>-<?=strtoupper(LANGUAGE_ID);?>">

<head>

<?$APPLICATION->ShowHead();?>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
<!-- =========================
      FAV & TOUCH ICONS  
============================== -->
    <!-- FAVICON FOR DESKTOPS -->
    <link rel="icon" href="<?=SITE_TEMPLATE_PATH?>/img/favicon/favicon.ico">
    
    <!-- SET OF FAVICONS FOR APPLE PRODUCTS -->
    <link rel="apple-touch-icon" href="<?=SITE_TEMPLATE_PATH?>/img/favicon/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?=SITE_TEMPLATE_PATH?>/img/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?=SITE_TEMPLATE_PATH?>/img/favicon/apple-touch-icon-114x114.png">
    <!-- JQUERY -->
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery_1.11.1.min.js"></script>
	
	
<!-- =========================
     FONTS  
============================== -->
    <!-- RALEWAY FONT FROM GOOGLE FONTS -->
	<?$APPLICATION->SetAdditionalCSS('http://fonts.googleapis.com/css?family=Raleway:300,400,600,700,900');?>
	
      
    <!-- ELEGANT ICON PACK FOR YOUR PAGE -->
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/icons/elegant.css');?>
	
	
<!-- =========================
     MAIN STYLESHEETS   
============================== -->
    <!-- BOOTSTRAP -->
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap/bootstrap.css');?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap/bootstrap.css.map');?>
      
    <!-- ALIVE! THEME -->
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/style-default.css');?>
      
    <!-- COLORBOX THEME -->
    <!-- CAROUSEL THEME -->
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/plugins/plugins.css');?>
	
	
	
<!-- =========================
     ANIMATIONS  
============================== -->
    <!-- ALIVE THEME ANIMATIONS -->
    <!-- ANIMATIONS BASED ON ANIMATE.CSS -->
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/animations/animations.css');?>
	
	
	
<!-- =========================
     INTERNET EXPLORER FIXES  
============================== -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	
		
	<title><?$APPLICATION->ShowTitle()?></title>
</head>


<body id="body" class="page-loading">
    
<!-- =========================
     PRELOADER
============================== -->
    <div id="preloader">
        <div id="loading"></div>
    </div>
	
	
<!-- =========================
     BITRIX PANEL
============================== -->	
<?$APPLICATION->ShowPanel();?>



<!-- =========================
     MAIN MENU
============================== -->
    <nav id="mainmenu" class="navbar navbar-fixed-top main-menu head-menu auto-height" role="navigation">
        <div class="container">
            
            <!-- LOGO CONTAINER -->
            <div class="logo-cont">
				<?$APPLICATION->IncludeComponent( 
					"bitrix:main.include", 
					"", 
					Array( 
					"AREA_FILE_SHOW" => "file", 
					"PATH" => SITE_TEMPLATE_PATH."/include/logo.php", 
					"EDIT_TEMPLATE" => "" 
					), 
					false 
				);?>
                                        
            </div>
			<div class="phone-cont">
                
                <span class="phone-primary"><?$APPLICATION->ShowProperty("telephone");?></span>
                
            </div>
             
            <!-- "BURGER MENU" FOR RESPONSIVE VIEW -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            
            <!-- MAIN MENU CONTAINER -->
            <?$APPLICATION->IncludeComponent(
				"bitrix:menu",
				"top_menu",
				Array(
					"ROOT_MENU_TYPE" => "top",
					"MENU_CACHE_TYPE" => "N",
					"MENU_CACHE_TIME" => "3600",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => "",
					"MAX_LEVEL" => "1",
					"CHILD_MENU_TYPE" => "left",
					"USE_EXT" => "N",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
				)
			);?>
            
        </div>
    </nav>