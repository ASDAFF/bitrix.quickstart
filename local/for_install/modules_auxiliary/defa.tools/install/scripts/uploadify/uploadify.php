<?
define("STOP_STATISTICS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (
	CModule::IncludeModule('defa.tools')
	&& !DefaTools_IBProp_MultipleFiles::SaveFile($_REQUEST["el_id"], $_REQUEST["iblock_id"], $_REQUEST["prop_id"], $_FILES['Filedata'])
) {
	header ('HTTP/1.1 403 Forbidden');
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");