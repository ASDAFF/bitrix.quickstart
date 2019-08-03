<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class ImporterHl {
	protected static $moduleId = 'esol.importxml';
	var $xmlParts = array();
	var $rcurrencies = array('#USD#', '#EUR#');
	
	function __construct($filename, $params, $fparams, $stepparams, $pid = false)
	{
		$this->filename = $_SERVER['DOCUMENT_ROOT'].$filename;
		$this->params = $params;
		$this->fparams = $fparams;
		$this->sections = array();
		$this->propVals = array();
		$this->hlbl = array();
		$this->errors = array();
		$this->maxStepRows = 1000;
		$this->xmlRowDiff = 0;
		$this->stepparams = $stepparams;
		$this->stepparams['total_read_line'] = intval($this->stepparams['total_read_line']);
		$this->stepparams['total_line'] = intval($this->stepparams['total_line']);
		$this->stepparams['correct_line'] = intval($this->stepparams['correct_line']);
		$this->stepparams['error_line'] = intval($this->stepparams['error_line']);
		$this->stepparams['element_added_line'] = intval($this->stepparams['element_added_line']);
		$this->stepparams['element_updated_line'] = intval($this->stepparams['element_updated_line']);
		$this->stepparams['old_removed_line'] = intval($this->stepparams['old_removed_line']);
		$this->stepparams['xmlCurrentRow'] = intval($this->stepparams['xmlCurrentRow']);
		$this->stepparams['total_file_line'] = 1;

		$this->xpathMulti = ($this->params['XPATHS_MULTI'] ? unserialize(base64_decode($this->params['XPATHS_MULTI'])) : array());
		if(!is_array($this->xpathMulti)) $this->xpathMulti = array();
		
		$this->fl = new \Bitrix\EsolImportxml\FieldList();
		$this->cloud = new \Bitrix\EsolImportxml\Cloud();
		$this->sftp = new \Bitrix\EsolImportxml\Sftp();
		
		$this->useProxy = false;
		$this->proxySettings = array(
			'proxyHost' => \Bitrix\Main\Config\Option::get(static::$moduleId, 'PROXY_HOST', ''),
			'proxyPort' => \Bitrix\Main\Config\Option::get(static::$moduleId, 'PROXY_PORT', ''),
			'proxyUser' => \Bitrix\Main\Config\Option::get(static::$moduleId, 'PROXY_USER', ''),
			'proxyPassword' => \Bitrix\Main\Config\Option::get(static::$moduleId, 'PROXY_PASSWORD', ''),
		);
		if($this->proxySettings['proxyHost'] && $this->proxySettings['proxyPort'])
		{
			$this->useProxy = true;
		}
		
		/*Temp folders*/
		$this->filecnt = 0;
		$dir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/'.static::$moduleId.'/';
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
		$this->archivedir = $this->stepparams['tmpdir'].'archives/';
		CheckDirPath($this->archivedir);
		
		$this->tmpfile = $this->tmpdir.'params.txt';
		$this->fileElementsId = $this->tmpdir.'elements_id.txt';
		/*/Temp folders*/
		
		if(file_exists($this->tmpfile) && filesize($this->tmpfile) > 0)
		{
			$this->stepparams = array_merge($this->stepparams, unserialize(file_get_contents($this->tmpfile)));
		}
		
		if(!isset($this->stepparams['curstep'])) $this->stepparams['curstep'] = 'import';
		
		if(!isset($this->params['MAX_EXECUTION_TIME']) || $this->params['MAX_EXECUTION_TIME']!==0)
		{
			if(\Bitrix\Main\Config\Option::get(static::$moduleId, 'SET_MAX_EXECUTION_TIME')=='Y' && is_numeric(\Bitrix\Main\Config\Option::get(static::$moduleId, 'MAX_EXECUTION_TIME')))
			{
				$this->params['MAX_EXECUTION_TIME'] = intval(\Bitrix\Main\Config\Option::get(static::$moduleId, 'MAX_EXECUTION_TIME'));
				if(ini_get('max_execution_time') && $this->params['MAX_EXECUTION_TIME'] > ini_get('max_execution_time') - 5) $this->params['MAX_EXECUTION_TIME'] = ini_get('max_execution_time') - 5;
				if($this->params['MAX_EXECUTION_TIME'] < 1) $this->params['MAX_EXECUTION_TIME'] = 1;
				if($this->params['MAX_EXECUTION_TIME'] > 300) $this->params['MAX_EXECUTION_TIME'] = 300;
			}
			else
			{
				$this->params['MAX_EXECUTION_TIME'] = intval(ini_get('max_execution_time')) - 10;
				if($this->params['MAX_EXECUTION_TIME'] < 10) $this->params['MAX_EXECUTION_TIME'] = 15;
				if($this->params['MAX_EXECUTION_TIME'] > 50) $this->params['MAX_EXECUTION_TIME'] = 50;
			}
		}
		
		if($pid!==false)
		{
			$this->procfile = $dir.$pid.'_highload.txt';
			$this->errorfile = $dir.$pid.'_highload_error.txt';
			if($this->stepparams['total_line'] < 1)
			{
				$oProfile = \Bitrix\EsolImportxml\Profile::getInstance('highload');
				$oProfile->UpdateFields($pid, array('DATE_START'=>new \Bitrix\Main\Type\DateTime()));
				
				if(file_exists($this->procfile)) unlink($this->procfile);
				if(file_exists($this->errorfile)) unlink($this->errorfile);
			}
			$this->pid = $pid;
		}
	}
	
	public function CheckTimeEnding($time)
	{
		return ($this->params['MAX_EXECUTION_TIME'] && (time()-$time >= $this->params['MAX_EXECUTION_TIME']));
	}
	
	public function Import()
	{
		register_shutdown_function(array($this, 'OnShutdown'));
		set_error_handler(array($this, "HandleError"));
		set_exception_handler(array($this, "HandleException"));
		$time = time();
		
		if($this->stepparams['curstep'] == 'import')
		{
			if($this->params['GROUPS']['ELEMENT'])
			{
				$this->InitImport('element');

				while($arItem = $this->GetNextRecord($time))
				{
					if(is_array($arItem)) $this->SaveRecord($arItem);
					if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}
				}
			}
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		}
		return $this->EndOfLoading($time);
	}
	
	public function EndOfLoading($time)
	{
		if($this->params['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y')
		{
			if($this->stepparams['curstep'] == 'import' || $this->stepparams['curstep'] == 'import_end')
			{
				$this->stepparams['curstep'] = 'deactivate_elements';				
				$this->stepparams['deactivate_element_last'] = \Bitrix\EsolImportxml\Utils::SortFileIds($this->fileElementsId);
				$this->stepparams['deactivate_element_first'] = 0;
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
			}
			
			$HIGHLOADBLOCK_ID = $this->params['HIGHLOADBLOCK_ID'];
			$entityDataClass = $this->GetHighloadBlockClass($HIGHLOADBLOCK_ID);
		
			while($this->stepparams['deactivate_element_first'] < $this->stepparams['deactivate_element_last'])
			{
				$arUpdatedIds = \Bitrix\EsolImportxml\Utils::GetPartIdsFromFile($this->fileElementsId, $this->stepparams['deactivate_element_first']);
				if(empty($arUpdatedIds))
				{
					$this->stepparams['deactivate_element_first'] = $this->stepparams['deactivate_element_last'];
					continue;
				}
				$lastElement = end($arUpdatedIds);
				$arFields['!ID'] = $arUpdatedIds;
				if($this->stepparams['deactivate_element_first'] > 0) $arFields['>ID'] = $this->stepparams['deactivate_element_first'];
				if($lastElement < $this->stepparams['deactivate_element_last']) $arFields['<=ID'] = $lastElement;
				
				$dbRes = $entityDataClass::getList(array('filter'=>$arFields, 'order'=>array('ID'=>'ASC'), 'select'=>array('ID')));
				while($arElement = $dbRes->Fetch())
				{
					if($this->params['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y')
					{
						$entityDataClass::delete($arElement['ID']);
						$this->stepparams['old_removed_line']++;
						continue;
					}
				}
				$this->stepparams['deactivate_element_first'] = $lastElement;
			}
		}
		
		$this->SaveStatusImport(true);
		
		$oProfile = \Bitrix\EsolImportxml\Profile::getInstance('highload');
		$oProfile->UpdateFileHash($this->pid, $this->filename);
		
		return $this->GetBreakParams('finish');
	}
	
	public function InitImport($type = 'element')
	{
		if($type == 'element' && $this->params['GROUPS']['ELEMENT'])
		{
			$emptyFields = array();
			foreach($this->params['ELEMENT_UID'] as $uidField)
			{
				if(!is_array($this->params['FIELDS']) || count(preg_grep('/;'.$uidField.'$/', $this->params['FIELDS']))==0)
				{
					$emptyFields[] = $uidField;
				}
			}
			if(!empty($emptyFields))
			{
				$arFieldsDef = $this->fl->GetHigloadBlockFields($this->params['HIGHLOADBLOCK_ID']);
				$emptyFieldNames = array();
				foreach($emptyFields as $field)
				{
					$emptyFieldNames[] = $arFieldsDef[$field]['NAME_LANG'];
				}
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_UID"), implode(', ', $emptyFieldNames));
				return false;
			}
		}
		
		$this->fieldOnlyNew = array();
		$this->fieldSettings = array();
		foreach($this->params['FIELDS'] as $k=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			//if(strpos($field, '|')!==false) $field = substr($field, 0, strpos($field, '|'));
			$field2 = '';
			if(strpos($field, '|')!==false)
			{
				list($field, $adata) = explode('|', $field);
				$adata = explode('=', $adata);
				$field2 = $adata[0];
				$fieldName = $field;
				if(strpos($field, 'OFFER_')===0) $fieldName = substr($field, 6);
				$field2 = substr($fieldName, 0, strpos($fieldName, '_') + 1).$field2;
			}
			
			$this->fieldSettings[$field] = $this->fparams[$k];
			
			if($this->fparams[$k]['SET_NEW_ONLY']=='Y')
			{
				$this->fieldOnlyNew[] = $field;
				if(strlen($field2) > 0) $this->fieldOnlyNew[] = $field2;
			}
		}
		
		//$this->fileEncoding = \Bitrix\EsolImportxml\Utils::GetXmlEncoding($this->filename);
		$this->fileEncoding = 'utf-8';
		$this->siteEncoding = \Bitrix\EsolImportxml\Utils::getSiteEncoding();
		//$this->xmlObject = simplexml_load_file($this->filename);
		
		$this->InitXml($type);
		
		return true;
	}
	
	public function InitXml($type)
	{
		if($type == 'element')
		{
			if(!isset($this->xmlCurrentRow)) $this->xmlCurrentRow = intval($this->stepparams['xmlCurrentRow']);
			if(preg_match('/\/offers$/', $this->params['GROUPS']['ELEMENT'])) $this->CheckGroupParams('ELEMENT', $this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['ELEMENT'].'/offer');
			if(preg_match('/\/'.Loc::getMessage("ESOL_IX_PRODUCTS_TAG_1C").'$/', $this->params['GROUPS']['ELEMENT'])) $this->CheckGroupParams('ELEMENT', $this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['ELEMENT'].'/'.Loc::getMessage("ESOL_IX_PRODUCT_TAG_1C"));
			
			$count = 0;
			$this->xmlElements = $this->GetXmlObject($count, $this->xmlCurrentRow, $this->params['GROUPS']['ELEMENT']);
			$this->xmlElementsCount = $this->stepparams['total_file_line'] = $count;
		}
		return true;
	}
	
	public function CheckGroupParams($type, $xpathFrom, $xpathTo)
	{
		if(trim($this->params['GROUPS'][$type], '/')==$xpathFrom)
		{
			$xmlCurrentRow = $this->xmlCurrentRow;
			$maxStepRows = $this->maxStepRows;
			$this->maxStepRows = 2;
			$xmlElements = $this->GetXmlObject(($count=0), 0, $xpathTo);
			if(is_array($xmlElements) && count($xmlElements) > 0)
			{
				$this->params['GROUPS'][$type] = $xpathTo;
			}
			$this->xmlCurrentRow = $xmlCurrentRow;
			$this->maxStepRows = $maxStepRows;
		}
	}
	
	public function GetXmlObject(&$countRows, $beginRow, $xpath, $nolimit = false)
	{
		$xpath = trim($xpath);
		if(strlen($xpath) == 0) return;
		
		$arXpath = explode('/', trim($xpath, '/'));
		$this->xpath = '/'.$xpath;
		$countRows = 0;
		if($this->params['NOT_USE_XML_READER']=='Y' || !class_exists('\XMLReader'))
		{
			$this->xmlRowDiff = 0;
			$this->xmlObject = simplexml_load_file($this->filename);
			//$rows = $this->xmlObject->xpath('/'.$xpath);
			$rows = $this->Xpath($this->xmlObject, '/'.$xpath);
			$countRows = count($rows); 
			return $rows;
		}

		$multiParent = false;
		for($i=1; $i<count($arXpath); $i++)
		{
			if(in_array(implode('/', array_slice($arXpath, 0, $i)), $this->xpathMulti))
			{
				$multiParent = true;
			}
		}
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$arXpath = \Bitrix\Main\Text\Encoding::convertEncodingArray($arXpath, $this->siteEncoding, $this->fileEncoding);
		}
		$cachedCountRowsKey = 'count_rows//'.$xpath;
		$cachedCountRows = 0;
		if(isset($this->stepparams[$cachedCountRowsKey]))
		{
			$cachedCountRows = (int)$this->stepparams[$cachedCountRowsKey];
		}
		
		$xml = new \XMLReader();
		$res = $xml->open($this->filename);
		
		$arObjects = array();
		$arObjectNames = array();
		$arXPaths = array();
		$curDepth = 0;
		$isRead = false;
		$countLoadedRows = 0;
		$break = false;
		$countRows = -1;
		while(($isRead || $xml->read()) && !$break) 
		{
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				$curDepth = $xml->depth;
				$arObjectNames[$curDepth] = $curName = $xml->name;
				$extraDepth = $curDepth + 1;
				while(isset($arObjectNames[$extraDepth]))
				{
					unset($arObjectNames[$extraDepth]);
					$extraDepth++;
				}
				
				$curXPath = implode('/', $arObjectNames);
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$curXPath = \Bitrix\Main\Text\Encoding::convertEncoding($curXPath, $this->fileEncoding, $this->siteEncoding);
				}
				if($multiParent)
				{
					if(strpos($xpath, $curXPath)!==0 && strpos($curXPath, $xpath)!==0) continue;
					if($xpath==$curXPath) $countRows++;
					if($countRows < $beginRow && strlen($curXPath)>=strlen($xpath)) continue;
					if($xpath==$curXPath)
					{
						$countLoadedRows++;
						if($countLoadedRows > $this->maxStepRows && !$nolimit && $cachedCountRows > 0)
						{
							$break = true;
						}
					}
				}
				else
				{
					if(strpos($xpath, $curXPath)!==0 && strpos($curXPath, $xpath)!==0)
					{
						$isRead = false;
						while(!$isRead && $xml->next($arXpath[$curDepth])) $isRead = true;
						continue;
					}
					if($xpath==$curXPath)
					{
						$countRows++;
						while($countRows < $beginRow && $xml->next($curName)) $countRows++;
					}
					if($countRows < $beginRow && strlen($curXPath)>=strlen($xpath)) continue;
					if($xpath==$curXPath)
					{
						$countLoadedRows++;
						if($countLoadedRows > $this->maxStepRows && !$nolimit)
						{
							if($cachedCountRows > 0)
							{
								$break = true;
							}
							else
							{
								while($xml->next($curName)) $countRows++;
							}
						}
					}
				}
				if($countLoadedRows > $this->maxStepRows && !$nolimit) continue;
				
				$arAttributes = array();
				if($xml->moveToFirstAttribute())
				{
					$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					while($xml->moveToNextAttribute ())
					{
						$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					}
				}
				$xml->moveToElement();
				

				$curName = $xml->name;
				$curValue = null;
				//$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);
				$curNamespace = null;
				if($xml->namespaceURI && strpos($curName, ':')!==false)
				{
					$curNamespace = $xml->namespaceURI;
				}

				$isSubRead = false;
				while(($xml->read() && ($isSubRead = true)) && ($xml->nodeType == \XMLReader::SIGNIFICANT_WHITESPACE)){}
				if($xml->nodeType == \XMLReader::TEXT || $xml->nodeType == \XMLReader::CDATA)
				{
					$curValue = $xml->value;
				}
				else
				{
					$isRead = $isSubRead;
				}

				if($curDepth == 0)
				{
					$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
					$arObjects[$curDepth] = &$xmlObj;
				}
				else
				{
					$curValue = str_replace('&', '&amp;', $curValue);
					$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
				}			

				foreach($arAttributes as $arAttr)
				{
					if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
					else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
				}
			}
		}
		$xml->close();
		$countRows++;
		if($cachedCountRows > 0) $countRows = $cachedCountRows;
		else $this->stepparams[$cachedCountRowsKey] = $countRows;
		
		if(is_object($xmlObj))
		{
			$this->xmlRowDiff = $beginRow;
			$this->xmlObject = $xmlObj;
			//return $this->xmlObject->xpath('/'.$xpath);
			return $this->Xpath($this->xmlObject, '/'.$xpath);
		}
		return false;
	}
	
	public function GetPartXmlObject($xpath)
	{
		if(!class_exists('\XMLReader'))
		{
			$xmlObject = simplexml_load_file($this->filename);
			//$rows = $xmlObject->xpath('/'.$xpath);
			$rows = $this->Xpath($xmlObject, '/'.$xpath);
			return $rows;
		}
		
		$xml = new \XMLReader();
		$res = $xml->open($this->filename);
		
		$arObjects = array();
		$arObjectNames = array();
		$arXPaths = array();
		$curDepth = 0;
		$isRead = false;
		$break = false;
		while(($isRead || $xml->read()) && !$break) 
		{
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				$curDepth = $xml->depth;
				$arObjectNames[$curDepth] = $xml->name;
				$extraDepth = $curDepth + 1;
				while(isset($arObjectNames[$extraDepth]))
				{
					unset($arObjectNames[$extraDepth]);
					$extraDepth++;
				}
				
				$curXPath = implode('/', $arObjectNames);
				if($this->siteEncoding!=$this->fileEncoding)
				{
					$curXPath = \Bitrix\Main\Text\Encoding::convertEncoding($curXPath, $this->fileEncoding, $this->siteEncoding);
				}
				if(strpos($xpath, $curXPath)!==0 && strpos($curXPath, $xpath)!==0) continue;
				
				$arAttributes = array();
				if($xml->moveToFirstAttribute())
				{
					$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					while($xml->moveToNextAttribute ())
					{
						$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					}
				}
				$xml->moveToElement();
				

				$curName = $xml->name;
				$curValue = null;
				//$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);
				$curNamespace = null;
				if($xml->namespaceURI && strpos($curName, ':')!==false)
				{
					$curNamespace = $xml->namespaceURI;
				}

				$isSubRead = false;
				while(($xml->read() && ($isSubRead = true)) && ($xml->nodeType == \XMLReader::SIGNIFICANT_WHITESPACE)){}
				if($xml->nodeType == \XMLReader::TEXT || $xml->nodeType == \XMLReader::CDATA)
				{
					$curValue = $xml->value;
				}
				else
				{
					$isRead = $isSubRead;
				}

				if($curDepth == 0)
				{
					$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
					$arObjects[$curDepth] = &$xmlObj;
				}
				else
				{
					$curValue = str_replace('&', '&amp;', $curValue);
					$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
				}			

				foreach($arAttributes as $arAttr)
				{
					if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
					else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
				}
			}
		}
		$xml->close();

		if(is_object($xmlObj))
		{
			//return $xmlObj->xpath('/'.$xpath);
			return $this->Xpath($xmlObj, '/'.$xpath);
		}
		return false;
	}
	
	public function GetBreakParams($action = 'continue')
	{
		$arStepParams = array(
			'params'=> array_merge($this->stepparams, array(
				'xmlCurrentRow' => intval($this->xmlCurrentRow)
			)),
			'action' => $action,
			'errors' => $this->errors,
			'sessid' => bitrix_sessid()
		);
		
		if($action == 'continue')
		{
			file_put_contents($this->tmpfile, serialize($arStepParams['params']));
			if(file_exists($this->imagedir))
			{
				DeleteDirFilesEx(substr($this->imagedir, strlen($_SERVER['DOCUMENT_ROOT'])));
			}
		}
		elseif(file_exists($this->tmpdir))
		{
			DeleteDirFilesEx(substr($this->tmpdir, strlen($_SERVER['DOCUMENT_ROOT'])));
			unlink($this->procfile);
		}
		
		return $arStepParams;
	}
	
	public function CheckSkipLine($arItem, $type='element')
	{
		$load = true;
		
		if($load)
		{
			foreach($this->fparams as $k=>$v)
			{
				if(!is_array($v)) continue;
				
				list($xpath, $field) = explode(';', $this->params['FIELDS'][$k], 2);
				
				if(is_array($v['UPLOAD_VALUES']) || is_array($v['NOT_UPLOAD_VALUES']) || $v['FILTER_EXPRESSION'])
				{
					$val = $arItem[$k];
					$valOrig = $arItem['~'.$k];
					$val = $this->ApplyConversions($valOrig, $v['CONVERSION'], array());
					$val = ToLower(trim($val));
				}
				else
				{
					$val = '';
				}
				
				if(is_array($v['UPLOAD_VALUES']))
				{
					$subload = false;
					foreach($v['UPLOAD_VALUES'] as $needval)
					{
						$needval = ToLower(trim($needval));
						if($needval==$val 
							|| ($needval=='{empty}' && strlen($val)==0)
							|| ($needval=='{not_empty}' && strlen($val) > 0))
						{
							$subload = true;
						}
					}
					$load = ($load && $subload);
				}
				
				if(is_array($v['NOT_UPLOAD_VALUES']))
				{
					$subload = true;
					foreach($v['NOT_UPLOAD_VALUES'] as $needval)
					{
						$needval = ToLower(trim($needval));
						if($needval==$val 
							|| ($needval=='{empty}' && strlen($val)==0)
							|| ($needval=='{not_empty}' && strlen($val) > 0))
						{
							$subload = false;
						}
					}
					$load = ($load && $subload);
				}
				
				if($v['FILTER_EXPRESSION'])
				{
					$load = ($load && $this->ExecuteFilterExpression($valOrig, $v['FILTER_EXPRESSION']));
				}
			}
		}
		
		return !$load;
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
	
	public function GetNextRecord($time)
	{
		while(isset($this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff])
			|| ($this->xmlElementsCount > $this->xmlCurrentRow
				&& $this->InitXml('element')
				&& isset($this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff])))
		{
			$this->currentXmlObj = $simpleXmlObj = $this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff];
			$this->xmlPartObjects = array();
			
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($m[1], strlen($this->params['GROUPS']['ELEMENT']) + 1);
					}
				}
				
				$xpath = substr($xpath, strlen($this->params['GROUPS']['ELEMENT']) + 1);
				$arPath = array_diff(explode('/', $xpath), array(''));
				$attr = false;
				if(strpos($arPath[count($arPath)-1], '@')===0)
				{
					$attr = substr(array_pop($arPath), 1);
				}
				if(count($arPath) > 0)
				{
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				
				if($attr!==false)
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v->attributes()->{$attr};
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2->attributes()->{$attr};
						}
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
							{
								$val[] = (string)$v;
							}
						}
						if(count($val)==0) $val = '';
						elseif(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
						elseif(count($val)==1) $val = current($val);
					}
					else
					{
						if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
						{
							$val = (string)$simpleXmlObj2;
						}
					}					
				}
				
				$val = $this->GetRealXmlValue($val);
		
				/*$arItem[$fieldName] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$fieldName] = $val;*/
				$arItem[$key] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlCurrentRow++;
			
			if(!$this->CheckSkipLine($arItem, 'element'))
			{
				return $arItem;
			}
			if($this->CheckTimeEnding($time)) return false;
		}
		
		return false;
	}
	
	public function ReplaceXpath($xpath)
	{
		if(is_array($this->xpathReplace) && isset($this->xpathReplace['FROM']) && isset($this->xpathReplace['TO']))
		{
			$xpath = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $xpath);
		}
		return $xpath;
	}
	
	public function ReplaceConditionXpath($m)
	{
		$offerXpath = substr($this->xpath, 1);
		if(strpos($m[1], $offerXpath)===0)
		{
			return '{'.substr($this->ReplaceXpath($m[1]), strlen($offerXpath) + 1).'}';
		}
		else
		{
			return '{'.$this->ReplaceXpath($m[1]).'}';
		}
	}
	
	public function ReplaceConditionXpathToValue($m)
	{
		$xpath = $this->replaceXpath;
		$simpleXmlObj = $this->replaceSimpleXmlObj;
		$simpleXmlObj2 = $this->replaceSimpleXmlObj2;
		$xpath2 = $m[1];
		if(strpos($xpath2, $xpath)===0)
		{
			$xpath2 = substr($xpath2, strlen($xpath) + 1);
			$simpleXmlObj = $simpleXmlObj2;
		}
		else
		{
			$arXpath2 = $this->GetXPathParts($xpath2);
			if(strlen($arXpath2['xpath']) > 0)
			{
				if(!isset($this->xmlParts[$arXpath2['xpath']]))
				{
					$this->xmlParts[$arXpath2['xpath']] = $this->GetPartXmlObject($arXpath2['xpath']);
				}
				$xmlPart = $this->xmlParts[$arXpath2['xpath']];
				if(is_array($xmlPart))
				{
					$valXpath = $xpath;
					if(isset($this->parentXpath) && strlen($this->parentXpath) > 0) $valXpath = rtrim($this->parentXpath, '/').'/'.ltrim($valXpath, '/');
					$val = $this->GetValueByXpath($valXpath, $simpleXmlObj);
					
					foreach($xmlPart as $xmlObj)
					{
						if(strlen($arXpath2['subpath'])==0) $xmlObj2 = $xmlObj;
						//else $xmlObj2 = $xmlObj->xpath($arXpath2['subpath']);
						else $xmlObj2 = $this->Xpath($xmlObj, $arXpath2['subpath']);
						if(is_array($xmlObj2)) $xmlObj2 = current($xmlObj2);
						if($arXpath2['attr']!==false && is_callable(array($xmlObj2, 'attributes')))
						{
							$val2 = (string)$xmlObj2->attributes()->{$arXpath2['attr']};
						}
						else
						{
							$val2 = (string)$xmlObj2;
						}
						if($val2==$val)
						{
							$this->xmlPartObjects[$arXpath2['xpath']] = $xmlObj;
							return $val;
						}
					}
				}
			}
		}
		$arPath = explode('/', $xpath2);
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
		}
		if(count($arPath) > 0)
		{
			//$simpleXmlObj3 = $simpleXmlObj->xpath(implode('/', $arPath));
			$simpleXmlObj3 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
			if(count($simpleXmlObj3)==1) $simpleXmlObj3 = current($simpleXmlObj3);
		}
		else $simpleXmlObj3 = $simpleXmlObj;
		
		if(is_array($simpleXmlObj3)) $simpleXmlObj3 = current($simpleXmlObj3);
		$condVal = (string)(($attr!==false && is_callable(array($simpleXmlObj3, 'attributes'))) ? $simpleXmlObj3->attributes()->{$attr} : $simpleXmlObj3);
		return $condVal;
	}
	
	public function GetXPathParts($xpath)
	{
		$arPath = explode('/', $xpath);
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
		}
		$xpath2 = implode('/', $arPath);
		$xpath3 = '';
		if(strpos($xpath2, '//')!==false)
		{
			list($xpath2, $xpath3) = explode('//', $xpath2, 2);
		}
		return array('xpath'=>$xpath2, 'subpath' => $xpath3, 'attr'=>$attr);
	}
	
	public function CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2, $key=false)
	{
		if(empty($conditions)) return true;
		if($key!==false)
		{
			$arPath = explode('/', $xpath);
			$attr = false;
			if(strpos($arPath[count($arPath)-1], '@')===0)
			{
				$attr = substr(array_pop($arPath), 1);
			}
			//if(count($arPath) > 1 && ($cnt = count($simpleXmlObj->xpath(implode('/', $arPath)))) && $cnt > 1)
			if(count($arPath) > 1 && ($cnt = count($this->Xpath($simpleXmlObj, implode('/', $arPath)))) && $cnt > 1)
			{
				while(($lastElem = array_pop($arPath)) && (count($arPath) > 0) /*&& (count($this->Xpath($simpleXmlObj, implode('/', $arPath)))==$cnt)*/ && ($cnt2 = count($this->Xpath($simpleXmlObj, implode('/', $arPath)))) && $cnt2>=$cnt){$cnt3 = $cnt2;}
				/*Fix for missign tag*/
				$key2 = $key;
				if($cnt3 > $cnt)
				{
					$subpath = implode('/', $arPath).'/'.$lastElem;
					for($i=0; $i<min($key2+1, $cnt3); $i++)
					{
						$xpath2 = $subpath.'['.($i+1).']/'.substr($xpath, strlen($subpath) + 1);
						//if(count($simpleXmlObj->xpath($xpath2))==0) $key2++;
						if(count($this->Xpath($simpleXmlObj, $xpath2))==0) $key2++;
					}
				}
				/*/Fix for missign tag*/

				$xpathReplace = $this->xpathReplace;
				$this->xpathReplace = array(
					'FROM' => ltrim(implode('/', $arPath).'/'.$lastElem, '/'),
					'TO' => ltrim(implode('/', $arPath).'/'.$lastElem.'['.($key2+1).']', '/')
				);
				foreach($conditions as $k3=>$v3)
				{
					$conditions[$k3]['XPATH'] = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $conditions[$k3]['XPATH']);
					$conditions[$k3]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k3]['FROM']);
				}
				$this->xpathReplace = $xpathReplace;
			}
		}
		
		$k = 0;
		while(isset($conditions[$k]))
		{
			$v = $conditions[$k];
			$pattern = '/^\{(\S*)\}$/';
			if(preg_match($pattern, $v['FROM']))
			{
				$this->replaceXpath = $xpath;
				$this->replaceSimpleXmlObj = $simpleXmlObj;
				$this->replaceSimpleXmlObj2 = $simpleXmlObj2;
				$v['FROM'] = preg_replace_callback($pattern, array($this, 'ReplaceConditionXpathToValue'), $v['FROM']);
			}
			
			$xpath2 = $v['XPATH'];

			$generalXpath = $xpath;
			if(strpos($xpath, '@')!==false) $generalXpath = rtrim(substr($xpath, 0, strpos($xpath, '@')), '/');
			if(strpos($xpath2, $generalXpath)===0)
			{
				//$xpath2 = substr($xpath2, strlen($xpath) + 1);
				$xpath2 = substr($xpath2, strlen($generalXpath));
				$xpath2 = ltrim(preg_replace('/^\[\d*\]/', '', $xpath2), '/');
				$simpleXmlObj = $simpleXmlObj2;
			}
			$arPath = explode('/', $xpath2);
			$attr = false;
			if(strpos($arPath[count($arPath)-1], '@')===0)
			{
				$attr = substr(array_pop($arPath), 1);
			}
			if(count($arPath) > 0)
			{
				//$simpleXmlObj3 = $simpleXmlObj->xpath(implode('/', $arPath));
				$simpleXmlObj3 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
				if(count($simpleXmlObj3)==1) $simpleXmlObj3 = current($simpleXmlObj3);
			}
			else $simpleXmlObj3 = $simpleXmlObj;
			
			$condVal = '';
			if(is_array($simpleXmlObj3))
			{					
				$find = false;
				foreach($simpleXmlObj3 as $k2=>$curObj)
				{
					$condVal = (string)($attr!==false ? $curObj->attributes()->{$attr} : $curObj);
					if($this->CheckCondition($condVal, $v))
					{
						$find = true;
						
						$cnt = count($simpleXmlObj3);
						if($cnt > 1)
						{
							$arPath2 = $arPath;
							$lastElem = array_pop($arPath2);
							while(($lastElem = array_pop($arPath2)) && (count($arPath) > 0) 
								//&& (count($simpleXmlObj->xpath(implode('/', $arPath2)))==$cnt)){}
								&& (count($this->Xpath($simpleXmlObj, implode('/', $arPath2)))==$cnt)){}
							$xpathReplace = $this->xpathReplace;
							$this->xpathReplace = array(
								'FROM' => implode('/', $arPath2).'/'.$lastElem,
								'TO' => implode('/', $arPath2).'/'.$lastElem.'['.($k2+1).']'
							);
							foreach($conditions as $k3=>$v3)
							{
								if($k3 <= $k) continue;
								$conditions[$k3]['XPATH'] = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $conditions[$k3]['XPATH']);
								$conditions[$k3]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k3]['FROM']);
							}
							$this->xpathReplace = $xpathReplace;
						}
					}
				}
				if(!$find) return false;
			}
			else
			{
				$condVal = (string)(($attr!==false && is_callable(array($simpleXmlObj3, 'attributes'))) ? $simpleXmlObj3->attributes()->{$attr} : $simpleXmlObj3);
				if(!$this->CheckCondition($condVal, $v)) return false;
			}
			$k++;
		}
		return true;
	}
	
	public function CheckCondition($condVal, $v)
	{
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$condVal = \Bitrix\Main\Text\Encoding::convertEncodingArray($condVal, $this->fileEncoding, $this->siteEncoding);
		}
		if(!(($v['WHEN']=='EQ' && $condVal==$v['FROM'])
			|| ($v['WHEN']=='NEQ' && $condVal!=$v['FROM'])
			|| ($v['WHEN']=='GT' && $condVal > $v['FROM'])
			|| ($v['WHEN']=='LT' && $condVal < $v['FROM'])
			|| ($v['WHEN']=='GEQ' && $condVal >= $v['FROM'])
			|| ($v['WHEN']=='LEQ' && $condVal <= $v['FROM'])
			|| ($v['WHEN']=='CONTAIN' && strpos($condVal, $v['FROM'])!==false)
			|| ($v['WHEN']=='NOT_CONTAIN' && strpos($condVal, $v['FROM'])===false)
			|| ($v['WHEN']=='REGEXP' && preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
			|| ($v['WHEN']=='EMPTY' && strlen($condVal)==0)
			|| ($v['WHEN']=='NOT_EMPTY' && strlen($condVal) > 0)))
		{
			return false;
		}
		return true;
	}
	
	public function SaveRecord($arItem)
	{
		$this->stepparams['total_read_line']++;
		if(count(array_diff(array_map('trim', $arItem), array('')))==0)
		{
			return false;
		}
		$this->stepparams['total_line']++;
		
		$filedList = preg_grep('/^[^~]/', array_keys($arItem));
		$HIGHLOADBLOCK_ID = $this->params['HIGHLOADBLOCK_ID'];
		$entityDataClass = $this->GetHighloadBlockClass($HIGHLOADBLOCK_ID);
		
		$iblockFields = $this->fl->GetHigloadBlockFields($HIGHLOADBLOCK_ID);
		$arFieldsElement = array();
		$arFieldsElementOrig = array();
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if($field=='VARIABLE') continue;

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions))
			{
				if(is_array($value))
				{
					foreach($value as $k2=>$v2)
					{
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				}
				if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
			}
			
			$this->GetHLField($arFieldsElement, $arFieldsElementOrig, $this->fparams[$key], $iblockFields[$field], $field, $value, $origValue);
		}

		$arUid = array();
		if(!is_array($this->params['ELEMENT_UID'])) $this->params['ELEMENT_UID'] = array($this->params['ELEMENT_UID']);
		foreach($this->params['ELEMENT_UID'] as $tuid)
		{
			$uid = $valUid = $nameUid = '';
			$canSubstring = true;
			
			$uid = $tuid;
			$nameUid = $iblockFields[$tuid]['NAME_LANG'];
			$valUid = $arFieldsElementOrig[$uid];
			
			if($iblockFields[$uid]['USER_TYPE_ID']=='hlblock')
			{
				$valUid = $this->GetHighloadBlockValue($iblockFields[$uid], $valUid);
				$canSubstring = false;
			}
			elseif($iblockFields[$uid]['USER_TYPE_ID']=='iblock_element')
			{
				$valUid = $this->GetIblockElementValue($iblockFields[$uid], $valUid, $this->fieldSettings[$tuid]);
				$canSubstring = false;
			}
			elseif($iblockFields[$uid]['USER_TYPE_ID']=='enumeration')
			{
				$valUid = $this->GetHighloadBlockEnum($iblockFields[$uid], $valUid);
				$canSubstring = false;
			}
			
			if($uid)
			{
				$arUid[] = array(
					'uid' => $uid,
					'nameUid' => $nameUid,
					'valUid' => $valUid,
					'substring' => ($this->fieldSettings[$tuid]['UID_SEARCH_SUBSTRING']=='Y' && $canSubstring)
				);
			}
		}
		
		$emptyFields = array();
		foreach($arUid as $k=>$v)
		{
			if(!trim($v['valUid'])) $emptyFields[] = $v['nameUid'];
		}
		
		if(!empty($emptyFields) || empty($arUid))
		{
			$this->errors[] = sprintf(GetMessage("KDA_IE_NOT_SET_FIELD"), implode(', ', $emptyFields), $this->worksheetNumForSave+1, $this->worksheetCurrentRow);
			$this->stepparams['error_line']++;
			return false;
		}
		
		foreach($arFieldsElement as $k=>$v)
		{
			if($iblockFields[$k]['MULTIPLE']=='Y')
			{
				if(!is_array($v))
				{
					$separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
					if($this->fieldSettings[$k]['CHANGE_MULTIPLE_SEPARATOR']=='Y')
					{
						$separator = $this->fieldSettings[$k]['MULTIPLE_SEPARATOR'];
					}
					$v = explode($separator, $v);
				}
				$arFieldsElement[$k] = array();
				foreach($v as $v2)
				{
					$arFieldsElement[$k][] = $this->GetElementFieldValue($v2, $iblockFields[$k], $k);
				}
			}
			else
			{
				$arFieldsElement[$k] = $this->GetElementFieldValue($v, $iblockFields[$k], $k);
			}
		}
		
		$arKeys = array_merge(array('ID'), array_keys($arFieldsElement));
		
		$arFilter = array();
		foreach($arUid as $v)
		{
			if(!$v['substring'])
			{
				if(strlen($v['valUid']) != strlen(trim($v['valUid'])))
				{
					$arFilter[] = array('LOGIC'=>'OR', array($v['uid']=>trim($v['valUid'])), array($v['uid']=>$v['valUid']));
				}
				else
				{
					$arFilter[$v['uid']] = trim($v['valUid']);
				}
			}
			else
			{
				$arFilter['%'.$v['uid']] = trim($v['valUid']);
			}
		}
		
		$dbRes = $entityDataClass::GetList(array('filter'=>$arFilter, 'select'=>$arKeys));
		while($arElement = $dbRes->Fetch())
		{
			$ID = $arElement['ID'];
			if($this->params['ONLY_CREATE_MODE']!='Y')
			{
				foreach($arElement as $k=>$v)
				{
					$action = $this->fieldSettings['IE_'.$k]['LOADING_MODE'];
					if($action)
					{
						if($action=='ADD_BEFORE') $arFieldsElement[$k] = $arFieldsElement[$k].$v;
						elseif($action=='ADD_AFTER') $arFieldsElement[$k] = $v.$arFieldsElement[$k];
					}
				}
				
				if($this->params['ELEMENT_NOT_UPDATE_WO_CHANGES']=='Y')
				{
					/*Delete unchanged data*/
					foreach($arFieldsElement as $k=>$v)
					{
						if($v==$arElement[$k])
						{
							unset($arFieldsElement[$k]);
						}
					}
					/*/Delete unchanged data*/
				}
				
				if(!empty($this->fieldOnlyNew))
				{
					$this->UnsetExcessFields($this->fieldOnlyNew, $arFieldsElement);
				}
				
				if(!empty($arFieldsElement))
				{
					if($entityDataClass::Update($ID, $arFieldsElement))
					{
						//$this->SetTimeBegin($ID);
					}
					else
					{
						$this->stepparams['error_line']++;
						$this->errors[] = sprintf(GetMessage("KDA_IE_UPDATE_ELEMENT_ERROR"), $el->LAST_ERROR, $this->worksheetNumForSave+1, $this->worksheetCurrentRow);
					}
				}
				
				$this->stepparams['element_updated_line']++;
			}
			$this->SaveElementId($ID);
		}
		
		if($dbRes->getSelectedRowsCount()==0 && $this->params['ONLY_UPDATE_MODE']!='Y')
		{
			$dbRes2 = $entityDataClass::Add($arFieldsElement, false, true, true);
			$ID = $dbRes2->GetID();
			
			if($ID)
			{
				//$this->SetTimeBegin($ID);
				$this->stepparams['element_added_line']++;
				$this->SaveElementId($ID);
			}
			else
			{
				$this->stepparams['error_line']++;
				$this->errors[] = sprintf(GetMessage("KDA_IE_ADD_ELEMENT_ERROR"), $el->LAST_ERROR, $this->worksheetNumForSave+1, $this->worksheetCurrentRow);
				return false;
			}
		}
		
		if($ID)
		{
			if($this->params['ONAFTERSAVE_HANDLER'])
			{
				$this->ExecuteOnAfterSaveHandler($this->params['ONAFTERSAVE_HANDLER'], $ID);
			}
		}
		
		$this->stepparams['correct_line']++;
		
		$this->SaveStatusImport();
	}
	
	public function GetHLField(&$arFieldsElement, &$arFieldsElementOrig, $fieldSettingsExtra, $propDef, $fieldName, $value, $origValue)
	{
		if(!isset($arFieldsElement[$fieldName])) $arFieldsElement[$fieldName] = null;
		if(!isset($arFieldsElementOrig[$fieldName])) $arFieldsElementOrig[$fieldName] = null;
		$arFieldsElementItem = &$arFieldsElement[$fieldName];
		$arFieldsElementOrigItem = &$arFieldsElementOrig[$fieldName];
		
		if($propDef	&& $propDef['USER_TYPE_ID']=='hlblock')
		{
			if($fieldSettingsExtra['HLBL_FIELD']) $key2 = $fieldSettingsExtra['HLBL_FIELD'];
			else $key2 = 'ID';
			if(!isset($arFieldsElementItem[$key2])) $arFieldsElementItem[$key2] = null;
			if(!isset($arFieldsElementOrigItem[$key2])) $arFieldsElementOrigItem[$key2] = null;
			$arFieldsElementItem = &$arFieldsElementItem[$key2];
			$arFieldsElementOrigItem = &$arFieldsElementOrigItem[$key2];
		}
		
		if($propDef['MULTIPLE']=='Y' && !is_null($arFieldsElementItem))
		{
			$arFieldsElement[$field][] = $value;
			$arFieldsElementOrig[$field][] = $origValue;
			if(is_array($arFieldsElementItem))
			{
				$arFieldsElementItem[] = $value;
				$arFieldsElementOrigItem[] = $origValue;
			}
			else
			{
				$arFieldsElementItem = array($arFieldsElementItem, $value);
				$arFieldsElementOrigItem = array($arFieldsElementOrigItem, $origValue);
			}
		}
		else
		{
			$arFieldsElementItem = $value;
			$arFieldsElementOrigItem = $origValue;
		}
	}
	
	public function SaveStatusImport($end = false)
	{
		if($this->procfile)
		{
			$writeParams = array_merge($this->stepparams, array(
				'xmlCurrentRow' => intval($this->xmlCurrentRow)
			));
			$writeParams['action'] = ($end ? 'finish' : 'continue');
			file_put_contents($this->procfile, \CUtil::PhpToJSObject($writeParams));
		}
	}
	
	public function GetElementFieldValue($val, $fieldParam, $key)
	{
		$ftype = $fieldParam['USER_TYPE_ID'];
		if($ftype=='integer')
		{
			$val = $this->GetIntVal($val);
		}
		elseif($ftype=='double')
		{
			$val = $this->GetFloatVal($val);
		}
		elseif($ftype=='datetime')
		{
			$val = $this->GetDateVal($val);
		}
		elseif($ftype=='date')
		{
			$val = $this->GetDateVal($val, 'PART');
		}
		elseif($ftype=='boolean')
		{
			$val = $this->GetHLBoolValue($val);
		}
		elseif($ftype=='file')
		{
			$picSettings = array();
			if($this->fieldSettings[$key]['PICTURE_PROCESSING'])
			{
				$picSettings = $this->fieldSettings[$key]['PICTURE_PROCESSING'];
			}
			$val = $this->GetFileArray($val, $picSettings);
		}
		elseif($ftype=='enumeration')
		{
			$val = $this->GetHighloadBlockEnum($fieldParam, $val);
		}
		elseif($ftype=='hlblock')
		{
			$val = $this->GetHighloadBlockValue($fieldParam, $val);
		}
		elseif($ftype=='iblock_element')
		{
			$val = $this->GetIblockElementValue($fieldParam, $val, $this->fieldSettings[$key], true);
		}
		elseif($ftype=='iblock_section')
		{
			$relField = $this->fieldSettings[$key]['REL_SECTION_FIELD'];
			if((!$relField || $relField=='ID') && !is_numeric($val))
			{
				$relField = 'NAME';
			}
			if($relField && $relField!='ID' && $val && $fieldParam['SETTINGS']['IBLOCK_ID'])
			{
				$arFilter = array(
					'IBLOCK_ID' => $fieldParam['SETTINGS']['IBLOCK_ID'],
					$relField => $val,
					'CHECK_PERMISSIONS' => 'N'
				);
				$dbRes = \CIblockSection::GetList(array('ID'=>'ASC'), $arFilter, false, array('ID'), array('nTopCount'=>1));
				if($arElem = $dbRes->Fetch()) $val = $arElem['ID'];
				else $val = '';
			}
		}
		return $val;
	}
	
	public function GetIntVal($val)
	{
		return intval(preg_replace('/[^\d\.\-]+/', '', str_replace(',', '.', $val)));
	}
	
	public function GetFloatVal($val)
	{
		return floatval(preg_replace('/[^\d\.\-]+/', '', str_replace(',', '.', $val)));
	}
	
	public function GetHighloadBlockEnum($fieldParam, $val)
	{		
		if(!$this->hlblEnum) $this->hlblEnum = array();
		if(!$this->hlblEnum[$fieldParam['ID']])
		{
			$arEnumVals = array();
			$fenum = new \CUserFieldEnum();
			$dbRes = $fenum->GetList(array(), array('USER_FIELD_ID'=>$fieldParam['ID']));
			while($arr = $dbRes->Fetch())
			{
				$arEnumVals[trim($arr['VALUE'])] = $arr['ID'];
			}
			$this->hlblEnum[$fieldParam['ID']] = $arEnumVals;
		}
		
		$val = trim($val);
		$arEnumVals = $this->hlblEnum[$fieldParam['ID']];
		if(!isset($arEnumVals[$val]))
		{
			$fenum = new \CUserFieldEnum();
			$arEnumValsOrig = array();
			$dbRes = $fenum->GetList(array(), array('USER_FIELD_ID'=>$fieldParam['ID']));
			while($arr = $dbRes->Fetch())
			{
				$arEnumValsOrig[$arr['ID']] = $arr;
			}
			$arEnumValsOrig['n0'] = array('VALUE'=>$val);
			$fenum->SetEnumValues($fieldParam['ID'], $arEnumValsOrig);

			$arEnumVals = array();
			$dbRes = $fenum->GetList(array(), array('USER_FIELD_ID'=>$fieldParam['ID']));
			while($arr = $dbRes->Fetch())
			{
				$arEnumVals[trim($arr['VALUE'])] = $arr['ID'];
			}
			$this->hlblEnum[$fieldParam['ID']] = $arEnumVals;
		}
		return $arEnumVals[$val];
	}
	
	public function UnsetUidFields(&$arFieldsElement, &$arFieldsProps, $arUids, $saveVal=false)
	{
		foreach($arUids as $field)
		{
			if(strpos($field, 'IE_')===0)
			{
				$fieldKey = substr($field, 3);
				if(isset($arFieldsElement[$fieldKey]) && is_array($arFieldsElement[$fieldKey]))
				{
					if($saveVal)
					{
						$arFieldsElement[$fieldKey] = array_diff($arFieldsElement[$fieldKey], array(''));
						if(count($arFieldsElement[$fieldKey]) > 0) $arFieldsElement[$fieldKey] = end($arFieldsElement[$fieldKey]);
						else $arFieldsElement[$fieldKey] = '';
					}
					else unset($arFieldsElement[$fieldKey]);
				}
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldKey = substr($field, 7);
				if(isset($arFieldsProps[$fieldKey]) && is_array($arFieldsProps[$fieldKey]))
				{
					if($saveVal)
					{
						$arFieldsProps[$fieldKey] = array_diff($arFieldsProps[$fieldKey], array(''));
						if(count($arFieldsProps[$fieldKey]) > 0) $arFieldsProps[$fieldKey] = end($arFieldsProps[$fieldKey]);
						else $arFieldsProps[$fieldKey] = '';
					}
					else unset($arFieldsProps[$fieldKey]);
				}
			}
		}
	}
	
	public function UnsetExcessFields($fieldsList, &$arFieldsElement)
	{
		foreach($fieldsList as $field)
		{
			unset($arFieldsElement[$field]);
		}
	}
	
	public function SaveElementId($ID, $offer=false)
	{
		$fn = $this->fileElementsId;
		$handle = fopen($fn, 'a');
		fwrite($handle, $ID."\r\n");
		fclose($handle);
	}
	
	function GetFilesByExt($path, $arExt=array())
	{
		$arFiles = array();
		$arDirFiles = array_diff(scandir($path), array('.', '..'));
		foreach($arDirFiles as $file)
		{
			if(is_file($path.$file) && (empty($arExt) || preg_match('/\.('.implode('|', $arExt).')$/i', ToLower($file))))
			{
				$arFiles[] = $path.$file;
			}
		}
		foreach($arDirFiles as $file)
		{
			if(is_dir($path.$file))
			{
				$arFiles = array_merge($arFiles, $this->GetFilesByExt($path.$file.'/', $arExt));
			}
		}
		return $arFiles;
	}
	
	public function CreateTmpImageDir()
	{
		$tmpsubdir = $this->imagedir.($this->filecnt++).'/';
		CheckDirPath($tmpsubdir);
		return $tmpsubdir;
	}
	
	public function GetFileArray($file, $arDef=array(), $arParams=array())
	{
		if(is_array($file))
		{
			if($arParams['MULTIPLE']=='Y')
			{
				$arFiles = array();
				foreach($file as $subfile)
				{
					$arFiles[] = $this->GetFileArray($subfile, $arDef, $arParams);
				}
				return $arFiles;
			}
			else
			{
				$file = current($file);
			}
		}
		
		$fileOrig = $file = trim($file);
		if($file=='-')
		{
			return array('del'=>'Y');
		}
		elseif($tmpFile = $this->GetFileFromArchive($fileOrig))
		{
			$file = $tmpFile;
		}
		elseif(strpos($file, '/')===0)
		{
			$file = \Bitrix\Main\IO\Path::convertLogicalToPhysical($file);
			$tmpsubdir = $this->CreateTmpImageDir();
			$arFile = \CFile::MakeFileArray($file);
			$file = $tmpsubdir.$arFile['name'];
			copy($arFile['tmp_name'], $file);
		}
		elseif(strpos($file, 'zip://')===0)
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			$oldfile = $file;
			$file = $tmpsubdir.basename($oldfile);
			copy($oldfile, $file);
		}
		elseif(preg_match('/ftp(s)?:\/\//', $file))
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			$arFile = $this->sftp->MakeFileArray($file);
			$file = $tmpsubdir.$arFile['name'];
			copy($arFile['tmp_name'], $file);
		}
		elseif($service = $this->cloud->GetService($file))
		{
			$tmpsubdir = $this->CreateTmpImageDir();
			if($arFile = $this->cloud->MakeFileArray($service, $file))
			{
				$file = $tmpsubdir.$arFile['name'];
				copy($arFile['tmp_name'], $file);
			}
		}
		elseif(preg_match('/http(s)?:\/\//', $file))
		{
			//$file = urldecode($file);
			$file = preg_replace_callback('/[^:\/?=&#@]+/', create_function('$m', 'return urldecode($m[0]);'), $file);
			$arUrl = parse_url($file);
			//Cyrillic domain
			if(preg_match('/[^A-Za-z0-9\-\.]/', $arUrl['host']))
			{
				if(!class_exists('idna_convert')) require_once(dirname(__FILE__).'/idna_convert.class.php');
				if(class_exists('idna_convert'))
				{
					$idn = new \idna_convert();
					$oldHost = $arUrl['host'];
					if(!\CUtil::DetectUTF8($oldHost)) $oldHost = \Bitrix\EsolImportxml\Utils::Win1251Utf8($oldHost);
					$file = str_replace($arUrl['host'], $idn->encode($oldHost), $file);
				}
			}
			if(class_exists('\Bitrix\Main\Web\HttpClient'))
			{
				$tmpsubdir = $this->CreateTmpImageDir();
				$basename = bx_basename($file);
				if(preg_match('/^[_+=!?]*\./', $basename)) $basename = 'f'.$basename;
				$tempPath = $tmpsubdir.$basename;
				$tempPath2 = $tmpsubdir.(\Bitrix\Main\IO\Path::convertLogicalToPhysical($basename));
				$arOptions = array();
				if($this->useProxy) $arOptions = $this->proxySettings;
				$arOptions['disableSslVerification'] = true;
				$arOptions['socketTimeout'] = $arOptions['streamTimeout'] = 10;
				$ob = new \Bitrix\Main\Web\HttpClient($arOptions);
				$ob->setHeader('User-Agent', 'BitrixSM HttpClient class');
				try{
					if(!\CUtil::DetectUTF8($file)) $file = \Bitrix\EsolImportxml\Utils::Win1251Utf8($file);
					$file = preg_replace_callback('/[^:\/?=&#@]+/', create_function('$m', 'return rawurlencode($m[0]);'), $file);
					if($ob->download($file, $tempPath) && $ob->getStatus()!=404) $file = $tempPath2;
					else return array();
				}catch(Exception $ex){}
			}
		}
		$arFile = \CFile::MakeFileArray($file);
		
		if(!file_exists($file) && !$arFile['name'] && !\CUtil::DetectUTF8($file))
		{
			$file = \Bitrix\EsolImportxml\Utils::Win1251Utf8($file);
			$arFile = \CFile::MakeFileArray($file);
		}
		
		$fileTypes = array();
		$bNeedImage = (bool)($arParams['FILETYPE']=='IMAGE');
		if($bNeedImage) $fileTypes = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
		elseif($arParams['FILE_TYPE']) $fileTypes = array_diff(array_map('trim', explode(',', ToLower($arParams['FILE_TYPE']))), array(''));
		$dirname = '';
		if(file_exists($file) && is_dir($file))
		{
			$dirname = $file;
		}
		elseif($arFile['type']=='application/zip' && !empty($fileTypes) && !in_array('zip', $fileTypes))
		{
			$archiveParams = $this->GetArchiveParams($fileOrig);
			if(!$archiveParams['exists'])
			{
				CheckDirPath($archiveParams['path']);
				$zipObj = \CBXArchive::GetArchive($arFile['tmp_name'], 'ZIP');
				$zipObj->Unpack($archiveParams['path']);
			}
			$dirname = $archiveParams['file'];
		}
		if(strlen($dirname) > 0)
		{
			$arFile = array();
			if(file_exists($dirname) && is_file($dirname)) $arFiles = array($dirname);
			else $arFiles = $this->GetFilesByExt($dirname, $fileTypes);
			if($arParams['MULTIPLE']=='Y' && count($arFiles) > 1)
			{
				foreach($arFiles as $k=>$v)
				{
					$arFiles[$k] = \CFile::MakeFileArray($v);
				}
				$arFile = array('VALUES'=>$arFiles);
			}
			elseif(count($arFiles) > 0)
			{
				$tmpfile = current($arFiles);
				$arFile = \CFile::MakeFileArray($tmpfile);
			}
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
		elseif($bNeedImage) $arFile = array();

		if(!empty($arDef) && !empty($arFile))
		{
			if(isset($arFile['VALUES']))
			{
				foreach($arFile['VALUES'] as $k=>$v)
				{
					$arFile['VALUES'][$k] = $this->PictureProcessing($v, $arDef);
				}
			}
			else
			{
				$arFile = $this->PictureProcessing($arFile, $arDef);
			}
		}
		if(!empty($arFile) && strpos($arFile['type'], 'image/')===0)
		{
			$arCacheKeys = array('width'=>$width, 'height'=>$height, 'size'=>$arFile['size']);
			if($this->params['ELEMENT_NOT_CHECK_NAME_IMAGES']!='Y') $arCacheKeys['name'] = $arFile['name'];
			list($width, $height, $type, $attr) = getimagesize($arFile['tmp_name']);
			$arFile['external_id'] = 'i_'.md5(serialize($arCacheKeys));
		}
		if(!empty($arFile) && strpos($arFile['type'], 'html')!==false)
		{
			$arFile = array();
		}
		
		return $arFile;
	}	
	
	public function GetArchiveParams($file)
	{
		$arUrl = parse_url($file);
		$fragment = (isset($arUrl['fragment']) ? $arUrl['fragment'] : '');
		if(strlen($fragment) > 0) $file = substr($file, 0, -strlen($fragment) - 1);
		$archivePath = $this->archivedir.md5($file).'/';
		return array(
			'path' => $archivePath, 
			'exists' => file_exists($archivePath),
			'file' => $archivePath.ltrim($fragment, '/')
		);
	}
	
	public function GetFileFromArchive($file)
	{
		$archiveParams = $this->GetArchiveParams($file);
		if(!$archiveParams['exists']) return false;
		return $archiveParams['file'];
	}
	
	public function GetHLBoolValue($val)
	{
		$res = $this->GetBoolValue($val);
		if($res=='Y') return 1;
		else return 0;
	}
	
	public function GetBoolValue($val, $numReturn = false)
	{
		$trueVals = array_map('trim', explode(',', Loc::getMessage("ESOL_IX_FIELD_VAL_Y")));
		$falseVals = array_map('trim', explode(',', Loc::getMessage("ESOL_IX_FIELD_VAL_N")));
		if(in_array(ToLower($val), $trueVals))
		{
			return ($numReturn ? 1 : 'Y');
		}
		elseif(in_array(ToLower($val), $falseVals))
		{
			return ($numReturn ? 0 : 'N');
		}
		else
		{
			return false;
		}
	}
	
	public function GetIblockElementValue($arProp, $val, $fsettings, $bAdd=false)
	{
		if($fsettings['REL_ELEMENT_FIELD'] && $fsettings['REL_ELEMENT_FIELD']!='IE_ID' && $arProp['SETTINGS']['IBLOCK_ID'])
		{
			$tuid = $fsettings['REL_ELEMENT_FIELD'];
			$arFilter = array('IBLOCK_ID'=>$arProp['SETTINGS']['IBLOCK_ID']);
			if(strpos($tuid, 'IE_')===0)
			{
				$arFilter[substr($tuid, 3)] = $val;
			}
			elseif(strpos($tuid, 'IP_PROP')===0)
			{
				$uid = substr($tuid, 7);
				if($arProp['PROPERTY_TYPE']=='L')
				{
					$arFilter['PROPERTY_'.$uid.'_VALUE'] = $val;
				}
				else
				{
					if($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
					{
						$val = $this->GetDictionaryValue($arProp, $val);
					}
					$arFilter['PROPERTY_'.$uid] = $val;
				}
			}

			$dbRes = \CIblockElement::GetList(array(), $arFilter, false, array('nTopCount'=>1), array('ID'));
			if($arRes = $dbRes->Fetch())
			{
				$val = $arRes['ID'];
			}
			elseif($bAdd && $arFilter['NAME'] && $arFilter['IBLOCK_ID'])
			{
				$iblockFields = $this->GetIblockFields($arFilter['IBLOCK_ID']);
				$this->GenerateElementCode($arFilter, $iblockFields);
				$el = new \CIblockElement();
				$val = $el->Add($arFilter, false, true, true);
			}
		}

		return $val;
	}
	
	public function GetIblockFields($IBLOCK_ID)
	{
		if(!$this->iblockFields[$IBLOCK_ID])
		{
			$this->iblockFields[$IBLOCK_ID] = \CIBlock::GetFields($IBLOCK_ID);
		}
		return $this->iblockFields[$IBLOCK_ID];
	}
	
	public function GenerateElementCode(&$arElement, $iblockFields)
	{
		if(($iblockFields['CODE']['IS_REQUIRED']=='Y' || $iblockFields['CODE']['DEFAULT_VALUE']['TRANSLITERATION']=='Y') && strlen($arElement['CODE'])==0 && strlen($arElement['NAME'])>0)
		{
			$arElement['CODE'] = $this->Str2Url($arElement['NAME'], $iblockFields['CODE']['DEFAULT_VALUE']);
			if($iblockFields['CODE']['DEFAULT_VALUE']['UNIQUE']=='Y')
			{
				$i = 0;
				while(($tmpCode = $arElement['CODE'].($i ? '-'.mt_rand() : '')) && \CIblockElement::GetList(array(), array('IBLOCK_ID'=>$arElement['IBLOCK_ID'], 'CODE'=>$tmpCode, 'CHECK_PERMISSIONS' => 'N'), array()) > 0 && ++$i){}
				$arElement['CODE'] = $tmpCode;
			}
		}
	}
	
	public function GetDictionaryValue($arProp, $val)
	{	
		if($val && Loader::includeModule('highloadblock') && $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])
		{
			$arFields = $val;
			if(!is_array($arFields))
			{
				$arFields = array('UF_NAME'=>$arFields);
			}
			$cacheKey = $arFields['UF_NAME'];

			if(!isset($this->propVals[$arProp['ID']][$cacheKey]))
			{
				if(!$this->hlbl[$arProp['ID']] || !$this->hlblFields[$arProp['ID']])
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
					if(!$this->hlbl[$arProp['ID']])
					{
						$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
						$this->hlbl[$arProp['ID']] = $entity->getDataClass();
					}
					if(!$this->hlblFields[$arProp['ID']])
					{
						$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>'HLBLOCK_'.$hlblock['ID']));
						$arHLFields = array();
						while($arHLField = $dbRes->Fetch())
						{
							$arHLFields[$arHLField['FIELD_NAME']] = $arHLField;
						}
						$this->hlblFields[$arProp['ID']] = $arHLFields;
					}
				}
				$entityDataClass = $this->hlbl[$arProp['ID']];
				$arHLFields = $this->hlblFields[$arProp['ID']];
				
				if(!$arFields['UF_NAME']) return false;
				$this->PrepareHighLoadBlockFields($arFields, $arHLFields);
				
				$dbRes2 = $entityDataClass::GetList(array('filter'=>array("UF_NAME"=>$arFields['UF_NAME']), 'select'=>array('ID', 'UF_XML_ID'), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					if(count($arFields) > 1)
					{
						$entityDataClass::Update($arr2['ID'], $arFields);
					}
					$this->propVals[$arProp['ID']][$cacheKey] = $arr2['ID'];
				}
				else
				{
					$dbRes3 = $entityDataClass::Add($arFields);
					$this->propVals[$arProp['ID']][$cacheKey] = $dbRes3->GetId();
				}
			}
			return $this->propVals[$arProp['ID']][$cacheKey];
		}
		return $val;
	}
	
	public function GetHighloadBlockValue($arProp, $val)
	{
		if($val && Loader::includeModule('highloadblock') && $arProp['SETTINGS']['HLBLOCK_ID'])
		{
			$arFields = $val;
			if(!is_array($arFields))
			{
				$arFields = array('UF_NAME'=>$arFields);
			}
			if(count(array_diff($arFields, array('')))==0) return false;
			
			if(count($arFields)==1) $cacheKey = md5(serialize($arFields));
			elseif($arFields['ID']) $cacheKey = 'ID_'.$arFields['ID'];
			elseif($arFields['UF_XML_ID']) $cacheKey = 'UF_XML_ID_'.$arFields['UF_XML_ID'];
			else $cacheKey = 'UF_NAME_'.$arFields['UF_NAME'];

			if(!isset($this->propVals[$arProp['ID']][$cacheKey]))
			{
				if(!$this->hlbl[$arProp['ID']] || !$this->hlblFields[$arProp['ID']])
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID'=>$arProp['SETTINGS']['HLBLOCK_ID'])))->fetch();
					if(!$this->hlbl[$arProp['ID']])
					{
						$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
						$this->hlbl[$arProp['ID']] = $entity->getDataClass();
					}
					if(!$this->hlblFields[$arProp['ID']])
					{
						$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>'HLBLOCK_'.$hlblock['ID']));
						$arHLFields = array();
						while($arHLField = $dbRes->Fetch())
						{
							$arHLFields[$arHLField['FIELD_NAME']] = $arHLField;
						}
						$this->hlblFields[$arProp['ID']] = $arHLFields;
					}
				}
				$entityDataClass = $this->hlbl[$arProp['ID']];
				$arHLFields = $this->hlblFields[$arProp['ID']];
				
				//if(!$arFields['ID'] && !$arFields['UF_NAME'] && !$arFields['UF_XML_ID']) return false;
				$this->PrepareHighLoadBlockFields($arFields, $arHLFields);
				
				if(count($arFields)==1) $arFilter = $arFields;
				elseif($arFields['ID']) $arFilter = array("ID"=>$arFields['ID']);
				elseif($arFields['UF_XML_ID']) $arFilter = array("UF_XML_ID"=>$arFields['UF_XML_ID']);
				else $arFilter = array("UF_NAME"=>$arFields['UF_NAME']);
				$dbRes2 = $entityDataClass::GetList(array('filter'=>$arFilter, 'select'=>array('ID'), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					if(count($arFields) > 1)
					{
						$entityDataClass::Update($arr2['ID'], $arFields);
					}
					$this->propVals[$arProp['ID']][$cacheKey] = $arr2['ID'];
				}
				else
				{
					if(count(array_diff(array_keys($arFields), array('ID'))) > 0 && $dbRes3 = $entityDataClass::Add($arFields))
						$this->propVals[$arProp['ID']][$cacheKey] = $dbRes3->GetId();
					else $this->propVals[$arProp['ID']][$cacheKey] = false;
				}
			}
			return $this->propVals[$arProp['ID']][$cacheKey];
		}
		return $val;
	}
	
	public function PrepareHighLoadBlockFields(&$arFields, $arHLFields)
	{
		foreach($arFields as $k=>$v)
		{
			if($k == 'ID')
			{
				$arFields[$k] = $this->GetFloatVal($v);
				continue;
			}
			if(!isset($arHLFields[$k]))
			{
				unset($arFields[$k]);
			}
			$type = $arHLFields[$k]['USER_TYPE_ID'];
			if($type=='file')
			{
				$arFields[$k] = $this->GetFileArray($v);
			}
			elseif($type=='integer' || $type=='double')
			{
				$arFields[$k] = $this->GetFloatVal($v);
			}
			elseif($type=='datetime')
			{
				$arFields[$k] = $this->GetDateVal($v);
			}
			elseif($type=='date')
			{
				$arFields[$k] = $this->GetDateVal($v, 'PART');
			}
			elseif($type=='boolean')
			{
				$arFields[$k] = $this->GetHLBoolValue($v);
			}
		}		
	}
	
	public function GetHLHLValue($val, $arSettings)
	{
		if(!Loader::includeModule('highloadblock')) return $val;
		$hlblId = $arSettings['HLBLOCK_ID'];
		$fieldId = $arSettings['HLFIELD_ID'];
		if($val && $hlblId && $fieldId)
		{
			if(!is_array($this->hlhlbl)) $this->hlhlbl = array();
			if(!is_array($this->hlhlblFields)) $this->hlhlblFields = array();
			if(!is_array($this->hlPropVals)) $this->hlPropVals = array();

			if(!isset($this->hlPropVals[$fieldId][$val]))
			{
				if(!$this->hlhlbl[$hlblId] || !$this->hlhlblFields[$hlblId])
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('ID'=>$hlblId)))->fetch();
					if(!$this->hlhlbl[$hlblId])
					{
						$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
						$this->hlhlbl[$hlblId] = $entity->getDataClass();
					}
					if(!$this->hlhlblFields[$hlblId])
					{
						$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>'HLBLOCK_'.$hlblock['ID']));
						$arHLFields = array();
						while($arHLField = $dbRes->Fetch())
						{
							$arHLFields[$arHLField['ID']] = $arHLField;
						}
						$this->hlhlblFields[$hlblId] = $arHLFields;
					}
				}
				
				$entityDataClass = $this->hlhlbl[$hlblId];
				$arHLFields = $this->hlhlblFields[$hlblId];
				
				if(!$arHLFields[$fieldId]) return false;
				
				$dbRes2 = $entityDataClass::GetList(array('filter'=>array($arHLFields[$fieldId]['FIELD_NAME']=>$val), 'select'=>array('ID'), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					$this->hlPropVals[$fieldId][$val] = $arr2['ID'];
				}
				else
				{
					$arFields = array($arHLFields[$fieldId]['FIELD_NAME']=>$val);
					$dbRes2 = $entityDataClass::Add($arFields);
					$this->hlPropVals[$fieldId][$val] = $dbRes2->GetID();
				}
			}
			return $this->hlPropVals[$fieldId][$val];
		}
		return $val;
	}
	
	public function PictureProcessing($arFile, $arDef)
	{
		if($arDef["SCALE"] === "Y")
		{
			$arNewPicture = \CIBlock::ResizePicture($arFile, $arDef);
			if(is_array($arNewPicture))
			{
				$arFile = $arNewPicture;
			}
			/*elseif($arDef["IGNORE_ERRORS"] !== "Y")
			{
				unset($arFile);
				$strWarning .= Loc::getMessage("IBLOCK_FIELD_PREVIEW_PICTURE").": ".$arNewPicture."<br>";
			}*/
		}

		if($arDef["USE_WATERMARK_FILE"] === "Y")
		{
			\CIBLock::FilterPicture($arFile["tmp_name"], array(
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
			\CIBLock::FilterPicture($arFile["tmp_name"], array(
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
	
	public function GetCurrencyRates()
	{
		if(!isset($this->currencyRates))
		{
			$arRates = array();
			$currFile = $this->tmpdir.'/currencies.txt';
			if(file_exists($currFile))
			{
				$arRates = unserialize(file_get_contents($currFile));
			}
			else
			{
				$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>20, 'disableSslVerification'=>true));
				$res = $client->get('http://www.cbr.ru/scripts/XML_daily.asp');
				if($res)
				{
					$xml = simplexml_load_string($res);
					if($xml->Valute)
					{
						foreach($xml->Valute as $val)
						{
							$arRates[(string)$val->CharCode] = $this->GetFloatVal((string)$val->Value);
						}
					}
				}
				file_put_contents($currFile, serialize($arRates));
			}
			$this->currencyRates = $arRates;
		}
		return $this->currencyRates;
	}
	
	public function ConversionReplaceValues($m)
	{
		if(preg_match('/^\{(([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*)\}$/', $m[0], $m2))
		{
			return $this->GetValueByXpath($m2[1]);
		}
		elseif(in_array($m[0], $this->rcurrencies))
		{
			$arRates = $this->GetCurrencyRates();
			$k = trim($m[0], '#');
			return (isset($arRates[$k]) ? floatval($arRates[$k]) : 1);
		}
	}
	
	public function GetValueByXpath($xpath, $simpleXmlObj=null)
	{
		if(preg_match('/^\d+$/', $xpath) && isset($this->currentItemValues[$xpath]))
		{
			return $this->currentItemValues[$xpath];
		}
		if(preg_match('/^[\d,]*$/', $xpath))
		{
			return '{'.$xpath.'}';
		}
		
		$val = '';
		
		/*if(strlen($xpath) > 0) $arPath = explode('/', $xpath);
		else $arPath = array();
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
		}*/
		$arXPath = $this->GetXPathParts($xpath);
		$curXpath2 = $arXPath['xpath'];
		$subXpath = $arXPath['subpath'];
		$attr = $arXPath['attr'];
		$currentXmlObj = $this->currentXmlObj;
		if(isset($simpleXmlObj)) $currentXmlObj = $simpleXmlObj;
		
		if(strlen($curXpath2) > 0)
		{
			$curXpath = '/'.ltrim($curXpath2, '/');
			if(isset($this->parentXpath) && strlen($this->parentXpath) > 0 && strpos($curXpath, $this->parentXpath)===0)
			{
				$tmpXpath = substr($curXpath, strlen($this->parentXpath) + 1);
				//$tmpXmlObj = $currentXmlObj->xpath($tmpXpath);
				$tmpXmlObj = $this->Xpath($currentXmlObj, $tmpXpath);
				if(!empty($tmpXmlObj))
				{
					$currentXmlObj = $tmpXmlObj;
					$curXpath = '';
				}
			}
			if(strlen($curXpath) > 0)
			{
				if(strpos($curXpath, $this->xpath)===0) $curXpath = substr($curXpath, strlen($this->xpath) + 1);
				elseif(isset($this->xmlPartObjects[$curXpath2]))
				{
					//$currentXmlObj = $this->xmlPartObjects[$curXpath2]->xpath($subXpath);
					$currentXmlObj = $this->Xpath($this->xmlPartObjects[$curXpath2], $subXpath);
					$curXpath = '';
				}
			}
			//if(strlen($curXpath) > 0) $simpleXmlObj2 = $currentXmlObj->xpath($curXpath);
			if(strlen($curXpath) > 0) $simpleXmlObj2 = $this->Xpath($currentXmlObj, ltrim($curXpath, '/'));
			else $simpleXmlObj2 = $currentXmlObj;
			if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
		}
		else $simpleXmlObj2 = $currentXmlObj;
		if(is_array($simpleXmlObj2)) $simpleXmlObj2 = current($simpleXmlObj2);
		
		if($attr!==false)
		{
			if(is_callable(array($simpleXmlObj2, 'attributes')))
			{
				$val = (string)$simpleXmlObj2->attributes()->{$attr};
			}
		}
		else
		{
			$val = (string)$simpleXmlObj2;					
		}
		
		$val = $this->GetRealXmlValue($val);
		
		return $val;
	}
	
	public function Xpath($simpleXmlObj, $xpath)
	{
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$xpath = \Bitrix\Main\Text\Encoding::convertEncoding($xpath, $this->siteEncoding, $this->fileEncoding);
		}
		if(preg_match('/((^|\/)[^\/]+):/', $xpath, $m))
		{
			$nss = $simpleXmlObj->getNamespaces(true);
			$nsKey = $m[1];
			if(isset($nss[$nsKey]))
			{
				$simpleXmlObj->registerXPathNamespace($nsKey, $nss[$nsKey]);
			}
		}
		if(strlen(trim($xpath)) > 0) return $simpleXmlObj->xpath($xpath);
		else return $simpleXmlObj;
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
			if(strlen($field['KEY']) > 0) $fieldKey = $field['KEY'];
		}
		
		if(is_array($arConv))
		{
			$execConv = false;
			$this->currentItemValues = $arItem;
			$prefixPattern = '/(\{([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*\}|'.implode('|', $this->rcurrencies).')/';
			foreach($arConv as $k=>$v)
			{
				$condVal = $val;

				if(preg_match('/^\{(\S*)\}$/', $v['CELL'], $m))
				{
					$condVal = $this->GetValueByXpath($m[1]);
				}

				if(strlen($v['FROM']) > 0) $v['FROM'] = preg_replace_callback($prefixPattern, array($this, 'ConversionReplaceValues'), $v['FROM']);
			
				if($v['CELL']=='ELSE') $v['WHEN'] = '';
				if(($v['CELL']=='ELSE' && !$execConv)
					|| ($v['WHEN']=='EQ' && $condVal==$v['FROM'])
					|| ($v['WHEN']=='NEQ' && $condVal!=$v['FROM'])
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
					if(strlen($v['TO']) > 0) $v['TO'] = preg_replace_callback($prefixPattern, array($this, 'ConversionReplaceValues'), $v['TO']);
					if($v['THEN']=='REPLACE_TO') $val = $v['TO'];
					elseif($v['THEN']=='REMOVE_SUBSTRING' && strlen($v['TO']) > 0) $val = str_replace($v['TO'], '', $val);
					elseif($v['THEN']=='REPLACE_SUBSTRING_TO' && strlen($v['FROM']) > 0) $val = str_replace($v['FROM'], $v['TO'], $val);
					elseif($v['THEN']=='ADD_TO_BEGIN') $val = $v['TO'].$val;
					elseif($v['THEN']=='ADD_TO_END') $val = $val.$v['TO'];
					elseif($v['THEN']=='LCASE') $val = ToLower($val);
					elseif($v['THEN']=='UCASE') $val = ToUpper($val);
					elseif($v['THEN']=='UFIRST') $val = preg_replace_callback('/^(\s*)(.*)$/', create_function('$m', 'return $m[1].ToUpper(substr($m[2], 0, 1)).ToLower(substr($m[2], 1));'), $val);
					elseif($v['THEN']=='UWORD') $val = implode(' ', array_map(create_function('$m', 'return ToUpper(substr($m, 0, 1)).ToLower(substr($m, 1));'), explode(' ', $val)));
					elseif($v['THEN']=='MATH_ROUND') $val = round($this->GetFloatVal($val));
					elseif($v['THEN']=='MATH_MULTIPLY') $val = $this->GetFloatVal($val) * $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='MATH_DIVIDE') $val = $this->GetFloatVal($val) / $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='MATH_ADD') $val = $this->GetFloatVal($val) + $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='MATH_SUBTRACT') $val = $this->GetFloatVal($val) - $this->GetFloatVal($v['TO']);
					elseif($v['THEN']=='NOT_LOAD') $val = false;
					elseif($v['THEN']=='EXPRESSION') $val = $this->ExecuteFilterExpression($val, $v['TO'], '');
					elseif($v['THEN']=='STRIP_TAGS') $val = strip_tags($val);
					elseif($v['THEN']=='CLEAR_TAGS') $val = preg_replace('/<([a-z][a-z0-9:]*)[^>]*(\/?)>/i','<$1$2>', $val);
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
	
	public function IsChangedImage($fileId, $arNewFile)
	{
		$fileId = (int)$fileId;
		if($this->params['ELEMENT_IMAGES_FORCE_UPDATE']=='Y' || !$fileId) return true;
		$arFile = \Bitrix\EsolImportxml\Utils::GetFileArray($fileId);
		$arNewFileVal = $arNewFile;
		if(isset($arNewFileVal['VALUE'])) $arNewFileVal = $arNewFileVal['VALUE'];
		if(isset($arNewFileVal['DESCRIPTION'])) $arNewFile['description'] = $arNewFile['DESCRIPTION'];
		list($width, $height, $type, $attr) = getimagesize($arNewFileVal['tmp_name']);
		if(($arFile['EXTERNAL_ID']==$arNewFileVal['external_id']
			|| ($arFile['FILE_SIZE']==$arNewFileVal['size'] 
				&& $arFile['ORIGINAL_NAME']==$arNewFileVal['name'] 
				&& (!$arFile['WIDTH'] || !$arFile['WIDTH'] || ($arFile['WIDTH']==$width && $arFile['HEIGHT']==$height))))
			&& (!isset($arNewFile['description']) || $arNewFile['description']==$arFile['DESCRIPTION']))
		{
			return false;
		}
		return true;
	}
	
	public function GetDateVal($val, $format = 'FULL')
	{
		$time = strtotime($val);
		if($time > 0)
		{
			return ConvertTimeStamp($time, $format);
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
		return \CUtil::translit($string, LANGUAGE_ID, $arParams);
	}
	
	public function GetRealXmlValue($val)
	{
		if($this->siteEncoding!=$this->fileEncoding)
		{
			$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $this->fileEncoding, $this->siteEncoding);
		}
		if($this->params['HTML_ENTITY_DECODE']=='Y')
		{
			if(is_array($val))
			{
				foreach($val as $k=>$v)
				{
					$val[$k] = html_entity_decode($v);
				}
			}
			else
			{
				$val = html_entity_decode($val);
			}
		}
		return $val;
	}
	
	public function OnShutdown()
	{
		$arError = error_get_last();
		if(!is_array($arError) || !isset($arError['type']) || !in_array($arError['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR))) return;
		
		$this->EndWithError(sprintf(Loc::getMessage("ESOL_IX_FATAL_ERROR"), $arError['type'], $arError['message'], $arError['file'], $arError['line']));
	}
	
	public function HandleError($code, $message, $file, $line)
	{
		return true;
	}
	
	public function HandleException($exception)
	{
		if(is_callable(array('\Bitrix\Main\Diag\ExceptionHandlerFormatter', 'format')))
		{
			$this->EndWithError(\Bitrix\Main\Diag\ExceptionHandlerFormatter::format($exception));
		}
		$this->EndWithError(sprintf(Loc::getMessage("ESOL_IX_FATAL_ERROR"), '', $exception->getMessage(), $exception->getFile(), $exception->getLine()));
	}
	
	public function EndWithError($error)
	{
		global $APPLICATION;
		$APPLICATION->RestartBuffer();
		ob_end_clean();
		$this->errors[] = $error;
		$this->SaveStatusImport();
		echo '<!--module_return_data-->'.(\CUtil::PhpToJSObject($this->GetBreakParams()));
		die();
	}
}