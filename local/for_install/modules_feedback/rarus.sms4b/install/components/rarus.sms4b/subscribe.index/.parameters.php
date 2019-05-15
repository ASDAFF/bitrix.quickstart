<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("subscribe"))
{
	ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
	return;
}

$rub = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y"));

$show_rub = array();
while($temp = $rub->Fetch())
{
	$show_rub[$temp["ID"]] = $temp["NAME"];
}
$count = $rub->SelectedRowsCount();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"SHOW_COUNT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME"=>GetMessage("SUBSCR_SHOW_COUNT"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N",
		),
		"SHOW_HIDDEN" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME"=>GetMessage("SUBSCR_SHOW_HIDDEN"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N",
		),
		"SHOW_POST_FORM" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME"=>GetMessage("SUBSCR_SHOW_POST_FORM"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"Y",
		),
		"SHOW_SMS_FORM" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME"=>GetMessage("SUBSCR_SHOW_SMS_FORM"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"Y",
		),
		"PAGE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"=>GetMessage("SUBSCR_FORM_PAGE"),
			"TYPE"=>"STRING",
			"DEFAULT"=>COption::GetOptionString("subscribe", "subscribe_section")."subscr_edit.php",
		),
		"SHOW_RUBS"=>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SUBSCR_SHOW_RUB"),
			"TYPE" => "LIST",
			"VALUES" => $show_rub,
			"MULTIPLE"=>"Y",
			"SIZE" => $count,
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"SET_TITLE" => array(),
	),
);
?>
