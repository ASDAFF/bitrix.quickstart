<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));

$arFilter = Array("SITE_ID" => SITE_ID, "LID" => LANGUAGE_ID);

$arETEvent = array();
$rsET = CEventType::GetList($arFilter);
while ($arET = $rsET->Fetch()){
	$arETEvent[$arET["EVENT_NAME"]] = "[".$arET["ID"]."] ".$arET["NAME"];
}

if($arCurrentValues["EVENT_ET_MESSAGE_ID"]){
	$arFilter = Array("TYPE_ID" => $arCurrentValues["EVENT_ET_MESSAGE_ID"], "ACTIVE" => "Y");
}else{
	$arFilter = Array("TYPE_ID" => "PB_BACKCALL_FORM_EVENT", "ACTIVE" => "Y");
}


if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];


$arIBlocks=Array(0 => '-');
$arProperty_S = array(0 => '-');

if(CModule::IncludeModule("iblock")){

	$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"]));
	while($arRes = $db_iblock->Fetch()){
		$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	}

	if($arCurrentValues["EMAIL_TO_IBLOCK"] > 0){

		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["EMAIL_TO_IBLOCK"]));
		while ($arr = $rsProp->Fetch()){
			if (in_array($arr["PROPERTY_TYPE"], array("S"))){
				$arProperty_S[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			}
		}
	}

}


$arComponentParameters = array(
	"GROUPS" => array(

	),
	"PARAMETERS" => array(
		"USE_SYSTEM_JQUERY" => Array(
			"NAME" => GetMessage("PBMFP_USE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE",
		),
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("PBMFP_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE",
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("PBMFP_OK_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_OK_TEXT"),
			"PARENT" => "BASE",
		),
		"ERROR_TEXT" => Array(
			"NAME" => GetMessage("PBMFP_BAD_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_BAD_TEXT"),
			"PARENT" => "BASE",
		),
		"ERROR_REQUIRED_TEXT" => Array(
			"NAME" => GetMessage("PBMFP_REQUIRED_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_BAD_TEXT"),
			"PARENT" => "BASE",
		),
		"POP_HEADING_LABEL" => Array(
			"NAME" => GetMessage("PBMFP_POP_HEADING_LABEL"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_ORDER_CALLBACK_LABEL"),
			"PARENT" => "BASE",
		),
		"TRIGGER_LABEL" => Array(
			"NAME" => GetMessage("PBMFP_TRIGGER_LABEL"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_TRIGGER_LABEL_TEXT"),
			"PARENT" => "BASE",
		),
		"EMAIL_TO" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO"),
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")),
			"PARENT" => "BASE",
		),
		"EMAIL_TO_IBLOCK" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"PARENT" => "BASE",
			"REFRESH" => "Y",
		),
		"EMAIL_TO_IBLOCK_PROPERTY" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty_S,
			"PARENT" => "BASE",
		),
		"EMAIL_TO_IBLOCK_ADD_PROPERTY" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_ADD_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty_S,
			"PARENT" => "BASE",
		),
		"EMAIL_TO_IBLOCK_ADD1_PROPERTY" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_ADD_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty_S,
			"PARENT" => "BASE",
		),
		"EMAIL_TO_IBLOCK_ADD_IGNORE_PROPERTY" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_ADD_IGNORE_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty_S,
			"PARENT" => "BASE",
		),
		"EMAIL_TO_IBLOCK_CACHE_TIME" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_CACHE_TIME"),
			"TYPE" => "STRING",
			"DEFAULT" => 3600000,
			"PARENT" => "BASE",
		),
		"EMAIL_TO_IBLOCK_LABEL" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_LABEL"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_EMAIL_TO_IBLOCK_LABEL_VAL"),
			"PARENT" => "BASE",
		),
		"CONTACT_FIELDS_LABEL" => Array(
			"NAME" => GetMessage("PBMFP_CONTACT_FIELDS_LABEL"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("PBMFP_CONTACT_FIELDS_LABEL_VAL"),
			"PARENT" => "BASE",
		),
		"ENABLED_FIELDS" => Array(
			"NAME" => GetMessage("PBMFP_ENABLED_FIELDS"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES" => Array("NONE" => GetMessage("PBMFP_ALL_REQ"), "form_client_name" => GetMessage("PBMFP_NAME"), "form_email" => GetMessage("PBMFP_E_MAIL"), "form_client_phone" => GetMessage("PBMFP_PHONE"), "form_comment" => GetMessage("PBMFP_MESSAGE")),
			"DEFAULT"=>"",
			"COLS"=>25,
			"PARENT" => "BASE",
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("PBMFP_REQUIRED_FIELDS"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES" => Array("NONE" => GetMessage("PBMFP_ALL_REQ"), "form_client_name" => GetMessage("PBMFP_NAME"), "form_email" => GetMessage("PBMFP_E_MAIL"), "form_client_phone" => GetMessage("PBMFP_PHONE"), "form_comment" => GetMessage("PBMFP_MESSAGE")),
			"DEFAULT"=>"",
			"COLS"=>25,
			"PARENT" => "BASE",
		),
		"EVENT_ET_MESSAGE_ID" => Array(
			"NAME" => GetMessage("PBMFP_ET_EMAIL_TEMPLATES"),
			"TYPE"=>"LIST",
			"VALUES" => $arETEvent,
			"MULTIPLE"=>"N",
			"DEFAULT"=>"PB_BACKCALL_FORM_EVENT",
			"COLS"=>25,
			"PARENT" => "BASE",
			"REFRESH" => "Y",
		),
		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("PBMFP_EMAIL_TEMPLATES"),
			"TYPE"=>"LIST",
			"VALUES" => $arEvent,
			"DEFAULT"=>COption::GetOptionString('pixelb.backcall','PIXELB_BACKCALL_MODULE_INSTALL_MSG_ID'),
			"MULTIPLE"=>"N",
			"COLS"=>25,
			"PARENT" => "BASE",
		),
		"MESSAGE_MAX_STRLEN" => Array(
			"NAME" => GetMessage("PBMFP_MAX_STRLEN"),
			"TYPE" => "STRING",
			"DEFAULT" => 300,
			"PARENT" => "BASE",
		),
		"FORM_ID" => Array(
			"NAME" => GetMessage("PBMFP_FORM_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => mktime(),
			"PARENT" => "BASE",
		),
		"SHOW_FORM_RULES" => Array(
			"NAME" => GetMessage("PBMFP_SHOW_FORM_RULES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE",
		),
		"FORM_RULES_ADDRESS" => Array(
			"NAME" => GetMessage("PBMFP_FORM_RULES_ADDRESS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "BASE",
		),
	)
);
?>
