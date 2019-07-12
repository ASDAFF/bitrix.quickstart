<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html>
<html>
<head>
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title> 
<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/bootstrap.css"); ?>
<? /*<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/bootstrap.css" type="text/css">*/?>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/font-awesome.css" type="text/css">
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/responsive.css" type="text/css">

<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/libs/jquery-1.10.1.min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/libs/bootstrap.min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/cross/modernizr.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.bxslider.min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.customSelect.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.validate.min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.colorbox-min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.waypoints.min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.parallax-1.1.3.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.jcarousel.min.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/custom.js'); ?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/loader.js'); ?>

<? /*<script src="<?=SITE_TEMPLATE_PATH?>/js/libs/jquery-1.10.1.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/libs/bootstrap.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/cross/modernizr.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.bxslider.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.customSelect.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.validate.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.colorbox-min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.waypoints.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.parallax-1.1.3.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/custom.js"></script>
<!-- file loader -->
<script src="<?=SITE_TEMPLATE_PATH?>/js/loader.js"></script>*/ ?>

</head>

<body>
<?$APPLICATION->ShowPanel();?>
<!--==========Header===========-->
<div id="preloader" style="display: none;">
	<div id="status" style="display: none;">
		<div class="spinner">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>
</div>

<div class="main-holder">
<header class="main-wrapper header">
	<div class="container apex">
		<div class="row">

			<nav class="navbar header-navbar" role="navigation">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<div class="logo navbar-brand">
						<a href="<?=SITE_DIR?>" title="LOGO">L.O.G.O.</a>
					</div>
		      <button class="toggle-slide-left visible-xs collapsed navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"><i class="fa fa-bars"></i></button>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<div class="navbar-right">
							<nav class="nav-menu navbar-left main-nav trig-mob slide-menu-left">
                            	
                                <?$APPLICATION->IncludeComponent("bitrix:menu","topmenu",Array(
                                        "ROOT_MENU_TYPE" => "top", 
                                        "MAX_LEVEL" => "1", 
                                        //"CHILD_MENU_TYPE" => "top", 
                                        "USE_EXT" => "Y",
                                        "DELAY" => "N",
                                        "ALLOW_MULTI_SELECT" => "Y",
                                        "MENU_CACHE_TYPE" => "N", 
                                        "MENU_CACHE_TIME" => "3600", 
                                        "MENU_CACHE_USE_GROUPS" => "Y", 
                                        "MENU_CACHE_GET_VARS" => "" 
                                    )
                                );?>
                                
								<? /*<ul class="list-unstyled">
									<li class="firstItem">
										<a href="#" data-scroll="information">
											<div class="inside">
												<div class="backside"> Информация </div>
												<div class="frontside"> Информация </div>
											</div>
										</a>
									</li>
									<li>
										<a href="#" data-scroll="features">
											<div class="inside">
												<div class="backside"> Наши преимущества </div>
												<div class="frontside"> Наши преимущества </div>
											</div>
										</a>
									</li>
									<li>
										<a href="#" data-scroll="testimonials">
										<div class="inside">
											<div class="backside"> Отзывы </div>
											<div class="frontside"> Отзывы </div>
										</div>
										</a>
									</li>
									<li>
										<a href="#" data-scroll="gallery">
											<div class="inside">
												<div class="backside"> Галерея </div>
												<div class="frontside"> Галерея </div>
											</div>
										</a>
									</li>
									<li class="lastItem">
										<a class="openform" role="button" href="#myModal">
											<div class="inside">
												<div class="backside"> Контакты </div>
												<div class="frontside"> Контакты </div>
											</div>
										</a>
									</li>
								</ul>*/?>
							</nav>
							<div class="wr-soc">
                            	<div class="header-social">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
                            "AREA_FILE_SHOW" => "file", 
                            "AREA_FILE_RECURSIVE" => "Y",  
                            "PATH" => SITE_DIR."include/social.php"
                            )
                            );?>
                            	</div>
							</div>
						</div>
		    </div><!-- /.navbar-collapse -->
			</nav>
		</div>
	</div>
</header>
	
<!--==========Content==========-->
<div class="main-wrapper content">




    