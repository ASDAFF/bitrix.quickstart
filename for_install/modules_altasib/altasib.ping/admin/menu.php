<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("altasib.ping")!="D")
{
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/prolog.php");

        $aMenu = array(
                "parent_menu" => "global_menu_services",
                "section" => "ping",
                "sort" => 200,
                "text" => GetMessage("PING_MENU_AREA"),
                "title" => GetMessage("PING_MENU_AREA_TITLE"),
                "icon" => "altasib_menu_icon",
                "page_icon" => "altasib_page_icon",
                "items_id" => "menu_ping",
                "more_url" => array(),
                "items" => array(
                        array(
                                "text" => GetMessage("PING_MENU_SET_PING_TITLE"),
                                "url" => "/bitrix/admin/altasib_ping.php?lang=".LANG,
                                "title" => GetMessage("PING_MENU_SET_PING_TITLE")
                        ),
                        array(
                                "text" => GetMessage("PING_MENU_LOG_PING_TITLE"),
                                "url" => "/bitrix/admin/altasib_ping_log.php?lang=".LANG,
                                "title" => GetMessage("PING_MENU_LOG_PING_TITLE")
                        )
                )
        );
                
        return $aMenu;
}
return false;
?>