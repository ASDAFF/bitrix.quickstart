<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("MFP_CAPTCHA"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("MFP_OK_MESSAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_OK_TEXT"), 
			"PARENT" => "BASE",
		),
		"EMAIL_TO" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TO"), 
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")), 
			"PARENT" => "BASE",
		),
		"USE_MESSAGE_FIELD" => Array(
			"NAME" => GetMessage("MFP_USE_MESSAGE_FIELD"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"N",
			"REFRESH"=> "Y",
			"PARENT" => "BASE",			
		),
		"SAVE_FORM_DATA" => Array(
			"NAME" => GetMessage("MFP_SAVE_FORM_DATA"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"N",
			"REFRESH"=> "Y",
			"PARENT" => "BASE",			
		),		
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("MFP_REQUIRED_FIELDS"), 
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"Y", 
			"VALUES" => Array("NONE" => GetMessage("MFP_ALL_REQ"), "NAME" => GetMessage("MFP_NAME"), "PHONE" => GetMessage("MFP_PHONE"), "TIMETOCALL" => GetMessage("MFP_TIME")),
			"DEFAULT"=>"", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),
		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TEMPLATE"), 
			"TYPE" => "STRING",
			"DEFAULT" => "", 
			"PARENT" => "BASE",
		),
		

		/*"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TEMPLATES"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),*/

	)
);

if($arCurrentValues["USE_MESSAGE_FIELD"] == "Y"){
	$arComponentParameters["PARAMETERS"]["REQUIRED_FIELDS"] = Array(
		"NAME" => GetMessage("MFP_REQUIRED_FIELDS"), 
		"TYPE"=>"LIST", 
		"MULTIPLE"=>"Y", 
		"VALUES" => Array(
			"NONE" => GetMessage("MFP_ALL_REQ"), 
			"NAME" => GetMessage("MFP_NAME"), 
			"PHONE" => GetMessage("MFP_PHONE"), 
			"MFP_TIME" => GetMessage("MFP_TIME"),
			"MESSAGE" => GetMessage("MFP_MESSAGE")),
		"DEFAULT"=>"", 
		"COLS"=>25, 
		"PARENT" => "BASE",
	);
}


if($arCurrentValues["SAVE_FORM_DATA"] == "Y"){
	if(!CModule::IncludeModule("iblock"))
		return;

	$arTypesEx = CIBlockParameters::GetIBlockTypes();
	
	$arComponentParameters["PARAMETERS"]["IBLOCK_TYPE"] =  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		);
	
	$arIBlocks = Array();
	$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
	while($arRes = $db_iblock->Fetch())
	{
		$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	}
	
	$arComponentParameters["PARAMETERS"]["IBLOCKS"]  =  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"MULTIPLE" => "Y",
		);
	
}
?>