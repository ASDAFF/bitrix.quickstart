<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//error_reporting(E_WARNING);
//ini_set("display_errors",1);

if(!CModule::IncludeModule("iblock") or !CModule::IncludeModule("sale") or !CModule::IncludeModule("catalog")) return;

//mprint($arCurrentValues);

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "MLIFE_BISTROKLICK", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
$LID = false;
while($arr=$rsIBlock->Fetch()) {
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	$LID = $arr['LID'];
}

$arTypePerson = array();
if($LID) {
	$filterPerson = array("LID" => $LID);
}else{
	$filterPerson = array();
}
$db_ptype = CSalePersonType::GetList(Array("SORT" => "ASC"), $filterPerson);
while ($ptype = $db_ptype->Fetch())
{
	$arTypePerson[$ptype["ID"]] = "[".$ptype["ID"]."] ".$ptype["NAME"];
}

$arTypePersonField = array();
$arFilter["PERSON_TYPE_ID"] = $arCurrentValues["PERSON_TYPE"];
$dbResultList = CSaleOrderProps::GetList(
				array(),
				$arFilter,
				false,
				false,
				array("ID","NAME")
			);
while ($arR = $dbResultList->Fetch()) {
	$arTypePersonField[$arR["ID"]] = "[".$arR["ID"]."] ".$arR["NAME"];
	$arTypePersonField2['addfield_'.$arR["ID"]] = "[".$arR["ID"]."] ".$arR["NAME"];
}

