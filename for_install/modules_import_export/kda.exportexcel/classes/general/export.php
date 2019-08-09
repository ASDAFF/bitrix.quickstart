<?php
require_once(dirname(__FILE__).'/../../lib/PHPExcel/PHPExcel.php');
IncludeModuleLangFile(__FILE__);

class CKDAExportExcel {
	protected static $moduleId = 'kda.exportexcel';
	protected static $moduleSubDir = '';
	private $pid = false;
	private $filesForMove = array();
	private $sectionPaths = array();
	private $sectionCache = array();
	private $sectionCacheSize = 0;

	function __construct($params=array(), $fparams=array(), $stepparams=false, $pid = false)
	{
		$this->params = $params;
		$this->fparams = $fparams;
		$this->maxReadRows = 100;
		$this->maxReadRowsWOffers = 20;
		$this->stepparams = array();
		$this->stepparams['parentSections'] = array();
		$this->docRoot = rtrim($_SERVER["DOCUMENT_ROOT"], '/');
		$this->bCurrency = CModule::IncludeModule("currency");
		$this->pid = $pid;
		$this->fparamsByName = array();
		if(is_array($this->params['FIELDS_LIST']))
		{
			foreach($this->params['FIELDS_LIST'] as $listIndex=>$arFields)
			{
				foreach($arFields as $key=>$field)
				{
					if($field==='IE_QR_CODE_IMAGE')
					{
						if(!is_array($this->fparams[$listIndex][$key])) $this->fparams[$listIndex][$key] = array();
						$this->fparams[$listIndex][$key]['INSERT_PICTURE'] = 'Y';
						$this->fparams[$listIndex][$key]['QRCODE_SIZE'] = (isset($this->fparams[$listIndex][$key]['QRCODE_SIZE']) && (int)$this->fparams[$listIndex][$key]['QRCODE_SIZE'] > 0 ? (int)$this->fparams[$listIndex][$key]['QRCODE_SIZE'] : 3);
						$this->fparams[$listIndex][$key]['PICTURE_WIDTH'] = $this->fparams[$listIndex][$key]['PICTURE_HEIGHT'] = $this->fparams[$listIndex][$key]['QRCODE_SIZE']*41;
					}
					$this->fparamsByName[$listIndex][$field] = $this->fparams[$listIndex][$key];
				}
			}
		}	
		if(strlen($this->params['ELEMENT_MULTIPLE_SEPARATOR']))
		{
			$this->params['ELEMENT_MULTIPLE_SEPARATOR'] = strtr($this->params['ELEMENT_MULTIPLE_SEPARATOR'], array('\r'=>"\r", '\n'=>"\n", '\t'=>"\t"));
		}

		if(is_array($stepparams))
		{
			$this->stepparams = $stepparams;
			$this->stepparams['list_number'] = (strlen($this->stepparams['list_number']) > 0 ? intval($this->stepparams['list_number']) : '');
			$this->stepparams['list_current_page'] = intval($this->stepparams['list_current_page']);
			$this->stepparams['list_last_page'] = intval($this->stepparams['list_last_page']);
			$this->stepparams['total_read_line'] = intval($this->stepparams['total_read_line']);
			$this->stepparams['total_file_line'] = intval($this->stepparams['total_file_line']);
			$this->stepparams['image_cnt'] = intval($this->stepparams['image_cnt']);
			if(!isset($this->stepparams['string_lengths']) && $this->params['FILE_EXTENSION']=='dbf') $this->stepparams['string_lengths'] = array();
			$this->stepparams['currentPageCnt'] = intval($this->stepparams['currentPageCnt']);
			
			if(!isset($this->params['MAX_EXECUTION_TIME']) || $this->params['MAX_EXECUTION_TIME']!==0)
			{
				if(COption::GetOptionString(static::$moduleId, 'SET_MAX_EXECUTION_TIME')=='Y' && is_numeric(COption::GetOptionString(static::$moduleId, 'MAX_EXECUTION_TIME')))
				{
					$this->params['MAX_EXECUTION_TIME'] = intval(COption::GetOptionString(static::$moduleId, 'MAX_EXECUTION_TIME'));
					if(ini_get('max_execution_time') && $this->params['MAX_EXECUTION_TIME'] > ini_get('max_execution_time') - 5) $this->params['MAX_EXECUTION_TIME'] = ini_get('max_execution_time') - 5;
					if($this->params['MAX_EXECUTION_TIME'] < 5) $this->params['MAX_EXECUTION_TIME'] = 5;
					if($this->params['MAX_EXECUTION_TIME'] > 300) $this->params['MAX_EXECUTION_TIME'] = 300;
				}
				else
				{
					/*$this->params['MAX_EXECUTION_TIME'] = intval(ini_get('max_execution_time')) - 10;
					if($this->params['MAX_EXECUTION_TIME'] < 10) $this->params['MAX_EXECUTION_TIME'] = 10;
					if($this->params['MAX_EXECUTION_TIME'] > 50) $this->params['MAX_EXECUTION_TIME'] = 30;*/
					$this->params['MAX_EXECUTION_TIME'] = 10;
				}
			}
			
			/*Temp folders*/
			$dir = $this->docRoot.'/upload/tmp/'.static::$moduleId.'/'.static::$moduleSubDir;
			CheckDirPath($dir);
			if(!$this->stepparams['tmpdir'])
			{
				if($pid!==false)
				{
					$tmpdir = $dir.'p'.$pid.'/';
					if(file_exists($tmpdir))
					{
						DeleteDirFilesEx(substr($tmpdir, strlen($this->docRoot)));
					}
				}
				else
				{
					$i = 0;
					while(($tmpdir = $dir.$i.'/') && file_exists($tmpdir)){$i++;}
				}
				$this->stepparams['tmpdir'] = $tmpdir;
				CheckDirPath($tmpdir);
			}
			$this->tmpdir = $this->stepparams['tmpdir'];
			$this->imagedir = $this->stepparams['tmpdir'].'images/';
			CheckDirPath($this->imagedir);
			
			$this->tmpfile = $this->tmpdir.'params.txt';
			$oProfile = CKDAExportProfile::getInstance();
			$oProfile->SetExportParams($pid);
			/*/Temp folders*/
			
			if(file_exists($this->tmpfile))
			{
				$this->stepparams = array_merge($this->stepparams, unserialize(file_get_contents($this->tmpfile)));
			}
			
			if(!isset($this->stepparams['curstep'])) $this->stepparams['curstep'] = 'export';
		
			if($pid!==false)
			{
				$this->procfile = $dir.$pid.'.txt';
				if((int)$this->stepparams['export_started'] < 1)
				{
					$oProfile = CKDAExportProfile::getInstance();
					$oProfile->OnStartImport();
				
					if(file_exists($this->procfile)) unlink($this->procfile);
					if($this->params['EXPORT_FILES_IN_ARCHIVE']=='Y' && strlen($this->params['FILES_ARCHIVE_PATH']) > 0)
					{
						$archivePath = $this->docRoot. preg_replace('/\.zip\s*$/U', '', '/'.ltrim($this->params['FILES_ARCHIVE_PATH'], '/'));
						for($suffix=0; $suffix<501; $suffix++)
						{
							$zipFile = $archivePath.($suffix > 0 ? '_'.$suffix : '').'.zip';
							if(file_exists($zipFile)) unlink($zipFile);
						}
					}
				}
			}
		}
	}
	
	public function GetProfileId()
	{
		return $this->pid;
	}
	
	public function CheckTimeEnding()
	{
		return ($this->params['MAX_EXECUTION_TIME'] && (time()-$this->timeBegin >= $this->params['MAX_EXECUTION_TIME']));
	}
	
	public function OpenTmpdataHandler($listIndex, $mode = 'a')
	{
		$this->CloseTmpdataHandler();
		$this->tmpdatafile = $this->tmpdir.'data_'.$listIndex.'.txt';
		$this->tmpdatafilehandler = fopen($this->tmpdatafile, $mode);
	}
	
	public function CloseTmpdataHandler()
	{
		if($this->tmpdatafilehandler)
		{
			fclose($this->tmpdatafilehandler);
		}
		$this->tmpdatafilehandler = false;
	}
	
	public function WriteTmpdata($arElement)
	{
		fwrite($this->tmpdatafilehandler, base64_encode(serialize($arElement))."\r\n");
	}
	
	public function Export()
	{
		$this->stepparams['export_started'] = 1;
		$this->SaveStatusImport();
		$this->timeBegin = time();
		
		$arListIndexes = array(0);
		if(is_array($this->params['LIST_NAME']) && count($this->params['LIST_NAME']) > 0)
		{
			$arListIndexes = array_keys($this->params['LIST_NAME']);
		}
		
		//$listIndex = 0;
		$listIndex = $this->stepparams['list_number'];
		if(!in_array($listIndex, $arListIndexes, true)) $listIndex = (int)current($arListIndexes);
		//$maxListIndex = max($arListIndexes);
		$lastListIndex = end($arListIndexes);
		
		$page = max(1, $this->stepparams['list_current_page']);
		$lastPage = $this->stepparams['list_last_page'];
		$sectionKey = max(1, $this->stepparams['list_current_section']);
		$lastSectionKey = $this->stepparams['list_last_section'];
		$arFields = $this->GetFieldList($listIndex);
		
		$break = (($lastSectionKey > 0 && $sectionKey > $lastSectionKey) 
			&& ($lastPage > 0 && $page > $lastPage)
			/*&& ($listIndex >= $maxListIndex)*/ && ($listIndex == $lastListIndex));
		if(!$break) $this->OpenTmpdataHandler($listIndex);
		while(!$break)
		{
			$this->currentPageCnt = $this->stepparams['currentPageCnt'];
			$arRes = $this->GetExportData($listIndex, $this->maxReadRows, $page, $sectionKey);
			$arData = $arRes['DATA'];
			$lastPage = $arRes['PAGE_COUNT'];
			$recordCount = $arRes['RECORD_COUNT'];
			$sectionKey = $arRes['SECTION_KEY'];
			$lastSectionKey = $arRes['SECTION_COUNT'];
			
			if(!empty($arData))
			{
				foreach($arData as $arElement)
				{
					$this->WriteTmpdata($arElement);
					$this->stepparams['total_read_line']++;
					if(!isset($this->stepparams['rows'][$listIndex])) $this->stepparams['rows'][$listIndex] = 0;
					$this->stepparams['rows'][$listIndex]++;
					if(!isset($this->stepparams['rows2'][$listIndex])) $this->stepparams['rows2'][$listIndex] = 0;
					$this->stepparams['rows2'][$listIndex] += ((isset($arElement['ROWS_COUNT']) && (int)$arElement['ROWS_COUNT'] > 0) ? (int)$arElement['ROWS_COUNT'] : 1);
				}
			}
			
			if(!$this->stepparams['currentPageCnt']) $page++;
			$break = (($lastSectionKey > 0 && $sectionKey > $lastSectionKey) && ($page > $lastPage));
			if($break)
			{
				$break = ($break /*&& ($listIndex >= $maxListIndex)*/ && ($listIndex == $lastListIndex));
				if(!$break)
				{
					$lastSectionKey = $sectionKey = $lastPage = $page = 1;
					reset($arListIndexes);
					while(($next = each($arListIndexes)) && $next['value']!=$listIndex){}
					$next = each($arListIndexes);
					$listIndex = (int)$next['value'];
					unset($this->sepSectionIds);
					$this->OpenTmpdataHandler($listIndex);
				}
			}
			
			if($page > $lastPage)
			{
				$page = 1;
			}
			
			$this->stepparams['list_number'] = $listIndex;
			$this->stepparams['list_current_page'] = $page;
			$this->stepparams['list_last_page'] = $lastPage;
			$this->stepparams['list_current_section'] = $sectionKey;
			$this->stepparams['list_last_section'] = $lastSectionKey;
			$this->stepparams['total_file_line'] = $recordCount;
			$this->SaveStatusImport();
			if($this->CheckTimeEnding())
			{
				return $this->GetBreakParams();
			}
		}
		
		$this->CloseTmpdataHandler();
		
		CKDAExportUtils::PrepareTextRows($this->params['TEXT_ROWS_TOP'], $this->params, $this->stepparams);
		CKDAExportUtils::PrepareTextRows($this->params['TEXT_ROWS_TOP2'], $this->params, $this->stepparams);

		$filePath = CKDAExportUtils::PrepareExportFileName($this->params['FILE_PATH']);
		$outputFile = $this->docRoot.$filePath;
		$dir = dirname($filePath);
		if(file_exists($dir) && is_writable($dir))
		{
			$outputFile = $filePath;
		}
		
		$arWriterParams = array(
			'OUTPUTFILE' => $outputFile,
			'TMPDIR' => $this->tmpdir,
			'IMAGEDIR' => $this->imagedir,
			'LIST_INDEXES' => $arListIndexes,
			'ROWS' => $this->stepparams['rows'],
			'STRING_LENGTHS' => $this->stepparams['string_lengths'],
			'EXTRAPARAMS' => $this->fparams,
			'PARAMS' => $this->params,
			'LISTINDEX' => $listIndex
		);
		if($this->params['FILE_EXTENSION']=='xlsx')
		{
			$objWriter = false;
			if(isset($this->stepparams['WRITER_FILE_PARAMS']) && file_exists($this->stepparams['WRITER_FILE_PARAMS']))
			{
				$objWriter = unserialize(file_get_contents($this->stepparams['WRITER_FILE_PARAMS']));
				if(is_callable(array($objWriter, 'SetEObject')))
				{
					$objWriter->SetEObject($this);
				}
			}
			if(!is_object($objWriter))
			{
				$objWriter = new CKDAExportExcelWriterXlsx($arWriterParams, $this);
			}
			if(false===$objWriter->Save()/* && $this->CheckTimeEnding()*/)
			{
				$writerFileParams = $this->tmpdir.'writer_params.txt';
				file_put_contents($writerFileParams, serialize($objWriter));
				$this->stepparams['WRITER_FILE_PARAMS'] = $writerFileParams;
				return $this->GetBreakParams();
			}
		}
		elseif($this->params['FILE_EXTENSION']=='csv')
		{
			$objWriter = false;
			if(isset($this->stepparams['WRITER_FILE_PARAMS']) && file_exists($this->stepparams['WRITER_FILE_PARAMS']))
			{
				$objWriter = unserialize(file_get_contents($this->stepparams['WRITER_FILE_PARAMS']));
				if(is_callable(array($objWriter, 'SetEObject')))
				{
					$objWriter->SetEObject($this);
				}
			}
			if(!is_object($objWriter))
			{
				$objWriter = new CKDAExportExcelWriterCsv($arWriterParams, $this);
			}
			if(false===$objWriter->Save()/* && $this->CheckTimeEnding()*/)
			{
				$writerFileParams = $this->tmpdir.'writer_params.txt';
				file_put_contents($writerFileParams, serialize($objWriter));
				$this->stepparams['WRITER_FILE_PARAMS'] = $writerFileParams;
				return $this->GetBreakParams();
			}
		}
		elseif($this->params['FILE_EXTENSION']=='dbf')
		{
			$dir = dirname(__FILE__).'/../../lib/PHPExcel/PHPExcel/Reader/XBase/';
			require_once($dir.'Table.php');
			require_once($dir.'WritableTable.php');
			require_once($dir.'Column.php');
			require_once($dir.'Record.php');
			require_once($dir.'Memo.php');
		
			$objWriter = false;
			if(isset($this->stepparams['WRITER_FILE_PARAMS']) && file_exists($this->stepparams['WRITER_FILE_PARAMS']))
			{
				$objWriter = unserialize(file_get_contents($this->stepparams['WRITER_FILE_PARAMS']));
				if(is_callable(array($objWriter, 'SetEObject')))
				{
					$objWriter->SetEObject($this);
				}
			}
			if(!is_object($objWriter))
			{
				$objWriter = new CKDAExportExcelWriterDbf($arWriterParams, $this);
			}
			if(false===$objWriter->Save()/* && $this->CheckTimeEnding()*/)
			{
				$writerFileParams = $this->tmpdir.'writer_params.txt';
				file_put_contents($writerFileParams, serialize($objWriter));
				$this->stepparams['WRITER_FILE_PARAMS'] = $writerFileParams;
				return $this->GetBreakParams();
			}
		}
		else
		{
			$writerType = 'CSV';
			if($this->params['FILE_EXTENSION']=='xlsx') $writerType = 'Excel2007';
			elseif($this->params['FILE_EXTENSION']=='xls') $writerType = 'Excel5';
			
			$objPHPExcel = new KDAPHPExcel();
			$arCols = range('A', 'Z');
			foreach(range('A', 'Z') as $v1)
			{
				foreach(range('A', 'Z') as $v2)
				{
					$arCols[] = $v1.$v2;
				}
			}
			
			$row = 1;
			foreach($arListIndexes as $listIndex)
			{
				$arFields = $this->GetFieldList($listIndex);
				if($listIndex == 0) $worksheet = $objPHPExcel->getActiveSheet();
				else
				{
					if($writerType != 'CSV')
					{
						$worksheet = $objPHPExcel->createSheet();
						$row = 1;
					}
				}

				if($this->params['LIST_NAME'][$listIndex])
				{
					$worksheet->setTitle($this->GetCellValue($this->params['LIST_NAME'][$listIndex]));
				}
				
				if(isset($this->params['TEXT_ROWS_TOP'][$listIndex]))
				{
					foreach($this->params['TEXT_ROWS_TOP'][$listIndex] as $k=>$v)
					{
						$worksheet->setCellValueExplicit($arCols[0].$row, $this->GetCellValue($v));
						$row++;
					}
				}

				if($this->params['HIDE_COLUMN_TITLES'][$listIndex]!='Y')
				{
					$col = 0;
					$fNames = array();
					if(isset($this->params['FIELDS_LIST_NAMES'][$listIndex]))
					{
						$fNames = $this->params['FIELDS_LIST_NAMES'][$listIndex];
					}
					foreach($arFields as $k=>$field)
					{
						$width = 200;
						if((int)$this->fparams[$listIndex][$col]['DISPLAY_WIDTH'] > 0) $width = (int)$this->fparams[$listIndex][$col]['DISPLAY_WIDTH'];
						$worksheet->getColumnDimension($arCols[$col])->setWidth($width / 9.7);
						$worksheet->setCellValueExplicit($arCols[$col].$row, $this->GetCellValue($fNames[$k]));
						$col++;
					}
					$row++;
				}
				
				if(isset($this->params['TEXT_ROWS_TOP2'][$listIndex]))
				{
					foreach($this->params['TEXT_ROWS_TOP2'][$listIndex] as $k=>$v)
					{
						$worksheet->setCellValueExplicit($arCols[0].$row, $this->GetCellValue($v));
						$row++;
					}
				}
				
				$this->OpenTmpdataHandler($listIndex, 'r');
				while(!feof($this->tmpdatafilehandler)) 
				{
					$buffer = trim(fgets($this->tmpdatafilehandler));
					if(strlen($buffer) < 1) continue;
					$arElement = unserialize(base64_decode($buffer));
					if(empty($arElement)) continue;
					
					if(isset($arElement['RTYPE']) && ($arElement['RTYPE']=='SECTION_PATH' || preg_match('/^SECTION_\d+$/', $arElement['RTYPE'])))
					{
						$worksheet->setCellValueExplicit($arCols[0].$row, $this->GetCellValue($arElement['NAME']));
					}
					else
					{
						$col = 0;
						foreach($arFields as $k=>$field)
						{
							$worksheet->setCellValueExplicit($arCols[$col++].$row, $this->GetCellValue((isset($arElement[$field.'_'.$k]) ? $arElement[$field.'_'.$k] : $arElement[$field])));
						}
					}
					$row++;
				}
				$this->CloseTmpdataHandler();
			}
			
			$objWriter = KDAPHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
			if($writerType == 'CSV')
			{
				//$objWriter->setExcelCompatibility(true);
				$delimiter = ($this->params['CSV_SEPARATOR'] ? $this->params['CSV_SEPARATOR'] : ';');
				$objWriter->setDelimiter($delimiter);
				$enclosure = ($this->params['CSV_ENCLOSURE'] ? $this->params['CSV_ENCLOSURE'] : '"');
				$objWriter->setEnclosure($enclosure);
				if($this->params['CSV_ENCODING']=='UTF-8')
				{
					$objWriter->setUseBOM(true);
				}
			}
			$objWriter->save($outputFile);
		}
		$this->SaveStatusImport(true);
		
		$this->CheckExtServices($outputFile);
		
		$oProfile = CKDAExportProfile::getInstance();
		$arEventData = $oProfile->OnEndImport();
		
		return $this->GetBreakParams('finish');
	}
	
