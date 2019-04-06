<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

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
		"SHOW_HIDDEN" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME"=>GetMessage("SUBSCR_SHOW_HIDDEN"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N",
		),
		"ALLOW_ANONYMOUS" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME"=>GetMessage("SUBSCR_ALLOW_ANONYMOUS"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>COption::GetOptionString("subscribe", "allow_anonymous"),
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
		"SHOW_AUTH_LINKS" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME"=>GetMessage("SUBSCR_SHOW_AUTH_LINKS"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>COption::GetOptionString("subscribe", "show_auth_links"),
		),
		"TEMPLATE_ID" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("TEMP_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
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
