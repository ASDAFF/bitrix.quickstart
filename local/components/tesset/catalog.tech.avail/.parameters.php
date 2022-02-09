<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();


if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	$arALLIBlocks[$arr["ID"]] = $arr;
}


$rsElements = CIBlockElement::GetList(
	array("SORT" => "ASC"),
	array(
		"IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"],
		"ACTIVE" => "Y"
		)
	);
while($arr=$rsElements->Fetch())
	$arIBlockElements[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];


$arComponentParameters = array(
 "PARAMETERS" => array(
 	"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => "Тип инфоблока",
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
	"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => "ID инфоблока",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
	"ID" => array(
			"PARENT" => "BASE",
			"NAME" => "ID баннеров (опцинально)",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"MULTIPLE" => "Y",
			"VALUES" => $arIBlockElements,
			"REFRESH" => "Y",
		),
	"CACHE_TIME"  =>  array(

			"DEFAULT" => 3600

		),

 )

);



?>