<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
\Bitrix\Main\Loader::includeModule("main");
\Bitrix\Main\Loader::includeModule("reaspekt.geobase");
\Bitrix\Main\Loader::includeModule("highloadblock");
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Config\Option;

$nameCompany = "reaspekt";

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/admin_notify.php");
$response = array();
if(!function_exists('gzopen'))
	$response["ERROR"] = 'GZIP module is not installed!';

ob_implicit_flush(true);
set_time_limit(1800);

if (@preg_match('#ru#i',$_SERVER['HTTP_ACCEPT_LANGUAGE']))
	$lang = 'ru';
if ($_REQUEST['lang'])
	$lang = htmlspecialchars($_REQUEST['lang']);
if (!in_array($lang,array('ru','en')))
	$lang = 'en';

define("LANG", $lang);
define('LOAD_HOST', 'ipgeobase.ru');
define('LOAD_PATH', '/files/db/Main/');
define('LOAD_FILE', 'geo_files.tar.gz');

$_REQUEST['timeout'] = intval($_REQUEST['timeout']);

define('TIMEOUT', ($_REQUEST['timeout'] > 120 ? 120 : htmlspecialchars($_REQUEST['timeout'])));

$strRequestedUrl = 'http://'.LOAD_HOST.LOAD_PATH.LOAD_FILE;
$strFilename = $_SERVER["DOCUMENT_ROOT"]."/upload/".$nameCompany."/geobase/".basename($strRequestedUrl);

$this_script_name = basename(__FILE__);

umask(0);
if (!defined("AS_DIR_PERMISSIONS"))
	define("AS_DIR_PERMISSIONS", 0777);

if (!defined("AS_FILE_PERMISSIONS"))
	define("AS_FILE_PERMISSIONS", 0777);

####### MESSAGES ########
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
####### MESSAGES ########

$strAction = htmlspecialchars($_REQUEST["action"]);

