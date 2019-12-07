<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"KEY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KEY"),
			"TYPE" => "STRING",
			"DEFAULT" => "AIzaSyBbJ16uUP1tqA_-qsojvMCBV12V71rukHA",
		),
		"CONTAINER_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CONTAINER_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "map-canvas",
		),
		"CONTAINER_CLASS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CONTAINER_CLASS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),		
		"LATITUDE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("LATITUDE"),
			"TYPE" => "STRING",
			"DEFAULT" => "55.805032",
		),
		"LONGITUDE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("LONGITUDE"),
			"TYPE" => "STRING",
			"DEFAULT" => "37.568039",
		),
		"LATITUDE_CENTER_MAP" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("LATITUDE_CENTER_MAP"),
			"TYPE" => "STRING",
			"DEFAULT" => "55.805032",
		),
		"LONGITUDE_CENTER_MAP" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("LONGITUDE_CENTER_MAP"),
			"TYPE" => "STRING",
			"DEFAULT" => "37.568039",
		),
		"SCROLLWHEEL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SCROLLWHEEL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),		
		"ZOOM" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZOOM"),
			"TYPE" => "STRING",
			"DEFAULT" => "17",
		),
		"TOUCH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TOUCH"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"MARKER_IMAGE_FILE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MARKER_IMAGE_FILE"),
			"TYPE" => "STRING",
			"DEFAULT" => "/images/marker.png",
		),
		"TITLE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CONTENT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CONTENT"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CONTENT_SHOW_ONLOAD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CONTENT_SHOW_ONLOAD"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CONTENT_OFFSET_TOP" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CONTENT_OFFSET_TOP"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"CONTENT_OFFSET_RIGHT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CONTENT_OFFSET_RIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"STYLES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("STYLES"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),		
	),
);
?>