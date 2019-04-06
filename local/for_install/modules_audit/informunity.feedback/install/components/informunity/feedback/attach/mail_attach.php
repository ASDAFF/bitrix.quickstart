<?
function SendAttache($event, $lid, $arFields, $filePath)
{
	global $DB;

	$event = $DB->ForSQL($event);
	$lid = $DB->ForSQL($lid);

	$rsMessTpl = $DB->Query("SELECT * FROM b_event_message WHERE EVENT_NAME='$event' AND LID='$lid';");
	while ($arMessTpl = $rsMessTpl->Fetch())
	{
		// get charset
		$strSql = "SELECT CHARSET FROM b_lang WHERE LID='$lid' ORDER BY DEF DESC, SORT";
		$dbCharset = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		$arCharset = $dbCharset->Fetch();
		$charset = $arCharset["CHARSET"];

		// additional params
		if (!isset($arFields["DEFAULT_EMAIL_FROM"]))
			$arFields["DEFAULT_EMAIL_FROM"] = COption::GetOptionString("main", "email_from", "admin@".$GLOBALS["SERVER_NAME"]);
		if (!isset($arFields["SITE_NAME"]))
			$arFields["SITE_NAME"] = COption::GetOptionString("main", "site_name", $GLOBALS["SERVER_NAME"]);
		if (!isset($arFields["SERVER_NAME"]))
			$arFields["SERVER_NAME"] = COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);

		// replace
		$from = CAllEvent::ReplaceTemplate($arMessTpl["EMAIL_FROM"], $arFields);
		$to = CAllEvent::ReplaceTemplate($arMessTpl["EMAIL_TO"], $arFields);
		$message = CAllEvent::ReplaceTemplate($arMessTpl["MESSAGE"], $arFields);
		$subj = CAllEvent::ReplaceTemplate($arMessTpl["SUBJECT"], $arFields);
		$bcc = CAllEvent::ReplaceTemplate($arMessTpl["BCC"], $arFields);


		$from = trim($from, "\r\n");
		$to = trim($to, "\r\n");
		$subj = trim($subj, "\r\n");
		$bcc = trim($bcc, "\r\n");

		if(COption::GetOptionString("main", "convert_mail_header", "Y")=="Y")
		{
			$from = CAllEvent::EncodeMimeString($from, $charset);
			$to = CAllEvent::EncodeMimeString($to, $charset);
			$subj = CAllEvent::EncodeMimeString($subj, $charset);
		}

		$all_bcc = COption::GetOptionString("main", "all_bcc", "");
		if ($all_bcc != "")
		{
			$bcc .= (strlen($bcc)>0 ? "," : "") . $all_bcc;
			$duplicate = "Y";
		}
		else
		{
			$duplicate = "N";
		}

		$strCFields = "";
		$cSearch = count($arSearch);
		foreach ($arSearch as $id => $key)
		{
			$strCFields .= substr($key, 1, strlen($key)-2)."=".$arReplace[$id];
			if ($id < $cSearch-1)
				$strCFields .= "&";
		}

		if (COption::GetOptionString("main", "CONVERT_UNIX_NEWLINE_2_WINDOWS", "N") == "Y")
			$message = str_replace("\n", "\r\n", $message);

		// read file(s)
		$arFiles = array();
		if (!is_array($filePath))
			$filePath = array($filePath);
		foreach ($filePath as $fPath)
		{
			$arFiles[] = array(
								"F_PATH" => $_SERVER['DOCUMENT_ROOT'].$fPath,
								"F_LINK" => $f = fopen($_SERVER['DOCUMENT_ROOT'].$fPath, "rb")
								);
		}

		$un = strtoupper(uniqid(time()));
		$eol = CAllEvent::GetMailEOL();
		$head = $body = "";

		// header
		$head .= "Mime-Version: 1.0".$eol;
		$head .= "From: $from".$eol;
		if(COption::GetOptionString("main", "fill_to_mail", "N")=="Y")
			$header = "To: $to".$eol;
		$head .= "Reply-To: $from".$eol;
		$head .= "X-Priority: 3 (Normal)".$eol;
		$head .= "X-MID: $messID.".$arMessTpl["ID"]."(".date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL"))).")".$eol;
		$head .= "X-EVENT_NAME: ISALE_KEY_F_SEND".$eol;
		if (strpos($bcc, "@") !== false)
			$head .= "BCC: $bcc".$eol;
		$head .= "Content-Type: multipart/mixed; ";
		$head .= "boundary=\"----".$un."\"".$eol.$eol;

		// body
		$body = "------".$un.$eol;
		if ($arMessTpl["BODY_TYPE"] == "text")
			$body .= "Content-Type:text/plain; charset=".$charset.$eol;
		else
			$body .= "Content-Type:text/html; charset=".$charset.$eol;
		$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$body .= $message.$eol.$eol;

		foreach ($arFiles as $arF)
		{
			$body .= "------".$un.$eol;
			$body .= "Content-Type: application/octet-stream; name=\"".basename($arF["F_PATH"])."\"".$eol;
			$body .= "Content-Disposition:attachment; filename=\"".basename($arF["F_PATH"])."\"".$eol;
			$body .= "Content-Transfer-Encoding: base64".$eol.$eol;
			$body .= chunk_split(base64_encode(fread($arF["F_LINK"], filesize($arF["F_PATH"])))).$eol.$eol;
		}
		$body .= "------".$un."--";

		// send
		if (!defined("ONLY_EMAIL") || $to==ONLY_EMAIL)
			bxmail($to, $subj, $body, $head, COption::GetOptionString("main", "mail_additional_parameters", ""));
	}
}
?>