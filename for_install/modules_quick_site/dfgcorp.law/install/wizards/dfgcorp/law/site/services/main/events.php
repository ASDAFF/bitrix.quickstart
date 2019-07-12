<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
	
	$arNewEventMessage = array();
	$arNewEventMessage["ACTIVE"] = "Y";
	$arNewEventMessage["SITE_ID"] = array(WIZARD_SITE_ID);
	$arNewEventMessage["LID"] = array(WIZARD_SITE_ID);
	$arNewEventMessage["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
	$arNewEventMessage["EMAIL_TO"] = "#EMAIL_TO#";
	$arNewEventMessage["BODY_TYPE"] = "text";
		
	$rsEventType = CEventType::GetByID("DFGCORP_FEEDBACK_FORM_FULL", "ru");
	$arEventType = $rsEventType->Fetch();
	if(empty($arEventType)){
		$obEvent = new CEventType;
		$arNewEventType = array(
		    "EVENT_NAME"    => "DFGCORP_FEEDBACK_FORM_FULL",
		    "NAME"          => GetMessage("DFGCORP_FEEDBACK_FORM_FULL_NAME"),
		    "DESCRIPTION"   => "TEXT",
			"LID" => 'ru'
		    );
		$obEvent->Add($arNewEventType);
		$arNewEventType["LID"] = 'en';
		$obEvent->Add($arNewEventType);
	
		$arNewEventMessage["EVENT_NAME"] = "DFGCORP_FEEDBACK_FORM_FULL";
		$arNewEventMessage["SUBJECT"] = GetMessage("FEEDBACK_FORM_FULL_SUBJECT");
		$arNewEventMessage["MESSAGE"] = GetMessage("FEEDBACK_FORM_FULL_BODY");
		
		$obEventMessage = new CEventMessage;
		$obEventMessage->Add($arNewEventMessage);
	
	}else{
		$arFilter = Array(
		    "TYPE_ID"       => "DFGCORP_FEEDBACK_FORM_FULL",
		    "ACTIVE"        => "Y",
	    );
		$rsMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
		$arMess = $rsMess->GetNext();
		if(empty($arMess)){
			$arNewEventMessage["EVENT_NAME"] = "DFGCORP_FEEDBACK_FORM_FULL";
			$arNewEventMessage["SUBJECT"] = GetMessage("FEEDBACK_FORM_FULL_SUBJECT");
			$arNewEventMessage["MESSAGE"] = GetMessage("FEEDBACK_FORM_FULL_BODY");
			
			$obEventMessage = new CEventMessage;
			$obEventMessage->Add($arNewEventMessage);
		}
	}
	
	$rsEventType = CEventType::GetByID("DFGCORP_FEEDBACK_FORM_CALL", "ru");
	$arEventType = $rsEventType->Fetch();
	
	if(empty($arEventType)){
		$obEvent = new CEventType;
		$arNewEventType = array(
		    "EVENT_NAME"    => "DFGCORP_FEEDBACK_FORM_CALL",
		    "NAME"          => GetMessage("DFGCORP_FEEDBACK_FORM_CALL_NAME"),
		    "DESCRIPTION"   => "TEXT",
			"LID" => 'ru'
		);
		$obEvent->Add($arNewEventType);
		$arNewEventType["LID"] = 'en';
		$obEvent->Add($arNewEventType);
	
		$arNewEventMessage["EVENT_NAME"] = "DFGCORP_FEEDBACK_FORM_CALL";
		$arNewEventMessage["SUBJECT"] = GetMessage("FEEDBACK_FORM_CALL_SUBJECT");
		$arNewEventMessage["MESSAGE"] = GetMessage("FEEDBACK_FORM_CALL_BODY");
		
		$obEventMessage = new CEventMessage;
		$obEventMessage->Add($arNewEventMessage);
	
	}else{
		$arFilter = Array(
		    "TYPE_ID"       => "DFGCORP_FEEDBACK_FORM_CALL",
		    "ACTIVE"        => "Y",
	    );
		$rsMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
		$arMess = $rsMess->GetNext();
		if(empty($arMess)){
			$arNewEventMessage["EVENT_NAME"] = "DFGCORP_FEEDBACK_FORM_CALL";
			$arNewEventMessage["SUBJECT"] = GetMessage("FEEDBACK_FORM_CALL_SUBJECT");
			$arNewEventMessage["MESSAGE"] = GetMessage("FEEDBACK_FORM_CALL_BODY");
			
			$obEventMessage = new CEventMessage;
			$obEventMessage->Add($arNewEventMessage);
		}
	}
?>