if ($strAction == "UPDATE"){
	if (ReaspGeoIP::GetIsUpdateDataFile(LOAD_HOST, LOAD_PATH, LOAD_FILE, $strFilename)){
		$ID = CAdminNotify::Add(
			array(
				"MESSAGE"	    => Loc::getMessage("REASPEKT_GEOBASE_THERE_IS"),
				"TAG"		    => "GEOBASE_DB_UPDATE_" . date('d.m.Y'),
				"MODULE_ID"	    => "reaspekt.geobase",
				"ENABLE_CLOSE"  => "Y"
			)
		);
		$response = array(
			"UPDATE"	=> "Y",
            "ID" => $ID
		);
	}else{
		$response = array(
			"UPDATE"	=> "N"
		);
	}
} elseif ($strAction == "LOAD"){
	/*********************************************************************/
	$iTimeOut = TIMEOUT;
	$strUserAgent = "ReaspektGeoBaseLoader";
	
	$strLog = '';
	$status = '';
	$res = LoadFile($strRequestedUrl, $strFilename, $iTimeOut);
	
	
	if (!$res) {
		$response["STATUS"]	 = $res;
		$response["PROGRESS"]   = $status;
		$response["NEXT_STEP"]  = false;
		$response["MESSAGE"]	= nl2br($strLog);
	
    } elseif ($res == 3) { // partial downloading
		
        $response["STATUS"]	 = $res;
		$response["PROGRESS"]   = $status;
		$response["NEXT_STEP"]  = "LOAD";
	
    } elseif ($res == 2) {
		
        $response["STATUS"]	 = $res;
		$response["PROGRESS"]   = $status;
		$response["NEXT_STEP"]  = "UNPACK";
		$response["BY_STEP"]	= "Y";
		$response["FILENAME"]   = urlencode(basename($strRequestedUrl));
	}

/*********************************************************************/
} elseif ($strAction == "UNPACK"){
	SetCurrentStatus(Loc::getMessage("LOADER_UNPACK_ACTION"));
	$oArchiver = new CArchiver($_SERVER["DOCUMENT_ROOT"]."/upload/".$nameCompany."/geobase/".$_REQUEST["filename"], true);
	$tRes = $oArchiver->extractFiles($_SERVER["DOCUMENT_ROOT"]."/upload/".$nameCompany."/geobase/");
	SetCurrentProgress($oArchiver->iCurPos, $oArchiver->iArchSize);
	if($tRes) {
		if (!$oArchiver->bFinish){
			$response["STATUS"]	 = !$oArchiver->bFinish ? 2 : 1;
			$response["PROGRESS"]   = $status;
			$response["NEXT_STEP"]  = "UNPACK";
			$response["SEEK"]	   = $oArchiver->iCurPos;
            $response["FILENAME"]   = urlencode(basename($strRequestedUrl));
		} else {
			$response["STATUS"]	 = !$oArchiver->bFinish ? 2 : 1;
			$response["PROGRESS"]   = $status;
			$response["NEXT_STEP"]  = "DBUPDATE";
			$response["FILENAME"]   = urlencode(basename('cidr_optim'));
			$response["DROP_T"]	 = (isset($_REQUEST['update_db']) && $_REQUEST['update_db'] == "Y") ? "C" : "Y";

			@unlink($_SERVER["DOCUMENT_ROOT"]."/upload/".$nameCompany."/geobase/".$_REQUEST["filename"].'.log');
			@unlink($_SERVER["DOCUMENT_ROOT"]."/upload/".$nameCompany."/geobase/".$_REQUEST["filename"].'.tmp');

			SetCurrentStatus(Loc::getMessage("LOADER_UNPACK_DELETE"));
		}
	} else {
		SetCurrentStatus(Loc::getMessage("LOADER_UNPACK_ERRORS"));
		$arErrors = &$oArchiver->GetErrors();
		if (count($arErrors)>0){
			if ($ft = fopen($_SERVER["DOCUMENT_ROOT"]."/upload/".$nameCompany."/geobase/".$this_script_name.".log", "wb")){
				foreach ($arErrors as $value){
					$str = "[".$value[0]."] ".$value[1]."\n";
					fwrite($ft, $str);
					$response["ERROR"] .= $str.'; ';
					$txt .= $str . '<br>';
				}
				fclose($ft);
			}
		}
	}
	/*********************************************************************/
} elseif ($strAction == "DBUPDATE") {
	$iTimeOut = TIMEOUT;
	if ($iTimeOut > 0)
		$start_time = reaspekt_geobase_getmicrotime ();
	
	//Удаление HL
	if ($_REQUEST["drop_t"] == 'Y') {
        //TODO: ПОЛУЧАТЬ ID HL по названию таблицы.
		
		$requestHL = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('order' => array('NAME'), 'filter' => array("TABLE_NAME" => array("reaspekt_geobase_cities","reaspekt_geobase_codeip"))));
		
		while ($rowHL = $requestHL->fetch()){
			if ($DB->TableExists($rowHL["TABLE_NAME"])) {
				HL\HighloadBlockTable::delete($rowHL["ID"]);
			}
		}
	}
    
    //Очистка HL для обновления
	if ($_REQUEST["drop_t"] == 'C') {
        //TODO: ПОЛУЧАТЬ ID HL по названию таблицы.
		
        $arTable = array("reaspekt_geobase_cities","reaspekt_geobase_codeip");
        
        foreach ($arTable as $nameSqlTable) {
            $DB->Query('TRUNCATE TABLE `' . $nameSqlTable . '`');
        }
	}
	
	switch($_REQUEST["filename"]){
		case "cidr_optim"   : {
			$FPath = '/upload/'.$nameCompany.'/geobase/cidr_optim.txt';
			$fileSize = filesize($_SERVER["DOCUMENT_ROOT"].$FPath);
			$f = fopen($_SERVER["DOCUMENT_ROOT"].$FPath, 'r');
			$_REQUEST["seek"] ? fseek($f, $_REQUEST["seek"]) : false;
			
			$highloadBlockData = array (
				'NAME' => 'ReaspektGeobaseCodeip',
				'TABLE_NAME' => 'reaspekt_geobase_codeip'
			);
			
			//Создаем HL
			$obResult = HL\HighloadBlockTable::add($highloadBlockData);
			
			//Успешное создание
			if ($obResult->isSuccess()) {
				
				//Формируем массив полей для записи в HL
				$arFieldsCodeIp = array(
					"ACTIVE" => "boolean",
					"BLOCK_BEGIN" => "string",
					"BLOCK_END" => "string",
					"BLOCK_ADDR" => "string",
					"COUNTRY_CODE" => "string",
					"CITY_ID" => "string",
				);
				
				$userTypeEntity = new CUserTypeEntity();
				
				foreach ($arFieldsCodeIp as $nameField => $typeField) {
					$userTypeData = array(
						'ENTITY_ID' => "HLBLOCK_".$obResult->getId(), /*это id highload блока*/
						'FIELD_NAME' => "UF_".$nameField,
						'USER_TYPE_ID' => $typeField,
						'MANDATORY' => 'N',
						'SHOW_FILTER' => 'S',
						'IS_SEARCHABLE' => 'N',
						'EDIT_FORM_LABEL'   => array(
							'ru'    => $nameField,
							'en'    => $nameField,
						),
						'LIST_COLUMN_LABEL' => array(
							'ru'    => $nameField,
							'en'    => $nameField,
						),
						'LIST_FILTER_LABEL' => array(
							'ru'    => $nameField,
							'en'    => $nameField,
						),
					);
					
					if ($typeField == "boolean") {
						$userTypeData["SETTINGS"]["DEFAULT_VALUE"] = 1;
						$userTypeData["SETTINGS"]["DISPLAY"] = "CHECKBOX";
					}
					
					//Добавляем поля в HL
					$userTypeId = $userTypeEntity->Add( $userTypeData );
					
				}
			}
			
			$bFinished = true;
			$strFields = "UF_ACTIVE, "
						."UF_BLOCK_BEGIN, "
						."UF_BLOCK_END, "
						."UF_BLOCK_ADDR, "
						."UF_COUNTRY_CODE, "
						."UF_CITY_ID";
			while (!feof ($f)) {
				if (TIMEOUT > 0 && (reaspekt_geobase_getmicrotime() - $start_time) > TIMEOUT) {
					$bFinished = False;
					break;
				}
				
				$strVar = fgets($f);
				
				
				
				if(trim($strVar) !== ''){
					$arValues = explode(',' ,preg_replace("/\t/", ',', $strVar));
                    
					if(!empty($arValues)){
							$strValues .=   (!!strlen($strValues) ? ', ' : '')
									.'( 1, '.$arValues[0].', '
									.$arValues[1].', '
									."'".$DB->ForSql($arValues[2])."', "
									."'".$DB->ForSql($arValues[3])."', "
									.intval($arValues[4]).')';
					}
				}
			}
			$DB->Query('INSERT INTO reaspekt_geobase_codeip ('.$strFields.') VALUES '.$strValues);
			SetCurrentProgress (ftell($f), $fileSize);
			if ($bFinished){
				$response = array(
					"STATUS"	=> 1,
					"PROGRESS"  => 100,
					"NEXT_STEP" => "DBUPDATE",
					"FILENAME"  => urlencode(basename("cities")),
					"SEEK"	  => 0,
					"DROP_T"	=> "N",
					"MES"	   => iconv("cp1251", "UTF-8", Loc::getMessage('REASPEKT_GEOBASE_TABLE_CODEIP_UPDATED'))
				);
			}else {
				$response = array(
					"STATUS"	=> 1,
					"PROGRESS"  => $status,
					"NEXT_STEP" => "DBUPDATE",
					"SEEK"	  => ftell($f),
					"SIZE"	  => $fileSize,
					"FILENAME"  => urlencode(basename("cidr_optim")),
					"DROP_T"	=> "N"
				);
			}
			break;
		}
		case "cities"	   : {
			$FPath = '/upload/'.$nameCompany.'/geobase/cities.txt';
			if(SITE_CHARSET == "UTF-8"){
				$strFName = $_SERVER['DOCUMENT_ROOT'].$FPath;
				file_put_contents($strFName, iconv("windows-1251", "UTF-8", file_get_contents($strFName)));
			}
			$fileSize = filesize($_SERVER["DOCUMENT_ROOT"].$FPath);
			$f = fopen($_SERVER["DOCUMENT_ROOT"].$FPath, 'r');
			$_REQUEST["seek"] ? fseek($f, $_REQUEST["seek"]) : false;
		
			if(!$DB->TableExists('reaspekt_geobase_cities')){
				
				$highloadBlockData = array (
					'NAME' => 'ReaspektGeobaseCities',
					'TABLE_NAME' => 'reaspekt_geobase_cities'
				);
				
				//Создаем HL
				$obResult = HL\HighloadBlockTable::add($highloadBlockData);
				
				//Успешное создание
				if ($obResult->isSuccess()) {
					
					//Формируем массив полей для записи в HL
					$arFieldsCodeIp = array(
						"XML_ID" => "integer",
						"ACTIVE" => "boolean",
						"NAME" => "string",
						"REGION_NAME" => "string",
						"COUNTY_NAME" => "string",
						"BREADTH_CITY" => "string",
						"LONGITUDE_CITY" => "string",
					);
					
					$userTypeEntity = new CUserTypeEntity();
					
					foreach ($arFieldsCodeIp as $nameField => $typeField) {
						$userTypeData = array(
							'ENTITY_ID' => "HLBLOCK_".$obResult->getId(), /*это id highload блока*/
							'FIELD_NAME' => "UF_".$nameField,
							'USER_TYPE_ID' => $typeField,
							'MANDATORY' => 'N',
							'SHOW_FILTER' => 'S',
							'IS_SEARCHABLE' => 'N',
							'EDIT_FORM_LABEL'   => array(
								'ru'    => $nameField,
								'en'    => $nameField,
							),
							'LIST_COLUMN_LABEL' => array(
								'ru'    => $nameField,
								'en'    => $nameField,
							),
							'LIST_FILTER_LABEL' => array(
								'ru'    => $nameField,
								'en'    => $nameField,
							),
						);
						
						if ($typeField == "boolean") {
							$userTypeData["SETTINGS"]["DEFAULT_VALUE"] = 1;
							$userTypeData["SETTINGS"]["DISPLAY"] = "CHECKBOX";
						}
						
						if ($typeField == "integer") {
							$userTypeData["SETTINGS"]["DEFAULT_VALUE"] = "";
							$userTypeData["SETTINGS"]["SIZE"] = "20";
							$userTypeData["SETTINGS"]["MIN_VALUE"] = "0";
							$userTypeData["SETTINGS"]["MAX_VALUE"] = "0";
						}
						
						//Добавляем поля в HL
						$userTypeId = $userTypeEntity->Add( $userTypeData );
						
					}
				}
			
			}
			
			$bFinished = true;
			$strFields  =   "UF_XML_ID, "
							."UF_ACTIVE, "
							."UF_NAME, "
							."UF_REGION_NAME, "
							."UF_COUNTY_NAME ,"
							."UF_BREADTH_CITY ,"
							."UF_LONGITUDE_CITY";
			
			while (!feof ($f)) {
				if (TIMEOUT > 0 && (reaspekt_geobase_getmicrotime() - $start_time) > TIMEOUT) {
					$bFinished = False;
					break;
				}
                
				$arValues = explode(',' ,preg_replace("/\t/", ',', fgets($f)));
                
                if (intval($arValues[0])) {
                    $strValues .= (!!strlen($strValues) ? ", " : "")
								."(".intval($arValues[0]).", "
								."1, "
								."'".$DB->ForSql($arValues[1]) ."', "
								."'".$DB->ForSql($arValues[2]) ."', "
								."'".$DB->ForSql($arValues[3]) ."', "
									.floatval($arValues[4])	 .", "
									.floatval($arValues[5])	 .")";
                }
			}
            
			$DB->Query('INSERT INTO reaspekt_geobase_cities ('.$strFields.') VALUES '.$strValues);
			
			SetCurrentProgress (ftell($f), $fileSize);
			
			if ($bFinished) {
				$response = array(
					"STATUS"	=> 0,
					"PROGRESS"  => 100,
					"MES"	   => iconv("cp1251", "UTF-8", Loc::getMessage('REASPEKT_GEOBASE_TABLE_CODEIP_UPDATED'))
				);
                CAdminNotify::DeleteByModule("reaspekt.geobase");
			} else {
				$response = array(
					"STATUS"	=> 1,
					"PROGRESS"  => $status,
					"NEXT_STEP" => "DBUPDATE",
					"SEEK"	  => ftell($f),
					"SIZE"	  => $fileSize,
					"FILENAME"  => urlencode(basename("cities")),
					"DROP_T"	=> "N"
				);
			}
			break;
		}
	}
}

