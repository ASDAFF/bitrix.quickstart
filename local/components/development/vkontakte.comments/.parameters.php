<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"GROUPS" => array(
      // Вид
      "FORM_SETTINGS" => array(
         "NAME" => GetMessage("FORM_SETTINGS"),
         "SORT" => 101
      ),
      // Медиа
      "MEDIA_SETTINGS" => array(
         "NAME" => GetMessage("MEDIA_SETTINGS"),
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
		// Ширина
		"WIDTH_FORM" => array(
			"PARENT" => "FORM_SETTINGS",
			"NAME" => GetMessage("WIDTH_FORM"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "496",
			"COLS" => 5
		),
		// Количество комментариев
		"NUM_COMMENTS" => Array(
			"NAME"=>GetMessage("NUM_COMMENTS"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "10", 
			"VALUES"=>array(
				"5" => "5",
				"10" => "10",
				"15" => "15", 
				"20" => "20"), 
			"ADDITIONAL_VALUES"=>"N"
		),
		// Граффити
		"MEDIA_GRAFFITI" => array(
			"PARENT" => "MEDIA_SETTINGS",
			"NAME" => GetMessage("MEDIA_GRAFFITI"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		// Фотографии
		"MEDIA_PHOTOS" => array(
			"PARENT" => "MEDIA_SETTINGS",
			"NAME" => GetMessage("MEDIA_PHOTOS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		// Видео
		"MEDIA_VIDEO" => array(
			"PARENT" => "MEDIA_SETTINGS",
			"NAME" => GetMessage("MEDIA_VIDEO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		// Аудио
		"MEDIA_AUDIO" => array(
			"PARENT" => "MEDIA_SETTINGS",
			"NAME" => GetMessage("MEDIA_AUDIO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		// Ссылки
		"MEDIA_REF" => array(
			"PARENT" => "MEDIA_SETTINGS",
			"NAME" => GetMessage("MEDIA_REF"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
?>
