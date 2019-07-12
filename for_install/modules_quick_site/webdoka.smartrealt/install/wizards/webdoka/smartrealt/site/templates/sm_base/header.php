<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo SITE_CHARSET ?>"/>  
    <title><?php echo $APPLICATION->ShowTitle();?></title>
    <?php echo $APPLICATION->ShowMeta("keywords") ?>
    <?php echo $APPLICATION->ShowMeta("description") ?>
    <link rel="icon" href="#WIZ_SITE_DIR#favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="#WIZ_SITE_DIR#favicon.ico" type="image/x-icon">
    <?php $APPLICATION->ShowCSS() ?>
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="ie7.css" />
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_TEMPLATE_PATH ?>/jquery.lightbox-0.5.css" media="screen" />
    <script type="text/javascript" src="<?php echo SITE_TEMPLATE_PATH ?>/js/jquery.js"></script> 
    <script type="text/javascript" src="<?php echo SITE_TEMPLATE_PATH ?>/js/jquery.lightbox-0.5.pack.js"></script> 
    <script type="text/javascript" src="<?php echo SITE_TEMPLATE_PATH ?>/js/script.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo SITE_TEMPLATE_PATH ?>/colors.css" media="screen" />
    <?php $APPLICATION->ShowHeadStrings() ?>
    <?php $APPLICATION->ShowHeadScripts() ?>
</head>
<body>
    <?php $APPLICATION->ShowPanel() ?>    
    <div class="center">
        <div class="header">
            <h1 class="logo"><a href="<?php echo SITE_DIR?>">
                <?php $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/logo.php')?>
            </a></h1>
            <?php
                $APPLICATION->IncludeComponent("bitrix:menu", "top", array(
                    "ROOT_MENU_TYPE" => "top",
                    "MENU_CACHE_TYPE" => "A",
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
                );
            ?>
            <div class="phone">
                <?php $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/phone.php')?>  
            </div>
            <div class="clear"></div>
        </div>
        <div class="top-rubricator" style="background: url('/bitrix/templates/sm_base/images/im1.png') bottom left no-repeat;">
            <!--<div class="im"><?php $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/site_image.php')?></div>-->
            <div class="rubricator theme-bgcolor">
                <?php
                    $APPLICATION->IncludeComponent("smartrealt:catalog.type.list", ".default", array(
	                    "CACHE_TYPE" => "A",
	                    "CACHE_TIME" => "3600",
	                    "CATALOG_LIST_URL" => ""
	                    ),
	                    false
                    );
                ?>
                <div class="clear"></div>
            </div>
        </div>

        <div class="left-column">
        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
            "AREA_FILE_SHOW" => "sect",
            "AREA_FILE_SUFFIX" => "left_inc",
            "AREA_FILE_RECURSIVE" => "Y",
            ),
            false
        );?>

        </div>
        <div class="main-column">
