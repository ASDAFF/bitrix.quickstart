<?
IncludeModuleLangFile(__FILE__);
if($APPLICATION->GetGroupRight("rinsvent.fastauth") != "D")
{
    $arItemsETMenu[] = array(
        "text" => GetMessage("RINSVENT_FASTAUTH_PROCESS"),
        "url" => "/bitrix/admin/rinsvent_fastauth.php?lang=".LANG,
        "title" => GetMessage("RINSVENT_FASTAUTH_PROCESS_ALT")
    );

    if(!empty($arItemsETMenu))
    {
        $aMenu = array(
            "parent_menu" => "global_menu_services",
            "section" => "rinsvent_fastauth",
            "sort" => 1000,
            "text" => GetMessage("RINSVENT_FASTAUTH_CONTROL"),
            "url"  => "/bitrix/admin/rinsvent_fastauth.php?lang=".LANG,
            "title"=> GetMessage("RINSVENT_FASTAUTH_CONTROL"),
            "icon" => "rinsvent_import_menu_icon",
            "page_icon" => "rinsvent_fastauth_page_icon",
            "items_id" => "menu_rinsvent_fastauth",
            "items" => $arItemsETMenu
        );

        return $aMenu;
    }
}
return false;
?>
