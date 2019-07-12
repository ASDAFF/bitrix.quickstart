<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Mlife\Asz;
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if(!CModule::IncludeModule('mlife.asz')) {
		return;
}

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));

$arUserFields = array();
$res = ASZ\OrderpropsTable::getList(array(
	'order' => array("SORT"=>"ASC"),
	'filter' => array("SITEID" => $site,"ACTIVE"=>"Y")
));
$arUserFields["-"] = GetMessage("MLIFE_ASZ_BASKET_FULL_P_3");
while($arRes = $res->Fetch()){
	if($arRes['CODE']){
		$arUserFields[$arRes['CODE']] = '['.$arRes['CODE'].'] - '.$arRes['NAME'];
	}
}

//группы
$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", array());
$arUserGroup = array();
while($arGroups = $rsGroups->Fetch()){
	$arUserGroup[$arGroups["ID"]] = $arGroups["NAME"];
}
	
$arComponentParameters = array(
	"GROUPS" => array(
		"USER" => array(
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_1"),
		),
		"TOVAR" => array(
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_2"),
		),
		"PROP" => array(
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_4"),
		),
	),
	"PARAMETERS" => array(
		"ORDERPRIV" => array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_5"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
	),
);

if($arCurrentValues["ORDERPRIV"]=='N') {
		$arComponentParameters["PARAMETERS"]["FINDUSER"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_6"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		);
		$arComponentParameters["PARAMETERS"]["FINDEMAIL"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_7"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		);
		$arComponentParameters["PARAMETERS"]["FINDEMAIL_NOAUT"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_8"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		);
		$arComponentParameters["PARAMETERS"]["GROUP_ADMIN"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_9"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arUserGroup,
		);
		$arComponentParameters["PARAMETERS"]["NOEMAIL"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_10"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"GEN" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_11"),
				"USER" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_12"),
			),
			"REFRESH" => "Y",
			"DEFAULT" => "N",
		);
		$arComponentParameters["PARAMETERS"]["PROP_NAME"] = array(
			"PARENT" => "PROP",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_13"),
			"TYPE" => "LIST",
			"VALUES" => $arUserFields,
			"MULTIPLE" => "N",
		);
		$arComponentParameters["PARAMETERS"]["PROP_EMAIL"] = array(
			"PARENT" => "PROP",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_14"),
			"TYPE" => "LIST",
			"VALUES" => $arUserFields,
			"MULTIPLE" => "N",
		);

		if($arCurrentValues["NOEMAIL"]=='USER') {
			$arComponentParameters["PARAMETERS"]["NOEMAIL_USER"] = array(
				"PARENT" => "USER",
				"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_15"),
				"TYPE" => "TEXT",
			);
		}
		$arComponentParameters["PARAMETERS"]["LOGIN"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_16"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"EMAIL" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_17"),
				"PREFIX" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_18"),
				"PREFIXEMAIL" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_19"),
			),
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		);
		if($arCurrentValues['LOGIN']=='PREFIX' || $arCurrentValues['LOGIN']=='PREFIXEMAIL'){
			$arComponentParameters["PARAMETERS"]["LOGIN_PREFIX"] = array(
				"PARENT" => "USER",
				"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_20"),
				"TYPE" => "TEXT",
				"DEFAULT" => 'user_',
			);
		}
		$arComponentParameters["PARAMETERS"]["GROUP_ADDUSER"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_21"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arUserGroup,
		);
}else{
	$arComponentParameters["PARAMETERS"]["ORDERPRIV_USERID"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_22"),
			"TYPE" => "TEXT",
		);
}
$arComponentParameters["PARAMETERS"]["ORDERPRIV_GROUP"] = array(
			"PARENT" => "USER",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_23"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arUserGroup,
		);
$arComponentParameters["PARAMETERS"]["QUANT"] = array(
			"PARENT" => "TOVAR",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_24"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		);
if($arCurrentValues["QUANT"]=="Y"){
$arComponentParameters["PARAMETERS"]["ZAKAZ"] = array(
			"PARENT" => "TOVAR",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_ZAKAZ"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		);
$arComponentParameters["PARAMETERS"]["ZAKAZ_TEXT"] = array(
			"PARENT" => "TOVAR",
			"NAME" => GetMessage("MLIFE_ASZ_BASKET_FULL_P_ZAKAZ_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		);
}
?>