<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

IncludeModuleLangFile(__FILE__);

global $BX_EVENT_SITE_PARAMS;
$BX_EVENT_SITE_PARAMS = array();

class CAllEvent
{
	function SendImmediate($event, $lid, $arFields, $Duplicate = "Y", $message_id="")
	{
		$flds = "";
		if(is_array($arFields))
		{
			foreach($arFields as $key => $value)
			{
				if($flds)
					$flds .= "&";
				$flds .= CEvent::fieldencode($key)."=".CEvent::fieldencode($value);
			}
		}

		$arLocalFields = array(
			"EVENT_NAME" => $event,
			"C_FIELDS" => $flds,
			"LID" => is_array($lid)? implode(",", $lid): $lid,
			"DUPLICATE" => $Duplicate != "N"? "Y": "N",
			"MESSAGE_ID" => intval($message_id) > 0? intval($message_id): "",
			"DATE_INSERT" => GetTime(time(), "FULL"),
			"ID" => "0",
		);

		return CEvent::HandleEvent($arLocalFields);
	}

	function Send($event, $lid, $arFields, $Duplicate = "Y", $message_id="")
	{
		global $DB, $CACHE_MANAGER;

		foreach(GetModuleEvents("main", "OnBeforeEventAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$event, &$lid, &$arFields, &$message_id)) === false)
				return false;

		$flds = "";
		if(is_array($arFields))
		{
			foreach($arFields as $key => $value)
			{
				if($flds)
					$flds .= "&";
				$flds .= CEvent::fieldencode($key)."=".CEvent::fieldencode($value);
			}
		}

		$arLocalFields = array(
			"EVENT_NAME" => $event,
			"C_FIELDS" => $flds,
			"LID" => is_array($lid)? implode(",", $lid): $lid,
			"DUPLICATE" => $Duplicate != "N"? "Y": "N",
			"~DATE_INSERT" => $DB->CurrentTimeFunction(),
		);
		if(intval($message_id) > 0)
			$arLocalFields["MESSAGE_ID"] = intval($message_id);

		if(CACHED_b_event !== false && $CACHE_MANAGER->Read(CACHED_b_event, $cache_id = "events"))
		{
			$CACHE_MANAGER->Clean($cache_id);
		}

		return $DB->Add("b_event", $arLocalFields, array("C_FIELDS"));
	}

	function fieldencode($s)
	{
		if(is_array($s))
		{
			$ret_val = '';
			foreach($s as $v)
				$ret_val .= ($ret_val <> ''? ', ':'').CEvent::fieldencode($v);
		}
		else
		{
			$ret_val = str_replace("%", "%2", $s);
			$ret_val = str_replace("&","%1", $ret_val);
			$ret_val = str_replace("=", "%3", $ret_val);
		}
		return $ret_val;
	}

	function ExtractMailFields($str)
	{
		$ar = explode("&", $str);
		$newar = array();
		while (list (, $val) = each ($ar))
		{
			$val = str_replace("%1", "&", $val);
			$tar = explode("=", $val);
			$key = $tar[0];
			$val = $tar[1];
			$key = str_replace("%3", "=", $key);
			$val = str_replace("%3", "=", $val);
			$key = str_replace("%2", "%", $key);
			$val = str_replace("%2", "%", $val);
			if($key != "")
				$newar[$key] = $val;
		}
		return $newar;
	}

	function GetSiteFieldsArray($site_id)
	{
		global $BX_EVENT_SITE_PARAMS;
		if($site_id !== false && isset($BX_EVENT_SITE_PARAMS[$site_id]))
			return $BX_EVENT_SITE_PARAMS[$site_id];

		$SITE_NAME = COption::GetOptionString("main", "site_name", $GLOBALS["SERVER_NAME"]);
		$SERVER_NAME = COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);
		$DEFAULT_EMAIL_FROM = COption::GetOptionString("main", "email_from", "admin@".$GLOBALS["SERVER_NAME"]);

		if(strlen($site_id)>0)
		{
			$dbSite = CSite::GetByID($site_id);
			if($arSite = $dbSite->Fetch())
			{
				$BX_EVENT_SITE_PARAMS[$site_id] = array(
					"SITE_NAME" => ($arSite["SITE_NAME"]<>''? $arSite["SITE_NAME"] : $SITE_NAME),
					"SERVER_NAME" => ($arSite["SERVER_NAME"]<>''? $arSite["SERVER_NAME"] : $SERVER_NAME),
					"DEFAULT_EMAIL_FROM" => ($arSite["EMAIL"]<>''? $arSite["EMAIL"] : $DEFAULT_EMAIL_FROM),
					"SITE_ID" => $arSite['ID'],
					"SITE_DIR" => $arSite['DIR'],
				);
				return $BX_EVENT_SITE_PARAMS[$site_id];
			}
		}

		return array(
			"SITE_NAME" => $SITE_NAME,
			"SERVER_NAME" => $SERVER_NAME,
			"DEFAULT_EMAIL_FROM" => $DEFAULT_EMAIL_FROM
		);
	}

	function ReplaceTemplate($str, $ar, $bNewLineToBreak=false)
	{
		$str = str_replace("%", "%2", $str);
		foreach($ar as $key=>$val)
		{
			if($bNewLineToBreak && strpos($val, "<") === false)
				$val = nl2br($val);
			$val = str_replace("%", "%2", $val);
			$val = str_replace("#", "%1", $val);
			$str = str_replace("#".$key."#", $val, $str);
		}
		$str = str_replace("%1", "#", $str);
		$str = str_replace("%2", "%", $str);

		return $str;
	}

	function Is8Bit($str)
	{
		return preg_match("/[\\x80-\\xFF]/", $str) > 0;
	}

	function EncodeMimeString($text, $charset)
	{
		if(!CEvent::Is8Bit($text))
			return $text;

		//$maxl = IntVal((76 - strlen($charset) + 7)*0.4);
		$res = "";
		$maxl = 40;
		$eol = CEvent::GetMailEOL();
		$len = strlen($text);
		for($i=0; $i<$len; $i=$i+$maxl)
		{
			if($i>0)
				$res .= $eol."\t";
			$res .= "=?".$charset."?B?".base64_encode(substr($text, $i, $maxl))."?=";
		}
		return $res;
	}

	function EncodeSubject($text, $charset)
	{
		return "=?".$charset."?B?".base64_encode($text)."?=";
	}

	function EncodeHeaderFrom($text, $charset)
	{
		$i = strlen($text);
		while($i > 0)
		{
			if(ord(substr($text, $i-1, 1))>>7)
				break;
			$i--;
		}
		if($i==0)
			return $text;
		else
			return "=?".$charset."?B?".base64_encode(substr($text, 0, $i))."?=".substr($text, $i);
	}

	function GetMailEOL()
	{
		static $eol = false;
		if($eol!==false)
			return $eol;

		if(strtoupper(substr(PHP_OS,0,3)=='WIN'))
			$eol="\r\n";
		elseif(strtoupper(substr(PHP_OS,0,3)!='MAC'))
			$eol="\n"; 	 //unix
		else
			$eol="\r";

		return $eol;
	}

	function HandleEvent($arEvent)
	{
		global $DB;

		$flag = "0"; // no templates
		$arResult = array(
			"Success" => false,
			"Fail" => false,
			"Was" => false,
		);

		$eol = CAllEvent::GetMailEOL();
		$ar = CAllEvent::ExtractMailFields($arEvent["C_FIELDS"]);

		$arSites = explode(",", $arEvent["LID"]);
		foreach($arSites as $key => $value)
		{
			$value = trim($value);
			if(strlen($value) > 0)
				$arSites[$key] = "'".$DB->ForSql($value, 2)."'";
			else
				unset($arSites[$key]);
		}
		if(count($arSites) <= 0)
			return $flag;
		$strSites = implode(", ", $arSites);

		$strSql = "SELECT CHARSET FROM b_lang WHERE LID IN (".$strSites.") ORDER BY DEF DESC, SORT";
		$dbCharset = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		$arCharset = $dbCharset->Fetch();
		if(!$arCharset)
			return $flag;
		$charset = $arCharset["CHARSET"];

		$strWhere = "";
		$MESSAGE_ID = intval($arEvent["MESSAGE_ID"]);
		if($MESSAGE_ID > 0)
		{
			$strSql = "SELECT 'x' FROM b_event_message M WHERE M.ID=$MESSAGE_ID";
			$z = $DB->Query($strSql);
			if($z->Fetch())
				$strWhere = "WHERE M.ID=$MESSAGE_ID and M.ACTIVE='Y'";
		}

		$strSql = "
			SELECT DISTINCT ID
			FROM b_event_message M
			".($strWhere == ""?
				", b_event_message_site MS
				WHERE M.ID=MS.EVENT_MESSAGE_ID
				AND M.ACTIVE='Y'
				AND M.EVENT_NAME='".$DB->ForSql($arEvent["EVENT_NAME"])."'
				AND MS.SITE_ID IN (".$strSites.")"
			:
				$strWhere
			)."
		";

		$db_mail_result = $DB->Query($strSql);
		while($db_mail_result_array = $db_mail_result->Fetch())
		{
			$rsMail = $DB->Query("
				SELECT ID, SUBJECT, MESSAGE, EMAIL_FROM, EMAIL_TO, BODY_TYPE, BCC, CC, REPLY_TO, IN_REPLY_TO, PRIORITY, FIELD1_NAME, FIELD1_VALUE, FIELD2_NAME, FIELD2_VALUE
				FROM b_event_message M
				WHERE M.ID = ".intval($db_mail_result_array["ID"])."
			");
			$db_mail_result_array = $rsMail->Fetch();
			if(!$db_mail_result_array)
				continue;

			$strSqlMLid = "
				SELECT MS.SITE_ID
				FROM b_event_message_site MS
				WHERE MS.EVENT_MESSAGE_ID = ".$db_mail_result_array["ID"]."
				AND MS.SITE_ID IN (".$strSites.")
			";
			$dbr_mlid = $DB->Query($strSqlMLid);
			if($ar_mlid = $dbr_mlid->Fetch())
				$arFields = $ar + CAllEvent::GetSiteFieldsArray($ar_mlid["SITE_ID"]);
			else
				$arFields = $ar + CAllEvent::GetSiteFieldsArray(false);

			foreach (GetModuleEvents("main", "OnBeforeEventSend", true) as $event)
				ExecuteModuleEventEx($event, array(&$arFields, &$db_mail_result_array));

			$arMailFields = array();
			$arMailFields["From"] = CAllEvent::ReplaceTemplate($db_mail_result_array["EMAIL_FROM"], $arFields);

			if($db_mail_result_array["BCC"]!='')
			{
				$bcc = CAllEvent::ReplaceTemplate($db_mail_result_array["BCC"], $arFields);
				if(strpos($bcc, "@")!==false)
					$arMailFields["BCC"] = $bcc;
			}

			if($db_mail_result_array["CC"]!='')
				$arMailFields["CC"] = CAllEvent::ReplaceTemplate($db_mail_result_array["CC"], $arFields);

			if($db_mail_result_array["REPLY_TO"]!='')
				$arMailFields["Reply-To"] = CAllEvent::ReplaceTemplate($db_mail_result_array["REPLY_TO"], $arFields);
			else
				$arMailFields["Reply-To"] = preg_replace("/(.*)\\<(.*)\\>/i", '$2', $arMailFields["From"]);

			if($db_mail_result_array["IN_REPLY_TO"]!='')
				$arMailFields["In-Reply-To"] = CAllEvent::ReplaceTemplate($db_mail_result_array["IN_REPLY_TO"], $arFields);

			if($db_mail_result_array['FIELD1_NAME']!='' && $db_mail_result_array['FIELD1_VALUE']!='')
				$arMailFields[$db_mail_result_array['FIELD1_NAME']] = CAllEvent::ReplaceTemplate($db_mail_result_array["FIELD1_VALUE"], $arFields);

			if($db_mail_result_array['FIELD2_NAME']!='' && $db_mail_result_array['FIELD2_VALUE']!='')
				$arMailFields[$db_mail_result_array['FIELD2_NAME']] = CAllEvent::ReplaceTemplate($db_mail_result_array["FIELD2_VALUE"], $arFields);

			if($db_mail_result_array["PRIORITY"]!='')
				$arMailFields["X-Priority"] = CAllEvent::ReplaceTemplate($db_mail_result_array["PRIORITY"], $arFields);
			else
				$arMailFields["X-Priority"] = '3 (Normal)';

			foreach($ar as $f=>$v)
			{
				if(substr($f, 0, 1) == "=")
					$arMailFields[substr($f, 1)] = $v;
			}

			foreach($arMailFields as $k=>$v)
				$arMailFields[$k] = trim($v, "\r\n");

			//add those who want to receive all emails
			if($arEvent["DUPLICATE"]=="Y")
			{
				$all_bcc = COption::GetOptionString("main", "all_bcc", "");
				if(strpos($all_bcc, "@")!==false)
					$arMailFields["BCC"] .= (strlen($all_bcc)>0?(strlen($arMailFields["BCC"])>0?",":"").$all_bcc:"");
			}

			$email_to = CAllEvent::ReplaceTemplate($db_mail_result_array["EMAIL_TO"], $arFields);
			$subject = CAllEvent::ReplaceTemplate($db_mail_result_array["SUBJECT"], $arFields);

			if(COption::GetOptionString("main", "convert_mail_header", "Y")=="Y")
			{
				foreach($arMailFields as $k=>$v)
					if($k == 'From' || $k == 'CC')
						$arMailFields[$k] = CAllEvent::EncodeHeaderFrom($v, $charset);
					else
						$arMailFields[$k] = CAllEvent::EncodeMimeString($v, $charset);

				$email_to = CAllEvent::EncodeHeaderFrom($email_to, $charset);
				$subject = CAllEvent::EncodeMimeString($subject, $charset);
			}

			if(defined("BX_MS_SMTP") && BX_MS_SMTP===true)
			{
				$email_to = preg_replace("/(.*)\\<(.*)\\>/i", '$2', $email_to);
				if($arMailFields["From"]!='')
					$arMailFields["From"] = preg_replace("/(.*)\\<(.*)\\>/i", '$2', $arMailFields["From"]);
				if($arMailFields["To"]!='')
					$arMailFields["To"] = preg_replace("/(.*)\\<(.*)\\>/i", '$2', $arMailFields["To"]);
			}

			if(COption::GetOptionString("main", "fill_to_mail", "N")=="Y")
				$arMailFields["To"] = $email_to;

			$header = "";
			foreach($arMailFields as $k=>$v)
				$header .= $k.': '.$v.$eol;

			$header .=
				"X-MID: ".$arEvent["ID"].".".$db_mail_result_array["ID"]." (".$arEvent["DATE_INSERT"].")".$eol.
				"X-EVENT_NAME: ".$arEvent["EVENT_NAME"].$eol.
				($db_mail_result_array["BODY_TYPE"] == "html"? "Content-Type: text/html; charset=".$charset.$eol : "Content-Type: text/plain; charset=".$charset.$eol).
				"Content-Transfer-Encoding: 8bit";

			$bNewLineToBreak = ($db_mail_result_array["BODY_TYPE"] == "html");
			$message = CAllEvent::ReplaceTemplate($db_mail_result_array["MESSAGE"], $arFields, $bNewLineToBreak);

			if(COption::GetOptionString("main", "send_mid", "N")=="Y")
				$message .= ($db_mail_result_array["BODY_TYPE"] == "html"?"<br><br>" : "\n\n")."MID #".$arEvent["ID"].".".$db_mail_result_array["ID"]." (".$arEvent["DATE_INSERT"].")\n";

			$message = str_replace("\r\n", "\n", $message);

			if(COption::GetOptionString("main", "CONVERT_UNIX_NEWLINE_2_WINDOWS", "N")=="Y")
				$message = str_replace("\n", "\r\n", $message);
			if(defined("ONLY_EMAIL") && $email_to!=ONLY_EMAIL)
				$arResult["Success"] = true;
			elseif(bxmail($email_to, $subject, $message, $header, COption::GetOptionString("main", "mail_additional_parameters", "")))
				$arResult["Success"] = true;
			else
				$arResult["Fail"] = true;

			$arResult["Was"] = true;
		}

		if($arResult["Was"])
		{
			if($arResult["Success"])
			{
				if($arResult["Fail"])
					$flag = "P"; // partly sent
				else
					$flag = "Y"; // all sent
			}
			else
			{
				if($arResult["Fail"])
					$flag = "F"; // all templates failed
			}
		}

		return $flag;
	}
}


class CAllEventMessage
{
	var $LAST_ERROR;

