<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("sale"))
	return;
	
$arPaySys = Array();
$arPaySys = Array("0" => GetMessage("SOPR_CHOOSE_PC"));
$dbPaySystem = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("ACTIVE"=>"Y", "PSA_HAVE_RESULT_RECEIVE"=>"Y"));
while($arPaySystem = $dbPaySystem->Fetch())
{
	$arPaySys[$arPaySystem["ID"]] = $arPaySystem["NAME"];
}

$arPaySysActionArray = Array();
$arPersonTypeSelected = Array();
$arPersonTypeSelected = Array("0" => GetMessage("SOPR_CHOOSE_PT"));
if(IntVal($arCurrentValues["PAY_SYSTEM_ID"]) > 0 )
{
	$dbPersonType = CSalePersonType::GetList($arOrder = Array("SORT"=>"ASC", "NAME"=>"ASC"));
	while($arPersonType = $dbPersonType->Fetch())
	{
		$arPersonTypeArray[$arPersonType["ID"]] = $arPersonType["NAME"];
	}

	$dbPaySysAction = CSalePaySystemAction::GetList(
			array(),
			array(
					"PAY_SYSTEM_ID" => $arCurrentValues["PAY_SYSTEM_ID"],
				),
			false,
			false
		);

	while($arPaySysAction = $dbPaySysAction->Fetch())
	{
		$arPersonTypeSelected[$arPaySysAction["PERSON_TYPE_ID"]] = $arPersonTypeArray[$arPaySysAction["PERSON_TYPE_ID"]];
	}
}

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PAY_SYSTEM_ID" => Array(
			"NAME" => GetMessage("SOPR_PC"),
			"TYPE" => "LIST", 
			"MULTIPLE"=>"N",
			"VALUES" => $arPaySys,
			"COLS"=>25, 
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
			"REFRESH" => "Y",
		),
		"PERSON_TYPE_ID" => Array(
			"NAME" => GetMessage("SOPR_PT"),
			"TYPE" => "LIST", 
			"MULTIPLE"=>"N",
			"VALUES" => $arPersonTypeSelected,
			"COLS"=>25, 
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		)
	)
);
?>