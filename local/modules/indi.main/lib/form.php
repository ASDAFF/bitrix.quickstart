<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main;

/**
 * Обслуживает веб-формы
 */
class Form
{
	/**
	 * Обработчик события после добавления результата формы
	 *
	 * @param integer $formId ID формы
	 * @param integer $resultId ID результата
	 * @return void
	 */
	public static function onAfterResultAdd($formId, $resultId)
	{
		$form = \CForm::GetByID($formId)->Fetch();
		
		switch ($form['SID']) {
			case 'ASK_QUESTION':
				self::onAfterResultAddAskQuestion($form, $resultId);
				break;
		}
	}
	/**
	 * Обработчик события добавления результата формы "Задать вопрос"
	 *
	 * @param array $form Данные формы
	 * @param integer $resultId ID результата
	 * @return void
	 */
	protected static function onAfterResultAddAskQuestion($form, $resultId)
	{
		//Переписываем email из radio-поля TYPE в hidden-поле MAILTO
		$results = \CFormResult::GetDataByID(
			$resultId,
			array('TYPE')
		);
		if ($results['TYPE'] 
			&& ($typeResult = array_shift($results['TYPE']))
		) {
			\CFormResult::SetField($resultId, 'MAILTO', $typeResult['VALUE']);
		}
	}
	
	/**
	* Отправка письма со вложением
	*
	* @param string $event код события
	* @param string $lid код сайта
	* @param array $arFields поля для отправки
	* @param array $filePath путь/массив путей до файла
	*
	**/
	public function SendAttache($event, $lid, $arFields, $filePath)
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
			if(!$charset)
				$charset = 'utf8';
			// additional params
			if (!isset($arFields["DEFAULT_EMAIL_FROM"]))
				$arFields["DEFAULT_EMAIL_FROM"] = \COption::GetOptionString("main", "email_from", "admin@".$GLOBALS["SERVER_NAME"]);
			if (!isset($arFields["SITE_NAME"]))
				$arFields["SITE_NAME"] = \COption::GetOptionString("main", "site_name", $GLOBALS["SERVER_NAME"]);
			if (!isset($arFields["SERVER_NAME"]))
				$arFields["SERVER_NAME"] = \COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);

			// replace
			$from = \CAllEvent::ReplaceTemplate($arMessTpl["EMAIL_FROM"], $arFields);
			$to = \CAllEvent::ReplaceTemplate($arMessTpl["EMAIL_TO"], $arFields);
			$message = \CAllEvent::ReplaceTemplate($arMessTpl["MESSAGE"], $arFields);
			$subj = \CAllEvent::ReplaceTemplate($arMessTpl["SUBJECT"], $arFields);
			$bcc = \CAllEvent::ReplaceTemplate($arMessTpl["BCC"], $arFields);


			$from = trim($from, "\r\n");
			$to = trim($to, "\r\n");
			$subj = trim($subj, "\r\n");
			$bcc = trim($bcc, "\r\n");
			if(\COption::GetOptionString("main", "convert_mail_header", "Y")=="Y")
			{
				$from = \CAllEvent::EncodeMimeString($from, $charset);
				$to = \CAllEvent::EncodeMimeString($to, $charset);
				$subj = \CAllEvent::EncodeMimeString($subj, $charset);
			}

			$all_bcc = \COption::GetOptionString("main", "all_bcc", "");
			if ($all_bcc != "")
			{
				$bcc .= (strlen($bcc)>0 ? "," : "") . $all_bcc;
				$duplicate = "Y";
			}
			else
			{
				$duplicate = "N";
			}

			

			if (\COption::GetOptionString("main", "CONVERT_UNIX_NEWLINE_2_WINDOWS", "N") == "Y")
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
			$eol = \CAllEvent::GetMailEOL();
			$head = $body = "";

			// header
			$head .= "Mime-Version: 1.0".$eol;
			$head .= "From: $from".$eol;
			if(\COption::GetOptionString("main", "fill_to_mail", "N")=="Y")
				$header = "To: $to".$eol;
			$head .= "Reply-To: $from".$eol;
			$head .= "X-Priority: 3 (Normal)".$eol;
			$head .= "X-MID: $messID.".$arMessTpl["ID"]."(".date($DB->DateFormatToPHP(\CLang::GetDateFormat("FULL"))).")".$eol;
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
				bxmail($to, $subj, $body, $head, \COption::GetOptionString("main", "mail_additional_parameters", ""));
		}
	}
	
	
	/**
	 * Обработчик события отправки результата формы 
	 *
	 * @param array $arFields Данные формы
	 * @param integer $event событие
	 * @param integer $lid site_id
	 * @return void
	 */
	public static function OnBeforeEventAddHandler($event, $lid, $arFields)
	{
		
		if(array_key_exists('RS_RESULT_ID', $arFields)) {
			$arFiles = array();
			$arAnswer = \CFormResult::GetDataByID($arFields['RS_RESULT_ID'],array(), $arResult, $arAnswer);
			foreach($arAnswer as $arVariant) {
				foreach($arVariant as $arItem) {
					if($arItem['USER_FILE_ID'] > 0) {
						$arFiles[] =  \CFile::GetPath($arItem['USER_FILE_ID']);
					}
				}
			}
			if(count($arFiles) > 0) {
				self::SendAttache($event, $lid, $arFields, $arFiles);
				$event = 'null'; $lid = 'null';
				return false;
			}
	
		}
	}
	
}