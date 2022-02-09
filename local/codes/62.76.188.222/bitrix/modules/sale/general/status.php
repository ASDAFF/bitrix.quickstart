<?
IncludeModuleLangFile(__FILE__);
$GLOBALS["SALE_STATUS"] = Array();

class CAllSaleStatus
{
	function GetLangByID($ID, $strLang = LANGUAGE_ID)
	{
		global $DB;
		$ID = $DB->ForSql($ID, 1);
		$strLang = $DB->ForSql($strLang, 2);
		
		if (isset($GLOBALS["SALE_STATUS"]["SALE_STATUS_LANG_CACHE_".$ID."_".$strLang]) && is_array($GLOBALS["SALE_STATUS"]["SALE_STATUS_LANG_CACHE_".$ID."_".$strLang]) && is_set($GLOBALS["SALE_STATUS"]["SALE_STATUS_LANG_CACHE_".$ID."_".$strLang], "STATUS_ID"))
		{
			return $GLOBALS["SALE_STATUS"]["SALE_STATUS_LANG_CACHE_".$ID."_".$strLang];
		}
		else
		{
			$strSql =
				"SELECT * ".
				"FROM b_sale_status_lang ".
				"WHERE STATUS_ID = '".$ID."' ".
				"	AND LID = '".$strLang."' ";
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			if ($res = $db_res->Fetch())
			{
				$GLOBALS["SALE_STATUS"]["SALE_STATUS_LANG_CACHE_".$ID."_".$strLang] = $res;
				return $res;
			}
		}
		return False;
	}


	function CheckFields($ACTION, &$arFields, $ID = "")
	{
		global $DB;

		if ((is_set($arFields, "SORT") || $ACTION=="ADD") && IntVal($arFields["SORT"])<="Y") $arFields["SORT"] = 100;
		if ((is_set($arFields, "ID") || $ACTION=="ADD") && strlen($arFields["ID"])<=0) return false;

		if (is_set($arFields, "ID") && strlen($ID)>0 && $ID!=$arFields["ID"]) return false;
		
		if((is_set($arFields, "ID") && !preg_match("#[A-Za-z]#i", $arFields["ID"])) || (strlen($ID)>0 && !preg_match("#[A-Za-z]#i", $ID)))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGS_ID_NOT_SYMBOL"), "ERROR_ID_NOT_SYMBOL");
			return false;
		}

		if ($ACTION=="ADD")
		{
			$arFields["ID"] = $DB->ForSql($arFields["ID"], 1);
			$db_res = $DB->Query("SELECT ID FROM b_sale_status WHERE ID = '".$arFields["ID"]."' ");
			if ($db_res->Fetch()) return false;
		}

