<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetGroupRight("sota.parser") != "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_content",
        "section" => "sota.parser",
        "sort" => 100,
        "text" => GetMessage("mnu_sota_parser_sect"),
        "title" => GetMessage("mnu_sota_parser_sect_title"),
        "url" => "list_parser_admin.php?lang=" . LANGUAGE_ID,
        "icon" => "sota_parser_menu_icon",
        "page_icon" => "sota_parser_page_icon",
        "items_id" => "menu_sota.parser",
        "items" => array(
            array(
                "text" => GetMessage("mnu_sota_list_parser"),
                "url" => "list_parser_admin.php?lang=" . LANGUAGE_ID,
                "more_url" => array("list_parser_admin.php", "parser_edit.php"),
                "title" => GetMessage("mnu_sota_list_parser_alt")
            ),
        )
    );

    return $aMenu;
}
return false;
?>