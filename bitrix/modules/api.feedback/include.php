<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class CApiFeedback
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{

	}

	function Send($event_name, $site_id, $arFields, $Duplicate="Y", $message_id=false, $user_mess=false, $semi_rand=false, $arFieldsCodeName = array())
	{
		if(!$user_mess)
			foreach(GetModuleEvents('api.feedback', "OnBeforeEmailSend", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$event_name, &$site_id, &$arFields, &$message_id));

		$strFields =  $strFieldsNames =  $SITE_NAME = "";
		$bReturn = false;

		if(!$semi_rand) $semi_rand = md5(time());

		$arFilter = Array(
			//"ID"            => $message_id,
			"TYPE_ID"       => $event_name,
			"SITE_ID"       => $site_id,
			"ACTIVE"        => "Y",
		);
        if($message_id) $arFilter['ID'] = $message_id;

        $arMess = array();
		$rsMess = CEventMessage::GetList($by="id", $order="asc", $arFilter);
		while($obMess = $rsMess->Fetch())
			$arMess[] = $obMess;

		$rs_sites = CSite::GetList($by="sort", $order="desc");
		while ($ar_site = $rs_sites->Fetch())
			$arSites[$ar_site['ID']] = $ar_site;

		if(count($arSites)>1 && $site_id)
			$SITE_NAME = $arSites[$site_id]['SITE_NAME'];
		else
			$SITE_NAME = COption::GetOptionString("main", "site_name", $GLOBALS["SERVER_NAME"]);

		if(!empty($arMess))
		{
            // boundary
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            foreach($arMess as $k => $v)
			{
                $email_from = !$user_mess ? (($v['EMAIL_FROM'] == '#DEFAULT_EMAIL_FROM#') ? $arFields['DEFAULT_EMAIL_FROM'] : $v['EMAIL_FROM']) : $arFields['EMAIL_FROM'];
                $headers = "From: ". $email_from;

                // headers for attachment
                $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed; charset=\"". SITE_CHARSET ."\";\n" . " boundary=\"{$mime_boundary}\"";


                if($v['BODY_TYPE'] == 'text')
					$v['BODY_TYPE'] = 'plain';

				if(strpos($v['SUBJECT'],'#AUTHOR_MESSAGE_THEME#') !== false)
					$subject = "=?". SITE_CHARSET ."?B?". base64_encode(str_replace('#AUTHOR_MESSAGE_THEME#',$arFields['AUTHOR_MESSAGE_THEME'],$v['SUBJECT'])) . "?=";
				else
					$subject = "=?". SITE_CHARSET ."?B?". base64_encode(str_replace('#SITE_NAME#',$SITE_NAME,$v['SUBJECT'])) . "?=";

				if(!empty($arFields))
				{
					//v1.3.2 - this for #WORK_AREA# in e-mail template
					if(!empty($arFieldsCodeName))
					{
						$i = 0;
						$cnt = count($arFieldsCodeName);
						foreach($arFieldsCodeName as $code=>$name)
						{
							$i++;
							//If empty field value
							if(strlen($arFields[$code]))
							{
								if($v['BODY_TYPE'] == 'html')
								{
									$strFieldsNames .= "<b>". $name ."</b><br>". $arFields[$code] ."<br><br>";
								}
								else
								{
									$strFieldsNames .= $name ."\n". $arFields[$code];
									if($i != $cnt)	$strFieldsNames .= "\n\n";
								}
							}
						}

						//Add FILES array in custom fields if not exist macros FILES in e-mail template
						if(strpos($v['MESSAGE'],'#FILES#') === false)
						{
							if($v['BODY_TYPE'] == 'html')
								$strFieldsNames .="\n\n" . $arFields['FILES'];
							else
							{
								$arExpFilesLink = explode('<br>',$arFields['FILES']);
								if(!empty($arExpFilesLink))
								{
									$i = 0;
									foreach($arExpFilesLink as $fileLink)
									{
										$i++;
										if(strlen(trim($fileLink)))
										{
											if($i == 1)
												$strFieldsNames .= "\n";

											$strFieldsNames .= "\n" .strip_tags(trim($fileLink));
										}
									}
								}
							}
						}

					}

					$search = $replace = array();
					foreach($arFields as $k2=>$v2)
					{

						$search[] = '#'. $k2 .'#';
						$replace[] = ($k2 == 'FILES') ? "\n".$v2 : $v2;
					}
					$strFields = str_replace($search,$replace,$v['MESSAGE']);

					if(strpos($strFields,'#SITE_NAME#') !== false)
						$strFields = str_replace('#SITE_NAME#',$SITE_NAME,$strFields);

					if(strlen($strFieldsNames))
						$strFields = str_replace('#WORK_AREA#',$strFieldsNames,$strFields);
				}

				// multipart boundary
				$message = "--{$mime_boundary}\n" . "Content-Type: text/". $v['BODY_TYPE'] ."; charset=". SITE_CHARSET .";\n" .
						"Content-Transfer-Encoding: 8bit\n\n" . htmlspecialcharsback($strFields) . "\n\n";//iso-8859-1 ::  text/plain

				$message .= "--{$mime_boundary}--";

				$email_to = ($v['EMAIL_TO']=='#EMAIL_TO#') ? $arFields['EMAIL_TO'] : $v['EMAIL_TO'];
                if(bxmail($email_to, $subject, $message,$headers))
				{
					if(!$user_mess)
						foreach(GetModuleEvents('api.feedback', "OnAfterEmailSend", true) as $arEvent)
							ExecuteModuleEventEx($arEvent, array(&$event_name, &$site_id, &$arFields, &$message_id));

                    $bReturn = true;
				}
				else
					return false;
			}

            if($bReturn)
                return true;

		}
		else
			return false;

	}

	/**
	 * Fake translit
	 *
	 * @param string $str
	 * @return string
	 */
	function FakeTranslit($str)
	{
		$str = trim($str);

		$trans_from = explode(",", GetMessage("TRANSLIT_FROM"));
		$trans_to = explode(",", GetMessage("TRANSLIT_TO"));

		$str = str_replace($trans_from, $trans_to, $str);

		$str = preg_replace('/\s+/u', '-', $str);

		return $str;
	}
}