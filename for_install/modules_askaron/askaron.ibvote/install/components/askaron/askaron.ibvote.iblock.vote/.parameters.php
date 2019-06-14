<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"LIMITS" =>  array(
			"NAME" => GetMessage("IBVOTE_LIMITS"),
			"SORT" => 200,
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"MAX_VOTE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_MAX_VOTE"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"VOTE_NAMES" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_VOTE_NAMES"),
			"TYPE" => "STRING",
			"VALUES" => array(),
			"MULTIPLE" => "Y",
			"DEFAULT" => array("1","2","3","4","5"),
			"ADDITIONAL_VALUES" => "Y",
		),
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BIV_SET_STATUS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SESSION_CHECK" => array(
			"PARENT" => "LIMITS",
			"NAME" => GetMessage("IBVOTE_SESSION_CHECK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),		
		"COOKIE_CHECK" => array(
			"PARENT" => "LIMITS",
			"NAME" => GetMessage("IBVOTE_COOKIE_CHECK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),		
		"IP_CHECK_TIME" => array(
			"PARENT" => "LIMITS",
			"NAME" => GetMessage("IBVOTE_IP_CHECK_TIME"),
			"TYPE" => "VALUE",
			"DEFAULT" => 86400, //60*60*24
		),
		"USER_ID_CHECK_TIME" => array(
			"PARENT" => "LIMITS",
			"NAME" => GetMessage("IBVOTE_USER_ID_CHECK_TIME"),
			"TYPE" => "VALUE",
			"DEFAULT" => 0,
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
?>
