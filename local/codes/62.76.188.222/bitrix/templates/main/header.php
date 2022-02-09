<!DOCTYPE html>
<!--[if IE 7 ]><html lang="ru" class="no-js ie7"><![endif]-->
<!--[if IE 8 ]><html lang="ru" class="no-js ie8"><![endif]-->
<!--[if IE 9 ]><html lang="ru" class="no-js ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="ru" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="description" content="">
	<meta name="author" content="">
        <title><?$APPLICATION->ShowTitle();?></title>
	<!-- google web fonts -->
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,700&subset=latin,cyrillic" type="text/css">
	<link rel="stylesheet" href="/css/normalize.min.css">
        <link rel="stylesheet" href="/css/style.css">
	<!--[if lt IE 9]><link rel="stylesheet" href="/css/ie.css"><![endif]-->	
	<script type="text/javascript" src="/js/libs/modernizr-2.6.2.min.js"></script>
	<!-- PLUGINS: jQuery v1.8.2 -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.8.2.min.js"><\/script>')</script>
	<script src="/js/js.js"></script>
	<!-- chosen 0.9.11 -->
	<script src="/js/libs/chosen/chosen.jquery.min.js"></script>
	<link href="/js/libs/chosen/chosen.css" rel="stylesheet">
	<!-- jQuery UI 1.9.2 -->
	<script src="/js/libs/jquery-ui/jquery-ui-1.9.2.custom.min.js"></script>
	<link href="/js/libs/jquery-ui/jquery-ui-1.9.2.custom.css" rel="stylesheet">
	
	<!-- fancyBox 2.1.3 -->
	<script src="/js/libs/fancybox/jquery.fancybox.pack.js"></script>
	<link href="/js/libs/fancybox/jquery.fancybox.css" rel="stylesheet">
	
	<!--[if lt IE 10]>
	<script src="/js/libs/jquery.placeholder.min.js"></script>
	<script>
	$(document).ready(function() {
		$("input[placeholder], textarea[placeholder]").placeholder();
	});
	</script>
	<![endif]-->
        
    <?$APPLICATION->ShowHead();?>        
</head>
<body>
        <? $APPLICATION->ShowPanel(); ?>
<div class="b-page">
	<div class="b-cover">
		<header class="b-header">
			<div class="b-wrapper">
				<div class="b-topline clearfix">
				 
				  <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:search.form", "", Array(
                            "USE_SUGGEST" => "N",
                            "PAGE" => "#SITE_DIR#search/index.php" )
                        );
                        ?> 
					<div class="b-auth">
						<?
                        	global $USER;
                        	if ($USER->IsAuthorized()):?>
	                            <a href="/personal/" class="b-topline__link m-reg" id="b-auth__login"><span><?=$USER->GetLogin()?></span></a>
                             
                                  <a href="?logout=yes" class="b-topline__link m-login" id="b-auth__login"><span>Выйти</span></a>      
                                  <?else:?>
	                            <a href="#b-auth-window" class="b-topline__link m-login" id="b-auth__login"><span>Войти</span></a>
	                            <a href="/register/" class="b-topline__link m-reg"><span>Регистрация</span></a>
                            <?endif;?> </div>
				</div>
		 
				<nav class="b-nav">
					<div class="clearfix">
						<a href="/" class="b-logo"></a>
				        <?
                                        $APPLICATION->IncludeComponent("devteam:main_nav", "", Array());
                                        ?>
					</div>
					<div class="clearfix">
					     <?
                                $APPLICATION->IncludeComponent(
                                    "bitrix:breadcrumb", "", Array(
                                    "START_FROM" => "0",
                                    "PATH" => "",
                                    "SITE_ID" => "s1"
                                        )
                                );
                                ?>
						<div class="b-minicart">
							   <?
                                                            require_once $_SERVER["DOCUMENT_ROOT"] . '/inc/basket_line.php';
                                                            ?>
						</div>
					</div>
				</nav><!--/.b-nav-->
			</div><!--/.b-wrapper-->
		</header><!--/.b-header-->
		<div class="b-wrapper">
			<div class="b-container clearfix"><? 
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        return;
                   
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        ?><!DOCTYPE html>
<!--[if IE 7 ]><html lang="ru" class="no-js ie7"><![endif]-->
<!--[if IE 8 ]><html lang="ru" class="no-js ie8"><![endif]-->
<!--[if IE 9 ]><html lang="ru" class="no-js ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="ru" class="no-js"> <!--<![endif]-->
<head> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?$APPLICATION->ShowTitle();?></title>

    <!-- google web fonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,700&subset=latin,cyrillic" type="text/css">

    <link rel="stylesheet" href="/css/normalize.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <!--[if lt IE 9]><link rel="stylesheet" href="css/ie.css"><![endif]-->	

    <script type="text/javascript" src="/js/libs/modernizr-2.6.2.min.js"></script>

    <!-- PLUGINS: jQuery v1.8.2 -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>
    <script src="/js/js.js"></script>

    <!-- chosen 0.9.11 -->
    <script src="/js/libs/chosen/chosen.jquery.js"></script>
    <link href="/js/libs/chosen/chosen.css" rel="stylesheet">

    <!-- jQuery UI 1.9.2 -->
    <script src="/js/libs/jquery-ui/jquery-ui-1.9.2.custom.min.js"></script>
    <link href="/js/libs/jquery-ui/jquery-ui-1.9.2.custom.css" rel="stylesheet">

    <!-- fancyBox 2.1.3 -->
    <script src="/js/libs/fancybox/jquery.fancybox.pack.js"></script>
    <link href="/js/libs/fancybox/jquery.fancybox.css" rel="stylesheet">

    <!--[if lt IE 10]>
    <script src="/js/libs/jquery.placeholder.min.js"></script>
    <script>
    $(document).ready(function() {
            $("input[placeholder], textarea[placeholder]").placeholder();
    });
    </script>
    <![endif]-->	

    <?$APPLICATION->ShowHead();?>
