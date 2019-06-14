<?
global $MESS;
IncludeModuleLangFile(__FILE__);


//CJSCore::Init(array("api_feedback_validation"));
CJSCore::RegisterExt(
	 'api_feedback_validation',
	 array(
			'js'   => '/bitrix/js/api.feedback/validation/jquery.validation.min.js',
			'lang' => '/bitrix/modules/api.feedback/lang/' . LANGUAGE_ID . '/js_lang.php',
			//'css' => ''
			//'rel' => array('jquery')
			//'skip_core' => true,
	 )
);

Class CApiFeedback
{
	/**
	 * @var array
	 */
	public $LAST_ERROR = array();

	function Send($event_name, $site_id, $arFields, $Duplicate = "Y", $message_id = false, $user_mess = false,
	              $mime_boundary = false, $arFieldsCodeName = array(), $arParams = array()
	)
	{
		global $DB;
		$strFields        = $strFieldsNames = $sSiteName = "";
		$this->LAST_ERROR = array();

		/*
		foreach(GetModuleEvents("main", "OnBeforeEventAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$event_name, &$site_id, &$arFields, &$message_id)) === false)
				return false;
		*/


		if(!$user_mess)
			foreach(GetModuleEvents('api.feedback', "OnBeforeEmailSend", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$event_name, &$site_id, &$arFields, &$message_id));


		$arFilter = Array(
			 "TYPE_ID" => $event_name,
			 "SITE_ID" => $site_id,
			 "ACTIVE"  => "Y",
		);
		if($message_id)
			$arFilter['ID'] = $message_id;

		$arMess = array();
		$rsMess = CEventMessage::GetList($by = "id", $order = "asc", $arFilter);
		while($obMess = $rsMess->Fetch())
			$arMess[] = $obMess;

		$arSites  = array();
		$rs_sites = CSite::GetList($by = "sort", $order = "desc", array('ACTIVE' => 'Y'));
		while($ar_site = $rs_sites->Fetch())
			$arSites[ $ar_site['ID'] ] = $ar_site;

		$arCurSite   = ($site_id && count($arSites) > 1) ? $arSites[ $site_id ] : array();
		$sSiteName   = $arCurSite ? $arCurSite['SITE_NAME'] : COption::GetOptionString("main", "site_name", $GLOBALS["SERVER_NAME"]);
		$sServerName = $arCurSite ? $arCurSite['SERVER_NAME'] : COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);

		if($arParams['IBLOCK_ID']) {
			if(CModule::IncludeModule('iblock'))
				$el = new CIBlockElement;
			else
				$arParams['IBLOCK_ID'] = false;
		}

		if(!empty($arMess)) {
			foreach($arMess as $k => $v) {
				$sFilesFields   = $subject = '';
				$v['BODY_TYPE'] = ($v['BODY_TYPE'] == 'text' ? 'plain' : 'html');

				if($v['BODY_TYPE'] == 'html') {
					if($arFields['PAGE_URI'])
						$arFields['PAGE_URI'] = '<a href="' . $arFields['PAGE_URI'] . '">' . $arFields['PAGE_URI'] . '</a>';

					if($arFields['PAGE_URL'])
						$arFields['PAGE_URL'] = '<a href="' . $arFields['PAGE_URL'] . '">' . $arFields['PAGE_URL'] . '</a>';

					if($arFields['DIR_URL'])
						$arFields['DIR_URL'] = '<a href="' . $arFields['DIR_URL'] . '">' . $arFields['DIR_URL'] . '</a>';
				}

				$email_to   = check_email($v['EMAIL_TO']) ? $v['EMAIL_TO'] : $arFields[ str_replace('#', '', $v['EMAIL_TO']) ];
				$email_from = $arCurSite['EMAIL'] ? $arCurSite['EMAIL'] : trim(COption::GetOptionString('main', 'email_from', "noreply@" . $GLOBALS["SERVER_NAME"]));

				$bcc = check_email($v['BCC']) ? trim($v['BCC']) : trim($arFields['BCC']);

				//E-mail in this field has a maximum priority after $arParams['USER_EMAIL']
				if(check_email($v['EMAIL_FROM']))
					$email_from = $v['EMAIL_FROM'];

				if(!$user_mess) {
					//User E-mail has a maximum priority for Admin email
					$email_from = ($arParams['REPLACE_FIELD_FROM'] && $arParams['USER_EMAIL']) ? $arParams['USER_EMAIL'] : $email_from;
				}
				else {
					$email_to = $arParams['USER_EMAIL'];
				}

				//Parse: Tuning-Soft <support@tuning-soft.ru>
				if(preg_match("#(.*)?[<\\[\\(](.*?)[>\\]\\)].*#i", $email_from, $arr) && strlen($arr[1]) > 0 && strlen($arr[2]) > 0) {
					$email_from = "=?" . SITE_CHARSET . "?B?" . base64_encode($arr[1]) . "?= <" . $arr[2] . ">";
				}

				///bitrix/modules/main/lib/mail/mail.php:setHeaders()
				//Parse: Tuning-Soft <support@tuning-soft.ru> TO support@tuning-soft.ru
				//preg_replace("/(.*)\\<(.*)\\>/i", '$2', $email_from);
				$email_to = preg_replace("/(.*)\\<(.*)\\>/i", '$2', $email_to);


				//$subject = "=?". SITE_CHARSET ."?B?". base64_encode($subject) . "?=";
				$headers = "MIME-Version: 1.0\n";
				$headers .= "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\"\n";
				$headers .= "From: {$email_from}\n";

				if($bcc && !$user_mess)
					$headers .= "Bcc: {$bcc}\n";

				//Убираем перенос строки в последнем заголовке, иначе не работает отправка писем через smtp
				//https://marketplace.1c-bitrix.ru/solutions/wsrubi.smtp/
				$headers .= "Return-Path: " . ($arr[2] ? $arr[2] : $email_from);

				if(!empty($arFields)) {
					//For #WORK_AREA# in e-mail template
					if(!empty($arFieldsCodeName)) {
						$i   = 0;
						$cnt = count($arFieldsCodeName);

						//For table html template
						if($arParams['WRITE_MESS_FILDES_TABLE'] && $v['BODY_TYPE'] == 'html') {
							$strFieldsNames .= '<table style="' . $arParams['WRITE_MESS_TABLE_STYLE'] . '"><tbody>';

							foreach($arFieldsCodeName as $code => $name) {
								$i++;

								$curVal = is_array($arFields[ $code ]) ? implode('<br>', $arFields[ $code ]) : $arFields[ $code ];

								if($arParams['WRITE_ONLY_FILLED_VALUES']) {
									if(strlen($curVal) > 0) {
										$strFieldsNames .= '<tr>';
										$strFieldsNames .= '<td style="' . $arParams['WRITE_MESS_TABLE_STYLE_NAME'] . '">' . $name . '</td>';
										$strFieldsNames .= '<td style="' . $arParams['WRITE_MESS_TABLE_STYLE_VALUE'] . '">' . $curVal . '</td>';
										$strFieldsNames .= '</tr>';
									}
								}
								else {
									$strFieldsNames .= '<tr>';
									$strFieldsNames .= '<td style="' . $arParams['WRITE_MESS_TABLE_STYLE_NAME'] . '">' . $name . '</td>';
									$strFieldsNames .= '<td style="' . $arParams['WRITE_MESS_TABLE_STYLE_VALUE'] . '">' . $curVal . '</td>';
									$strFieldsNames .= '</tr>';
								}
							}

							$strFieldsNames .= '</tbody></table>';
						}
						else {
							foreach($arFieldsCodeName as $code => $name) {
								$i++;
								if($v['BODY_TYPE'] == 'html') {
									$curVal = is_array($arFields[ $code ]) ? implode('<br>', $arFields[ $code ]) : $arFields[ $code ];

									if($arParams['WRITE_ONLY_FILLED_VALUES']) {
										if(strlen($curVal) > 0) {
											$strFieldsNames .= "\n<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE'] . "\">";
											$strFieldsNames .= "\n\t<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE_NAME'] . "\">" . $name . "</div>";
											$strFieldsNames .= "\n\t<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE_VALUE'] . "\">" . $curVal . "</div>";
											$strFieldsNames .= "\n</div>";
										}
									}
									else {
										$strFieldsNames .= "\n<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE'] . "\">";
										$strFieldsNames .= "\n\t<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE_NAME'] . "\">" . $name . "</div>";
										$strFieldsNames .= "\n\t<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE_VALUE'] . "\">" . $curVal . "</div>";
										$strFieldsNames .= "\n</div>";
									}
								}
								else {
									$curVal = is_array($arFields[ $code ]) ? implode("\n", $arFields[ $code ]) : $arFields[ $code ];

									if($arParams['WRITE_ONLY_FILLED_VALUES']) {
										if(strlen($curVal) > 0) {
											$strFieldsNames .= "*" . $name . "*\n" . strip_tags($curVal);
										}
									}
									else {
										$strFieldsNames .= "*" . $name . "*\n" . strip_tags($curVal);
									}

									if($i != $cnt)
										$strFieldsNames .= "\n\n";
								}
							}
						}

						unset($curVal);
					}


					//Include FILES
					if($v['BODY_TYPE'] == 'html') {
						if($arFields['FILES'] && !$arParams['SEND_ATTACHMENT'] && !$arParams['DELETE_FILES_AFTER_UPLOAD'])
							$sFilesFields = "<br><br><b>" . GetMessage('DOWNLOAD_FILES') . '</b><br>' . $arFields['FILES'];
						else
							$sFilesFields = "\n" . $arFields['FILES'];
					}
					else {
						//<bold>
						$arExpFilesLink = explode('<br>', $arFields['FILES']);
						if(!empty($arExpFilesLink)) {
							if(!$arParams['SEND_ATTACHMENT'] && !$arParams['DELETE_FILES_AFTER_UPLOAD'])
								$sFilesFields = "\n\n*" . GetMessage('DOWNLOAD_FILES') . "*\n";

							$i = 0;
							foreach($arExpFilesLink as $fileLink) {
								$i++;
								if(strlen(trim($fileLink))) {
									$sFilesFields .= strip_tags(trim($fileLink)) . "\n";
								}
							}
						}
					}

					$search           = $replace = array();
					$bFindFilesMacros = (strpos($v['MESSAGE'], '#FILES#') !== false) ? true : false;


					foreach($arFields as $k2 => $v2) {
						if($k2 == 'FILES')
							$v2 = (!$arParams['SEND_ATTACHMENT'] && !$arParams['DELETE_FILES_AFTER_UPLOAD']) ? $sFilesFields : "\n";

						$search[]  = '#' . $k2 . '#';
						$replace[] = (is_array($v2) ? implode("<br>",$v2) : $v2);
					}

					$strFields = str_replace($search, $replace, $v['MESSAGE']);

					if(strlen($arFields['SUBJECT']) > 0)
						$v['SUBJECT'] = $arFields['SUBJECT'];

					$subject = str_replace($search, $replace, $v['SUBJECT']);

					if(strpos($strFields, '#SITE_NAME#') !== false)
						$strFields = str_replace('#SITE_NAME#', $sSiteName, $strFields);

					if(strpos($strFields, '#SERVER_NAME#') !== false)
						$strFields = str_replace('#SERVER_NAME#', $sServerName, $strFields);


					///// MAIL HEADERS EXT /////
					if($v['CC']){
						$cc = str_replace($search, $replace, $v['CC']);
						$headers .= "\nCc: {$cc}";
					}
					if($v['REPLY_TO']){
						$reply_to = str_replace($search, $replace, $v['REPLY_TO']);
						$headers .= "\nReply-To: {$reply_to}";
					}
					if($v['IN_REPLY_TO']){
						$reply_to = str_replace($search, $replace, $v['IN_REPLY_TO']);
						$headers .= "\nIn-Reply-To: {$reply_to}";
					}
					if($v['PRIORITY']){
						$headers .= "\nX-Priority: {$v['PRIORITY']}";
					}
					$headers .= "\nX-EVENT_NAME: API_FEEDBACK_". intval($v['ID']);
					//////////////////////////////



					//v2.9.0 - SERVER_VARS, REQUEST_VARS
					if(!$user_mess) {
						if($v['BODY_TYPE'] == 'html') {
							if($arParams['SERVER_VARS']) {

								$strFields .= '<br><br><div><b>' . GetMessage('SERVER_VARS_TITLE') . '</b></div>';
								foreach($arParams['SERVER_VARS'] as $var) {
									$strFields .= '<br><b>' . $var . '</b>: ' . (is_array($_SERVER[ $var ]) ? implode('<br>', $_SERVER[ $var ]) : trim($_SERVER[ $var ]));
								}
							}
							if($arParams['REQUEST_VARS']) {

								$strFields .= '<br><br><div><b>' . GetMessage('REQUEST_VARS_TITLE') . '</b></div>';
								foreach($arParams['REQUEST_VARS'] as $var) {
									$strFields .= '<br><b>' . $var . '</b>: ' . trim($_REQUEST[ $var ]);
								}
							}
						}
						else {
							if($arParams['SERVER_VARS']) {
								$strFields .= "\n\n" . GetMessage('SERVER_VARS_TITLE');
								foreach($arParams['SERVER_VARS'] as $var) {
									$strFields .= "\n" . $var . ': ' . (is_array($_SERVER[ $var ]) ? implode("\n", $_SERVER[ $var ]) : trim($_SERVER[ $var ]));
								}
							}

							if($arParams['REQUEST_VARS']) {
								$strFields .= "\n\n" . GetMessage('REQUEST_VARS_TITLE');
								foreach($arParams['REQUEST_VARS'] as $var) {
									$strFields .= "\n" . $var . ': ' . trim($_REQUEST[ $var ]);
								}
							}
						}
					}


					//Prepare html-template
					if($v['BODY_TYPE'] == 'html') {
						$message_header = '<html>
                        <head>
                        <meta http-equiv="content-type" content="text/html; charset=' . SITE_CHARSET . '">
                        </head>
                        <body text="#000000" bgcolor="#FFFFFF">
                        ';
						$message_footer = '
                        </body></html>';

						$strFields = $message_header . $strFields . $message_footer;
					}

					//Replace html-template
					if(strlen($strFieldsNames)) {
						if(strlen($arFields['FILES']) && !$bFindFilesMacros && !$arParams['SEND_ATTACHMENT'] && !$arParams['DELETE_FILES_AFTER_UPLOAD'])
							$strFields = str_replace('#WORK_AREA#', '#WORK_AREA#' . $sFilesFields, $strFields);

						$strFields = str_replace('#WORK_AREA#', $strFieldsNames, $strFields);
					}


					//Work with iblock
					if($arParams['IBLOCK_ID']) {
						$TICKET_ID = self::GetOrderNumber('TICKET_ID', $arParams['IBLOCK_ID']);
						if($user_mess && $TICKET_ID > 1)
							$TICKET_ID -= 1;

						$arFields['TICKET_ID'] = $TICKET_ID;
						$strFields             = str_replace('#TICKET_ID#', $TICKET_ID, $strFields);

						$_SESSION['API_FEEDBACK']['TICKET_ID'] = $TICKET_ID;

						$arProps = array(
							 'TICKET_ID' => $TICKET_ID,
							 //'FILES' => $arFields['AR_FILES'],
						);

						//Write form fields to iblock props
						if($arFields && !$user_mess) {
							foreach($arFields as $key => $val) {
								if($key != 'FILES') {
									if($key == 'AR_FILES')
										$key = 'FILES';

									$arProps[ $key ] = $val;
								}
							}
						}

						$arLoadFields = array(
							 'IBLOCK_ID'         => $arParams['IBLOCK_ID'],
							 'DATE_ACTIVE_FROM'  => date($DB->DateFormatToPHP(CSite::GetDateFormat())),
							 'IBLOCK_SECTION_ID' => false,
							 'ACTIVE'            => $arParams['IBLOCK_ELEMENT_ACTIVE'] ? 'Y' : 'N',
							 'NAME'              => 'Ticket#' . $TICKET_ID,
							 'PROPERTY_VALUES'   => $arProps,
							 'DETAIL_TEXT'       => $strFields,
							 'DETAIL_TEXT_TYPE'  => ($v['BODY_TYPE'] == 'html' ? 'html' : 'text'),
							 'CODE'              => 'Ticket#' . $TICKET_ID,
						);

						if(!$user_mess && $TICKET_ID) {
							$el->Add($arLoadFields, false, false, false);
							if($el->LAST_ERROR)
								$this->LAST_ERROR[] = $el->LAST_ERROR;
						}
					}

					//Include attachments in message
					if(strlen($arFields['FILES']) && ($arParams['SEND_ATTACHMENT'] || $arParams['DELETE_FILES_AFTER_UPLOAD']))
						$strFields .= "\n" . $sFilesFields;
				}


				// multipart boundary
				$message = "--{$mime_boundary}\n";
				$message .= "Content-Type: text/" . $v['BODY_TYPE'] . "; charset=" . SITE_CHARSET . "\n";
				$message .= "Content-Transfer-Encoding: 8bit\n\n";
				$message .= htmlspecialcharsback($strFields) . "\n\n";//iso-8859-1 ::  text/plain
				$message .= "--{$mime_boundary}--";

				if(strlen($sSiteName))
					$subject = str_replace('#SITE_NAME#', $sSiteName, $subject);
				if(strlen($sServerName))
					$subject = str_replace('#SERVER_NAME#', $sServerName, $subject);
				if(strlen($TICKET_ID))
					$subject = str_replace('#TICKET_ID#', $TICKET_ID, $subject);
				$subject = "=?" . SITE_CHARSET . "?B?" . base64_encode($subject) . "?=";

				if($arParams['DISABLE_SEND_MAIL']) {
					if(!$user_mess)
						foreach(GetModuleEvents('api.feedback', "OnAfterEmailSend", true) as $arEvent)
							ExecuteModuleEventEx($arEvent, array(&$event_name, &$site_id, &$arFields, &$message_id));
				}
				elseif(bxmail($email_to, $subject, $message, $headers)) {
					if(!$user_mess)
						foreach(GetModuleEvents('api.feedback', "OnAfterEmailSend", true) as $arEvent)
							ExecuteModuleEventEx($arEvent, array(&$event_name, &$site_id, &$arFields, &$message_id));
				}
				else
					$this->LAST_ERROR[] = GetMessage('NO_WORK_MAIL_FUNCTION');
			}
		}
		else
			$this->LAST_ERROR[] = GetMessage('NO_FOUND_EVENT_MESSAGE');

		if(!empty($this->LAST_ERROR))
			return false;

		return true;
	}

	/**
	 * FakeTranslit()
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	function FakeTranslit($str)
	{
		$str = trim($str);

		$trans_from = explode(",", GetMessage("TRANSLIT_FROM"));
		$trans_to   = explode(",", GetMessage("TRANSLIT_TO"));

		$str = str_replace($trans_from, $trans_to, $str);

		$str = preg_replace('/\s+/u', '-', $str);

		return $str;
	}

	/**
	 * GetOrderNumber()
	 *
	 * @param string $sTicketNumPropCode
	 * @param int    $IBLOCK_ID
	 *
	 * @return int
	 */
	function GetOrderNumber($sTicketNumPropCode, $IBLOCK_ID)
	{
		//For first ticket
		$TICKET_ID = 1;

		//found last order number from property ORDER_NUMBER, not ID
		if($IBLOCK_ID && $sTicketNumPropCode && CModule::IncludeModule('iblock')) {
			//First find a property in infoblock
			$dbRes = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'CODE' => $sTicketNumPropCode));
			if(!$arProp = $dbRes->Fetch()) {
				$this->LAST_ERROR[] = GetMessage('NOT_FOUND_IBLOCK_PROP_TICKET_ID');
				return false;
			}

			$el     = new CIBlockElement();
			$db_res = $el->GetList(array('ID' => 'DESC'), array('IBLOCK_ID' => $IBLOCK_ID), false, array('nTopCount' => 1), array('ID', 'PROPERTY_' . $sTicketNumPropCode));
			if($ar_res = $db_res->Fetch()) {
				$tmpTicketNum = $ar_res[ 'PROPERTY_' . $sTicketNumPropCode . '_VALUE' ];
				$TICKET_ID    = intval($tmpTicketNum) + 1;
			}
		}

		return $TICKET_ID;
	}

	function GetUUID($length = 10, $prefix = '')
	{
		if($length > 32)
			$length = 32;

		mt_srand((double)microtime() * 10000);
		$chars = ToUpper(md5(uniqid(rand(), true)));

		$uuid = substr($chars, 0, $length);

		if(strlen($prefix))
			$uuid = $prefix . $uuid;

		return $uuid;
	}

	function sortFields(&$arCustomFields)
	{
		//Sort fields
		$arSortFields = array();
		foreach($arCustomFields as $key => $val) {
			$sort     = 0;
			$arFields = explode('@', $val);
			foreach($arFields as $arField) {
				if(substr($arField, 0, 4) == "sort") {
					$arData = explode('=', $arField);
					if($arData[1])
						$sort = intval($arData[1]);
				}
			}

			$arSortFields[ $key ] = $sort;
		}

		asort($arSortFields, SORT_NUMERIC);

		$i         = 0;
		$bFoundKey = 0;
		foreach($arSortFields as $val) {
			if($val > 0) {
				$bFoundKey = $i;
				break;
			}
			$i++;
		}

		if($bFoundKey) {
			$arSlice1 = array_slice($arSortFields, $bFoundKey, null, true);
			$arSlice2 = array_slice($arSortFields, 0, $bFoundKey, true);
			ksort($arSlice2);

			$arMerge            = $arSlice1 + $arSlice2;
			$arFakeCustomFields = array();
			foreach($arMerge as $key => $val) {
				$arFakeCustomFields[] = $arCustomFields[ $key ];
			}

			if($arFakeCustomFields)
				$arCustomFields = $arFakeCustomFields;
		}

		return $arCustomFields;
	}

	/**
	 * Get file size in bytes form K|M|G
	 *
	 * @param $val
	 *
	 * @return int
	 */
	function getFileSizeInBytes($val)
	{
		$val = trim($val);

		if(empty($val))
			return 0;

		preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

		$last = '';
		if(isset($matches[2])) {
			$last = $matches[2];
		}

		if(isset($matches[1])) {
			$val = (int)$matches[1];
		}

		switch(ToUpper($last)) {
			case 'T':
			case 'TB':
				$val *= pow(1024, 4);
				break;

			case 'G':
			case 'GB':
				$val *= pow(1024, 3);
				break;

			case 'M':
			case 'MB':
				$val *= pow(1024, 2);
				break;

			case 'K':
			case 'KB':
				$val *= 1024;
				break;

			default:
				$val *= 1;
		}

		return (int)$val;
	}
}

?>