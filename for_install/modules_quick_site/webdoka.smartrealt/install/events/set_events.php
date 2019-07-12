<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = "webdoka.smartrealt";

$arEventTypes = array (
	"SMARTREALT_FEEDBACK_FORM",
);
include ($_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/install/events/unset_events.php");
$arTemplates = array ();

function UET($EVENT_NAME, $NAME, $LID, $DESCRIPTION) {
	global $DB;
	$et = new CEventType ( );
	$et->Add ( Array ("LID" => $LID, "EVENT_NAME" => $EVENT_NAME, "NAME" => $NAME, "DESCRIPTION" => $DESCRIPTION ) );
}

$langs = CLanguage::GetList ( ($b = ""), ($o = "") );
while ( $lang = $langs->Fetch () ) :
	
	$arSites = array ();
	$sites = CSite::GetList ( $by, $order, Array ("LANGUAGE_ID" => $lang ["LID"] ) );
	while ( $site = $sites->Fetch () )
		$arSites [] = $site ["LID"];
	
	if (count($arSites) <= 0)
		continue;
	
	$lid = $lang ["LID"];
	
	reset ( $arEventTypes );
	foreach ( $arEventTypes as $sEventType ) {
		include ($_SERVER ["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module_id . "/lang/" . $lid . "/events/". strtolower ( $sEventType ) . ".php");
	}
endwhile;   

//************************************************************************************************


$emess = new CEventMessage ( );
foreach ( $arTemplates as $Template ) {
	$arFields = Array ("ACTIVE" => "Y", "EVENT_NAME" => $Template ["EVENT_NAME"], "LID" => $Template ["SITE_ID"], "EMAIL_FROM" => $Template ["EMAIL_FROM"], "EMAIL_TO" => $Template ["EMAIL_TO"], "BCC" => $Template ["BCC"], "SUBJECT" => $Template ["SUBJECT"], "MESSAGE" => $Template ["MESSAGE"], "BODY_TYPE" => $Template ["BODY_TYPE"] );
	$emess->Add ( $arFields );
}
?>