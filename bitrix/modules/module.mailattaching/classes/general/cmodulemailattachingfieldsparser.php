<?
/**
 * 
 * Парсеры полей для почтовых сообщений различных модулей
 *
 */

class CModuleMailAttachingFieldsParser {

	//
	// Возвращает функции обратного вызова парсинга полей поддерживаемых модулей
	//
	public static function GetParser($arFields, $arMailResult, $arAttaches) {
		$mCallback = false;

		// form
		if(!$mCallback) {
			// относится ли отправлемое сообщение к результатам модуля веб-форм
			$mCallback = self::ModuleFormCheck($arFields, $arMailResult, $arAttaches);
		}

		// support
		if(!$mCallback) {
			// относится ли отправлемое сообщение к модулю техподдержки
			$mCallback = self::ModuleSupportCheck($arFields, $arMailResult, $arAttaches);
		}

		// ***
		/*
		if(!$mCallback) {
			// относится ли отправлемое сообщение к модулю ***
			$mCallback = self::Module***Check($arFields, $arMailResult, $arAttaches);
		}
		*/

		return $mCallback;
	}

	//
	// Модуль form. --- Проверка и обработчики для почтовых сообщений 
	//
	protected static function ModuleFormCheck($arFields, $arMailResult, $arAttaches) {
		$mCallback = false;
		$bResult = isset($arFields['RS_FORM_ID']) && !empty($arFields['RS_FORM_ID']) && !empty($arFields['RS_RESULT_ID']) && intval($arFields['RS_RESULT_ID']) > 0 && CModule::IncludeModule('form');
		if($bResult) {
			$mCallback = array(__CLASS__, 'CallbackModuleForm');
		}
		return $mCallback;
	}

	public static function CallbackModuleForm($sFile, $arFields, $arMailResult, $arAttaches) {
		$arAttachedFiles = array();

		$bGetByMacros = false;
		$sFileMacros = trim($sFile, '#');
		if($sFile != $sFileMacros) {
			if(array_key_exists($sFileMacros, $arFields)) {
				$bGetByMacros = true;
			}
		}

		if($bGetByMacros) {
			// если файл подставляется через макросы, то определим значение
			$sTxtVal = $arFields[$sFileMacros];
			$iFormId = intval($arFields['RS_RESULT_ID']);
			if($iFormId > 0 && strlen($sTxtVal)) {
				$arWebFormFieldData = CFormResult::GetDataByID($iFormId, array($sFileMacros), $arTmp1, $arTmp2);
				if(!empty($arWebFormFieldData[$sFileMacros])) {
					// найдено поле в результате веб-формы, переберем все варианты ответов, они могут быть разного типа
					foreach($arWebFormFieldData[$sFileMacros] as $arCurMeta) {
						if(isset($arCurMeta['USER_FILE_ID']) && intval($arCurMeta['USER_FILE_ID']) > 0) {
							// ответ хранит ID файла
							$arAttachedFiles[] = array(
								'FILE' => $arCurMeta['USER_FILE_ID'], 
								'FILE_NAME' => $arCurMeta['USER_FILE_NAME']
							);
						}
					}
				}
			}
		} else {
			$arAttachedFiles[] = array(
				'FILE' => $sFile,
				'FILE_NAME' => ''
			);
		}

		return $arAttachedFiles;
	}

	//
	// Модуль support. --- Проверка и обработчики для почтовых сообщений 
	//
	protected static function ModuleSupportCheck($arFields, $arMailResult, $arAttaches) {
		$mCallback = false;

		$iEventMessageId = isset($arMailResult['ID']) ? $arMailResult['ID'] : 0;
		if($iEventMessageId > 0 && IsModuleInstalled('support')) {
			$arAvMessageTypes = array(
				'TICKET_NEW_FOR_AUTHOR',
				'TICKET_NEW_FOR_TECHSUPPORT',
				'TICKET_CHANGE_BY_SUPPORT_FOR_AUTHOR',
				'TICKET_CHANGE_BY_AUTHOR_FOR_AUTHOR',
				'TICKET_CHANGE_FOR_TECHSUPPORT',
			);

			$sEventMessageType = CModuleMailAttaching::GetEventMessageType($arMailResult['ID']);
			$bResult = in_array($sEventMessageType, $arAvMessageTypes);
			if($bResult) {
				$mCallback = array(__CLASS__, 'CallbackModuleSupport');
			}
		}

		return $mCallback;
	}

