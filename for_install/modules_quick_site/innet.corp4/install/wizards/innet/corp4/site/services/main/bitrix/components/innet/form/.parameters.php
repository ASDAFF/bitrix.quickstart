<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "INNET_FORM", "ACTIVE" => "Y");
if($site !== false)
    $arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
    $arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

if (CModule::IncludeModule("iblock")){
    $arIBlocks = Array();
    $db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
    while ($arRes = $db_iblock->Fetch())
        $arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "USE_CAPTCHA" => Array(
            "NAME" => GetMessage("INNET_FORM_USE_CAPTCHA"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "PARENT" => "BASE",
        ),
        "OK_MESSAGE" => Array(
            "NAME" => GetMessage("INNET_FORM_OK_MESSAGE"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
        "EMAIL_TO" => Array(
            "NAME" => GetMessage("INNET_FORM_EMAIL_TO"),
            "TYPE" => "STRING",
            "DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")),
            "PARENT" => "BASE",
        ),
        "REQUIRED_FIELDS" => Array(
            "NAME" => GetMessage("INNET_FORM_REQUIRED_FIELDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => Array("NONE" => GetMessage("INNET_FORM_ALL_OPTIONAL"), "NAME" => GetMessage("INNET_FORM_REQUIRED_NAME"), "EMAIL" => GetMessage("INNET_FORM_REQUIRED_EMAIL"), "PHONE" => GetMessage("INNET_FORM_REQUIRED_PHONE"), "MESSAGE" => GetMessage("INNET_FORM_REQUIRED_MESSAGE")),
            "DEFAULT" => "",
            "COLS" => 25,
            "PARENT" => "BASE",
        ),
        "EVENT_MESSAGE_ID" => Array(
            "NAME" => GetMessage("INNET_FORM_EVENT_MESSAGE_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arEvent,
            "MULTIPLE" => "Y",
            "COLS" => 25,
            "PARENT" => "BASE",
        ),
        "EVENT_MESSAGE_TYPE" => Array(
            "NAME" => GetMessage("INNET_FORM_EVENT_MESSAGE_TYPE"),
            "TYPE" => "STRING",
            "DEFAULT" => "INNET_FORM",
            "PARENT" => "BASE",
        ),
        "EVENT_MESSAGE_TYPE_USER" => Array(
            "NAME" => GetMessage("INNET_FORM_EVENT_MESSAGE_TYPE_USER"),
            "TYPE" => "STRING",
            "DEFAULT" => "EVENT_MESSAGE_TYPE_USER",
            "PARENT" => "BASE",
        ),
        "ELEMENT_NAME" => Array(
            "NAME" => GetMessage("INNET_FORM_ELEMENT_NAME"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
        "ELEMENT_ID" => Array(
            "NAME" => GetMessage("INNET_FORM_ELEMENT_ID"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
		"PRICE_PRODUCT" => Array(
            "NAME" => GetMessage("INNET_FORM_PRICE_PRODUCT"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),		
        "IBLOCK_ID_ORDER" => Array(
            "NAME" => GetMessage("INNET_FORM_IBLOCK_ID_ORDER"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "AJAX_MODE" => array(),
    )
);
?>