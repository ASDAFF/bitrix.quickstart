<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $_GET["src_path"] != "" && $_GET["src_line"] != "" ) {
	$src_path = Rel2Abs("/", $_GET["src_path"]);
	$src_line = intval($_GET["src_line"]);
	$abs_path = $_SERVER["DOCUMENT_ROOT"].$src_path;
	$filesrc = CMain::GetFileContent($abs_path);
	$arComponent = PHPParser::FindComponent($_GET["component_name"], $filesrc, $src_line);
	$PARAMS = $arComponent["DATA"]["PARAMS"]["WEB_FORM_PARAMS"];
} else {
	$PARAMS = $_REQUEST["curval"]["WEB_FORM_PARAMS"];
}

$arrForms = GetMessage("JQUERY_NONE").";";       // 1
$arrForms .= GetMessage("SELECT_NONE").";";      // 2
$arrForms .= GetMessage("FSEARCH_NONE").";";     // 3
$arrForms .= GetMessage("FORM_SEARCH").";";      // 4
$arrForms .= GetMessage("FORM_SEARCH_MORE").";"; // 5
$arrForms .= GetMessage("FORM_OTHER").""; // 6

if (CModule::IncludeModule("form"))
{
	$i = 0;
	$rsForm = CForm::GetList($by='s_sort', $order='asc', array("SITE" => $_REQUEST["site"]), $v3);
	while ($arForm = $rsForm->Fetch())
	{
		
		$arrForms .= "||FORM#name#".$arForm["SID"]."#(".$arForm["ID"].") ".str_replace("#","",$arForm["NAME"]);
		
		++$i;
		
	}
}

$PARAMS = explode("||",$PARAMS);

foreach( $PARAMS as $valData ) {
	if( trim($valData) != "" ) {
		$arrForms .= "||SELECTED#".$valData;
	}
}

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		//"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"WEB_FORM" => array(
			"NAME" => GetMessage("COMP_FORM_PARAMS_WEB_FORM_ID"),
			"TYPE" => "CUSTOM",
			"JS_FILE" => "/bitrix/components/elipseart/form.design_modify/script/settings.js",
			"JS_EVENT" => "OnFormDesignSettingsEdit",
			"JS_DATA" => $arrForms,
			"DEFAULT" => "",
			"PARENT" => "DATA_SOURCE",
		),
		
		"WEB_FORM_PARAMS" => array(
			"NAME" => "valid_param",
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DATA_SOURCE",
		),
		
	)
);

?>