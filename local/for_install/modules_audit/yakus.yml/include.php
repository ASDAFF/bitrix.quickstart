<?
IncludeModuleLangFile(__FILE__);

Class CYakusYml
{
    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {

        if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
            return;

        $MODULE_ID = basename(dirname(__FILE__));
        $aMenu = array(
            "parent_menu" => "global_menu_store",
            "section" => "yakus_services",
            "sort" => 105,
            "url" => "yakus_yml.php",
            //"more_url" => array("tc_comment_edit.php"),
            "text" => GetMessage("yakus.yml_NAME"),
            "title" => GetMessage("yakus.yml_NAME"),
            "icon" => "update_menu_icon_partner",
            //"page_icon" => "",
            "items_id" => "menu_yakus_yml",
            "items" => array()
        );


        $aModuleMenu[] = $aMenu;

    }
}

?>
