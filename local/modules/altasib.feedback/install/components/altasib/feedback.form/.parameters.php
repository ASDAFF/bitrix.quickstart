<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$bNewVers = ((defined("SM_VERSION") && version_compare(SM_VERSION, "15.0.7") >= 0) ? true : false);
if($_REQUEST['bxsender'] != 'fileman_html_editor' && (!$bNewVers || $_REQUEST["edit_file"] == "template")):?>
<div style="background-color:#fff;padding:0;border-top:1px solid #8E8E8E;border-bottom:1px solid #8E8E8E;margin-bottom:15px;"><div style="background-color:#8E8E8E;height:30px;padding:7px;border:1px solid #fff">
	<a href="http://www.is-market.ru?param=cl" target="_blank"><img src="/bitrix/components/altasib/feedback.form/images/is-market.gif" style="float:left;margin-right:15px;" border="0" /></a>
	<div style="margin:13px 0px 0px 0px">
		<a href="http://www.is-market.ru?param=cl" target="_blank" style="color:#fff;font-size:10px;text-decoration:none"><?=GetMessage("ALTASIB_IS")?></a>
	</div>
</div></div>
<?endif;

$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while($arr=$rsIBlockType->Fetch())
{
	if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
		$arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
}

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsIBlock = CIBlock::GetList(Array(), Array("CODE" => "altasib_feedback"));
if($arr=$rsIBlock->Fetch())
	$defaultIBid = $arr["ID"];

if(empty($arCurrentValues["IBLOCK_ID"]) && !empty($defaultIBid))
	$arCurrentValues["IBLOCK_ID"] = $defaultIBid;