	public static function CallbackModuleSupport($sFile, $arFields, $arMailResult, $arAttaches) {
		$arAttachedFiles = array();

		$bGetByMacros = false;
		$sFileMacros = trim($sFile, '#');
		if($sFile != $sFileMacros) {
			if(array_key_exists($sFileMacros, $arFields)) {
				$bGetByMacros = true;
			}
		}

		if($bGetByMacros) {
			// если файл подставляется через макросы, то определим значение
			$sTxtVal = $arFields[$sFileMacros];
			$iTicketId = isset($arFields['ID']) ? intval($arFields['ID']) : 0;
			if($iTicketId > 0 && strlen($sTxtVal)) {
				$arHashes = array();
				// получим хэш-коды загруженных файлов
				if(preg_match_all('#(?:hash=([0-9a-z]{32}))(?:.*?)#is'.BX_UTF_PCRE_MODIFIER, $sTxtVal, $arMatches)) {
					if(!empty($arMatches[1])) {
						$arHashes = $arMatches[1];
					}
				}

				if(!empty($arHashes)) {
					$dbItems = CTicket::GetFileList(
						$sBy = 's_id',
						$sOrder = 'asc',
						array(
							'TICKET_ID' => $iTicketId,
							'HASH' => implode('|', $arHashes)
						)
					);
					while($arItem = $dbItems->Fetch()) {
						$sFilePath = CFile::GetFileSRC($arItem);
						if(strlen($sFilePath)) {
							$sFileName = isset($arItem['ORIGINAL_NAME']) && strlen($arItem['ORIGINAL_NAME']) ? $arItem['ORIGINAL_NAME'] : $arItem['FILE_NAME'];
							$iSufLength = strlen($arItem['EXTENSION_SUFFIX']);
							if($iSufLength) {
								$sFileName = substr($sFileName, 0, strlen($sFileName) - $iSufLength);
							}
							$arAttachedFiles[] = array(
								'FILE' => $sFilePath,
								'FILE_NAME' => $sFileName
							);
						}
					}
				}
			}
		} else {
			$arAttachedFiles[] = array(
				'FILE' => $sFile,
				'FILE_NAME' => ''
			);
		}

		return $arAttachedFiles;
	}

	//
	// заготовки для следующих модулей
	//
	/*
	protected static function Module***Check($arFields, $arMailResult, $arAttaches) {
		$mCallback = false;

		$bSuccess = false;

		//
		// ...
		//

		if($bSuccess) {
			$mCallback = array(__CLASS__, 'CallbackModuleSupport');
		}

		return $mCallback;
	}

	public static function CallbackModule***($sFile, $arFields, $arMailResult, $arAttaches) {
		$arAttachedFiles = array();

		$bGetByMacros = false;
		$sFileMacros = trim($sFile, '#');
		if($sFile != $sFileMacros) {
			if(array_key_exists($sFileMacros, $arFields)) {
				$bGetByMacros = true;
			}
		}

		if($bGetByMacros) {
			// если файл подставляется через макросы, то определим значение
			$sTxtVal = $arFields[$sFileMacros];
			$iSomeId = isset($arFields['ID']) ? intval($arFields['ID']) : 0;
			if($iSomeId > 0 && strlen($sTxtVal)) {
				//
				// ...
				//
				$arAttachedFiles[] = array(
					'FILE' => $sFilePath,
					'FILE_NAME' => $sFileName
				);
			}
		} else {
			$arAttachedFiles[] = array(
				'FILE' => $sFile,
				'FILE_NAME' => ''
			);
		}

		return $arAttachedFiles;
	}
	*/
}
