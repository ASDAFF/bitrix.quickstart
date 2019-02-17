<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
        return;

$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while ($arr=$rsIBlockType->Fetch())
{
        if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
                $arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
}

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
        $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_LNS = array();
$arProperty_N = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
        if($arr["CODE"] != "USERIP")
		{
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			if($arr["PROPERTY_TYPE"] == "S" && $arr["USER_TYPE"] != "HTML" && $arr["USER_TYPE"] != "DateTime")
				$arrStringProperties[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}

$arProperty["FEEDBACK_TEXT"] = GetMessage("PROP_FEEDBACK_TEXT");

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
	if($arr["PROPERTY_TYPE"] != "F" && $arr["PROPERTY_TYPE"] != "L" && $arr["CODE"] != "USERIP")
		$arPropForNameEl[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

$dEmailTo = COption::GetOptionString("main", "email_from");
$arSectionIB["SECTION_MAIL_ALL"] = Array(
	        "PARENT" => "SECTION_MAIL",
	        "NAME" => GetMessage("SECTION_MAIL_ALL"),
	        "TYPE" => "STRING",
	        "DEFAULT" => $dEmailTo
        );
		
$rsIBlock = CIBlock::GetList(Array(), Array("CODE" => "altasib_feedback"));
if($arr=$rsIBlock->Fetch())
        $defaultIBid = $arr["ID"];
		
if(empty($arCurrentValues["IBLOCK_ID"]) && !empty($defaultIBid))
	$arCurrentValues["IBLOCK_ID"] = $defaultIBid;

$rsIBlock = CIBlockSection::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while($arr=$rsIBlock->Fetch())
        $arSectionIB["SECTION_MAIL".$arr["ID"]] = Array(
	        "PARENT" => "SECTION_MAIL",
	        "NAME" => $arr["NAME"],
	        "TYPE" => "STRING",
	        "DEFAULT" => ""
        );

$arProperty_nameEl = array_merge(Array("ALX_DATE" => GetMessage("CURRENT_DATE"), "ALX_TEXT" => GetMessage("TEXT_MESS")),$arPropForNameEl);

$arComponentParameters = array(
        "GROUPS" => array(
                "APPEARANCE" => array(
                        "NAME" => GetMessage("APPEARANCE"),
                        "SORT" => "110",
                ),
                "SECTION_MAIL" => array(
						"NAME" => GetMessage("SECTION_MAIL"),
                        "SORT" => "120",
                ),
        ),
        "PARAMETERS" => array(
                "AJAX_MODE"	=> Array(),
                "IBLOCK_TYPE" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_IBLOCK_TYPE"),
                        "TYPE" => "LIST",
                        "VALUES" => $arIBlockType,
                        "REFRESH" => "Y",
                        "DEFAULT" => "altasib_feedback",
                ),                
                "IBLOCK_ID" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_IBLOCK_ID"),
                        "TYPE" => "LIST",
                        "ADDITIONAL_VALUES" => "Y",
                        "VALUES" => $arIBlock,
                        "REFRESH" => "Y",
                        "DEFAULT" => $defaultIBid,
                ),
                "FORM_ID" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_ID_FORM"),
                        "TYPE" => "STRING",
                        "DEFAULT" => 1
                ),
                "EVENT_TYPE" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_EVENT_TYPE"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "ALX_FEEDBACK_FORM"
                ),
                "PROPERTY_FIELDS" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_PROPERTY_FIELDS"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "Y",
                        "VALUES" => $arProperty,
						"DEFAULT" => array("FIO","EMAIL","FEEDBACK_TEXT"),						
                ),
                "PROPERTY_FIELDS_REQUIRED" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_PROPERTY_FIELDS_REQ"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "Y",
                        "VALUES" => $arProperty,
						"DEFAULT" => array("FEEDBACK_TEXT"),
                ),
                "NAME_ELEMENT" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_NAME_ELEMENT"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "N",
                        "VALUES" => $arProperty_nameEl,
                        "ADDITIONAL_VALUES" => "N",
                        "DEFAULT" => "ALX_DATE",
                ),
                "BBC_MAIL" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_BBC_MAIL"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "",
                ),
                "MESSAGE_OK" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("MESS_OK"),
                        "TYPE" => "STRING",
                        "DEFAULT" => GetMessage("MESSAGE_OK"),
                        "COLS" => 50,
                ),
                "CHECK_ERROR" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("CHECK_ERROR"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y",
                ),
                "ACTIVE_ELEMENT" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_ACTIVE_ELEMENT"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y",
                ),
                "USE_CAPTCHA" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("F_USE_CAPTCHA"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y",
                ),
                "SEND_MAIL" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("SEND_MAIL"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
                ),
                "HIDE_FORM" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("HIDE_FORM"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
                ),
				"USERMAIL_FROM" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("USERMAIL_FROM"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N"
				),                
				"SHOW_MESSAGE_LINK" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("SHOW_MESSAGE_LINK"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y"
				),
                "WIDTH_FORM" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_WIDTH_FORM"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "50%"
                ),
                "SIZE_NAME" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_SIZE_NAME"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "12px"
                ),
                "COLOR_NAME" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_COLOR_NAME"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#000000"
                ),
				"SIZE_HINT" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_SIZE_HINT"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "10px"
				),
                "COLOR_HINT" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_COLOR_HINT"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#000000"
				),                
                "SIZE_INPUT" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_SIZE_INPUT"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "12px"
                ),
                "COLOR_INPUT" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_COLOR_INPUT"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#727272"
                ),
                "BACKCOLOR_ERROR" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_BACKCOLOR_ERROR"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#ffffff"
                ),
                "COLOR_ERROR_TITLE" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_COLOR_ERROR_TITLE"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#A90000"
                ),
                "COLOR_ERROR" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_COLOR_ERROR"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#8E8E8E"
                ),
                "IMG_ERROR" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_IMG_ERROR"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "/upload/altasib.feedback.gif"
                ),
				"BORDER_RADIUS" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_BORDER_RADIUS"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "3px"
                ),
				"COLOR_MESS_OK" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_COLOR_MESS_OK"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "#963258"
                ),
                "IMG_OK" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_IMG_OK"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "/upload/altasib.feedback.ok.gif"
                ),
                "CATEGORY_SELECT_NAME" => Array(
                        "PARENT" => "APPEARANCE",
                        "NAME" => GetMessage("APPEARANCE_CATEGORY_SELECT_NAME"),
                        "TYPE" => "STRING",
                        "DEFAULT" => GetMessage("APPEARANCE_CATEGORY_SELECT_NAME_DEF")
                ),
                "REWIND_FORM" => Array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("REWIND_FORM"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
                ),                
        ),
);

foreach($arSectionIB as $k => $v)
{
	$arComponentParameters["PARAMETERS"][$k] = $v;
}
?>
