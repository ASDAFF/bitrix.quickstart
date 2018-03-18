<?
IncludeModuleLangFile(__FILE__);

class SMSCPostingGeneral
{
	var $LAST_ERROR = "";
	//email count for one hit
	static $current_emails_per_hit = 0;

	//get by ID
	function GetByID($ID)
	{
		global $DB;
		$ID = intval($ID);

		$strSql = "
			SELECT
				P.*
				,".$DB->DateToCharFunction("P.TIMESTAMP_X", "FULL")." AS TIMESTAMP_X
				,".$DB->DateToCharFunction("P.DATE_SENT", "FULL")." AS DATE_SENT
				,".$DB->DateToCharFunction("P.AUTO_SEND_TIME", "FULL")." AS AUTO_SEND_TIME
			FROM iwebsms_posting P
			WHERE P.ID=".$ID."
		";

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	//list of categories linked with message
	function GetRubricList($ID)
	{
		global $DB;
		$ID = intval($ID);

		$strSql = "
			SELECT
				R.ID
				,R.NAME
				,R.SORT
				,R.LID
				,R.ACTIVE
			FROM
				iwebsms_list_rubric R
				,iwebsms_posting_rubric PR
			WHERE
				R.ID=PR.LIST_RUBRIC_ID
				AND PR.POSTING_ID=".$ID."
			ORDER BY
				R.LID, R.SORT, R.NAME
		";

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	//list of user group linked with message
	function GetGroupList($ID)
	{
		global $DB;
		$ID = intval($ID);

		$strSql = "
			SELECT
				G.ID
				,G.NAME
			FROM
				b_group G
				,iwebsms_posting_group PG
			WHERE
				G.ID=PG.GROUP_ID
				AND PG.POSTING_ID=".$ID."
			ORDER BY
				G.C_SORT, G.ID
		";

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	// delete by ID
	function Delete($ID)
	{
		global $DB;
		$ID = intval($ID);

		$DB->StartTransaction();

		SMSCPosting::DeleteFile($ID);

		$res = $DB->Query("DELETE FROM iwebsms_posting_rubric WHERE POSTING_ID='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res)
			$res = $DB->Query("DELETE FROM iwebsms_posting_group WHERE POSTING_ID='".$ID."' ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res)
			$res = $DB->Query("DELETE FROM iwebsms_posting_sms WHERE POSTING_ID='".$ID."' ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res)
			$res = $DB->Query("DELETE FROM iwebsms_posting WHERE ID='".$ID."' ", false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if($res)
			$DB->Commit();
		else
			$DB->Rollback();

		return $res;
	}

	function OnGroupDelete($group_id)
	{
		global $DB;
		$group_id = intval($group_id);

		return $DB->Query("DELETE FROM iwebsms_posting_group WHERE GROUP_ID=".$group_id, true);
	}

	function DeleteFile($ID, $file_id=false)
	{
		global $DB;

		$rsFile = SMSCPosting::GetFileList($ID, $file_id);
		while($arFile = $rsFile->Fetch())
		{
			$rs = $DB->Query("DELETE FROM iwebsms_posting_file where POSTING_ID=".intval($ID)." AND FILE_ID=".intval($arFile["ID"]), false, "File: ".__FILE__."<br>Line: ".__LINE__);
			CFile::Delete(intval($arFile["ID"]));
		}
	}

	function SplitFileName($file_name)
	{
		$found = array();
		// exapmle(2).txt
		if(preg_match("/^(.*)\((\d+?)\)(\..+?)$/", $file_name, $found))
		{
			$fname = $found[1];
			$fext = $found[3];
			$index = $found[2];
		}
		// example(2)
		elseif(preg_match("/^(.*)\((\d+?)\)$/", $file_name, $found))
		{
			$fname = $found[1];
			$fext = "";
			$index = $found[2];
		}
		// example.txt
		elseif(preg_match("/^(.*)(\..+?)$/", $file_name, $found))
		{
			$fname = $found[1];
			$fext = $found[2];
			$index = 0;
		}
		// example
		else
		{
			$fname = $file_name;
			$fext = "";
			$index = 0;
		}
		return array($fname, $fext, $index);
	}

	function SaveFile($ID, $file)
	{
		global $DB;
		$ID = intval($ID);

		$arFileName = SMSCPosting::SplitFileName($file["name"]);
		//Check if file with this name already exists
		$arSameNames = array();
		$rsFile = SMSCPosting::GetFileList($ID);
		while($arFile = $rsFile->Fetch())
		{
			$arSavedName = SMSCPosting::SplitFileName($arFile["ORIGINAL_NAME"]);
			if($arFileName[0] == $arSavedName[0] && $arFileName[1] == $arSavedName[1])
				$arSameNames[$arSavedName[2]] = true;
		}
		while(array_key_exists($arFileName[2], $arSameNames))
		{
			$arFileName[2]++;
		}
		if($arFileName[2] > 0)
		{
			$file["name"] = $arFileName[0]."(".($arFileName[2]).")".$arFileName[1];
		}
		//save file
		$file["MODULE_ID"] = "imaginweb.sms";
		$fid = intval(CFile::SaveFile($file, "imaginweb.sms", true, true));
		if(($fid > 0) && $DB->Query("INSERT INTO iwebsms_posting_file (POSTING_ID, FILE_ID) VALUES (".$ID." ,".$fid.")", false, "File: ".__FILE__."<br>Line: ".__LINE__))
		{
			return true;
		}
		else
		{
			$this->LAST_ERROR = GetMessage("iwebsms_class_post_err_att");
			return false;
		}
	}

	function GetFileList($ID, $file_id=false)
	{
		global $DB;
		$ID = intval($ID);
		$file_id = intval($file_id);

		$strSql = "
			SELECT
				F.ID
				,F.FILE_SIZE
				,F.ORIGINAL_NAME
				,F.SUBDIR
				,F.FILE_NAME
				,F.CONTENT_TYPE
				,F.HANDLER_ID
			FROM
				b_file F
				,iwebsms_posting_file PF
			WHERE
				F.ID=PF.FILE_ID
				AND PF.POSTING_ID=".$ID."
			".($file_id>0?"AND PF.FILE_ID = ".$file_id:"")."
			ORDER BY F.ID
		";

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	//check fields before writing
	function CheckFields($arFields, $ID)
	{
		global $DB;
		$this->LAST_ERROR = "";
		$aMsg = array();

		if(array_key_exists("FROM_FIELD", $arFields))
		{
			if(strlen($arFields["FROM_FIELD"]) == 0)
				$aMsg[] = array("id"=>"FROM_FIELD", "text"=>GetMessage("iwebsms_class_post_err_from"));
		}

		if(!array_key_exists("DIRECT_SEND", $arFields) || $arFields["DIRECT_SEND"]=="N")
		{
			if(array_key_exists("TO_FIELD", $arFields) && strlen($arFields["TO_FIELD"]) <= 0)
				$aMsg[] = array("id"=>"TO_FIELD", "text"=>GetMessage("iwebsms_class_post_err_to"));
		}
		
		if(array_key_exists("TO_FIELD", $arFields))
		{
			if(strlen($arFields["TO_FIELD"]) > 0 && !CIWebSMS::MakePhoneNumber($arFields["TO_FIELD"])){
				$aMsg[] = array("id"=>"TO_FIELD", "text"=> GetMessage("iwebsms_class_post_err_phone_to"));
			}
		}

		if(array_key_exists("SUBJECT", $arFields))
		{
			if(strlen($arFields["SUBJECT"])<=0)
				$aMsg[] = array("id"=>"SUBJECT", "text"=>GetMessage("iwebsms_class_post_err_subj"));
		}

		if(array_key_exists("BODY", $arFields))
		{
			if(strlen($arFields["BODY"])<=0)
				$aMsg[] = array("id"=>"BODY", "text"=>GetMessage("iwebsms_class_post_err_text"));
		}

		if(array_key_exists("AUTO_SEND_TIME", $arFields) && $arFields["AUTO_SEND_TIME"]!==false)
		{
			if($DB->IsDate($arFields["AUTO_SEND_TIME"], false, false, "FULL")!==true)
				$aMsg[] = array("id"=>"AUTO_SEND_TIME", "text"=>GetMessage("iwebsms_class_post_err_auto_time"));
		}

		if(array_key_exists("CHARSET", $arFields))
		{
			$aCharset = explode(",", COption::GetOptionString("imaginweb.sms", "posting_charset"));
			if(!in_array($arFields["CHARSET"], $aCharset))
				$aMsg[] = array("id"=>"CHARSET", "text"=>GetMessage("iwebsms_class_post_err_charset"));
		}

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
			$this->LAST_ERROR = $e->GetString();
			return false;
		}

		return true;
	}

	//relation with categories
	function UpdateRubrics($ID, $aRubric)
	{
		global $DB;
		$ID = intval($ID);

		$DB->Query("DELETE FROM iwebsms_posting_rubric WHERE POSTING_ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$arID = array();
		if(is_array($aRubric))
			foreach($aRubric as $i)
				$arID[] = intval($i);
		if(count($arID)>0)
			$DB->Query("
				INSERT INTO iwebsms_posting_rubric (POSTING_ID, LIST_RUBRIC_ID)
				SELECT ".$ID.", ID
				FROM iwebsms_list_rubric
				WHERE ID IN (".implode(", ",$arID).")
				", false, "File: ".__FILE__."<br>Line: ".__LINE__
			);
	}

	//relation with user groups
	function UpdateGroups($ID, $aGroup)
	{
		global $DB;
		$ID = intval($ID);

		$DB->Query("DELETE FROM iwebsms_posting_group WHERE POSTING_ID='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$arID = array();
		if(is_array($aGroup))
			foreach($aGroup as $i)
				$arID[] = intval($i);
		if(count($arID)>0)
			$DB->Query("
				INSERT INTO iwebsms_posting_group (POSTING_ID, GROUP_ID)
				SELECT ".$ID.", ID
				FROM b_group
				WHERE ID IN (".implode(", ",$arID).")
				", false, "File: ".__FILE__."<br>Line: ".__LINE__
			);
	}

	//Addition
	function Add($arFields)
	{
		global $DB;

		if(!$this->CheckFields($arFields, 0))
			return false;

		if(!array_key_exists("MSG_CHARSET", $arFields))
			$arFields["MSG_CHARSET"] = LANG_CHARSET;
		$arFields["VERSION"] = '2';

		$ID = $DB->Add("iwebsms_posting", $arFields, Array("BCC_FIELD","BODY"));
		if($ID > 0)
		{
			$this->UpdateRubrics($ID, $arFields["RUB_ID"]);
			$this->UpdateGroups($ID, $arFields["GROUP_ID"]);
		}
		return $ID;
	}

	//Update
	function Update($ID, $arFields)
	{
		global $DB, $USER;
		$ID = intval($ID);

		if(!$this->CheckFields($arFields, $ID))
			return false;

		$strUpdate = $DB->PrepareUpdate("iwebsms_posting", $arFields);
		if($strUpdate!="")
		{
			$strSql = "UPDATE iwebsms_posting SET ".$strUpdate." WHERE ID=".$ID;
			$arBinds = array(
				"BCC_FIELD" => $arFields["BCC_FIELD"],
				//"SENT_BCC" => $arFields["SENT_BCC"],
				"BODY" => $arFields["BODY"],
				//"ERROR_PHONE" => $arFields["ERROR_PHONE"],
				//"BCC_TO_SEND" => $arFields["BCC_TO_SEND"],
			);
			if(!$DB->QueryBind($strSql, $arBinds))
				return false;
		}
		if(is_set($arFields, "RUB_ID"))
			$this->UpdateRubrics($ID, $arFields["RUB_ID"]);
		if(is_set($arFields, "GROUP_ID"))
			$this->UpdateGroups($ID, $arFields["GROUP_ID"]);

		return true;
	}

	function GetPhones($post_arr)
	{
		$aPhone = array();

		//send to categories
		$aPostRub = array();
		$post_rub = SMSCPostingGeneral::GetRubricList($post_arr["ID"]);
		while($post_rub_arr = $post_rub->Fetch())
			$aPostRub[] = $post_rub_arr["ID"];

		$subscr = SMSCSubscription::GetList(
			array("ID"=>"ASC"),
			array("RUBRIC_MULTI" => $aPostRub, "CONFIRMED" => "Y", "ACTIVE" => "Y",
				"FORMAT" => $post_arr["SUBSCR_FORMAT"], "PHONE" => $post_arr["PHONE_FILTER"])
		);
		while(($subscr_arr = $subscr->Fetch()))
			$aPhone[] = $subscr_arr["PHONE"];

		//send to user groups
		$aPostGrp = array();
		$post_grp = SMSCPostingGeneral::GetGroupList($post_arr["ID"]);
		while($post_grp_arr = $post_grp->Fetch())
			$aPostGrp[] = $post_grp_arr["ID"];

		if(count($aPostGrp)>0)
		{
			$user = CUser::GetList(
				($b="id"), ($o="asc"),
				array("GROUP_MULTI"=>$aPostGrp, "ACTIVE"=>"Y", "PHONE"=>$post_arr["PHONE_FILTER"])
			);
			while(($user_arr = $user->Fetch()))
				$aPhone[] = $user_arr["PHONE"];
		}

		//from additional emails
		$BCC = $post_arr["BCC_FIELD"];
		if($post_arr["DIRECT_SEND"] == "Y")
			$BCC .= ($BCC <> ""? ",":"").$post_arr["TO_FIELD"];
		if($BCC <> "")
		{
			$BCC = str_replace("\r\n", "\n", $BCC);
			$BCC = str_replace("\n", ",", Trim($BCC));
			$aBcc = explode(",", $BCC);
			for($i=0; $i<count($aBcc); $i++)
				if(Trim($aBcc[$i]) <> "")
					$aPhone[] = Trim($aBcc[$i]);
		}

		$aPhone = array_unique($aPhone);

		return $aPhone;
	}

	function AutoSend($ID=false, $limit=false, $site_id=false)
	{

		if($ID===false)
		{
			//Here is cron job entry
			$cPosting = new SMSCPosting;
			$rsPosts = SMSCPosting::GetList(
				array("AUTO_SEND_TIME"=>"ASC", "ID"=>"ASC"),
				array("STATUS_ID"=>"P", "AUTO_SEND_TIME_2"=>ConvertTimeStamp(false, "FULL"))
			);
			while($arPosts=$rsPosts->Fetch())
			{
				if($limit===true)
				{
					$maxcount = COption::GetOptionInt("imaginweb.sms", "subscribe_max_sms_per_hit") - self::$current_emails_per_hit;
					if($maxcount <= 0)
						break;
				}
				else
				{
					$maxcount = 0;
				}
				$cPosting->SendMessage($arPosts["ID"], 0, $maxcount);
			}
		}
		else
		{
			if($site_id && $site_id != SITE_ID)
			{
				return "SMSCPosting::AutoSend(".$ID.($limit? ",true": ",false").",\"".$site_id."\");";
			}

			//Here is agent entry
			if($limit===true)
			{
 				$maxcount = COption::GetOptionInt("imaginweb.sms", "subscribe_max_emails_per_hit") - self::$current_emails_per_hit;
				if($maxcount <= 0)
					return "SMSCPosting::AutoSend(".$ID.",true".($site_id? ",\"".$site_id."\"": "").");";
			}
			else
			{
				$maxcount = 0;
			}

			$cPosting = new SMSCPosting;
			$res = $cPosting->SendMessage($ID, COption::GetOptionString("imaginweb.sms", "posting_interval"), $maxcount, true);
			if($res == "CONTINUE")
				return "SMSCPosting::AutoSend(".$ID.($limit? ",true": ",false").($site_id?",\"".$site_id."\"":"").");";
		}
		return "";
	}

	//Send message
	function SendMessage($ID, $timeout=0, $maxcount=0, $check_charset=false)
	{
		global $DB, $APPLICATION;

		$eol = CEvent::GetMailEOL();
		$ID = intval($ID);
		$timeout = intval($timeout);
		$start_time = getmicrotime();

		@set_time_limit(0);
		$this->LAST_ERROR = "";

		$post = $this->GetByID($ID);
		if(!($post_arr = $post->Fetch()))
		{
			$this->LAST_ERROR .= GetMessage("iwebsms_class_post_err_notfound");
			return false;
		}
		
		if($post_arr["STATUS"] != "P")
		{
			$this->LAST_ERROR .= GetMessage("iwebsms_class_post_err_status")."<br>";
			return false;
		}

		if(
			$check_charset
			&& (strlen($post_arr["MSG_CHARSET"]) > 0)
			&& (strtoupper($post_arr["MSG_CHARSET"]) != strtoupper(LANG_CHARSET))
		)
		{
			return "CONTINUE";
		}

		if(SMSCPosting::Lock($ID) === false)
		{
			if($e = $APPLICATION->GetException())
			{
				$this->LAST_ERROR .= GetMessage("iwebsms_class_post_err_lock")."<br>".$e->GetString();
				if(strpos($this->LAST_ERROR, "PLS-00201") !== false && strpos($this->LAST_ERROR, "'DBMS_LOCK'") !== false)
					$this->LAST_ERROR .= "<br>".GetMessage("iwebsms_class_post_err_lock_advice");
				$APPLICATION->ResetException();
				return false;
			}
			else
			{
				return "CONTINUE";
			}
		}

		if($post_arr["VERSION"] <> '2')
		{
			if(is_string($post_arr["BCC_TO_SEND"]) && strlen($post_arr["BCC_TO_SEND"])>0)
			{
				$a =  explode(",", $post_arr["BCC_TO_SEND"]);
				foreach($a as $e)
					$DB->Query("INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE) VALUES (".$ID.", 'Y', '".$DB->ForSQL($e)."')");
			}

			if(is_string($post_arr["ERROR_PHONE"]) && strlen($post_arr["ERROR_PHONE"])>0)
			{
				$a =  explode(",", $post_arr["ERROR_PHONE"]);
				foreach($a as $e)
					$DB->Query("INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE) VALUES (".$ID.", 'E', '".$DB->ForSQL($e)."')");
			}

			if(is_string($post_arr["SENT_BCC"]) && strlen($post_arr["SENT_BCC"])>0)
			{
				$a =  explode(",", $post_arr["SENT_BCC"]);
				foreach($a as $e)
					$DB->Query("INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE) VALUES (".$ID.", 'N', '".$DB->ForSQL($e)."')");
			}

			$DB->Query("UPDATE iwebsms_posting SET VERSION='2', BCC_TO_SEND=null, ERROR_PHONE=null, SENT_BCC=null WHERE ID=".$ID);
		}

		if(strlen($post_arr["CHARSET"]) > 0)
		{
			$from_charset = $post_arr["MSG_CHARSET"]? $post_arr["MSG_CHARSET"]: SITE_CHARSET;
			$post_arr["BODY"] = $APPLICATION->ConvertCharset($post_arr["BODY"], $from_charset, $post_arr["CHARSET"]);
			$post_arr["SUBJECT"] = $APPLICATION->ConvertCharset($post_arr["SUBJECT"], $from_charset, $post_arr["CHARSET"]);
			$post_arr["FROM_FIELD"] = $APPLICATION->ConvertCharset($post_arr["FROM_FIELD"], $from_charset, $post_arr["CHARSET"]);
		}

		//Preparing message header, text, subject
		$sBody = str_replace("\r\n","\n",$post_arr["BODY"]);
		if(COption::GetOptionString("main", "CONVERT_UNIX_NEWLINE_2_WINDOWS", "N") == "Y")
			$sBody = str_replace("\n", "\r\n", $sBody);

		/* if(COption::GetOptionString("imaginweb.sms", "allow_8bit_chars") <> "Y")
		{
			$sSubject = SMSCMailTools::EncodeSubject($post_arr["SUBJECT"], $post_arr["CHARSET"]);
			$sFrom = SMSCMailTools::EncodeHeaderFrom($post_arr["FROM_FIELD"], $post_arr["CHARSET"]);
		}
		else
		{
			$sSubject = $post_arr["SUBJECT"];
			$sFrom = $post_arr["FROM_FIELD"];
		} */

		/* if($post_arr["BODY_TYPE"] == "html")
		{
			//URN2URI
			$tools = new SMSCMailTools;
			$sBody = $tools->ReplaceHrefs($sBody);
		}

		$bHasAttachments = false;
		if($post_arr["BODY_TYPE"]=="html" && COption::GetOptionString("imaginweb.sms", "attach_images")=="Y")
		{
			//MIME with attachments
			$tools = new SMSCMailTools;
			$sBody = $tools->ReplaceImages($sBody);
			if(count($tools->aMatches) > 0)
			{
				$bHasAttachments = true;

				$sBoundary = "----------".uniqid("");
				$sHeader =
					'From: '.$sFrom.$eol.
					'MIME-Version: 1.0'.$eol.
					'Content-Type: multipart/related; boundary="'.$sBoundary.'"'.$eol.
					'Content-Transfer-Encoding: 8bit';

				$sBody =
					"--".$sBoundary.$eol.
					"Content-Type: ".($post_arr["BODY_TYPE"]=="html"? "text/html":"text/plain").($post_arr["CHARSET"]<>""? "; charset=".$post_arr["CHARSET"]:"").$eol.
					"Content-Transfer-Encoding: 8bit".$eol.$eol.
					$sBody.$eol;

				foreach($tools->aMatches as $attachment)
				{
					$aImage = @getimagesize($_SERVER["DOCUMENT_ROOT"].$attachment["SRC"]);
					if($aImage === false)
						continue;

					$filename = $_SERVER["DOCUMENT_ROOT"].$attachment["SRC"];
					$handle = fopen($filename, "rb");
					$file = fread($handle, filesize($filename));
					fclose($handle);

					$sBody .=
						$eol."--".$sBoundary.$eol.
						"Content-Type: ".(function_exists("image_type_to_mime_type")? image_type_to_mime_type($aImage[2]) : SMSCMailTools::ImageTypeToMimeType($aImage[2]))."; name=\"".$attachment["DEST"]."\"".$eol.
						"Content-Transfer-Encoding: base64".$eol.
						"Content-ID: <".$attachment["ID"].">".$eol.$eol.
						chunk_split(base64_encode($file), 72, $eol);
				}
			}
		}

		$rsFile = SMSCPosting::GetFileList($ID);
		$arFile = $rsFile->Fetch();
		if($arFile)
		{
			if(!$bHasAttachments)
			{
				$bHasAttachments = true;
				$sBoundary = "----------".uniqid("");
				$sHeader =
					"From: ".$sFrom.$eol.
					"MIME-Version: 1.0".$eol.
					"Content-Type: multipart/related; boundary=\"".$sBoundary."\"".$eol.
					"Content-Transfer-Encoding: 8bit";

				$sBody =
					"--".$sBoundary.$eol.
					"Content-Type: ".($post_arr["BODY_TYPE"]=="html"? "text/html":"text/plain").($post_arr["CHARSET"]<>""? "; charset=".$post_arr["CHARSET"]:"").$eol.
					"Content-Transfer-Encoding: 8bit".$eol.$eol.
					$sBody.$eol;
			}

			do {
				$sBody .=
					$eol."--".$sBoundary.$eol.
					"Content-Type: ".$arFile["CONTENT_TYPE"]."; name=\"".$arFile["ORIGINAL_NAME"]."\"".$eol.
					"Content-Transfer-Encoding: base64".$eol.
					"Content-Disposition: attachment; filename=\"".$arFile["ORIGINAL_NAME"]."\"".$eol.$eol;

				$arTempFile = CFile::MakeFileArray($arFile["ID"]);
				$sBody .= chunk_split(
					base64_encode(
						file_get_contents($arTempFile["tmp_name"])
					),
					72,
					$eol
				);
			} while ($arFile = $rsFile->Fetch());
		}

		if($bHasAttachments)
		{
			$sBody .= $eol."--".$sBoundary."--".$eol;
		}
		else
		{
			//plain message without MIME
			$sHeader =
				"From: ".$sFrom.$eol.
				"MIME-Version: 1.0".$eol.
				"Content-Type: ".($post_arr["BODY_TYPE"]=="html"? "text/html":"text/plain").($post_arr["CHARSET"]<>""? "; charset=".$post_arr["CHARSET"]:"").$eol.
				"Content-Transfer-Encoding: 8bit";
		} */
		
		//$mail_additional_parameters = trim(COption::GetOptionString("imaginweb.sms", "mail_additional_parameters"));
		if($post_arr["DIRECT_SEND"] == "Y"){
			//personal delivery
			/* $arEvents = array();
			$rsEvents = GetModuleEvents("imaginweb.sms", "BeforePostingSendMail");
			while($arEvent = $rsEvents->Fetch())
				$arEvents[]=$arEvent; */
			$n = 1;
			$rsSms = $DB->Query("SELECT * FROM iwebsms_posting_sms WHERE POSTING_ID = ".$ID." AND STATUS='Y'");
			while($arSms = $rsSms->Fetch()){
				//Event part
				$arFields = array(
					"POSTING_ID" => $ID,
					"PHONE" => $arSms["PHONE"],
					//"SUBJECT" => $sSubject,
					"BODY" => $sBody,
					"PHONE_EX" => $arSms,
				);
				
				/* foreach($arEvents as $arEvent)
					$arFields = ExecuteModuleEventEx($arEvent, array($arFields)); */
				//Sending

				if(is_array($arFields)){
					$result = CIWebSMS::Send($arFields["PHONE"], $arFields["BODY"], array("ORIGINATOR" => $post_arr["FROM_FIELD"]));
					//$result = bxmail($arFields["PHONE"], $arFields["SUBJECT"], $arFields["BODY"], $arFields["HEADER"], $mail_additional_parameters);
				}
				else{
					$result = $arFields !== false;
				}

				//Result check and iteration
				if($result){
					$DB->Query("UPDATE iwebsms_posting_sms SET STATUS='N' WHERE ID = ".$arSms["ID"]);
				}
				else{
					$DB->Query("UPDATE iwebsms_posting_sms SET STATUS='E' WHERE ID = ".$arSms["ID"]);
				}

				if($timeout > 0 && getmicrotime()-$start_time >= $timeout){
					break;
				}
				if($maxcount > 0 && $n >= $maxcount){
					break;
				}
				$n++;
				self::$current_emails_per_hit++;
			}
		}
		/* else
		{
			//BCC delivery
			$max_bcc_count = intval(COption::GetOptionString("imaginweb.sms", "max_bcc_count"));
			$aStep = array();
			$rsSms = $DB->Query("SELECT * FROM iwebsms_posting_sms WHERE POSTING_ID = ".$ID." AND STATUS='Y'");
			while($arSms = $rsSms->Fetch()){
				$aStep[$arSms["ID"]] = $arSms["PHONE"];
				if($max_bcc_count > 0 && count($aStep) >= $max_bcc_count){
					break;
				}
			}
			
			if(count($aStep) > 0)
			{
				$BCC = implode(",", $aStep);
				$sHeaderStep = $sHeader.$eol."Bcc: ".$BCC;
				$result = bxmail($post_arr["TO_FIELD"], $sSubject, $sBody, $sHeaderStep, $mail_additional_parameters);
				if($result){
					$DB->Query("UPDATE iwebsms_posting_sms SET STATUS='N' WHERE ID in (".implode(", ", array_keys($aStep)).")");
				}
				else{
					$DB->Query("UPDATE iwebsms_posting_sms SET STATUS='E' WHERE ID in (".implode(", ", array_keys($aStep)).")");
					$this->LAST_ERROR .= GetMessage("iwebsms_class_post_err_mail")."<br>";
				}
			}
		} */

		//set status and delivered and error emails
		$arStatuses = $this->GetSmsStatuses($ID);
		
		if(!array_key_exists("Y", $arStatuses))
		{
			$STATUS = array_key_exists("E", $arStatuses)? "E": "S";
			$DATE = $DB->GetNowFunction();
		}
		else
		{
			$STATUS = "P";
			$DATE = "null";
		}

		SMSCPosting::UnLock($ID);

		$DB->Query("UPDATE iwebsms_posting SET STATUS='".$STATUS."', DATE_SENT=".$DATE." WHERE ID=".$ID);

		return ($STATUS == "P" ? "CONTINUE" : true);
	}

	function GetSmsStatuses($ID)
	{
		global $DB;
		$arStatuses = array();
		$rs = $DB->Query("
			SELECT STATUS, COUNT(*) CNT
			FROM iwebsms_posting_sms
			WHERE POSTING_ID = ".intval($ID)."
			GROUP BY STATUS
		");
		while($ar = $rs->Fetch())
			$arStatuses[$ar["STATUS"]] = $ar["CNT"];
		return $arStatuses;
	}

	function GetSmssByStatus($ID, $STATUS)
	{
		global $DB;

		return $DB->Query("
			SELECT *
			FROM iwebsms_posting_sms
			WHERE POSTING_ID = ".intval($ID)."
			AND STATUS = '".$DB->ForSQL($STATUS)."'
			ORDER BY PHONE
		");
	}

	function ChangeStatus($ID, $status)
	{
		global $DB;
		$FIELD_PHONE = COption::GetOptionString("imaginweb.sms", "subscribe_field_phone");
		$ID = intval($ID);
		$this->LAST_ERROR = "";

		$strSql = "SELECT STATUS, VERSION FROM iwebsms_posting WHERE ID=".$ID;
		$db_result = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$arResult = $db_result->Fetch();
		if(!$arResult)
		{
			$this->LAST_ERROR = GetMessage("iwebsms_class_post_err_notfound")."<br>";
			return false;
		}

		if($arResult["STATUS"] == $status)
			return true;

		switch($arResult["STATUS"].$status)
		{
			case "DP":
				//BCC_TO_SEND fill
				$post = $this->GetByID($ID);
				if(!($post_arr = $post->Fetch())){
					$this->LAST_ERROR .= GetMessage("iwebsms_class_post_err_notfound")."<br>";
					return false;
				}
				
				$DB->Query("DELETE from iwebsms_posting_sms WHERE POSTING_ID = ".$ID);
				
				$DB->Query("
					INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE, SUBSCRIPTION_ID, USER_ID)
					SELECT DISTINCT
						PR.POSTING_ID, 'Y', S.PHONE, S.ID, S.USER_ID
					FROM
						iwebsms_posting_rubric PR
						INNER JOIN iwebsms_subscription_rubric SR ON SR.LIST_RUBRIC_ID = PR.LIST_RUBRIC_ID
						INNER JOIN iwebsms_subscription S ON S.ID = SR.SUBSCRIPTION_ID
						LEFT JOIN b_user U ON U.ID = S.USER_ID
					WHERE
						PR.POSTING_ID = ".$ID."
						AND S.CONFIRMED = 'Y'
						AND S.ACTIVE = 'Y'
						AND (U.ID IS NULL OR U.ACTIVE = 'Y')
						".(strlen($post_arr["SUBSCR_FORMAT"]) <= 0 || $post_arr["SUBSCR_FORMAT"]==="NOT_REF" ? "": "AND S.FORMAT='".($post_arr["SUBSCR_FORMAT"]=="text"? "text": "html")."'")."
						".(strlen($post_arr["PHONE_FILTER"]) <= 0 || $post_arr["PHONE_FILTER"]==="NOT_REF" ? "": "AND ".GetFilterQuery("S.PHONE", $post_arr["PHONE_FILTER"], "Y", array("@", ".", "_")))."
				");
				
				//send to user groups
				$res = $DB->Query("SELECT * FROM iwebsms_posting_group WHERE POSTING_ID = ".$ID." AND GROUP_ID = 2");
				if($res->Fetch()){
					$DB->Query("
						INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE, SUBSCRIPTION_ID, USER_ID)
						SELECT
							".$ID.", 'Y', U.$FIELD_PHONE, NULL, MIN(U.ID)
						FROM
							b_user U
						WHERE
							U.ACTIVE = 'Y'
							AND NOT (length(U.$FIELD_PHONE) <= 0)
							".(strlen($post_arr["PHONE_FILTER"]) <= 0 || $post_arr["PHONE_FILTER"] === "NOT_REF" ? "" : "AND ".GetFilterQuery("U.$FIELD_PHONE", $post_arr["PHONE_FILTER"], "Y", array("@", ".", "_")))."
							AND U.$FIELD_PHONE not in (SELECT PHONE FROM iwebsms_posting_sms WHERE POSTING_ID = ".$ID.")
						GROUP BY U.$FIELD_PHONE
					");
				}
				else{
					$DB->Query("
						INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE, SUBSCRIPTION_ID, USER_ID)
						SELECT
							PG.POSTING_ID, 'Y', U.$FIELD_PHONE, NULL, MIN(U.ID)
						FROM
							iwebsms_posting_group PG
							INNER JOIN b_user_group UG ON UG.GROUP_ID = PG.GROUP_ID
							INNER JOIN b_user U ON U.ID = UG.USER_ID
						WHERE
							PG.POSTING_ID = ".$ID."
							AND (UG.DATE_ACTIVE_FROM is null or UG.DATE_ACTIVE_FROM <= ".$DB->CurrentTimeFunction().")
							AND (UG.DATE_ACTIVE_TO is null or UG.DATE_ACTIVE_TO >= ".$DB->CurrentTimeFunction().")
							AND U.ACTIVE = 'Y'
							AND NOT (length(U.$FIELD_PHONE) <= 0)
							".(strlen($post_arr["PHONE_FILTER"]) <= 0 || $post_arr["PHONE_FILTER"]==="NOT_REF" ? "": "AND ".GetFilterQuery("U.$FIELD_PHONE", $post_arr["PHONE_FILTER"], "Y", array("@", ".", "_")))."
							AND U.$FIELD_PHONE not in (SELECT PHONE FROM iwebsms_posting_sms WHERE POSTING_ID = ".$ID.")
						GROUP BY PG.POSTING_ID, U.$FIELD_PHONE
					");
				}
				//from additional emails
				$BCC = $post_arr["BCC_FIELD"];
				if($post_arr["DIRECT_SEND"] == "Y"){
					$BCC .= ($BCC <> "" ? "," : "").$post_arr["TO_FIELD"];
				}
				$BCC = str_replace("\r\n", "\n", $BCC);
				$BCC = str_replace("\n", ",", $BCC);
				$aBcc = explode(",", $BCC);
				
				foreach($aBcc as $phone){
					$phone = trim($phone);
					if($phone <> ""){
						$DB->Query("
							INSERT INTO iwebsms_posting_sms (POSTING_ID, STATUS, PHONE, SUBSCRIPTION_ID, USER_ID)
							SELECT
								P.ID, 'Y', '".($DB->ForSQL($phone))."', NULL, NULL
							FROM
								iwebsms_posting P
							WHERE
								P.ID = ".$ID."
								and '".($DB->ForSQL($phone))."' not in (SELECT PHONE FROM iwebsms_posting_sms WHERE POSTING_ID = ".$ID.")
						");
					}
				}
				
				$res = $DB->Query("SELECT count(*) CNT from iwebsms_posting_sms WHERE POSTING_ID = ".$ID);
				$ar = $res->Fetch();

				if($ar["CNT"] > 0)
				{
					$DB->Query("UPDATE iwebsms_posting SET STATUS='".$status."', VERSION='2', BCC_TO_SEND=null, ERROR_PHONE=null, SENT_BCC=null WHERE ID=".$ID);
				}
				else
				{
					$this->LAST_ERROR .= GetMessage("iwebsms_class_post_err_status4");
					return false;
				}
				break;
			case "PW":
			case "WP":
			case "PE":
			case "PS":
				$DB->Query("UPDATE iwebsms_posting SET STATUS='".$status."' WHERE ID=".$ID);
				break;
			case "EW"://This is the way to resend error e-mails
			case "EP":
				if($arResult["VERSION"] == "2")
				{
					$DB->Query("UPDATE iwebsms_posting_sms SET STATUS='Y' WHERE POSTING_ID=".$ID." AND STATUS='E'");
					$DB->Query("UPDATE iwebsms_posting SET STATUS='".$status."' WHERE ID=".$ID);
				}
				else
				{
					//Send it in old fashion way
					$DB->Query("UPDATE iwebsms_posting SET STATUS='".$status."', BCC_TO_SEND=ERROR_PHONE, ERROR_PHONE=null WHERE ID=".$ID);
				}
				break;
			case "ED":
			case "SD":
			case "WD":
				$DB->Query("UPDATE iwebsms_posting SET STATUS='".$status."', VERSION='2', SENT_BCC=null, ERROR_PHONE=null, BCC_TO_SEND=null, DATE_SENT=null WHERE ID=".$ID);
				break;
			default:
				$this->LAST_ERROR = GetMessage("iwebsms_class_post_err_status2");
				return false;
		}

		return true;
	}
}

class SMSCMailTools
{
	var $aMatches = array();
	var $pcre_backtrack_limit = false;
	var $server_name = null;

	function IsEightBit($str)
	{
		$len = strlen($str);
		for($i=0; $i<$len; $i++)
			if(ord(substr($str, $i, 1))>>7)
				return true;
		return false;
	}

	function EncodeMimeString($text, $charset)
	{
		if(!SMSCMailTools::IsEightBit($text))
			return $text;

		$maxl = IntVal((76 - strlen($charset) + 7)*0.4);

		$res = "";
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

	function __replace_img($matches)
	{
		$src = $matches[3];
		if($src <> "")
		{
			if(array_key_exists($src, $this->aMatches))
			{
				$uid = $this->aMatches[$src]["ID"];
			}
			elseif(file_exists($_SERVER["DOCUMENT_ROOT"].$src) && is_array(@getimagesize($_SERVER["DOCUMENT_ROOT"].$src)))
			{
				$dest = basename($src);
				$uid = uniqid(md5($dest));
				$this->aMatches[$src] = array("SRC"=>$src, "DEST"=>$dest, "ID"=>$uid);
			}
			else
				return $matches[0];
			return $matches[1].$matches[2]."cid:".$uid.$matches[4].$matches[5];
		}
		return $matches[0];
	}

	function ReplaceHrefs($text)
	{
		if($this->pcre_backtrack_limit === false)
			$this->pcre_backtrack_limit = intval(ini_get("pcre.backtrack_limit"));
		$text_len = defined("BX_UTF")? mb_strlen($text, 'latin1'): strlen($text);
		$text_len++;
		if($this->pcre_backtrack_limit < $text_len)
		{
			@ini_set("pcre.backtrack_limit", $text_len);
			$this->pcre_backtrack_limit = intval(ini_get("pcre.backtrack_limit"));
		}

		if(!isset($this->server_name))
			$this->server_name = COption::GetOptionString("main", "server_name", "");

		if($this->server_name != '')
			$text = preg_replace(
				"/(<a\\s[^>]*?(?<=\\s)href\\s*=\\s*)([\"'])(\\/.*?)(\\2)(\\s.+?>|\\s*>)/is",
				"\\1\\2http://".$this->server_name."\\3\\4\\5",
				$text
			);

		return $text;
	}

	function ReplaceImages($text)
	{
		if($this->pcre_backtrack_limit === false)
			$this->pcre_backtrack_limit = intval(ini_get("pcre.backtrack_limit"));
		$text_len = defined("BX_UTF")? mb_strlen($text, 'latin1'): strlen($text);
		$text_len++;
		if($this->pcre_backtrack_limit < $text_len)
		{
			@ini_set("pcre.backtrack_limit", $text_len);
			$this->pcre_backtrack_limit = intval(ini_get("pcre.backtrack_limit"));
		}
		$this->aMatches = array();
		$text = preg_replace_callback(
			"/(<img\\s[^>]*?(?<=\\s)src\\s*=\\s*)([\"']?)(.*?)(\\2)(\\s.+?>|\\s*>)/is",
			array(&$this, "__replace_img"),
			$text
		);
		$text = preg_replace_callback(
			"/(background-image\\s*:\\s*url\\s*\\()([\"']?)(.*?)(\\2)(\\s*\\);)/is",
			array(&$this, "__replace_img"),
			$text
		);
		$text = preg_replace_callback(
			"/(<td\\s[^>]*?(?<=\\s)background\\s*=\\s*)([\"']?)(.*?)(\\2)(\\s.+?>|\\s*>)/is",
			array(&$this, "__replace_img"),
			$text
		);
		$text = preg_replace_callback(
			"/(<table\\s[^>]*?(?<=\\s)background\\s*=\\s*)([\"']?)(.*?)(\\2)(\\s.+?>|\\s*>)/is",
			array(&$this, "__replace_img"),
			$text
		);
		return $text;
	}

	function ImageTypeToMimeType($type)
	{
		$aTypes = array(
			1 => "image/gif",
			2 => "image/jpeg",
			3 => "image/png",
			4 => "application/x-shockwave-flash",
			5 => "image/psd",
			6 => "image/bmp",
			7 => "image/tiff",
			8 => "image/tiff",
			9 => "application/octet-stream",
			10 => "image/jp2",
			11 => "application/octet-stream",
			12 => "application/octet-stream",
			13 => "application/x-shockwave-flash",
			14 => "image/iff",
			15 => "image/vnd.wap.wbmp",
			16 => "image/xbm",
		);
		if(!empty($aTypes[$type]))
			return $aTypes[$type];
		else
			return "application/octet-stream";
	}
}
?>