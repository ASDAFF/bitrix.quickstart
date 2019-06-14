<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_services",
    "section" => "mibix.yamexport",
    "sort" => 300,
    "text" => GetMessage("yam_mnu_sect"),
    "title" => GetMessage("yam_mnu_sect_title"),
    "url" => "mibix.yamexport_service_index.php?lang=".LANGUAGE_ID,
    "icon" => "mibix_yamexport_menu_icon",
    "page_icon" => "mibix_yamexport_page_icon",
    "items_id" => "menu_mibix_yam",
    "items" => array(
        array(
            "text" => GetMessage("yam_mnu_main"),
            "url" => "mibix.yamexport_general_list.php?lang=".LANGUAGE_ID,
            "more_url" => array("mibix.yamexport_general_settings.php"),
            "title" => GetMessage("yam_mnu_main_alt")
        ),
        array(
            "text" => GetMessage("yam_mnu_datasource"),
            "url" => "mibix.yamexport_datasource_list.php?lang=".LANGUAGE_ID,
            "more_url" => array("mibix.yamexport_datasource_edit.php"),
            "title" => GetMessage("yam_mnu_datasource_alt")
        ),
        array(
            "text" => GetMessage("yam_mnu_rules"),
            "url" => "mibix.yamexport_rules_admin.php?lang=".LANGUAGE_ID,
            "more_url" => array("mibix.yamexport_rules_edit.php"),
            "title" => GetMessage("yam_mnu_rules_alt")
        ),
        array(
            "text" => GetMessage("yam_mnu_instr"),
            "url" => "mibix.yamexport_instr.php?lang=".LANGUAGE_ID,
            "title" => GetMessage("yam_mnu_instr_alt")
        )
    )
);

return $aMenu;
?>