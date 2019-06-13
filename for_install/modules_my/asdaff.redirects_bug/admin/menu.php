<?php
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);

$aMenu = Array();
$sModuleId = "asdaff.redirects";

$SEO2_GLOSSARY_RIGHT = $APPLICATION->GetGroupRight($sModuleId);
if ($SEO2_GLOSSARY_RIGHT > "D")
{
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => $sModuleId,
        "sort" => 500,
        "text" => GetMessage("SEO2_REDIRECT_MAIN_MENU"),
        "title" =>GetMessage("SEO2_REDIRECT_MENU_TITLE"),
        "icon" => "seo2_redirect_menu_icon",
        "page_icon" => "seo2_redirect_page_icon",
        "items_id" => "seo2_redirect_main",
        "url" => "asdaff_redirects_index.php?lang=".LANG,
        "items" =>
        Array(

            array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_MENU"),
                "url" => "asdaff_redirects_list.php?lang=".LANGUAGE_ID,
                "item_id"=> "seo2_redirect_list",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "more_url" => array("asdaff_redirects_edit.php", "asdaff_redirects_list.php"),
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_TITLE"),
            ),
			array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_CSV"),
                "url" => "asdaff_redirects_import_csv.php?lang=".LANGUAGE_ID,
                "item_id"=> "seo2_redirect_list",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_CSV_DESC"),
            ),
            array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_404_REPORT"),
                "url" => "asdaff_redirects_404_report.php?lang=".LANGUAGE_ID, // report
                "item_id"=> "seo2_redirect_404",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "more_url" => array("asdaff_redirects_404_report.php"),
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_404_REPORT_TITLE"),
            ),
            array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_404"),
                "url" => "asdaff_redirects_404_list.php?lang=".LANGUAGE_ID, // report
                "item_id"=> "seo2_redirect_404",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "more_url" => array("asdaff_redirects_404_list.php"),
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_404_TITLE"),
            ),
            array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_404_IGNORE"),
                "url" => "asdaff_redirects_404_ignore_list.php?lang=".LANGUAGE_ID, // report
                "item_id"=> "seo2_redirect_404_ignore",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_404_IGNORE_TITLE"),
                "more_url" => array('asdaff_redirects_404_ignore_list.php', "asdaff_redirects_404_ignore_edit.php"),
            ),
            array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_CHECK_INDEX"),
                "url" => "asdaff_redirects_check_index.php?lang=".LANGUAGE_ID,
                "item_id"=> "seo2_redirect_check_index",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "more_url" => array("asdaff_redirects_check_index.php"),
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_CHECK_INDEX"),
            ),
            array(
                "text" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_SETTINGS"),
                "url" => "/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=asdaff.redirects",
                "item_id"=> "seo2_redirect_settings",
                "icon" => "seo2_redirect_menu_icon_gallery",
                "page_icon" => "seo2_redirect_listpage_icon",
                "title" => GetMessage("SEO2_REDIRECT_ADMIN_MENU_SETTINGS"),
            ),
			
            // 
        )
    );
}
else
{
    define("SEO2_REDIRECT_ACCESS_DENIED","Y");
    return false;
}
return $aMenu;
?>