$arPrice = array();
$arSort = array();
$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
if($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
	while($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty_Offers[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

//доставка
$arDelivery = array();
$db_dtype = CSaleDelivery::GetList(
    array(
            "SORT" => "ASC",
            "NAME" => "ASC"
        ),
    array(
            "LID" => $site,
            "ACTIVE" => "Y",
        ),
    false,
    false,
    array()
);
while ($ar_dtype = $db_dtype->Fetch())
{
    $arDelivery[$ar_dtype["ID"]] = $ar_dtype["NAME"];
}

//оплата
$arPaysystem = array();
$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("LID"=>$site, "ACTIVE"=>"Y", "PERSON_TYPE_ID"=>$arCurrentValues["PERSON_TYPE"]));
while ($ptype = $db_ptype->Fetch())
{
	$arPaysystem[$ptype["ID"]] = $ptype["NAME"];
}

//типы цен
$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["ID"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];

//список групп пользователей
$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", array("ACTIVE"=>"Y","ADMIN"=>"N"));
$arUsersGroups = array();
if(intval($rsGroups->SelectedRowsCount()) > 0)
{
   while($arGroups = $rsGroups->Fetch())
   {
      $arUsersGroups[$arGroups["ID"]] = $arGroups["NAME"];
   }
}
	
$arComponentParameters = array(
	"GROUPS" => array(
			"FORM" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_FORM"),
            "SORT" => "305",
            ),
			"ORDER" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_ORDER"),
            "SORT" => "310",
            ),
			"NOTICE" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_NOTICE"),
            "SORT" => "315",
            ),
			"SETT" => array(
            "NAME" => GetMessage("MLIFE_CAT_BK_GROUP_SETT"),
            "SORT" => "320",
            ),
	),

	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_CAT_BK_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_CAT_BK_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"KEY" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_KEY"),
			'TYPE' => 'TEXT',
			'DEFAULT' => '3j5h34kj5h34kj5h3k4j5h'.rand(10,100),
			"PARENT" => "FORM",
			"REFRESH" => "N",
		),
		"FIELD_SHOW" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_SHOW"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => array_merge(array(
					"name" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_NAME"),
					"phone" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_PHONE"),
					"email" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_EMAIL"),
					"mess" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_MESS")
				),$arTypePersonField2),
			"SIZE" => 8
		),
		"FIELD_REQ" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_REQ"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => array_merge(array(
					"name" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_NAME"),
					"phone" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_PHONE"),
					"email" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_EMAIL"),
					"mess" => GetMessage("MLIFE_CAT_BK_FIELD_REQ_MESS")
				),$arTypePersonField2),
			"SIZE" => 8
		),
		"FIELD_DELIVERY" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_FIELD_DELIVERY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => $arDelivery,
			"SIZE" => 4
		),
		"FIELD_PAYSYSTEM" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_FIELD_PAYSYSTEM"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "FORM",
			"VALUES" => $arPaysystem,
			"SIZE" => 4
		),
		
		"CREATE_ORDER" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_CREATE_ORDER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "ORDER",
		),
		"NOTICE_ADMIN" => array(
			'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "NOTICE",
		),
		
	)
);

	if (CModule::IncludeModule('currency'))
	{

		$arCurrencyList = array();
		$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
		while ($arCurrency = $rsCurrencies->Fetch())
		{
			$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
		}
		$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('MLIFE_CAT_BK_CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arCurrencyList,
			'DEFAULT' => CCurrency::GetBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
		$arComponentParameters['PARAMETERS']['CURRENCY_SECOND'] = array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('MLIFE_CAT_BK_CURRENCY_ID2'),
			'TYPE' => 'LIST',
			'VALUES' => $arCurrencyList,
			'DEFAULT' => CCurrency::GetBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}
	
	$arComponentParameters['PARAMETERS']["PRICE_CODE"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_CAT_BK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		);
	
	if($arCurrentValues['CREATE_ORDER']=='Y') {
		
		$arComponentParameters['PARAMETERS']["PERSON_TYPE"] = array(
			"PARENT" => "ORDER",
			"NAME" => GetMessage("MLIFE_CAT_BK_PERSON_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypePerson,
			"REFRESH" => "Y",
		);
		$arComponentParameters['PARAMETERS']["CHECK_USER"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_CHECK_USER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "ORDER",
		);
		$arComponentParameters['PARAMETERS']["CREATE_USER"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_CREATE_USER"),
			"TYPE" => "LIST",
			"PARENT" => "ORDER",
			"VALUES" => array(
					"SET" => GetMessage("MLIFE_CAT_BK_CREATE_USER_SET"),
					"CUR" => GetMessage("MLIFE_CAT_BK_CREATE_USER_CUR")
			),
			"REFRESH" => "Y",
		);
		
		if($arCurrentValues['CREATE_USER']=='SET') {
			$arComponentParameters['PARAMETERS']['CUR_USER'] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_CUR_USER"),
				'TYPE' => 'TEXT',
				'DEFAULT' => 1,
				"PARENT" => "ORDER",
				"REFRESH" => "N",
			);
		}else{
			$arComponentParameters['PARAMETERS']['USER_PREFIX'] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_USER_PREFIX"),
				'TYPE' => 'TEXT',
				'DEFAULT' => 'user_',
				"PARENT" => "ORDER",
				"REFRESH" => "N",
			);
			$arComponentParameters['PARAMETERS']["USER_GROUP"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_FIELD_USER_GROUP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"PARENT" => "ORDER",
			"VALUES" => $arUsersGroups,
			"SIZE" => 4
			);
		}
		$arComponentParameters['PARAMETERS']["PERSON_FIELD_NAME"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_PERSON_FIELD_NAME"),
			"TYPE" => "LIST",
			"PARENT" => "ORDER",
			"VALUES" => $arTypePersonField,
			"REFRESH" => "N",
		);
		$arComponentParameters['PARAMETERS']["PERSON_FIELD_EMAIL"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_PERSON_FIELD_EMAIL"),
			"TYPE" => "LIST",
			"PARENT" => "ORDER",
			"VALUES" => $arTypePersonField,
			"REFRESH" => "N",
		);
		$arComponentParameters['PARAMETERS']["PERSON_FIELD_PHONE"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_PERSON_FIELD_PHONE"),
			"TYPE" => "LIST",
			"PARENT" => "ORDER",
			"VALUES" => $arTypePersonField,
			"REFRESH" => "N",
		);
		
	
	}
	
	if($arCurrentValues['NOTICE_ADMIN']=='Y') {
		$arComponentParameters['PARAMETERS']["NOTICE_ADMIN_MAIL"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_ADMIN_MAIL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "NOTICE",
		);
		if($arCurrentValues["NOTICE_ADMIN_MAIL"]=='Y'){
			$arComponentParameters['PARAMETERS']["NOTICE_ADMIN_MAIL_EMAIL"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_ADMIN_MAIL_EMAIL"),
				"TYPE" => "TEXT",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"PARENT" => "NOTICE",
			);
			$arComponentParameters['PARAMETERS']["NOTICE_ADMIN_MAIL_EVENT_MESSAGE_ID"] = Array(
				"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID"), 
				"TYPE"=>"LIST", 
				"VALUES" => $arEvent,
				"DEFAULT"=>"", 
				"MULTIPLE"=>"N",
				"COLS"=>25, 
				"PARENT" => "NOTICE",
			);
		}
		$arComponentParameters['PARAMETERS']["NOTICE_USER_MAIL"] = array(
			'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_USER_MAIL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "NOTICE",
		);
		if($arCurrentValues["NOTICE_USER_MAIL"]=='Y'){
			$arComponentParameters['PARAMETERS']["NOTICE_USER_MAIL_EVENT_MESSAGE_ID"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
					"PARENT" => "NOTICE",
			);
			$arComponentParameters['PARAMETERS']["NOTICE_USER_MAIL_EVENT_MESSAGE_ID2"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID2"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
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
			if($arCurrentValues["NOTICE_ADMIN_SMS"]=='Y'){
				$arComponentParameters['PARAMETERS']["NOTICE_ADMIN_SMS_PHONE"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_ADMIN_SMS_PHONE"),
				"TYPE" => "TEXT",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"PARENT" => "NOTICE",
				);
				$arComponentParameters['PARAMETERS']["NOTICE_ADMIN_SMS_EVENT_MESSAGE_ID"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
					"PARENT" => "NOTICE",
				);
			}
			$arComponentParameters['PARAMETERS']["NOTICE_USER_SMS"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_USER_SMS"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"REFRESH" => "Y",
				"PARENT" => "NOTICE",
			);
			if($arCurrentValues["NOTICE_USER_SMS"]=='Y'){
				$arComponentParameters['PARAMETERS']["NOTICE_USER_SMS_EVENT_MESSAGE_ID"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
					"PARENT" => "NOTICE",
				);
				$arComponentParameters['PARAMETERS']["NOTICE_USER_SMS_EVENT_MESSAGE_ID2"] = Array(
					"NAME" => GetMessage("MLIFE_CAT_BK_NOTICE_EVENT_MESSAGE_ID2"), 
					"TYPE"=>"LIST", 
					"VALUES" => $arEvent,
					"DEFAULT"=>"", 
					"MULTIPLE"=>"N",
					"COLS"=>25, 
					"PARENT" => "NOTICE",
				);
			}
			if($arCurrentValues["NOTICE_USER_SMS"]=='Y' || $arCurrentValues["NOTICE_ADMIN_SMS"]=='Y'){
				$arComponentParameters['PARAMETERS']["NOTICE_SMS_MODULE"] = array(
					'NAME' => GetMessage("MLIFE_CAT_BK_NOTICE_SMS_MODULE"),
					"TYPE" => "LIST",
					"PARENT" => "NOTICE",
					"VALUES" => $arSms,
					"REFRESH" => "N",
				);
			}
		}
	}
	
	$arComponentParameters['PARAMETERS']["MESS_OK"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_MESS_OK"),
				"TYPE" => "TEXT",
				"DEFAULT" =>  GetMessage("MLIFE_CAT_BK_MESS_OK_DEFAULT"),
				"REFRESH" => "N",
				"PARENT" => "SETT",
			);
	$arComponentParameters['PARAMETERS']["SHOW_KAPCHA"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_SHOW_KAPCHA"),
				"TYPE" => "LIST",
				"VALUES" =>  array(
						GetMessage("MLIFE_CAT_BK_SHOW_KAPCHA_LIST1"),
						GetMessage("MLIFE_CAT_BK_SHOW_KAPCHA_LIST2"),
						GetMessage("MLIFE_CAT_BK_SHOW_KAPCHA_LIST3")
					),
				"REFRESH" => "N",
				"MULTIPLE"=>"N",
				"PARENT" => "SETT",
			);
	if(count($arProperty_Offers)>0){
	$arComponentParameters['PARAMETERS']["OFFERS_PROPERTY_CODE"] = array(
				'NAME' => GetMessage("MLIFE_CAT_BK_OFFERS_PROPERTY_CODE"),
				"TYPE" => "LIST",
				"VALUES" => $arProperty_Offers,
				"MULTIPLE"=>"Y",
				"REFRESH" => "N",
				"PARENT" => "SETT",
			);
	}

?>