########### JSON #########
print json_encode_cyr($response);
##########################


function LoadFile ($strRequestedUrl, $strFilename, $iTimeOut){
		global $strUserAgent;
		$iTimeOut = IntVal($iTimeOut);
		if ($iTimeOut > 0)
			$start_time = reaspekt_geobase_getmicrotime ();
		$strRealUrl = $strRequestedUrl;
		$iStartSize = 0;

		// Initialize if spool download
		$strRealUrl_tmp = "";
		$iRealSize_tmp = 0;
		if (file_exists ($strFilename . ".tmp") && file_exists ($strFilename . ".log") && filesize ($strFilename . ".log") > 0) {
			$fh = fopen ($strFilename . ".log", "rb");
			$file_contents_tmp = fread ($fh, filesize ($strFilename . ".log"));
			fclose ($fh);

			list($strRealUrl_tmp, $iRealSize_tmp) = preg_split ("/\n/", $file_contents_tmp);
			$strRealUrl_tmp = Trim($strRealUrl_tmp);
			$iRealSize_tmp = Trim($iRealSize_tmp);
		}
		if ($iRealSize_tmp <= 0 || strlen ($strRealUrl_tmp) <= 0) {
			if (file_exists ($strFilename . ".tmp"))
				@unlink ($strFilename . ".tmp");
			if (file_exists ($strFilename . ".log"))
				@unlink ($strFilename . ".log");
		} else {
			$strRealUrl = $strRealUrl_tmp;
			$iStartSize = filesize ($strFilename . ".tmp");
		}
		
		// END: Initialize if spool download

		// Look for a file and requests INFO
		do {
			$lasturl	= $strRealUrl;
			$parsedUrl  = parse_url ($strRealUrl);
			$host	   = $parsedUrl["host"];
			$port	   = $parsedUrl["port"];
			$hostName   = $host;
			$port	   = $port ? $port : "80";

			$socketHandle = fsockopen ($host, $port, $error_id, $error_msg, 30);
			if (!$socketHandle) {
				 return false;
			}else{
				if (!$parsedUrl["path"]) $parsedUrl["path"] = "/";
				$request = "";
				$request .= "HEAD " . $parsedUrl["path"] . ($parsedUrl["query"] ? '?' . $parsedUrl["query"] : '') . " HTTP/1.0\r\n";
				$request .= "Host: $hostName\r\n";
				if ($strUserAgent != "") $request .= "User-Agent: $strUserAgent\r\n";
				$request .= "\r\n";
				fwrite ($socketHandle, $request);
				$replyHeader = "";
				while (($result = fgets ($socketHandle, 4024)) && $result != "\r\n") {
					$replyHeader .= $result;
				}
				fclose ($socketHandle);
				$ar_replyHeader = preg_split ("/\r\n/", $replyHeader);
				$replyCode = 0;
				$replyMsg = "";
				if (preg_match("#([A-Z]{4})/([0-9.]{3}) ([0-9]{3})#", $ar_replyHeader[0], $regs)) {
					$replyCode = IntVal ($regs[3]);
					$replyMsg = substr ($ar_replyHeader[0], strpos ($ar_replyHeader[0], $replyCode) + strlen ($replyCode) + 1, strlen ($ar_replyHeader[0]) - strpos ($ar_replyHeader[0], $replyCode) + 1);
				}
				if ($replyCode != 200 && $replyCode != 302) {
					if ($replyCode == 403) SetCurrentStatus (Loc::getMessage ("LOADER_LOAD_SERVER_ANSWER1")); else
						SetCurrentStatus (str_replace ("#ANS#", $replyCode . " - " . $replyMsg, Loc::getMessage ("LOADER_LOAD_SERVER_ANSWER")) . '<br>' . htmlspecialchars ($strRequestedUrl));
					return false;
				}
				$strLocationUrl = "";
				$iNewRealSize = 0;
				$strAcceptRanges = "";
				for ($i = 1; $i < count ($ar_replyHeader); $i++) {
					if (strpos ($ar_replyHeader[$i], "Location") !== false) $strLocationUrl = trim (substr ($ar_replyHeader[$i], strpos ($ar_replyHeader[$i], ":") + 1, strlen ($ar_replyHeader[$i]) - strpos ($ar_replyHeader[$i], ":") + 1)); elseif (strpos ($ar_replyHeader[$i], "Content-Length") !== false) $iNewRealSize = IntVal (Trim (substr ($ar_replyHeader[$i], strpos ($ar_replyHeader[$i], ":") + 1, strlen ($ar_replyHeader[$i]) - strpos ($ar_replyHeader[$i], ":") + 1))); elseif (strpos ($ar_replyHeader[$i], "Accept-Ranges") !== false) $strAcceptRanges = Trim (substr ($ar_replyHeader[$i], strpos ($ar_replyHeader[$i], ":") + 1, strlen ($ar_replyHeader[$i]) - strpos ($ar_replyHeader[$i], ":") + 1));
				}
				if (strlen ($strLocationUrl) > 0) {
					$redirection = $strLocationUrl;
					if ((strpos ($redirection, "http://") === false))
						$strRealUrl = dirname ($lasturl) . "/" . $redirection;
					else
						$strRealUrl = $redirection;
				}
				if (strlen ($strLocationUrl) <= 0)
					break;
			}
		} while (true);
		// END: Look for a file and requests INFO
		
		$bCanContinueDownload = ($strAcceptRanges == "bytes");

		// If it is possible to complete the download
		if ($bCanContinueDownload) {
			$fh = fopen ($strFilename . ".log", "wb");
			if (!$fh) {
				SetCurrentStatus (str_replace ("#FILE#", $strFilename . ".log", Loc::getMessage ("LOADER_LOAD_NO_WRITE2FILE")));
				return false;
			}
			fwrite ($fh, $strRealUrl . "\n");
			fwrite ($fh, $iNewRealSize . "\n");
			fclose ($fh);
		}
		// END: If it is possible to complete the download

		// download file
		$parsedUrl = parse_url($strRealUrl);
		$host = $parsedUrl["host"];
		$port = $parsedUrl["port"];
		$hostName = $host;
		$port = $port ? $port : "80";

		SetCurrentStatus (str_replace ("#HOST#", $host, Loc::getMessage ("LOADER_LOAD_CONN2HOST")));
		
		
		$socketHandle = fsockopen ($host, $port, $error_id, $error_msg, 30);
		if (!$socketHandle) {
			SetCurrentStatus (str_replace ("#HOST#", $host, Loc::getMessage ("LOADER_LOAD_NO_CONN2HOST")) . " [" . $error_id . "] " . $error_msg);
			return false;
		} else {
			if (!$parsedUrl["path"]) $parsedUrl["path"] = "/";

			SetCurrentStatus (Loc::getMessage ("LOADER_LOAD_QUERY_FILE"));

			$request = "";
			$request .= "GET " . $parsedUrl["path"] . ($parsedUrl["query"] ? '?' . $parsedUrl["query"] : '') . " HTTP/1.0\r\n";
			$request .= "Host: $hostName\r\n";

			if ($strUserAgent != "") $request .= "User-Agent: $strUserAgent\r\n";
			if ($bCanContinueDownload && $iStartSize > 0) $request .= "Range: bytes=" . $iStartSize . "-\r\n";

			$request .= "\r\n";

			fwrite ($socketHandle, $request);

			$result = "";
			SetCurrentStatus (Loc::getMessage ("LOADER_LOAD_WAIT"));
			$replyHeader = "";
			while (($result = fgets ($socketHandle, 4096)) && $result != "\r\n")
				$replyHeader .= $result;
			$ar_replyHeader = preg_split ("/\r\n/", $replyHeader);
			$replyCode = 0;
			$replyMsg = "";
			if (preg_match("#([A-Z]{4})/([0-9.]{3}) ([0-9]{3})#", $ar_replyHeader[0], $regs)) {
				$replyCode = IntVal ($regs[3]);
				$replyMsg = substr ($ar_replyHeader[0], strpos ($ar_replyHeader[0], $replyCode) + strlen ($replyCode) + 1, strlen ($ar_replyHeader[0]) - strpos ($ar_replyHeader[0], $replyCode) + 1);
			}
			if ($replyCode != 200 && $replyCode != 302 && $replyCode != 206) {
				SetCurrentStatus (str_replace ("#ANS#", $replyCode . " - " . $replyMsg, Loc::getMessage ("LOADER_LOAD_SERVER_ANSWER")));
				return false;
			}
			$strContentRange = "";
			$iContentLength = 0;
			for ($i = 1; $i < count ($ar_replyHeader); $i++) {
				if (strpos ($ar_replyHeader[$i], "Content-Range") !== false) $strContentRange = trim (substr ($ar_replyHeader[$i], strpos ($ar_replyHeader[$i], ":") + 1, strlen ($ar_replyHeader[$i]) - strpos ($ar_replyHeader[$i], ":") + 1)); elseif (strpos ($ar_replyHeader[$i], "Content-Length") !== false) $iContentLength = doubleval (Trim (substr ($ar_replyHeader[$i], strpos ($ar_replyHeader[$i], ":") + 1, strlen ($ar_replyHeader[$i]) - strpos ($ar_replyHeader[$i], ":") + 1))); elseif (strpos ($ar_replyHeader[$i], "Accept-Ranges") !== false) $strAcceptRanges = Trim (substr ($ar_replyHeader[$i], strpos ($ar_replyHeader[$i], ":") + 1, strlen ($ar_replyHeader[$i]) - strpos ($ar_replyHeader[$i], ":") + 1));
			}
			$bReloadFile = True;
			if (strlen ($strContentRange) > 0) {
				if (preg_match("# *bytes +([0-9]*) *- *([0-9]*) */ *([0-9]*)#", $strContentRange, $regs)) {
				$iStartBytes_tmp = doubleval ($regs[1]);
				$iEndBytes_tmp = doubleval ($regs[2]);
				$iSizeBytes_tmp = doubleval ($regs[3]);

				if ($iStartBytes_tmp == $iStartSize && $iEndBytes_tmp == ($iNewRealSize - 1) && $iSizeBytes_tmp == $iNewRealSize) {
					$bReloadFile = False;
				}
			}
		}
		if ($bReloadFile) {
			@unlink ($strFilename . ".tmp");
			$iStartSize = 0;
		}
		if (($iContentLength + $iStartSize) != $iNewRealSize) {
			SetCurrentStatus (Loc::getMessage ("LOADER_LOAD_ERR_SIZE"));
			return false;
		}
		$fh = fopen ($strFilename . ".tmp", "ab");
		if (!$fh) {
			SetCurrentStatus (str_replace ("#FILE#", $strFilename . ".tmp", Loc::getMessage ("LOADER_LOAD_CANT_OPEN_WRITE")));
			return false;
		}
		$bFinished = True;
		$downloadsize = (double)$iStartSize;
		SetCurrentStatus (Loc::getMessage ("LOADER_LOAD_LOADING"));
		while (!feof ($socketHandle)) {
			if ($iTimeOut > 0 && (reaspekt_geobase_getmicrotime() - $start_time) > $iTimeOut) {
				$bFinished = False;
				break;
			}
			$result = fread ($socketHandle, 256 * 1024);
			$downloadsize += strlen ($result);
			if ($result == "") break;
			fwrite ($fh, $result);
		}
		SetCurrentProgress ($downloadsize, $iNewRealSize);
		fclose ($fh);
		fclose ($socketHandle);
		if ($bFinished) {
			@unlink ($strFilename);
			if (!@rename ($strFilename . ".tmp", $strFilename)) {
				SetCurrentStatus (str_replace ("#FILE2#", $strFilename, str_replace ("#FILE1#", $strFilename . ".tmp", Loc::getMessage ("LOADER_LOAD_ERR_RENAME"))));
				return false;
			}
			@unlink ($strFilename . ".tmp");
		} else
			return 3;

		SetCurrentStatus (str_replace ("#SIZE#", $downloadsize, str_replace ("#FILE#", $strFilename, Loc::getMessage ("LOADER_LOAD_FILE_SAVED"))));
		@unlink ($strFilename . ".log");
		return 2;
	}
	// END: download file
}
function SetCurrentStatus($str) {
	global $strLog;
	$strLog .= $str."\n";
}
function SetCurrentProgress($cur, $total = 0) {
	global $status;
	if (!$total){
		$total  = 100;
		$cur	= 0;
	}
	$val = intval($cur/$total*100);
	if ($val > 100){
		$val = 100;
	}

	$status = $val;
}
function reaspekt_geobase_getmicrotime() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
function json_encode_cyr($str) {
	$arr_replace_utf = array(   'null', '\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
		'\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
		'\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
		'\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
		'\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
		'\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
		'\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
		'\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');

	$arr_replace_cyr = array('false', 'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
		'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
		'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
		'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');

	$str1 = json_encode($str, JSON_FORCE_OBJECT);
	$str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
	return $str2;
}

