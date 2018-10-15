<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("fileman");
CMedialib::Init();
$rsMedia = CMedialibCollection::GetList(array());
foreach($rsMedia as $arMedia)
	$arMediaLib[$arMedia["ID"]] = "[".$arMedia["ID"]."] ".$arMedia["NAME"];
$arComponentParameters = array(
	"PARAMETERS" => array(
		"INCLUDE_JQUERY"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_INCLUDE_JQUERY"),
			"TYPE"=>"CHECKBOX"
		),
		"MEDIA_ID"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_KOLLEKCIA_MEDIABIBLI"),
			"TYPE"=>"LIST",
			"VALUES"=>$arMediaLib,
		),
		"MEDIA_SORT_FIELD"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_SORTIROVATQ_PO"),
			"TYPE"=>"LIST",
			"ADDITIONAL_VALUES"=>"Y",
			"VALUES"=>array(
				"DESCRIPTION"=>GetMessage("APROF_LENTAZOOM_OPISANIU"),
				"KEYWORDS"=>GetMessage("APROF_LENTAZOOM_KLUCEVYM_SLOVAM")
			)
		),
		"MEDIA_SORT_ORDER"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_PORADOK_SORTIROVKI"),
			"TYPE"=>"LIST",
			"VALUES"=>array(
				"ASC"=>GetMessage("APROF_LENTAZOOM_PO_VOZRASTANIU"),
				"DESC"=>GetMessage("APROF_LENTAZOOM_PO_UBYVANIU")
			)
		),
		"SLIDE_WIDTH"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_MAKSIMALQNAA_SIRINA"),
			"TYPE"=>"TEXT"
		),
		"SLIDE_HEIGHT"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_MAKSIMALQNAA_VYSOTA"),
			"TYPE"=>"TEXT"
		),
		"SLIDE_ZOOM_WIDTH"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_MAKSIMALQNAA_SIRINA1"),
			"TYPE"=>"TEXT"
		),
		"SLIDE_ZOOM_HEIGHT"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_MAKSIMALQNAA_VYSOTA1"),
			"TYPE"=>"TEXT"
		),
		"CNT"=>array(
			"PARENT"=>"BASE",
			"NAME"=>GetMessage("APROF_LENTAZOOM_KOLICESTVO_SLAYDOV"),
			"TYPE"=>"TEXT"
		)
	)
);
?>