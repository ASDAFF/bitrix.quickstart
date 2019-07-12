<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt';
if (CModule::IncludeModule($module_id) && $APPLICATION->GetGroupRight($module_id) >= "R")
{
	IncludeModuleLangFile(__FILE__);      
	    
	$sPage = $APPLICATION->GetCurPage();
	$arPage = pathinfo($sPage);
	$sPage = $arPage['basename'];
	
	$arItems = array();
	$arRefItems = array();   
	
    $arItems[] = array( 
        "text" => GetMessage("SMARTREALT_MENU_RUBRIC_GROUP"),
        "url" => "smartrealt_rubric_group_list.php?lang=".LANGUAGE_ID,
        "more_url" => array("smartrealt_rubric_group_list.php", "smartrealt_rubric_group_edit.php"),
        "title" => GetMessage("SMARTREALT_MENU_RUBRIC_GROUP_ALT"),
        "icon" => "smartrealt_guide_menu_icon",
        "page_icon" => "smartrealt_guide_page_icon",
    );
    
    $arItems[] = array( 
        "text" => GetMessage("SMARTREALT_MENU_RUBRIC"),
        "url" => "smartrealt_rubric_list.php?lang=".LANGUAGE_ID,
        "more_url" => array("smartrealt_rubric_list.php", "smartrealt_rubric_edit.php"),
        "title" => GetMessage("SMARTREALT_MENU_RUBRIC_ALT"),
        "icon" => "smartrealt_guide_menu_icon",
        "page_icon" => "smartrealt_guide_page_icon",
    );
    
    $arItems[] = array( 
        "text" => GetMessage("SMARTREALT_MENU_UPDATE_DATA"),
        "url" => "smartrealt_update_data.php?lang=".LANGUAGE_ID,
        "more_url" => array("smartrealt_update_data.php"),
        "title" => GetMessage("SMARTREALT_MENU_UPDATE_DATA_ALT"),
        "icon" => "smartrealt_update_menu_icon",
        "page_icon" => "smartrealt_update_page_icon",
    );
    
	$arItems[] = array( 
		"text" => GetMessage("SMARTREALT_MENU_DOWNLOAD_SOFT"),
		"url" => "http://soft.smartrealt.com/setup.exe",
		"more_url" => array(""),
        "title" => GetMessage("SMARTREALT_MENU_DOWNLOAD_SOFT_ALT"),
		"icon" => "smartrealt_update_menu_icon",
		"page_icon" => "smartrealt_update_page_icon",
	);
	
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "webdoka.smartrealt",
		"sort" => 550,
		"text" => GetMessage("SMARTREALT_MENU"),
		"title"=> GetMessage("SMARTREALT_MENU_ALT"),
		"url" => "smartrealt_desktop.php?lang=".LANGUAGE_ID,
		"more_url" => array("smartrealt_wrong_query.php"), 
		"icon" => "smartrealt_menu_icon",
		"page_icon" => "smartrealt_page_icon",
		"items_id" => "webdoka.smartrealt_menu",
		"items" => $arItems,
	);
	
	return $aMenu;
}
return false;
?>
