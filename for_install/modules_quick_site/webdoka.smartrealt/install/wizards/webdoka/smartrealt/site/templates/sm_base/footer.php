<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:soft@smartrealt.com      #
* ###################################
*/
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
?>
        </div>
        <div class="clear"></div>
        <?php if (!eregi('^#WIZ_SITE_DIR#news/', $_SERVER['REQUEST_URI'])) { ?>
        <?php
            $APPLICATION->IncludeComponent("bitrix:news.line", ".default", array(
                    "IBLOCK_TYPE" => "news",
                    "IBLOCKS" => array(
                        0 => "#NEWS_IBLOCK_ID#",
                    ),
                    "NEWS_COUNT" => "3",
                    "FIELD_CODE" => array(
                        0 => "",
                        1 => "CODE",
                        2 => "NAME",
                        3 => "PREVIEW_TEXT",
                        4 => "PREVIEW_PICTURE",
                        5 => "DATE_ACTIVE_FROM",
                        6 => "",
                    ),
                    "SORT_BY1" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_BY2" => "ACTIVE_FROM",
                    "SORT_ORDER2" => "DESC",
                    "DETAIL_URL" => "",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "300",
                    "CACHE_GROUPS" => "Y",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y"
                    ),
                    false
                );
        ?>
        <?php } ?>

        <div class="footer theme-bgcolor">
            <div class="copyright">
                <?php $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/copyright.php')?> 
            </div>
            <div class="right">
                <?php
                    $APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
                        "ROOT_MENU_TYPE" => "bottom",
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
                <div class="clear"></div>
                <div class="counters">
                    <?php $APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/counters.php')?> 
                </div>
                <div class="clear"></div>
                <a class="made" href="http://smartrealt.com/" target="_blank" title="<?php echo GetMessage('R_SMARTREALT'); ?>"><?php echo GetMessage('R_SMARTREALT'); ?></a>
            </div> 
        </div>
    </div>
</body>
</html>

