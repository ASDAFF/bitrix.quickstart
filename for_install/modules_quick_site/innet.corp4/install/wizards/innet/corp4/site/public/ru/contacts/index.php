<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<div class="cols4 contacts">
    <div class="col1">
        <div class="icon1-1">
            <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_title.php", "EDIT_TEMPLATE" => "" ), false );?>:</span><br>
            <div class="fs18">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/phone_2.php", "EDIT_TEMPLATE" => "" ), false );?>
            </div>
        </div>
        <div class="icon1-2">
            <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_title.php", "EDIT_TEMPLATE" => "" ), false );?>:</span><br>
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/email_2.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>
        <div class="icon1-3">
            <span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/address_title.php", "EDIT_TEMPLATE" => "" ), false );?>:</span>
            <div><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/address.php", "EDIT_TEMPLATE" => "" ), false );?></div>
        </div>
        <div class="icon1-4">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/mode.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>
    </div>
    <div class="col2">
        <div class="map">
            <?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
                    "INIT_MAP_TYPE" => "MAP",
                    "MAP_DATA" => "a:3:{s:10:\"yandex_lat\";d:55.74925447012473;s:10:\"yandex_lon\";d:37.587810015946864;s:12:\"yandex_scale\";i:10;}",
                    "MAP_WIDTH" => "100%",
                    "MAP_HEIGHT" => "460",
                    "CONTROLS" => array(
                        0 => "ZOOM",
                        1 => "SMALLZOOM",
                        2 => "MINIMAP",
                        3 => "TYPECONTROL",
                        4 => "SCALELINE",
                    ),
                    "OPTIONS" => array(
                        0 => "ENABLE_SCROLL_ZOOM",
                        1 => "ENABLE_DBLCLICK_ZOOM",
                        2 => "ENABLE_DRAGGING",
                    ),
                    "MAP_ID" => "yam_1"
                ),
                false
            );?>
        </div>
        <br/><br/>
        <?$APPLICATION->IncludeComponent("innet:form", "feedback", array(
                "USE_CAPTCHA" => "Y",
                "EVENT_MESSAGE_ID" => array(),
                "EVENT_MESSAGE_TYPE" => "INNET_FEEDBACK",
                "EVENT_MESSAGE_TYPE_USER" => "INNET_FEEDBACK_USER",
                "REQUIRED_FIELDS" => array("NAME", "PHONE"),
                "AJAX_MODE" => "Y",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "INNET_IBLOCK_ID_RECORD" => "#INNET_IBLOCK_ID_FEEDBACK#",
            ),
            false
        );?>
    </div>
</div>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/details.php", "EDIT_TEMPLATE" => "" ), false );?>

<br/><br/>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>