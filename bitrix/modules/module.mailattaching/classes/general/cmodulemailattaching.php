<?
/**
 * 
 * �������� ����� ������
 *
 */

class CModuleMailAttaching {
	// ���� �������� ��������
	private static $arAttachFilesPath = array();
	// ����� ������ ��� ��������
	private static $arAttachFilesNames = array();

	// ��������� ���� ��������
	private static $arCustomMime = array();
	// ��������� ���� ��������
	private static $arSysMime = array(
		'png' => 'image/png',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'docx' => 'application/msword',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'zip' => 'application/x-zip-compressed',
		'gz' => 'application/x-gzip',
		'tar' => 'application/x-tar',
		'txt' => 'text/plain',
		'rtf' => 'text/rtf',
		'html' => 'text/html'
	);
	private static $arMimeVirtCache = array();

	public static function GetPhysicalName($sFilePath) {
		if(!file_exists($sFilePath)) {
			static $obVirtualIo = null;
			if(class_exists('CBXVirtualIo')) {
				if(!$obVirtualIo) {
					$obVirtualIo = CBXVirtualIo::GetInstance();
				}
				$sFilePath = $obVirtualIo->GetPhysicalName($sFilePath);
			}
		}
		return $sFilePath;
	}

	//
	// �������� ������ � ��������� (���������� �� custom_mail)
	//
	public static function ExecCustomMail($sTo, $sSubject, $sMessage, $sAdditionalHeaders, $sAdditionalParameters) {
		//
		// OnStartCustomMail
		//
		$dbEvents = GetModuleEvents('module.mailattaching', 'OnStartCustomMail');
		$mEventResult = true;
		while(($arEvent = $dbEvents->Fetch()) && $mEventResult !== false) {
			$mEventResult = ExecuteModuleEventEx($arEvent, array(&$sTo, &$sSubject, &$sMessage, &$sAdditionalHeaders, &$sAdditionalParameters));
		}
		if($mEventResult === false) {
			return;
		}
		

		// ������������� �����
		$arAttaches = CModuleMailAttaching::GetAttachesEx();
		// ������� ������ ������������ ������
		CModuleMailAttaching::FlushAttaches();
		if(!empty($arAttaches)) {
			if(strpos($sAdditionalHeaders, 'Content-Type: multipart') === false) {
				$sLF = CEvent::GetMailEOL();
				$sCRLF = "\r\n";
	
				$sBoundaryName = md5(uniqid(time()));
				$sBoundary = '--'.$sBoundaryName;
				$sBoundaryClose = '--'.$sBoundaryName.'--';

				// ������� �� ��������� Content-Type � ��� ��� � ��� �������
				$iPos = strpos($sAdditionalHeaders, 'Content-Type:');
				$sMessageType = substr($sAdditionalHeaders, $iPos);
				$sAdditionalHeaders = substr($sAdditionalHeaders, 0, $iPos);
				$sCharset = '';
				if(preg_match("#charset=(.+)\n|\n\r#i", $sMessageType, $arMatches)) {
					if(!empty($arMatches[1])) {
						$sCharset = $arMatches[1];
					}
				}

				// ��������� MIME-���������
				$sAdditionalHeaders .= 'Mime-Version: 1.0';
				$sAdditionalHeaders .= $sCRLF;
				$sAdditionalHeaders .= 'Content-Type: multipart/mixed;';
				$sAdditionalHeaders .= $sCRLF;
				$sAdditionalHeaders .= ' boundary="'.$sBoundaryName.'"';

				// ������ Content-Type ��������� � ���� � ����������� boundary
				$sMessageType = ltrim($sMessageType, $sCRLF);
				$sMessageType = ltrim($sMessageType, $sLF);
				$sMessageType = str_replace($sCRLF, $sLF, $sMessageType);
				$sMessageOriginal = $sMessage;
				$sMessage = '';
				$sMessage .= $sBoundary; 
				$sMessage .= $sLF;
				$sMessage .= $sMessageType;
				$sMessage .= $sLF.$sLF; // !!! 2
				$sMessage .= $sMessageOriginal;
				$sMessage .= $sLF;

				// ������ ����������� �����
				foreach($arAttaches as $arFileItem) {
					$sFilePath = $arFileItem['FILE'];
					$sFullPath = $_SERVER['DOCUMENT_ROOT'].'/'.trim($sFilePath, '/');
					// ������� ���������� ��� �����
					$sFullPath = self::GetPhysicalName($sFullPath);
					if(file_exists($sFullPath) && is_file($sFullPath)) {
						$sFileName = $arFileItem['FILE_NAME'];
						if(!strlen($sFileName)) {
							//$arPathInfo = pathinfo($sFullPath);
							//$sFileName = $arPathInfo['basename'];
							// pathinfo ��� ������������� �������� ������� ���������� ��������� ������, ��������: setlocale(LC_CTYPE, 'ru_RU.utf8');
							// �.�. �� ��� �� ������������, �� ����� ������������ ������� ���� ��� ��������� ����� �����
							$sFileName = GetFileName($sFullPath);
						}
						$sFileNameEncoded = $sFileName;
						if(!empty($sCharset)) {
							$sFileNameEncoded = CAllEvent::EncodeMimeString($sFileName, $sCharset);
						}
						$sType = CModuleMailAttaching::GetMime($sFullPath);

						$sMessage .= $sBoundary;
						$sMessage .= $sLF;
						$sMessage .= 'Content-Type: '.$sType.';';
						$sMessage .= $sLF;
						$sMessage .= ' name="'.$sFileNameEncoded.'"';
						$sMessage .= $sLF;
						$sMessage .= 'Content-transfer-encoding: base64';
						$sMessage .= $sLF;
						$sMessage .= 'Content-Disposition: attachment;';
						$sMessage .= $sLF;
						$sMessage .= ' filename="'.$sFileNameEncoded.'"';
						$sMessage .= $sLF.$sLF; // !!!
						$sMessage .= chunk_split(base64_encode(file_get_contents($sFullPath)), 72);
					}
				}
				$sMessage .= $sBoundaryClose;
				$sMessage .= $sLF;
			}
		}

		//
		// OnBeforeCustomMailSend
		//
		$dbEvents = GetModuleEvents('module.mailattaching', 'OnBeforeCustomMailSend');
		$mEventResult = true;
		while(($arEvent = $dbEvents->Fetch()) && $mEventResult !== false) {
			$mEventResult = ExecuteModuleEventEx($arEvent, array(&$sTo, &$sSubject, &$sMessage, &$sAdditionalHeaders, &$sAdditionalParameters));
		}
		if($mEventResult === false) {
			return;
		}

		//
		// sending
		//
		if(!empty($sAdditionalParameters)) {
			return @mail($sTo, $sSubject, $sMessage, $sAdditionalHeaders, $sAdditionalParameters);
		}
		return @mail($sTo, $sSubject, $sMessage, $sAdditionalHeaders);
	}


