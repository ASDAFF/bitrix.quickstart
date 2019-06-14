<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("shs.parser")!="D")
{
    $aMenu = array(
        "parent_menu" => "global_menu_content",
        "section" => "shs.parser",
        "sort" => 100,
        "text" => GetMessage("mnu_shs_parser_sect"),
        "title" => GetMessage("mnu_shs_parser_sect_title"),
        "url" => "list_parser_admin.php?lang=".LANGUAGE_ID,
        "icon" => "shs_parser_menu_icon",
        "page_icon" => "shs_parser_page_icon",
        "items_id" => "menu_shs.parser",
        "items" => array(
            array(
                "text" => GetMessage("mnu_shs_list_parser"),
                "url" => "list_parser_admin.php?lang=".LANGUAGE_ID,
                "more_url" => array("list_parser_admin.php", "parser_edit.php"),
                "title" => GetMessage("mnu_shs_list_parser_alt")
            ),
            array(
                "text" => GetMessage("mnu_shs_list_result"),
                "url" => "list_parser_result_admin.php?lang=".LANGUAGE_ID,
                "more_url" => array(),
                "title" => GetMessage("mnu_shs_list_result")
            ),
        )
    );

    return $aMenu;
}
return false;
?>