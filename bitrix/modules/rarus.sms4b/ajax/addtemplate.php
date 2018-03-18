<?
include ($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("rarus.sms4b");
global $SMS4B;

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
	$arFilter = array(
		"ID" => intval($_GET['eventID']),
		"LID" => "ru"
	);

	$obEvents = CEventType::GetList($arFilter);
	if ($arEvent = $obEvents->Fetch())
	{
		$macro = $arEvent["DESCRIPTION"];
		//проверяем, есть ли шаблон СМС для
		$smsText = $SMS4B->GetEventTemplate("SMS4B_". $arEvent['EVENT_NAME'], $_GET["site"], "USER");
		$userPhone = $smsText["EMAIL_TO"];
		$smsAdminText = $SMS4B->GetEventTemplate("SMS4B_". $arEvent['EVENT_NAME'], $_GET["site"], "ADMIN");
	}
?>		<form method="POST" action="/bitrix/admin/sms4b_addtemplate.php" id="myForm">
			<table>
			<tr>
				<td><?=GetMessage("MACRO");?></td>
				<td><pre><?=$macro?></pre></td>
			</tr>
			<tr>
				<td><label><?=GetMessage("PHONE");?></label> </td>
				<td><input type = "text" name ="phone" value="<?=$userPhone?>"><span id="SPAN_ID"></span>
			</tr>
			<tr>
				<td><label><?=GetMessage("USER_SMS")?></label></td>
				<td><textarea name="smstemplate" style="height: 78px; width: 300px;"><?=$smsText["MESSAGE"]?></textarea></td>
			</tr>
			<tr>
				<td><label><?=GetMessage("ADMIN_SMS")?></label></td>
				<td><textarea name="smsadmintemplate" style="height: 78px; width: 300px;"><?=$smsAdminText["MESSAGE"]?></textarea></td>
			</tr>
			<input type = "hidden" name ="eventType" value="<?=$arEvent["EVENT_NAME"]?>">
			<input type = "hidden" name ="site" value="<?=$_GET["site"]?>">
		</form>
		
		<script>BX .hint_replace(BX('SPAN_ID'), '<?echo CUtil::JSEscape(GetMessage("SPAN_ID"))?>');</script>

<?
}
elseif($_SERVER["REQUEST_METHOD"] == "POST")
{
	$userSms = trim($_POST["smstemplate"]);
	$adminSms = trim($_POST["smsadmintemplate"]);
	$userPhone = trim($_POST['phone']);
	$eventType = trim($_POST['eventType']);
	$site = trim($_POST["site"]);

	//find tempalte 
	$arFilter = array(
		"TYPE_ID" => "SMS4B_" . $eventType,
	);

	$obTemplate  = CEventType::GetList($arFilter);
	$isTemplate = false;
	if ($arTemplate = $obTemplate->Fetch())
	{
		$isTemplate = true;
	}

	$smsTemplate = $SMS4B->GetEventTemplate("SMS4B_". $eventType, $site, "SMS4B_USER");
	$smsAdminTemplate = $SMS4B->GetEventTemplate("SMS4B_". $eventType, $site, "SMS4B_ADMIN");

	//нет текста для обоих сообщений. Удаляем тип
	if (empty($userSms) && empty($adminSms))
	{
		CEventMessage::Delete($smsTemplate['ID']);
		CEventMessage::Delete($smsAdminTemplate['ID']);
		$error[] = GetMessage("TEMPLATE_DELETE_ADMIN");
		$et = new CEventType;
		$et->Delete("SMS4B_" . $eventType);
		$error[] = getMessage("EVENT_DELETE") . "SMS4B_". $eventType . $et->LAST_ERROR;
	}
	//add
	elseif ( !$isTemplate )
	{
		$et = new CEventType;
		$arFilter = array(
			"TYPE_ID" => $eventType,
			"LID" => "ru"
		);
		$obTemplate  = CEventType::GetList($arFilter);
		if ($arTemplate = $obTemplate->Fetch())
		{
			$arFields = array(
				"LID" => "ru",
				"EVENT_NAME" => "SMS4B_" . $eventType,
				"NAME" => $arTemplate["NAME"],
				"DESCRIPTION" => $arTemplate["DESCRIPTION"]
			);

			if (!$et->Add($arFields))
			{
				$error[] = GetMessage("EVENT_ADD_ERROR") . $el->LAST_ERROR;
			}
			else
			{
				$error[] = GetMessage("EVENT_ADD") . $eventType;
				if (!empty($userSms))
				{
					$arr = array(
						"ACTIVE" => "Y",
						"EVENT_NAME" => "SMS4B_" . $eventType,
						"LID" => $site,
						"EMAIL_FROM" => "SMS4B_USER",
						"EMAIL_TO" => $phone,
						"SUBJECT" => $arTemplate["NAME"],
						"BODY_TYPE" => "text",
						"MESSAGE" => $userSms
					);

					$obSMSTemplate = new CEventMessage;
					if ( $obSMSTemplate->Add($arr))
					{
						$error[] = GetMessage("TEMPLATE_ADD");
					}
					else
					{
						$error[] = GetMessage("TEMPLATE_ADD_ERROR");
					}
				}
				if (!empty($adminSms))
				{
					$arr = array(
						"ACTIVE" => "Y",
						"EVENT_NAME" => "SMS4B_" . $eventType,
						"LID" => $site,
						"EMAIL_FROM" => "SMS4B_ADMIN",
						"BODY_TYPE" => "text",
						"SUBJECT" => $arTemplate["NAME"],
						"MESSAGE" => $adminSms
					);
					$obSMSTemplate = new CEventMessage;
					if ( $obSMSTemplate->Add($arr))
					{
						$error[] = GetMessage("TEMPLATE_ADD_ADMIN");
					}
					else
					{
						$error[] = GetMessage("TEMPLATE_ADD_ADMIN_ERROR");
					}
				}
			}
		}
	}
	elseif ( $isTemplate )
	{
		if( empty($userSms)	&&  !empty($smsTemplate))
		{
			CEventMessage::Delete($smsTemplate['ID']);
			$error[] = GetMessage("TEMPLATE_DELETE");
		}
		elseif ( !empty($userSms))
		{
			$em = new CEventMessage;
			$arFields = Array(
				"EMAIL_TO" => $userPhone,
				"MESSAGE" => $userSms
			);
			if (empty($smsTemplate['ID']))
			{
				$arr = array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "SMS4B_" . $eventType,
					"LID" => $site,
					"EMAIL_FROM" => "SMS4B_USER",
					"EMAIL_TO" => $phone,
					"BODY_TYPE" => "text",
					"SUBJECT" => $arTemplate["NAME"],
					"MESSAGE" => $userSms
				);
				$obSMSTemplate = new CEventMessage;
				if ( $obSMSTemplate->Add($arr))
				{
					$error[] = GetMessage("TEMPLATE_ADD");
				}
				else
				{
					$error[] = GetMessage("TEMPLATE_ADD_ERROR");
				}
			}
			else
			{
				if($em->Update($smsTemplate['ID'], $arFields))
				{
					$error[] = GetMessage("TEMPLATE_UPDATE");
				}
				else
				{
					$error[] = GetMessage("TEMPLATE_UPDATE_ERROR");
				}
			}
		}
		if( empty($adminSms)	&&  !empty($smsAdminTemplate))
		{
			CEventMessage::Delete($smsAdminTemplate['ID']);
			$error[] = GetMessage("TEMPLATE_DELETE_ADMIN");
		}
		elseif ( !empty($adminSms))
		{
			$em = new CEventMessage;
			$arFields = Array(
				"MESSAGE" => $adminSms
			);
			if (empty($smsAdminTemplate['ID']))
			{
				$arr = array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "SMS4B_" . $eventType,
					"LID" => $site,
					"EMAIL_FROM" => "SMS4B_ADMIN",
					"BODY_TYPE" => "text",
					"SUBJECT" => $arTemplate["NAME"],
					"MESSAGE" => $adminSms
				);
				$obSMSTemplate = new CEventMessage;
				if ( $obSMSTemplate->Add($arr))
				{
					$error[] = GetMessage("TEMPLATE_ADD_ADMIN");
				}
				else
				{
					$error[] = GetMessage("TEMPLATE_ADD_ADMIN_ERROR");
				}
			}
			else
			{
				if($em->Update($smsAdminTemplate['ID'], $arFields))
				{
					$error[] = GetMessage("TEMPLATE_UPDATE_ADMIN");
				}
				else
				{
					$error[] = GetMessage("TEMPLATE_UPDATE_ADMIN_ERROR");
				}
			}
		}
	}
	echo implode("<br/>", $error);
}
?>