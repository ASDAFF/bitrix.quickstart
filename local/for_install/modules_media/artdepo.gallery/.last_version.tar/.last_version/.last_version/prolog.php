<?
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("fileman");
CMedialib::Init();

define('ADMIN_MODULE_NAME', 'artdepo.gallery');

if (!CMedialib::CanDoOperation('medialib_edit_collection', 0))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
?>