	public function CheckExtServices($outputFile)
	{
		if($this->params['EXPORT_TO_BX24']="Y" && $this->params['BX24_REST_URL'] && $this->params['BX24_FOLDER_ID'])
		{
			$url = trim($this->params['BX24_REST_URL']);
			if(substr($url, -1)!='/') $url .= '/';
			$folderType = 'storage';
			$folderId = trim($this->params['BX24_FOLDER_ID']);
			if(preg_match('/_\d+$/', $folderId, $m))
			{
				$folderType = ToLower(substr($folderId, 0, -strlen($m[0])));
				$folderId = substr($m[0], 1);
			}
			$fileName = basename($outputFile);
			$fileContent = base64_encode(file_get_contents($outputFile));
			if(in_array($folderType, array('folder', 'storage')))
			{
				$client = new \Bitrix\Main\Web\HttpClient();
				$res = $client->post($url.'disk.'.$folderType.'.getchildren', array('id' => $folderId, 'filter' => array('TYPE' => 'file', 'NAME'=>$fileName)));
				$arResult = CUtil::JsObjectToPhp($res);
				if($arResult['total'] > 0 && $arResult['result'][0]['ID'])
				{
					$fileId = $arResult['result'][0]['ID'];
					if($this->params['BX24_MODE']=='REPLACE')
					{
						$client = new \Bitrix\Main\Web\HttpClient();
						$res = $client->post($url.'disk.file.delete', array('id' => $fileId));
						$client = new \Bitrix\Main\Web\HttpClient();
						$res = $client->post($url.'disk.'.$folderType.'.uploadfile', array('id' => $folderId, 'data' => array('NAME' => $fileName), 'fileContent'=>$fileContent));
					}
					else
					{
						$client = new \Bitrix\Main\Web\HttpClient();
						$res = $client->post($url.'disk.file.uploadversion', array('id' => $fileId, 'fileContent'=>$fileContent));
					}
				}
				else
				{
					$client = new \Bitrix\Main\Web\HttpClient();
					$res = $client->post($url.'disk.'.$folderType.'.uploadfile', array('id' => $folderId, 'data' => array('NAME' => $fileName), 'fileContent'=>$fileContent));
				}
			}
		}
		
		if($this->params['EXPORT_TO_YADISK']="Y" && $this->params['YADISK_TOKEN'] && $this->params['YADISK_PATH'])
		{
			$token = $this->params['YADISK_TOKEN'];
			$path = $this->params['YADISK_PATH'];
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>15, 'disableSslVerification'=>true));
			$client->setHeader('Authorization', "OAuth ".$token);
			$res = $client->get('https://cloud-api.yandex.net/v1/disk/resources/upload?path='.urlencode($path).'&overwrite=true');
			$arRes = \CUtil::JsObjectToPhp($res);
			if(is_array($arRes) && $arRes['href'])
			{
				$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>60, 'disableSslVerification'=>true));
				$client->setHeader('Authorization', "OAuth ".$token);
				$res = $client->query('PUT', $arRes['href'], file_get_contents($outputFile));
			}
		}
	}
	
	public function GetCellValue($val)
	{
		if($this->params['FILE_EXTENSION']=='csv' && $this->params['CSV_ENCODING']=='CP1251')
		{
			if(defined('BX_UTF') && BX_UTF)
			{
				$val = $GLOBALS['APPLICATION']->ConvertCharset($val, 'UTF-8', 'CP1251');
			}
		}
		elseif(!defined('BX_UTF') || !BX_UTF)
		{
			$val = $GLOBALS['APPLICATION']->ConvertCharset($val, 'CP1251', 'UTF-8');
		}
		return $val;
	}
	
	public function GetBreakParams($action = 'continue')
	{
		$arStepParams = array(
			'params'=> $this->stepparams,
			'action' => $action,
			'errors' => $this->errors,
			'sessid' => bitrix_sessid()
		);
		
		if($action == 'continue')
		{
			fclose($this->tmpdatafilehandler);
			file_put_contents($this->tmpfile, serialize($arStepParams['params']));
			/*if(file_exists($this->imagedir))
			{
				DeleteDirFilesEx(substr($this->imagedir, strlen($this->docRoot)));
			}*/
		}
		elseif(file_exists($this->tmpdir))
		{
			DeleteDirFilesEx(substr($this->tmpdir, strlen($this->docRoot)));
			unlink($this->procfile);
		}
		
		return $arStepParams;
	}
	
	public function ExecuteFilterExpression($val, $expression, $altReturn = true)
	{
		$expression = trim($expression);
		try{				
			if(stripos($expression, 'return')===0)
			{
				return eval($expression.';');
			}
			elseif(preg_match('/\$val\s*=/', $expression))
			{
				eval($expression.';');
				return $val;
			}
			else
			{
				return eval('return '.$expression.';');
			}
		}catch(Exception $ex){
			return $altReturn;
		}
	}
	
	public function ExecuteOnAfterSaveHandler($handler, $ID)
	{
		try{				
			eval($handler.';');
		}catch(Exception $ex){}
	}
	
	public function SaveStatusImport($end = false)
	{
		if($this->procfile)
		{
			$writeParams = $this->stepparams;
			$writeParams['action'] = ($end ? 'finish' : 'continue');
			file_put_contents($this->procfile, CUtil::PhpToJSObject($writeParams));
		}
	}
	
	public function GetFileArray($file, $arDef=array())
	{
		$file = \Bitrix\Main\IO\Path::convertLogicalToPhysical(trim($file));
		if(strpos($file, '/')===0)
		{
			if(file_exists($this->docRoot.$file))
			{
				$arFile = CFile::MakeFileArray($file);
				$ext = '.jpg';
				if(preg_match('/\.[^\.]{2,5}$/', $arFile['name'], $m))
				{
					$ext = ToLower($m[0]);
				}
				$file = $this->imagedir.'image'.(++$this->stepparams['image_cnt']).$ext;
				copy($arFile['tmp_name'], $file);
			}
			else
			{
				$arFile = array();
			}
		}
		elseif(preg_match('/http(s)?:\/\//', $file))
		{
			$arUrl = parse_url($file);
			//Cyrillic domain
			if(preg_match('/[^A-Za-z0-9\-\.]/', $arUrl['host']))
			{
				if(!class_exists('idna_convert')) require_once(dirname(__FILE__).'/../../lib/idna_convert.class.php');
				if(class_exists('idna_convert'))
				{
					$idn = new idna_convert();
					$oldHost = $arUrl['host'];
					if(!CUtil::DetectUTF8($oldHost)) $oldHost = CKDAExportUtils::Win1251Utf8($oldHost);
					$file = str_replace($arUrl['host'], $idn->encode($oldHost), $file);
				}
			}
		}
		$arFile = CFile::MakeFileArray($file);
		if(!$arFile['name'] && !CUtil::DetectUTF8($file))
		{
			$file = CKDAExportUtils::Win1251Utf8($file);
			$arFile = CFile::MakeFileArray($file);
		}
		if(strpos($arFile['type'], 'image/')===0)
		{
			$ext = ToLower(str_replace('image/', '', $arFile['type']));
			if(substr($arFile['name'], -(strlen($ext) + 1))!='.'.$ext)
			{
				if($ext!='jpeg' || (($ext='jpg') && substr($arFile['name'], -(strlen($ext) + 1))!='.'.$ext))
				{
					$arFile['name'] = $arFile['name'].'.'.$ext;
				}
			}
		}
		if(!empty($arDef))
		{
			$arFile = $this->PictureProcessing($arFile, $arDef);
		}
		return $arFile;
	}
	
	public function GetBoolValue($val)
	{
		$trueVals = array_map('trim', explode(',', GetMessage("KDA_EE_FIELD_VAL_Y")));
		$falseVals = array_map('trim', explode(',', GetMessage("KDA_EE_FIELD_VAL_N")));
		if(in_array(ToLower($val), $trueVals))
		{
			return 'Y';
		}
		elseif(in_array(ToLower($val), $falseVals))
		{
			return 'N';
		}
		else
		{
			return false;
		}
	}
	
	public function GetIblockProperties($IBLOCK_ID)
	{
		if(!$this->props[$IBLOCK_ID])
		{
			$this->props[$IBLOCK_ID] = array();
			$dbRes = CIBlockProperty::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID));
			while($arProp = $dbRes->Fetch())
			{
				$this->props[$IBLOCK_ID][$arProp['ID']] = $arProp;
			}
		}
		return $this->props[$IBLOCK_ID];
	}
	
	public function GetPropertyListValue($arProp, $val)
	{
		if($val)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID"=>$arProp['ID'], "ID"=>$val));
				if($arPropEnum = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arPropEnum['VALUE'];
				}
				else
				{
					$this->propVals[$arProp['ID']][$val] = '';
				}
			}
			$val = $this->propVals[$arProp['ID']][$val];
		}
		return $val;
	}
	
	public function GetPropertyElementValue($arProp, $val, $relField)
	{
		if($val)
		{
			$selectField = 'NAME';
			if($relField)
			{
				if(strpos($relField, 'IE_')===0)
				{
					$selectField = substr($relField, 3);
				}
				elseif(strpos($relField, 'IP_PROP')===0)
				{
					$selectField = 'PROPERTY_'.substr($relField, 7);
				}
			}
			
			if(!isset($this->propVals[$arProp['ID']][$selectField][$val]))
			{
				$dbRes = CIBlockElement::GetList(array(), array("ID"=>$val), false, false, array($selectField));
				if($arElem = $dbRes->GetNext())
				{
					$selectedField = $selectField;
					if(strpos($selectedField, 'PROPERTY_')===0) $selectedField .= '_VALUE';
					$this->propVals[$arProp['ID']][$selectField][$val] = $arElem[$selectedField];
				}
				else
				{
					$this->propVals[$arProp['ID']][$selectField][$val] = '';
				}
			}
			$val = $this->propVals[$arProp['ID']][$selectField][$val];
		}
		return $val;
	}
	
	public function GetPropertySectionValue($arProp, $val, $relField)
	{
		if($val)
		{
			$selectField = 'NAME';
			if($relField)
			{
				$selectField = $relField;
			}
			if(!isset($this->propVals[$arProp['ID']][$selectField][$val]))
			{
				$arFilter = array("ID"=>$val);
				if($arProp['LINK_IBLOCK_ID']) $arFilter['IBLOCK_ID'] = $arProp['LINK_IBLOCK_ID'];
				$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array($selectField));
				if($arSect = $dbRes->GetNext())
				{
					$this->propVals[$arProp['ID']][$selectField][$val] = $arSect[$selectField];
				}
				else
				{
					$this->propVals[$arProp['ID']][$selectField][$val] = '';
				}
			}
			$val = $this->propVals[$arProp['ID']][$selectField][$val];
		}
		return $val;
	}
	
	public function GetFileValue($val, $key=false)
	{
		if($val)
		{
			if(is_numeric($val))
			{
				$arFile = CKDAExportUtils::GetFileArray($val);
				if($arFile)
				{
					$val = $arFile['SRC'];
				}
				else
				{
					$val = '';
				}
			}
			
			if($this->params['EXPORT_FILES_IN_ARCHIVE']=='Y' && strlen($this->params['FILES_ARCHIVE_PATH']) > 0 && strlen($val) > 0 && file_exists($this->docRoot.$val))
			{
				if($key!==false && !empty($this->fparamsByName[$this->listIndex][$key]['CONVERSION']))
				{
					$this->filesForMove[] = array('path'=>$val, 'conv'=>$this->fparamsByName[$this->listIndex][$key]['CONVERSION']);
				}
				else
				{
					$this->PutFileToArchive($val);
				}
			}
		}
		return $val;
	}
	
	public function ProcessMoveFiles($arElementData)
	{
		if(empty($this->filesForMove)) return;
		$parentDir = $this->tmpdir.'tmpimages/';
		foreach($this->filesForMove as $arFile)
		{
			$newPath = $this->ApplyConversions($arFile['path'], $arFile['conv'], $arElementData);
			$newPath = trim(trim(preg_replace('/[\x01-\x1F'.preg_quote("\\:*?\"'<>|~#&;", "/").']+/', '', $newPath)), '/');
			if(preg_match('/^\s*https?:\/\//i', $newPath)) continue;
			$newPath = $parentDir.$newPath;
			$io = CBXVirtualIo::GetInstance();
			$io->copy($this->docRoot.$arFile['path'], $newPath);
			$this->PutFileToArchive(substr($newPath, strlen($this->docRoot)), $parentDir);
		}
		DeleteDirFilesEx(substr($parentDir, strlen($this->docRoot)));
		$this->filesForMove = array();
	}
	
	public function PutFileToArchive($val, $removePath='')
	{
		if($this->stepparams['curstep'] != 'export') return;
		$zipFile = '';
		$suffix = 0;
		$archivePath = $this->docRoot. preg_replace('/\.zip\s*$/U', '', '/'.ltrim($this->params['FILES_ARCHIVE_PATH'], '/'));
		while(strlen($zipFile)==0 || (file_exists($zipFile) && filesize($zipFile)>1024*1024*100))
		{
			$zipFile = $archivePath.($suffix > 0 ? '_'.$suffix : '').'.zip';
			$suffix++;
		}
		$siteEncoding = CKDAExportUtils::getSiteEncoding();
		$fsEncoding = CKDAExportUtils::getfileSystemEncoding();
		
		if(class_exists('ZipArchive') && ($zipObj = new ZipArchive()) && $zipObj->open($zipFile, ZipArchive::CREATE)===true)
		{
			$f1 = $this->docRoot.$val;
			if($siteEncoding!=$fsEncoding) $f1 = \Bitrix\Main\Text\Encoding::convertEncoding($f1, $siteEncoding, $fsEncoding);
			$f2 = \Bitrix\Main\Text\Encoding::convertEncoding(ltrim($val, '/'), $siteEncoding, 'cp866');
			if(strlen($removePath) > 0) $f2 = \Bitrix\Main\Text\Encoding::convertEncoding(ltrim(substr($this->docRoot.$val, strlen($removePath)), '/'), $siteEncoding, 'cp866');
			$zipObj->addFile($f1, $f2);
			$zipObj->close();
		}
		else
		{
			$zipObj = \CBXArchive::GetArchive($zipFile, 'ZIP');
			$zipObj->Add($this->docRoot.$val, array("add_path" => false, "remove_path" => (strlen($removePath) > 0 ? $removePath : $this->docRoot.'/')));
		}
	}
	
	public function GetFileDescription($val)
	{
		if($val)
		{
			$arFile = CKDAExportUtils::GetFileArray($val);
			if($arFile)
			{
				$val = $arFile['DESCRIPTION'];
			}
			else
			{
				$val = '';
			}
		}
		return $val;
	}
	
	public function GetHighloadBlockValue($arProp, $val)
	{
		if($val && CModule::IncludeModule('highloadblock') && $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				if(!$this->hlbl[$arProp['ID']])
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
					$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
					$this->hlbl[$arProp['ID']] = $entity->getDataClass();
				}
				$entityDataClass = $this->hlbl[$arProp['ID']];
				
				$dbRes2 = $entityDataClass::GetList(array('filter'=>array("UF_XML_ID"=>$val), 'select'=>array('ID', 'UF_NAME'), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arr2['UF_NAME'];
				}
				else
				{
					$this->propVals[$arProp['ID']][$val] = '';
				}
			}
			return $this->propVals[$arProp['ID']][$val];
		}
		return $val;
	}
	
	public function GetHTMLValue($arProp, $val)
	{
		if(isset($val['TEXT'])) return $val['TEXT'];
		else return $val;
	}
	
	public function PictureProcessing($arFile, $arDef)
	{
		if($arDef["SCALE"] === "Y")
		{
			$arNewPicture = CIBlock::ResizePicture($arFile, $arDef);
			if(is_array($arNewPicture))
			{
				$arFile = $arNewPicture;
			}
			/*elseif($arDef["IGNORE_ERRORS"] !== "Y")
			{
				unset($arFile);
				$strWarning .= GetMessage("IBLOCK_FIELD_PREVIEW_PICTURE").": ".$arNewPicture."<br>";
			}*/
		}

		if($arDef["USE_WATERMARK_FILE"] === "Y")
		{
			CIBLock::FilterPicture($arFile["tmp_name"], array(
				"name" => "watermark",
				"position" => $arDef["WATERMARK_FILE_POSITION"],
				"type" => "file",
				"size" => "real",
				"alpha_level" => 100 - min(max($arDef["WATERMARK_FILE_ALPHA"], 0), 100),
				"file" => $this->docRoot.Rel2Abs("/", $arDef["WATERMARK_FILE"]),
			));
		}

		if($arDef["USE_WATERMARK_TEXT"] === "Y")
		{
			CIBLock::FilterPicture($arFile["tmp_name"], array(
				"name" => "watermark",
				"position" => $arDef["WATERMARK_TEXT_POSITION"],
				"type" => "text",
				"coefficient" => $arDef["WATERMARK_TEXT_SIZE"],
				"text" => $arDef["WATERMARK_TEXT"],
				"font" => $this->docRoot.Rel2Abs("/", $arDef["WATERMARK_TEXT_FONT"]),
				"color" => $arDef["WATERMARK_TEXT_COLOR"],
			));
		}
		return $arFile;
	}
	
	public function GetIblockSite($IBLOCK_ID, $one=false)
	{
		if(!isset($this->arIblockSites)) $this->arIblockSites = array();
		if(!$this->arIblockSites[$IBLOCK_ID])
		{
			/*$dbRes = CIBlock::GetList(array(), array('ID'=>$IBLOCK_ID));
			$arIblock = $dbRes->Fetch();
			$this->arIblockSites[$IBLOCK_ID] = $arIblock['LID'];*/
			$arSiteList = array();
			$rsIBlockSites = CIBlock::GetSite($IBLOCK_ID);
			while ($arIBlockSite = $rsIBlockSites->Fetch())
			{
				$arSiteList[] = $arIBlockSite['SITE_ID'];
			}
			if(count($arSiteList)==0) $arSiteList[] = '';
			$this->arIblockSites[$IBLOCK_ID] = $arSiteList;
		}
		if($one) return $this->arIblockSites[$IBLOCK_ID][0];
		else return $this->arIblockSites[$IBLOCK_ID];
	}
	
	public function ConversionReplaceValues($m)
	{
		$k = substr($m[0], 1, -1);		
		if(1 || isset($this->currentItemValues[$k]))
		{
			$val = $this->GetValueForConversion($this->currentItemValues[$k]);
			if(is_array($val)) $val = implode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $val);
			if(preg_match('/^(OFFER_)?(PURCHASING_PRICE|ICAT_PRICE\d+_PRICE(_DISCOUNT)?)$/', $this->currentFieldName)
				&& preg_match('/^(OFFER_)?(PURCHASING_PRICE|ICAT_PRICE\d+_PRICE(_DISCOUNT)?)$/', $k))
			{
				$currKey = preg_replace('/_PRICE(_DISCOUNT)?$/', '_CURRENCY', $k);
				$val = $this->GetConvertedPrice($val, $this->currentItemValues[$currKey], $this->currentFieldName);
			}
			return $val;
		}
		return $m[0];
	}
	
	public function ApplyConversions($val, $arConv, $arItem, $field=false, $iblockFields=array())
	{
		$fieldName = $fieldKey = false;
		if(!is_array($field))
		{
			$fieldName = $field;
		}
		else
		{
			if($field['NAME']) $fieldName = $field['NAME'];
			if($field['KEY']) $fieldKey = $field['KEY'];
		}
		
		if(is_array($arConv))
		{
			$execConv = false;
			$this->currentItemValues = $arItem;
			foreach($arConv as $k=>$v)
			{
				$condVal = $val;
				if(strlen($v['CELL']) > 0 && !in_array($v['CELL'], array('ELSE')))
				{
					$condVal = $this->GetValueForConversion($arItem[$v['CELL']]);
				}
				if(is_array($condVal)) $condVal = implode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $condVal);
				if(strlen($v['FROM']) > 0) $v['FROM'] = preg_replace_callback('/(#[A-Za-z0-9\_]+#)/', array($this, 'ConversionReplaceValues'), $v['FROM']);
				if($v['CELL']=='ELSE') $v['WHEN'] = '';
				if(($v['CELL']=='ELSE' && !$execConv)
					|| ($v['WHEN']=='EQ' && ($condVal==$v['FROM'] && strlen($condVal)==strlen($v['FROM'])))
					|| ($v['WHEN']=='NEQ' && ($condVal!=$v['FROM'] || strlen($condVal)!=strlen($v['FROM'])))
					|| ($v['WHEN']=='GT' && $condVal > $v['FROM'])
					|| ($v['WHEN']=='LT' && $condVal < $v['FROM'])
					|| ($v['WHEN']=='GEQ' && $condVal >= $v['FROM'])
					|| ($v['WHEN']=='LEQ' && $condVal <= $v['FROM'])
					|| ($v['WHEN']=='CONTAIN' && strpos($condVal, $v['FROM'])!==false)
					|| ($v['WHEN']=='NOT_CONTAIN' && strpos($condVal, $v['FROM'])===false)
					|| ($v['WHEN']=='REGEXP' && preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
					|| ($v['WHEN']=='NOT_REGEXP' && !preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
					|| ($v['WHEN']=='EMPTY' && strlen($condVal)==0)
					|| ($v['WHEN']=='NOT_EMPTY' && strlen($condVal) > 0)
					|| ($v['WHEN']=='ANY'))
				{
					$this->currentFieldKey = $fieldKey;
					$this->currentFieldName = $fieldName;
					if(strlen($v['TO']) > 0) $v['TO'] = preg_replace_callback('/(#[A-Za-z0-9\_]+#)/', array($this, 'ConversionReplaceValues'), $v['TO']);
					if($v['THEN']=='REPLACE_TO') $val = $v['TO'];
					elseif($v['THEN']=='REMOVE_SUBSTRING' && strlen($v['TO']) > 0) $val = str_replace($v['TO'], '', $val);
					elseif($v['THEN']=='REPLACE_SUBSTRING_TO' && strlen($v['FROM']) > 0) $val = str_replace($v['FROM'], $v['TO'], $val);
					elseif($v['THEN']=='ADD_TO_BEGIN') $val = $v['TO'].$val;
					elseif($v['THEN']=='ADD_TO_END') $val = $val.$v['TO'];
					elseif($v['THEN']=='MATH_ROUND') $val = round(doubleval(str_replace(',', '.', $val)));
					elseif($v['THEN']=='MATH_MULTIPLY') $val = doubleval(str_replace(',', '.', $val)) * doubleval(str_replace(',', '.', $v['TO']));
					elseif($v['THEN']=='MATH_DIVIDE') $val = doubleval(str_replace(',', '.', $val)) / doubleval(str_replace(',', '.', $v['TO']));
					elseif($v['THEN']=='MATH_ADD') $val = doubleval(str_replace(',', '.', $val)) + doubleval(str_replace(',', '.', $v['TO']));
					elseif($v['THEN']=='MATH_SUBTRACT') $val = doubleval(str_replace(',', '.', $val)) - doubleval(str_replace(',', '.', $v['TO']));
					elseif($v['THEN']=='NOT_LOAD') $val = false;
					elseif($v['THEN']=='EXPRESSION') $val = $this->ExecuteFilterExpression($val, $v['TO'], '');
					elseif($v['THEN']=='STRIP_TAGS') $val = strip_tags($val);
					elseif($v['THEN']=='CLEAR_TAGS') $val = preg_replace('/<([a-z][a-z0-9:]*)[^>]*(\/?)>/i','<$1$2>', $val);
					elseif($v['THEN']=='ADD_LINK') $val = '<a class="kda-ee-conversion-link" href="'.$v['TO'].'">'.$val.'</a>';
					elseif($v['THEN']=='TRANSLIT')
					{
						$arParams = array();
						if($fieldName && !empty($iblockFields))
						{
							$paramName = '';
							if($fieldName=='IE_CODE') $paramName = 'CODE';
							if(preg_match('/^ISECT\d+_CODE$/', $fieldName)) $paramName = 'SECTION_CODE';
							if($paramName && $iblockFields[$paramName]['DEFAULT_VALUE']['TRANSLITERATION']=='Y')
							{
								$arParams = $iblockFields['SECTION_CODE']['DEFAULT_VALUE'];
							}
						}
						$val = $this->Str2Url($val, $arParams);
					}
					$execConv = true;
				}
			}
		}
		return $val;
	}
	
	public function GetValueForConversion($val)
	{
		if(is_array($val) && array_key_exists('TYPE', $val))
		{
			unset($val['TYPE']);
			if(count($val) > 0)
			{
				reset($val);
				return current($val);
			}
			else return '';
		}
		return $val;
	}
	
	public function GetExportData($listIndex, $limit=10, $page=1, $sectionKey=1)
	{
		$this->listIndex = $listIndex;
		$this->sectionKey = $sectionKey;
		if(isset($this->stepparams['string_lengths']) && !isset($this->stepparams['string_lengths'][$listIndex]))
		{
			$this->stepparams['string_lengths'][$listIndex] = array();
		}

		$iblockId = $this->params['IBLOCK_ID'];
		$changeIblockId = (bool)($this->params['CHANGE_IBLOCK_ID'][$listIndex]=='Y');
		if($changeIblockId && $this->params['LIST_IBLOCK_ID'][$listIndex])
		{
			$iblockId = $this->params['LIST_IBLOCK_ID'][$listIndex];
		}
		$boolSKU = false;
		if($arCatalog = CKDAExportUtils::GetOfferIblock($iblockId, true))
		{
			$offersIblockId = $arCatalog['OFFERS_IBLOCK_ID'];
			$offersPropId = $arCatalog['OFFERS_PROPERTY_ID'];
			$boolSKU = true;
		}
		
		if(!isset($this->filters)) $this->filters = array();
		if(!isset($this->skuFilters)) $this->filters = array();
		if(!isset($this->filters[$listIndex]))
		{
			$arFilter = array(
				'IBLOCK_ID' => $iblockId
			);
			if($this->params['EXPORT_SEP_SECTIONS']=='Y')
			{
				$arFilter['!SECTION_ID'] = false;
				$arFilter['INCLUDE_SUBSECTIONS'] = 'N';
			}
			if($this->params['FILTER'][$listIndex])
			{
				if(!isset($this->filterProps)) $this->filterProps = array();
				if(!isset($this->filterProps[$iblockId]))
				{
					$dbrFProps = CIBlockProperty::GetList(
						array(
							"SORT"=>"ASC",
							"NAME"=>"ASC"
						),
						array(
							"IBLOCK_ID"=>$iblockId,
							"CHECK_PERMISSIONS"=>"N",
						)
					);
					
					$arProps = array();
					while ($arProp = $dbrFProps->GetNext())
					{
						if ($arProp["ACTIVE"] == "Y")
						{
							$arProp["PROPERTY_USER_TYPE"] = ('' != $arProp["USER_TYPE"] ? CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
							$arProps[] = $arProp;
						}
					}
					$this->filterProps[$iblockId] = $arProps;
				}
				else
				{
					$arProps = $this->filterProps[$iblockId];
				}
				
				if($boolSKU)
				{
					if(!isset($this->filterProps[$offersIblockId]))
					{
						$dbrFProps = CIBlockProperty::GetList(
							array(
								"SORT"=>"ASC",
								"NAME"=>"ASC"
							),
							array(
								"IBLOCK_ID"=>$offersIblockId,
								"CHECK_PERMISSIONS"=>"N",
							)
						);
						
						$arSKUProps = array();
						while ($arProp = $dbrFProps->GetNext())
						{
							if ($arProp["ACTIVE"] == "Y")
							{
								$arProp["PROPERTY_USER_TYPE"] = ('' != $arProp["USER_TYPE"] ? CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
								$arSKUProps[] = $arProp;
							}
						}
						$this->filterProps[$offersIblockId] = $arSKUProps;
					}
					else
					{
						$arSKUProps = $this->filterProps[$offersIblockId];
					}
				}
				
				$arAddFilter = $this->params['FILTER'][$listIndex];
				
				$arSkuFilter = array();
				if ($boolSKU)
				{
					$arSkuFilter = array("IBLOCK_ID" => $offersIblockId);
					if(!empty($arProductIds)) $arSkuFilter['ID'] = $arProductIds;
					if(!empty($arAddFilter['find_sub_el_id_start'])) $arSkuFilter[">=ID"] = $arAddFilter['find_sub_el_id_start'];
					if(!empty($arAddFilter['find_sub_el_id_end'])) $arSkuFilter["<=ID"] = $arAddFilter['find_sub_el_id_end'];
					if(strlen($arAddFilter['find_sub_el_active']) > 0) $arSkuFilter['ACTIVE'] = $arAddFilter['find_sub_el_active'];
					if(strlen(trim($arAddFilter['find_sub_el_sort'])) > 0)
					{
						$op = $this->GetNumberOperation($arAddFilter['find_sub_el_sort'], $arAddFilter['find_sub_el_sort_comp']);
						$arSkuFilter[$op.'SORT'] = $arAddFilter['find_sub_el_sort'];
					}
					$this->AddDateFilter($arSkuFilter, $arAddFilter, 'DATE_MODIFY_FROM', 'DATE_MODIFY_TO', 'find_sub_el_timestamp');
					
					if(strlen($arAddFilter['find_sub_el_catalog_quantity']) > 0)
					{
						$op = $this->GetNumberOperation($arAddFilter['find_sub_el_catalog_quantity'], $arAddFilter['find_sub_el_catalog_quantity_comp']);
						$arSkuFilter[$op.'CATALOG_QUANTITY'] = $arAddFilter['find_sub_el_catalog_quantity'];
					}
					if(strlen($arAddFilter['find_sub_el_catalog_purchasing_price']) > 0)
					{
						$op = $this->GetNumberOperation($arAddFilter['find_sub_el_catalog_purchasing_price'], $arAddFilter['find_sub_el_catalog_purchasing_price_comp']);
						$arSkuFilter[$op.'CATALOG_PURCHASING_PRICE'] = $arAddFilter['find_sub_el_catalog_purchasing_price'];
					}
					
					$arStoreKeys = preg_grep('/^find_sub_el_catalog_store\d+_/', array_keys($arAddFilter));
					$arStoreKeys = array_unique(array_map(create_function('$n', 'return preg_replace("/^find_sub_el_catalog_store(\d+)_.*$/", "$1", $n);'), $arStoreKeys));
					if(!empty($arStoreKeys))
					{
						foreach($arStoreKeys as $storeKey)
						{
							if(strlen($arAddFilter['find_sub_el_catalog_store'.$storeKey.'_quantity']) > 0)
							{
								$op = $this->GetNumberOperation($arAddFilter['find_sub_el_catalog_store'.$storeKey.'_quantity'], $arAddFilter['find_sub_el_catalog_store'.$storeKey.'_quantity_comp']);
								$arSkuFilter[$op.'CATALOG_STORE_AMOUNT_'.$storeKey] = $arAddFilter['find_sub_el_catalog_store'.$storeKey.'_quantity'];
							}
						}
					}
					
					if(strlen($arAddFilter['find_sub_el_catalog_store_any_quantity']) > 0 && is_array($arAddFilter['find_sub_el_catalog_store_any_quantity_stores']) && count($arAddFilter['find_sub_el_catalog_store_any_quantity_stores']) > 0)
					{
						$op = $this->GetNumberOperation($arAddFilter['find_sub_el_catalog_store_any_quantity'], $arAddFilter['find_sub_el_catalog_store_any_quantity_comp']);
						$arFilterItem = array('LOGIC'=>'OR');
						foreach($arAddFilter['find_sub_el_catalog_store_any_quantity_stores'] as $storeKey)
						{
							$arFilterItem[] = array($op.'CATALOG_STORE_AMOUNT_'.$storeKey=>$arAddFilter['find_sub_el_catalog_store_any_quantity']);
						}
						$arSkuFilter[] = $arFilterItem;
					}
					
					$arPriceKeys = preg_grep('/^find_sub_el_catalog_price_\d+$/', array_keys($arAddFilter));
					$arPriceKeys = array_unique(array_map(create_function('$n', 'return preg_replace("/^find_sub_el_catalog_price_(\d+)$/", "$1", $n);'), $arPriceKeys));
					if(!empty($arPriceKeys))
					{
						foreach($arPriceKeys as $priceKey)
						{
							if(strlen($arAddFilter['find_sub_el_catalog_price_'.$priceKey]) > 0
								|| $arAddFilter['find_sub_el_catalog_price_'.$priceKey.'_comp']=='empty')
							{
								$op = $this->GetNumberOperation($arAddFilter['find_sub_el_catalog_price_'.$priceKey], $arAddFilter['find_sub_el_catalog_price_'.$priceKey.'_comp']);
								$arSkuFilter[$op.'CATALOG_PRICE_'.$priceKey] = $arAddFilter['find_sub_el_catalog_price_'.$priceKey];
							}
						}
					}
					
					if(isset($arSKUProps) && is_array($arSKUProps))
					{
						foreach ($arSKUProps as $arProp)
						{
							if ('Y' == $arProp["FILTRABLE"] && 'F' != $arProp["PROPERTY_TYPE"])
							{
								if (!empty($arProp['PROPERTY_USER_TYPE']) && isset($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"]))
								{
									$fieldName = "filter_".$listIndex."_find_sub_el_property_".$arProp["ID"];
									$GLOBALS[$fieldName] = $arAddFilter["find_sub_el_property_".$arProp["ID"]];
									$GLOBALS['set_filter'] = 'Y';
									call_user_func_array($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
										$arProp,
										array("VALUE" => $fieldName),
										&$arSkuFilter,
										&$filtered,
									));
								}
								else
								{
									$value = $arAddFilter["find_sub_el_property_".$arProp["ID"]];
									if(is_array($value)) $value = array_diff(array_map('trim', $value), array(''));
									if((is_array($value) && count($value)>0) || (!is_array($value) && strlen($value)))
									{
										if(is_array($value))
										{
											foreach($value as $k=>$v)
											{
												if($v === "NOT_REF") $value[$k] = false;
											}
										}
										elseif($value === "NOT_REF") $value = false;
										if($arProp["PROPERTY_TYPE"]=='E' && $arProp["USER_TYPE"]=='')
										{
											$value = trim($value);
											if(preg_match('/[,;\s\|]/', $value)) $arSkuFilter["PROPERTY_".$arProp["ID"]] = array_diff(array_map('trim', preg_split('/[,;\s\|]/', $value)), array(''));
											else $arSkuFilter["PROPERTY_".$arProp["ID"]] = $value;
										}
										elseif($arProp["PROPERTY_TYPE"]=='N' && $arProp["USER_TYPE"]=='')
										{
											$value = trim($value);
											$op = $this->GetNumberOperation($value, $arAddFilter["find_sub_el_property_".$arProp["ID"]."_comp"]);
											$arSkuFilter[$op.'PROPERTY_'.$arProp["ID"]] = $value;
										}
										else
										{
											$op = $this->GetStringOperation($value, $arAddFilter["find_sub_el_property_".$arProp["ID"]."_comp"]);
											$arSkuFilter[$op."PROPERTY_".$arProp["ID"]] = $value;
										}
									}
								}
							}
						}
					}
				}
				
				$arProductIds = array();
				if(strlen($arAddFilter['find_el_sale_order']) > 0 && CModule::IncludeModule('sale'))
				{
					$arOrders = array_diff(preg_split('/\D+/', $arAddFilter['find_el_sale_order']), array(''));
					if(!empty($arOrders))
					{
						$this->arFilterOrders = $arOrders;
						$dbRes = CSaleBasket::GetList(array(), array('ORDER_ID'=>$arOrders), array('PRODUCT_ID'), false, array('PRODUCT_ID'));
						while($arr = $dbRes->Fetch())
						{
							$arProductIds[] = $arr['PRODUCT_ID'];
						}
						if(!empty($arProductIds))
						{
							$arFilter['ID'] = $arProductIds;
						}
					}
				}
				
				if(is_array($arAddFilter['find_section_section']) && count(array_diff($arAddFilter['find_section_section'], array('','-1'))) > 0) 
					$arFilter['SECTION_ID'] = array_diff($arAddFilter['find_section_section'], array('', '-1'));
				elseif(strlen($arAddFilter['find_section_section']) > 0 && (int)$arAddFilter['find_section_section'] >= 0) 
					$arFilter['SECTION_ID'] = $arAddFilter['find_section_section'];
				if($arAddFilter['find_el_subsections']=='Y')
				{
					if($arFilter['SECTION_ID']==0) unset($arFilter["SECTION_ID"]);
					else $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
				}
				if(strlen($arAddFilter['find_el_modified_user_id']) > 0) $arFilter['MODIFIED_USER_ID'] = $arAddFilter['find_el_modified_user_id'];
				if(strlen($arAddFilter['find_el_modified_by']) > 0) $arFilter['MODIFIED_BY'] = $arAddFilter['find_el_modified_by'];
				if(strlen($arAddFilter['find_el_created_user_id']) > 0) $arFilter['CREATED_USER_ID'] = $arAddFilter['find_el_created_user_id'];
				if(strlen($arAddFilter['find_el_active']) > 0) $arFilter['ACTIVE'] = $arAddFilter['find_el_active'];
				if(strlen(trim($arAddFilter['find_el_sort'])) > 0)
				{
					$op = $this->GetNumberOperation($arAddFilter['find_el_sort'], $arAddFilter['find_el_sort_comp']);
					$arFilter[$op.'SORT'] = $arAddFilter['find_el_sort'];
				}
				if(strlen($arAddFilter['find_el_code']) > 0) $arFilter['?CODE'] = $arAddFilter['find_el_code'];
				if(strlen($arAddFilter['find_el_external_id']) > 0) $arFilter['EXTERNAL_ID'] = $arAddFilter['find_el_external_id'];
				if(strlen($arAddFilter['find_el_tags']) > 0) $arFilter['?TAGS'] = $arAddFilter['find_el_tags'];
				if(strlen($arAddFilter['find_el_name']) > 0) $arFilter['?NAME'] = $arAddFilter['find_el_name'];
				if(strlen($arAddFilter['find_el_vtype_pretext']) > 0)
				{
					if($arAddFilter['find_el_vtype_pretext']=='empty') $arFilter['PREVIEW_TEXT'] = false;
					elseif($arAddFilter['find_el_vtype_pretext']=='not_empty') $arFilter['!PREVIEW_TEXT'] = false;
				}
				elseif(strlen($arAddFilter['find_el_pretext']) > 0) $arFilter['?PREVIEW_TEXT'] = $arAddFilter['find_el_pretext'];
				if(strlen($arAddFilter['find_el_vtype_intext']) > 0)
				{
					if($arAddFilter['find_el_vtype_intext']=='empty') $arFilter['DETAIL_TEXT'] = false;
					elseif($arAddFilter['find_el_vtype_intext']=='not_empty') $arFilter['!DETAIL_TEXT'] = false;
				}
				elseif(strlen($arAddFilter['find_el_intext']) > 0) $arFilter['?DETAIL_TEXT'] = $arAddFilter['find_el_intext'];
				if($arAddFilter['find_el_preview_picture']=='Y') $arFilter['!PREVIEW_PICTURE'] =  false;
				elseif($arAddFilter['find_el_preview_picture']=='N') $arFilter['PREVIEW_PICTURE'] =  false;
				if($arAddFilter['find_el_detail_picture']=='Y') $arFilter['!DETAIL_PICTURE'] =  false;
				elseif($arAddFilter['find_el_detail_picture']=='N') $arFilter['DETAIL_PICTURE'] =  false;
				
				if(!empty($arAddFilter['find_el_id_start'])) $arFilter[">=ID"] = $arAddFilter['find_el_id_start'];
				if(!empty($arAddFilter['find_el_id_end'])) $arFilter["<=ID"] = $arAddFilter['find_el_id_end'];
				$this->AddDateFilter($arFilter, $arAddFilter, 'DATE_MODIFY_FROM', 'DATE_MODIFY_TO', 'find_el_timestamp');
				$this->AddDateFilter($arFilter, $arAddFilter, '>=DATE_CREATE', '<=DATE_CREATE', 'find_el_created');
				if(!empty($arAddFilter['find_el_created_by']) && strlen($arAddFilter['find_el_created_by'])>0) $arFilter["CREATED_BY"] = $arAddFilter['find_el_created_by'];
				if($arAddFilter['find_el_vtype_active_from']=='empty') $arFilter["DATE_ACTIVE_FROM"] = false;
				elseif($arAddFilter['find_el_vtype_active_from']=='not_empty') $arFilter["!DATE_ACTIVE_FROM"] = false;
				else
				{
					if(!empty($arAddFilter['find_el_date_active_from_from'])) $arFilter[">=DATE_ACTIVE_FROM"] = $arAddFilter['find_el_date_active_from_from'];
					if(!empty($arAddFilter['find_el_date_active_from_to'])) $arFilter["<=DATE_ACTIVE_FROM"] = $arAddFilter['find_el_date_active_from_to'];
				}
				if($arAddFilter['find_el_vtype_date_active_to']=='empty') $arFilter["DATE_ACTIVE_TO"] = false;
				elseif($arAddFilter['find_el_vtype_date_active_to']=='not_empty') $arFilter["!DATE_ACTIVE_TO"] = false;
				else
				{
					if(!empty($arAddFilter['find_el_date_active_to_from'])) $arFilter[">=DATE_ACTIVE_TO"] = $arAddFilter['find_el_date_active_to_from'];
					if(!empty($arAddFilter['find_el_date_active_to_to'])) $arFilter["<=DATE_ACTIVE_TO"] = $arAddFilter['find_el_date_active_to_to'];
				}
				if (!empty($arAddFilter['find_el_catalog_type'])) $arFilter['CATALOG_TYPE'] = $arAddFilter['find_el_catalog_type'];
				if (!empty($arAddFilter['find_el_catalog_available'])) $arFilter['CATALOG_AVAILABLE'] = $arAddFilter['find_el_catalog_available'];
				if (!empty($arAddFilter['find_el_catalog_bundle'])) $arFilter['CATALOG_BUNDLE'] = $arAddFilter['find_el_catalog_bundle'];
				if (strlen($arAddFilter['find_el_catalog_quantity']) > 0)
				{
					$op = $this->GetNumberOperation($arAddFilter['find_el_catalog_quantity'], $arAddFilter['find_el_catalog_quantity_comp']);
					if(!empty($arSkuFilter) && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
					{
						$arFilter[] = array('LOGIC'=>'OR',
							array('CATALOG_TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU),
							array($op.'CATALOG_QUANTITY'=>$arAddFilter['find_el_catalog_quantity'])
						);
						if(!isset($arFilter['CATALOG_TYPE'])) $arFilter['CATALOG_TYPE'] = array(1,2,3);
					}
					else
					{
						$arFilter[$op.'CATALOG_QUANTITY'] = $arAddFilter['find_el_catalog_quantity'];
					}
				}
				if (strlen($arAddFilter['find_el_catalog_purchasing_price']) > 0)
				{
					$op = $this->GetNumberOperation($arAddFilter['find_el_catalog_purchasing_price'], $arAddFilter['find_el_catalog_purchasing_price_comp']);
					if(!empty($arSkuFilter) && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
					{
						$arFilter[] = array('LOGIC'=>'OR',
							array('CATALOG_TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU),
							array($op.'CATALOG_PURCHASING_PRICE'=>$arAddFilter['find_el_catalog_purchasing_price'])
						);
						if(!isset($arFilter['CATALOG_TYPE'])) $arFilter['CATALOG_TYPE'] = array(1,2,3);
					}
					else
					{
						$arFilter[$op.'CATALOG_PURCHASING_PRICE'] = $arAddFilter['find_el_catalog_purchasing_price'];
					}
				}
				
				$arStoreKeys = preg_grep('/^find_el_catalog_store\d+_/', array_keys($arAddFilter));
				$arStoreKeys = array_unique(array_map(create_function('$n', 'return preg_replace("/^find_el_catalog_store(\d+)_.*$/", "$1", $n);'), $arStoreKeys));
				if(!empty($arStoreKeys))
				{
					foreach($arStoreKeys as $storeKey)
					{
						if(strlen($arAddFilter['find_el_catalog_store'.$storeKey.'_quantity']) > 0)
						{
							$op = $this->GetNumberOperation($arAddFilter['find_el_catalog_store'.$storeKey.'_quantity'], $arAddFilter['find_el_catalog_store'.$storeKey.'_quantity_comp']);
							if(!empty($arSkuFilter) && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
							{
								$arFilter[] = array('LOGIC'=>'OR',
									array('CATALOG_TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU),
									array($op.'CATALOG_STORE_AMOUNT_'.$storeKey=>$arAddFilter['find_el_catalog_store'.$storeKey.'_quantity'])
								);
								if(!isset($arFilter['CATALOG_TYPE'])) $arFilter['CATALOG_TYPE'] = array(1,2,3);
							}
							else
							{
								$arFilter[$op.'CATALOG_STORE_AMOUNT_'.$storeKey] = $arAddFilter['find_el_catalog_store'.$storeKey.'_quantity'];
							}
						}
					}
				}
				
				if(strlen($arAddFilter['find_el_catalog_store_any_quantity']) > 0 && is_array($arAddFilter['find_el_catalog_store_any_quantity_stores']) && count($arAddFilter['find_el_catalog_store_any_quantity_stores']) > 0)
				{
					$op = $this->GetNumberOperation($arAddFilter['find_el_catalog_store_any_quantity'], $arAddFilter['find_el_catalog_store_any_quantity_comp']);
					$arFilterItem = array('LOGIC'=>'OR');
					if(!empty($arSkuFilter) && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
					{
						$arFilterItem[] = array('CATALOG_TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU);
						if(!isset($arFilter['CATALOG_TYPE'])) $arFilter['CATALOG_TYPE'] = array(1,2,3);
					}
					foreach($arAddFilter['find_el_catalog_store_any_quantity_stores'] as $storeKey)
					{
						$arFilterItem[] = array($op.'CATALOG_STORE_AMOUNT_'.$storeKey=>$arAddFilter['find_el_catalog_store_any_quantity']);
					}
					$arFilter[] = $arFilterItem;
				}
				
				$arPriceKeys = preg_grep('/^find_el_catalog_price_\d+$/', array_keys($arAddFilter));
				$arPriceKeys = array_unique(array_map(create_function('$n', 'return preg_replace("/^find_el_catalog_price_(\d+)$/", "$1", $n);'), $arPriceKeys));
				if(!empty($arPriceKeys))
				{
					foreach($arPriceKeys as $priceKey)
					{
						if(strlen($arAddFilter['find_el_catalog_price_'.$priceKey]) > 0
							|| $arAddFilter['find_el_catalog_price_'.$priceKey.'_comp']=='empty')
						{
							$op = $this->GetNumberOperation($arAddFilter['find_el_catalog_price_'.$priceKey], $arAddFilter['find_el_catalog_price_'.$priceKey.'_comp']);
							if(!empty($arSkuFilter) && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
							{
								$arFilter[] = array('LOGIC'=>'OR',
									array('CATALOG_TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU),
									array($op.'CATALOG_PRICE_'.$priceKey=>$arAddFilter['find_el_catalog_price_'.$priceKey])
								);
								if(!isset($arFilter['CATALOG_TYPE'])) $arFilter['CATALOG_TYPE'] = array(1,2,3);
							}
							else
							{
								$arFilter[$op.'CATALOG_PRICE_'.$priceKey] = $arAddFilter['find_el_catalog_price_'.$priceKey];
							}
						}
					}
				}
				
				foreach ($arProps as $arProp)
				{
					if ($arProp["FILTRABLE"]=='Y' || $arProp["PROPERTY_TYPE"]=='F')
					{
						if (!empty($arProp['PROPERTY_USER_TYPE']) && isset($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"]))
						{
							$fieldName = "filter_".$listIndex."_find_el_property_".$arProp["ID"];
							$GLOBALS[$fieldName] = $arAddFilter["find_el_property_".$arProp["ID"]];
							$GLOBALS['set_filter'] = 'Y';
							call_user_func_array($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
								$arProp,
								array("VALUE" => $fieldName),
								&$arFilter,
								&$filtered,
							));
						}
						else
						{
							$value = $arAddFilter["find_el_property_".$arProp["ID"]];
							if(is_array($value)) $value = array_diff(array_map('trim', $value), array(''));
							if((is_array($value) && count($value)>0) || (!is_array($value) && strlen($value)))
							{
								if(is_array($value))
								{
									foreach($value as $k=>$v)
									{
										if($v === "NOT_REF") $value[$k] = false;
									}
								}
								elseif($value === "NOT_REF") $value = false;
								if($arProp["PROPERTY_TYPE"]=='E' && $arProp["USER_TYPE"]=='')
								{
									$value = trim($value);
									if(preg_match('/[,;\s\|]/', $value)) $arFilter["PROPERTY_".$arProp["ID"]] = array_diff(array_map('trim', preg_split('/[,;\s\|]/', $value)), array(''));
									else $arFilter["PROPERTY_".$arProp["ID"]] = $value;
								}
								elseif($arProp["PROPERTY_TYPE"]=='N' && $arProp["USER_TYPE"]=='')
								{
									$value = trim($value);
									$op = $this->GetNumberOperation($value, $arAddFilter["find_el_property_".$arProp["ID"]."_comp"]);
									$arFilter[$op.'PROPERTY_'.$arProp["ID"]] = $value;
								}
								elseif($arProp["PROPERTY_TYPE"]=='F')
								{
									if($arAddFilter['find_el_property_'.$arProp["ID"]]=='Y') $arFilter['!PROPERTY_'.$arProp["ID"]] =  false;
									elseif($arAddFilter['find_el_property_'.$arProp["ID"]]=='N') $arFilter['PROPERTY_'.$arProp["ID"]] =  false;
								}
								else
								{
									$op = $this->GetStringOperation($value, $arAddFilter["find_el_property_".$arProp["ID"]."_comp"]);
									$arFilter[$op."PROPERTY_".$arProp["ID"]] = $value;
								}
							}
						}
					}
				}
			}
			foreach(GetModuleEvents(static::$moduleId, "OnBeforeSaveFilter", true) as $arEvent)
			{
				ExecuteModuleEventEx($arEvent, array(&$arFilter, &$arSkuFilter, $this->pid, $listIndex));
			}
			$this->filters[$listIndex] = $arFilter;
			$this->skuFilters[$listIndex] = $arSkuFilter;
		}
		else
		{
			$arFilter = $this->filters[$listIndex];
			$arSkuFilter = $this->skuFilters[$listIndex];
		}

		$arFields = $this->GetFieldList($listIndex);		
	
		$this->customFieldSettings = array();
		$this->arPricesGroup = array();
		$this->arPropListProps = array();
		$arFieldsAdded = array();
		if(is_array($this->fparams[$listIndex]))
		{
			foreach($this->fparams[$listIndex] as $fieldIndex=>$arSettings)
			{
				$field = $arFields[$fieldIndex];
				$this->customFieldSettings[$field] = $arSettings;
				if($field=='IP_LIST_PROPS' || $field=='OFFER_IP_LIST_PROPS')
				{
					if(is_array($arSettings['PROPLIST_PROPS_LIST']))
					{
						foreach($arSettings['PROPLIST_PROPS_LIST'] as $k=>$v)
						{
							$v = (int)$v;
							if($v > 0 && !in_array($v, $this->arPropListProps)) $this->arPropListProps[] = $v;
						}
					}
				}
				if(preg_match('/^(OFFER_)?ICAT_PRICE(\d+)_PRICE_DISCOUNT$/', $field, $m))
				{
					if(isset($arSettings['USER_GROUP']) && is_array($arSettings['USER_GROUP']) && !empty($arSettings['USER_GROUP']))
					{
						$userGroup = $arSettings['USER_GROUP'];
						sort($userGroup, SORT_NUMERIC);
						$ugKey = implode('_', $userGroup);
						if(!isset($this->arPricesGroup[$m[2]])) $this->arPricesGroup[$m[2]] = array();
						if(!isset($this->arPricesGroup[$m[2]][$ugKey])) $this->arPricesGroup[$m[2]][$ugKey] = $userGroup;
					}
				}
				if(isset($arSettings['REL_ELEMENT_FIELD']) && strlen($arSettings['REL_ELEMENT_FIELD']) > 0)
				{
					$fa = $arFields[$fieldIndex].'|'.$arSettings['REL_ELEMENT_FIELD'];
					if(!in_array($fa, $arFieldsAdded)) $arFieldsAdded[] = $fa;
				}
				if(isset($arSettings['REL_SECTION_FIELD']) && strlen($arSettings['REL_SECTION_FIELD']) > 0)
				{
					$fa = $arFields[$fieldIndex].'|'.$arSettings['REL_SECTION_FIELD'];
					if(!in_array($fa, $arFieldsAdded)) $arFieldsAdded[] = $fa;
				}
				if(isset($arSettings['REL_USER_FIELD']) && strlen($arSettings['REL_USER_FIELD']) > 0)
				{
					$fa = $arFields[$fieldIndex].'|'.$arSettings['REL_USER_FIELD'];
					if(!in_array($fa, $arFieldsAdded)) $arFieldsAdded[] = $fa;
				}
				if(isset($arSettings['CONVERSION']) && is_array($arSettings['CONVERSION']) && $field)
				{
					foreach($arSettings['CONVERSION'] as $k=>$v)
					{
						$arKeys = array();
						if(preg_match_all('/#([A-Za-z0-9\_]+)#/', $v['FROM'], $m)) $arKeys = array_merge($arKeys, $m[1]);
						if(preg_match_all('/#([A-Za-z0-9\_]+)#/', $v['TO'], $m)) $arKeys = array_merge($arKeys, $m[1]);
						if($v['CELL'] && !in_array($v['CELL'], array('ELSE'))) $arKeys[] = $v['CELL'];
						foreach($arKeys as $key)
						{
							if(!in_array($key, $arFields) && !in_array($key, $arFieldsAdded))
							{
								$arFieldsAdded[] = $key;
							}
						}
					}
				}
			}
		}
		
		$arAllFields = array_merge($arFields, $arFieldsAdded);

		$bOnlySections = true;
		while($bOnlySections && $arCurField = each($arAllFields))
		{
			if(!preg_match('/^ISECT(\d+)?_/', $arCurField['value']) && $arCurField['value']!='IE_SECTION_PATH' && $arCurField['value']!='')
			{
				$bOnlySections = false;
			}
		}
		
		$arOfferFields = array();
		foreach($arAllFields as $k=>$v)
		{
			if(strpos($v, 'OFFER_')===0)
			{
				$arOfferFields[$k] = substr($v, 6);
			}
		}
		
		$arNavParams = false;
		if(is_numeric($limit) && $limit > 0)
		{
			if(!empty($arOfferFields) && $limit > $this->maxReadRowsWOffers)
			{
				$limit = $this->maxReadRowsWOffers;
			}
			if($page==0)
			{
				$arNavParams = array('nTopCount' => (int)$limit);
			}
			else
			{
				$arNavParams = array(
					'nPageSize' => (int)$limit,
					'iNumPage' => $page
				);
			}
		}
		
		$arData = array();
		$arParams = array(
			'FILTER' => $arFilter,
			'SKU_FILTER' => $arSkuFilter,
			'NAV_PARAMS' => $arNavParams,
			'FIELDS' => $arAllFields,
			'TYPE' => ($bOnlySections ? 'SECTION' : 'ELEMENT'),
			'SECTION_KEY' => $sectionKey
		);
		$arResElements = $this->GetElementsData($arData, $arParams);

		$arMultiRows = array();
		foreach($arData as $k=>$arElementData)
		{
			foreach($arFields as $fname)
			{
				if(!isset($arElementData[$fname])) $arData[$k][$fname] = $arElementData[$fname] = '';
			}
			
			$arFieldSettings = array();
			if(is_array($this->fparams[$listIndex]))
			{
				foreach($this->fparams[$listIndex] as $fieldIndex=>$arSettings)
				{
					$field = $arFields[$fieldIndex];
					$arFieldSettings[$field] = $arSettings;
					$arFieldSettings[$field.'_'.$fieldIndex] = $arSettings;
					if($field=='IP_LIST_PROPS' || $field=='OFFER_IP_LIST_PROPS')
					{
						if($field=='OFFER_IP_LIST_PROPS')
						{
							$fieldPrefix = 'OFFER_';
							$arIblockProps = $this->GetIblockProperties($offersIblockId);
						}
						else
						{
							$fieldPrefix = '';
							$arIblockProps = $this->GetIblockProperties($iblockId);
						}
						$plFullVal = '';
						if(is_array($arSettings['PROPLIST_PROPS_LIST']))
						{
							$sep1 = $arSettings['PROPLIST_PROPS_SEP_VALS'];
							if(strlen(trim($sep1))==0) $sep1 = "\r\n";
							$sep2 = $arSettings['PROPLIST_PROPS_SEP_NAMEVAL'];
							if(strlen(trim($sep2))==0) $sep2 = ": ";
							$showEmpty = (bool)($arSettings['PROPLIST_PROPS_SHOW_EMPTY']=='Y');
							foreach($arSettings['PROPLIST_PROPS_LIST'] as $plKey)
							{
								$plVal = $arElementData[$fieldPrefix.'IP_PROP'.$plKey];
								if(is_array($plVal)) $plVal = implode(', ', $plVal);
								$plVal = trim($plVal);
								if(!$showEmpty && strlen($plVal)==0) continue;
								$plFullVal .= (strlen($plFullVal) > 0 ? $sep1 : '').$arIblockProps[$plKey]['NAME'].$sep2.$plVal;
							}
						}
						$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $plFullVal;
					}
					if(isset($arSettings['PRICE_CONVERT_CURRENCY']) && $arSettings['PRICE_CONVERT_CURRENCY']=='Y' && $arSettings['PRICE_CONVERT_CURRENCY_TO']!=$this->customFieldSettings[$field]['PRICE_CONVERT_CURRENCY_TO'] && isset($arElementData[$field.'_ORIG']) && strpos($field, 'PRICE')!==false)
					{
						$currencyField = preg_replace('/_PRICE(_|$)/', '_CURRENCY$1', $field);
						if(isset($arElementData[$currencyField]))
						{
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $this->GetConvertedPrice($arElementData[$field.'_ORIG'], $arElementData[$currencyField], $arSettings);
						}
					}
					if(preg_match('/^(OFFER_)?ICAT_PRICE(\d+)_PRICE_DISCOUNT$/', $field, $m))
					{
						if(isset($arSettings['USER_GROUP']) && is_array($arSettings['USER_GROUP']) && !empty($arSettings['USER_GROUP']))
						{
							$userGroup = $arSettings['USER_GROUP'];
							sort($userGroup, SORT_NUMERIC);
							$ugKey = implode('_', $userGroup);
							if(isset($arElementData[$field.'__'.$ugKey]))
							{
								$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $arElementData[$field.'__'.$ugKey];
							}
						}
					}
					if(isset($arSettings['REL_ELEMENT_FIELD']) || isset($arSettings['REL_SECTION_FIELD']) || isset($arSettings['REL_USER_FIELD']))
					{
						if(isset($arSettings['REL_ELEMENT_FIELD'])) $fieldKey = $field.'|'.$arSettings['REL_ELEMENT_FIELD'];
						elseif(isset($arSettings['REL_SECTION_FIELD'])) $fieldKey = $field.'|'.$arSettings['REL_SECTION_FIELD'];
						elseif(isset($arSettings['REL_USER_FIELD'])) $fieldKey = $field.'|'.$arSettings['REL_USER_FIELD'];
						if(is_array($arElementData[$fieldKey]))
						{
							foreach($arElementData[$fieldKey] as $k2=>$val)
							{
								$arData[$k][$field.'_'.$fieldIndex][$k2] = $arElementData[$field.'_'.$fieldIndex][$k2] = $val;
							}
						}
						else
						{
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $arElementData[$fieldKey];
						}
					}
					if(isset($arSettings['CONVERSION']) && is_array($arSettings['CONVERSION']) && $field && isset($arElementData[$field]))
					{
						$fieldVal = (isset($arElementData[$field.'_'.$fieldIndex]) ? $arElementData[$field.'_'.$fieldIndex] : $arElementData[$field]);
						if(is_array($fieldVal))
						{
							$isMulty = (bool)array_key_exists('TYPE', $fieldVal);
							foreach($fieldVal as $k2=>$val)
							{
								if($isMulty)
								{
									if($k2==='TYPE')
									{
										$arData[$k][$field.'_'.$fieldIndex][$k2] = $arElementData[$field.'_'.$fieldIndex][$k2] = $val;
									}
									else
									{
										$arElementData2 = $arElementData;
										foreach($arElementData2 as $k3=>$v3)
										{
											if(is_array($v3) && array_key_exists('TYPE', $v3) && array_key_exists($k2, $v3)) $arElementData2[$k3] = $v3[$k2];
										}
										$arData[$k][$field.'_'.$fieldIndex][$k2] = $arElementData[$field.'_'.$fieldIndex][$k2] = $this->ApplyConversions($val, $arSettings['CONVERSION'], $arElementData2, $field);
									}
									continue;
								}
								$arData[$k][$field.'_'.$fieldIndex][$k2] = $arElementData[$field.'_'.$fieldIndex][$k2] = $this->ApplyConversions($val, $arSettings['CONVERSION'], $arElementData, $field);
							}
						}
						else
						{
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $this->ApplyConversions($fieldVal, $arSettings['CONVERSION'], $arElementData, $field);
						}
					}
					
					$isMultiple = (bool)($field && ((isset($arData[$k][$field]) && is_array($arData[$k][$field])) || (isset($arData[$k][$field.'_'.$fieldIndex]) && is_array($arData[$k][$field.'_'.$fieldIndex]))));
					if($isMultiple)
					{
						$fromValue = (isset($arSettings['MULTIPLE_FROM_VALUE']) ? $arSettings['MULTIPLE_FROM_VALUE'] : '');
						$toValue = (isset($arSettings['MULTIPLE_TO_VALUE']) ? $arSettings['MULTIPLE_TO_VALUE'] : '');
						if(strlen($fromValue) > 0 || strlen($toValue) > 0)
						{
							if(isset($arData[$k][$field.'_'.$fieldIndex]))
							{
								$arVals = $arData[$k][$field.'_'.$fieldIndex];
								if(!is_array($arVals)) $arVals = array($arVals);
							}
							else
							{
								$arVals = $arData[$k][$field];
							}
							
							if(is_numeric($fromValue) || is_numeric($toValue))
							{
								$from = (is_numeric($fromValue) ? ((int)$fromValue >= 0 ? ((int)$fromValue - 1) : (int)$fromValue) : 0);
								$to = (is_numeric($toValue) ? ((int)$toValue >= 0 ? ((int)$toValue - max(0, $from)) : (int)$toValue) : 0);
								if($to!=0) $arVals = array_slice($arVals, $from, $to);
								else $arVals = array_slice($arVals, $from);
							}
							elseif(strpos($fromValue, ',')!=false)
							{
								$arIndexes = array_diff(array_map('intval', explode(',', $fromValue)), array('0'));
								if(count($arIndexes) > 0)
								{
									$arNewVals = array();
									foreach($arVals as $k1=>$v1)
									{
										if(in_array($k1+1, $arIndexes)) $arNewVals[] = $v1;
									}
									$arVals = $arNewVals;
								}
							}
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $arVals;
						}
					}
					
					if(isset($arSettings['INSERT_PICTURE']) && $arSettings['INSERT_PICTURE']=='Y' && $this->imagedir && $this->IsPictureField($field) && $this->params['FILE_EXTENSION']=='xlsx')
					{
						if(isset($arData[$k][$field.'_'.$fieldIndex]))
						{
							$arVals = $arData[$k][$field.'_'.$fieldIndex];
						}
						else
						{
							$arVals = $arData[$k][$field];
						}
						if(!is_array($arVals)) $arVals = array($arVals);
						foreach($arVals as $key=>$val)
						{
							if($key==='TYPE') continue;
							$before = $after = '';
							if(preg_match('/(<a[^>]+class="kda\-ee\-conversion\-link"[^>]*>)(.*)(<\/a>)/Uis', $val, $m))
							{
								$before = $m[1];
								$val = $m[2];
								$after = $m[3];
							}
							$arFile = $this->GetFileArray($val);
							if($arFile['tmp_name'])
							{
								$maxWidth = ((int)$arSettings['PICTURE_WIDTH'] > 0 ? (int)$arSettings['PICTURE_WIDTH'] : 100);
								$maxHeight = ((int)$arSettings['PICTURE_HEIGHT'] > 0 ? (int)$arSettings['PICTURE_HEIGHT'] : 100);
								$filePath = $arFile['tmp_name'];
								
								$loop = 0;
								while(!CFile::ResizeImage($arFile, array("width" => $maxWidth, "height" => $maxHeight)) && $loop < 10)
								{
									usleep(1000);
									$loop++;
								}

								if($filePath != $arFile['tmp_name'])
								{
									copy($arFile['tmp_name'], $filePath);
								}
								$arVals[$key] = $before.substr($filePath, strlen($this->imagedir)).$after;
							}
							else
							{
								$arVals[$key] = '';
							}
						}
						$arVals = array_diff($arVals, array(''));
						if(count($arVals) > 1)
						{
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $arVals;
						}
						else
						{
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = implode('', $arVals);
						}
					}

					if($isMultiple)
					{
						if(isset($arData[$k][$field.'_'.$fieldIndex]))
						{
							$arVals = $arData[$k][$field.'_'.$fieldIndex];
							if(!is_array($arVals)) $arVals = array($arVals);
						}
						else
						{
							$arVals = $arData[$k][$field];
						}
						
						if(isset($arSettings['MULTIPLE_SEPARATE_BY_ROWS']) && $arSettings['MULTIPLE_SEPARATE_BY_ROWS']=='Y')
						{
							$arVals['TYPE'] = 'MULTIROW';
							$val = $arVals;
							$arMultiRows[$field.'_'.$fieldIndex] = $field.'_'.$fieldIndex;
						}
						elseif(isset($arVals['TYPE']) && $arVals['TYPE']=='MULTICELL')
						{
							$val = $arVals;
						}
						else
						{
							if(isset($arSettings['CHANGE_MULTIPLE_SEPARATOR']) && $arSettings['CHANGE_MULTIPLE_SEPARATOR']=='Y') $separator = $arSettings['MULTIPLE_SEPARATOR'];
							else $separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
							$val = implode($separator, $arVals);
						}
						$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $val;
					}
				}
			}

			foreach($arElementData as $k2=>$val)
			{
				if(is_array($val))
				{
					if(isset($val['TYPE']) && $val['TYPE']=='MULTICELL')
					{
						$arData[$k]['ROWS_COUNT'] = max(1, (int)$arData[$k]['ROWS_COUNT'], count($val)-1);
					}
					elseif(isset($val['TYPE']) && $val['TYPE']=='MULTIROW')
					{
						
					}
					else
					{
						if(isset($arFieldSettings[$k2]['CHANGE_MULTIPLE_SEPARATOR']) && $arFieldSettings[$k2]['CHANGE_MULTIPLE_SEPARATOR']=='Y') $separator = $arFieldSettings[$k2]['MULTIPLE_SEPARATOR'];
						else $separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];					
						$arData[$k][$k2] = implode($separator, $val);
					}
				}
			}
			
			if(isset($this->stepparams['string_lengths']))
			{
				foreach($arFields as $fk=>$fv)
				{
					$val = isset($arElementData[$fv.'_'.$fk]) ? $arElementData[$fv.'_'.$fk] : $arElementData[$fv];
					$this->stepparams['string_lengths'][$listIndex][$fk] = max(0, (int)$this->stepparams['string_lengths'][$listIndex][$fk], strlen(is_array($val) ? serialize($val) : $val));
				}
			}
		}
		
		if(!empty($arMultiRows))
		{
			$arDataNew = array();
			foreach($arData as $k=>$v)
			{
				$arRows = array($v);
				foreach($arMultiRows as $v4) $arRows[0][$v4] = '';
				foreach($v as $k2=>$v2)
				{
					if(is_array($v2) && isset($v2['TYPE']) && $v2['TYPE']=='MULTIROW')
					{
						$i = 0;
						foreach($v2 as $k3=>$v3)
						{
							if($k3==='TYPE') continue;
							if(!isset($arRows[$i]))
							{
								$arRows[$i] = $v;
								foreach($arMultiRows as $v4) $arRows[$i][$v4] = '';
							}
							$arRows[$i][$k2] = $v3;
							$i++;
						}
					}
				}
				$arDataNew = array_merge($arDataNew, $arRows);
			}
			$arData = $arDataNew;
		}
	
		return array(
			'FIELDS' => $arFields,
			'DATA' => $arData,
			'PAGE_COUNT' => $arResElements['navPageCount'],
			'RECORD_COUNT' => $arResElements['navRecordCount'],
			'SECTION_KEY' => $arResElements['sectionKey'],
			'SECTION_COUNT' => $arResElements['sectionCount']
		);
	}
	
	public function GetFieldList($listIndex)
	{
		$arFields = array();
		if(isset($this->params['FIELDS_LIST'][$listIndex]))
		{
			$arFields = $this->params['FIELDS_LIST'][$listIndex];
		}
		if(!is_array($arFields) || count($arFields)==0)
		{
			$arFields = array('IE_ID', 'IE_NAME');
		}
		return $arFields;
	}
	
	public function GetElementsData(&$arData, $arParams)
	{
		if($arParams['TYPE']=='SECTION')
		{
			return $this->GetSectionsData($arData, $arParams);
		}
		
		$arFilter = $arParams['FILTER'];
		$arSkuFilter = $arParams['SKU_FILTER'];
		$arNavParams = (is_array($arParams['NAV_PARAMS']) ? $arParams['NAV_PARAMS'] : false);
		$arAllFields = $arParams['FIELDS'];
		$showOnlyFilterSection = (bool)($this->params['SHOW_ONLY_SECTION_FROM_FILTER'][$this->listIndex]=='Y' && is_array($arFilter['SECTION_ID']) && count(array_diff($arFilter['SECTION_ID'], array(-1, 0))) > 0);

		$arOfferParams = false;
		$offersPropertyId = 0;
		if($arParams['TYPE'] != 'OFFER')
		{
			$arOfferFields = array();
			foreach($arAllFields as $k=>$v)
			{
				if(strpos($v, 'OFFER_')===0)
				{
					$arOfferFields[$k] = substr($v, 6);
				}
			}
			if(!empty($arOfferFields) && ($iblockOffer = $this->GetCachedOfferIblock($arFilter['IBLOCK_ID'])))
			{
				$arOfferParams = array(
					'TYPE' => 'OFFER',
					'FIELDS' => $arOfferFields,
					'NAV_PARAMS' => false,
					'FILTER' => array(
						'IBLOCK_ID' => $iblockOffer['OFFERS_IBLOCK_ID']
					)
				);
				if($this->params['EXPORT_ONE_OFFER_MIN_PRICE']=='Y')
				{
					$arOfferParams['NAV_PARAMS'] = array('nTopCount' => 1);
					if($this->params['EXPORT_ONE_OFFER_MIN_PRICE_TYPE'])
						$arOfferParams['ORDER'] = array($this->params['EXPORT_ONE_OFFER_MIN_PRICE_TYPE']=>'ASC');
					else 
						$arOfferParams['ORDER'] = array('CATALOG_PURCHASING_PRICE'=>'ASC');
				}
				if(is_array($arSkuFilter))
				{
					$arOfferParams['FILTER'] = array_merge($arOfferParams['FILTER'], $arSkuFilter);
				}
				$offersPropertyId = (int)$iblockOffer['OFFERS_PROPERTY_ID'];
				
				/*if(count($arOfferParams['FILTER']) > 1)
				{
					$arFilter['ID'] = CIBlockElement::SubQuery('PROPERTY_'.$offersPropertyId, $arOfferParams['FILTER']);
				}*/
			}
		}		
		
		$arElementFields = array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID');
		$arElementFieldsRels = array();
		$arElementNameFields = array();
		$arPropsFields = array();
		$arPropsFieldsRels = array();
		$arFieldsIpropTemp = array();
		$arFieldsProduct = array();
		$arFieldsPrices = array();
		$arFieldsProductStores = array();
		$arFieldsDiscount = array();
		$arFieldsOrder = array();
		$arFieldsSections = array();
		$arFieldsSet = array();
		$arFieldsSet2 = array();
		foreach($arAllFields as $field)
		{
			if(strpos($field, 'IE_')===0)
			{
				$key = substr($field, 3);
				$arElementNameFields[] = $key;
				if($key=='SECTION_PATH') continue;
				if($key=='PREVIEW_PICTURE_DESCRIPTION' || $key=='DETAIL_PICTURE_DESCRIPTION')
				{
					$key = substr($key, 0, -12);
				}
				if($key=='QR_CODE_IMAGE' && !in_array('DETAIL_PAGE_URL', $arElementFields))
				{
					$arElementFields[] = 'DETAIL_PAGE_URL';
				}
				if(strpos($key, '|')!==false)
				{
					list($key, $fieldRel) = explode('|', $key, 2);
					$arElementFieldsRels[$key][] = $fieldRel;
				}
				$arElementFields[] = $key;
			}
			elseif(strpos($field, 'ISECT')===0)
			{
				$arSect = explode('_', substr($field, 5), 2);
				if(strlen($arSect[0])==0) $arSect[0] = 0;
				$arFieldsSections[$arSect[0]][] = $arSect[1];
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$arPrice = explode('_', substr($field, 10), 2);
				$arFieldsPrices[$arPrice[0]][] = $arPrice[1];
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				$arFieldsProductStores[$arStore[0]][] = $arStore[1];
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				$arFieldsDiscount[] = substr($field, 14);
			}
			elseif(strpos($field, 'ICAT_ORDER_')===0)
			{
				if(CModule::IncludeModule('sale'))
				{
					$arFieldsOrder[] = substr($field, 11);
				}
			}
			elseif(strpos($field, 'ICAT_SET_')===0)
			{
				$arFieldsSet[] = substr($field, 9);
			}
			elseif(strpos($field, 'ICAT_SET2_')===0)
			{
				$arFieldsSet2[] = substr($field, 10);
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				$arFieldsProduct[] = substr($field, 5);
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldKey = substr($field, 7);
				if(strpos($fieldKey, '|')!==false)
				{
					list($fieldKey, $fieldRel) = explode('|', $fieldKey, 2);
					$arPropsFieldsRels[$fieldKey][] = $fieldRel;
				}
				$arPropsFields[] = $fieldKey;
			}
			elseif(strpos($field, 'IP_LIST_PROPS')===0)
			{
				if(is_array($this->arPropListProps))
				{
					$arPropsFields = array_merge($arPropsFields, $this->arPropListProps);
				}
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				$arFieldsIpropTemp[] = substr($field, 11);
			}
		}
	
		$arSelectElementFields = $arElementFields;
		$arSelectElementFieldsForPrice = $arElementFields;
		if(!empty($arFieldsPrices))
		{
			$arPriceIds = array();
			foreach($arFieldsPrices as $k=>$v)
			{
				$arPriceIds[] = $k;
			}
			$arPriceCodes = array();
			$dbRes = CCatalogGroup::GetList(array(), array('ID'=>$arPriceIds), false, false, array('ID', 'NAME'));
			while($arCatalogGroup = $dbRes->Fetch())
			{
				$arPriceCodes[$arCatalogGroup['ID']] = $arCatalogGroup['NAME'];
			}
			$arGroupPrices = CIBlockPriceTools::GetCatalogPrices($arFilter['IBLOCK_ID'], $arPriceCodes);
			if(!is_array($arGroupPrices)) $arGroupPrices = array();
			foreach($arGroupPrices as $k=>$v)
			{
				$arGroupPrices[$k]['CAN_VIEW'] = 1;
				//$arSelectElementFields[] = $v['SELECT'];
				$arSelectElementFieldsForPrice[] = $v['SELECT'];
			}
		}
		
		$arSections = $this->GetSelectSections($arFilter, $arParams);
		$sCount = count($arSections);
	
		$arFilterOriginal = $arFilter;
		$sectionKeyInc = 2;
		$dbResCnt = 0;
		$dbElemResCnt = 0;
		foreach($arSections as $skey=>$arSection)
		{
			if($arParams['SECTION_KEY'] && $arParams['SECTION_KEY'] > $skey + 1) continue;
			$break = false;
			$isSection = false;
			if(!empty($arSection) && is_numeric($arSection['ID']))
			{
				$isSection = true;
				$arFilter['SECTION_ID'] = $arSection['ID'];				
			}
			if($this->params['EXPORT_SEP_SECTIONS']=='Y')
			{
				$arFilter['INCLUDE_SUBSECTIONS'] = 'N';
			}
			if($this->params['EXPORT_ELEMENT_ONE_SECTION']=='Y' && !$showOnlyFilterSection && is_numeric($arSection['ID']) && $arFilter['SECTION_ID'] > 0)
			{
				$dbESRes = Bitrix\Iblock\ElementTable::GetList(array('filter'=>array('IBLOCK_SECTION_ID'=>$arFilter['SECTION_ID']), 'select'=>array('ID')));
				$arIds = array(0);
				while($arES = $dbESRes->Fetch())
				{
					$arIds[] = $arES['ID'];
				}
				if(!is_array($arFilterOriginal['ID'])) $arFilter['ID'] = $arIds;
				else $arFilter['ID'] = array_intersect($arFilterOriginal['ID'], $arIds);
			}

			if(isset($arParams['ORDER']) && !empty($arParams['ORDER'])) $arOrder = $arParams['ORDER'];
			else $arOrder = $this->GetElementOrder($arFilter['IBLOCK_ID']);
			$dbResElements = CIblockElement::GetList($arOrder, $arFilter, false, $arNavParams, $arSelectElementFields);

			if($isSection 
				&& (!isset($arNavParams['iNumPage']) || $arNavParams['iNumPage']==1)
				&& $dbResElements->SelectedRowsCount() > 0)
			{
				if($this->params['EXPORT_SECTION_PATH']=='Y')
				{
					/*$arData[] = array(
						'RTYPE' => 'SECTION_PATH',
						'NAME' => $this->GetSectionPath($arSection['ID'])
					);
					$dbResCnt++;*/
					$arParentSections = $this->GetParentSections($arSection['ID']);
					foreach($arParentSections as $key=>$arParentSection)
					{
						$arData[] = array(
							'RTYPE' => 'SECTION_'.$key,
							'NAME' => $this->GetSectionPath($arParentSection['ID']),
							'ELEMENT_CNT' => $dbResElements->SelectedRowsCount()
						);
						$dbResCnt++;
					}
				}
				else
				{
					$arParentSections = $this->GetParentSections($arSection['ID']);
					foreach($arParentSections as $key=>$arParentSection)
					{
						$arData[] = array(
							'RTYPE' => 'SECTION_'.$key,
							'NAME' => $arParentSection['NAME'],
							'ELEMENT_CNT' => $dbResElements->SelectedRowsCount()
						);
						$dbResCnt++;
					}
				}
			}
			
			/*Prepare elements data*/
			$arElementList = array();
			$arElementIds = array();
			$arElementPrices = array();
			while($arElement = $dbResElements->GetNext())
			{
				$arElementList[] = $arElement;
				$arElementIds[] = $arElement['ID'];
			}
			if(!empty($arElementIds))
			{
				if(!empty($arFieldsPrices))
				{
					$arCatlogGroupIds = array();
					foreach($arFieldsPrices as $key=>$arPriceSelectField)
					{
						if(empty($arPriceSelectField)) continue;
						$arCatlogGroupIds[] = $key;
					}
					if(!empty($arCatlogGroupIds))
					{
						$arPriceFilter = array('PRODUCT_ID'=>$arElementIds, 'CATALOG_GROUP_ID'=>$arCatlogGroupIds);
						if(is_callable(array('\Bitrix\Catalog\Model\Price', 'getList')))
						{
							$dbRes2 = \Bitrix\Catalog\Model\Price::getList(array('filter'=>$arPriceFilter));
						}
						else
						{
							$dbRes2 = CPrice::GetList(array(), $arPriceFilter, false, false);
						}
						while($arPrice = $dbRes2->Fetch())
						{
							$arElementPrices[$arPrice['CATALOG_GROUP_ID']][$arPrice['PRODUCT_ID']][] = $arPrice;
						}
					}
				}
			}
			/*/Prepare elements data*/
			
			if($arParams['TYPE'] != 'OFFER')
			{
				$curPageCnt = 0;
				$this->stepparams['currentPageCnt'] = 0;
			}
			//while($arElement = $dbResElements->GetNext())
			foreach($arElementList as $arElement)
			{
				if($arParams['TYPE'] != 'OFFER')
				{
					$curPageCnt++;
					if($curPageCnt <= (int)$this->currentPageCnt) continue;
				}
				
				$arElement2 = array();
				foreach($arElement as $k=>$v)
				{
					if(strpos($k, '~')===0)
					{
						$arElement[substr($k, 1)] = $v;
					}
				}

				foreach($arElement as $k=>$v)
				{
					if(strpos($k, '~')!==0 && in_array($k, $arElementFields))
					{
						if($k=='PREVIEW_PICTURE' || $k=='DETAIL_PICTURE')
						{
							$v = $this->GetFileValue($v, 'IE_'.$k);
						}
						$arElement2['IE_'.$k] = $v;
					}
				}

				if(in_array('PREVIEW_PICTURE_DESCRIPTION', $arElementNameFields))
				{
					$arElement2['IE_PREVIEW_PICTURE_DESCRIPTION'] = $this->GetFileDescription($arElement['PREVIEW_PICTURE']);
				}
				if(in_array('DETAIL_PICTURE_DESCRIPTION', $arElementNameFields))
				{
					$arElement2['IE_DETAIL_PICTURE_DESCRIPTION'] = $this->GetFileDescription($arElement['DETAIL_PICTURE']);
				}
				if(in_array('QR_CODE_IMAGE', $arElementNameFields) && ((int)$this->stepparams['export_started'] > 0 || (int)$this->stepparams['qrcode_qnt'] < 10))
				{
					if(!class_exists('\QRcode')) require_once(dirname(__FILE__).'/../../lib/phpqrcode/qrlib.php');
					$obRequest = \Bitrix\Main\Context::getCurrent()->getRequest();
					$requestUri = trim($obRequest->getRequestUri());
					$qrSize = (int)$this->fparamsByName[$this->listIndex]['IE_QR_CODE_IMAGE']['QRCODE_SIZE'];
					$qrpath = $this->imagedir.'image'.(++$this->stepparams['image_cnt']).'.png';
					\QRcode::png(($obRequest->isHttps() ? 'https' : 'http').'://'.$obRequest->getHttpHost().$arElement['DETAIL_PAGE_URL'], $qrpath, QR_ECLEVEL_H, $qrSize, 4);
					$arElement2['IE_QR_CODE_IMAGE'] = $this->GetFileValue($qrpath, 'IE_QR_CODE_IMAGE');
					$this->stepparams['qrcode_qnt']++;
				}
				
				foreach($arElementFieldsRels as $fk=>$arRels)
				{
					if(in_array($fk, array('CREATED_BY', 'MODIFIED_BY')))
					{
						foreach($arRels as $relField)
						{
							$fieldKeyOrig = 'IE_'.$fk;
							$fieldKey = $fieldKeyOrig.'|'.$relField;
							$arElement2[$fieldKey] = $this->GetUserField($arElement2[$fieldKeyOrig], $relField);
						}
					}
				}
				
				$this->GetElementSectionShare($arElement2, $arElement, $arElementNameFields, $arFieldsSections);
			
				if(!empty($arPropsFields))
				{
					$dbRes2 = CIBlockElement::GetProperty($arElement['IBLOCK_ID'], $arElement['ID'], array(), array());
					while($arProp = $dbRes2->Fetch())
					{
						if(in_array($arProp['ID'], $arPropsFields))
						{
							$arRels = $arPropsFieldsRels[$arProp['ID']];
							if(!is_array($arRels) || empty($arRels)) $arRels = array('');
							foreach($arRels as $relField)
							{
								$fieldKey = $fieldKeyOrig = 'IP_PROP'.$arProp['ID'];
								if($relField) $fieldKey .= '|'.$relField;
								
								$val = $arProp['VALUE'];
								if($relField=='IE_PREVIEW_PICTURE' || $relField=='IE_DETAIL_PICTURE')
								{
									$val = $this->GetFileValue($val);
								}
								else
								{
									if($arProp['PROPERTY_TYPE']=='L')
									{
										$val = $this->GetPropertyListValue($arProp, $val);
									}
									elseif($arProp['PROPERTY_TYPE']=='E')
									{
										$val = $this->GetPropertyElementValue($arProp, $val, $relField);
									}
									elseif($arProp['PROPERTY_TYPE']=='G')
									{
										$val = $this->GetPropertySectionValue($arProp, $val, $relField);
									}
									elseif($arProp['PROPERTY_TYPE']=='F')
									{
										$val = $this->GetFileValue($val);
									}
									elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
									{
										$val = $this->GetHighloadBlockValue($arProp, $val);
									}
									elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='HTML')
									{
										$val = $this->GetHTMLValue($arProp, $val);
									}
								}
								
								if($arProp['MULTIPLE']=='Y')
								{
									if(!isset($arElement2[$fieldKey]))
									{
										$arElement2[$fieldKey] = array();
									}
									$arElement2[$fieldKey][] = $val;
								}
								else
								{
									$arElement2[$fieldKey] = $val;
								}
								
								if(!isset($arElement2[$fieldKeyOrig]))
								{
									$arElement2[$fieldKeyOrig] = $arElement2[$fieldKey];
								}
							}
						}
						
						if(in_array($arProp['ID'].'_DESCRIPTION', $arPropsFields))
						{
							$val = $arProp['DESCRIPTION'];
							$key = 'IP_PROP'.$arProp['ID'].'_DESCRIPTION';
							
							if($arProp['MULTIPLE']=='Y')
							{
								if(!isset($arElement2[$key])) $arElement2[$key] = array();
								$arElement2[$key][] = $val;
							}
							else
							{
								$arElement2[$key] = $val;
							}
						}
					}
				}
				
				if(!empty($arFieldsIpropTemp))
				{
					$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arElement['IBLOCK_ID'], $arElement['ID']);
					$arFieldsIpropTempCh = preg_grep('/^CH_/', $arFieldsIpropTemp);
					if(!empty($arFieldsIpropTempCh))
					{
						$arPropVals = $ipropValues->queryValues();
						foreach($arFieldsIpropTempCh as $key)
						{
							$subKey = substr($key, 3);
							$v = (bool)(isset($arPropVals[$subKey]) && $arPropVals[$subKey]['ENTITY_TYPE']=='E');
							$arElement2['IPROP_TEMP_'.$key] = ($v ? 'Y' : 'N');
						}
					}					
					
					$arPropVals = $ipropValues->getValues();
					foreach(array_diff($arFieldsIpropTemp, $arFieldsIpropTempCh) as $key)
					{
						$arElement2['IPROP_TEMP_'.$key] = $arPropVals[$key];
					}
				}
				
				if(!empty($arFieldsSet) && CBXFeatures::IsFeatureEnabled('CatCompleteSet') && CCatalogProductSet::isProductHaveSet($arElement['ID'], CCatalogProductSet::TYPE_GROUP))
				{
					$arSets = CCatalogProductSet::getAllSetsByProduct($arElement['ID'], CCatalogProductSet::TYPE_GROUP);
					$arSet = current($arSets);
					if(is_array($arSet['ITEMS']))
					{
						foreach($arSet['ITEMS'] as $arSetItem)
						{
							foreach($arFieldsSet as $setField)
							{
								$arElement2['ICAT_SET_'.$setField][] = $arSetItem[$setField];
							}
						}
					}
				}
				
				if(!empty($arFieldsSet2) && CBXFeatures::IsFeatureEnabled('CatCompleteSet') && CCatalogProductSet::isProductHaveSet($arElement['ID'], CCatalogProductSet::TYPE_SET))
				{
					$arSets2 = CCatalogProductSet::getAllSetsByProduct($arElement['ID'], CCatalogProductSet::TYPE_SET);
					$arSet2 = current($arSets2);
					if(is_array($arSet2['ITEMS']))
					{
						foreach($arSet2['ITEMS'] as $arSet2Item)
						{
							foreach($arFieldsSet2 as $set2Field)
							{
								$arElement2['ICAT_SET2_'.$set2Field][] = $arSet2Item[$set2Field];
							}
						}
					}
				}
				
				if(!empty($arFieldsProduct))
				{
					$dbRes2 = CCatalogProduct::GetList(array(), array('ID'=>$arElement['ID']), false, array('nTopCount'=>1), array());
					if($arProduct = $dbRes2->Fetch())
					{
						foreach($arProduct as $k=>$v)
						{
							if($k=='VAT_ID')
							{
								if($v)
								{
									if(!isset($this->catalogVats)) $this->catalogVats = array();
									if(!isset($this->catalogVats[$v]))
									{
										$vatPercent = '';
										$dbRes = CCatalogVat::GetList(array(), array('ID'=>$v), array('RATE'));
										if($arVat = $dbRes->Fetch())
										{
											$vatPercent = $arVat['RATE'];
										}
										$this->catalogVats[$v] = $vatPercent;
									}
									$v = $this->catalogVats[$v];
								}
								else
								{
									$v = '';
								}
							}
							elseif($k=='MEASURE')
							{
								if(!isset($this->catalogMeasure) || !is_array($this->catalogMeasure))
								{
									$this->catalogMeasure = array();
									$dbRes = CCatalogMeasure::getList(array(), array());
									while($arr = $dbRes->Fetch())
									{
										$this->catalogMeasure[$arr['ID']] = ($arr['SYMBOL_RUS'] ? $arr['SYMBOL_RUS'] : $arr['SYMBOL_INTL']);
									}
								}
								$v = $this->catalogMeasure[$v];
							}
								
							
							$elemKey = $elemParamKey = 'ICAT_'.$k;
							if($arParams['TYPE'] == 'OFFER') $elemParamKey = 'OFFER_'.$elemParamKey;
							if($k=='PURCHASING_PRICE')
							{
								$arElement2[$elemKey.'_ORIG'] = $v;
								$v = $this->GetConvertedPrice($v, $arProduct['PURCHASING_CURRENCY'], $elemParamKey);
							}
							$arElement2[$elemKey] = $v;
						}
						
						if(in_array('MEASURE_RATIO', $arFieldsProduct))
						{
							$dbRes = CCatalogMeasureRatio::getList(array(), array('PRODUCT_ID' => $arElement['ID']), false, false, array('RATIO'));
							if($arRatio = $dbRes->Fetch())
							{
								$arElement2['ICAT_MEASURE_RATIO'] = $arRatio['RATIO'];
							}
							else
							{
								$arElement2['ICAT_MEASURE_RATIO'] = '';
							}
						}
						
						if(in_array('BARCODE', $arFieldsProduct))
						{
							$dbRes = CCatalogStoreBarCode::getList(array(), array('PRODUCT_ID' => $arElement['ID']), false, false, array('ID', 'BARCODE'));
							$arElement2['ICAT_BARCODE'] = '';
							while($arBarcode = $dbRes->Fetch())
							{
								$arElement2['ICAT_BARCODE'] .= (strlen($arElement2['ICAT_BARCODE']) > 0 ? $this->params['ELEMENT_MULTIPLE_SEPARATOR'] : '').$arBarcode['BARCODE'];
							}
						}
					}
				}
				
				if(!empty($arFieldsPrices))
				{
					foreach($arFieldsPrices as $key=>$arPriceSelectField)
					{
						if(empty($arPriceSelectField)) continue;
						$arNavStartParams = array('nTopCount'=>1);
						$needPriceExt = false;
						if(in_array('PRICE_EXT', $arPriceSelectField))
						{
							$arNavStartParams = false;
							$needPriceExt = true;
						}
						
						if(in_array('PRICE_DISCOUNT', $arPriceSelectField))
						{
							$elemKey = $elemParamKey = 'ICAT_PRICE'.$key.'_PRICE_DISCOUNT';
							if($arParams['TYPE'] == 'OFFER') $elemParamKey = 'OFFER_'.$elemParamKey;
							$siteId = (isset($this->customFieldSettings[$elemParamKey]['SITE_ID']) ? $this->customFieldSettings[$elemParamKey]['SITE_ID'] : '');
							if(!$siteId) $siteId = $this->GetIblockSite($arElement['IBLOCK_ID'], true);
							$dbResElementForPrice = CIblockElement::GetList(array(), array('ID'=>$arElement['ID']), false, array('nTopCount'=>1), $arSelectElementFieldsForPrice);
							$arElementForPrice = $dbResElementForPrice->Fetch();
							$arPrices = CIBlockPriceTools::GetItemPrices($arElement['IBLOCK_ID'], $arGroupPrices, $arElementForPrice, true, array(), 0, $siteId);
							$arPrice = $arPrices[$arPriceCodes[$key]];
							$arElement2[$elemKey.'_ORIG'] = $arPrice['DISCOUNT_VALUE'];
							$arElement2[$elemKey] = $this->GetConvertedPrice($arPrice['DISCOUNT_VALUE'], $arPrice['CURRENCY'], $elemParamKey);
							
							if(isset($this->arPricesGroup[$key]) && is_array($this->arPricesGroup[$key]))
							{
								$origUserId = $GLOBALS['USER']->GetID();
								foreach($this->arPricesGroup[$key] as $keyGroups=>$arGroups)
								{
									$userId = $this->GetUserByGroups($arGroups);
									if(!$userId) continue;
									$GLOBALS['USER']->Authorize($userId);
									$arPrices = CIBlockPriceTools::GetItemPrices($arElement['IBLOCK_ID'], $arGroupPrices, $arElementForPrice, true, array(), 0, $siteId);
									$arPrice = $arPrices[$arPriceCodes[$key]];
									$arElement2[$elemKey.'__'.$keyGroups.'_ORIG'] = $arPrice['DISCOUNT_VALUE'];
									$arElement2[$elemKey.'__'.$keyGroups] = $this->GetConvertedPrice($arPrice['DISCOUNT_VALUE'], $arPrice['CURRENCY'], $elemParamKey);
									if($origUserId > 0) $GLOBALS['USER']->Authorize($origUserId);
									else $GLOBALS['USER']->Logout();
								}
							}
						}
						
						if(in_array('PRICE', $arPriceSelectField) && !in_array('CURRENCY', $arPriceSelectField)) $arPriceSelectField[] = 'CURRENCY';
						if($needPriceExt)
						{
							
							if(!in_array('PRICE', $arPriceSelectField)) $arPriceSelectField[] = 'PRICE';
							if(!in_array('CURRENCY', $arPriceSelectField)) $arPriceSelectField[] = 'CURRENCY';
							if(!in_array('QUANTITY_FROM', $arPriceSelectField)) $arPriceSelectField[] = 'QUANTITY_FROM';
							if(!in_array('QUANTITY_TO', $arPriceSelectField)) $arPriceSelectField[] = 'QUANTITY_TO';
						}					
						//if(in_array('EXTRA', $arPriceSelectField)) $arPriceSelectField[] = 'EXTRA_ID';
						$arPriceExtraSelectField = preg_grep('/^EXTRA/', $arPriceSelectField);
						if(count($arPriceExtraSelectField) > 0) $arPriceSelectField[] = 'EXTRA_ID';
						
						$arPrices = array();
						if(isset($arElementPrices[$key][$arElement['ID']]))
						{
							$arPrices = $arElementPrices[$key][$arElement['ID']];
							if($arNavStartParams['nTopCount']==1) $arPrices = array_slice($arPrices, 0, 1);
						}
						else
						{
							$dbRes2 = CPrice::GetList(array(), array('PRODUCT_ID'=>$arElement['ID'], 'CATALOG_GROUP_ID'=>$key), false, $arNavStartParams, $arPriceSelectField);
							while($arPrice = $dbRes2->Fetch())
							{
								$arPrices[] = $arPrice;
							}
						}
						//$dbRes2 = CPrice::GetList(array(), array('PRODUCT_ID'=>$arElement['ID'], 'CATALOG_GROUP_ID'=>$key), false, $arNavStartParams, $arPriceSelectField);
						//while($arPrice = $dbRes2->Fetch())
						foreach($arPrices as $arPrice)
						{
							if($needPriceExt)
							{
								$elemKey = 'ICAT_PRICE'.$key.'_PRICE_EXT';
								$firstPrice = (bool)(!isset($arElement2[$elemKey]) || strlen($arElement2[$elemKey])==0);
								$arElement2[$elemKey] .= ($firstPrice ? "" : ";\r\n").implode(':', array($arPrice['QUANTITY_FROM'], $arPrice['QUANTITY_TO'], $arPrice['PRICE'], $arPrice['CURRENCY']));
								if(!$firstPrice) continue;
							}
							
							foreach($arPrice as $k=>$v)
							{
								$elemKey = $elemParamKey = 'ICAT_PRICE'.$key.'_'.$k;
								if($arParams['TYPE'] == 'OFFER') $elemParamKey = 'OFFER_'.$elemParamKey;
								if($k=='PRICE')
								{
									$arElement2[$elemKey.'_ORIG'] = $v;
									$v = $this->GetConvertedPrice($v, $arPrice['CURRENCY'], $elemParamKey);
								}
								$arElement2[$elemKey] = $v;
							}
							
							if($arPrice['EXTRA_ID'])
							{
								if(!isset($this->catalogPriceExtra)) $this->catalogPriceExtra = array();
								if(!isset($this->catalogPriceExtra[$arPrice['EXTRA_ID']]))
								{
									$extraPercent = '';
									$dbRes = CExtra::GetList(array(), array('ID'=>$arPrice['EXTRA_ID']), false, array('nTopCount'=>1)/*, array('PERCENTAGE')*/);
									/*if($arExtra = $dbRes->Fetch())
									{
										$extraPercent = $arExtra['PERCENTAGE'];
									}
									$this->catalogPriceExtra[$arPrice['EXTRA_ID']] = $extraPercent;*/
									$arExtra = $dbRes->Fetch();
									$this->catalogPriceExtra[$arPrice['EXTRA_ID']] = $arExtra;
								}
								foreach($arPriceExtraSelectField as $v)
								{
									if($v=='EXTRA') $extraKey = 'PERCENTAGE';
									else $extraKey = substr($v, 6);
									$elemKey = 'ICAT_PRICE'.$key.'_'.$v;
									$arElement2[$elemKey] = $this->catalogPriceExtra[$arPrice['EXTRA_ID']][$extraKey];
								}
								/*$elemKey = 'ICAT_PRICE'.$key.'_EXTRA';
								$arElement2[$elemKey] = $this->catalogPriceExtra[$arPrice['EXTRA_ID']];*/
							}
						}
					}
				}

				if(!empty($arFieldsProductStores))
				{
					foreach($arFieldsProductStores as $key=>$arStoreSelectField)
					{
						$dbRes2 = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID'=>$arElement['ID'], 'STORE_ID'=>$key), false, array('nTopCount'=>1), $arStoreSelectField);
						if($arStore = $dbRes2->Fetch())
						{
							foreach($arStore as $k=>$v)
							{
								$elemKey = 'ICAT_STORE'.$key.'_'.$k;
								$arElement2[$elemKey] = $v;
							}
						}
					}
				}
				
				if(!empty($arFieldsDiscount))
				{
					$arBasePrice = CPrice::GetBasePrice($arElement['ID']);
					$basePrice = $arBasePrice['PRICE'];
					//$arDiscountList = CCatalogDiscount::GetDiscountForProduct(array('ID' => $arElement['ID'], 'IBLOCK_ID' => $arElement['IBLOCK_ID']), array());

					$userGroups = array();
					$arSites = $this->GetIblockSite($arElement['IBLOCK_ID']);
					foreach($arFieldsDiscount as $fieldName)
					{
						$fieldKey = 'ICAT_DISCOUNT_'.$fieldName;
						if(isset($this->customFieldSettings[$fieldKey]) && is_array($this->customFieldSettings[$fieldKey]))
						{
							$arSettings = $this->customFieldSettings[$fieldKey];
							if(isset($arSettings['USER_GROUP']) && is_array($arSettings['USER_GROUP']) && !empty($arSettings['USER_GROUP']))
							{
								$userGroups = $arSettings['USER_GROUP'];
							}
							if(isset($arSettings['SITE_ID']) && $arSettings['SITE_ID'])
							{
								$arSites = array($arSettings['SITE_ID']);
							}
						}
					}
					$arDiscountList = CCatalogDiscount::GetDiscount($arElement['ID'], $arElement['IBLOCK_ID'], array(), $userGroups, "N", $arSites);
					$maxPercent = -1;
					$maxIndex = -1;
					if(is_array($arDiscountList))
					{
						foreach($arDiscountList as $ind=>$arDiscount)
						{
							$percent = 0;
							if($arDiscount['VALUE_TYPE']=='P') $percent = $arDiscount['VALUE'];
							elseif($arDiscount['VALUE_TYPE']=='F') $percent = (1 - ($basePrice - $arDiscount['VALUE']) / $basePrice) * 100;
							elseif($arDiscount['VALUE_TYPE']=='S') $percent = (1 - ($arDiscount['VALUE']) / $basePrice) * 100;
							if($percent > 0 && $percent > $maxPercent)
							{
								$maxPercent = $percent;
								$maxIndex = $ind;
							}
							if($arDiscount['LAST_DISCOUNT']=='Y') break;
						}
					}
					if($maxIndex >= 0)
					{
						$arDiscount = $arDiscountList[$maxIndex];
						foreach($arFieldsDiscount as $fieldName)
						{
							if($fieldName=='VALUE|VALUE_TYPE=P')
							{
								$val = '';
								if($arDiscount['VALUE_TYPE']=='P') $val = $arDiscount['VALUE'];
								elseif($arDiscount['VALUE_TYPE']=='F') $val = (1 - ($basePrice - $arDiscount['VALUE']) / $basePrice) * 100;
								elseif($arDiscount['VALUE_TYPE']=='S') $val = (1 - ($arDiscount['VALUE']) / $basePrice) * 100;
								$arElement2['ICAT_DISCOUNT_'.$fieldName] = ($val ? round((float)$val, 4) : '');
							}
							elseif($fieldName=='VALUE|VALUE_TYPE=F')
							{
								$val = '';
								if($arDiscount['VALUE_TYPE']=='P') $val = $basePrice * ($arDiscount['VALUE'] / 100);
								elseif($arDiscount['VALUE_TYPE']=='F') $val = $arDiscount['VALUE'];
								elseif($arDiscount['VALUE_TYPE']=='S') $val = $basePrice - $arDiscount['VALUE'];
								$arElement2['ICAT_DISCOUNT_'.$fieldName] = ($val ? round((float)$val, 4) : '');
							}
							elseif($fieldName=='VALUE|VALUE_TYPE=S')
							{
								$val = '';
								if($arDiscount['VALUE_TYPE']=='P') $val = $basePrice * (1 - $arDiscount['VALUE'] / 100);
								elseif($arDiscount['VALUE_TYPE']=='F') $val = $basePrice - $arDiscount['VALUE'];
								elseif($arDiscount['VALUE_TYPE']=='S') $val = $arDiscount['VALUE'];
								$arElement2['ICAT_DISCOUNT_'.$fieldName] = ($val ? round((float)$val, 4) : '');
							}
						}
					}
				}
				
				if(!empty($arFieldsOrder))
				{
					if(in_array('PRODUCT_QNT', $arFieldsOrder))
					{
						$arOrderFilter = array('PRODUCT_ID'=>$arElement['ID'], '!ORDER_ID'=>false);
						if(isset($this->arFilterOrders)) $arOrderFilter['ORDER_ID'] = $this->arFilterOrders;
						$arElement2['ICAT_ORDER_PRODUCT_QNT'] = 0;
						if($arOrderData = \CSaleBasket::GetList(array(), $arOrderFilter, array('SUM'=>'QUANTITY'))->Fetch())
						{
							$arElement2['ICAT_ORDER_PRODUCT_QNT'] = (int)$arOrderData['QUANTITY'];
						}
					}
				}
				
				if($arParams['TYPE'] == 'OFFER')
				{
					$arElement3 = $arElement2;
					$arElement2 = array();
					foreach($arElement3 as $k=>$v)
					{
						$arElement2['OFFER_'.$k] = $v;
					}
				}
				
				if($this->params['EXPORT_SECTIONS_ONE_CELL']=='Y')
				{
					$arElementSections = $this->GetElementSectionList($arElement2, $arElement, $arFieldsSections, $arElementNameFields, $arFilter, $arParams, $showOnlyFilterSection);
					foreach($arElementSections as $arElement3)
					{
						foreach($arElement3 as $k=>$v)
						{
							if(strlen($arElement2[$k]) > 0) $arElement2[$k] .= $this->params['ELEMENT_MULTIPLE_SEPARATOR'].' ';
							$arElement2[$k] .= $v;
						}
					}
				}
				
				$needAdd = true;
				if(is_array($arOfferParams))
				{			
					$needSku = (bool)(count($arOfferParams['FILTER']) > 1);
					if($needSku && defined('\Bitrix\Catalog\ProductTable::TYPE_PRODUCT') && \Bitrix\Catalog\ProductTable::TYPE_PRODUCT==$arElement['CATALOG_TYPE'] && isset($arFilter['CATALOG_TYPE']) && is_array($arFilter['CATALOG_TYPE']) && in_array(\Bitrix\Catalog\ProductTable::TYPE_PRODUCT, $arFilter['CATALOG_TYPE']))
					{
						$needSku = false;
					}
					$arOfferParams['FILTER']['PROPERTY_'.$offersPropertyId] = $arElement['ID'];
					$arOfferParams['PARENTFIELDS'] = $arElement2;
					if($this->params['EXPORT_OFFERS_JOIN']=='Y' || $this->params['EXPORT_PROPUCTS_JOIN']=='Y')
					{
						$arDataOffers = array();
						$arResElements2 = $this->GetElementsData($arDataOffers, $arOfferParams);
						
						if($this->params['EXPORT_OFFERS_JOIN']=='Y')
						{
							$arDataFields = array();
							foreach($arDataOffers as $arDataOffer)
							{
								foreach($arDataOffer as $k=>$v)
								{
									if(strpos($k, 'OFFER_')===0) 
									{
										if(!is_array($arDataFields[$k])) $arDataFields[$k] = array();
										if(is_array($v))
										{
											foreach($v as $v2) $arDataFields[$k][] = $v2;
										}
										else $arDataFields[$k][] = $v;
									}
								}
							}
							foreach($arDataFields as $k=>$v)
							{
								if(is_array($v))
								{
									if(strpos($k, 'OFFER_IP_PROP')===0) $v = array_diff(array_unique($v), array(''));
								}
								$arElement2[$k] = $v;
							}
						}
						elseif($this->params['EXPORT_PROPUCTS_JOIN']=='Y')
						{
							$arDataFields = array();
							foreach($arDataOffers as $arDataOffer)
							{
								foreach($arDataOffer as $k=>$v)
								{
									if(strpos($k, 'OFFER_')===0) 
									{
										if(!is_array($arDataFields[$k])) $arDataFields[$k] = array('TYPE'=>'MULTICELL');
										$arDataFields[$k][] = $v;
									}
								}
							}
							foreach($arDataFields as $k=>$v)
							{
								$arElement2[$k] = $v;
							}
						}
						if($arResElements2['navRecordCount'] == 0 && $needSku)
						{
							$needAdd = false;
						}
					}
					else
					{
						if(isset($this->params['MAX_PREVIEW_LINES']) && $this->params['MAX_PREVIEW_LINES'] > 0 && $this->params['EXPORT_ONE_OFFER_MIN_PRICE']!='Y')
						{
							$arOfferParams['NAV_PARAMS']['nTopCount'] = $this->params['MAX_PREVIEW_LINES'];
						}

						$arResElements2 = $this->GetElementsData($arData, $arOfferParams);
						if($arResElements2['navRecordCount'] > 0 || $needSku)
						{
							$needAdd = false;
						}
					}
					unset($arOfferParams['FILTER']['PROPERTY_'.$offersPropertyId]);
				}
				
				if($needAdd)
				{
					$arElementSections = array();
					if($this->params['EXPORT_SECTIONS_ONE_CELL']!='Y')
					{
						$arElementSections = $this->GetElementSectionList($arElement2, $arElement, $arFieldsSections, $arElementNameFields, $arFilter, $arParams, $showOnlyFilterSection);
					}
					$arData[] = $arElement2;
					$this->ProcessMoveFiles($arElement2);
					foreach($arElementSections as $arElement3)
					{
						$arData[] = $arElement3;
					}
				}
				$dbResCnt++;
				$dbElemResCnt++;
				
				if($arParams['NAV_PARAMS']['nTopCount'] && $dbResCnt >= $arParams['NAV_PARAMS']['nTopCount'])
				{
					$break = true;
					break;
				}
				elseif($arParams['TYPE'] != 'OFFER' && $this->CheckTimeEnding())
				{
					$this->stepparams['currentPageCnt'] = $curPageCnt;
					$break = true;
					break;
				}
			}
			
			if(($arParams['NAV_PARAMS']['iNumPage'] && $arParams['NAV_PARAMS']['iNumPage'] < $dbResElements->NavPageCount) || $this->stepparams['currentPageCnt'] > 0)
			{
				$sectionKeyInc = 1;
			}
			if($break || ($arParams['NAV_PARAMS']['iNumPage'] && $dbResElements->SelectedRowsCount() > 0)) break;
		}
		
		$navRecordCount = $dbResElements->NavRecordCount;
		$navPageCount = $dbResElements->NavPageCount;
		if(is_array($arOfferParams))
		{
			$arFilter2 = $arOfferParams['FILTER'];
			unset($arFilter2['PROPERTY_'.$offersPropertyId]);
			foreach($arFilterOriginal as $k=>$v)
			{
				$arFilter2['PROPERTY_'.$offersPropertyId.'.'.$k] = $v;
			}
			$cnt = CIblockElement::GetList(array(), $arFilter2, array());
			if($cnt > 0)
			{
				if(isset($arOfferParams['NAV_PARAMS']['nTopCount']) && $arOfferParams['NAV_PARAMS']['nTopCount'] < $cnt) $cnt = $arOfferParams['NAV_PARAMS']['nTopCount'];
				$navRecordCount = $cnt;
			}
		}
		
		if(!empty($arFieldsDiscount))
		{
			\CCatalogDiscount::ClearDiscountCache(array(
			   'PRODUCT' => true,
			   'SECTIONS' => true,
			   'SECTION_CHAINS' => true,
			   'PROPERTIES' => true
			));
		}
		
		if($dbResCnt > $navRecordCount) $navRecordCount = $dbResCnt;
		return array(
			'navRecordCount' => $navRecordCount,
			'navPageCount' => $navPageCount,
			'sectionKey' => $skey + $sectionKeyInc,
			'sectionCount' => $sCount
		);
	}
	
	public function GetElementSectionList(&$arElement2, $arElement, $arFieldsSections, $arElementNameFields, $arFilter, $arParams, $showOnlyFilterSection)
	{
		$arElementSections = array();
		if(!empty($arFieldsSections) && $arElement['IBLOCK_SECTION_ID'])
		{
			$onlyOneSection = (bool)($this->params['CSV_YANDEX']=='Y' || $this->params['EXPORT_ELEMENT_ONE_SECTION']=='Y');
			$mainSection = $arElement['IBLOCK_SECTION_ID'];
			if(!$showOnlyFilterSection && !$onlyOneSection)
			{
				$dbRes = CIBlockElement::GetElementGroups($arElement['ID'], true, array('ID'));
				while($arSect = $dbRes->Fetch())
				{
					if($arSect['ID']!=$mainSection)
					{
						$arElement3 = array();
						$arElement['IBLOCK_SECTION_ID'] = $arSect['ID'];
						$this->GetElementSection($arElement3, $arElement, $arElementNameFields, $arFieldsSections);
						if(isset($arElement3['IE_SECTION_PATH'])) unset($arElement3['IE_SECTION_PATH']);
						$arElementSections[] = $arElement3;
					}
				}
			}
			elseif($showOnlyFilterSection)
			{
				if(!in_array($mainSection, $arFilter['SECTION_ID']) || !$onlyOneSection)
				{
					$dbRes = CIBlockElement::GetElementGroups($arElement['ID'], true, array('ID'));
					while($arSect = $dbRes->Fetch())
					{
						if(in_array($arSect['ID'], $arFilter['SECTION_ID']) || ($arFilter['INCLUDE_SUBSECTIONS']=='Y' && count(array_intersect($this->GetSectWithParents($arSect['ID']), $arFilter['SECTION_ID'])) > 0))
						{
							$arElement3 = array();
							$arElement['IBLOCK_SECTION_ID'] = $arSect['ID'];
							$this->GetElementSection($arElement3, $arElement, $arElementNameFields, $arFieldsSections);
							if(isset($arElement3['IE_SECTION_PATH'])) unset($arElement3['IE_SECTION_PATH']);
							if($arSect['ID']==$mainSection) array_unshift($arElementSections, $arElement3);
							else $arElementSections[] = $arElement3;
						}
					}
					if(/*!in_array($mainSection, $arFilter['SECTION_ID']) && */!empty($arElementSections))
					{
						$arElement3 = array_shift($arElementSections);
						$arElement2 = array_merge($arElement2, $arElement3);
					}
					if($onlyOneSection) $arElementSections = array();
				}
			}
		}
		
		if(is_array($arParams['PARENTFIELDS']))
		{
			$arElement2 = array_merge($arElement2, $arParams['PARENTFIELDS']);
		}
		return $arElementSections;
	}
	
	public function GetConvertedPrice($v, $currency, $elemParamKey)
	{
		if(is_array($elemParamKey) || empty($elemParamKey)) $arSettings = $elemParamKey;
		else $arSettings = $this->customFieldSettings[$elemParamKey];
		if(strlen(trim($v)) > 0 && $this->bCurrency && is_array($arSettings))
		{
			if(isset($arSettings['PRICE_CONVERT_CURRENCY']) && $arSettings['PRICE_CONVERT_CURRENCY']=='Y' && isset($arSettings['PRICE_CONVERT_CURRENCY_TO']) && $arSettings['PRICE_CONVERT_CURRENCY_TO'])
			{
				$v = CCurrencyRates::ConvertCurrency($v, $currency, $arSettings['PRICE_CONVERT_CURRENCY_TO']);
				$currency = $arSettings['PRICE_CONVERT_CURRENCY_TO'];
			}
			$showCurrency = (bool)(isset($arSettings['PRICE_SHOW_CURRENCY']) && $arSettings['PRICE_SHOW_CURRENCY']=='Y');
			$useLangSettings = (bool)(isset($arSettings['PRICE_USE_LANG_SETTINGS']) && $arSettings['PRICE_USE_LANG_SETTINGS']=='Y');
			if($useLangSettings)
			{
				$v = CCurrencyLang::CurrencyFormat($v, $currency);
				if(!$showCurrency)
				{
					$arFormat = CCurrencyLang::GetCurrencyFormat($currency);
					$arParts = explode('#', $arFormat['FORMAT_STRING']);
					$part1 = current($arParts);
					$part2 = end($arParts);
					if(strlen($part1) > 0) $v = substr($v, strlen($part1));
					if(strlen($part2) > 0) $v = substr($v, 0, -strlen($part2));
				}
			}
			elseif($showCurrency)
			{
				$arFormat = CCurrencyLang::GetCurrencyFormat($currency);
				$arParts = explode('#', $arFormat['FORMAT_STRING']);
				$part1 = current($arParts);
				$part2 = end($arParts);
				$v = $part1.$v.$part2;
			}
		}
		return $v;
	}
	
	public function GetSectionsData(&$arData, $arParams)
	{
		$arFilter = $arParams['FILTER'];
		$arSkuFilter = $arParams['SKU_FILTER'];
		$arNavParams = (is_array($arParams['NAV_PARAMS']) ? $arParams['NAV_PARAMS'] : false);
		$arAllFields = $arParams['FIELDS'];
		
		/*IE_SECTION_PATH*/
		$arFieldsSections = array();
		foreach($arAllFields as $field)
		{
			if(strpos($field, 'ISECT')===0)
			{
				$arSect = explode('_', substr($field, 5), 2);
				if(strlen($arSect[0])==0) $arSect[0] = 0;
				$arFieldsSections[$arSect[0]][] = $arSect[1];
			}
		}
		ksort($arFieldsSections);

		$arResult = $this->GetSectionsLevelData($arFilter, $arFieldsSections, $arNavParams);
		$dbResSections = $arResult['dbResSections'];
		$arSubData = $arResult['data'];
		foreach($arSubData as $data)
		{
			$arData[] = $data;
		}
		
		$navRecordCount = $dbResSections->NavRecordCount;
		$navPageCount = $dbResSections->NavPageCount;
		
		$sectionKey = 2;
		if($arNavParams['iNumPage'] && $arNavParams['iNumPage'] < $navPageCount)
		{
			$sectionKey = 1;
		}
		
		return array(
			'navRecordCount' => $navRecordCount,
			'navPageCount' => $navPageCount,
			'sectionKey' => $sectionKey,
			'sectionCount' => 1
		);
	}
	
	public function GetSectionsLevelData($arFilter, $arFieldsSections, $arNavParams)
	{
		$arData = array();
		$arKeys = array_keys($arFieldsSections);
		$currentKey = array_shift($arKeys);
		$arSelectField = $arFieldsSections[$currentKey];
		unset($arFieldsSections[$currentKey]);
		$arUserFields = $this->GetSectionUserFields($arFilter['IBLOCK_ID']);
		
		$arFilter2 = array_merge($arFilter, ($currentKey > 0 ? array('DEPTH_LEVEL'=>$currentKey) : array()));
		if((array_key_exists('<=LEFT_MARGIN', $arFilter2) || array_key_exists('>=RIGHT_MARGIN', $arFilter2)) && array_key_exists('DEPTH_LEVEL', $arFilter2) && class_exists('\Bitrix\Iblock\SectionTable'))
		{
			if($arTmpSection = \Bitrix\Iblock\SectionTable::getList(array('filter'=>$arFilter2, 'select'=>array('ID'), 'limit'=>1))->Fetch())
			{
				$arFilter2['ID'] = $arTmpSection['ID'];
				if(array_key_exists('<=LEFT_MARGIN', $arFilter2)) unset($arFilter2['<=LEFT_MARGIN']);
				if(array_key_exists('>=RIGHT_MARGIN', $arFilter2)) unset($arFilter2['>=RIGHT_MARGIN']);
			}
		}
		$dbResSections = CIblockSection::GetList(array('LEFT_MARGIN'=>'ASC'), $arFilter2, false, array_merge($arSelectField, array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'DEPTH_LEVEL', 'LEFT_MARGIN', 'RIGHT_MARGIN')), $arNavParams);
		while($arSection = $dbResSections->GetNext())
		{
			$this->GetSectionIpropTemplates($arSection, $arSelectField);
			$arSectionData = array();
			foreach($arSection as $key=>$val)
			{
				if(in_array($key, $arSelectField))
				{
					if($key=='PICTURE' || $key=='DETAIL_PICTURE' || (isset($arUserFields[$key]) && $arUserFields[$key]['USER_TYPE_ID']=='file'))
					{
						if(is_array($val))
						{
							foreach($val as $k=>$v)
							{
								$val[$k] = $this->GetFileValue($val[$k]);
							}
						}
						else
						{
							$val = $this->GetFileValue($val);
						}
					}
					$arSectionData['ISECT'.($currentKey > 0 ? $currentKey : '').'_'.$key] = $val;
				}
			}

			$isSubData = false;
			$arFieldsSections2 = $arFieldsSections;
			if(!empty($arFieldsSections2) && $currentKey==0)
			{
				foreach($arFieldsSections2 as $k=>$v)
				{
					if($k > $arSection['DEPTH_LEVEL'])
					{
						unset($arFieldsSections2[$k]);
					}
				}
			}
			if(!empty($arFieldsSections2))
			{
				if($currentKey > 0)
				{
					$arFilter['>LEFT_MARGIN'] = $arSection['LEFT_MARGIN'];
					$arFilter['<RIGHT_MARGIN'] = $arSection['RIGHT_MARGIN'];
				}
				else
				{
					$arFilter['<=LEFT_MARGIN'] = $arSection['LEFT_MARGIN'];
					$arFilter['>=RIGHT_MARGIN'] = $arSection['RIGHT_MARGIN'];
				}
				$arResult = $this->GetSectionsLevelData($arFilter, $arFieldsSections2, false);
				$arSubData = $arResult['data'];
				if(!empty($arSubData))
				{
					$isSubData = true;
					foreach($arSubData as $data)
					{
						$arData[] = array_merge($arSectionData, $data);
					}
				}
			}
			if(!$isSubData)
			{
				$arData[] = $arSectionData;
			}
		}
		return array('dbResSections' => $dbResSections, 'data' => $arData);
	}
	
	public function GetSectionUserFields($IBLOCK_ID)
	{
		if(!$IBLOCK_ID) return array();
		if(!isset($this->sectionUserFields[$IBLOCK_ID]))
		{
			$arFields = array();
			$dbRes = CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>'IBLOCK_'.$IBLOCK_ID.'_SECTION'));
			while($arr = $dbRes->Fetch())
			{
				$arFields[$arr['FIELD_NAME']] = $arr;
			}
			$this->sectionUserFields[$IBLOCK_ID] = $arFields;
		}
		return $this->sectionUserFields[$IBLOCK_ID];
	}
	
	public function GetElementOrder($IBLOCK_ID)
	{
		$arProps = $this->GetIblockProperties($IBLOCK_ID);
		$listIndex = $this->listIndex;
		$arOrder = array();
		$arSort = array_map('trim', explode('=>', $this->params['SORT'][$listIndex]));
		if($arSort[0])
		{
			$sortField = $arSort[0];
			$sortOrder = (ToUpper($arSort[1])=='DESC' ? 'DESC' : 'ASC');
			if(strpos($sortField, 'IE_')===0)
			{
				$arOrder[substr($sortField, 3)] = $sortOrder;
			}
			elseif(strpos($sortField, 'IP_PROP')===0)
			{
				$propId = substr($sortField, 7);
				if($arProps[$propId]['PROPERTY_TYPE']=='E')
				{
					$arOrder['PROPERTY_'.$propId.'.NAME'] = $sortOrder;
				}
				else
				{
					$arOrder['PROPERTY_'.$propId] = $sortOrder;
				}
			}
			elseif(strpos($sortField, 'ICAT_PRICE')===0)
			{
				$arFieldParts = explode('_', substr($sortField, 10), 2);
				$arOrder['CATALOG_'.$arFieldParts[1].'_'.$arFieldParts[0]] = $sortOrder;
			}
			elseif(strpos($sortField, 'ICAT_')===0)
			{
				$arOrder['CATALOG_'.substr($sortField, 5)] = $sortOrder;
			}
			elseif(strpos($sortField, 'ISECT')===0)
			{
				$arOrder['IBLOCK_SECTION_ID'] = $sortOrder;
			}
		}
		if(count($arOrder)==0) $arOrder = array('NAME'=>'ASC', 'ID'=>'ASC');
		if(!isset($arOrder['ID'])) $arOrder['ID'] = 'ASC';
		return $arOrder;
	}
	
	public function GetParentSections($ID)
	{
		$arParentSections = array();
		$parentId = $ID;
		while($parentId)
		{
			$arSelectFields = array('ID', 'NAME', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL');
			$dbRes = CIblockSection::GetList(array(), array('ID'=>$parentId), false, $arSelectFields, array('nTopCount'=>1));
			if($arSection = $dbRes->Fetch())
			{
				$arParentSections[$arSection['DEPTH_LEVEL']] = $arSection;
				$this->stepparams['parentSections'][$arSection['DEPTH_LEVEL']] = $arSection['ID'];
				if($arSection['DEPTH_LEVEL'] > 1 && (!isset($this->stepparams['parentSections'][$arSection['DEPTH_LEVEL'] - 1]) || $this->stepparams['parentSections'][$arSection['DEPTH_LEVEL'] - 1]!=$arSection['IBLOCK_SECTION_ID']))
				{
					$parentId = $arSection['IBLOCK_SECTION_ID'];
				}
				else
				{
					$parentId = false;
				}
			}
			else
			{
				$parentId = false;
			}
		}
		$arParentSections = array_reverse($arParentSections, true);
		return $arParentSections;
	}
	
	public function GetSectWithParents($ID)
	{
		if(!isset($this->sectWithParents)) $this->sectWithParents = array();
		if(!isset($this->sectWithParents[$ID]))
		{
			$arSections = array();
			$parentId = $ID;
			while($parentId)
			{
				$arSelectFields = array('ID', 'IBLOCK_SECTION_ID');
				$dbRes = CIblockSection::GetList(array(), array('ID'=>$parentId), false, $arSelectFields, array('nTopCount'=>1));
				if($arSection = $dbRes->Fetch())
				{
					$arSections[] = $arSection['ID'];
					if($arSection['IBLOCK_SECTION_ID'] > 0)
					{
						$parentId = $arSection['IBLOCK_SECTION_ID'];
					}
					else
					{
						$parentId = false;
					}
				}
				else
				{
					$parentId = false;
				}
			}
			$this->sectWithParents[$ID] = array_reverse($arSections);
		}
		return $this->sectWithParents[$ID];
	}
	
	public function GetSectionPath($ID)
	{
		if(!isset($this->sectionPaths[$ID]))
		{
			$curLevel = 1;
			$parentId = $ID;
			$arSectionNames = array();
			while($curLevel > 0)
			{
				$arSelectFields = array('ID', 'NAME', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL');
				$dbRes = CIblockSection::GetList(array(), array('ID'=>$parentId), false, $arSelectFields, array('nTopCount'=>1));
				if($arSection = $dbRes->Fetch())
				{
					$arSectionNames[$arSection['DEPTH_LEVEL']] = $arSection['NAME'];
					$parentId = (int)$arSection['IBLOCK_SECTION_ID'];
					$curLevel = (int)$arSection['DEPTH_LEVEL'];
				}
				else
				{
					$curLevel = 0;
				}
			}
			ksort($arSectionNames, SORT_NUMERIC);
			
			$separator = trim($this->params['DISPLAY_PARAMS'][$this->listIndex]['SECTION_PATH']['SECTION_PATH_SEPARATOR']);
			if(!$separator) $separator = '/';
			$separator = ' '.$separator.' ';			
			$this->sectionPaths[$ID] = implode($separator, $arSectionNames);
		}
		return $this->sectionPaths[$ID];
	}
	
	public function GetSelectSections($arFilter, $arParams)
	{
		$arSectionIds = array();
		$arSections = array();
		if($arParams['TYPE'] != 'OFFER')
		{
			if(!isset($this->sepSectionIds))
			{
				/*if($this->params['EXPORT_SEP_SECTIONS']=='Y')
				{
					$dbRes = CIblockElement::GetList(array(), $arFilter, array('IBLOCK_SECTION_ID'), false, array('IBLOCK_SECTION_ID'));
					while($arr = $dbRes->Fetch())
					{
						$arSectionIds[] = $arr['IBLOCK_SECTION_ID'];
					}
				}*/
		
				//if(!empty($arSectionIds))
				if($this->params['EXPORT_SEP_SECTIONS']=='Y')
				{
					$arSort = array('LEFT_MARGIN'=>'ASC');
					if($arFilter['SECTION_ID'] > 0 || (is_array($arFilter['SECTION_ID']) && count($arFilter['SECTION_ID']) > 0))
					{
						if($arFilter['INCLUDE_SUBSECTIONS']=='Y')
						{
							$dbResMain = CIblockSection::GetList($arSort, array('IBLOCK_ID'=>$arFilter['IBLOCK_ID'], 'ID'=>$arFilter['SECTION_ID'], 'GLOBAL_ACTIVE'=>'Y'), false, array('LEFT_MARGIN', 'RIGHT_MARGIN'));
							while($arMainSect = $dbResMain->Fetch())
							{
								$dbRes = CIblockSection::GetList($arSort, array('IBLOCK_ID'=>$arFilter['IBLOCK_ID'], '>=LEFT_MARGIN'=>$arMainSect['LEFT_MARGIN'], '<=RIGHT_MARGIN'=>$arMainSect['RIGHT_MARGIN'], 'GLOBAL_ACTIVE'=>'Y'), false, array('ID', 'NAME'));
								while($arr = $dbRes->Fetch()) $arSections[$arr['ID']] = $arr;
							}
						}
						else
						{
							$dbRes = CIblockSection::GetList($arSort, array('IBLOCK_ID'=>$arFilter['IBLOCK_ID'], 'ID'=>$arFilter['SECTION_ID'], 'GLOBAL_ACTIVE'=>'Y'), false, array('ID', 'NAME'));
							while($arr = $dbRes->Fetch()) $arSections[$arr['ID']] = $arr;
						}
					}
					else
					{
						$dbRes = CIblockSection::GetList($arSort, array(/*'ID'=>$arSectionIds*/'IBLOCK_ID'=>$arFilter['IBLOCK_ID'], 'GLOBAL_ACTIVE'=>'Y'), false, array('ID', 'NAME'));
						while($arr = $dbRes->Fetch()) $arSections[$arr['ID']] = $arr;
					}

					if(!empty($arSections) && strlen($this->params['EXPORT_SEP_SECTIONS_SORT']) > 0)
					{
						$arNewSections = array();
						$this->GetSectionsStruct($arNewSections, $arSections, $this->params['EXPORT_SEP_SECTIONS_SORT']);
						$arSections = $arNewSections;
					}
				}
				$arSections = array_values($arSections);
				$this->sepSectionIds = $arSections;
			}
			else
			{
				$arSections = $this->sepSectionIds;
			}
		}
		if(empty($arSections)) $arSections[] = array();
		return $arSections;
	}
	
	public function GetSectionsStruct(&$arNewSections, &$arSections, $sortBy='NAME', $parentId=0)
	{
		if(empty($arSections)) return;
		$arFilter = array('ID'=>$arSections);
		if($parentId > 0) $arFilter['SECTION_ID'] = $parentId;
		$dbRes = CIblockSection::GetList(array('DEPTH_LEVEL'=>'ASC', $sortBy=>'ASC'), $arFilter, false, array('ID', 'NAME', 'LEFT_MARGIN', 'RIGHT_MARGIN'));
		while($arr = $dbRes->Fetch())
		{
			if(!isset($arSections[$arr['ID']])) continue;
			unset($arSections[$arr['ID']]);
			$arNewSections[$arr['ID']] = array('ID'=>$arr['ID'], 'NAME'=>$arr['NAME']);
			if($arr['RIGHT_MARGIN'] - $arr['LEFT_MARGIN'] > 1)
			{
				$this->GetSectionsStruct($arNewSections, $arSections, $sortBy, $arr['ID']);
			}
		}
	}
	
	public function GetElementSectionShare(&$arElement2, $arElement, $arElementNameFields, $arFieldsSections)
	{
		$baseSectionId = $arElement['IBLOCK_SECTION_ID'];
		$this->GetElementSection($arElement2, $arElement, $arElementNameFields, $arFieldsSections);
		
		$needSectionPath = (bool)(in_array('SECTION_PATH', $arElementNameFields));
		if($needSectionPath && $arElement['IBLOCK_SECTION_ID'])
		{
			$arElement3 = $arElement2;
			if(is_callable(array('\Bitrix\Iblock\SectionElementTable', 'getList')))
			{
				$dbRes = \Bitrix\Iblock\SectionElementTable::getList(array('filter'=>array('IBLOCK_ELEMENT_ID'=>$arElement['ID'], 'ADDITIONAL_PROPERTY_ID'=>false), 'select'=>array('ID'=>'IBLOCK_SECTION_ID')));
			}
			else
			{
				$dbRes = CIBlockElement::GetElementGroups($arElement['ID'], true, array('ID'));
			}
			while($arSect = $dbRes->Fetch())
			{
				if($arSect['ID']!=$baseSectionId)
				{
					$arElement['IBLOCK_SECTION_ID'] = $arSect['ID'];
					$this->GetElementSection($arElement3, $arElement, $arElementNameFields, $arFieldsSections);
				}
			}
			$arElement2['IE_SECTION_PATH'] = $arElement3['IE_SECTION_PATH'];
		}
	}
	
	public function GetElementSection(&$arElement2, $arElement, $arElementNameFields, $arFieldsSections)
	{
		$needSectionPath = (bool)(in_array('SECTION_PATH', $arElementNameFields));
		if((!empty($arFieldsSections) || $needSectionPath) && $arElement['IBLOCK_SECTION_ID'])
		{
			$arUserFields = $this->GetSectionUserFields($arElement['IBLOCK_ID']);
			if($needSectionPath) $minLevel = 1;
			else $minLevel = max(min(array_keys($arFieldsSections)), 1);
			$curLevel = 0;
			$arSectionNames = array();
			/*$dbRes2 = CIblockSection::GetList(array(), array('ID'=>$arElement['IBLOCK_SECTION_ID']), false, array('ID', 'DEPTH_LEVEL'), array('nTopCount'=>1));
			if($arSection = $dbRes2->Fetch())*/
			if($arSection = $this->GetSectionFromCache(array('ID'=>$arElement['IBLOCK_SECTION_ID']), array('ID', 'DEPTH_LEVEL')))
			{
				$curLevel = $arSection['DEPTH_LEVEL'];
			}
			$elemLevel = $curLevel;
			if(isset($arFieldsSections[0]) && is_array($arFieldsSections[0]))
			{
				if(!isset($arFieldsSections[$elemLevel]) || !is_array($arFieldsSections[$elemLevel]))
				{
					$arFieldsSections[$elemLevel] = array();
				}
				$arFieldsSections[$elemLevel] = array_merge($arFieldsSections[0], $arFieldsSections[$elemLevel]);
			}
			$parentId = $arElement['IBLOCK_SECTION_ID'];
			while($curLevel >= $minLevel)
			{
				$arSelectFields = array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL');
				if(is_array($arFieldsSections[$curLevel])) $arSelectFields = array_merge($arSelectFields, $arFieldsSections[$curLevel]);				
				/*$dbRes2 = CIblockSection::GetList(array(), array('ID'=>$parentId, 'IBLOCK_ID'=>$arElement['IBLOCK_ID']), false, $arSelectFields, array('nTopCount'=>1));
				if($arSection = $dbRes2->GetNext())*/
				if($arSection = $this->GetSectionFromCache(array('ID'=>$parentId, 'IBLOCK_ID'=>$arElement['IBLOCK_ID']), $arSelectFields))
				{
					$this->GetSectionIpropTemplates($arSection, $arSelectFields);
					foreach($arSection as $k=>$v)
					{
						if(strpos($k, '~')===0)
						{
							$arSection[substr($k, 1)] = $v;
						}
					}
					if(is_array($arFieldsSections[$curLevel]))
					{
						foreach($arFieldsSections[$curLevel] as $key)
						{
							$val = $arSection[$key];
							if($key=='PICTURE' || $key=='DETAIL_PICTURE' || (isset($arUserFields[$key]) && $arUserFields[$key]['USER_TYPE_ID']=='file'))
							{
								if(is_array($val))
								{
									foreach($val as $k=>$v)
									{
										$val[$k] = $this->GetFileValue($val[$k]);
									}
								}
								else
								{
									$val = $this->GetFileValue($val);
								}
							}
							$arElement2['ISECT'.$arSection['DEPTH_LEVEL'].'_'.$key] = $val;
							if($elemLevel==$arSection['DEPTH_LEVEL'])
							{
								$arElement2['ISECT_'.$key] = $val;
							}
						}
					}
					$arSectionNames[$curLevel] = $arSection['NAME'];
				}
				$parentId = (int)$arSection['IBLOCK_SECTION_ID'];
				$curLevel--;
			}
			if($needSectionPath && !empty($arSectionNames))
			{
				ksort($arSectionNames, SORT_NUMERIC);
				if(isset($this->customFieldSettings['IE_SECTION_PATH']['SECTION_PATH_SEPARATOR']) && strlen($this->customFieldSettings['IE_SECTION_PATH']['SECTION_PATH_SEPARATOR']) > 0) $separator = $this->customFieldSettings['IE_SECTION_PATH']['SECTION_PATH_SEPARATOR'];
				else $separator = '/';
				if(!is_array($arElement2['IE_SECTION_PATH'])) $arElement2['IE_SECTION_PATH'] = array();
				$arElement2['IE_SECTION_PATH'][] = implode(' '.$separator.' ', $arSectionNames);
			}
		}
	}
	
	public function GetSectionIpropTemplates(&$arSection, $arSelectFields)
	{
		$arIpropTempKeys = preg_grep('/^IPROP_TEMP_/', $arSelectFields);
		$arIpropTempKeys = array_map(create_function('$k', 'return substr($k, 11);'), $arIpropTempKeys);
		$arIpropTempKeys2 = preg_grep('/^TEMPLATE_/', $arIpropTempKeys);
		$arIpropTempKeys = array_diff($arIpropTempKeys, $arIpropTempKeys2);
		$arIpropTempKeys2 = array_map(create_function('$k', 'return substr($k, 9);'), $arIpropTempKeys2);
		if(!empty($arIpropTempKeys))
		{
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection['IBLOCK_ID'], $arSection['ID']);
			$arTemplates = $ipropValues->getValues();
			foreach($arIpropTempKeys as $v)
			{
				if(isset($arTemplates[$v])) $arSection['IPROP_TEMP_'.$v] = $arTemplates[$v];
				else $arSection['IPROP_TEMP_'.$v] = '';
			}
		}
		if(!empty($arIpropTempKeys2))
		{
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionTemplates($arSection['IBLOCK_ID'], $arSection['ID']);
			$arTemplates = $ipropValues->findTemplates();
			foreach($arIpropTempKeys2 as $v)
			{
				if(isset($arTemplates[$v])) $arSection['IPROP_TEMP_TEMPLATE_'.$v] = $arTemplates[$v]['TEMPLATE'];
				else $arSection['IPROP_TEMP_TEMPLATE_'.$v] = '';
			}
		}
		
		$sectPropKey = 'SECTION_PROPERTIES';
		if(in_array($sectPropKey, $arSelectFields) && class_exists('\Bitrix\Iblock\SectionPropertyTable'))
		{
			$arCodes = array();
			$dbRes = \Bitrix\Iblock\SectionPropertyTable::getList(array('select' => array('PROPERTY_ID'), 'filter' => array('=IBLOCK_ID' => $arSection['IBLOCK_ID'], 'SECTION_ID'=>$arSection['ID'])));
			while($arr = $dbRes->Fetch())
			{
				$arProp = $this->GetCachedProperty($arr['PROPERTY_ID']);
				$arCodes[] = (strlen($arProp['CODE']) > 0 ? $arProp['CODE'] : $arr['PROPERTY_ID']);
			}
			$arSection[$sectPropKey] = implode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arCodes);
		}
		
		$sectPathKey = 'PATH_NAMES';
		if(in_array($sectPathKey, $arSelectFields))
		{
			$curLevel = $arSection['DEPTH_LEVEL'];
			$parentId = $arSection['IBLOCK_SECTION_ID'];
			$arSectionNames = array($curLevel=>$arSection['NAME']);
			while($curLevel >= 1)
			{
				$curLevel--;
				if($arSection2 = $this->GetSectionFromCache(array('ID'=>$parentId, 'IBLOCK_ID'=>$arSection['IBLOCK_ID']), array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL')))
				{
					$arSectionNames[$curLevel] = $arSection2['NAME'];
				}
				$parentId = (int)$arSection2['IBLOCK_SECTION_ID'];
			}
			ksort($arSectionNames, SORT_NUMERIC);
			$arSection[$sectPathKey] = implode(' / ', $arSectionNames);
		}
	}
	
	public function IsPictureField($field)
	{
		$isOffer = false;
		if(strpos($field, 'OFFER_')===0)
		{
			$field = substr($field, 6);
			$isOffer = true;
		}

		$isPicture = false;
		if(in_array($field, array('IE_PREVIEW_PICTURE', 'IE_DETAIL_PICTURE', 'IE_QR_CODE_IMAGE')) || preg_match('/^ISECT\d*(_DETAIL)?_PICTURE$/', $field)) $isPicture = true;
		if(!$isPicture && strpos($field, 'IP_PROP')===0)
		{
			$propId = substr($field, 7);
			$arProp = $this->GetCachedProperty($propId);
			$isPicture = (bool)($arProp['PROPERTY_TYPE']=='F');
		}
		return $isPicture;
	}
	
	public function IsMultipleField($field)
	{
		$isOffer = false;
		if(strpos($field, 'OFFER_')===0)
		{
			$field = substr($field, 6);
			$isOffer = true;
		}
		
		$isMultiple = false;
		if(in_array($field, array('IE_SECTION_PATH'))) $isMultiple = true;
		if(!$isMultiple && strpos($field, 'IP_PROP')===0)
		{
			$propId = substr($field, 7);
			$arProp = $this->GetCachedProperty($propId);
			$isMultiple = (bool)($arProp['MULTIPLE']=='Y');
		}
		return $isMultiple;
	}
	
	public function GetCachedProperty($propId)
	{
		if(!isset($this->dataProps)) $this->dataProps = array();
		if(!isset($this->dataProps[$propId]))
		{
			$dbRes = CIBlockProperty::GetList(array(), array('ID'=>$propId));
			if($arProp = $dbRes->Fetch())
			{
				$this->dataProps[$propId] = $arProp;
			}
		}
		return $this->dataProps[$propId];
	}
	
	public function GetCachedOfferIblock($IBLOCK_ID)
	{
		if(!$this->iblockoffers || !isset($this->iblockoffers[$IBLOCK_ID]))
		{
			$this->iblockoffers[$IBLOCK_ID] = CKDAExportUtils::GetOfferIblock($IBLOCK_ID, true);
		}
		return $this->iblockoffers[$IBLOCK_ID];
	}
	
	public function GetBasePriceId()
	{
		if(!$this->catalogBasePriceId)
		{
			$arBasePrice = CCatalogGroup::GetBaseGroup();
			$this->catalogBasePriceId = $arBasePrice['ID'];
		}
		return $this->catalogBasePriceId;
	}
	
	public function GetNumberOperation(&$val, $op)
	{
		if($op=='eq') return '=';
		elseif($op=='gt') return '>';
		elseif($op=='geq') return '>=';
		elseif($op=='lt') return '<';
		elseif($op=='leq') return '<=';
		elseif($op=='from_to')
		{
			$val = array_map('trim', explode('-', $val));
			return '><';
		}
		elseif($op=='empty')
		{
			$val = false;
			return '';
		}
		else return '';
	}
	
	public function GetStringOperation(&$val, $op)
	{
		if($op=='eq') return '=';
		elseif($op=='neq') return '!=';
		elseif($op=='contain') return '%';
		elseif($op=='not_contain') return '!%';
		elseif($op=='logical') return '?';
		elseif($op=='empty')
		{
			$val = false;
			return '';
		}
		elseif($op=='not_empty')
		{
			$val = false;
			return '!';
		}
		else return '';
	}
	
	public function GetCalculatedValue($val)
	{
		try{
			if($this->params['ELEMENT_NOT_LOAD_FORMATTING']=='Y') $val = $val->getCalculatedValue();
			else $val = $val->getFormattedValue();
		}catch(Exception $ex){}
		return self::CorrectCalculatedValue($val);
	}
	
	public static function CorrectCalculatedValue($val)
	{
		$val = str_ireplace('_x000D_', '', $val);
		if((!defined('BX_UTF') || !BX_UTF) && CUtil::DetectUTF8($val)/*function_exists('mb_detect_encoding') && (mb_detect_encoding($val) == 'UTF-8')*/)
		{
			$val = strtr($val, array('Ø'=>'&#216;', '™'=>'&#153;', '®'=>'&#174;', '©'=>'&#169;'));
			$val = utf8win1251($val);
		}
		return $val;
	}
	
	public function GetFloatVal($val)
	{
		return floatval(preg_replace('/[^\d\.]+/', '', str_replace(',', '.', $val)));
	}
	
	public function GetDateVal($val)
	{
		$time = strtotime($val);
		if($time > 0)
		{
			return ConvertTimeStamp($time, 'FULL');
		}
		return false;
	}
	
	public function GetUserByGroups($arGroups)
	{
		if(empty($arGroups)) return 0;
		$xmlId = 'kda_groups_'.implode('_', $arGroups);
		
		if(!isset($this->usersByGroups)) $this->usersByGroups = array();
		if(!isset($this->usersByGroups[$xmlId]))
		{
			$userId = 0;
			$dbRes = \CUser::GetList(($by='ID'), ($order='ASC'), array('XML_ID'=>$xmlId), array('FIELDS'=>array('ID')));
			if($arUser = $dbRes->Fetch())
			{
				$userId = $arUser['ID'];
			}
			else
			{
				$pass = substr(md5(mt_rand()), 0, 8).'.*aA2';
				$arFieldsUser = array(
					'XML_ID' => $xmlId,
					'LOGIN' => $xmlId,
					'EMAIL' => $xmlId.'@nodomain.com',
					'PASSWORD' => $pass,
					'CONFIRM_PASSWORD' => $pass,
					'GROUP_ID' => $arGroups
				);
				$user = new \CUser;
				if($id = $user->Add($arFieldsUser))
				{
					$userId = $id;
				}
			}
			$this->usersByGroups[$xmlId] = $userId;
		}
		return $this->usersByGroups[$xmlId];
	}
	
	public function GetUserField($ID, $field)
	{
		if(!$ID) return '';
		if(!isset($this->bUserFields)) $this->bUserFields = array();
		$fieldKey = $ID.'|'.$field;
		if(!isset($this->bUserFields[$fieldKey]))
		{
			$arFields = array_diff(array_map('trim', explode(' ', $field)), array(''));
			$arUser = \Bitrix\Main\UserTable::GetList(array('filter'=>array('ID'=>$ID), 'select'=>$arFields))->Fetch();
			$arVals = array();
			foreach($arFields as $subfield)
			{
				$arVals[] = $arUser[$subfield];
			}
			$this->bUserFields[$fieldKey] = implode(' ', $arVals);
		}
		return $this->bUserFields[$fieldKey];
	}
	
	public function GetSectionFromCache($arFilter=array(), $arSelect=array())
	{
		if($this->sectionCacheSize > 10*1024*1024)
		{
			$this->sectionCache = array();
			$this->sectionCacheSize = 0;
		}
		$hash = md5(serialize(array('FILTER'=>$arFilter, 'SELECT'=>$arSelect)));
		if(!array_key_exists($hash, $this->sectionCache))
		{
			if(class_exists('\Bitrix\Iblock\SectionTable') && count(array_diff($arSelect, array('ID', 'MODIFIED_BY', 'CREATED_BY', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'ACTIVE', 'GLOBAL_ACTIVE', 'SORT', 'NAME', 'PICTURE', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL', 'DESCRIPTION', 'DESCRIPTION_TYPE', 'SEARCHABLE_CONTENT', 'CODE', 'XML_ID', 'TMP_ID', 'DETAIL_PICTURE', 'SOCNET_GROUP_ID')))==0)
			{
				$dbRes = \Bitrix\Iblock\SectionTable::GetList(array('filter' => $arFilter, 'select'=> $arSelect, 'limit'=>1));
			}
			else
			{
				$dbRes = \CIblockSection::GetList(array(), $arFilter, false, $arSelect, array('nTopCount'=>1));
			}
			if(in_array('SECTION_PAGE_URL', $arSelect) && is_callable(array($dbRes, 'GetNext'))) $arSection = $dbRes->GetNext();
			else $arSection = $dbRes->Fetch();
			$this->sectionCache[$hash] = $arSection;
			$this->sectionCacheSize += strlen($hash.serialize($arSection));
		}
		return $this->sectionCache[$hash];
	}
	
	public function AddDateFilter(&$arFilter, $arAddFilter, $field1, $field2, $addField)
	{
		/*if(isset($arAddFilter[$addField.'_from_FILTER_PERIOD']) && in_array($arAddFilter[$addField.'from_FILTER_PERIOD'], array('day', 'week', 'month', 'quarter', 'year'))
			&& isset($arAddFilter[$addField.'_from_FILTER_DIRECTION']) && in_array($arAddFilter[$addField.'from_FILTER_PERIOD'], array('previous', 'current', 'next')))
		{}*/
		if($arAddFilter[$addField.'_from_FILTER_PERIOD']=='last_days'
			&& isset($arAddFilter[$addField.'_from_FILTER_LAST_DAYS']) && strlen(trim($arAddFilter[$addField.'_from_FILTER_LAST_DAYS'])) > 0)
		{
			$days = (int)trim($arAddFilter[$addField.'_from_FILTER_LAST_DAYS']);
			$arFilter[$field1] = $arAddFilter[$addField.'_from'] = ConvertTimeStamp(time()-$days*24*60*60, "FULL");
		}
		else
		{
			if(!empty($arAddFilter[$addField.'_from'])) $arFilter[$field1] = $arAddFilter[$addField.'_from'];
			if(!empty($arAddFilter[$addField.'_to'])) $arFilter[$field2] = CIBlock::isShortDate($arAddFilter[$addField.'_to'])? ConvertTimeStamp(AddTime(MakeTimeStamp($arAddFilter[$addField.'_to']), 1, "D"), "FULL"): $arAddFilter[$addField.'_to'];
		}
	}
	
	public function Str2Url($string, $arParams=array())
	{
		if(!is_array($arParams)) $arParams = array();
		if($arParams['TRANSLITERATION']=='Y')
		{
			if(isset($arParams['TRANS_LEN'])) $arParams['max_len'] = $arParams['TRANS_LEN'];
			if(isset($arParams['TRANS_CASE'])) $arParams['change_case'] = $arParams['TRANS_CASE'];
			if(isset($arParams['TRANS_SPACE'])) $arParams['replace_space'] = $arParams['TRANS_SPACE'];
			if(isset($arParams['TRANS_OTHER'])) $arParams['replace_other'] = $arParams['TRANS_OTHER'];
			if(isset($arParams['TRANS_EAT']) && $arParams['TRANS_EAT']=='N') $arParams['delete_repeat_replace'] = false;
		}
		return CUtil::translit($string, LANGUAGE_ID, $arParams);
	}
}
?>