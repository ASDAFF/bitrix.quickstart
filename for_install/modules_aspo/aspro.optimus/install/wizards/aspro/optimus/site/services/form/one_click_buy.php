<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	if(!CModule::IncludeModule("form")) return;
	if(!CModule::IncludeModule("main")) return;
	
	$dbSite = CSite::GetByID(WIZARD_SITE_ID);
	if($arSite = $dbSite -> Fetch()) $lang = $arSite["LANGUAGE_ID"];
	if(strlen($lang) <= 0) $lang = "ru";
	
	WizardServices::IncludeServiceLang("forms.php", $lang);
		
	/*Добавляем почтовое событие*/
	$eventTypeExists = false;
	$db_res = CEventType::GetList( array ("TYPE_ID" => "NEW_ONE_CLICK_BUY"));
	if ($db_res) 
	{ 
		$count = $db_res->SelectedRowsCount(); 
		if ($count>0) { $eventTypeExists = true; } 
	}
	if (!$eventTypeExists)
	{
		$oEventType = new CEventType();
		$arFields = array(	"LID" => $lang,
							"EVENT_NAME" => "NEW_ONE_CLICK_BUY",
							"NAME" => GetMessage("EVENT_NEW_ONE_CLICK_BUY_NAME"),
							"DESCRIPTION" => GetMessage("EVENT_NEW_ONE_CLICK_BUY_DESCRIPTION"));		
		$oEventTypeSrcID = $oEventType->Add($arFields);
	}

	
	/*Добавляем почтовый шаблон*/
	$eventMessageExists = false;
	$eventMessageID = 0;
	$by = "id";
	$order = "asc";
	$db_res = CEventMessage::GetList( $by, $order, array ("TYPE_ID"=>"NEW_ONE_CLICK_BUY", "SITE_ID" => array(WIZARD_SITE_ID)));
	if ($db_res) 
	{ 
		$count = $db_res->SelectedRowsCount(); 
		if ($count>0) 
		{
			$eventMessageExists = true; 
			if ($count==1)
			{
				while ($res = $db_res->GetNext()) { $eventMessageID = $res["ID"]; }
			}
		} 
	}

	$arFields = array(  "ACTIVE" => "Y",
						"EVENT_NAME" => "NEW_ONE_CLICK_BUY",
						"LID" => WIZARD_SITE_ID,
						"EMAIL_FROM" => $wizard->GetVar("shopEmail"),
						"EMAIL_TO" => $wizard->GetVar("shopEmail").", #EMAIL_BUYER#",
						"SUBJECT" => GetMessage("NEW_ONE_CLICK_BUY_EMAIL_SUBJECT"),
						"MESSAGE" => GetMessage("NEW_ONE_CLICK_BUY_EMAIL_TEXT"),
						"BODY_TYPE" => "html");
						
	$oEventMessage = new CEventMessage();
	if (!$eventMessageExists)
	{
		$eventMessageID = $oEventMessage->Add($arFields);
	}
	elseif (intVal($eventMessageID)>0)
	{
		$oEventMessage->Update($eventMessageID, $arFields);
	}
?>