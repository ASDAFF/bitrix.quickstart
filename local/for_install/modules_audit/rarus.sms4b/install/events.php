<?
$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", Array("EVENT_NAME" => "SMS4B_SALE_NEW_ORDER"));
if(!($dbEvent->Fetch()))
{
	$langs = CLanguage::GetList(($b=""), ($o=""));
	while($lang = $langs->Fetch())
	{
		$lid = $lang["LID"];
		IncludeModuleLangFile(__FILE__, $lid);

		$et = new CEventType;
		$et->Add(array(
				"LID" => $lid,
				"EVENT_NAME" => "SMS4B_TASK_ADD",
				"NAME" => GetMessage("SMS4B_TASK_ADD_NAME"),
				"DESCRIPTION" => GetMessage("SMS4B_TASK_ADD_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
				"LID" => $lid,
				"EVENT_NAME" => "SMS4B_TASK_UPDATE",
				"NAME" => GetMessage("SMS4B_TASK_UPDATE_NAME"),
				"DESCRIPTION" => GetMessage("SMS4B_TASK_UPDATE_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
				"LID" => $lid,
				"EVENT_NAME" => "SMS4B_TASK_DELETE",
				"NAME" => GetMessage("SMS4B_TASK_DELETE_NAME"),
				"DESCRIPTION" => GetMessage("SMS4B_TASK_DELETE_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
				"LID" => $lid,
				"EVENT_NAME" => "SMS4B_TICKET_NEW_FOR_TECHSUPPORT",
				"NAME" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_NAME"),
				"DESCRIPTION" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_TICKET_NEW_FOR_TECHSUPPORT",
			"NAME" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_TICKET_NEW_FOR_TECHSUPPORT",
			"NAME" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT",
			"NAME" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT",
			"NAME" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_SUBSCRIBE_CONFIRM",
			"NAME" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SUBSCRIBE_CONFIRM",
			"NAME" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_SALE_NEW_ORDER",
			"NAME" => GetMessage("SMS4B_SALE_NEW_ORDER_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_NEW_ORDER_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SALE_NEW_ORDER",
			"NAME" => GetMessage("SMS4B_SALE_NEW_ORDER_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_NEW_ORDER_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_SALE_ORDER_CANCEL",
			"NAME" => GetMessage("SMS4B_SALE_ORDER_CANCEL_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_ORDER_CANCEL_DESC"),
		));

				$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SALE_ORDER_CANCEL",
			"NAME" => GetMessage("SMS4B_SALE_ORDER_CANCEL_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_ORDER_CANCEL_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_SALE_ORDER_PAID",
			"NAME" => GetMessage("SMS4B_SALE_ORDER_PAID_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_ORDER_PAID_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SALE_ORDER_PAID",
			"NAME" => GetMessage("SMS4B_SALE_ORDER_PAID_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_ORDER_PAID_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_SALE_ORDER_DELIVERY",
			"NAME" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SALE_ORDER_DELIVERY",
			"NAME" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_SALE_RECURRING_CANCEL",
			"NAME" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_DESC"),
		));

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SALE_RECURRING_CANCEL",
			"NAME" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_DESC"),
		));
		
		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "SMS4B_ADMIN_SEND",
			"NAME" => GetMessage("SMS4B_ADMIN_SEND_NAME"),
			"DESCRIPTION" => GetMessage("SMS4B_ADMIN_SEND_DESC"),
		));
		
		

		$arSites = array();
		$sites = CSite::GetList(($b=""), ($o=""), Array("LANGUAGE_ID"=>$lid));
		while ($site = $sites->Fetch())
			$arSites[] = $site["LID"];

		if(count($arSites) > 0)
		{
			$emess = new CEventMessage;
			$emess->Add(array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "SMS4B_ADMIN_SEND",
					"LID" => $arSites,
					"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
					"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
					"SUBJECT" => GetMessage("SMS4B_ADMIN_SEND_SUBJECT"),
					"MESSAGE" => GetMessage("SMS4B_ADMIN_SEND_MESSAGE"),
					"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "SMS4B_TASK_ADD",
					"LID" => $arSites,
					"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
					"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
					"SUBJECT" => GetMessage("SMS4B_TASK_ADD_SUBJECT"),
					"MESSAGE" => GetMessage("SMS4B_TASK_ADD_MESSAGE"),
					"BODY_TYPE" => "text",
			));


			$emess = new CEventMessage;
			$emess->Add(array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "SMS4B_TASK_UPDATE",
					"LID" => $arSites,
					"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
					"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
					"SUBJECT" => GetMessage("SMS4B_TASK_UPDATE_SUBJECT"),
					"MESSAGE" => GetMessage("SMS4B_TASK_UPDATE_MESSAGE"),
					"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "SMS4B_TASK_DELETE",
					"LID" => $arSites,
					"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
					"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
					"SUBJECT" => GetMessage("SMS4B_TASK_DELETE_SUBJECT"),
					"MESSAGE" => GetMessage("SMS4B_TASK_DELETE_MESSAGE"),
					"BODY_TYPE" => "text",
			));


			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_TICKET_NEW_FOR_TECHSUPPORT",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
				"EMAIL_TO" => "#PHONE_TO#",
				"SUBJECT" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_TICKET_NEW_FOR_TECHSUPPORT",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
				"EMAIL_TO" => "#PHONE_TO#",
				"SUBJECT" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
				"EMAIL_TO" => "#PHONE_TO#",
				"SUBJECT" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE"),
				"BODY_TYPE" => "text",
			));
			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
				"EMAIL_TO" => "#PHONE_TO#",
				"SUBJECT" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_SUBSCRIBE_CONFIRM",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
				"EMAIL_TO" => "#PHONE_TO#",
				"BCC" => "",
				"SUBJECT" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_SUBSCRIBE_CONFIRM",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
				"EMAIL_TO" => "#PHONE_TO#",
				"BCC" => "",
				"SUBJECT" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SUBSCRIBE_CONFIRM_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_SALE_NEW_ORDER",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_NEW_ORDER_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_NEW_ORDER_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_SALE_NEW_ORDER",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_NEW_ORDER_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_NEW_ORDER_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_SALE_ORDER_CANCEL",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_ORDER_CANCEL_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_ORDER_CANCEL_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_SALE_ORDER_CANCEL",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_ORDER_CANCEL_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_ORDER_CANCEL_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_SALE_ORDER_DELIVERY",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_SALE_ORDER_DELIVERY",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_ORDER_DELIVERY_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_SALE_ORDER_PAID",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_ORDER_PAID_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_ORDER_PAID_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_SALE_ORDER_PAID",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_ORDER_PAID_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_ORDER_PAID_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_SALE_RECURRING_CANCEL",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_MESSAGE"),
				"BODY_TYPE" => "text",
			));

			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "SMS4B_ADMIN_SALE_RECURRING_CANCEL",
				"LID" => $arSites,
				"EMAIL_FROM" => "#SALE_PHONE#",
				"EMAIL_TO" => "#PHONE_TO#",

				"SUBJECT" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_SUBJECT"),
				"MESSAGE" => GetMessage("SMS4B_SALE_RECURRING_CANCEL_MESSAGE"),
				"BODY_TYPE" => "text",
			));
		}
	}

	if (CModule::IncludeModule("sale"))
	{
		global $SMS4B;
		$dbStatus = CSaleStatus::GetList(
				array($by => $order),
				array(),
				false,
				false,
				array("ID", "SORT", "LID", "NAME", "DESCRIPTION")
			);
		while($arStatus = $dbStatus->Fetch())
		{
			$ID = $arStatus["ID"];
			$eventType = new CEventType;
			$eventMessage = new CEventMessage;

			$dbSiteList = CSite::GetList(($b = "sort"), ($o = "asc"));
			while ($arSiteList = $dbSiteList->Fetch())
			{
				$arStatusLang = CSaleStatus::GetLangByID($ID, $arSiteList["LANGUAGE_ID"]);

				$dbEventType = $eventType->GetList(
						array(
								"EVENT_NAME" => "SMS4B_SALE_STATUS_CHANGED_".$ID,
								"LID" => $arSiteList["LANGUAGE_ID"]
							)
					);
				if (!($arEventType = $dbEventType->Fetch()))
				{
					$str  = "";
					$str .= "#ORDER_ID# - ".GetMessage("SMS4B_ORDER_ID")."\n";
					$str .= "#ORDER_DATE# - ".GetMessage("SMS4B_ORDER_DATE")."\n";
					$str .= "#ORDER_STATUS# - ".GetMessage("SMS4B_ORDER_STATUS")."\n";
					$str .= "#PHONE_TO# - ".GetMessage("SMS4B_ORDER_PHONE")."\n";
					$str .= "#ORDER_DESCRIPTION# - ".GetMessage("SMS4B_STATUS_DESCR")."\n";
					$str .= "#TEXT# - ".GetMessage("SMS4B_STATUS_TEXT")."\n";
					$str .= "#SALE_PHONE# - ".GetMessage("SMS4B_SALE_PHONE");

					$eventTypeID = $eventType->Add(
							array(
									"LID" => $arSiteList["LANGUAGE_ID"],
									"EVENT_NAME" => "SMS4B_SALE_STATUS_CHANGED_".$ID,
									"NAME" => GetMessage("SMS4B_CHANGING_STATUS_TO")." ".$arStatusLang["NAME"],
									"DESCRIPTION" => $str
								)
						);
				}
				// ====
				$dbEventType = $eventType->GetList(
						array(
								"EVENT_NAME" => "SMS4B_ADMIN_SALE_STATUS_CHANGED_".$ID,
								"LID" => $arSiteList["LANGUAGE_ID"]
							)
					);
				if (!($arEventType = $dbEventType->Fetch()))
				{
					$str  = "";
					$str .= "#ORDER_ID# - ".GetMessage("SMS4B_ORDER_ID")."\n";
					$str .= "#ORDER_DATE# - ".GetMessage("SMS4B_ORDER_DATE")."\n";
					$str .= "#ORDER_STATUS# - ".GetMessage("SMS4B_ORDER_STATUS")."\n";
					$str .= "#PHONE_TO# - ".GetMessage("SMS4B_ORDER_PHONE")."\n";
					$str .= "#ORDER_DESCRIPTION# - ".GetMessage("SMS4B_STATUS_DESCR")."\n";
					$str .= "#TEXT# - ".GetMessage("SMS4B_STATUS_TEXT")."\n";
					$str .= "#SALE_PHONE# - ".GetMessage("SMS4B_SALE_PHONE");

					$eventTypeID = $eventType->Add(
							array(
									"LID" => $arSiteList["LANGUAGE_ID"],
									"EVENT_NAME" => "SMS4B_ADMIN_SALE_STATUS_CHANGED_".$ID,
									"NAME" => GetMessage("SMS4B_CHANGING_STATUS_TO")." ".$arStatusLang["NAME"],
									"DESCRIPTION" => $str
								)
						);
				}
				// ====

				$dbEventMessage = $eventMessage->GetList(
						($b = "sort"),
						($o = "asc"),
						array(
								"EVENT_NAME" => "SMS4B_SALE_STATUS_CHANGED_".$ID,
								"SITE_ID" => $arSiteList["LID"]
							)
					);
				if (!($arEventMessage = $dbEventMessage->Fetch()))
				{
					$subject = GetMessage("SMS4B_STATUS_PHONE_SUBJ");
					$message = GetMessage("SMS4B_STATUS_PHONE_BODY1").$arStatusLang["NAME"]."\n";
					$message .= "#ORDER_DESCRIPTION#\n";
					$message .= "#TEXT#";

					$arFields = Array(
						"ACTIVE" => "Y",
						"EVENT_NAME" => "SMS4B_SALE_STATUS_CHANGED_".$ID,
						"LID" => $arSiteList["LID"],
						"EMAIL_FROM" => "#SALE_PHONE#",
						"EMAIL_TO" => "#PHONE_TO#",
						"SUBJECT" => $subject,
						"MESSAGE" => $message,
						"BODY_TYPE" => "text"
					);
					$eventMessageID = $eventMessage->Add($arFields);
				}
				//====
				$dbEventMessage = $eventMessage->GetList(
						($b = "sort"),
						($o = "asc"),
						array(
								"EVENT_NAME" => "SMS4B_ADMIN_SALE_STATUS_CHANGED_".$ID,
								"SITE_ID" => $arSiteList["LID"]
							)
					);
				if (!($arEventMessage = $dbEventMessage->Fetch()))
				{
					$subject = GetMessage("SMS4B_STATUS_PHONE_SUBJ");
					$message = GetMessage("SMS4B_STATUS_PHONE_BODY1").$arStatusLang["NAME"]."\n";
					$message .= "#ORDER_DESCRIPTION#\n";
					$message .= "#TEXT#";

					$arFields = Array(
						"ACTIVE" => "Y",
						"EVENT_NAME" => "SMS4B_ADMIN_SALE_STATUS_CHANGED_".$ID,
						"LID" => $arSiteList["LID"],
						"EMAIL_FROM" => "#SALE_PHONE#",
						"EMAIL_TO" => "#PHONE_TO#",
						"SUBJECT" => $subject,
						"MESSAGE" => $message,
						"BODY_TYPE" => "text"
					);
					$eventMessageID = $eventMessage->Add($arFields);
				}
				//====
			}
		}
	}
}
?>