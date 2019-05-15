<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_settings",
    "section" => "aqw.video",
    "sort" => 200,
    "text" => GetMessage("aqw_video_options_text"),
    "title" => GetMessage("aqw_video_options_title"),
    "icon" => "sys_menu_icon",
    "page_icon" => "sys_page_icon",
    "items_id" => "menu_aqw_video",
    "items" => array(
		array(
            "text" => GetMessage("aqw_video_settings_text"),
            "url" => "aqw.video_settings.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("aqw.video_settings.php"),
            "title" => GetMessage("aqw_video_settings_alt"),
        ),
    )
);
return $aMenu;

?>
