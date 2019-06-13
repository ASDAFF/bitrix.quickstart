<?

IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("mcart.media")!="D"){
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "mcart.media",
        "sort" => 10,
        "text" => GetMessage("MCART_MEDIA_TITLE"),
        "title" => GetMessage("MCART_MEDIA_TITLE"),
        "url" => "media.php?lang=".LANGUAGE_ID,          
        "icon" => "mcart_media_menu_icon",
        "page_icon" => "mcart_media_page_icon",
        "items_id" => "menu_media",
		
    );
    return $aMenu;
	
}
return false;


?>