$arProperty_LNS = array();
$arPropAuto = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

	if($arr["CODE"] != "USERIP")
	{
		if(!in_array($arr["PROPERTY_TYPE"], array("F", "E", "L"))
			&& $arr["USER_TYPE"] != "DateTime"
		)
			$arPropAuto[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"] != "F" && $arr["PROPERTY_TYPE"] != "L")
		{
			$arPropForNameEl[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S", "F")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}
$arProperty["FEEDBACK_TEXT"] = GetMessage("PROP_FEEDBACK_TEXT");
$arProperty_LNS["FEEDBACK_TEXT"] = GetMessage("PROP_FEEDBACK_TEXT");

$arFBText = array(
	"PREVIEW_TEXT" => GetMessage("F_FB_TEXT_SOURCE_PREVIEW"),
	"DETAIL_TEXT" => GetMessage("F_FB_TEXT_SOURCE_DETAIL"),
);

$dEmailTo = COption::GetOptionString("main", "email_from");
$arSectionIB["SECTION_MAIL_ALL"] = Array(
	"PARENT" => "SECTION_MAIL",
	"NAME" => GetMessage("SECTION_MAIL_ALL"),
	"TYPE" => "STRING",
	"DEFAULT" => $dEmailTo
);

$arSectionFields = array();

$rsIBlock = CIBlockSection::GetList(
	Array("sort" => "asc", "name" => "asc"),
	Array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])
	)
);
while($arr = $rsIBlock->Fetch())
{
	$arSectionIB["SECTION_MAIL".$arr["ID"]] = Array(
		"PARENT" => "SECTION_MAIL",
		"NAME" => $arr["NAME"],
		"TYPE" => "STRING",
		"DEFAULT" => ""
	);
	if($arCurrentValues["SECTION_FIELDS_ENABLE"] == "Y")
	{
		$arSectionFields["SECTION_FIELDS".$arr["ID"]] = Array(
			"PARENT" => "SECTION_FIELDS",
			"NAME" => $arr["NAME"],
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"DEFAULT" => array("FEEDBACK_TEXT"),
		);
	}
}

if(is_array($arPropForNameEl))
	$arProperty_nameEl = array_merge(Array("ALX_DATE" => GetMessage("CURRENT_DATE"), "ALX_TEXT" => GetMessage("TEXT_MESS")),$arPropForNameEl);
else
	$arProperty_nameEl =Array("ALX_DATE" => GetMessage("CURRENT_DATE"), "ALX_TEXT" => GetMessage("TEXT_MESS"));


$arComponentParameters = array(
	"GROUPS" => array(
		"POPUP" => array(
			"NAME" => GetMessage("SECTION_POPUP"),
			"SORT" => "260",
		),
		"SECTION_FIELDS" => array(
			"NAME" => GetMessage("F_SECTION_FIELDS"),
			"SORT" => "270",
		),
		"SECTION_AUTOCOMPLETE" => array(
			"NAME" => GetMessage("SECTION_AUTOCOMPLETE"),
			"SORT" => "280",
		),
		"SECTION_MAIL" => array(
			"NAME" => GetMessage("SECTION_MAIL"),
			"SORT" => "290",
		),
	),
	"PARAMETERS" => array(
		"USER_CONSENT" => array(),
		"USER_CONSENT_INPUT_LABEL" => Array(
			"PARENT" => "USER_CONSENT",
			"NAME" => GetMessage("USER_CONSENT_INPUT_LABEL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),		
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
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("F_PROPERTY_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"DEFAULT" => array("FIO","EMAIL","FEEDBACK_TEXT"),
		),
		"PROPERTY_FIELDS_REQUIRED" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("F_PROPERTY_FIELDS_REQ"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"DEFAULT" => array("FEEDBACK_TEXT"),
		),
		"FB_TEXT_SOURCE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("F_FB_TEXT_SOURCE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arFBText,
			"DEFAULT" => array("PREVIEW_TEXT"),
		),
		"FB_TEXT_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("F_FB_TEXT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
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
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("F_BBC_MAIL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"MESSAGE_OK" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MESS_OK"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MESSAGE_OK"),
			"COLS" => 50,
		),

		"SHOW_LINK_TO_SEND_MORE" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_LINK_TO_SEND_MORE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),

		"LINK_SEND_MORE_TEXT" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("LINK_SEND_MORE_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("LINK_SEND_MORE_TEXT"),
			"COLS" => 50,
		),

		"CHECK_ERROR" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CHECK_ERROR"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ACTIVE_ELEMENT" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("F_ACTIVE_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"USERMAIL_FROM" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("USERMAIL_FROM"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"SHOW_MESSAGE_LINK" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_MESSAGE_LINK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"SEND_MAIL" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SEND_MAIL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
	/*	"AGREEMENT" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("AFBF_AGREEMENT_ON"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),*/
		"ALX_LINK_POPUP" => Array(
			"PARENT" => "POPUP",
			"NAME" => GetMessage("ALX_CHECKBOX_NAME_LINK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"INPUT_APPEARENCE" => Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ALX_INPUT_APPEARENCE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => Array(
				'DEFAULT'=>GetMessage('INPUT_APPEARENCE_DEFAULT'),
				'FLOATING_LABELS'=>GetMessage('INPUT_APPEARENCE_WITH_FLOATING'),
				'FORM_INPUTS_LINE'=>GetMessage('INPUT_APPEARENCE_WITHOUT_BORDERS')
			),
			"DEFAULT" => "DEFAULT"
		),
		"WIDTH_FORM" => Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("APPEARANCE_WIDTH_FORM"),
			"TYPE" => "STRING",
			"DEFAULT" => "50%"
		),
		"CATEGORY_SELECT_NAME" => Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("APPEARANCE_CATEGORY_SELECT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("APPEARANCE_CATEGORY_SELECT_NAME_DEF")
		),
		"CHECKBOX_TYPE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CHECKBOX_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => Array('CHECKBOX'=>GetMessage('CHECKBOX_TYPE_CHECK'),'TOGGLE'=>GetMessage('CHECKBOX_TYPE_TOGGLE')),
			"DEFAULT" => "CHECKBOX",
		),

		"COLOR_SCHEME" => Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("F_COLOR_SCHEME"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"REFRESH" => "Y",
			"VALUES" => Array(
				'BRIGHT' => GetMessage('F_SCHEME_BRIGHT'),
				'PALE' => GetMessage('F_SCHEME_PALE'),
			),
			"DEFAULT" => "BRIGHT"
		),

		"SECTION_FIELDS_ENABLE" => array(
			"PARENT" => "SECTION_FIELDS",
			"NAME" => GetMessage("F_SECTION_FIELDS_ENABLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"PROPS_AUTOCOMPLETE_NAME" => array(
			"PARENT" => "SECTION_AUTOCOMPLETE",
			"NAME" => GetMessage("AUTOCOMPLETE_AUTHOR_NAME"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPropAuto,
			"DEFAULT" => "FIO",
		),
		"PROPS_AUTOCOMPLETE_EMAIL" => array(
			"PARENT" => "SECTION_AUTOCOMPLETE",
			"NAME" => GetMessage("AUTOCOMPLETE_AUTHOR_EMAIL"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPropAuto,
			"DEFAULT" => "EMAIL",
		),
		"PROPS_AUTOCOMPLETE_PERSONAL_PHONE" => array(
			"PARENT" => "SECTION_AUTOCOMPLETE",
			"NAME" => GetMessage("AUTOCOMPLETE_AUTHOR_PHONE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPropAuto,
			"DEFAULT" => "PHONE",
		),
		"PROPS_AUTOCOMPLETE_VETO" => array(
			"PARENT" => "SECTION_AUTOCOMPLETE",
			"NAME" => GetMessage("MASKED_INPUT_AUTOCOMPLETE_VETO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"MASKED_INPUT_PHONE" => array(
			"PARENT" => "SECTION_AUTOCOMPLETE",
			"NAME" => GetMessage("MASKED_INPUT_PHONE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPropAuto,
			"DEFAULT" => "PHONE",
		),
	)
);

if($arCurrentValues["COLOR_SCHEME"] == "PALE")
{
	$arColors = array(
		"" => GetMessage("F_OTHER"),
		"c1" => GetMessage("F_SCHEME_GB"),
		"c2" => GetMessage("F_SCHEME_GG"),
		"c3" => GetMessage("F_SCHEME_R"),
		"c4" => GetMessage("F_SCHEME_G"),
		"c5" => GetMessage("F_SCHEME_P"),
		"c6" => GetMessage("F_SCHEME_O"),
		"c7" => GetMessage("F_SCHEME_S"),
		"c8" => GetMessage("F_SCHEME_SS"),
	);
}
elseif($arCurrentValues["COLOR_SCHEME"] == "BRIGHT")
{
	$arColors = array(
		"" => GetMessage("F_OTHER"),
		"c1" => GetMessage("F_SCHEME_GB_L"),
		"c2" => GetMessage("F_SCHEME_GG_L"),
		"c3" => GetMessage("F_SCHEME_R_L"),
		"c4" => GetMessage("F_SCHEME_G_L"),
		"c5" => GetMessage("F_SCHEME_P_L"),
		"c6" => GetMessage("F_SCHEME_O_L"),
		"c7" => GetMessage("F_SCHEME_S_L"),
		"c8" => GetMessage("F_SCHEME_SS_L"),
	);
}
if(!empty($arColors))
{
	$arComponentParameters["PARAMETERS"]["COLOR_THEME"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("COLOR_SCHEME"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"VALUES" => $arColors,
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "Y",
	);
}

if($arCurrentValues["SEND_MAIL"] == "Y")
{
	$arComponentParameters["PARAMETERS"]["USER_EVENT"] = array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("USER_EVENT"),
		"TYPE" => "STRING",
		"DEFAULT" => "ALX_FEEDBACK_FORM_SEND_MAIL",
	);
}

if(isset($arCurrentValues["COLOR_THEME"]) && $arCurrentValues["COLOR_THEME"] == "")
{
	$arComponentParameters["PARAMETERS"]["COLOR_OTHER"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("COLOR_OTHER"),
		"TYPE" => "COLORPICKER",
		"SHOW_BUTTON" => "Y",
		"DEFAULT" => "#009688"
	);
}

if($arCurrentValues["ALX_LINK_POPUP"] == "Y")
{
	$arComponentParameters["PARAMETERS"]["POPUP_ANIMATION"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("ALX_POPUP_ANIMATION"),
		"TYPE" => "LIST",
		"VALUES" => Array(
			GetMessage('ALX_POPUP_ANIMATION_DEF'),
			GetMessage('ALX_POPUP_ANIMATION1'),
			GetMessage('ALX_POPUP_ANIMATION2'),
			GetMessage('ALX_POPUP_ANIMATION3'),
			GetMessage('ALX_POPUP_ANIMATION4')
		),
		"DEFAULT" => "0"
	);
	$arComponentParameters["PARAMETERS"]["ALX_NAME_LINK"] = array(
		"PARENT" => "POPUP",
		"NAME" => GetMessage("ALX_NAME_LINK"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("ALX_NAME_LINK_DEFAULT"),
		"COLS" => 50,
	);
	$arComponentParameters["PARAMETERS"]["ALX_LOAD_PAGE"] = array(
		"PARENT" => "POPUP",
		"NAME" => GetMessage("ALX_LOAD_PAGE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["WIDTH_FORM"]["DEFAULT"] = "100%";

	if($arCurrentValues["ALX_LOAD_PAGE"] == "Y")
	{
		$arComponentParameters["PARAMETERS"]["POPUP_DELAY"] = array(
			"PARENT" => "POPUP",
			"NAME" => GetMessage("F_POPUP_DELAY"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
			"COLS" => 50,
		);
	}
}


$arComponentParameters["PARAMETERS"]["USE_CAPTCHA"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("F_USE_CAPTCHA"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y",
);

if($arCurrentValues["USE_CAPTCHA"] == "Y")
{
	$arCaptcha = array("default" => GetMessage("F_CAPTCHA_BITRIX"), "recaptcha" => GetMessage("F_CAPTCHA_GOOGLE"));
	$arComponentParameters["PARAMETERS"]["CAPTCHA_TYPE"] = array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("F_CAPTCHA_TYPE"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"MULTIPLE" => "N",
		"REFRESH" => "Y",
		"VALUES" => $arCaptcha,
		"DEFAULT" => "default",
	);

	$arComponentParameters["PARAMETERS"]["NOT_CAPTCHA_AUTH"] = array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("F_NOT_CAPTCHA_AUTH"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);

	if($arCurrentValues["CAPTCHA_TYPE"]=="recaptcha")
	{
		$arReCThemes = array("dark" => GetMessage("RECAPTCHA_THEME_DARK"), "light" => GetMessage("RECAPTCHA_THEME_LIGHT"));
		$arComponentParameters["PARAMETERS"]["RECAPTCHA_THEME"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("F_RECAPTCHA_THEME"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"MULTIPLE" => "N",
			"VALUES" => $arReCThemes,
			"DEFAULT" => "light",
		);
		$arReCTypes = array("audio" => GetMessage("RECAPTCHA_TYPE_AUDIO"), "image" => GetMessage("RECAPTCHA_TYPE_IMAGE"));
		$arComponentParameters["PARAMETERS"]["RECAPTCHA_TYPE"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("F_RECAPTCHA_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"MULTIPLE" => "N",
			"VALUES" => $arReCTypes,
			"DEFAULT" => "image",
		);
	}
	else
	{
		$arComponentParameters["PARAMETERS"]["CHANGE_CAPTCHA"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("F_CHANGE_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"REFRESH" => "N",
			"DEFAULT" => "N",
		);
	}
}

$arComponentParameters["PARAMETERS"]["JQUERY_EN"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("FB_ADD_JQUERY"),
	"TYPE" => "LIST",
	"ADDITIONAL_VALUES" => "N",
	"MULTIPLE" => "N",
	"REFRESH" => "N",
	"VALUES" => array("jquery" => GetMessage("FB_ADD_JQUERY_YES"), "jquery2" => GetMessage("FB_ADD_JQUERY_JQUERY2"), "N" => GetMessage("FB_ADD_JQUERY_NO")),
	"DEFAULT" => "Y",
);


$arComponentParameters["PARAMETERS"]["SEND_IMMEDIATE"] = array(
	"PARENT" => "ADDITIONAL_SETTINGS",
	"NAME" => GetMessage("SEND_EMAIL_IMMEDIATE"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y",
);

if($arCurrentValues["SEND_IMMEDIATE"] == "N" && defined("SM_VERSION") && version_compare(SM_VERSION, "15.0.15") >= 0)
{
	$arComponentParameters["PARAMETERS"]["ADD_EVENT_FILES"] = array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("ADD_EVENT_FILES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	);
}

$arComponentParameters["PARAMETERS"]["LOCAL_REDIRECT_ENABLE"] = array(
	"PARENT" => "ADDITIONAL_SETTINGS",
	"NAME" => GetMessage("LOCAL_REDIRECT_ENABLE"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "Y",
);

if($arCurrentValues["LOCAL_REDIRECT_ENABLE"] == "Y")
{
	$arComponentParameters["PARAMETERS"]["LOCAL_REDIRECT_URL"] = array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("LOCAL_REDIRECT_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"COLS" => 50,
	);
}

$arComponentParameters["PARAMETERS"]["ADD_HREF_LINK"] = array(
	"PARENT" => "ADDITIONAL_SETTINGS",
	"NAME" => GetMessage("ADD_EVENT_ADD_HREF_LINK"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);

if($arCurrentValues["SECTION_FIELDS_ENABLE"] == "Y")
{
	if(!empty($arSectionFields))
	{
		foreach($arSectionFields as $k => $v)
		{
			$arComponentParameters["PARAMETERS"][$k] = $v;
		}
	}
}

foreach($arSectionIB as $k => $v)
{
	$arComponentParameters["PARAMETERS"][$k] = $v;
}

BXClearCache(true, "/altasib/feedback");
?>