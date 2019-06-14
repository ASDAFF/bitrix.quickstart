<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

if(!\Bitrix\Main\Loader::includeModule("highloadblock"))
	return;



$arIBlock = array();

$rsIBlock  = \Bitrix\Highloadblock\HighloadBlockTable::getList();
while($arr = $rsIBlock->Fetch())
	{
		$arIBlock[$arr["TABLE_NAME"]] = "[".$arr["TABLE_NAME"]."] ".$arr["NAME"];
   
}


$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_CB_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
		),
		"CACHE_TIME"  =>  array(
			"DEFAULT" => 36000000
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("IBLOCK_CB_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		)
	)
);
?>