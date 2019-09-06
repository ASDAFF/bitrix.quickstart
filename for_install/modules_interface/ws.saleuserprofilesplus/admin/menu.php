<?php
use WS\SaleUserProfilesPlus\Module;

$MODULE_ID = basename(dirname(dirname(__FILE__)));
CModule::IncludeModule($MODULE_ID);

global $APPLICATION;

$POST_RIGHT = $APPLICATION->GetGroupRight("ws.saleuserprofilesplus");
if ($POST_RIGHT == "D"){
    return array();
}

return array(
    "parent_menu" => "global_menu_store",
    "section" => $MODULE_ID,
    "sort" => 125,
    "text" => Module::get()->getMessage("MODULE_NAME"),
    "title" => "",
    "url" => $MODULE_ID . "_" . "list.php",
    "icon" => "sale_menu_icon_buyers",
    "page_icon" => "",
    "items_id" => $MODULE_ID."_items",
    "more_url" => array("/bitrix/admin/ws.saleuserprofilesplus_edit.php"),
    "items" => array()
);