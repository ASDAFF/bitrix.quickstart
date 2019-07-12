<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

global $DB;
$module_id = "webdoka.smartrealt";
$arEventTypes = array (
	"SMARTREALT_FEEDBACK_FORM",
);
$sEventTypes = "";
foreach ( $arEventTypes as $sEventType ) {
	$sEventTypes .= ($sEventTypes ? ", " : "") . "'" . $sEventType . "'";
}
$DB->Query ( "DELETE FROM b_event_type WHERE EVENT_NAME in (" . $sEventTypes . ")" );
$DB->Query ( "DELETE FROM b_event_message WHERE EVENT_NAME in (" . $sEventTypes . ")" );
?>