<?
##############################################
# ArtDepo.Gallery module                     #
# Copyright (c) 2013 AdrDepo                 #
# http://artdepo.com.ua                      #
# mailto:depo@artdepo.cm.ua                  #
##############################################

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("fileman");
CMedialib::Init();

if( CMedialib::CanDoOperation('medialib_edit_collection', 0) )
{
    CModule::IncludeModule('artdepo.gallery');
    $aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "artdepo.gallery",
		"sort" => 500,
		"module_id" => "artdepo.gallery",
		"text" => GetMessage("ARTDEPO_GALLERY_MENU_MAIN"),
		"title" => GetMessage("ARTDEPO_GALLERY_MENU_MAIN"),
		"url" => "artdepo_gallery_index_admin.php?lang=".LANG,
		"icon"=>"artdepo_gallery_menu_icon",
		"items_id" => "menu_artdepo_gallery",
		"more_url" => array(
		    "artdepo_gallery_index_admin.php",
		    "artdepo_gallery_section_admin.php",
		    "artdepo_gallery_album_admin.php",
		),
		"items" => array()
    );
	return $aMenu;
}
return false;
?>