	//
	// �������� ��������� ������ mime
	//
	public static function GetSysMimeList() {
		return self::$arSysMime;
	}

	//
	// �������� ��������� ������ mime
	//
	public static function GetCustomMimeList() {
		return self::$arCustomMime;
	}

	//
	// �������� ������ ������ mime
	//
	public static function GetMimeList() {
		if(empty(self::$arMimeVirtCache)) {
			$arSysMime = self::GetSysMimeList();
			$arCutomMime = self::GetCustomMimeList();
			self::$arMimeVirtCache = array_merge($arSysMime, $arCutomMime);
		}
		return self::$arMimeVirtCache;
	}

	//
	// ���������� ��������� ������ mime
	//
	public static function SetCustomMimeList($arMimeList) {
		self::FlushMimeCache();
		if(is_array($arMimeList)) {
			$arCutomMime = array();
			foreach($arMimeList as $sExt => $sMime) {
				if((is_string($sExt) || is_numeric($sExt)) && is_string($sMime)) {
					$sMime = trim($sMime);
					$sExt = tolower(trim($sExt));
					if(strlen($sMime) && strlen($sExt)) {
						$arCutomMime[$sExt] = $sMime;
					}
				}
			}
			self::$arCustomMime = $arCutomMime;
		}
	}

	public static function FlushMimeCache() {
		self::$arMimeVirtCache = array();
	}

