<?
	define("STOP_STATISTICS", true);

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
	IncludeModuleLangFile(__FILE__);

	if(!CModule::IncludeModule("av.ibprops")) {
		CAdminMessage::ShowMessage(GetMessage("av_error_module"));
		die();
	}


	header('Content-Type: application/json');

	require_once($_SERVER["DOCUMENT_ROOT"].getLocalPath("/modules/".$MODULE_ID."/classes/general/WebprofyAutoreplace/Data.php"));
	require_once($_SERVER["DOCUMENT_ROOT"].getLocalPath("/modules/".$MODULE_ID."/classes/general/WebprofyAutoreplace/View.php"));

	echo json_encode(WebprofyAutoreplace\Data::getInstance()->getIBlocks());