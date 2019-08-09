<?php
require_once(dirname(__FILE__).'/../../lib/PHPExcel/PHPExcel.php');
IncludeModuleLangFile(__FILE__);

class CKDAExportExcelHighload {
	protected static $moduleId = 'kda.exportexcel';
	protected static $moduleSubDir = '';
	public $sectionPaths = array();
	public $parentSections = array();
	
	function __construct($params=array(), $fparams=array(), $stepparams=false, $pid = false)
	{
		$this->params = $params;
		$this->fparams = $fparams;
		$this->maxReadRows = 100;
		$this->fl = new CKDAEEFieldList($params);
		
		if(is_array($stepparams))
		{
			$this->stepparams = $stepparams;
			$this->stepparams['list_number'] = intval($this->stepparams['list_number']);
			$this->stepparams['list_current_page'] = intval($this->stepparams['list_current_page']);
			$this->stepparams['list_last_page'] = intval($this->stepparams['list_last_page']);
			$this->stepparams['total_read_line'] = intval($this->stepparams['total_read_line']);
			$this->stepparams['total_file_line'] = intval($this->stepparams['total_file_line']);
			
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
					$this->params['MAX_EXECUTION_TIME'] = intval(ini_get('max_execution_time')) - 10;
					if($this->params['MAX_EXECUTION_TIME'] < 10) $this->params['MAX_EXECUTION_TIME'] = 10;
					if($this->params['MAX_EXECUTION_TIME'] > 50) $this->params['MAX_EXECUTION_TIME'] = 30;
				}
				$this->params['MAX_EXECUTION_TIME'] = 10;
			}
			
			/*Temp folders*/
			$dir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/'.static::$moduleId.'/'.static::$moduleSubDir;
			CheckDirPath($dir);
			if(!$this->stepparams['tmpdir'])
			{
				$i = 0;
				while(($tmpdir = $dir.$i.'/') && file_exists($tmpdir)){$i++;}
				$this->stepparams['tmpdir'] = $tmpdir;
				CheckDirPath($tmpdir);
			}
			$this->tmpdir = $this->stepparams['tmpdir'];
			$this->imagedir = $this->stepparams['tmpdir'].'images/';
			CheckDirPath($this->imagedir);
			
			$this->tmpfile = $this->tmpdir.'params.txt';
			/*/Temp folders*/
			
			if(file_exists($this->tmpfile))
			{
				$this->stepparams = array_merge($this->stepparams, unserialize(file_get_contents($this->tmpfile)));
			}
			
			if(!isset($this->stepparams['curstep'])) $this->stepparams['curstep'] = 'export';
		