	function CheckFields($arFields, $ID=false)
	{
		/** @global CMain $APPLICATION */
		global $APPLICATION;
		$this->LAST_ERROR = "";
		$arMsg = array();

		if(is_set($arFields, "EMAIL_FROM") && strlen($arFields["EMAIL_FROM"])<3)
		{
			$this->LAST_ERROR .= GetMessage("BAD_EMAIL_FROM")."<br>";
			$arMsg[] = array("id"=>"EMAIL_FROM", "text"=> GetMessage("BAD_EMAIL_FROM"));
		}
		if(is_set($arFields, "EMAIL_TO") && strlen($arFields["EMAIL_TO"])<3)
		{
			$this->LAST_ERROR .= GetMessage("BAD_EMAIL_TO")."<br>";
			$arMsg[] = array("id"=>"EMAIL_TO", "text"=> GetMessage("BAD_EMAIL_TO"));
		}

		if($ID===false && !is_set($arFields, "EVENT_NAME"))
		{
			$this->LAST_ERROR .= GetMessage(GetMessage("MAIN_BAD_EVENT_NAME_NA"))."<br>";
			$arMsg[] = array("id"=>"EVENT_NAME", "text"=> GetMessage("MAIN_BAD_EVENT_NAME_NA"));
		}
		if(is_set($arFields, "EVENT_NAME"))
		{
			$r = CEventType::GetListEx(array(), array("EVENT_NAME"=>$arFields["EVENT_NAME"]), array("type"=>"none"));
			if(!$r->Fetch())
			{
				$this->LAST_ERROR .= GetMessage("BAD_EVENT_TYPE")."<br>";
				$arMsg[] = array("id"=>"EVENT_NAME", "text"=> GetMessage("BAD_EVENT_TYPE"));
			}
		}

		if(
			($ID===false && !is_set($arFields, "LID")) ||
			(is_set($arFields, "LID")
			&& (
				(is_array($arFields["LID"]) && count($arFields["LID"])<=0)
				||
				(!is_array($arFields["LID"]) && strlen($arFields["LID"])<=0)
				)
			)
		)
		{
			$this->LAST_ERROR .= GetMessage("MAIN_BAD_SITE_NA")."<br>";
			$arMsg[] = array("id"=>"LID", "text"=> GetMessage("MAIN_BAD_SITE_NA"));
		}
		elseif(is_set($arFields, "LID"))
		{
			if(!is_array($arFields["LID"]))
				$arFields["LID"] = array($arFields["LID"]);

			foreach($arFields["LID"] as $v)
			{
				$r = CSite::GetByID($v);
				if(!$r->Fetch())
				{
					$this->LAST_ERROR .= "'".$v."' - ".GetMessage("MAIN_EVENT_BAD_SITE")."<br>";
					$arMsg[] = array("id"=>"LID", "text"=> GetMessage("MAIN_EVENT_BAD_SITE"));
				}
			}
		}

		if(!empty($arMsg))
		{
			$e = new CAdminException($arMsg);
			$APPLICATION->ThrowException($e);
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		return true;
	}

	///////////////////////////////////////////////////////////////////
	// New event message template
	///////////////////////////////////////////////////////////////////
	function Add($arFields)
	{
		global $DB;

		unset($arFields["ID"]);

		if(!$this->CheckFields($arFields))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		$arLID = array();
		$str_LID = "''";
		if(is_set($arFields, "LID"))
		{
			if(is_array($arFields["LID"]))
				$arLID = $arFields["LID"];
			else
				$arLID[] = $arFields["LID"];

			$arFields["LID"] = false;
			foreach($arLID as $v)
			{
				$arFields["LID"] = $v;
				$str_LID .= ", '".$DB->ForSql($v)."'";
			}
		}

		$ID = CDatabase::Add("b_event_message", $arFields, array("MESSAGE"));

		if(count($arLID)>0)
		{
			$strSql = "DELETE FROM b_event_message_site WHERE EVENT_MESSAGE_ID=".$ID;
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

			$strSql =
				"INSERT INTO b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) ".
				"SELECT ".$ID.", LID ".
				"FROM b_lang ".
				"WHERE LID IN (".$str_LID.") ";

			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		return $ID;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		if(!$this->CheckFields($arFields, $ID))
			return false;

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		$arLID = array();
		$str_LID = "''";
		if(is_set($arFields, "LID"))
		{
			if(is_array($arFields["LID"]))
				$arLID = $arFields["LID"];
			else
				$arLID[] = $arFields["LID"];

			$arFields["LID"] = false;
			foreach($arLID as $v)
			{
				$arFields["LID"] = $v;
				$str_LID .= ", '".$DB->ForSql($v)."'";
			}
		}

		$ID = IntVal($ID);
		$strUpdate = $DB->PrepareUpdate("b_event_message", $arFields);
		$strSql = "UPDATE b_event_message SET ".$strUpdate." WHERE ID=".$ID;

		$arBinds=array();
		if(is_set($arFields, "MESSAGE"))
			$arBinds["MESSAGE"] = $arFields["MESSAGE"];

		$DB->QueryBind($strSql, $arBinds);

		if(count($arLID)>0)
		{
			$strSql = "DELETE FROM b_event_message_site WHERE EVENT_MESSAGE_ID=".$ID;
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

			$strSql =
				"INSERT INTO b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) ".
				"SELECT ".$ID.", LID ".
				"FROM b_lang ".
				"WHERE LID IN (".$str_LID.") ";
			$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		return true;
	}

	///////////////////////////////////////////////////////////////////
	// Query
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CEventMessage::GetList(($o = ""), ($b = ""), array("ID"=>$ID));
	}

	function GetSite($event_message_id)
	{
		global $DB;
		$strSql = "SELECT L.*, MS.* FROM b_event_message_site MS, b_lang L WHERE L.LID=MS.SITE_ID AND MS.EVENT_MESSAGE_ID=".IntVal($event_message_id);
		return $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
	}

	function GetLang($event_message_id)
	{
		return CEventMessage::GetSite($event_message_id);
	}

	function Delete($ID)
	{
		/**
		 * @global CMain $APPLICATION
		 * @global CDatabase $DB
		 */
		global $DB, $APPLICATION;
		$ID = Intval($ID);

		foreach(GetModuleEvents("main", "OnBeforeEventMessageDelete", true) as $arEvent)
		{
			if(ExecuteModuleEventEx($arEvent, array($ID))===false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				if($ex = $APPLICATION->GetException())
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}
		}

		@set_time_limit(600);

		//check module event for OnDelete
		foreach(GetModuleEvents("main", "OnEventMessageDelete", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($ID));

		$DB->Query("DELETE FROM b_event_message_site WHERE EVENT_MESSAGE_ID=".$ID, true);
		return $DB->Query("DELETE FROM b_event_message WHERE ID=".$ID, true);
	}
}

class CEventType
{
	function CheckFields($arFields = array(), $action = "ADD", $ID = array())
	{
		/** @global CMain $APPLICATION */
		global $APPLICATION;

		$arFilter = array();
		$aMsg = array();
		//ID, LID, EVENT_NAME, NAME, DESCRIPTION, SORT
		if ($action == "ADD")
		{
			if (empty($arFields["EVENT_NAME"]))
				$aMsg[] = array("id"=>"EVENT_NAME_EMPTY", "text"=>GetMessage("EVENT_NAME_EMPTY"));

			if(!is_set($arFields, "LID") && is_set($arFields, "SITE_ID"))
				$arFields["LID"] = $arFields["SITE_ID"];
			if (is_set($arFields, "LID") && empty($arFields["LID"]))
				$aMsg[] = array("id"=>"LID_EMPTY", "text"=>GetMessage("LID_EMPTY"));

			if (empty($aMsg))
			{
				$db_res = CEventType::GetList(array("LID" => $arFields["LID"], "EVENT_NAME" => $arFields["EVENT_NAME"]));
				if ($db_res && $db_res->Fetch())
				{
					$aMsg[] = array("id"=>"EVENT_NAME_EXIST", "text"=>str_replace(
						array("#SITE_ID#", "#EVENT_NAME#"),
						array($arFields["LID"], $arFields["EVENT_NAME"]),
						GetMessage("EVENT_NAME_EXIST")));
				}
			}
		}
		elseif ($action == "UPDATE")
		{
			if (empty($ID) && (empty($ID["ID"]) || (empty($ID["EVENT_NAME"]))))
			{
				if (empty($ID))
					$aMsg[] = array("id"=>"EVENT_ID_EMPTY", "text"=>GetMessage("EVENT_ID_EMPTY"));
				else
					$aMsg[] = array("id"=>"EVENT_NAME_LID_EMPTY", "text"=>GetMessage("EVENT_ID_EMPTY"));
			}

			if (empty($aMsg) && is_set($arFields, "EVENT_NAME") && (is_set($arFields, "LID")))
			{
				if (is_set($arFields, "EVENT_NAME"))
					$arFilter["EVENT_NAME"] = $arFields["EVENT_NAME"];
				if (is_set($arFields, "LID"))
					$arFilter["LID"] = $arFields["LID"];

				if (!empty($arFilter) && (count($arFilter) < 2) && is_set($arFilter, "LID"))
				{
					unset($arFields["LID"]);
				}
				else
				{
					$db_res = CEventType::GetList($arFilter);

					if ($db_res && ($res = $db_res->Fetch()))
					{
						if (($action == "UPDATE") &&
							((is_set($ID, "EVENT_NAME") && is_set($ID, "LID") &&
								(($res["EVENT_NAME"] != $ID["EVENT_NAME"]) || ($res["LID"] != $ID["LID"]))) ||
								(is_set($ID, "ID") && $res["ID"] != $ID["ID"]) ||
								(is_set($ID, "EVENT_NAME") && ($res["EVENT_NAME"] != $ID["EVENT_NAME"]))))
						{
							$aMsg[] = array("id"=>"EVENT_NAME_EXIST", "text"=>str_replace(
								array("#SITE_ID#", "#EVENT_NAME#"),
								array($arFields["LID"], $arFields["EVENT_NAME"]),
								GetMessage("EVENT_NAME_EXIST")));
						}
					}
				}
			}
		}
		else
		{
			$aMsg[] = array("id"=>"ACTION_EMPTY", "text"=>GetMessage("ACTION_EMPTY"));
		}

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			return false;
		}
		return true;
	}

	function Add($arFields)
	{
		global $DB;

		if(!is_set($arFields, "LID") && is_set($arFields, "SITE_ID"))
			$arFields["LID"] = $arFields["SITE_ID"];

		if (CEventType::CheckFields($arFields))
		{
			return $DB->Add("b_event_type", $arFields, array("DESCRIPTION"));
		}
		return false;
	}

	function Update($arID = array(), $arFields = array())
	{
		global $DB;

		$ID = array();
		// update event type by ID, or (LID+EVENT_NAME)
		if (is_array($arID) && !empty($arID))
		{
			foreach ($arID as $key => $val)
			{
				if (in_array($key, array("ID", "LID", "EVENT_NAME")))
					$ID[$key] = $val;
			}
		}
		if (!empty($ID) && CEventType::CheckFields($arFields, "UPDATE", $ID))
		{
			foreach ($ID as $key => $val)
				$ID[$key] = $key."='".$DB->ForSql($val)."'";

			$arBinds = array();
			if (is_set($arFields, "DESCRIPTION"))
				$arBinds["DESCRIPTION"] = $arFields["DESCRIPTION"];
			unset($arFields["ID"]);
			return $DB->QueryBind(
				"UPDATE b_event_type SET ".$DB->PrepareUpdate("b_event_type", $arFields)." WHERE (".implode(") AND (", $ID).")",
				$arBinds,
				false);
		}
		return false;
	}

	function Delete($arID)
	{
		global $DB;
		$ID = array();
		if (!is_array($arID))
			$arID = array("EVENT_NAME" => $arID);
		foreach ($arID as $k => $v)
		{
			if (!in_array(strToUpper($k), array("ID", "LID", "EVENT_NAME", "NAME", "SORT")))
				continue;
			$ID[] = $k."='".$DB->ForSQL($v)."'";
		}
		if (!empty($ID))
		{
			return $DB->Query("DELETE FROM b_event_type WHERE ".implode(" AND ", $ID), true);
		}
		return false;
	}

	function GetList($arFilter=array(), $arOrder=array())
	{
		global $DB;
		$arSqlSearch = $arSqlOrder = array();

		foreach($arFilter as $key => $val)
		{
			$val = $DB->ForSQL($val);
			if($val == '')
				continue;
			switch(strtoupper($key))
			{
				case "EVENT_NAME":
				case "TYPE_ID":
					$arSqlSearch[] = "ET.EVENT_NAME = '".$val."'";
					break;
				case "LID":
					$arSqlSearch[] = "ET.LID = '".$val."'";
					break;
				case "ID":
					$arSqlSearch[] = "ET.ID=".IntVal($val);
					break;
			}
		}

		$strSqlSearch = "";
		if(!empty($arSqlSearch))
			$strSqlSearch = "WHERE ".implode(" AND ", $arSqlSearch);

		if(is_array($arOrder))
		{
			static $arFields = array("ID"=>1, "LID"=>1, "EVENT_NAME"=>1, "NAME"=>1, "SORT"=>1);
			foreach($arOrder as $by => $ord)
				if(array_key_exists(($by = strtoupper($by)), $arFields))
					$arSqlOrder[] = $by." ".(($ord = strtoupper($ord)) == "DESC"? "DESC":"ASC");
		}
		if(empty($arSqlOrder))
			$arSqlOrder[] = "ID ASC";

		$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

		$strSql =
			"SELECT ID, LID, EVENT_NAME, NAME, DESCRIPTION, SORT ".
			"FROM b_event_type ET ".
			$strSqlSearch.
			$strSqlOrder;

		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $res;
	}

	function GetListEx($arOrder = array(), $arFilter = array(), $arParams = array())
	{
		global $DB;
		$arSqlSearch = array();
		$strSqlSearch = "";
		$arSqlOrder = array();
		foreach($arFilter as $key => $val)
		{
			if(strlen($val) <= 0)
				continue;
			$val = $DB->ForSql($val);
			$key_res = CEventType::GetFilterOperation($key);
			$key = strToUpper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];
			switch($key)
			{
				case "EVENT_NAME":
				case "TYPE_ID":
					if ($strOperation == "LIKE")
						$val = "%".$val."%";
					$arSqlSearch[] = ($strNegative=="Y"?" #TABLE_ID#.EVENT_NAME  IS NULL OR NOT ":"")."(#TABLE_ID#.EVENT_NAME ".$strOperation." '".$val."' )";
					break;
				case "DESCRIPTION":
				case "NAME":
					if ($strOperation == "LIKE")
						$val = "%".$val."%";
					$arSqlSearch[] = ($strNegative=="Y"?" ET.".$key." IS NULL OR NOT ":"")."(ET.".$key." ".$strOperation." '".$val."' )";
					break;
				case "LID":
					$arSqlSearch[] = ($strNegative=="Y"?" ET.".$key." IS NULL OR NOT ":"")."(ET.".$key." ".$strOperation." '".$val."' )";
					break;
				case "ID":
					$arSqlSearch[] = ($strNegative=="Y"?" ET.".$key." IS NULL OR NOT ":"")."(ET.".$key." ".$strOperation." ".intVal($val)." )";
					break;
				case "MESSAGE_ID":
					$arSqlSearch[] = ($strNegative=="Y"?" ET.ID IS NULL OR NOT ":"")."(EM.ID ".$strOperation." ".intVal($val)." )";
					break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = "WHERE (".implode(") AND (", $arSqlSearch).") ";

		if (is_array($arOrder))
		{
			foreach($arOrder as $by=>$order)
			{
				$by = strtoupper($by);
				$order = strtoupper($order);
				$order = ($order <> "DESC"? "ASC" : "DESC");
				if($by == "EVENT_NAME" || $by == "ID")
					$arSqlOrder["EVENT_NAME"] = "EVENT_NAME ".$order;
			}
		}
		if(empty($arSqlOrder))
			$arSqlOrder["EVENT_NAME"] = "EVENT_NAME ASC";

		$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

		$strSql = "
			SELECT EM.EVENT_NAME AS ID, EM.EVENT_NAME AS EVENT_NAME
			FROM b_event_message EM
			LEFT JOIN b_event_type ET ON (ET.EVENT_NAME = EM.EVENT_NAME)
			".str_replace("#TABLE_ID#", "EM", $strSqlSearch)."
			UNION
			SELECT ET.EVENT_NAME AS ID, ET.EVENT_NAME
			FROM b_event_type ET
			LEFT JOIN b_event_message EM ON (ET.EVENT_NAME = EM.EVENT_NAME)
			".str_replace("#TABLE_ID#", "ET", $strSqlSearch)."
			".$strSqlOrder;

		$db_res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$db_res = new _CEventTypeResult($db_res, $arParams);
		return $db_res;
	}

	///////////////////////////////////////////////////////////////////
	// selecting type
	///////////////////////////////////////////////////////////////////
	function GetByID($ID, $LID)
	{
		global $DB;

		$strSql =
			"SELECT ET.* ".
			"FROM b_event_type ET ".
			"WHERE ET.EVENT_NAME = '".$DB->ForSql($ID)."' ".
			"	AND ET.LID = '".$DB->ForSql($LID)."'";

		$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

		return $res;
	}

	function GetFilterOperation($key)
	{
		$strNegative = "N";
		if (substr($key, 0, 1)=="!")
		{
			$key = subStr($key, 1);
			$strNegative = "Y";
		}

		$strOrNull = "N";
		if (subStr($key, 0, 1)=="+")
		{
			$key = subStr($key, 1);
			$strOrNull = "Y";
		}

		if (subStr($key, 0, 2)==">=")
		{
			$key = subStr($key, 2);
			$strOperation = ">=";
		}
		elseif (subStr($key, 0, 1)==">")
		{
			$key = subStr($key, 1);
			$strOperation = ">";
		}
		elseif (subStr($key, 0, 2)=="<=")
		{
			$key = subStr($key, 2);
			$strOperation = "<=";
		}
		elseif (subStr($key, 0, 1)=="<")
		{
			$key = subStr($key, 1);
			$strOperation = "<";
		}
		elseif (subStr($key, 0, 1)=="@")
		{
			$key = subStr($key, 1);
			$strOperation = "IN";
		}
		elseif (subStr($key, 0, 1)=="~")
		{
			$key = subStr($key, 1);
			$strOperation = "LIKE";
		}
		elseif (subStr($key, 0, 1)=="%")
		{
			$key = subStr($key, 1);
			$strOperation = "QUERY";
		}
		else
		{
			$strOperation = "=";
		}

		return array("FIELD" => $key, "NEGATIVE" => $strNegative, "OPERATION" => $strOperation, "OR_NULL" => $strOrNull);
	}
}

class _CEventTypeResult extends CDBResult
{
	var $type = "type";
	var $LID = LANGUAGE_ID;
	var $SITE_ID = SITE_ID;

	function _CEventTypeResult($res, $arParams = array())
	{
		$this->type = empty($arParams["type"]) ? "type" : $arParams["type"];
		$this->LID = empty($arParams["LID"]) ? LANGUAGE_ID : $arParams["LID"];
		$this->SITE_ID = empty($arParams["SITE_ID"]) ? SITE_ID : $arParams["SITE_ID"];
		parent::CDBResult($res);
	}

	function Fetch()
	{
		$arr = array();
		$arr_lid = array();
		$arr_lids = array();

		if($res = parent::Fetch())
		{
			if ($this->type != "none")
			{
				$db_res_ = CEventType::GetList(array("EVENT_NAME" => $res["EVENT_NAME"]));
				if ($db_res_ && $res_ = $db_res_->Fetch())
				{
					do
					{
						$arr[$res_["ID"]] = $res_;
						$arr_lid[] = $res_["LID"];
						$arr_lids[$res_["LID"]] = $res_;
					}while($res_ = $db_res_->Fetch());
				}
				$res["ID"] = array_keys($arr);
				$res["LID"] = $arr_lid;

				$res["NAME"] = empty($arr_lids[$this->LID]["NAME"]) ? $arr_lids["en"]["NAME"] : $arr_lids[$this->LID]["NAME"];
				$res["SORT"] = empty($arr_lids[$this->LID]["SORT"]) ? $arr_lids["en"]["SORT"] : $arr_lids[$this->LID]["SORT"];
				$res["DESCRIPTION"] = empty($arr_lids[$this->LID]["DESCRIPTION"]) ? $arr_lids["en"]["DESCRIPTION"] : $arr_lids[$this->LID]["DESCRIPTION"];
				$res["TYPE"] = $arr;
				if ($this->type != "type")
				{
					$arr = array();
					$db_res_ = CEventMessage::GetList(($sort = "sort"), ($by = "asc"), array("EVENT_NAME" => $res["EVENT_NAME"]));
					if ($db_res_ && $res_ = $db_res_->Fetch())
					{
						do
						{
							$arr[$res_["ID"]] = $res_;
						}while($res_ = $db_res_->Fetch());
					}
					$res["TEMPLATES"] = $arr;
				}
			}
		}
		return $res;
	}
}
