<?
##############################################
# ArtDepo.Gallery module                     #
# Copyright (c) 2013 AdrDepo                 #
# http://artdepo.com.ua                      #
# mailto:depo@artdepo.cm.ua                  #
##############################################

global $DB, $MESS, $APPLICATION;

CModule::IncludeModule("fileman");
CMedialib::Init();

CModule::AddAutoloadClasses("artdepo.gallery", array(
	"CArtDepoGallerySection" => "classes/general/artdepo_gallery.php",
	"CArtDepoGalleryImage" => "classes/general/artdepo_gallery.php",
	"CArtDepoGalleryUtils" => "classes/general/artdepo_gallery.php",
));

// JavaScript with lang-files
CJSCore::RegisterExt('artdepo_gallery_sections', array(
    'js' => '/bitrix/js/artdepo.gallery/sections.js',
    'lang' => '/bitrix/modules/artdepo.gallery/lang/'.LANGUAGE_ID.'/sections_js.php',
    'rel' => array('admin_interface')
));
CJSCore::RegisterExt('artdepo_gallery_image_upload_handler', array(
    'js' => '/bitrix/js/artdepo.gallery/image_upload_handler.js',
    'lang' => '/bitrix/modules/artdepo.gallery/lang/'.LANGUAGE_ID.'/image_upload_handler_js.php',
    'rel' => array('admin_interface')
));
?>
