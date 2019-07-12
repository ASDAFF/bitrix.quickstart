<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//error_reporting(E_WARNING);
//ini_set("display_errors",1);

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.minilanding") && !CModule::IncludeModule("main")) return;

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "MLIFE_MINILANDING");
//if($site !== false)
	//$arFilter["LID"] = $site;
$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];
	//print_r($arEvent);
//инфоблок
$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));
$arIBlocks = Array("-"=>" ");
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = '['.$arRes["CODE"].'] '.$arRes["NAME"];
	
$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["ID"]."---"] = "[".$arr["ID"]."] [".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["ID"]."---"] = "[".$arr["ID"]."] [".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arComponentParameters = array(
	"GROUPS" => array(
			"FORM" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_FORM"),
            "SORT" => "305",
            ),
			"NOTICE" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_NOTICE"),
            "SORT" => "315",
            ),
			"IBL" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_IBL"),
            "SORT" => "318",
            ),
			"BX24" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_BX24"),
            "SORT" => "319",
            ),
			"SETT" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_SETT"),
            "SORT" => "320",
            ),
	),

	"PARAMETERS" => array(
		"KEY" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_KEY"),
			'TYPE' => 'TEXT',
			'DEFAULT' => '3j5h34kj5h34kj5h3k4j5h'.rand(10,100),
			"PARENT" => "FORM",
			"REFRESH" => "N",
		),
		"FORMID" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FORMID"),
			'TYPE' => 'TEXT',
			'DEFAULT' => '1',
			"PARENT" => "FORM",
			"REFRESH" => "N",
		),
		"FIELD_SHOW" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_SHOW"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => array(
					"name" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_NAME"),
					"phone" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_PHONE"),
					"email" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_EMAIL"),
					"mess" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_MESS")
				),
			"SIZE" => 4
		),
		"FIELD_REQ" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_REQ"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => array(
					"name" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_NAME"),
					"phone" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_PHONE"),
					"email" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_EMAIL"),
					"mess" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_MESS")
				),
			"SIZE" => 4
		),
		"FIELD_SHOW_HIDDEN" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_SHOW_HIDDEN"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => array(
					"addfield1" => GetMessage("MLIFE_CAT_BK_FIELD_SHOW_HIDDEN1"),
					"addfield2" => GetMessage("MLIFE_CAT_BK_FIELD_SHOW_HIDDEN2"),
					"addfield3" => GetMessage("MLIFE_CAT_BK_FIELD_SHOW_HIDDEN3"),
					"addfield4" => GetMessage("MLIFE_CAT_BK_FIELD_SHOW_HIDDEN4")
				),
			"SIZE" => 4
		),
		"NOTICE_ADMIN" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_ADMIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "NOTICE",
		),
		"IBL_ADMIN" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_IBL_ADMIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "IBL",
		),
		"IBL_BX24" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_IBL_BX24"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "BX24",
		),
		"CHECK_HASH" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_CHECK_HASH"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "FORM",
		),
		
	)
);
	
	if($arCurrentValues['NOTICE_ADMIN']=='Y') {
		
		$arComponentParameters['PARAMETERS']["NOTICE_EMAIL"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
					"PARENT" => "NOTICE",
				);
				
		$arComponentParameters['PARAMETERS']["NOTICE_EMAIL_EMAIL"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_EMAIL_EMAIL"),
				"TYPE" => "TEXT",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"PARENT" => "NOTICE",
			);
		$arComponentParameters['PARAMETERS']["EVENTPOST2"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_EVENTPOST2"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "NOTICE",
		);
		
	}
	
	$arSms = array();
	if(CModule::IncludeModule("mlife.smsservices")) $arSms['smsservices'] = 'mlife.smsservices';
	if(CModule::IncludeModule("asd.smsswitcher")) $arSms['smsswitcher'] = 'asd.smsswitcher';
	if(count($arSms)>0){
	
			$arComponentParameters['PARAMETERS']["NOTICE_ADMIN_SMS"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_ADMIN_SMS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "NOTICE",
			);
	
			if($arCurrentValues['NOTICE_ADMIN_SMS']=='Y') {
				
				$arComponentParameters['PARAMETERS']["NOTICE_SMS"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
					"PARENT" => "NOTICE",
				);
				
				$arComponentParameters['PARAMETERS']["NOTICE_SMS_PHONE"] = array(
					'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_SMS_PHONE"),
					"TYPE" => "TEXT",
					"DEFAULT" => "",
					"REFRESH" => "N",
					"PARENT" => "NOTICE",
				);
				
				$arComponentParameters['PARAMETERS']["NOTICE_SMS_MODULE"] = array(
					'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_SMS_MODULE"),
					"TYPE" => "LIST",
					"PARENT" => "NOTICE",
					"VALUES" => $arSms,
					"REFRESH" => "N",
				);
				
			}
	
	}
	
	if($arCurrentValues['IBL_ADMIN']=='Y') {
	
		$arComponentParameters['PARAMETERS']["IBLOCK_TYPE"] = Array(
			"PARENT" => "IBL",
			"NAME" => GetMessage("MLIFE_CAT_BK_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "-",
			"REFRESH" => "Y",
		);
		$arComponentParameters['PARAMETERS']["IBLOCK_ID"] = Array(
			"PARENT" => "IBL",
			"NAME" => GetMessage("MLIFE_CAT_BK_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '-',
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		);
		
		$arComponentParameters['PARAMETERS']["FIELD1_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD1_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD1_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD1_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD1_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD1_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD2_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD2_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD2_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD2_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD2_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD2_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD3_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD3_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD3_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD3_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD3_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD3_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD4_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD4_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD4_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD4_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD4_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD4_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD5_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD5_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD5_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD5_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD5_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD5_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD6_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD6_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD6_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD6_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD6_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD6_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD7_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD7_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD7_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD7_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD7_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD7_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELD8_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD8_CODE"), "IBL");
		$artemp = $arComponentParameters['PARAMETERS']["FIELD8_CODE"]["VALUES"];
		$arComponentParameters['PARAMETERS']["FIELD8_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$artemp,$arProperty_LNS);
		$arComponentParameters['PARAMETERS']["FIELD8_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELD8_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["IBL_ACTIVE"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_IBL_ACTIVE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Т",
			"PARENT" => "IBL",
			);
	
	}
	
	if($arCurrentValues['IBL_BX24']=='Y') {
		$arBxField = array(
			"COMPANY_TITLE" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD1"),
			"NAME" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD2"),
			"LAST_NAME" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD3"),
			"SECOND_NAME" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD4"),
			"POST" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD5"),
			"ADDRESS" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD6"),
			"COMMENTS" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD7"),
			"SOURCE_DESCRIPTION" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD8"),
			"STATUS_DESCRIPTION" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD9"),
			//"SOURCE_ID" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD13"),
			"STATUS_ID" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD14"),
			"PHONE_WORK" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD16"),
			"PHONE_MOBILE" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD17"),
			"PHONE_FAX" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD18"),
			"PHONE_HOME" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD19"),
			"PHONE_PAGER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD20"),
			"PHONE_OTHER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD21"),
			"WEB_WORK" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD22"),
			"WEB_HOME" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD23"),
			"WEB_FACEBOOK" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD24"),
			"WEB_LIVEJOURNAL" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD25"),
			"WEB_TWITTER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD26"),
			"WEB_OTHER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD27"),
			"EMAIL_WORK" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD28"),
			"EMAIL_HOME" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD29"),
			"EMAIL_OTHER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD30"),
			"IM_SKYPE" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD31"),
			"IM_ICQ" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD32"),
			"IM_MSN" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD33"),
			"IM_JABBER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD34"),
			"IM_OTHER" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD35"),
			"OPPORTINUTY" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD10"),
			"CURRENCY_ID" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD11"),
			"PRODUCT_ID" => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD12"),
		);
		
		$arComponentParameters['PARAMETERS']["FIELDBX1_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD1_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX1_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX1_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX1_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX2_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD2_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX2_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX2_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX2_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX3_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD3_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX3_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX3_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX3_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX4_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD4_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX4_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX4_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX4_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX5_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD5_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX5_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX5_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX5_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX6_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD6_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX6_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX6_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX6_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX7_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD7_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX7_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX7_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX7_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX8_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_FIELD8_CODE"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX8_CODE"]["VALUES"] = array_merge(array("-"=>GetMessage("MLIFE_CAT_BK_EMP")),$arBxField);
		$arComponentParameters['PARAMETERS']["FIELDBX8_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX8_CODE"]["MULTIPLE"] = "N";
		
		$arComponentParameters['PARAMETERS']["FIELDBX10_CODE"] = array(
			"TYPE" => "TEXT",
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD10_CODE"),
			"PARENT" => "BX24",
			"ADDITIONAL_VALUES" => "N",
		);
		$arComponentParameters['PARAMETERS']["FIELDBX11_CODE"] = array(
			"TYPE" => "TEXT",
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD11_CODE"),
			"PARENT" => "BX24",
			"ADDITIONAL_VALUES" => "N",
		);
		$arComponentParameters['PARAMETERS']["FIELDBX12_CODE"] = array(
			"TYPE" => "TEXT",
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD12_CODE"),
			"PARENT" => "BX24",
			"ADDITIONAL_VALUES" => "N",
		);
		$arComponentParameters['PARAMETERS']["FIELDBX13_CODE"] = array(
			"TYPE" => "TEXT",
			'NAME' => GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD15"),
			"PARENT" => "BX24",
			"ADDITIONAL_VALUES" => "N",
		);
		$arComponentParameters['PARAMETERS']["FIELDBX14_CODE"] = array(
			"TYPE" => "TEXT",
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD13_CODE"),
			"PARENT" => "BX24",
			"ADDITIONAL_VALUES" => "N",
		);
		
		$arNewEvent = array("-"=>GetMessage("MLIFE_CAT_BK_EMP"));
		foreach($arEvent as $key=>$val){
			$arNewEvent["n".$key] = $val;
		}
		
		$arComponentParameters['PARAMETERS']["FIELDBX15_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("MLIFE_CAT_BK_IBL_BX24_FIELD7"), "BX24");
		$arComponentParameters['PARAMETERS']["FIELDBX15_CODE"]["VALUES"] = $arNewEvent;
		$arComponentParameters['PARAMETERS']["FIELDBX15_CODE"]["ADDITIONAL_VALUES"] = "N";
		$arComponentParameters['PARAMETERS']["FIELDBX15_CODE"]["MULTIPLE"] = "N";
		
	}
	
	
	$arComponentParameters['PARAMETERS']["MESS_OK"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_MESS_OK"),
				"TYPE" => "TEXT",
				"DEFAULT" =>  GetMessage("MLIFE_CAT_BK_MESS_OK_DEFAULT"),
				"REFRESH" => "N",
				"PARENT" => "SETT",
			);
	$arComponentParameters['PARAMETERS']["F_NAME"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FORMNAME"),
			"TYPE" => "TEXT",
			"DEFAULT" => "",
			"PARENT" => "SETT",
		);
	$arComponentParameters['PARAMETERS']["F_DESC"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FORMDESC"),
			"TYPE" => "TEXT",
			"DEFAULT" => "",
			"PARENT" => "SETT",
		);

?>