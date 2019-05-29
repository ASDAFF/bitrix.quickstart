<?php
IncludeModuleLangFile(__FILE__);

$aMenu = Array();
$sModuleId = "step2use.redirects";

$S2U_GLOSSARY_RIGHT = $APPLICATION->GetGroupRight($sModuleId);
if ($S2U_GLOSSARY_RIGHT > "D")
{
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => $sModuleId,
        "sort" => 500,
        "text" => GetMessage("S2U_REDIRECT_MAIN_MENU"),
        "title" =>GetMessage("S2U_REDIRECT_MENU_TITLE"),
        "icon" => "s2u_redirect_menu_icon",
        "page_icon" => "s2u_redirect_page_icon",
        "items_id" => "s2u_redirect_main",
        "url" => "step2use_redirects_index.php?lang=".LANG,
        "items" =>
        Array(

            array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU"),
                "url" => "step2use_redirects_list.php?lang=".LANGUAGE_ID,
                "item_id"=> "s2u_redirect_list",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "more_url" => array("step2use_redirects_edit.php", "step2use_redirects_list.php"),
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_TITLE"),
            ),
			array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_CSV"),
                "url" => "step2use_redirects_import_csv.php?lang=".LANGUAGE_ID,
                "item_id"=> "s2u_redirect_list",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "title" => GetMessage("S2U_REDIRECT_ADMIN_CSV_DESC"),
            ),
            array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_404_REPORT"),
                "url" => "step2use_redirects_404_report.php?lang=".LANGUAGE_ID, // report
                "item_id"=> "s2u_redirect_404",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "more_url" => array("step2use_redirects_404_report.php"),
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_404_REPORT_TITLE"),
            ),
            array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_404"),
                "url" => "step2use_redirects_404_list.php?lang=".LANGUAGE_ID, // report
                "item_id"=> "s2u_redirect_404",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "more_url" => array("step2use_redirects_404_list.php"),
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_404_TITLE"),
            ),
            array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_404_IGNORE"),
                "url" => "step2use_redirects_404_ignore_list.php?lang=".LANGUAGE_ID, // report
                "item_id"=> "s2u_redirect_404_ignore",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_404_IGNORE_TITLE"),
                "more_url" => array('step2use_redirects_404_ignore_list.php', "step2use_redirects_404_ignore_edit.php"),
            ),
            /*array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_CHECK_INDEX"),
                "url" => "step2use_redirects_check_index.php?lang=".LANGUAGE_ID,
                "item_id"=> "s2u_redirect_check_index",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "more_url" => array("step2use_redirects_check_index.php"),
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_CHECK_INDEX"),
            ),*/
            array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN"),
                "url" => "#",
                "item_id"=> "s2u_redirect_gen",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN"),
                "items" => array(
                    array(
                        "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN_CHANGE_SEF"),
                        "url" => "/bitrix/admin/step2use_redirects_change_sef.php?lang=".LANGUAGE_ID,
                        "item_id"=> "s2u_redirect_gen_change_sef",
                        "icon" => "s2u_redirect_menu_icon_gallery",
                        "page_icon" => "s2u_redirect_listpage_icon",
                        "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN_CHANGE_SEF"),
                    ),
					array(
                        "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN_CHANGE_SEF_SECT"),
                        "url" => "/bitrix/admin/step2use_redirects_change_sef_sect.php?lang=".LANGUAGE_ID,
                        "item_id"=> "s2u_redirect_gen_change_sef",
                        "icon" => "s2u_redirect_menu_icon_gallery",
                        "page_icon" => "s2u_redirect_listpage_icon",
                        "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN_CHANGE_SEF_SECT"),
                    ),
                    array(
                        "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN_TO_BITRIX"),
                        "url" => "/bitrix/admin/step2use_redirects_to_bitrix.php?lang=".LANGUAGE_ID,
                        "item_id"=> "s2u_redirect_gen_to_bitrix",
                        "icon" => "s2u_redirect_menu_icon_gallery",
                        "page_icon" => "s2u_redirect_listpage_icon",
                        "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_GEN_TO_BITRIX"),
                    ),
                ),
            ),

            array(
                "text" => GetMessage("S2U_REDIRECT_ADMIN_MENU_SETTINGS"),
                "url" => "/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=step2use.redirects",
                "item_id"=> "s2u_redirect_settings",
                "icon" => "s2u_redirect_menu_icon_gallery",
                "page_icon" => "s2u_redirect_listpage_icon",
                "title" => GetMessage("S2U_REDIRECT_ADMIN_MENU_SETTINGS"),
            ),

            //
        )
    );
}
else
{
    define("S2U_REDIRECT_ACCESS_DENIED","Y");
    return false;
}
return $aMenu;
?>
