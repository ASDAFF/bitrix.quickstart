<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
    return;

global $DB;
$events=array();
$events['']='- '.GetMessage("ITLOGIC_MESSAGES_NE_VAJNO");
$res = $DB->Query( "SELECT `b_event_message`.`ID`, `b_event_message`.`EVENT_NAME`, `b_event_message`.`MESSAGE`, `b_event_type`.`NAME`
	FROM `b_event_type`,`b_event_message`
	WHERE `b_event_type`.`EVENT_NAME`=`b_event_message`.`EVENT_NAME` AND `b_event_type`.`LID` = '".LANGUAGE_ID."' ORDER BY `b_event_type`.`NAME`" );
while( $row = $res->getNext() ){
    $events[ $row['ID'] ] = htmlspecialchars_decode($row['NAME']);
}

$arComponentParameters = array(
    "PARAMETERS" => array(

        "MAIL_TEMPLATE_TYPE" => Array(
            "NAME" => GetMessage("MAIL_TEMPLATE_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $events,
            "ADDITIONAL_VALUES" => "N",
            "PARENT" => "BASE",
        ),

        /*"MAIL_TEMPLATE_EVENT_ID" => Array(
            "NAME" => GetMessage("MAIL_TEMPLATE_EVENT_ID"),
            "TYPE" => "TEXT",
            "PARENT" => "BASE",
            "DEFAULT" => 0,
        )*/
    )
);
?>
