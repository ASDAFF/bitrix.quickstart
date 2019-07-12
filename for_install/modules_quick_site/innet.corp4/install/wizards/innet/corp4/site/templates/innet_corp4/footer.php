<?IncludeTemplateLangFile(__FILE__);?>

        </div><!--.inner-->

        <?if ($index){?>
            <?$APPLICATION->IncludeComponent("bitrix:news.list", "projects", array(
                    "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                    "IBLOCK_ID" => "#INNET_IBLOCK_ID_PROJECTS#",
                    "NEWS_COUNT" => "10",
                    "SORT_BY1" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => array(),
                    "PROPERTY_CODE" => array(),
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "SET_STATUS_404" => "Y",
                    "SET_TITLE" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "INCLUDE_SUBSECTIONS" => "N",
                    "PAGER_TEMPLATE" => "",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "AJAX_OPTION_ADDITIONAL" => ""
                ),
                false
            );?>

            <div class="slide">
                <div class="inner cols1">
                    <div class="col1">
                        <div class="title5"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/about_1.php", "EDIT_TEMPLATE" => "" ), false );?></div>
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/about_2.php", "EDIT_TEMPLATE" => "" ), false );?>
                    </div>
                    <div class="col2">
                        <div class="clearfix">
                            <div class="title5 fll"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/news_1.php", "EDIT_TEMPLATE" => "" ), false );?></div>
                            <a href="<?=SITE_DIR?>news/" class="btn3 flr"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/news_2.php", "EDIT_TEMPLATE" => "" ), false );?></a>
                        </div>
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:news.list",
                            "news",
                            array(
                                "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                                "IBLOCK_ID" => "#INNET_IBLOCK_ID_NEWS#",
                                "NEWS_COUNT" => "2",
                                "SORT_BY1" => "ACTIVE_FROM",
                                "SORT_ORDER1" => "DESC",
                                "SORT_BY2" => "SORT",
                                "SORT_ORDER2" => "ASC",
                                "FILTER_NAME" => "",
                                "FIELD_CODE" => array(
                                    0 => "DATE_ACTIVE_FROM",
                                ),
                                "PROPERTY_CODE" => array(),
                                "CHECK_DATES" => "Y",
                                "DETAIL_URL" => "",
                                "AJAX_MODE" => "N",
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "N",
                                "AJAX_OPTION_HISTORY" => "N",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "3600",
                                "CACHE_FILTER" => "N",
                                "CACHE_GROUPS" => "Y",
                                "PREVIEW_TRUNCATE_LEN" => "",
                                "ACTIVE_DATE_FORMAT" => "j F Y",
                                "SET_STATUS_404" => "N",
                                "SET_TITLE" => "N",
                                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                "ADD_SECTIONS_CHAIN" => "N",
                                "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                                "PARENT_SECTION" => "",
                                "PARENT_SECTION_CODE" => "",
                                "INCLUDE_SUBSECTIONS" => "N",
                                "PAGER_TEMPLATE" => "",
                                "DISPLAY_TOP_PAGER" => "N",
                                "DISPLAY_BOTTOM_PAGER" => "N",
                                "PAGER_TITLE" => "",
                                "PAGER_SHOW_ALWAYS" => "N",
                                "PAGER_DESC_NUMBERING" => "N",
                                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                "PAGER_SHOW_ALL" => "N",
                                "AJAX_OPTION_ADDITIONAL" => "",
                                "COMPONENT_TEMPLATE" => "news",
                                "SET_BROWSER_TITLE" => "N",
                                "SET_META_KEYWORDS" => "N",
                                "SET_META_DESCRIPTION" => "N",
                                "SET_LAST_MODIFIED" => "N",
                                "PAGER_BASE_LINK_ENABLE" => "N",
                                "SHOW_404" => "N",
                                "MESSAGE_404" => ""
                            ),
                            false
                        );?>
                    </div>
                </div>
            </div>

            <?$APPLICATION->IncludeComponent("bitrix:news.list", "partners", array(
                    "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                    "IBLOCK_ID" => "#INNET_IBLOCK_ID_PARTNERS#",
                    "NEWS_COUNT" => "10",
                    "SORT_BY1" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => array(),
                    "PROPERTY_CODE" => array(),
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "SET_STATUS_404" => "Y",
                    "SET_TITLE" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "INCLUDE_SUBSECTIONS" => "N",
                    "PAGER_TEMPLATE" => "",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "AJAX_OPTION_ADDITIONAL" => ""
                ),
                false
            );?>

            <div class="map">
                <div class="inner">
                    <div class="block">
                        <div class="title5"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/contacts_title.php", "EDIT_TEMPLATE" => "" ), false );?></div>
                        <ul>
                            <li>
                                <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/address_title.php", "EDIT_TEMPLATE" => "" ), false );?></span>
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/address.php", "EDIT_TEMPLATE" => "" ), false );?>
                            </li>
                            <li>
                                <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_title.php", "EDIT_TEMPLATE" => "" ), false );?></span>
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_2.php", "EDIT_TEMPLATE" => "" ), false );?>
                            </li>
                            <li>
                                <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_title.php", "EDIT_TEMPLATE" => "" ), false );?></span>
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_2.php", "EDIT_TEMPLATE" => "" ), false );?>
                            </li>
                        </ul>
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/map/big_map.php", "EDIT_TEMPLATE" => "" ), false );?>
                    </div>
                </div>

                <?$APPLICATION->IncludeComponent(
                    "bitrix:map.yandex.view", 
                    ".default", 
                    array(
                        "INIT_MAP_TYPE" => "MAP",
                        "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.7465434164318;s:10:\"yandex_lon\";d:37.43674800422821;s:12:\"yandex_scale\";i:10;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.62214229133761;s:3:\"LAT\";d:55.75273983257828;s:4:\"TEXT\";s:0:\"\";}}}",
                        "MAP_WIDTH" => "100%",
                        "MAP_HEIGHT" => "350",
                        "CONTROLS" => array(
                            0 => "ZOOM",
                            1 => "SMALLZOOM",
                            2 => "MINIMAP",
                            3 => "TYPECONTROL",
                            4 => "SCALELINE",
                        ),
                        "OPTIONS" => array(
                            0 => "ENABLE_DBLCLICK_ZOOM",
                            1 => "ENABLE_DRAGGING",
                        ),
                        "MAP_ID" => "yam_1",
                        "COMPONENT_TEMPLATE" => ".default",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO"
                    ),
                    false
                );?>
            </div>
        <?}?>
    </div><!-- .content -->
