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
                <div class="header">
                    <div class="headerTop clearfix">
                        <div class="logo"><a href="<?=SITE_DIR?>"><?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/school_logo.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?></a></div>
                        <div class="headerR">
                        <?  $APPLICATION->IncludeFile(
                            SITE_DIR."include_areas/schooltop_links.php",
                            Array(),
                            Array("MODE"=>"html")
                        );?>  
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
                        <h1><?  $APPLICATION->IncludeFile(
                            SITE_DIR."include_areas/school_name.php",
                            Array(),
                            Array("MODE"=>"html")
                        );?></h1>
                    </div>
                    <?$APPLICATION->IncludeComponent("bitrix:menu", "top_menu", Array(
                            "ROOT_MENU_TYPE"	=>	"top",
                            "MAX_LEVEL"	=>	"2",
                            "CHILD_MENU_TYPE"	=>	"left",
                            "USE_EXT"	=>	"Y",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => Array()
                        )
                    );?>
                    <?if($APPLICATION->GetCurDir() == SITE_DIR):?>
                    <div class="headerBot">
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
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/index_menu.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                    </div>
                    <?endif?>
                </div>
                <div class="content clearfix">
                    <div class="leftCol">
                        <?if($APPLICATION->GetCurDir() == SITE_DIR):?>
                         <?$APPLICATION->IncludeComponent(
                          "bitrix:main.include",
                          "",
                          Array(
                           "AREA_FILE_SHOW" => "file",
                           "PATH" => "index_news.php",
                           "EDIT_TEMPLATE" => ""
                          ),
                         false
                         );?>
                         <?$APPLICATION->IncludeComponent(
                          "bitrix:main.include",
                          "",
                          Array(
                           "AREA_FILE_SHOW" => "file",
                           "PATH" => "index_events.php",
                           "EDIT_TEMPLATE" => ""
                          ),
                         false
                         );?>
                        <?else:?>
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
                         <?endif?>
                        <?if($APPLICATION->GetCurDir() == SITE_DIR.'search/'):?>
                         <?$APPLICATION->IncludeComponent("bitrix:menu", "main_left_menu", array(
                            "ROOT_MENU_TYPE" => "left",
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
                        );?>
                        <?else:?>
                         <?$APPLICATION->IncludeComponent("bitrix:menu", "left_menu", array(
                            "ROOT_MENU_TYPE" => "top",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => array(
                            ),
                            "MAX_LEVEL" => "3",
                            "CHILD_MENU_TYPE" => "left",
                            "USE_EXT" => "Y",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
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
                    <?if($APPLICATION->GetCurDir() == SITE_DIR):?>
                    <div class="rightCol">
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/right_top.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                     
                <?$APPLICATION->IncludeComponent("bitrix:news.line", "main_docs", array(
                    "IBLOCK_TYPE" => "library",
                    "IBLOCKS" => array(
                    ),
                    "NEWS_COUNT" => "3",
                    "FIELD_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "DETAIL_URL" => "",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "300",
                    "CACHE_GROUPS" => "Y",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y"
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
                        <?if($APPLICATION->GetCurDir() != SITE_DIR):?>
                         <div class="top clearfix">
                         <?$APPLICATION->IncludeComponent(
                             "bitrix:breadcrumb",
                             "breadcrumb",
                             Array(
                                 "START_FROM" => "0",
                                 "PATH" => "",
                                 "SITE_ID" => ""
                             )
                         );?>
                         </div>
                         <h2><?=$APPLICATION->ShowTitle(false)?></h2>
                        <?endif?>
                        