class CArchiver{
	var $_strArchiveName = "";
	var $_bCompress = false;
	var $_strSeparator = " ";
	var $_dFile = 0;

	var $_arErrors = array();
	var $iArchSize = 0;
	var $iCurPos = 0;
	var $bFinish = false;

	function CArchiver($strArchiveName, $bCompress = false){
		$this->_bCompress = false;
		if (!$bCompress){
			if (file_exists($strArchiveName)){
				if ($fp = fopen($strArchiveName, "rb"))	{
					$data = fread($fp, 2);
					if ($data == "\37\213"){
						$this->_bCompress = True;
					}
				}
			}else{
				if (substr($strArchiveName, -2) == 'gz'){
					$this->_bCompress = True;
				}
			}
		}
		else{
			$this->_bCompress = True;
		}
		$this->_strArchiveName = $strArchiveName;
		$this->_arErrors = array();
	}

	function extractFiles($strPath, $vFileList = false)	{
		$this->_arErrors = array();

		$v_result = true;
		$v_list_detail = array();

		$strExtrType = "complete";
		$arFileList = 0;
		if ($vFileList!==false){
			$arFileList = &$this->_parseFileParams($vFileList);
			$strExtrType = "partial";
		}

		if ($v_result = $this->_openRead()){
			$v_result = $this->_extractList($strPath, $v_list_detail, $strExtrType, $arFileList, '', '');
			$this->_close();
		}

		return $v_result;
	}