			if($pid!==false)
			{
				$this->procfile = $dir.$pid.'.txt';
				if($this->stepparams['total_read_line'] < 1)
				{
					if(file_exists($this->procfile)) unlink($this->procfile);
				}
			}
		}
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
		$this->timeBegin = time();
		
		$arListIndexes = array(0);
		if(is_array($this->params['LIST_NAME']) && count($this->params['LIST_NAME']) > 0)
		{
			$arListIndexes = array_keys($this->params['LIST_NAME']);
		}
		//$listIndex = $this->stepparams['list_number'];
		$listIndex = 0;
		$page = max(1, $this->stepparams['list_current_page']);
		$lastPage = $this->stepparams['list_last_page'];
		$arFields = $this->GetFieldList($listIndex);
		
		$break = ($lastPage > 0 && $page > $lastPage);
		if(!$break) $this->OpenTmpdataHandler($listIndex);
		while(!$break)
		{
			$arRes = $this->GetExportData($listIndex, $this->maxReadRows, $page);
			$arData = $arRes['DATA'];
			$lastPage = $arRes['PAGE_COUNT'];
			$recordCount = $arRes['RECORD_COUNT'];
			
			if(empty($arData))
			{
				$break = true;
				continue;
			}
			
			foreach($arData as $arElement)
			{
				$this->WriteTmpdata($arElement);
				$this->stepparams['total_read_line']++;
			}
			
			$page++;
			$break = (bool)($lastPage > 0 && $page > $lastPage);
			
			/*if($page > $lastPage)
			{
				$page = 1;
			}*/
			
			$this->stepparams['list_number'] = $listIndex;
			$this->stepparams['list_current_page'] = $page;
			$this->stepparams['list_last_page'] = $lastPage;
			$this->stepparams['total_file_line'] = $recordCount;
			$this->SaveStatusImport();
			if($this->CheckTimeEnding())
			{
				return $this->GetBreakParams();
			}
		}
		
		$this->CloseTmpdataHandler();
		
		$arWriterParams = array(
			'OUTPUTFILE' => $_SERVER['DOCUMENT_ROOT'].$this->params['FILE_PATH'],
			'TMPDIR' => $this->tmpdir,
			'IMAGEDIR' => $this->imagedir,
			'LIST_INDEXES' => $arListIndexes,
			'ROWS' => $this->stepparams['total_read_line'] + 1,
			'EXTRAPARAMS' => $this->fparams[$listIndex],
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
		else
		{
			$objPHPExcel = new KDAPHPExcel();
			$worksheet = $objPHPExcel->getActiveSheet();
			$arCols = range('A', 'Z');
			foreach(range('A', 'Z') as $v1)
			{
				foreach(range('A', 'Z') as $v2)
				{
					$arCols[] = $v1.$v2;
				}
			}
			if($this->params['LIST_NAME'][$listIndex])
			{
				$worksheet->setTitle($this->GetCellValue($this->params['LIST_NAME'][$listIndex]));
			}
			
			$row = 1;
			if(isset($this->params['TEXT_ROWS_TOP'][$listIndex]))
			{
				foreach($this->params['TEXT_ROWS_TOP'][$listIndex] as $k=>$v)
				{
					$worksheet->setCellValueExplicit($arCols[0].$row, $this->GetCellValue($v));
					$row++;
				}
			}

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
			
			if(isset($this->params['TEXT_ROWS_TOP2'][$listIndex]))
			{
				foreach($this->params['TEXT_ROWS_TOP2'][$listIndex] as $k=>$v)
				{
					$worksheet->setCellValueExplicit($arCols[0].$row, $this->GetCellValue($v));
					$row++;
				}
			}
			
			$this->tmpdatafilehandler = fopen($this->tmpdatafile, 'r');
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
			fclose($this->tmpdatafilehandler);
			
			
			//$fn = $_SERVER['DOCUMENT_ROOT'].'/upload/export.'.$this->params['FILE_EXTENSION'];
			$fn = $_SERVER['DOCUMENT_ROOT'].$this->params['FILE_PATH'];
			$writerType = 'CSV';
			if($this->params['FILE_EXTENSION']=='xlsx') $writerType = 'Excel2007';
			elseif($this->params['FILE_EXTENSION']=='xls') $writerType = 'Excel5';
			$objWriter = KDAPHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
			if($writerType == 'CSV')
			{
				//$objWriter->setExcelCompatibility(true);
				$delimiter = ($this->params['CSV_SEPARATOR'] ? $this->params['CSV_SEPARATOR'] : ';');
				$objWriter->setDelimiter($delimiter);
				if($this->params['CSV_ENCODING']=='UTF-8')
				{
					$objWriter->setUseBOM(true);
				}
			}
			$objWriter->save($fn);
		}
		$this->SaveStatusImport(true);
		
		return $this->GetBreakParams('finish');
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
				DeleteDirFilesEx(substr($this->imagedir, strlen($_SERVER['DOCUMENT_ROOT'])));
			}*/
		}
		elseif(file_exists($this->tmpdir))
		{
			DeleteDirFilesEx(substr($this->tmpdir, strlen($_SERVER['DOCUMENT_ROOT'])));
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
		$file = trim($file);
		if(strpos($file, '/')===0)
		{
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$file))
			{
				$arFile = CFile::MakeFileArray($file);
				$ext = '.jpg';
				if(preg_match('/\.[^\.]{2,5}$/', $arFile['name'], $m))
				{
					$ext = ToLower($m[0]);
				}
				$bExists = true;
				while($bExists)
				{
					$file = $this->imagedir.md5(mt_rand()).$ext;
					$bExists = file_exists($file);
				}
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
	
	public function GetFieldValue($arProp, $val)
	{
		if($this->IsPictureField($arProp['FIELD_NAME']))
		{
			$val = $this->GetFileValue($val);
		}
		elseif($arProp['USER_TYPE_ID']=='iblock_element')
		{
			$val = $this->GetFieldElementValue($arProp, $val);
		}
		elseif($arProp['USER_TYPE_ID']=='iblock_section')
		{
			$val = $this->GetFieldSectionValue($arProp, $val);
		}
		elseif($arProp['USER_TYPE_ID']=='hlblock')
		{
			$val = $this->GetHighloadBlockValue($arProp, $val);
		}
		elseif($arProp['USER_TYPE_ID']=='enumeration')
		{
			$val = $this->GetFieldEnumValue($arProp, $val);
		}
		
		return $val;
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
	
	public function GetFieldEnumValue($arProp, $val)
	{
		if($val)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = CUserFieldEnum::GetList(array(), array('ID'=>$val));
				if($arr = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arr['VALUE'];
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
	
	public function GetFieldElementValue($arProp, $val)
	{
		if(strlen($val) > 0)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = CIBlockElement::GetList(array(), array("ID"=>$val), false, false, array('NAME'));
				if($arElem = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arElem['NAME'];
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
	
	public function GetFieldSectionValue($arProp, $val)
	{
		if(strlen($val) > 0)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = CIBlockSection::GetList(array(), array("ID"=>$val), false, array('NAME'));
				if($arSect = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arSect['NAME'];
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
	
	public function GetFileValue($val)
	{
		if(strlen($val) > 0)
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
		return $val;
	}
	
	public function GetFileDescription($val)
	{
		if(strlen($val) > 0)
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
		if(strlen($val) > 0 && CModule::IncludeModule('highloadblock') && $arProp['SETTINGS']['HLBLOCK_ID'] && $arProp['SETTINGS']['HLFIELD_ID'])
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$entityDataClass = $this->GetHighloadBlockClass($arProp['SETTINGS']['HLBLOCK_ID']);
				$arField = CUserTypeEntity::GetList(array(), array('ID'=>$arProp['SETTINGS']['HLFIELD_ID']))->Fetch();
				
				$dbRes2 = $entityDataClass::GetList(array('filter'=>array("ID"=>$val), 'select'=>array('ID', $arField['FIELD_NAME']), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arr2[$arField['FIELD_NAME']];
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
				"file" => $_SERVER["DOCUMENT_ROOT"].Rel2Abs("/", $arDef["WATERMARK_FILE"]),
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
				"font" => $_SERVER["DOCUMENT_ROOT"].Rel2Abs("/", $arDef["WATERMARK_TEXT_FONT"]),
				"color" => $arDef["WATERMARK_TEXT_COLOR"],
			));
		}
		return $arFile;
	}
	
	public function ConversionReplaceValues($m)
	{
		$k = substr($m[0], 1, -1);
		if(1 || isset($this->currentItemValues[$k]))
		{
			return $this->currentItemValues[$k];
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
			$this->currentItemValues = $arItem;
			foreach($arConv as $k=>$v)
			{
				if(($v['WHEN']=='EQ' && $val==$v['FROM'])
					|| ($v['WHEN']=='GT' && $val > $v['FROM'])
					|| ($v['WHEN']=='LT' && $val < $v['FROM'])
					|| ($v['WHEN']=='GEQ' && $val >= $v['FROM'])
					|| ($v['WHEN']=='LEQ' && $val <= $v['FROM'])
					|| ($v['WHEN']=='CONTAIN' && strpos($val, $v['FROM'])!==false)
					|| ($v['WHEN']=='REGEXP' && preg_match('/'.$v['FROM'].'/', $val))
					|| ($v['WHEN']=='EMPTY' && strlen($val)==0)
					|| ($v['WHEN']=='NOT_EMPTY' && strlen($val) > 0)
					|| ($v['WHEN']=='ANY'))
				{
					$this->currentFieldKey = $fieldKey;
					if($v['TO']) $v['TO'] = preg_replace_callback('/(#\S+#)/', array($this, 'ConversionReplaceValues'), $v['TO']);
					if($v['THEN']=='REPLACE_TO') $val = $v['TO'];
					elseif($v['THEN']=='REMOVE_SUBSTRING' && $v['TO']) $val = str_replace($v['TO'], '', $val);
					elseif($v['THEN']=='REPLACE_SUBSTRING_TO' && $v['FROM']) $val = str_replace($v['FROM'], $v['TO'], $val);
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
				}
			}
		}
		return $val;
	}
	
	public function GetExportData($listIndex, $limit=10, $page=1)
	{
		$this->listIndex = $listIndex;
		$arNavParams = false;
		if(is_numeric($limit) && $limit > 0)
		{
			$arNavParams['limit'] = (int)$limit;
			$arNavParams['offset'] = $arNavParams['limit'] * (max(1, $page)-1);
		}
		$hlblId = $this->params['HIGHLOADBLOCK_ID'];
		
		if(!isset($this->filters)) $this->filters = array();
		if(!isset($this->filters[$hlblId]))
		{
			$arFilter = array();
			if($this->params['FILTER'][$listIndex])
			{
				$arAddFilter = $this->params['FILTER'][$listIndex];
				$arFieldsHlbl = $this->fl->GetHigloadBlockFields($hlblId);
				foreach($arFieldsHlbl as $FIELD_NAME=>$arUserField)
				{
					if(
						isset($arAddFilter["find_".$FIELD_NAME])
						&& ((!is_array($arAddFilter["find_".$FIELD_NAME]) && strlen($arAddFilter["find_".$FIELD_NAME])>0) || count(array_diff($arAddFilter["find_".$FIELD_NAME], array(''))) > 0)
						&& $arUserField["SHOW_FILTER"] != "N"
						&& $arUserField["USER_TYPE"]["BASE_TYPE"] != "file"
					)
					{
						$value = $arAddFilter["find_".$FIELD_NAME];
						if($arUserField["SHOW_FILTER"]=="I")
							$arFilter["=".$FIELD_NAME]=$value;
						elseif($arUserField["SHOW_FILTER"]=="S")
							$arFilter["%".$FIELD_NAME]=$value;
						else
							$arFilter[$FIELD_NAME]=$value;
					}
				}
			}
			$this->filters[$hlblId] = $arFilter;
		}
		else
		{
			$arFilter = $this->filters[$hlblId];
		}
		
		$arFields = $this->GetFieldList($listIndex);		
		
		$this->customFieldSettings = array();
		$arFieldsAdded = array();
		if(is_array($this->fparams[$listIndex]))
		{
			foreach($this->fparams[$listIndex] as $fieldIndex=>$arSettings)
			{
				$field = $arFields[$fieldIndex];
				$this->customFieldSettings[$field] = $arSettings;
				if(isset($arSettings['CONVERSION']) && is_array($arSettings['CONVERSION']) && $field)
				{
					foreach($arSettings['CONVERSION'] as $k=>$v)
					{
						if(preg_match_all('/#(\S+)#/', $v['TO'], $m))
						{
							foreach($m[1] as $key)
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
		}
		
		$arAllFields = array_merge($arFields, $arFieldsAdded);
		
		$arData = array();
		$arParams = array(
			'FILTER' => $arFilter,
			'NAV_PARAMS' => $arNavParams,
			'FIELDS' => $arAllFields
		);
		$arResElements = $this->GetElementsData($arData, $arParams);
		
		
		foreach($arData as $k=>$arElementData)
		{
			$arFieldSettings = array();
			if(is_array($this->fparams[$listIndex]))
			{
				foreach($this->fparams[$listIndex] as $fieldIndex=>$arSettings)
				{
					$field = $arFields[$fieldIndex];
					$arFieldSettings[$field] = $arSettings;
					$arFieldSettings[$field.'_'.$fieldIndex] = $arSettings;
					if(isset($arSettings['CONVERSION']) && is_array($arSettings['CONVERSION']) && $field && isset($arElementData[$field]))
					{
						if(is_array($arElementData[$field]))
						{
							foreach($arElementData[$field] as $k2=>$val)
							{
								$arData[$k][$field.'_'.$fieldIndex][$k2] = $arElementData[$field.'_'.$fieldIndex][$k2] = $this->ApplyConversions($val, $arSettings['CONVERSION'], $arElementData);
							}
						}
						else
						{
							$arData[$k][$field.'_'.$fieldIndex] = $arElementData[$field.'_'.$fieldIndex] = $this->ApplyConversions($arElementData[$field], $arSettings['CONVERSION'], $arElementData);
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
							$arFile = $this->GetFileArray($val);
							if($arFile['tmp_name'])
							{
								$maxWidth = ((int)$arSettings['PICTURE_WIDTH'] > 0 ? (int)$arSettings['PICTURE_WIDTH'] : 100);
								$maxHeight = ((int)$arSettings['PICTURE_HEIGHT'] > 0 ? (int)$arSettings['PICTURE_HEIGHT'] : 100);
								$filePath = $arFile['tmp_name'];
								CFile::ResizeImage($arFile, array("width" => $maxWidth, "height" => $maxHeight));
								if($filePath != $arFile['tmp_name'])
								{
									copy($arFile['tmp_name'], $filePath);
								}
								$arVals[$key] = substr($filePath, strlen($this->imagedir));
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
				}
			}

			foreach($arElementData as $k2=>$val)
			{
				if(is_array($val))
				{
					if(isset($arFieldSettings[$k2]) && isset($arFieldSettings[$k2]['CHANGE_MULTIPLE_SEPARATOR']) && $arFieldSettings[$k2]['CHANGE_MULTIPLE_SEPARATOR']=='Y') $separator = $arFieldSettings[$k2]['MULTIPLE_SEPARATOR'];
					else $separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
					$arData[$k][$k2] = implode($separator, $val);
				}
			}
		}
		
		return array(
			'FIELDS' => $arFields,
			'DATA' => $arData,
			'PAGE_COUNT' => $arResElements['navPageCount'],
			'RECORD_COUNT' => $arResElements['navRecordCount']
		);
	}
	
	public function GetFieldList($listIndex)
	{
		$hlblId = $this->params['HIGHLOADBLOCK_ID'];
		$arFieldsHlbl = $this->fl->GetHigloadBlockFields($hlblId);		
		
		$arFields = array();
		if(isset($this->params['FIELDS_LIST'][$listIndex]))
		{
			$arFields = $this->params['FIELDS_LIST'][$listIndex];
		}
		if(!is_array($arFields) || count($arFields)==0)
		{
			$arFields = array();
			foreach($arFieldsHlbl as $k=>$v)
			{
				if(!isset($v['SHOW_IN_LIST']) || $v['SHOW_IN_LIST']=='Y')
				{
					$arFields[] = $k;
				}
			}
		}
		return $arFields;
	}
	
	public function GetElementsData(&$arData, $arParams)
	{
		$arFilter = $arParams['FILTER'];
		$arNavParams = (is_array($arParams['NAV_PARAMS']) ? $arParams['NAV_PARAMS'] : false);
		$arAllFields = $arParams['FIELDS'];
		
		$hlblId = $this->params['HIGHLOADBLOCK_ID'];
		$entityDataClass = $this->GetHighloadBlockClass($hlblId);
		
		$dbResCnt = 0;
		$limit = (int)$arNavParams['limit'] > 0 ? (int)$arNavParams['limit'] : 10;
		$offset = (int)$arNavParams['offset'];
		
		$arSelectField = array();
		$arFieldsHlbl = $this->fl->GetHigloadBlockFields($hlblId);
		foreach($arAllFields as $fieldName)
		{
			if(isset($arFieldsHlbl[$fieldName])) $arSelectField[] = $fieldName;
		}

		$dbResElements = $entityDataClass::GetList(array(
			'filter' => $arFilter,
			'order' => array('ID'=>'ASC'),
			'select' => $arSelectField,
			'limit' => $limit,
			'offset' => $offset
		));
		
		while($arElement = $dbResElements->Fetch())
		{
			foreach($arElement as $k=>$v)
			{				
				if(is_array($v))
				{
					$arVals = array();
					foreach($v as $k2=>$v2)
					{
						$arVals[] = $this->GetFieldValue($arFieldsHlbl[$k], $v2);
					}
					$arElement[$k] = $arVals;
				}
				else
				{
					$arElement[$k] = $this->GetFieldValue($arFieldsHlbl[$k], $v);
				}
			}
			
			$arData[] = $arElement;
			$dbResCnt++;

			if($arParams['NAV_PARAMS']['nTopCount'] && $dbResCnt >= $arParams['NAV_PARAMS']['nTopCount'])
			{
				$break = true;
				break;
			}
		}
		
		$navRecordCount = $entityDataClass::getCount($arFilter);
		$navPageCount = ceil($navRecordCount / $limit);
		
		if($dbResCnt > $navRecordCount) $navRecordCount = $dbResCnt;
		return array(
			'navRecordCount' => $navRecordCount,
			'navPageCount' => $navPageCount
		);
	}
	
	public function IsPictureField($field)
	{
		$hlblId = $this->params['HIGHLOADBLOCK_ID'];
		$arFieldsHlbl = $this->fl->GetHigloadBlockFields($hlblId);
		$isPicture = (bool)($arFieldsHlbl[$field]['USER_TYPE_ID']=='file');
		return $isPicture;
	}
	
	public function IsMultipleField($field)
	{
		$hlblId = $this->params['HIGHLOADBLOCK_ID'];
		$arFieldsHlbl = $this->fl->GetHigloadBlockFields($hlblId);
		$isMultiple = (bool)($arFieldsHlbl[$field]['MULTIPLE']=='Y');
		return $isMultiple;
	}
	
	public function GetHighloadBlockClass($HIGHLOADBLOCK_ID)
	{
		if(!$this->hlbl[$HIGHLOADBLOCK_ID])
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID'=>$HIGHLOADBLOCK_ID)))->fetch();
			$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
			$this->hlbl[$HIGHLOADBLOCK_ID] = $entity->getDataClass();
		}
		return $this->hlbl[$HIGHLOADBLOCK_ID];
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