</div><!-- .wrapper -->

<div class="footer header">
    <div class="lvl2">
        <div class="inner clearfix">
            <?$APPLICATION->IncludeComponent("bitrix:menu", "footer", array(
                    "ROOT_MENU_TYPE" => "top",
                    "MENU_CACHE_TYPE" => "A",
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_USE_GROUPS" => "N",
                    "MENU_CACHE_GET_VARS" => array(),
                    "MAX_LEVEL" => "1",
                    "CHILD_MENU_TYPE" => "",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N"
                ),
                false
            );?>
        </div>
    </div>
    <div class="lvl1">
        <div class="inner in-row-mid">
            <div class="col1">
                <a href="<?=SITE_DIR?>">
                    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/logo.php", "EDIT_TEMPLATE" => "" ), false );?>
                </a>
            </div>
            <div class="col2">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_1.php", "EDIT_TEMPLATE" => "" ), false );?>
            </div>
            <div class="col3">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_1.php", "EDIT_TEMPLATE" => "" ), false );?>
            </div>
            <div class="col4 in-row-mid">
                <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/social_title.php", "EDIT_TEMPLATE" => "" ), false );?></span>
                <div>
                    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/templates/social.php", "EDIT_TEMPLATE" => "" ), false );?>
                </div>
            </div>
        </div>
    </div>
</div><!-- .footer header-->

<div id="1" class="popwindow" data-title="" data-height="auto">
    <div class="popup-wrap1">
        <a class="bw_close"></a>
        <?$APPLICATION->IncludeComponent("innet:form", "callback", array(
                "USE_CAPTCHA" => "Y",
                "EVENT_MESSAGE_ID" => array(),
                "EVENT_MESSAGE_TYPE" => "INNET_CALLBACK",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_CALLBACK_USER",
                "OK_TEXT" => "",
                "EMAIL_TO" => "",
                "REQUIRED_FIELDS" => array("NAME", "PHONE"),
                "AJAX_MODE" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "INNET_IBLOCK_ID_RECORD" => "#INNET_IBLOCK_ID_CALLBACK#",
            ),
            false
        );?>
    </div>
</div>

<div id="7" class="popwindow" data-title="" data-height="auto">
    <div class="popup-wrap1">
        <a class="bw_close"></a>
        <?$APPLICATION->IncludeComponent("innet:form", "services_question", array(
                "USE_CAPTCHA" => "Y",
                "EVENT_MESSAGE_ID" => array(),
                "OK_TEXT" => "",
                "EMAIL_TO" => "",
                "REQUIRED_FIELDS" => array("NAME", "EMAIL"),
                "AJAX_MODE" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "EVENT_MESSAGE_TYPE" => "INNET_SERVICE_QUESTIONS",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_SERVICE_QUESTIONS_USER",
                "INNET_IBLOCK_ID_RECORD" => "#INNET_IBLOCK_ID_QUESTIONS_SERVICES#",
            ),
            false
        );?>
    </div>
</div>

<!--    bottom fixed menu  <<<    -->
<?/*$APPLICATION->IncludeComponent("innet:fixed.footer.menu", "innet", array(
        "CACHE_TYPE" => "Y",
        "CACHE_TIME" => "3600",
        "CATALOG_IBLOCK_ID" => "#INNET_IBLOCK_ID_CATALOG#",
    ),
    false
);*/?>
<!--    >>>    bottom fixed menu    -->

</body>
</html>