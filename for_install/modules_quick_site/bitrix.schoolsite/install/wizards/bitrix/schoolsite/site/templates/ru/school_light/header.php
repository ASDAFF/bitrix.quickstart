<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!--[if lt IE 8]>
			<link rel="stylesheet" media="screen" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/ie67.css" />
		<![endif]-->
		<!--[if lt IE 9]>
			<link rel="stylesheet" media="screen" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/ie68.css" />
		<![endif]-->
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.6.min.js" type="text/javascript"></script>
        <?$APPLICATION->ShowHead();?>
        <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/colors.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/js/plugins/fancybox/jquery.fancybox-1.3.4.css" />
        <title><?$APPLICATION->ShowTitle()?></title>
        <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/skins/tango/skin.css" />
        <script src="<?=SITE_TEMPLATE_PATH?>/js/plugins/logic.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/plugins/fancybox/jquery.easing-1.3.pack.js"></script>
        <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/plugins/fancybox/jquery.fancybox-1.3.4.js"></script>
        <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/plugins/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/plugins/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	</head>
	<body>
  <div id="panel"><?$APPLICATION->ShowPanel();?></div>
        <div class="wrapper">
            <div class="container">
                <div class="top">
                    <?$APPLICATION->IncludeFile(
                        $APPLICATION->GetTemplatePath("include_areas/buttons.php"),
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                    <div class="log">
                        <?$APPLICATION->IncludeComponent("bitrix:main.user.link", "profile", array(
                            "ID" => $USER->GetID(),
                            "NAME_TEMPLATE" => "#NOBR##LAST_NAME# #NAME##/NOBR# #SECOND_NAME#",
                            "SHOW_LOGIN" => "Y",
                            "USE_THUMBNAIL_LIST" => "Y",
                            "SHOW_FIELDS" => array(
                                0 => "NAME",
                                1 => "SECOND_NAME",
                                2 => "LAST_NAME",
                            ),
                            "USER_PROPERTY" => array(
                                0 => "UF_DEPARTMENT",
                                1 => "UF_CLASS",
                            ),
                            "PROFILE_URL" => "",
                            "THUMBNAIL_LIST_SIZE" => "28",
                            "DATE_TIME_FORMAT" => "d.m.Y H:i:s",
                            "PATH_TO_SONET_USER_PROFILE" => "/company/personal/user/#user_id#/",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "7200",
                            "SHOW_YEAR" => "N"
                            ),
                            false
                        );?>
                        <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "auth", array(
                          "REGISTER_URL" => "/auth/",
                          "PROFILE_URL" => "/personal/profile/",
                          "SHOW_ERRORS" => "Y"
                        ),
                        false
                        );?>
                    </div>
        <?  $APPLICATION->IncludeFile(
            SITE_DIR."include_areas/schooltop_links.php",
            Array(),
            Array("MODE"=>"html")
        );?>  
                </div>
                <div class="header">
                    <div class="logo"><a href="<?=SITE_DIR?>"><?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/school_logo.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?></a></div>
                    <div class="headerR">
                        <h1><?  $APPLICATION->IncludeFile(
                            SITE_DIR."include_areas/school_name.php",
                            Array(),
                            Array("MODE"=>"html")
                        );?></h1>
                        <?$APPLICATION->IncludeComponent("bitrix:search.title", "search_title", Array(
                         "NUM_CATEGORIES" => "3",
                         "TOP_COUNT" => "5",
                         "USE_LANGUAGE_GUESS" => "Y",
                         "CHECK_DATES" => "N",
                         "SHOW_OTHERS" => "Y",
                         "PAGE" => "#SITE_DIR#search/index.php",
                         "CATEGORY_0_TITLE" => GetMessage("SCHOOL_SEARCH_FORM_CATEGORY_0"),
                         "CATEGORY_0" => array(
                          0 => "iblock_news",
                         ),
                         "CATEGORY_0_iblock_news" => array(
                          0 => "all",
                         ),
                         "CATEGORY_1_TITLE" => GetMessage("SCHOOL_SEARCH_FORM_CATEGORY_1"),
                         "CATEGORY_1" => array(
                          0 => "main",
                         ),
                         "CATEGORY_1_main" => "",
                         "CATEGORY_OTHERS_TITLE" => GetMessage("SCHOOL_SEARCH_FORM_CATEGORY_OTHERS"),
                         "SHOW_INPUT" => "Y",
                         "INPUT_ID" => "title-search-input",
                         "CONTAINER_ID" => "title-search",
                         ),
                         false
                        );?>
                    </div>
                </div>
                <div class="content clearfix">
                    <div class="leftCol">
                        <?if($APPLICATION->GetCurDir() == SITE_DIR."search/"):?>
                         <?$APPLICATION->IncludeComponent("bitrix:menu", "left_menu", Array(
                            "ROOT_MENU_TYPE"	=>	"left",
                            "MAX_LEVEL"	=>	"1",
                            "CHILD_MENU_TYPE"	=>	"left",
                            "USE_EXT"	=>	"Y",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => Array()
                          )
                         );?>
                        <?else:?>
                         <?$APPLICATION->IncludeComponent("bitrix:menu", "left_menu", Array(
                            "ROOT_MENU_TYPE"	=>	"top",
                            "MAX_LEVEL"	=>	"3",
                            "CHILD_MENU_TYPE"	=>	"left",
                            "USE_EXT"	=>	"Y",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => Array()
                          )
                         );?>
                        <?endif?>
                       <br />
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/left_top.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                        <br /><br>
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/left_center.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                     <br />
                     <?$APPLICATION->IncludeFile(
                        $APPLICATION->GetTemplatePath("include_areas/left_bottom.php"),
                        Array(),
                        Array("MODE"=>"html")
                     );?>
                    </div>
                    <?if ($APPLICATION->GetCurPage() == SITE_DIR):?>
                    <div class="rightCol">
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/right_top.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                        <div class="about">
                            <?$APPLICATION->IncludeFile(
                                $APPLICATION->GetTemplatePath("include_areas/about.php"),
                                Array(),
                                Array("MODE"=>"html")
                            );?>
                        </div>
                        <br />
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/right_center.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?><br />
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            Array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => "index_photo.php",
                                "EDIT_TEMPLATE" => ""
                            ),
                        false
                        );?>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            Array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => "index_video.php",
                                "EDIT_TEMPLATE" => ""
                            ),
                        false
                        );?>
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/right_bottom.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                    </div>
                    <?endif?>
                    <div class="centerCol">
                      <?$APPLICATION->IncludeFile(
                        $APPLICATION->GetTemplatePath("include_areas/center_top.php"),
                        Array(),
                        Array("MODE"=>"html")
                      );?>
                      <?if ($APPLICATION->GetCurPage() != SITE_DIR):?>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:breadcrumb",
                            "breadcrumb",
                            Array(
                                "START_FROM" => "0",
                                "PATH" => "",
                                "SITE_ID" => ""
                            )
                        );?>
                        <h2><?=$APPLICATION->ShowTitle(false)?> <i class="line"></i></h2>
                      <?endif?>
                        