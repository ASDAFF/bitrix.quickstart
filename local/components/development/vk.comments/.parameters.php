<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arComponentParameters = array(
	"GROUPS" => array(
		"ATTACHMENTS" => array(
			"NAME" => GetMessage("ATTACMENTS_NAME_GROUP"),
			"SORT" => 200
		)
	),
	
	"PARAMETERS" => array(
		
		"API_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("API_ID_NAME"),
			"TYPE" => "STRING",
			"REFRESH" => "N"
		),
		
		"HEIGHT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COM_HEIGHT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 520,
			"REFRESH" => "N"
		),
	
		"WIDTH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COM_WIDTH_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 520,
			"REFRESH" => "N"
		),
		
		"COM_AMMOUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COM_AMMOUNT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 10,
			"REFRESH" => "N"
		),
				
		"AUTO_PUBLISH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("AUTO_PUBLISH_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		
		"NO_REAL_TIME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("NO_REAL_TIME_NAME"),
			"TYPE" => "CHECKBOX",
			"DEAFAULT" => "Y"
		),
		
		"ATTACH_GRAFFITI" => array(
			"PARENT" => "ATTACHMENTS",
			"NAME" => GetMessage("ATTACH_GRAFFITI_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'Y'
		),
		
		"ATTACH_PHOTO" => array(
			"PARENT" => "ATTACHMENTS",
			"NAME" => GetMessage("ATTACH_PHOTO_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'Y'
		),
		
		"ATTACH_AUDIO" => array(
			"PARENT" => "ATTACHMENTS",
			"NAME" => GetMessage("ATTACH_AUDIO_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'Y'
		),
		
		"ATTACH_VIDEO" => array(
			"PARENT" => "ATTACHMENTS",
			"NAME" => GetMessage("ATTACH_VIDEO_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'Y'
		),
	)
);
?>