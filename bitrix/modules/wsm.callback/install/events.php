<?
$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("EVENT_NAME" => "WSM_CALLBACK_NOTICE"));
if(!($arEvent = $dbEvent->Fetch()))
{
	//if not found - crete mail event
	$langs = CLanguage::GetList(($b=""), ($o=""));

	while($lang = $langs->Fetch())
	{
		IncludeModuleLangFile(__FILE__, $lang["LID"]);

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lang["LID"],
			"EVENT_NAME" => "WSM_CALLBACK_NOTICE",
			"NAME" => GetMessage("WSM_CALLBACK_NOTICE_NAME"),
			"DESCRIPTION" => GetMessage("WSM_CALLBACK_NOTICE_DESC"),
		));
	}

	//site list
	$arSites = array();
	$sites = CSite::GetList(($b=""), ($o=""), array("LANGUAGE_ID"=>$lid));
	while ($site = $sites->Fetch())
		$arSites[] = $site;

	if(count($arSites) > 0)
	{
		foreach($arSites as $site)
		{
			IncludeModuleLangFile(__FILE__, $site["LANGUAGE_ID"]);
			
			//list property iblock
			$emess = new CEventMessage;

			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "WSM_CALLBACK_NOTICE",
				"LID" => $site["LID"],
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
				"EMAIL_TO" => "#NOTICE_EMAIL#",
				"SUBJECT" => GetMessage("WSM_CALLBACK_NOTICE_SUBJECT"),
				"MESSAGE" => GetMessage("WSM_CALLBACK_NOTICE_MESSAGE"),
				"BODY_TYPE" => "text",
			));
		}
	}
}
?>