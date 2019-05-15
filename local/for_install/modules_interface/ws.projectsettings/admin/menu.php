<?php
__IncludeLang(realpath(dirname(__FILE__).'/../lang/'.LANGUAGE_ID.'.php'));
/* @var $APPLICATION CMain */
if ($APPLICATION->GetGroupRight("ws.projectsettings") == "D") {
    return array();
}

return array(
    "parent_menu" => "global_menu_settings",
    "section"     => "ws_projectsettings",
    "sort"        => 2300,
    "text"        => GetMessage("ws_module_name"),
    "title"       => GetMessage("ws_module_description"),
    "url"         => "ws_projectsettings_settings.php?lang=" . LANGUAGE_ID,
    "icon"        => "sys_menu_icon",
    "page_icon"   => "sys_page_icon",
    "items_id"    => "menu_ws_projectsettings",
    "items"       => array()
);