	//
	// ���������� �������
	//
	public static function OnBeforeEventSendHandler(&$arFields, &$arMailResult) {
		$arAttaches = array();
		if(strtoupper($arMailResult['FIELD1_NAME']) == 'ATTACHED-FILES') {
			if(!empty($arMailResult['FIELD1_VALUE'])) {
				$arFiles = preg_split('#,|;#', $arMailResult['FIELD1_VALUE']);
				if(!empty($arFiles) && is_array($arFiles)) {
					$arAttaches = array_merge($arAttaches, $arFiles);
				}
			}
			$arMailResult['FIELD1_NAME'] = '';
			$arMailResult['FIELD1_VALUE'] = '';
		} 
		if(strtoupper($arMailResult['FIELD2_NAME']) == 'ATTACHED-FILES') {
			if(!empty($arMailResult['FIELD2_VALUE'])) {
				$arFiles = preg_split('#,|;#', $arMailResult['FIELD2_VALUE']);
				if(!empty($arFiles) && is_array($arFiles)) {
					$arAttaches = array_merge($arAttaches, $arFiles);
				}
			}
			$arMailResult['FIELD2_NAME'] = '';
			$arMailResult['FIELD2_VALUE'] = '';
		}

		self::FlushAttaches();
		if(!empty($arAttaches)) {

			$mCallback = self::GetMessageFieldsParser($arFields, $arMailResult, $arAttaches);

			foreach($arAttaches as $sFile) {
				$arCurAttachedFiles = array();
				$sFile = trim($sFile);
				if($mCallback && is_callable($mCallback)) {
					//
					// ����� callback-�������
					//
					$arCurAttachedFiles = call_user_func_array($mCallback, array($sFile, $arFields, $arMailResult, $arAttaches));
				} else {
					//
					// ������� ���������� ���� �������� ���� ��� ����� ������ �� ��������� ���� �����
					//
					$sTmpFileMacros = trim($sFile, '#');
					if($sFile != $sTmpFileMacros) {
						if(array_key_exists($sTmpFileMacros, $arFields)) {
							// ���� ���� ������������� ����� �������
							$sFile = trim($arFields[$sTmpFileMacros]);
						}
					}
					if(strlen($sFile)) {
						$arCurAttachedFiles[] = array(
							'FILE' => $sFile, 
							'FILE_NAME' => ''
						);
					}
				}

				if(!empty($arCurAttachedFiles)) {
					foreach($arCurAttachedFiles as $arTmpItem) {
						if(isset($arTmpItem['FILE']) && is_scalar($arTmpItem['FILE'])) {
							self::AddAttachedFile(
								array(
									'FILE' => $arTmpItem['FILE'], 
									'FILE_NAME' => isset($arTmpItem['FILE_NAME']) && is_scalar($arTmpItem['FILE_NAME']) ? $arTmpItem['FILE_NAME'] : ''
								)
							);
						}
					}
				}
			}
		}
	}

	//
	// ���������� ������� ��������� ������ ��� ������� �������� ����� ��������� �������
	// @params array $arFields - ������ ����������� ����� ��������� �������
	// @params array $arMailResult - ���� ��������� �������
	// @params array $arAttaches - �������� ����� ��� ������������� ������
	// @return mixed callback
	//
	public static function GetMessageFieldsParser($arFields, $arMailResult, $arAttaches) {
		$mCallbackReturn = false;
		//
		// OnGetMessageFieldsParser
		//
		$dbEvents = GetModuleEvents('module.mailattaching', 'OnGetMessageFieldsParser');
		while(($arEvent = $dbEvents->Fetch())) {
			$mCallbackReturn = ExecuteModuleEventEx($arEvent, array($arFields, $arMailResult, $arAttaches, $mCallbackReturn));
		}

		if(!$mCallbackReturn) {
			$mCallbackReturn = CModuleMailAttachingFieldsParser::GetParser($arFields, $arMailResult, $arAttaches);
		}

		return $mCallbackReturn;
	}