	function &GetErrors() {
		return $this->_arErrors;
	}

	function _extractList($p_path, &$p_list_detail, $p_mode, $p_file_list, $p_remove_path, $v_filename)	{
		global $iNumDistrFiles;

		$v_result = true;
		$v_nb = 0;
		$v_extract_all = true;
		$v_listing = false;

		$p_path = str_replace("\\", "/", $p_path);

		if ($p_path == '' || (substr($p_path, 0, 1) != '/' && substr($p_path, 0, 3) != "../" && !strpos($p_path, ':'))){
			$p_path = "./".$p_path;
		}

		$p_remove_path = str_replace("\\", "/", $p_remove_path);
		if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/'))
			$p_remove_path .= '/';

		$p_remove_path_size = strlen($p_remove_path);

		switch ($p_mode){
			case "complete" :
				$v_extract_all = TRUE;
				$v_listing = FALSE;
				break;
			case "partial" :
				$v_extract_all = FALSE;
				$v_listing = FALSE;
				break;
			case "list" :
				$v_extract_all = FALSE;
				$v_listing = TRUE;
				break;
			default :
				$this->_arErrors[] = array("ERR_PARAM", "Invalid extract mode (".$p_mode.")");
				return false;
		}

		clearstatcache();

		$tm=time();
		while((extension_loaded("mbstring")? mb_strlen($v_binary_data = $this->_readBlock(), "latin1") : strlen($v_binary_data = $this->_readBlock())) != 0){
			$v_extract_file = FALSE;
			$v_extraction_stopped = 0;

			if (!$this->_readHeader($v_binary_data, $v_header))
				return false;

			if ($v_header['filename'] == '')
				continue;

			// ----- Look for long filename
				if ($v_header['typeflag'] == 'L')
			{
				if (!$this->_readLongHeader($v_header))
					return false;
			}


			if ((!$v_extract_all) && (is_array($p_file_list)))
			{
				// ----- By default no unzip if the file is not found
				$v_extract_file = false;

				for ($i = 0; $i < count($p_file_list); $i++)
				{
					// ----- Look if it is a directory
					if (substr($p_file_list[$i], -1) == '/')
					{
						// ----- Look if the directory is in the filename path
						if ((strlen($v_header['filename']) > strlen($p_file_list[$i]))
							&& (substr($v_header['filename'], 0, strlen($p_file_list[$i])) == $p_file_list[$i]))
						{
							$v_extract_file = TRUE;
							break;
						}
					}
					elseif ($p_file_list[$i] == $v_header['filename'])
					{
						// ----- It is a file, so compare the file names
						$v_extract_file = TRUE;
						break;
					}
				}
			}
			else{
			  $v_extract_file = TRUE;
			}

			// ----- Look if this file need to be extracted
			if (($v_extract_file) && (!$v_listing)){
				if (($p_remove_path != '') && (substr($v_header['filename'], 0, $p_remove_path_size) == $p_remove_path)){
					$v_header['filename'] = substr($v_header['filename'], $p_remove_path_size);
				}
				if (($p_path != './') && ($p_path != '/')){
					while (substr($p_path, -1) == '/')
						$p_path = substr($p_path, 0, strlen($p_path)-1);

					if (substr($v_header['filename'], 0, 1) == '/')
						$v_header['filename'] = $p_path.$v_header['filename'];
					else
						$v_header['filename'] = $p_path.'/'.$v_header['filename'];
				}
				if (file_exists($v_header['filename'])){
					if ((@is_dir($v_header['filename'])) && ($v_header['typeflag'] == '')){
						$this->_arErrors[] = array("DIR_EXISTS", "File '".$v_header['filename']."' already exists as a directory");
						return false;
					}
					if ((is_file($v_header['filename'])) && ($v_header['typeflag'] == "5")){
						$this->_arErrors[] = array("FILE_EXISTS", "Directory '".$v_header['filename']."' already exists as a file");
						return false;
					}
					if (!is_writeable($v_header['filename'])){
						$this->_arErrors[] = array("FILE_PERMS", "File '".$v_header['filename']."' already exists and is write protected");
						return false;
					}
				}elseif (($v_result = $this->_dirCheck(($v_header['typeflag'] == "5" ? $v_header['filename'] : dirname($v_header['filename'])))) != 1){
					$this->_arErrors[] = array("NO_DIR", "Unable to create path for '".$v_header['filename']."'");
					return false;
				}

				if ($v_extract_file){
					if ($v_header['typeflag'] == "5"){
						if (!@file_exists($v_header['filename'])){
							if (!@mkdir($v_header['filename'], AS_DIR_PERMISSIONS)){
								$this->_arErrors[] = array("ERR_CREATE_DIR", "Unable to create directory '".$v_header['filename']."'");
								return false;
							}
						}
					}else{
						if (($v_dest_file = fopen($v_header['filename'], "wb")) == 0){
							$this->_arErrors[] = array("ERR_CREATE_FILE", Loc::getMessage('NO_PERMS') .' '. $v_header['filename']);
							return false;
						}else{
							$n = floor($v_header['size']/512);
							for ($i = 0; $i < $n; $i++){
								$v_content = $this->_readBlock();
								fwrite($v_dest_file, $v_content, 512);
							}
							if (($v_header['size'] % 512) != 0){
								$v_content = $this->_readBlock();
								fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
							}

							@fclose($v_dest_file);

							@chmod($v_header['filename'], AS_FILE_PERMISSIONS);
							@touch($v_header['filename'], $v_header['mtime']);
						}

						clearstatcache();
						if (filesize($v_header['filename']) != $v_header['size']){
							$this->_arErrors[] = array("ERR_SIZE_CHECK", "Extracted file '".$v_header['filename']."' have incorrect file size '".filesize($v_filename)."' (".$v_header['size']." expected). Archive may be corrupted");
							return false;
						}
					}
				}else{
					$this->_jumpBlock(ceil(($v_header['size']/512)));
				}
			}else{
				$this->_jumpBlock(ceil(($v_header['size']/512)));
			}

			if ($v_listing || $v_extract_file || $v_extraction_stopped){
				if (($v_file_dir = dirname($v_header['filename'])) == $v_header['filename'])
					$v_file_dir = '';
				if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == ''))
					$v_file_dir = '/';

				$p_list_detail[$v_nb++] = $v_header;

				if ($v_nb % 100 == 0)
					SetCurrentProgress($this->iCurPos, $this->iArchSize, False);
			}