		if (is_set($arFields, "LANG"))
		{
			$db_lang = CLangAdmin::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));
			while ($arLang = $db_lang->Fetch())
			{
				$bFound = False;
				for ($i = 0; $i<count($arFields["LANG"]); $i++)
				{
					if ($arFields["LANG"][$i]["LID"]==$arLang["LID"] && strlen($arFields["LANG"][$i]["NAME"])>0)
					{
						$bFound = True;
					}
				}
				if (!$bFound) return false;
			}
		}

		return True;
	}

	function Add($arFields)
	{
		global $DB;

		if (!CSaleStatus::CheckFields("ADD", $arFields))
			return false;

		$ID = $DB->ForSql($arFields["ID"], 1);

		$db_events = GetModuleEvents("sale", "OnBeforeStatusAdd");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, Array($ID, &$arFields))===false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sale_status", $arFields);
		$strSql =
			"INSERT INTO b_sale_status(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		for ($i = 0; $i < count($arFields["LANG"]); $i++)
		{
			$arInsert = $DB->PrepareInsert("b_sale_status_lang", $arFields["LANG"][$i]);
			$strSql =
				"INSERT INTO b_sale_status_lang(STATUS_ID, ".$arInsert[0].") ".
				"VALUES('".$ID."', ".$arInsert[1].")";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		if (isset($arFields["PERMS"]) && is_array($arFields["PERMS"]))
		{
			for ($i = 0; $i < count($arFields["PERMS"]); $i++)
			{
				$arInsert = $DB->PrepareInsert("b_sale_status2group", $arFields["PERMS"][$i]);
				$strSql =
					"INSERT INTO b_sale_status2group(STATUS_ID, ".$arInsert[0].") ".
					"VALUES('".$ID."', ".$arInsert[1].")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		$events = GetModuleEvents("sale", "OnStatusAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, Array($ID, $arFields));

		return $ID;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = $DB->ForSql($ID, 1);
		if (!CSaleStatus::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$db_events = GetModuleEvents("sale", "OnBeforeStatusUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, Array($ID, &$arFields))===false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_status", $arFields);
		$strSql = "UPDATE b_sale_status SET ".$strUpdate." WHERE ID = '".$ID."' ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (is_set($arFields, "LANG"))
		{
			$DB->Query("DELETE FROM b_sale_status_lang WHERE STATUS_ID = '".$ID."'");

			for ($i = 0; $i<count($arFields["LANG"]); $i++)
			{
				$arInsert = $DB->PrepareInsert("b_sale_status_lang", $arFields["LANG"][$i]);
				$strSql =
					"INSERT INTO b_sale_status_lang(STATUS_ID, ".$arInsert[0].") ".
					"VALUES('".$ID."', ".$arInsert[1].")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		if (isset($arFields["PERMS"]) && is_array($arFields["PERMS"]))
		{
			$DB->Query("DELETE FROM b_sale_status2group WHERE STATUS_ID = '".$ID."'");

			for ($i = 0; $i < count($arFields["PERMS"]); $i++)
			{
				$arInsert = $DB->PrepareInsert("b_sale_status2group", $arFields["PERMS"][$i]);
				$strSql =
					"INSERT INTO b_sale_status2group(STATUS_ID, ".$arInsert[0].") ".
					"VALUES('".$ID."', ".$arInsert[1].")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}

		$events = GetModuleEvents("sale", "OnStatusUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, Array($ID, $arFields));

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = $DB->ForSql($ID, 1);

		$db_res = $DB->Query("SELECT ID FROM b_sale_order WHERE STATUS_ID = '".$ID."'");
		if ($db_res->Fetch())
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGS_ERROR_DELETE"), "ERROR_DELETE_STATUS_TO_ORDER");
			return false;
		}

		$db_events = GetModuleEvents("sale", "OnBeforeStatusDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, Array($ID))===false)
				return false;

		$events = GetModuleEvents("sale", "OnStatusDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, Array($ID));

		$DB->Query("DELETE FROM b_sale_status2group WHERE STATUS_ID = '".$ID."'", true);
		$DB->Query("DELETE FROM b_sale_status_lang WHERE STATUS_ID = '".$ID."'", true);
		return $DB->Query("DELETE FROM b_sale_status WHERE ID = '".$ID."'", true);
	}

	function CreateMailTemplate($ID)
	{
		$ID = Trim($ID);

		if (strlen($ID) <= 0)
			return False;

		if (!($arStatus = CSaleStatus::GetByID($ID, LANGUAGE_ID)))
			return False;

		$eventType = new CEventType;
		$eventMessage = new CEventMessage;

		$eventType->Delete("SALE_STATUS_CHANGED_".$ID);

		$dbSiteList = CSite::GetList(($b = ""), ($o = ""));
		while ($arSiteList = $dbSiteList->Fetch())
		{
			IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/status.php", $arSiteList["LANGUAGE_ID"]);
			$arStatusLang = CSaleStatus::GetLangByID($ID, $arSiteList["LANGUAGE_ID"]);

			$dbEventType = $eventType->GetList(
					array(
							"EVENT_NAME" => "SALE_STATUS_CHANGED_".$ID,
							"LID" => $arSiteList["LANGUAGE_ID"]
						)
				);
			if (!($arEventType = $dbEventType->Fetch()))
			{
				$str  = "";
				$str .= "#ORDER_ID# - ".GetMessage("SKGS_ORDER_ID")."\n";
				$str .= "#ORDER_DATE# - ".GetMessage("SKGS_ORDER_DATE")."\n";
				$str .= "#ORDER_STATUS# - ".GetMessage("SKGS_ORDER_STATUS")."\n";
				$str .= "#EMAIL# - ".GetMessage("SKGS_ORDER_EMAIL")."\n";
				$str .= "#ORDER_DESCRIPTION# - ".GetMessage("SKGS_STATUS_DESCR")."\n";
				$str .= "#TEXT# - ".GetMessage("SKGS_STATUS_TEXT")."\n";
				$str .= "#SALE_EMAIL# - ".GetMessage("SKGS_SALE_EMAIL")."\n";

				$eventTypeID = $eventType->Add(
						array(
								"LID" => $arSiteList["LANGUAGE_ID"],
								"EVENT_NAME" => "SALE_STATUS_CHANGED_".$ID,
								"NAME" => GetMessage("SKGS_CHANGING_STATUS_TO")." \"".$arStatusLang["NAME"]."\"",
								"DESCRIPTION" => $str
							)
					);
			}

			$dbEventMessage = $eventMessage->GetList(
					($b = ""),
					($o = ""),
					array(
							"EVENT_NAME" => "SALE_STATUS_CHANGED_".$ID,
							"SITE_ID" => $arSiteList["LID"]
						)
				);
			if (!($arEventMessage = $dbEventMessage->Fetch()))
			{
				$subject = GetMessage("SKGS_STATUS_MAIL_SUBJ");

				$message  = GetMessage("SKGS_STATUS_MAIL_BODY1");
				$message .= "------------------------------------------\n\n";
				$message .= GetMessage("SKGS_STATUS_MAIL_BODY2");
				$message .= GetMessage("SKGS_STATUS_MAIL_BODY3");
				$message .= "#ORDER_STATUS#\n";
				$message .= "#ORDER_DESCRIPTION#\n";
				$message .= "#TEXT#\n\n";
				$message .= "#SITE_NAME#\n";

				$arFields = Array(
						"ACTIVE" => "Y",
						"EVENT_NAME" => "SALE_STATUS_CHANGED_".$ID,
						"LID" => $arSiteList["LID"],
						"EMAIL_FROM" => "#SALE_EMAIL#",
						"EMAIL_TO" => "#EMAIL#",
						"SUBJECT" => $subject,
						"MESSAGE" => $message,
						"BODY_TYPE" => "text"
					);
				$eventMessageID = $eventMessage->Add($arFields);
			}
		}

		return True;
	}
}
?>