	//
	// ���������� ������ ��������� ������� �� ��� ID
	//
	public static function GetEventMessageById($iEventMessageId = 0) {
		static $arStaticCache = array();
		$arReturn = array();
		$iEventMessageId = intval($iEventMessageId);
		if($iEventMessageId > 0) {
			if(!isset($arStaticCache[$iEventMessageId])) {
				$arStaticCache[$iEventMessageId] =& $arReturn;
				$dbItems = CEventMessage::GetById($iEventMessageId);
				if($arItem = $dbItems->Fetch()) {
					$arReturn = $arItem;
				}
			} else {
				$arReturn = $arStaticCache[$iEventMessageId];
			}
		}
		return $arReturn;
	}

	//
	// ���������� �������� ��� �� ID ��������� �������
	//
	public static function GetEventMessageType($iEventMessageId = 0) {
		$sReturn = '';
		$arEventMessage = self::GetEventMessageById($iEventMessageId);
		if(!empty($arEventMessage) && isset($arEventMessage['EVENT_NAME'])) {
			$sReturn = $arEventMessage['EVENT_NAME'];
		}
		return $sReturn;
	}

	private static function AddAttachedFile($arFile) {
		$sFilePath = isset($arFile['FILE']) ? $arFile['FILE'] : '';
		$sFileAttachName = isset($arFile['FILE_NAME']) ? trim($arFile['FILE_NAME']) : '';
		$sFilePath = self::TreatFilePath($sFilePath);
		if(strlen($sFilePath)) {
			$mKey = count(self::$arAttachFilesPath);
			self::$arAttachFilesPath[$mKey] = $sFilePath;
			if(strlen($sFileAttachName)) {
				self::$arAttachFilesNames[$mKey] = $sFileAttachName;
			}
		}
	}

	protected static function TreatFilePath($sFilePath) {
		// ��������, ���� �������� ����� �����, �� �������� CFile::GetFileArray()
		// is_numeric() ������ ���������, �.�., ��������, ����� +0123.45e6 ����� ���� ������ �����,
		// � ctype_digit() ����� ���� ��������
		$sFilePath = trim($sFilePath);
		$iFileId = intval($sFilePath);
		if($iFileId > 0 && strlen($iFileId) == strlen($sFilePath)) {
			$sFilePath = '';
			$arFileInfo = CFile::GetFileArray($iFileId);
			if(!empty($arFileInfo['SRC'])) {
				$sFilePath = $arFileInfo['SRC'];
			}
		}
		return $sFilePath;
	}

	//
	// ������� ���������� ���� ������ � ����� ������ (���� ���� ������ �������������), ������� ����� ������������� � ������
	//
	public static function GetAttachesEx() {
		$arReturn = array();
		$arAttachFilesPath = self::GetAttaches();
		$arAttachFilesNames = self::GetAttachFilesNames();
		foreach($arAttachFilesPath as $mKey => $sPath) {
			$arReturn[$mKey] = array(
				'FILE' => $sPath,
				'FILE_NAME' => isset($arAttachFilesNames[$mKey]) ? $arAttachFilesNames[$mKey] : ''
			);
		}
		return $arReturn;
	}

	//
	// ������� ���������� ���� ������, ������� ����� ������������� � ������
	//
	public static function GetAttaches() {
		return self::$arAttachFilesPath;
	}

	//
	// ������� ���������� ����� ������, � �������� ����� ������������� �����
	// ���������� �������: ������������ ����� � ������ ����� ���������� �� ���� ������ �� �������.
	// ������������� ���� �������������� �� ������ ������������ �������� GetAttaches() � GetAttachFilesNames()
	//
	public static function GetAttachFilesNames() {
		return self::$arAttachFilesNames;
	}

	public static function FlushAttaches() {
		self::$arAttachFilesPath = array();
		self::$arAttachFilesNames = array();
	}

	public static function GetMime($sFilePath) {
		$arPath = pathinfo($sFilePath);
		$arPath['extension'] = strtolower($arPath['extension']);
		$sMimeType = 'application/octet-stream';
		$arMimes = self::GetMimeList();
		$bContinue = true;
		while((list($sExt, $sCurMimeType) = each($arMimes)) && $bContinue) {
			if($arPath['extension'] == $sExt) {
				$bContinue = false;
				$sMimeType = $sCurMimeType;
			}
		}

		return $sMimeType;
	}
}
