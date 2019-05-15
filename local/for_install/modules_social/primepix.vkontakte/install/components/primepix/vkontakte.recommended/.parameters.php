<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"GROUPS" => array(
      // Вид
      "FORM_SETTINGS" => array(
         "NAME" => GetMessage("FORM_SETTINGS"),
         "SORT" => 101
      ),
      // Дополнительно
      "EXTRA_SETTINGS" => array(
         "NAME" => GetMessage("EXTRA_SETTINGS"),
         "SORT" => 102
      ),

   ),
	"PARAMETERS" => array(
		// ID приложения
		"ID_APLICATION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ID_APLICATION"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25
		),
		// Количество записей
		"NUM_RECORDS" => Array(
			"NAME"=>GetMessage("NUM_RECORDS"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "5", 
			"VALUES"=>array(
				"3" => "3",
				"5" => "5", 
				"10" => "10"), 
			"ADDITIONAL_VALUES"=>"N"
		),
		// Период выборки
		"PERIOD" => Array(
			"NAME"=>GetMessage("PERIOD"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "week", 
			"VALUES"=>array(
				"day" => GetMessage("PERIOD_DAY"),
				"week" => GetMessage("PERIOD_WEEK"), 
				"month" => GetMessage("PERIOD_MONTH")), 
			"ADDITIONAL_VALUES"=>"N"
		),
		// Формулировка
		"FORMULATION" => Array(
			"NAME"=>GetMessage("FORMULATION"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "0", 
			"VALUES"=>array(
				"0" => GetMessage("FORMULATION_LIKE"),
				"1" => GetMessage("FORMULATION_INTERES")),
			"ADDITIONAL_VALUES"=>"N"
		),
		// Сортировка
		"SORT" => Array(
			"NAME"=>GetMessage("SORT"), 
			"PARENT" => "EXTRA_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "friend_likes", 
			"VALUES"=>array(
				"friend_likes" => GetMessage("SORT_FRIEND_LIKES"),
				"likes" => GetMessage("SORT_LIKES")),
			"ADDITIONAL_VALUES"=>"N"
		),
		// Ссылки на странице
		"REF" => Array(
			"NAME"=>GetMessage("REF"), 
			"PARENT" => "EXTRA_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "parent", 
			"VALUES"=>array(
				"blank" => GetMessage("REF_BLANK"),
				"top" => GetMessage("REF_TOP"),
				"parent" => GetMessage("REF_PARENT")),
			"ADDITIONAL_VALUES"=>"N"
		),
	),
);
?>
