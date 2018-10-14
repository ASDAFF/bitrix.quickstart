<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <title><?php if (isset($arParams['TITLE'])) echo($arParams['TITLE']); ?></title>

    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,700,600,300,800&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Philosopher:400,700,400italic,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" type="text/css" href="<?php echo($templateFolder); ?>/css/grid.css">
    <link rel="stylesheet" type="text/css" href="<?php echo($templateFolder); ?>/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo($templateFolder); ?>/css/common.css">
    <?php
    	if (isset($arParams['THEME'])) { ?>
    		<link rel="stylesheet" type="text/css" href="<?php echo($templateFolder); ?>/themes/<?php echo($arParams['THEME']); ?>/style.css">
    <?php } ?>
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="/bitrix/js/lssoft.comingsoon/jquery.countdown.js"></script>
</head>

<body>

    <div id="main">
        <div class="social-buttons">
            <ul>
            	<?php if (isset($arParams['SHARE']['FB']) and $arParams['SHARE']['FB']) { ?>
                	<li class="fb"><a href="<?php echo($arParams['SHARE']['FB']); ?>" title="<?php echo GetMessage("LS_CS_SHARE_FB"); ?>"><?php echo GetMessage("LS_CS_SHARE_FB"); ?></a></li>
                <?php } ?>
                <?php if (isset($arParams['SHARE']['TW']) and $arParams['SHARE']['TW']) { ?>
                	<li class="tw"><a href="<?php echo($arParams['SHARE']['TW']); ?>" title="<?php echo GetMessage("LS_CS_SHARE_TW"); ?>"><?php echo GetMessage("LS_CS_SHARE_TW"); ?></a></li>
                <?php } ?>
                <?php if (isset($arParams['SHARE']['VK']) and $arParams['SHARE']['VK']) { ?>
                	<li class="vk"><a href="<?php echo($arParams['SHARE']['VK']); ?>" title="<?php echo GetMessage("LS_CS_SHARE_VK"); ?>"><?php echo GetMessage("LS_CS_SHARE_VK"); ?></a></li>
                <?php } ?>
                <?php if (isset($arParams['SHARE']['ODN']) and $arParams['SHARE']['ODN']) { ?>
                	<li class="odn"><a href="<?php echo($arParams['SHARE']['ODN']); ?>" title="<?php echo GetMessage("LS_CS_SHARE_ODN"); ?>"><?php echo GetMessage("LS_CS_SHARE_ODN"); ?></a></li>
                <?php } ?>
                <?php if (isset($arParams['SHARE']['GP']) and $arParams['SHARE']['GP']) { ?>
                	<li class="gp"><a href="<?php echo($arParams['SHARE']['GP']); ?>" title="<?php echo GetMessage("LS_CS_SHARE_GP"); ?>"><?php echo GetMessage("LS_CS_SHARE_GP"); ?></a></li>
                <?php } ?>
                
                <!--
                    <li class="in"><a href="#" title="LinkedIn">LinkedIn</a></li>
                    <li class="lj"><a href="#" title="LiveJournal">LiveJournal</a></li>
                    <li class="ml"><a href="#" title="Мой Круг">"Мой Круг</a></li>
                    <li class="yt"><a href="#" title="Youtube">Youtube</a></li>
                    <li class="pt"><a href="#" title="Pinterest">Pinterest</a></li>
                    <li class="su"><a href="#" title="Stumbleupon">Stumbleupon</a></li>
                    <li class="ya"><a href="#" title="Яндекс">Яндекс</a></li>
                    <li class="hb"><a href="#" title="ХабраХабр">ХабраХабр</a></li>
                    <li class="vm"><a href="#" title="Vimeo">Vimeo</a></li>
                    <li class="tb"><a href="#" title="Tumblr">Tumblr</a></li>
                -->
            </ul>
        </div>

        <div class="inner">
            <div id="header">
                <a href="<?php echo $arParams['_SITE_DIR']; ?>" class="logo"><img src="<?php if (isset($arParams['LOGO'])) echo($arParams['LOGO']); ?>" alt="logo"></a>
            </div>

            <div id="wrapper">
                <div class="content">