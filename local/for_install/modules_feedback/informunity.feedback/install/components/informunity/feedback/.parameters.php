<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$lang_par = GetMessage("MFP_LANG");
$arEmail = array(htmlspecialchars(COption::GetOptionString("main", "email_from")) => htmlspecialchars(COption::GetOptionString("main", "email_from")));
$arBCC = explode(',',COption::GetOptionString("main", "all_bcc"));
foreach($arBCC as $e_bcc) {
	if(strlen($e_bcc) > 0)
		$arEmail[$e_bcc] = $e_bcc;
}
$arComponentParameters = array(
	"GROUPS" => array(
		"SET_SEND" => array(
			"NAME" => GetMessage("MFP_SET_SEND"),
		),
		"SET_FORM" => array(
			"NAME" => GetMessage("MFP_SET_FORM"),
		),
	),
	"PARAMETERS" => array(
		"USE_CAPTCHA" => array(
			"NAME"    => GetMessage("MFP_CAPTCHA"), 
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT"  => "BASE",
		),
		"OK_TEXT" => array(
			"NAME"    => GetMessage("MFP_OK_MESSAGE"), 
			"TYPE"    => "STRING",
			"DEFAULT" => GetMessage("MFP_OK_TEXT"), 
			"PARENT"  => "BASE",
			"COLS"     => 50,
		),
		"USE_IU_PAT" => array(
			"NAME"    => GetMessage("MFP_IU_PAT"), 
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT"  => "BASE",
			"REFRESH" => "Y",
		),
		"USE_IU_IB" => array(
			"NAME"    => GetMessage("MFP_USE_IU_IB"), 
			"TYPE"    => "CHECKBOX",
			"DEFAULT" => "N", 
			"PARENT"  => "BASE",
			"REFRESH" => "Y",
		),
		"EMAIL_TO" => array(
			"NAME"     => GetMessage("MFP_EMAIL_TO"), 
			"TYPE"     => "LIST",
			"MULTIPLE" => "Y", 
			"PARENT"   => "SET_SEND",
			"REFRESH"  => "N",
			"VALUES"   => $arEmail,
			"DEFAULT"  => array(htmlspecialchars(COption::GetOptionString("main", "email_from"))),
			"SIZE"     => (count($arEmail)+1),
			"COLS"     => 30,
			"ADDITIONAL_VALUES" => "Y",
		)
	)
);
$arComponentParameters["PARAMETERS"]["EXT_FIELDS"] = array(
	"NAME"     => GetMessage("MFP_EXT_FIELDS"),
	"TYPE"     => "LIST",
	"MULTIPLE" => "Y", 
	"PARENT"   => "SET_SEND",
	"REFRESH"  => "Y",
	"VALUES"   => array(
		"iu_0" => GetMessage("MFP_NAME"),
		"iu_1" => "E-mail",
		"iu_2" => GetMessage("MFP_MESS"),
	),
	"DEFAULT"  => array("iu_0","iu_1","iu_2"),
	"SIZE"     => 4,
	"COLS"     => 30,
	"ADDITIONAL_VALUES" => "Y",
);
$arReq = array();
$arReqn = array("iu_none" => GetMessage("MFP_NONE"));
$ar_textar = array();
if ($arCurrentValues["USE_IU_PAT"] == "N") {
	unset($arComponentParameters["PARAMETERS"]["EMAIL_TO"]);
	unset($arComponentParameters["PARAMETERS"]["EXT_FIELDS"]);
	$arEType = array();
	$dbType = CEventType::GetList(array("LID" => $lang_par));
	while($arType = $dbType->GetNext())
		if($arType["EVENT_NAME"] != "IU_FEEDBACK_FORM")
			$arEType[$arType["EVENT_NAME"]] = (strlen($arType["NAME"])<50)?$arType["NAME"]:(mb_substr($arType["NAME"],0,48,'utf8').'..');
	$arComponentParameters["PARAMETERS"]["EVENT_TYPE_ID"] = array(
		"NAME"     => GetMessage("MFP_TYPE_ID"),
		"TYPE"     => "LIST", 
		"VALUES"   => $arEType,
		"DEFAULT"  => "FEEDBACK_FORM",
		"MULTIPLE" => "N", 
		"PARENT"   => "SET_SEND",
		"COLS"     => 25,
		"REFRESH"  => "Y",
	);
	$arEvent = array();
	if ($arCurrentValues["EVENT_TYPE_ID"] != '') {
		$rsET = CEventType::GetByID($arCurrentValues["EVENT_TYPE_ID"],$lang_par);
		$arET = $rsET->Fetch();
		if (preg_match_all('/#(\w+)#\s-\s(.+)/i',$arET["DESCRIPTION"],$matches)) 
			for($pp=0; $pp < count($matches[1]); $pp++) {
				$arReq[$matches[1][$pp]] = $matches[2][$pp];
				$arReqn[$matches[1][$pp]] = $matches[2][$pp];
				if($matches[1][$pp] !== 'iu_2') $ar_textar[$matches[1][$pp]] = $matches[2][$pp];
			}
		$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
		$arFilter = array("TYPE_ID" => $arCurrentValues["EVENT_TYPE_ID"], "ACTIVE" => "Y");
		if($site !== false) $arFilter["LID"] = $site;
		$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
		while($arType = $dbType->GetNext())
			$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".((strlen($arType["SUBJECT"])<50)?$arType["SUBJECT"]:(mb_substr($arType["SUBJECT"],0,48,'utf8').'..'));
	}
	$arComponentParameters["PARAMETERS"]["EVENT_MESSAGE_ID"] = array(
		"NAME"     => GetMessage("MFP_EMAIL_TEMPLATES"), 
		"TYPE"     => "LIST", 
		"VALUES"   => $arEvent,
		"DEFAULT"  => "",
		"MULTIPLE" => "N", 
		"PARENT"   => "SET_SEND",
		"COLS"     => 25,
	);
} else {
	if($arCurrentValues["USE_ATTACH"] && $arCurrentValues["USE_ATTACH"] == "Y") $ar_attach = array();
	if ($arCurrentValues["EXT_FIELDS"]) foreach ($arCurrentValues["EXT_FIELDS"] as $ext_name) {
		if ($ext_name != '') {
			$ext_val = preg_match('/^iu_[0-2]$/',$ext_name)?$arComponentParameters["PARAMETERS"]["EXT_FIELDS"]["VALUES"][$ext_name]:$ext_name;
			$arReq[$ext_name] = $ext_val;
			$arReqn[$ext_name] = $ext_val;
			if($ext_name !== 'iu_2') $ar_textar[$ext_name] = $ext_val;
			if($arCurrentValues["USE_ATTACH"] && $arCurrentValues["USE_ATTACH"] == "Y" &&
			$ext_name !== $arCurrentValues["FIELD_FOR_THEME"] &&
			$ext_name !== $arCurrentValues["FIELD_FOR_NAME"] &&
			$ext_name !== $arCurrentValues["FIELD_FOR_EMAIL"])
				$ar_attach[$ext_name] = $ext_val;
		}
	}
	$arComponentParameters["PARAMETERS"]["FIELD_FOR_THEME"] = array(
		"NAME"     => GetMessage("MFP_FIELD_FOR_THEME"),
		"TYPE"     => "LIST",
		"VALUES"   => $arReqn,
		"DEFAULT"  => "iu_none", 
		"PARENT"   => "SET_SEND",
		"REFRESH"  => "Y",
	);
	$arComponentParameters["PARAMETERS"]["EM_THEME"] = array(
		"NAME"    => GetMessage("MFP_EM_THEME"),
		"TYPE"    => "STRING",
		"DEFAULT" => GetMessage("MFP_EM_THEME_DEF"), 
		"PARENT"  => "SET_SEND",
		"COLS"    => 45,
	);
	$arComponentParameters["PARAMETERS"]["AFTER_TEXT"] = array(
		"NAME"    => GetMessage("MFP_AFTER_TEXT"), 
		"TYPE"    => "STRING", 
		"DEFAULT" => "", 
		"COLS"    => 45, 
		"PARENT"  => "SET_SEND",
	);
	$arComponentParameters["PARAMETERS"]["USE_EMAIL_USER"] = array(
		"NAME"    => GetMessage("MFP_USE_EMAIL_USER"), 
		"TYPE"    => "CHECKBOX", 
		"DEFAULT" => "N", 
		"PARENT"  => "SET_SEND",
	);
	$arComponentParameters["PARAMETERS"]["USE_ATTACH"] = array(
		"NAME"    => GetMessage("MFP_USE_ATTACH"), 
		"TYPE"    => "CHECKBOX", 
		"DEFAULT" => "N", 
		"PARENT"  => "BASE",
		"REFRESH"  => "Y",
	);
}
$arComponentParameters["PARAMETERS"]["REQUIRED_FIELDS"] = array(
	"NAME"     => GetMessage("MFP_REQUIRED_FIELDS"), 
	"TYPE"     => "LIST", 
	"MULTIPLE" => "Y", 
	"VALUES"   => $arReq,
	"DEFAULT"  => "", 
	"COLS"     => 20, 
	"SIZE"     => count($arReq),
	"PARENT"   => "SET_FORM",
);
$arComponentParameters["PARAMETERS"]["TEXTAREA_FIELDS"] = array(
	"NAME"     => GetMessage("MFP_TEXTAREA_FIELDS"), 
	"TYPE"     => "LIST", 
	"MULTIPLE" => "Y", 
	"VALUES"   => $ar_textar,
	"DEFAULT"  => "", 
	"COLS"     => 20, 
	"SIZE"     => count($ar_textar),
	"PARENT"   => "SET_FORM",
);
$arComponentParameters["PARAMETERS"]["FIELD_FOR_NAME"] = array(
	"NAME"     => GetMessage("MFP_FIELD_FOR_NAME"),
	"TYPE"     => "LIST",
	"VALUES"   => $arReqn,
	"DEFAULT"  => "iu_none", 
	"PARENT"   => "SET_FORM",
	"REFRESH"  => "Y",
);
$arComponentParameters["PARAMETERS"]["FIELD_FOR_EMAIL"] = array(
	"NAME"     => GetMessage("MFP_FIELD_FOR_EMAIL"),
	"TYPE"     => "LIST",
	"VALUES"   => $arReqn,
	"DEFAULT"  => "iu_none", 
	"PARENT"   => "SET_FORM",
	"REFRESH"  => "Y",
);
if($arCurrentValues["USE_IU_PAT"] !== "N") {
	$arComponentParameters["PARAMETERS"]["COPY_LETTER"] = array(
		"NAME"     => GetMessage("MFP_COPY_LETTER"), 
		"TYPE"     => "CHECKBOX", 
		"DEFAULT"  => "N",
		"PARENT"   => "SET_FORM",
	);
}
if ($arCurrentValues["USE_IU_IB"] == "Y") {
	$arComponentParameters["GROUPS"]["SET_IB"] = array("NAME" => GetMessage("MFP_SET_IB"));
	$arComponentParameters["PARAMETERS"]["USE_IU_IBC"] = array(
		"NAME"     => GetMessage("MFP_USE_IU_IBC"), 
		"TYPE"     => "CHECKBOX", 
		"DEFAULT"  => "Y",
		"PARENT"   => "SET_IB",
		"REFRESH"  => "Y",
	);
	if ($arCurrentValues["USE_IU_IBC"] == "N") {
		CModule::IncludeModule("iblock");
		$arTypesEx = Array();
		$db_iblock_type = CIBlockType::GetList(Array("SORT"=>"ASC"));
		while($arRes = $db_iblock_type->Fetch())
			if($arIBType = CIBlockType::GetByIDLang($arRes["ID"], $lang_par))
				$arTypesEx[$arRes["ID"]] = $arIBType["NAME"];
		$arIBlocks = Array();
		if ($arCurrentValues["IB_TYPE"] != '') {
			$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IB_TYPE"]!="-"?$arCurrentValues["IB_TYPE"]:"")));
			while($arRes = $db_iblock->Fetch())
				$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
		}
		$arComponentParameters["PARAMETERS"]["IB_TYPE"] = array(
			"NAME"     => GetMessage("MFP_TYPE"), 
			"TYPE"     => "LIST",
			"MULTIPLE" => "N",
			"PARENT"   => "SET_IB",
			"VALUES"   => $arTypesEx,
			"DEFAULT"  => '',
			"REFRESH"  => "Y",
		);
		$arComponentParameters["PARAMETERS"]["IB_IB"] = array(
			"NAME"     => GetMessage("MFP_IB"), 
			"TYPE"     => "LIST",
			"MULTIPLE" => "N",
			"PARENT"   => "SET_IB",
			"VALUES"   => $arIBlocks,
			"DEFAULT"  => '',
		);
	} else {
		$arComponentParameters["PARAMETERS"]["IBLOCK_NAME"] = array(
			"NAME"     => GetMessage("MFP_IBLOCK_NAME"), 
			"TYPE"     => "STRING", 
			"DEFAULT"  => GetMessage("MFP_IBLOCK_NAME_DEF"), 
			"COLS"     => 45, 
			"PARENT"   => "SET_IB",
		);
	}
	$arComponentParameters["PARAMETERS"]["IB_ACT"] = array(
		"NAME"     => GetMessage("MFP_ACT"),
		"TYPE"     => "CHECKBOX",
		"DEFAULT"  => "N",
		"PARENT"   => "SET_IB",
	);
	$arComponentParameters["PARAMETERS"]["IBE_NAME"] = array(
		"NAME"     => GetMessage("MFP_IBE_NAME"),
		"TYPE"     => "LIST",
		"MULTIPLE" => "N",
		"PARENT"   => "SET_IB",
		"VALUES"   => $arReqn,
		"DEFAULT"  => "iu_none",
	);
	$arComponentParameters["PARAMETERS"]["IB_DET"] = array(
		"NAME"     => GetMessage("MFP_DET"),
		"TYPE"     => "LIST",
		"MULTIPLE" => "N",
		"PARENT"   => "SET_IB",
		"VALUES"   => $arReqn,
		"DEFAULT"  => "iu_none",
	);
	$arComponentParameters["PARAMETERS"]["IB_ANONS"] = array(
		"NAME"     => GetMessage("MFP_ANONS"),
		"TYPE"     => "LIST",
		"MULTIPLE" => "N",
		"PARENT"   => "SET_IB",
		"VALUES"   => $arReqn,
		"DEFAULT"  => "iu_none",
	);
	$arComponentParameters["PARAMETERS"]["IB_PARAM"] = array(
		"NAME"     => GetMessage("MFP_PARAM"),
		"TYPE"     => "CHECKBOX",
		"DEFAULT"  => "Y",
		"PARENT"   => "SET_IB",
	);
	$arComponentParameters["PARAMETERS"]["WRIT_A"] = array(
		"NAME"     => GetMessage("MFP_WRIT_A"),
		"TYPE"     => "CHECKBOX",
		"DEFAULT"  => "Y",
		"PARENT"   => "SET_IB",
	);
}
if ($arCurrentValues["USE_IU_PAT"] == "Y" && $arCurrentValues["USE_ATTACH"] && $arCurrentValues["USE_ATTACH"] == "Y") {
	$arComponentParameters["GROUPS"]["SET_ATTACH"] = array("NAME" => GetMessage("MFP_SET_ATTACH"));
	$arComponentParameters["PARAMETERS"]["FILE_FIELDS"] = array(
		"NAME"     => GetMessage("MFP_FILE_FIELDS"), 
		"TYPE"     => "LIST", 
		"MULTIPLE" => "Y", 
		"VALUES"   => $ar_attach,
		"DEFAULT"  => "", 
		"COLS"     => 20, 
		"SIZE"     => count($ar_attach),
		"PARENT"   => "SET_ATTACH",
	);
	$arComponentParameters["PARAMETERS"]["FILE_DIR"] = array(
		"PARENT"  => "SET_ATTACH",
		"NAME"    => GetMessage("MFP_FILE_DIR"),
		"TYPE"    => "FILE",
		"DEFAULT" => "/upload/tmp",
		"REFRESH" => "N",
		"FD_TARGET" => "D",
		"FD_UPLOAD" => false,
		"FD_USE_MEDIALIB" => false,
	);
	$arComponentParameters["PARAMETERS"]["SAVE_FILE"] = array(
		"NAME"     => GetMessage("MFP_SAVE_FILE"),
		"TYPE"     => "CHECKBOX",
		"DEFAULT"  => "N",
		"PARENT"   => "SET_ATTACH",
	);
	$arComponentParameters["PARAMETERS"]["MAX_SIZE_FILE"] = array(
		"NAME"     => GetMessage("MFP_MAX_SIZE_FILE"),
		"TYPE"     => "STRING",
		"DEFAULT"  => "1024",
		"PARENT"   => "SET_ATTACH",
		"COLS"     => 10
	);
	$ar_file_format = array(
		"jpg"  => "jpg",
		"bmp"  => "bmp",
		"gif"  => "gif",
		"png"  => "png",
		"zip"  => "zip",
		"pdf"  => "pdf",
		"doc"  => "doc",
		"docx" => "docx",
		"xls"  => "xls",
		"xlsx" => "xlsx",
		"odt"  => "odt",
		"ppt"  => "ppt"
	);
	$arComponentParameters["PARAMETERS"]["FILE_FORMAT"] = array(
		"NAME"     => GetMessage("MFP_FILE_FORMAT"), 
		"TYPE"     => "LIST", 
		"MULTIPLE" => "Y", 
		"VALUES"   => $ar_file_format,
		"DEFAULT"  => "", 
		"COLS"     => 20, 
		"SIZE"     => count($ar_file_format),
		"PARENT"   => "SET_ATTACH",
	);
}
?>