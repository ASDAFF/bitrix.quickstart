<?

use \Bitrix\Main\Mail;
use \Bitrix\Main\Event;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CApiFormDesigner
{
	protected function executeEvent($type,&$parameters){
		$event = new Event('api.formdesigner',$type,$parameters);
		$event->send();

		/*if ($event->getResults()) {
			foreach ($event->getResults() as $evenResult) {
				if ($evenResult->getType() == \Bitrix\Main\EventResult::SUCCESS) {
					$parameters = $evenResult->getParameters();
				}
			}
		}*/
	}

	public function sendMessage($emId, $siteID, $arFields, $arFieldsNames, $arParams, $bUserMess = false, $duplicate = 'N', $files = array())
	{
		$id               = false;
		$eventType        = 'API_FORMDESIGNER';
		$strFieldsNames   = '';
		$error            = '';

		//Fields for email
		$arEventFields = array(
			 'EMAIL_TO'  => ($bUserMess ? $arParams['USER_EMAIL'] : $arFields['EMAIL_TO']),
		);

		if(!$bUserMess && $arParams['POST_REPLACE_FROM'] && $arParams['USER_EMAIL'])
			$arEventFields['EMAIL_FROM'] = $arParams['USER_EMAIL'];


		//---------- Execute events ----------//
		//$oldEventParameters = array(&$emId, &$siteID, &$arFields, &$arFieldsNames, &$arParams);

		$newEventParameters = array(
			 'EVENT_NAME'  => &$eventType,
			 'SITE_ID'     => &$siteID,
			 'FIELDS'      => &$arFields,
			 'MESSAGE_ID'  => &$emId,
			 'FILES'       => &$files,
			 'FIELDS_NAME' => &$arFieldsNames,
			 'PARAMS'      => $arParams,
			 'ERROR'       => '',
		);

		//Execute events before send message
		/*foreach(GetModuleEvents('api.formdesigner', "OnBeforeMailSend", true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, $oldEventParameters);
		}
		if($bUserMess) {
			foreach(GetModuleEvents('api.formdesigner', "OnBeforeUserMailSend", true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, $oldEventParameters);
			}
		}
		else {
			foreach(GetModuleEvents('api.formdesigner', "OnBeforeAdminMailSend", true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, $oldEventParameters);
			}
		}*/


		//NEW EVENTS BEFORE
		$this->executeEvent('onBeforeMailSend',$newEventParameters);
		if($bUserMess)
			$this->executeEvent('onBeforeUserMailSend',$newEventParameters);
		else
			$this->executeEvent('onBeforeAdminMailSend',$newEventParameters);


		if($arFields && $emId) {
			if($arFieldsNames) {
				foreach($arFieldsNames as $code => $name) {
					$htmlValue = is_array($arFields[ $code ]) ? join("<br>", $arFields[ $code ]) : $arFields[ $code ];
					//$htmlValue = self::getTextForEmail($htmlValue);

					if(strlen($htmlValue) > 0) {
						$strFieldsNames .= "\n<div style=\"" . $arParams['POST_MESS_STYLE_WRAP'] . "\">";
						$strFieldsNames .= "\n\t<div style=\"" . $arParams['POST_MESS_STYLE_NAME'] . "\">" . $name . "</div>";
						$strFieldsNames .= "\n\t<div style=\"" . $arParams['POST_MESS_STYLE_VALUE'] . "\">" . $htmlValue . "</div>";
						$strFieldsNames .= "\n</div>";
					}
				}
			}

			foreach($arFields as &$value) {
				$value = self::replaceMacros($value, $arFields);
			}
			unset($value);


			if(!$bUserMess) {

				$strTpl    = Loc::getMessage('API_FD_INCLUDE_VARS_TPL');
				$strRowTpl = Loc::getMessage('API_FD_INCLUDE_VARS_ROW_TPL');
				if($strTpl && $strRowTpl) {

					//PAGE_VARS
					if($arParams['PAGE_VARS']) {
						$strTitle = Loc::getMessage('PAGE_VARS_TITLE');

						$strRows = '';
						foreach($arParams['PAGE_VARS'] as $key => $val) {
							$name = Loc::getMessage('PAGE_VARS_' . $key);
							$val  = self::getTextForEmail($val);

							$strRows .= str_replace(array('#NAME#', '#VALUE#'), array($name, $val), $strRowTpl);
						}

						$strFieldsNames .= str_replace(
							 array('#TITLE#', '#ROWS#'),
							 array($strTitle, $strRows),
							 $strTpl
						);

						unset($strTitle, $strRows, $key, $val, $name);
					}

					//UTM_VARS
					if($arParams['UTM_VARS']) {
						$strTitle = Loc::getMessage('UTM_VARS_TITLE');

						$strRows = '';
						foreach($arParams['UTM_VARS'] as $key => $val) {
							$strRows .= str_replace(array('#NAME#', '#VALUE#'), array($key, $val), $strRowTpl);
						}

						$strFieldsNames .= str_replace(
							 array('#TITLE#', '#ROWS#'),
							 array($strTitle, $strRows),
							 $strTpl
						);

						unset($strTitle, $strRows, $key, $val, $srvVal);
					}


					//SERVER_VARS
					if($arParams['SERVER_VARS']) {
						$strTitle = Loc::getMessage('SERVER_VARS_TITLE');

						$strRows = '';
						foreach($arParams['SERVER_VARS'] as $key) {
							$srvVal = $_SERVER[ $key ];
							$val    = (is_array($srvVal) ? implode('<br>', $srvVal) : self::getTextForEmail($srvVal));

							$strRows .= str_replace(array('#NAME#', '#VALUE#'), array($key, $val), $strRowTpl);
						}

						$strFieldsNames .= str_replace(
							 array('#TITLE#', '#ROWS#'),
							 array($strTitle, $strRows),
							 $strTpl
						);

						unset($strTitle, $strRows, $key, $val, $srvVal);
					}
				}
			}


			//Prepare fields for email
			$arEventFields['WORK_AREA'] = $strFieldsNames;
			$arEventFields = array_merge($arFields, $arEventFields);


			//BASE BITRIX EVENT FOR EMAIL
			foreach(GetModuleEvents('main', 'OnBeforeEventAdd', true) as $arEvent)
				if(ExecuteModuleEventEx($arEvent, array(&$eventType, &$siteID, &$arEventFields, &$emId, &$files)) === false)
					return false;

			//EXECUTE MAIL SEND
			$arLocalFields = array(
				 'EVENT_NAME' => $eventType,
				 'C_FIELDS'   => $arEventFields,
				 'LID'        => is_array($siteID) ? join(',', $siteID) : $siteID,
				 'DUPLICATE'  => $duplicate != 'N' ? 'Y' : 'N',
				 'FILE'       => $files,
			);
			if(intval($emId) > 0)
				$arLocalFields['MESSAGE_ID'] = intval($emId);


			$result = Mail\Event::send($arLocalFields);
			//Mail\EventManager::executeEvents();

			if($result->isSuccess()) {
				$id = $result->getId();

				//NEW EVENTS AFTER
				$this->executeEvent('onAfterMailSend',$newEventParameters);
				if($bUserMess)
					$this->executeEvent('onAfterUserMailSend',$newEventParameters);
				else
					$this->executeEvent('onAfterAdminMailSend',$newEventParameters);
			}
			else{
				$error = Loc::getMessage('API_FDI_MAIL_SEND_ERROR');
				$newEventParameters['ERROR'] = $error;

				$this->executeEvent('onErrorMailSend',$newEventParameters);
			}
		}

		/*
		foreach(GetModuleEvents('api.formdesigner', "OnAfterMailSend", true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, $oldEventParameters);
		}

		if($bUserMess) {
			foreach(GetModuleEvents('api.formdesigner', "OnAfterUserMailSend", true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, $oldEventParameters);
			}
		}
		else {
			foreach(GetModuleEvents('api.formdesigner', "OnAfterAdminMailSend", true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, $oldEventParameters);
			}
		}*/

		if(!empty($error))
			return false;

		return $id;
	}

	/**
	 * Get last insert ticket ID
	 *
	 * @param string $sPropCode
	 * @param int    $IBLOCK_ID
	 *
	 * @return int
	 */
	public static function getTicketID($sPropCode, $IBLOCK_ID)
	{
		//For first ticket
		$TICKET_ID = 1;
		$el        = new CIBlockElement();

		//found last ticket number from property ORDER_NUMBER, not ID
		if($IBLOCK_ID && $sPropCode) {
			$db_res = $el->GetList(array('ID' => 'DESC'), array('IBLOCK_ID' => $IBLOCK_ID), false, array('nTopCount' => 1), array('ID', 'PROPERTY_' . $sPropCode));
			if($ar_res = $db_res->Fetch()) {
				$tmpTicketID = intval($ar_res[ 'PROPERTY_' . $sPropCode . '_VALUE' ]);
				if($tmpTicketID)
					$TICKET_ID = $tmpTicketID + 1;
			}
		}

		return $TICKET_ID;
	}

	/**
	 * Translit cyrillic file name
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function translit($str)
	{
		$str = trim($str);

		$trans_from = explode(",", Loc::getMessage("TRANSLIT_FROM"));
		$trans_to   = explode(",", Loc::getMessage("TRANSLIT_TO"));

		$str = str_replace($trans_from, $trans_to, $str);

		$str = preg_replace('/\s+/u', '-', $str);

		return $str;
	}

	public static function getUniqueFileName($fileName, $propId, $userId)
	{
		return md5(trim($fileName) . bitrix_sessid() . uniqid('api_fd_', true) . intval($propId) . intval($userId)) . '.' . GetFileExtension($fileName);
	}

	/**
	 * Replace all macros in string
	 *
	 * @param $string
	 * @param $arFields
	 * @param $arFields2
	 *
	 * @return bool|mixed
	 */
	public static function replaceMacros($string, $arFields, $arFields2 = array())
	{
		if($arFields) {
			foreach($arFields as $key => $val) {
				$newVal = (is_array($val) ? implode('-', $val) : $val);

				$string = str_replace('#' . $key . '#', $newVal, $string);
			}
		}

		if($arFields2) {
			foreach($arFields2 as $key => $val) {
				$newVal = (is_array($val) ? implode('-', $val) : $val);

				$string = str_replace('#' . $key . '#', $newVal, $string);
			}
		}

		return $string;
	}

	/**
	 * Get file size in bytes form K|M|G
	 *
	 * @param $val
	 *
	 * @return int
	 */
	public static function getFileSizeInBytes($val)
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

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public static function getFormatText($text = '')
	{
		return (preg_match('/<[\/\!]*?[^<>]*?>/im' . BX_UTF_PCRE_MODIFIER, $text) ? $text : nl2br($text));
	}


	//Обрабатывает php-код и опасные теги, иначе письма не отправляются и в футере сайта появляется ошибка
	//Отлично обрабатывает HTML в текстовых полях, не визуальный режим.
	public static function getTextForEmail($text = '')
	{
		return TxtToHTML($text);
	}

	//TODO: доработать
	//Обработчик HTML-полей визуального редактора
	public static function getHtmlForEmail($str = '')
	{

		// Apply XSS filtering, but blacklist the <script>, <style>, <link>, <embed>
		// and <object> tags.
		// The <script> and <style> tags are blacklisted because their contents
		// can be malicious (and therefor they are inherently unsafe), whereas for
		// all other tags, only their attributes can make them malicious. Since
		// \Drupal\Component\Utility\Xss::filter() protects against malicious
		// attributes, we take no blacklisting action.
		// The exceptions to the above rule are <link>, <embed> and <object>:
		// - <link> because the href attribute allows the attacker to import CSS
		//   using the HTTP(S) protocols which Xss::filter() considers safe by
		//   default. The imported remote CSS is applied to the main document, thus
		//   allowing for the same XSS attacks as a regular <style> tag.
		// - <embed> and <object> because these tags allow non-HTML applications or
		//   content to be embedded using the src or data attributes, respectively.
		//   This is safe in the case of HTML documents, but not in the case of
		//   Flash objects for example, that may access/modify the main document
		//   directly.
		// <iframe> is considered safe because it only allows HTML content to be
		// embedded, hence ensuring the same origin policy always applies.
		//$dangerous_tags = ['script', 'style', 'link', 'embed', 'object'];

		//iframe
		$str = preg_replace(
			 '#<iframe[^>]*src="([^"]*)"[^>]*>[^<]*<\/iframe>#is',
			 '<span style="border:1px solid #E5E5E5;display:block;"><span style="background-color:#fafafa;font-size:10px;padding:5px;display:block;">iframe</span><span style="padding:5px;display:block;"><a href="\\1" target="_blank">\\1</a></span></span>',
			 $str
		);

		//code
		$str = preg_replace(
			 "#<code>(.*?)</code>#is",
			 '<code style="padding:2px 4px;background-color:#f5f2f0;font-family:Consolas,Menlo,Monaco,monospace,sans-serif;font-size:13px;line-height:16px;color:#bd4147;border-radius:2px;border:none;">\\1</code>',
			 $str
		);

		//pre
		$str = preg_replace(
			 "#<pre>(.*?)</pre>#is",
			 '<pre style="padding:10px;background-color:#fafafa;font-family:Consolas,Menlo,Monaco,monospace,sans-serif;font-size:13px;line-height:16px;color:#444;overflow:auto;border:1px solid #ddd;border-radius:3px;">\\1</pre>',
			 $str
		);

		//blockquote
		$str = preg_replace(
			 "#<blockquote>(.*?)</blockquote>#is",
			 '<blockquote style="margin-left:0;padding-left:20px;border-left: 1px solid #E5E5E5;font-family: \'Trebuchet MS\',Tahoma,sans-serif;font-size:18px;font-style:italic;color:#666666;">\\1</blockquote>',
			 $str
		);

		static $search1 = array(
			 "'<script[^>]*?>.*?</script>'is",
			 "'<style[^>]*?>.*?</style>'is",
			 "'<select[^>]*?>.*?</select>'is",
			 "'<link[^>]*?>.*?</link>'is",
			 "'<embed[^>]*?>.*?</embed>'is",
			 "'<object[^>]*?>.*?</object>'is",
			 "'<iframe[^>]*?>.*?</iframe>'is", //Очистит все остальные непонятные фрэймы
			 "'<!--.*?-->'is",
		);
		$str = preg_replace($search1, '', $str);

		static $search2 = array(
			 "'&(quot|#34);'i",
			 "'&(iexcl|#161);'i",
			 "'&(cent|#162);'i",
			 "'&(pound|#163);'i",
			 "'&(copy|#169);'i",
		);
		static $replace2 = array(
			 "\"",
			 "\xa1",
			 "\xa2",
			 "\xa3",
			 "\xa9",
		);
		$str = preg_replace($search2, $replace2, $str);

		$str = str_replace(
			 array('<?',    '?>',    '->',    '=>'),
			 array('&lt;?', '?&gt;', '-&gt;', '=&gt;'),
			 $str
		);

		static $search3=array("'","%",")","(","+");
		static $replace3=array("&#39;","&#37;","&#41;","&#40;","&#43;");
		$str = str_replace($search3, $replace3, $str);

		//$str = preg_replace("#<br[^>]*>#is", "<br />\n", $str);

		return $str;
	}




	//ТУДУ на кириллице!
	/** @deprecated  bullshit!!! */
	public static function сlearHtmlForEmail($str)
	{
		$search = array(
			 "'<script[^>]*?>.*?</script>'si",  // Вырезает javaScript
			 "'<style[^>]*?>.*?</style>'si",    // Вырезает CSS-стили
			 "'<!--[^>]*?>{0,}.*?-->'si",       // Вырезает HTML-комментарии
			 "'<[\/\!]*?[^<>]*?>'si",           // Вырезает HTML-теги
			 "'([\r\n])[\s]+'",                 // Вырезает пробельные символы
			 "'&(quot|#34);'i",                 // Заменяет HTML-сущности
			 "'&(amp|#38);'i",
			 "'&(lt|#60);'i",
			 "'&(gt|#62);'i",
			 "'&(nbsp|#160);'i",
			 "'&(ndash);'i",
			 "'&(laquo);'i",
			 "'&(raquo);'i",
			 "'&(iexcl|#161);'i",
			 "'&(cent|#162);'i",
			 "'&(pound|#163);'i",
			 "'&(copy|#169);'i",
			 "'&#(\d+);'e",                     // интерпретировать как php-код
			 "'\s{1,}?'si"                      // заменяем мультипробелы на 1 пробел
		);

		$replace = array(
			 "",
			 "",
			 "",
			 "",
			 "\\1",
			 "\"",
			 "&",
			 "<",
			 ">",
			 " ",
			 " — ",
			 "«",
			 "»",
			 chr(161),
			 chr(162),
			 chr(163),
			 chr(169),
			 "chr(\\1)",
			 " ");

		$str = preg_replace($search, $replace, $str);
		return $str;
	}
}

?>