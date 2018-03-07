<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	
$arComponentParameters = array(

	"GROUPS" => array(
		"DAYS_PARAM" => array(
			"NAME" => GetMessage("DAYS")
		),
	),

	"PARAMETERS" => array(
	
		"MONDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("MONDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"TUESDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("TUESDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"WEDNESDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("WEDNESDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"THURSDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("THURSDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"FRIDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("FRIDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"SATURDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("SATURDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"SUNDAY" =>array(
			"PARENT" => "DAYS_PARAM",
			"NAME" => GetMessage("SUNDAY"),
			"TYPE" => "CHECKBOX",
		),
		
		"TIME_WORK" =>array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TIME_WORK"),
			"TYPE" => "STRING",
			"DEFAULT" => "08:00 - 18:00"
		),
		
		"TIME_WEEKEND" =>array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TIME_WEEKEND"),
			"TYPE" => "STRING",
			"DEFAULT" => "08:00-17:00"
		),	

		"LUNCH" =>array(
			"PARENT" => "BASE",
			"NAME" => "Выпадающий текст",
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("WORKTIME_OBED_S")." 13:00 ".GetMessage("WORKTIME_DO")." 14:00"
		),		


		"CACHE_TIME"  =>  Array("DEFAULT"=>360000),
	),

);
?>