</head>
<body>
    <? $APPLICATION->ShowPanel(); ?>
    <div class="b-page">
        <div class="b-cover">
            <header class="b-header">
                <div class="b-wrapper">
                    <div class="b-topline clearfix">
                        <?
                        $APPLICATION->IncludeComponent(
                                "bitrix:menu", "top", Array(), false
                        );
                        ?> 
                        <div class="b-auth">
                        	<?
                        	global $USER;
                        	if ($USER->IsAuthorized()):?>
	                            <a href="/personal/" class="b-topline__link m-reg" id="b-auth__login"><span><?=$USER->GetLogin()?></span></a>
                            
                                  <a href="?logout=yes" class="b-topline__link m-login" id="b-auth__login"><span>Выйти</span></a>      
                                  <?else:?>
	                            <a href="#b-auth-window" class="b-topline__link m-login" id="b-auth__login"><span>Войти</span></a>
	                            <a href="/register/" class="b-topline__link m-reg"><span>Регистрация</span></a>
                            <?endif;?> 
                        </div>
                    </div>
                    <div class="b-topline clearfix">
                        <a href="/" class="b-logo"></a>
                        <div class="b-phone"></div>
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:search.form", "", Array(
                            "USE_SUGGEST" => "N",
                            "PAGE" => "#SITE_DIR#search/index.php" )
                        );
                        ?> 
                    </div>
                    <nav class="b-nav">
                        <?
                        $APPLICATION->IncludeComponent(
                          "devteam:main_nav", "", Array());
                        ?>
                            <div class="clearfix">
                                <?
                                $APPLICATION->IncludeComponent(
                                    "bitrix:breadcrumb", "", Array(
                                    "START_FROM" => "0",
                                    "PATH" => "",
                                    "SITE_ID" => "s1"
                                        )
                                );
                                ?>
                            <div class="b-minicart">
                                <?
                                require_once $_SERVER["DOCUMENT_ROOT"] . '/inc/basket_line.php';
                                ?>
                            </div>
                        </div>
                    </nav><!--/.b-nav-->
                </div><!--/.b-wrapper-->
            </header><!--/.b-header-->
            <div class="b-wrapper">
                 <div class="b-container clearfix"> 