			if ($_REQUEST['by_step'] && (time()-$tm) > TIMEOUT){
				SetCurrentProgress($this->iCurPos, $this->iArchSize, False);
				return true;
			}
		}
		$this->bFinish = true;
		return true;
	}

	function _readBlock(){
		$v_block = "";
		if (is_resource($this->_dFile)){
			if (isset($_REQUEST['seek'])){
				if ($this->_bCompress)
					gzseek($this->_dFile, intval($_REQUEST['seek']));
				else
					fseek($this->_dFile, intval($_REQUEST['seek']));

				$this->iCurPos = IntVal($_REQUEST['seek']);

				unset($_REQUEST['seek']);
			}
			if ($this->_bCompress)
				$v_block = gzread($this->_dFile, 512);
			else
				$v_block = fread($this->_dFile, 512);

			$this->iCurPos +=  (extension_loaded("mbstring")? mb_strlen($v_block, "latin1") : strlen($v_block));
		}
		return $v_block;
	}

	function _readHeader($v_binary_data, &$v_header){
		if ((extension_loaded("mbstring")? mb_strlen($v_binary_data, "latin1") : strlen($v_binary_data)) ==0)
		{
			$v_header['filename'] = '';
			return true;
		}

		if ((extension_loaded("mbstring")? mb_strlen($v_binary_data, "latin1") : strlen($v_binary_data)) != 512)
		{
			$v_header['filename'] = '';
			$this->_arErrors[] = array("INV_BLOCK_SIZE", "Invalid block size : ".strlen($v_binary_data)."");
			return false;
		}

		$v_checksum = 0;
		for ($i = 0; $i < 148; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));
		for ($i = 148; $i < 156; $i++)
			$v_checksum += ord(' ');
		for ($i = 156; $i < 512; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));

		$v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix/a12temp", $v_binary_data);

		$v_header['checksum'] = OctDec(trim($v_data['checksum']));
		if ($v_header['checksum'] != $v_checksum)
		{
			$v_header['filename'] = '';

			if (($v_checksum == 256) && ($v_header['checksum'] == 0))
				return true;

			$this->_arErrors[] = array("INV_BLOCK_CHECK", "Invalid checksum for file '".$v_data['filename']."' : ".$v_checksum." calculated, ".$v_header['checksum']." expected");
			return false;
		}

		// ----- Extract the properties
		$v_header['filename'] = trim($v_data['prefix']."/".$v_data['filename']);
		$v_header['mode'] = OctDec(trim($v_data['mode']));
		$v_header['uid'] = OctDec(trim($v_data['uid']));
		$v_header['gid'] = OctDec(trim($v_data['gid']));
		$v_header['size'] = OctDec(trim($v_data['size']));
		$v_header['mtime'] = OctDec(trim($v_data['mtime']));
		if (($v_header['typeflag'] = $v_data['typeflag']) == "5")
			$v_header['size'] = 0;

		return true;
	}

	function _readLongHeader(&$v_header){
		$v_filename = '';
		$n = floor($v_header['size']/512);
		for ($i = 0; $i < $n; $i++)
		{
			$v_content = $this->_readBlock();
			$v_filename .= $v_content;
		}
		if (($v_header['size'] % 512) != 0)
		{
			$v_content = $this->_readBlock();
			$v_filename .= $v_content;
		}

		$v_binary_data = $this->_readBlock();

		if (!$this->_readHeader($v_binary_data, $v_header))
			return false;

		$v_header['filename'] = $v_filename;

		return true;
	}

	function _jumpBlock($p_len = false){
		if (is_resource($this->_dFile))
		{
			if ($p_len === false)
				$p_len = 1;

			if ($this->_bCompress)
				gzseek($this->_dFile, gztell($this->_dFile)+($p_len*512));
			else
				fseek($this->_dFile, ftell($this->_dFile)+($p_len*512));
		}
		return true;
	}

	function &_parseFileParams(&$vFileList){
		if (isset($vFileList) && is_array($vFileList))
			return $vFileList;
		elseif (isset($vFileList) && strlen($vFileList)>0)
			return explode($this->_strSeparator, $vFileList);
		else
			return array();
	}

	function _openRead(){
		if ($this->_bCompress){
			$this->_dFile = gzopen($this->_strArchiveName, "rb");
			$this->iArchSize = filesize($this->_strArchiveName) * 3;
		}else {
			$this->_dFile = fopen($this->_strArchiveName, "rb");
			$this->iArchSize = filesize($this->_strArchiveName);
		}
		if (!$this->_dFile){
			$this->_arErrors[] = array("ERR_OPEN", "Unable to open '".$this->_strArchiveName."' in read mode");
			return false;
		}
		return true;
	}

	function _close(){
		if (is_resource($this->_dFile)){
			if ($this->_bCompress)
				gzclose($this->_dFile);
			else
				fclose($this->_dFile);

			$this->_dFile = 0;
		}

		return true;
	}

	function _dirCheck($p_dir){
		if ((is_dir($p_dir)) || ($p_dir == ''))
			return true;

		$p_parent_dir = dirname($p_dir);

		if (($p_parent_dir != $p_dir) &&
			($p_parent_dir != '') &&
			(!$this->_dirCheck($p_parent_dir)))
			return false;

		if (!is_dir($p_dir) && !mkdir($p_dir, AS_DIR_PERMISSIONS))
		{
			$this->_arErrors[] = array("CANT_CREATE_PATH", "Unable to create directory '".$p_dir."'");
			return false;
		}

		return true;
	}
}