<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

WizardServices::IncludeServiceLang(pathinfo(__FILE__, PATHINFO_BASENAME));

$typeSettings = array (
		'CITRUS_REALTY_NEW_QUESTION' => 
		array (
			'LID' => 'ru',
			'EVENT_NAME' => 'CITRUS_REALTY_NEW_QUESTION',
			'NAME' => GetMessage("CITRUS_REALTY_NEW_QUESTION_NAME"),
			'DESCRIPTION' => GetMessage("CITRUS_REALTY_NEW_QUESTION_DESCRIPTION"),
			'SORT' => '150',
			'MESSAGES' => 
			array (
				0 => 
				array (
					'EVENT_NAME' => 'CITRUS_REALTY_NEW_QUESTION',
					'ACTIVE' => 'Y',
					'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
					'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
					'SUBJECT' => GetMessage("CITRUS_REALTY_NEW_QUESTION_MESSAGES_0_SUBJECT"),
					'MESSAGE' => GetMessage("CITRUS_REALTY_NEW_QUESTION_MESSAGES_0_MESSAGE"),
					'BODY_TYPE' => 'text',
					'REPLY_TO' => '#PROPERTY_author_address#',
				),
			),
		),
		'CITRUS_REALTY_NEW_REQUEST' => 
		array (
			'LID' => 'ru',
			'EVENT_NAME' => 'CITRUS_REALTY_NEW_REQUEST',
			'NAME' => GetMessage("CITRUS_REALTY_NEW_REQUEST_NAME"),
			'DESCRIPTION' => GetMessage("CITRUS_REALTY_NEW_REQUEST_DESCRIPTION"),
			'SORT' => '150',
			'MESSAGES' => 
			array (
				0 => 
				array (
					'EVENT_NAME' => 'CITRUS_REALTY_NEW_REQUEST',
					'ACTIVE' => 'Y',
					'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
					'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
					'SUBJECT' => GetMessage("CITRUS_REALTY_NEW_REQUEST_MESSAGES_0_SUBJECT"),
					'MESSAGE' => GetMessage("CITRUS_REALTY_NEW_REQUEST_MESSAGES_0_MESSAGE"),
					'BODY_TYPE' => 'text',
				),
			),
		),
		"CITRUS_REALTY_SHARE" => 
		array (
			'LID' => 'ru',
			'EVENT_NAME' => 'CITRUS_REALTY_SHARE',
			'NAME' => GetMessage("CITRUS_REALTY_SHARE_NAME"),
			'DESCRIPTION' => GetMessage("CITRUS_REALTY_SHARE_DESCRIPTION"),
			'SORT' => '150',
			'MESSAGES' => 
			array(
				0 => 
				array(
					'EVENT_NAME' => 'CITRUS_REALTY_SHARE',
					'ACTIVE' => 'Y',
					"FIELD1_NAME" => "Sender",
					"FIELD1_VALUE" => "#HEADER_SENDER#",
					"EMAIL_FROM" => "#EMAIL_FROM#",
					"EMAIL_TO" => "#EMAIL_TO#",
					"SUBJECT" => GetMessage("CITRUS_REALTY_SHARE_MESSAGES_0_SUBJECT"),
					"MESSAGE" => GetMessage("CITRUS_REALTY_SHARE_MESSAGES_0_MESSAGE"),
					"BODY_TYPE" => "text",
					/*"ADDITIONAL_FIELD" => array(
						array("NAME" => "Sender", "VALUE" => "#HEADER_SENDER#"),
					),*/
				),
			),
		),
	);

$obEventType = new CEventType();
$obEventMessage = new CEventMessage();
foreach ($typeSettings as $eventType => $type)
{
	$arEventTypeFields = $type;
	unset($arEventTypeFields["MESSAGES"]);

	$arEventType = CEventType::GetList(Array("EVENT_NAME" => $eventType, "LID" => LANGUAGE_ID))->Fetch();
	if (is_array($arEventType))
		$bSuccess = $obEventType->Update(Array("ID" => $arEventType["ID"]), $arEventTypeFields);
	else
	{
		$bSuccess = $obEventType->Add($arEventTypeFields) > 0;
		if ($bSuccess)
		{
			// создание/обновление почтовых шаблонов
			foreach ($type["MESSAGES"] as $arTemplate)
			{
				$arTemplate["EVENT_NAME"] = $eventType;
				$arTemplate["LID"] = WIZARD_SITE_ID;

				if (!array_key_exists("EMAIL_FROM", $arTemplate))
					$arTemplate["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
				if (!array_key_exists("EMAIL_TO", $arTemplate))
					$arTemplate["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
				if (!array_key_exists("BODY_TYPE", $arTemplate))
					$arTemplate["BODY_TYPE"] = "text";
				if (!array_key_exists("ACTIVE", $arTemplate))
					$arTemplate["ACTIVE"] = "Y";

				$bSuccess = $obEventMessage->Add($arTemplate) > 0;

				if (!$bSuccess)
				{
					echo "CEventMessage error: " . $obEventMessage->LAST_ERROR;
					die();
				}
			}
		}
	}

	if (!$bSuccess)
	{
		echo "CEventType error: " . $obEventType->LAST_ERROR;
		die();
	}
}