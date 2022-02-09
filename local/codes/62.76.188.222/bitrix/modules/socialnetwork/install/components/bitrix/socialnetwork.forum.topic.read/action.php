<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("forum"))
	return 0;
$this->IncludeComponentLang("action.php");
$action = strtoupper($arParams["ACTION"]);
$action = ($action == "SUPPORT" ? "FORUM_MESSAGE2SUPPORT" : $action);

if (strLen($action) <= 0)
{
}
elseif (!check_bitrix_sessid())
{
	$arError[] = array(
		"id" => "bad_sessid", 
		"text" => GetMessage("F_ERR_SESS_FINISH")
	);
}
elseif ($_REQUEST["MESSAGE_MODE"] == "VIEW")
{
	$arResult["VIEW"] = "Y";
	$bVarsFromForm = true;
/************** Preview message ************************************/
	$arAllow["SMILES"] = ($_POST["USE_SMILES"]!="Y" ? "N" : "Y" );

	$arResult["POST_MESSAGE_VIEW"] = $parser->convert($_POST["POST_MESSAGE"], $arAllow);
	$arResult["MESSAGE_VIEW"]["AUTHOR_NAME"] = ($USER->IsAuthorized() || empty($_POST["AUTHOR_NAME"]) ? $arResult["USER"]["SHOW_NAME"] : trim($_POST["AUTHOR_NAME"]));
	$arResult["MESSAGE_VIEW"]["TEXT"] = $arResult["POST_MESSAGE_VIEW"];
	$arFields = array(
		"FORUM_ID" => intVal($arParams["FID"]), 
		"TOPIC_ID" => intVal($arParams["TID"]), 
		"MESSAGE_ID" => intVal($arParams["MID"]), 
		"USER_ID" => intVal($GLOBALS["USER"]->GetID()));
	$arFiles = array();
	$arFilesExists = array();
	$res = array();
	
	foreach ($_FILES as $key => $val):
		if (substr($key, 0, strLen("FILE_NEW")) == "FILE_NEW" && !empty($val["name"])):
			$arFiles[] = $_FILES[$key];
		endif;
	endforeach;
	foreach ($_REQUEST["FILES"] as $key => $val) 
	{
		if (!in_array($val, $_REQUEST["FILES_TO_UPLOAD"]))
		{
			$arFiles[$val] = array("FILE_ID" => $val, "del" => "Y");
			unset($_REQUEST["FILES"][$key]);
			unset($_REQUEST["FILES_TO_UPLOAD"][$key]);
		}
		else 
		{
			$arFilesExists[$val] = array("FILE_ID" => $val);
		}
	}
	if (!empty($arFiles))
	{
		$res = CForumFiles::Save($arFiles, $arFields);
		$res1 = $GLOBALS['APPLICATION']->GetException();
		if ($res1):
			$strErrorMessage .= $res1->GetString();
		endif;
	}
	$res = is_array($res) ? $res : array();
	foreach ($res as $key => $val)
		$arFilesExists[$key] = $val;
	$arFilesExists = array_keys($arFilesExists);
	sort($arFilesExists);
	$arResult["MESSAGE_VIEW"]["FILES"] = $_REQUEST["FILES"] = $arFilesExists;	
}
else
{
	$arSonetAllow = array(
		"HTML" => "N",
		"ANCHOR" => "N",
		"BIU" => "N",
		"IMG" => "N",
		"LIST" => "N",
		"QUOTE" => "N",
		"CODE" => "N",
		"FONT" => "N",
		"UPLOAD" => $arAllow["UPLOAD"],
		"NL2BR" => "N",
		"SMILES" => "N"
	);

	$arFields = array(
		"PERMISSION_EXTERNAL" => $arParams["PERMISSION"], 
		"PERMISSION" => $arParams["PERMISSION"]);

	$url = false; $code = false;
	$message = (!empty($_REQUEST["MID_ARRAY"]) ? $_REQUEST["MID_ARRAY"] : $_REQUEST["MID"]);
	if ((empty($message) || $message == "s") && !empty($_REQUEST["message_id"]))
		$message = $_REQUEST["message_id"];
	if ((empty($message) || $message == "s") && !empty($arParams["MID"]))
		$message = $arParams["MID"];

	switch ($action)
	{
		case "EDIT_TOPIC":
			$MID = 0;
			$db_res = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$arParams["TID"]), false, 1);
			if (($db_res) && ($res = $db_res->Fetch()))
				$MID = intVal($res["ID"]);
			if ($MID > 0)
			{
				$url = ForumAddPageParams(
					CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_TOPIC_EDIT"], 
						array("FID" => $arParams["FID"], "TID" => $arParams["TID"], "MID" => $MID, "MESSAGE_TYPE" => "EDIT")), 
					array("TID" => $arParams["TID"], "MID" => $MID, "MESSAGE_TYPE" => "EDIT", "sessid" => bitrix_sessid()), false, false);
				LocalRedirect($url);
			}
			break;
		case "REPLY":
			$arFields = array(
				"FID" => $arParams["FID"],
				"TID" => $arParams["TID"],
				"POST_MESSAGE" => $_POST["POST_MESSAGE"],
				"AUTHOR_NAME" => $_POST["AUTHOR_NAME"],
				"AUTHOR_EMAIL" => $_POST["AUTHOR_EMAIL"],
				"USE_SMILES" => $_POST["USE_SMILES"],
				"ATTACH_IMG" => $_FILES["ATTACH_IMG"],
				"captcha_word" =>  $_POST["captcha_word"],
				"captcha_code" => $_POST["captcha_code"],
				"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"]);
				if (!empty($_FILES["ATTACH_IMG"]))
				{
					$arFields["ATTACH_IMG"] = $_FILES["ATTACH_IMG"]; 
				}
				else
				{
					$arFiles = array();
					if (!empty($_REQUEST["FILES"]))
					{
						foreach ($_REQUEST["FILES"] as $key):
							$arFiles[$key] = array("FILE_ID" => $key);
							if (!in_array($key, $_REQUEST["FILES_TO_UPLOAD"]))
								$arFiles[$key]["del"] = "Y";
						endforeach;
					}
					if (!empty($_FILES))
					{
						$res = array();
						foreach ($_FILES as $key => $val):
							if (substr($key, 0, strLen("FILE_NEW")) == "FILE_NEW" && !empty($val["name"])):
								$arFiles[] = $_FILES[$key];
							endif;
						endforeach;
					}
					if (!empty($arFiles))
						$arFields["FILES"] = $arFiles; 
				}
				$url = CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_MESSAGE"], 
					array("FID" => $arParams["FID"], "TID" => $arParams["TID"], "MID"=>"#result#"));
			break;
		case "VOTE4USER":
			return false;
			$arFields = array(
				"UID" => $_GET["UID"],
				"VOTES" => $_GET["VOTES"],
				"VOTE" => (($_GET["VOTES_TYPE"]=="U") ? True : False));
			$url = CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_MESSAGE"], 
				array("FID" => $arParams["FID"], "TID" => $arParams["TID"], 
					"MID" => (intVal($_REQUEST["MID"]) > 0 ? $_REQUEST["MID"] : "s")));
			break;
		case "HIDE":
		case "SHOW":
		case "FORUM_MESSAGE2SUPPORT":
			$arFields = array("MID" => $message);
			$mid = (is_array($message) ? $message[0] : $message);
			$url = CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_MESSAGE"], 
					array("FID" => $arParams["FID"], "TID" => $arParams["TID"], "MID" => (!empty($mid) ? $mid : "s")));
			if ($action == "FORUM_MESSAGE2SUPPORT")
			{
				$url = "/bitrix/admin/ticket_edit.php?ID=#result#&amp;lang=".LANGUAGE_ID;
			}
			break;
		case "DEL":
		case "SPAM":
			$arFields = array("MID" => $message, "PERMISSION" => $arParams["PERMISSION"]);
			$url = CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_MESSAGE"], 
					array("FID" => $arParams["FID"], "TID" => $arParams["TID"], "MID" => "#MID#"));
			break;
		case "SET_ORDINARY":
		case "SET_TOP":
		case "STATE_Y":
		case "STATE_N":
			if ($action == "STATE_Y")
				$action = "OPEN";
			elseif ($action == "STATE_N")
				$action = "CLOSE";
			elseif ($action == "SET_ORDINARY")
				$action = "ORDINARY";
			else 
				$action = "TOP";
				
			$arFields = array("TID" => $arParams["TID"]);
			$url = CComponentEngine::MakePathFromTemplate(
				$arParams["~URL_TEMPLATES_MESSAGE"], 
				array("FID" => $arParams["FID"], 
					"TID" => $arParams["TID"], 
					"MID" => ($arParams["MID"] > 0 ? $arParams["MID"] : "s")));
			break;
		case "HIDE_TOPIC":
		case "SHOW_TOPIC":
			$arFields = array("TID" => $arParams["TID"]);
			$url = CComponentEngine::MakePathFromTemplate(
				$arParams["~URL_TEMPLATES_MESSAGE"], 
				array("FID" => $arParams["FID"], 
					"TID" => $arParams["TID"], 
					"MID" => ($arParams["MID"] > 0 ? $arParams["MID"] : "s")));
			break;
		case "SPAM_TOPIC":
		case "DEL_TOPIC":
			$arFields = array("TID" => $arParams["TID"]);
			$url = CComponentEngine::MakePathFromTemplate(
				$arParams["~URL_TEMPLATES_TOPIC_LIST"], 
				array("FID" => $arParams["FID"]));
			break;
	}
	$strErrorMessage = ""; $strOKMessage = ""; $res = false;
	$arFields["PERMISSION_EXTERNAL"] = $arParams["PERMISSION"];
	$arFields["PERMISSION"] = $arParams["PERMISSION"];
	
	$arLogID_Del = array();
	$arLogCommentID_Del = array();
	switch ($action)
	{
		case "DEL":
		case "HIDE":
			// delete message log record
			$dbRes = CSocNetLogComments::GetList(
				array("ID" => "DESC"),
				array(
					"EVENT_ID" => "forum",
					"SOURCE_ID" => $arFields["MID"]
				),
				false,
				false,
				array("ID")
			);
			while ($arRes = $dbRes->Fetch())
				$arLogCommentID_Del[] = $arRes["ID"];
			break;
		case "DEL_TOPIC":
		case "HIDE_TOPIC":
			if (!is_array($arFields["TID"]))
				$arTID = array($arFields["TID"]);
			else
				$arTID = $arFields["TID"];

			$arLogID_Del = array();
			foreach($arTID as $topic_id_tmp)
			{
				// delete message log records
				$dbForumMessage = CForumMessage::GetList(
					array("ID" => "ASC"),
					array("TOPIC_ID" => $topic_id_tmp)
				);
				while ($arForumMessage = $dbForumMessage->Fetch())
				{
					$dbRes = CSocNetLog::GetList(
						array("ID" => "DESC"),
						array(
							"EVENT_ID" => "forum",
							"SOURCE_ID" => $arForumMessage["ID"]
						),
						false,
						false,
						array("ID")
					);
					while ($arRes = $dbRes->Fetch())
						$arLogID_Del[] = $arRes["ID"];
				}
			}
			break;
	}

	$actionResult = $res = ForumActions($action, $arFields, $strErrorMessage, $strOKMessage);

	if ($res)
	{
		// check out not hidden topic messages
		$iApprovedMessagesCnt = CForumMessage::GetList(array(), array("TOPIC_ID"=>$arParams["TID"], "APPROVED"=>"Y"), true);
		if ($iApprovedMessagesCnt <= 0)
		{
			$rsForumMessage = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$arParams["TID"]), false, 1);		
			if ($arForumMessage = $rsForumMessage->Fetch())
			{
				$dbLogRes = CSocNetLog::GetList(
					array("ID" => "DESC"),
					array(
						"EVENT_ID" => "forum",
						"SOURCE_ID" => $arForumMessage["ID"]
					),
					false,
					false,
					array("ID")
				);		
				if ($arLogRes = $dbLogRes->Fetch())
					$arLogID_Del[] = $arLogRes["ID"];
			}
		}

		foreach($arLogID_Del as $log_id)
			CSocNetLog::Delete($log_id);
		foreach($arLogCommentID_Del as $log_comment_id)
			CSocNetLogComments::Delete($log_comment_id);
	}

	if (!empty($strErrorMessage))
	{
		$arError[] = array(
			"id" => $action, 
			"text" => $strErrorMessage
		);
	}
	elseif ($action == "DEL" || $action == "SPAM")
	{
		$arFields = CForumTopic::GetByID($arParams["TID"]);
		if (empty($arFields))
		{
			$url = CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_TOPIC_LIST"], array("FID" => $arParams["FID"]));
			$action = "del_topic";
		}
		else 
		{
			$res = intVal($message); $mid = "s";
			if (is_array($message)):
				sort($message);
				$res = array_pop($message);
			endif;
			$arFilter = array("TOPIC_ID" => $arParams["TID"], ">ID" => $res);
			if ($arParams["PERMISSION"] < "Q"): 
				$arFilter["APPROVED"] = "Y";
			endif;
			
			$db_res = CForumMessage::GetList(array("ID" => "ASC"), $arFilter);
			if ($db_res && $res = $db_res->Fetch())
				$mid = $res["ID"];
			$url = str_replace("#MID#", $mid, $url);
		}
	}
	elseif ($action == "REPLY" || $action == "SHOW")
	{
		if ($action == "REPLY")
			$arParams["MID"] = intVal($res);

		$result = CForumMessage::GetByIDEx($arParams["MID"], array("GET_TOPIC_INFO" => "Y"));
		$arResult["MESSAGE"] = $result;
		if (is_array($result) && !empty($result))
		{
			$arParams["TID"] = intVal($result["TOPIC_ID"]);
			if ($arParams["AUTOSAVE"])
				$arParams["AUTOSAVE"]->Reset();
			$sText = (COption::GetOptionString("forum", "FILTER", "Y") == "Y" ? $result["POST_MESSAGE_FILTER"] : $result["POST_MESSAGE"]);
			if ($arParams["MODE"] == "GROUP")
				CSocNetGroup::SetLastActivity($arParams["SOCNET_GROUP_ID"]);

			// calculate root MID
			$dbForumMessage = CForumMessage::GetList(
				array("ID" => "ASC"),
				array("TOPIC_ID" => $arParams["TID"])
			);
			if ($arForumMessage = $dbForumMessage->Fetch())
			{
				$dbRes = CSocNetLog::GetList(
					array("ID" => "DESC"),
					array(
						"EVENT_ID" => "forum",
						"SOURCE_ID" => $arForumMessage["ID"]
					),
					false,
					false,
					array("ID", "TMP_ID")
				);
				if ($arRes = $dbRes->Fetch())
					$log_id = $arRes["TMP_ID"];
				else
				{
					// get root message
					$dbFirstMessage = CForumMessage::GetList(
						array("ID" => "ASC"), 
						array("TOPIC_ID" => $arParams["TID"]),
						false,
						1
					);
					if ($arFirstMessage = $dbFirstMessage->Fetch())
					{
						$arTopic = CForumTopic::GetByID($arFirstMessage["TOPIC_ID"]);
						$sFirstMessageText = (COption::GetOptionString("forum", "FILTER", "Y") == "Y" ? $arFirstMessage["POST_MESSAGE_FILTER"] : $arFirstMessage["POST_MESSAGE"]);
						
						$sFirstMessageURL = CComponentEngine::MakePathFromTemplate(
							$arParams["~URL_TEMPLATES_MESSAGE"], 
							array(
								"UID" => $arFirstMessage["AUTHOR_ID"], 
								"FID" => $arFirstMessage["FORUM_ID"], 
								"TID" => $arFirstMessage["TOPIC_ID"], 
								"MID" => $arFirstMessage["ID"]
							)
						);

						$arFieldsForSocnet = array(
							"ENTITY_TYPE" => ($arParams["MODE"] == "GROUP" ? SONET_ENTITY_GROUP : SONET_ENTITY_USER),
							"ENTITY_ID" => ($arParams["MODE"] == "GROUP" ? $arParams["SOCNET_GROUP_ID"] : $arParams["USER_ID"]),
							"EVENT_ID" => "forum",
							"LOG_DATE" => $arFirstMessage["POST_DATE"],
							"LOG_UPDATE" => $arFirstMessage["POST_DATE"],
							"TITLE_TEMPLATE" => str_replace("#AUTHOR_NAME#", $arFirstMessage["AUTHOR_NAME"], GetMessage("SONET_FORUM_LOG_TOPIC_TEMPLATE")),
							"TITLE" => $arTopic["TITLE"],								
							"MESSAGE" => $parser->convert($sFirstMessageText, $arSonetAllow),
							"TEXT_MESSAGE" => $parser->convert4mail($sFirstMessageText),
							"URL" => $sFirstMessageURL,
							"PARAMS" => serialize(array("PATH_TO_MESSAGE" => CComponentEngine::MakePathFromTemplate($arParams["~URL_TEMPLATES_MESSAGE"], array("TID" => $arParams["TID"])))),
							"MODULE_ID" => false,
							"CALLBACK_FUNC" => false,
							"SOURCE_ID" => $arFirstMessage["ID"],
							"RATING_TYPE_ID" => "FORUM_TOPIC",
							"RATING_ENTITY_ID" => intval($arParams["TID"])
						);

						if (intVal($arFirstMessage["AUTHOR_ID"]) > 0)
							$arFieldsForSocnet["USER_ID"] = $arFirstMessage["AUTHOR_ID"];

						$log_id = CSocNetLog::Add($arFieldsForSocnet, false);
						if (intval($log_id) > 0)
						{
							CSocNetLog::Update($log_id, array("TMP_ID" => $log_id));
							CSocNetLogRights::SetForSonet($log_id, ($arParams["MODE"] == "GROUP" ? SONET_ENTITY_GROUP : SONET_ENTITY_USER), ($arParams["MODE"] == "GROUP" ? $arParams["SOCNET_GROUP_ID"] : $arParams["USER_ID"]), "forum", "view");
						}
					}
				}
				
				if (intval($log_id) > 0)
				{
					$arFieldsForSocnet = array(
						"ENTITY_TYPE" => ($arParams["MODE"] == "GROUP" ? SONET_ENTITY_GROUP : SONET_ENTITY_USER),
						"ENTITY_ID" => ($arParams["MODE"] == "GROUP" ? $arParams["SOCNET_GROUP_ID"] : $arParams["USER_ID"]),
						"EVENT_ID" => "forum",
						"=LOG_DATE" => $GLOBALS["DB"]->CurrentTimeFunction(),
						"MESSAGE" => $parser->convert($sText, $arSonetAllow),
						"TEXT_MESSAGE" => $parser->convert4mail($sText),
						"URL" => str_replace("#result#", $arParams["MID"], $url),
						"MODULE_ID" => false,
						"SOURCE_ID" => $arParams["MID"],
						"LOG_ID" => $log_id,
						"RATING_TYPE_ID" => "FORUM_POST",
						"RATING_ENTITY_ID" => intval($arParams["MID"])
					);

					if (intVal($arResult["MESSAGE"]["AUTHOR_ID"]) > 0)
						$arFieldsForSocnet["USER_ID"] = $arResult["MESSAGE"]["AUTHOR_ID"];

					CSocNetLogComments::Add($arFieldsForSocnet);
				}
			}
		}
		$res = $arParams["MID"];
	}
	if (!$res)
	{
		$bVarsFromForm = true;
	}
	else 
	{
		$arNote = array(
			"code" => $action,
			"title" => $strOKMessage, 
			"link" => $url);
	}
	$arResult['RESULT'] = $res;
	if (isset($_REQUEST['AJAX_CALL']) && in_array($action, array('SHOW', 'HIDE', 'DEL')))
	{
		$GLOBALS['APPLICATION']->RestartBuffer();
		$arRes = array('status' => (!($actionResult === false)), 'message' => ( (!($actionResult===false)) ? $strOKMessage : $strErrorMessage));
		echo CUtil::PhpToJSObject($arRes);
		die();
	}
	if (empty($arError) && !($arParams['AJAX_POST'] == 'Y' && $action == 'REPLY'))
	{
		$url = str_replace("#result#", $res, $url);
		LocalRedirect(ForumAddPageParams($url, array("result" => strtolower($action)), true, false).(!empty($arParams["MID"]) ? "#message".$arParams["MID"] : ""));	
	}
}
if (!empty($arError))
{
	$bVarsFromForm = true;
}

?>
