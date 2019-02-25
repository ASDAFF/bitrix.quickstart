<?php
IncludeModuleLangFile(__FILE__);
if('D' < $APPLICATION->GetGroupRight("zionec.sitemap")) {
    $arMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "zionec.sitemap",
        "sort" => 500,
        "text" => GetMessage("SITEMAP_MENU_ITEM"),
        "title" => GetMessage("SITEMAP_MENU_TITLE"),
        "url" => "sitemap_edit.php?lang=" . LANGUAGE_ID,
        "icon" => "sitemap_menu_icon",
        "page_icon" => "sitemap_page_icon",
    );
    return $arMenu;
}
?>