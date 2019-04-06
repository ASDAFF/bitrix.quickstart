<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("sale"))
	return;
	

$arStatuses = Array();
$arStatuses = Array("0" => GetMessage("CHOOSE_STATUS"));
$dsres = CSaleStatus::GetList(array(),array(),false,false,array());
while ($ar_status = $dsres->Fetch()){
	if($ar_status['LID'] == LANGUAGE_ID){
		$arStatuses[$ar_status["ID"]] = '['.$ar_status["ID"].'] '.$ar_status["NAME"];
	}
}

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"SET_STATUS_AFTER_PAYMENT" => Array(
			"NAME" => GetMessage("SET_STATUS"),
			"TYPE" => "LIST", 
			"MULTIPLE"=>"N",
			"VALUES" => $arStatuses,
			"COLS"=>25, 
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
			"REFRESH" => "N",
		)
	)
);
?>