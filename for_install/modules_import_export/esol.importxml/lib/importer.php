<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Importer {
	protected static $moduleId = 'esol.importxml';
	var $rcurrencies = array('#USD#', '#EUR#');
	var $xmlParts = array();
	var $xmlPartsValues = array();
	var $xmlSingleElems = array();
	var $arTmpImageDirs = array();
	var $arTmpImages = array();
	var $tagIblocks = array();
	var $offerParentId = null;
	
	function __construct($filename, $params, $fparams, $stepparams, $pid = false)
	{
		$this->filename = $_SERVER['DOCUMENT_ROOT'].$filename;
		$this->params = $params;
		$this->fparams = $fparams;
		$this->sections = array();
		$this->sectionIds = array();
		$this->sectionsTmp = array();
		$this->propertyIds = array();
		$this->propVals = array();
		$this->hlbl = array();
		$this->errors = array();
		$this->breakWorksheet = false;
		$this->maxStepRows = 1000;
		$this->xmlRowDiff = 0;
		$this->stepparams = $stepparams;
		$this->stepparams['total_read_line'] = intval($this->stepparams['total_read_line']);
		$this->stepparams['total_line'] = intval($this->stepparams['total_line']);
		$this->stepparams['correct_line'] = intval($this->stepparams['correct_line']);
		$this->stepparams['error_line'] = intval($this->stepparams['error_line']);
		$this->stepparams['killed_line'] = intval($this->stepparams['killed_line']);
		$this->stepparams['offer_killed_line'] = intval($this->stepparams['offer_killed_line']);
		$this->stepparams['element_added_line'] = intval($this->stepparams['element_added_line']);
		$this->stepparams['element_updated_line'] = intval($this->stepparams['element_updated_line']);
		$this->stepparams['element_removed_line'] = intval($this->stepparams['element_removed_line']);
		$this->stepparams['sku_added_line'] = intval($this->stepparams['sku_added_line']);
		$this->stepparams['sku_updated_line'] = intval($this->stepparams['sku_updated_line']);
		$this->stepparams['section_added_line'] = intval($this->stepparams['section_added_line']);
		$this->stepparams['section_updated_line'] = intval($this->stepparams['section_updated_line']);
		$this->stepparams['zero_stock_line'] = intval($this->stepparams['zero_stock_line']);
		$this->stepparams['offer_zero_stock_line'] = intval($this->stepparams['offer_zero_stock_line']);
		$this->stepparams['old_removed_line'] = intval($this->stepparams['old_removed_line']);
		$this->stepparams['offer_old_removed_line'] = intval($this->stepparams['offer_old_removed_line']);
		$this->stepparams['xmlCurrentRow'] = intval($this->stepparams['xmlCurrentRow']);
		$this->stepparams['xmlSectionCurrentRow'] = intval($this->stepparams['xmlSectionCurrentRow']);
		$this->stepparams['total_file_line'] = 1;

		if(!$this->params['SECTION_UID']) $this->params['SECTION_UID'] = 'NAME';

		//$this->fileEncoding = \Bitrix\EsolImportxml\Utils::GetXmlEncoding($this->filename);
		$this->fileEncoding = 'utf-8';
		$this->siteEncoding = \Bitrix\EsolImportxml\Utils::getSiteEncoding();
		$this->xpathMulti = ($this->params['XPATHS_MULTI'] ? unserialize(base64_decode($this->params['XPATHS_MULTI'])) : array());
		if(!is_array($this->xpathMulti)) $this->xpathMulti = array();
		$this->xpathMulti = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($this->xpathMulti, $this->fileEncoding, $this->siteEncoding);
		
		$this->skuInElement = (bool)(isset($this->params['GROUPS']['OFFER']) && strpos($this->params['GROUPS']['OFFER'], $this->params['GROUPS']['ELEMENT'].'/')===0);
		if($this->skuInElement)
		{
			$arSkuFields = $arSkuAddFields = $arSkuDuplicateFields = array();
			foreach($this->params['FIELDS'] as $key=>$fieldFull)
			{
				list($xpath, $field) = explode(';', $fieldFull, 2);
				if(strpos($field, 'OFFER_')!==0) continue;
				if(preg_match('/^'.preg_quote($this->params['GROUPS']['OFFER'], '/').'(\/|$)/', $xpath)) $arSkuFields[$key] = $field;
				elseif(preg_match('/^'.preg_quote($this->params['GROUPS']['ELEMENT'], '/').'(\/|$)/', $xpath)) $arSkuAddFields[$key] = $field;
			}
			foreach($arSkuAddFields as $key=>$field)
			{
				if(($key2 = array_search($field, $arSkuFields))!==false)
				{
					$arSkuDuplicateFields[$key] = $key2;
					unset($arSkuAddFields[$key]);
				}
			}
			$this->arSkuAddFields = array_keys($arSkuAddFields);
			$this->arSkuDuplicateFields = $arSkuDuplicateFields;
		}		
		$this->subSectionInSection = (bool)(isset($this->params['GROUPS']['SUBSECTION']) && strpos($this->params['GROUPS']['SUBSECTION'], $this->params['GROUPS']['SECTION'].'/')===0);
		$this->sectionInElement = (bool)(isset($this->params['GROUPS']['SECTION']) && strpos($this->params['GROUPS']['SECTION'], $this->params['GROUPS']['ELEMENT'].'/')===0);
		$this->elementInSection = (bool)(isset($this->params['GROUPS']['ELEMENT']) && strpos($this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['SECTION'].'/')===0);
		if($this->elementInSection)
		{
			if(strpos($this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['SUBSECTION'].'/')===0)
			{
				$this->xpathElementInSection = trim(substr($this->params['GROUPS']['ELEMENT'], strlen($this->params['GROUPS']['SUBSECTION'])), '/');
			}
			else
			{
				$this->xpathElementInSection = trim(substr($this->params['GROUPS']['ELEMENT'], strlen($this->params['GROUPS']['SECTION'])), '/');
			}
		}
		$this->propertyInOffer = (bool)(isset($this->params['GROUPS']['OFFER']) && isset($this->params['GROUPS']['PROPERTY']) && strpos($this->params['GROUPS']['PROPERTY'], $this->params['GROUPS']['OFFER'].'/')===0);
		$this->propertyInElement = (bool)(!$this->propertyInOffer && isset($this->params['GROUPS']['PROPERTY']) && strpos($this->params['GROUPS']['PROPERTY'], $this->params['GROUPS']['ELEMENT'].'/')===0);
		$this->useSectionPathByLink = (bool)(!$this->sectionInElement && !$this->elementInSection && $this->params['GROUPS']['SECTION'] && count(preg_grep('/IE_SECTION_PATH/', $this->params['FIELDS'])) > 0 && count(preg_grep('/IE_IBLOCK_SECTION_TMP_ID/', $this->params['FIELDS'])) == 0 && count(preg_grep('/ISECT_NAME/', $this->params['FIELDS'])) > 0 && count(preg_grep('/ISECT_TMP_ID/', $this->params['FIELDS'])) > 0);
		
		if(is_array($this->params['OLD_FIELDS']))
		{
			foreach($this->params['OLD_FIELDS'] as $fieldKey)
			{
				unset($this->params['FIELDS'][$fieldKey]);
			}
		}
		if(is_array($this->params['OLD_GROUPS']))
		{
			foreach($this->params['OLD_GROUPS'] as $fieldKey)
			{
				unset($this->params['GROUPS'][$fieldKey]);
			}
		}
		
		if(strlen(trim($this->params['INACTIVE_FIELDS'])) > 0)
		{
			$arInactiveFields = array_map('trim', explode(';', $this->params['INACTIVE_FIELDS']));
			foreach($arInactiveFields as $fkey)
			{
				if(isset($this->params['FIELDS'][(int)$fkey])) unset($this->params['FIELDS'][(int)$fkey]);
			}
		}
		
		$saveStat = (bool)($params['STAT_SAVE']=='Y');
		$this->logger = new \Bitrix\EsolImportxml\Logger($saveStat, $pid);
		$this->fl = new \Bitrix\EsolImportxml\FieldList();
		$this->conv = new \Bitrix\EsolImportxml\Conversion($this);
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
		
		$this->saveProductWithOffers = (bool)(Loader::includeModule('catalog') && \Bitrix\Main\Config\Option::get('catalog', 'show_catalog_tab_with_offers') == 'Y');
		AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', array($this, 'OnBeforeIBlockElementUpdateHandler'), 999999);
		
		$cm = new \Bitrix\EsolImportxml\ClassManager($this);
		$this->pricer = $cm->GetPricer();
		$this->productor = $cm->GetProductor();
		
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
		$this->fileOffersId = $this->tmpdir.'offers_id.txt';
		$oProfile = \Bitrix\EsolImportxml\Profile::getInstance();
		$oProfile->SetImportParams($pid, $this->tmpdir, $stepparams);
		/*/Temp folders*/
		
		if(file_exists($this->tmpfile) && filesize($this->tmpfile) > 0)
		{
			$this->stepparams = array_merge($this->stepparams, unserialize(file_get_contents($this->tmpfile)));
		}
		
		if(!isset($this->stepparams['curstep'])) $this->stepparams['curstep'] = 'import_props';
		if(isset($this->stepparams['sectionIds']))
		{
			$this->sectionIds = $this->stepparams['sectionIds'];
			unset($this->stepparams['sectionIds']);
		}
		if(isset($this->stepparams['propertyIds']))
		{
			$this->propertyIds = $this->stepparams['propertyIds'];
			unset($this->stepparams['propertyIds']);
		}
		if(isset($this->stepparams['sectionsTmp']))
		{
			$this->sectionsTmp = $this->stepparams['sectionsTmp'];
			unset($this->stepparams['sectionsTmp']);
		}
		
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
		
		if($this->params['ONLY_UPDATE_MODE']=='Y')
		{
			$this->params['ONLY_UPDATE_MODE_ELEMENT'] = $this->params['ONLY_UPDATE_MODE_SECTION'] = 'Y';
		}
		if($this->params['ONLY_CREATE_MODE']=='Y')
		{
			$this->params['ONLY_CREATE_MODE_ELEMENT'] = $this->params['ONLY_CREATE_MODE_SECTION'] = 'Y';
		}
		
		if($pid!==false)
		{
			$this->procfile = $dir.$pid.'.txt';
			$this->errorfile = $dir.$pid.'_error.txt';
			if((int)$this->stepparams['import_started'] < 1)
			{
				$oProfile = \Bitrix\EsolImportxml\Profile::getInstance();
				$oProfile->OnStartImport();
				
				if(file_exists($this->procfile)) unlink($this->procfile);
				if(file_exists($this->errorfile)) unlink($this->errorfile);
			}
			$this->pid = $pid;
		}
	}
	
	public function OnBeforeIBlockElementUpdateHandler(&$arFields)
	{
		if(isset($arFields['PROPERTY_VALUES'])) unset($arFields['PROPERTY_VALUES']);
	}
	
	public function CheckTimeEnding($time = 0)
	{
		if($time==0) $time = $this->timeBeginImport;
		$this->ClearIblocksTagCache(true);
		return ($this->params['MAX_EXECUTION_TIME'] && (time()-$time >= $this->params['MAX_EXECUTION_TIME']));
	}
	
	public function Import()
	{
		register_shutdown_function(array($this, 'OnShutdown'));
		set_error_handler(array($this, "HandleError"));
		set_exception_handler(array($this, "HandleException"));
		$this->stepparams['import_started'] = 1;
		$this->SaveStatusImport();
		
		if(is_callable(array('\CIBlock', 'disableClearTagCache'))) \CIBlock::disableClearTagCache();
		$time = $this->timeBeginImport = $this->timeBeginTagCache = time();
		
		if($this->stepparams['curstep'] == 'import_props')
		{
			if($this->params['GROUPS']['IBPROPERTY'])
			{
				$this->InitImport('ibproperty');

				while($arItem = $this->GetNextIbPropRecord($time))
				{
					if(is_array($arItem)) $this->SaveIbPropRecord($arItem);
					if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}
				}
			}
			$this->stepparams['curstep'] = 'import_sections';
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		}
		
		if($this->stepparams['curstep'] == 'import_sections')
		{
			if($this->sectionInElement)
			{
				$this->stepparams['curstep'] = 'import';
			}
			else
			{
				if($this->params['GROUPS']['SECTION'])
				{
					if($this->params['GROUPS']['ELEMENT'] && (int)$this->xmlElementsCount==0)
					{
						$this->InitImport('element');
					}
					
					$this->InitImport('section');

					while($arItem = $this->GetNextSectionRecord($time))
					{
						$this->currentSectionXpath = rtrim($this->params['GROUPS']['SECTION'], '/');
						if(is_array($arItem)) $this->SaveSectionRecord($arItem);
						if($this->CheckTimeEnding($time))
						{
							if($this->elementInSection && $this->stepparams['xmlCurrentRowInSection'] > 0)
							{
								$this->xmlSectionCurrentRow--;
							}
							return $this->GetBreakParams();
						}
					}
				}
				$this->stepparams['curstep'] = 'import';
				if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
			}
		}
		
		if($this->stepparams['curstep'] == 'import' && !$this->elementInSection)
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
		$arElemDefaults = array();
		if($this->params['CELEMENT_MISSING_DEFAULTS'])
		{
			$arElemDefaults = unserialize(base64_decode($this->params['CELEMENT_MISSING_DEFAULTS']));
			if(!is_array($arElemDefaults)) $arElemDefaults = array();
		}
		$bSetDefaultProps = (bool)(count($arElemDefaults) > 0);

		$bElemDeactivate = (bool)($this->params['ELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params['ELEMENT_MISSING_TO_ZERO']=='Y' || $this->params['ELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params['CELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params['CELEMENT_MISSING_TO_ZERO']=='Y' || $this->params['CELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y' || $this->params['OFFER_MISSING_DEACTIVATE']=='Y' || $this->params['OFFER_MISSING_TO_ZERO']=='Y' || $this->params['OFFER_MISSING_REMOVE_PRICE']=='Y' || $this->params['OFFER_MISSING_REMOVE_ELEMENT']=='Y');
		
		if($bElemDeactivate || $bSetDefaultProps)
		{
			$bOnlySetDefaultProps = (bool)($bSetDefaultProps && !$bElemDeactivate);
			if($this->stepparams['curstep'] == 'import')
			{
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
				$this->stepparams['curstep'] = 'deactivate_elements';
				$this->stepparams['deactivate_element_last'] = \Bitrix\EsolImportxml\Utils::SortFileIds($this->fileElementsId);
				$this->stepparams['deactivate_offer_last'] = \Bitrix\EsolImportxml\Utils::SortFileIds($this->fileOffersId);
				$this->stepparams['deactivate_element_first'] = 0;
				$this->stepparams['deactivate_element_processed'] = 0;
				$this->stepparams['deactivate_offer_first'] = 0;
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time + 1000)) return $this->GetBreakParams();
			}
			
			$arFieldsList = array();
			$offersExists = false;			
			if(count(preg_grep('/;OFFER_/', $this->params['FIELDS'])) > 0)
			{
				$offersExists = true;
			}
			
			$arFieldsList = array(
				'IBLOCK_ID' => $this->params['IBLOCK_ID']
			);
			if($this->params['SECTION_ID'] && $this->params['MISSING_ACTIONS_IN_SECTION']!='N')
			{
				$arFieldsList['SECTION_ID'] = $this->params['SECTION_ID'];
				$arFieldsList['INCLUDE_SUBSECTIONS'] = 'Y';
			}
			if(is_array($this->fparams))
			{
				$propsDef = $this->GetIblockProperties($this->params['IBLOCK_ID']);
				foreach($this->fparams as $k2=>$ffilter)
				{
					if(isset($this->stepparams['fparams'][$k2]) && $ffilter['USE_FILTER_FOR_DEACTIVATE']=='Y')
					{
						$ffilter2 = $this->stepparams['fparams'][$k2];
						if(is_array($ffilter2['UPLOAD_VALUES']))
						{
							if(!is_array($ffilter['UPLOAD_VALUES'])) $ffilter['UPLOAD_VALUES'] = array();
							$ffilter['UPLOAD_VALUES'] = array_unique(array_merge($ffilter['UPLOAD_VALUES'], $ffilter2['UPLOAD_VALUES']));
						}
						if(is_array($ffilter2['NOT_UPLOAD_VALUES']))
						{
							if(!is_array($ffilter['NOT_UPLOAD_VALUES'])) $ffilter['NOT_UPLOAD_VALUES'] = array();
							$ffilter['NOT_UPLOAD_VALUES'] = array_unique(array_merge($ffilter['NOT_UPLOAD_VALUES'], $ffilter2['NOT_UPLOAD_VALUES']));
						}
					}
					if($ffilter['USE_FILTER_FOR_DEACTIVATE']=='Y' && (!empty($ffilter['UPLOAD_VALUES']) || !empty($ffilter['NOT_UPLOAD_VALUES'])))
					{
						$fieldName = '';
						$fieldFull = $this->params['FIELDS'][$k2];
						list($xpath, $field) = explode(';', $fieldFull, 2);
						
						if(strpos($field, 'IE_')===0)
						{
							$fieldName = substr($field, 3);
							if(strpos($fieldName, '|')!==false) $fieldName = current(explode('|', $fieldName));
						}
						elseif(strpos($field, 'IP_PROP')===0)
						{
							$propId = substr($field, 7);
							$fieldName = 'PROPERTY_'.$propId;
							if($propsDef[$propId]['PROPERTY_TYPE']=='L')
							{
								$fieldName .= '_VALUE';
							}
							elseif($propsDef[$propId]['PROPERTY_TYPE']=='S' && $propsDef[$propId]['USER_TYPE']=='directory')
							{
								if(is_array($ffilter['UPLOAD_VALUES']))
								{
									foreach($ffilter['UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['UPLOAD_VALUES'][$k3] = $this->GetHighloadBlockValue($propsDef[$propId], $v3);
									}
								}
								if(is_array($ffilter['NOT_UPLOAD_VALUES']))
								{
									foreach($ffilter['NOT_UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['NOT_UPLOAD_VALUES'][$k3] = $this->GetHighloadBlockValue($propsDef[$propId], $v3);
									}
								}
							}
							elseif($propsDef[$propId]['PROPERTY_TYPE']=='E')
							{
								if(is_array($ffilter['UPLOAD_VALUES']))
								{
									foreach($ffilter['UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['UPLOAD_VALUES'][$k3] = $this->GetIblockElementValue($propsDef[$propId], $v3, $ffilter);
									}
								}
								if(is_array($ffilter['NOT_UPLOAD_VALUES']))
								{
									foreach($ffilter['NOT_UPLOAD_VALUES'] as $k3=>$v3)
									{
										$ffilter['NOT_UPLOAD_VALUES'][$k3] = $this->GetIblockElementValue($propsDef[$propId], $v3, $ffilter);
									}
								}
							}
						}
						if(strlen($fieldName) > 0)
						{
							if(!empty($ffilter['UPLOAD_VALUES']))
							{
								//$arFieldsList[$fieldName] = $ffilter['UPLOAD_VALUES'];
								$keys = (isset($ffilter['UPLOAD_KEYS']) && is_array($ffilter['UPLOAD_KEYS']) ? $ffilter['UPLOAD_KEYS'] : array());
								foreach($ffilter['UPLOAD_VALUES'] as $ukey=>$uval)
								{
									$key = (isset($keys[$ukey]) ? $keys[$ukey] : '');
									$op = '';
									$this->GetUVFilterParams($uval, $op, $key);
									$arSubFilter[] = array($op.$fieldName => $uval);
								}
								if(count($arSubFilter) > 1) $arFieldsList[] = array_merge(array('LOGIC'=>'OR'), $arSubFilter);
								else $arFieldsList = array_merge($arFieldsList, current($arSubFilter));
							}
							elseif(!empty($ffilter['NOT_UPLOAD_VALUES']))
							{
								//$arFieldsList['!'.$fieldName] = $ffilter['NOT_UPLOAD_VALUES'];
								$keys = (isset($ffilter['NOT_UPLOAD_KEYS']) && is_array($ffilter['NOT_UPLOAD_KEYS']) ? $ffilter['NOT_UPLOAD_KEYS'] : array());
								foreach($ffilter['NOT_UPLOAD_VALUES'] as $ukey=>$uval)
								{
									$key = (isset($keys[$ukey]) ? $keys[$ukey] : '');
									$op = '!';
									$this->GetUVFilterParams($uval, $op, $key);
									$arSubFilter[] = array($op.$fieldName => $uval);
								}
								if(count($arSubFilter) > 1) $arFieldsList[] = array_merge(array('LOGIC'=>'AND'), $arSubFilter);
								else $arFieldsList = array_merge($arFieldsList, current($arSubFilter));
							}
						}
					}
				}
				\Bitrix\EsolImportxml\Utils::AddFilter($arFieldsList, $this->params['CELEMENT_MISSING_FILTER']);
			}
		
			while($this->stepparams['deactivate_element_first'] < $this->stepparams['deactivate_element_last'])
			{
				$arUpdatedIds = \Bitrix\EsolImportxml\Utils::GetPartIdsFromFile($this->fileElementsId, $this->stepparams['deactivate_element_first']);
				if(empty($arUpdatedIds))
				{
					$this->stepparams['deactivate_element_first'] = $this->stepparams['deactivate_element_last'];
					continue;
				}
				$lastElement = end($arUpdatedIds);
				
				$arFields = $arFieldsList;
				$arFields["CHECK_PERMISSIONS"] = "N";
				if($this->stepparams['begin_time'])
				{
					$arFields['<TIMESTAMP_X'] = $this->stepparams['begin_time'];
				}
				
				$arSubFields = $this->GetMissingFilter(false, $arFields['IBLOCK_ID'], $arUpdatedIds);
				
				if($offersExists && ($arOfferIblock = $this->GetCachedOfferIblock($arFields['IBLOCK_ID'])))
				{
					$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
					$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
					$arOfferFields = array("IBLOCK_ID" => $OFFERS_IBLOCK_ID);
					$arSubOfferFields = $this->GetMissingFilter(true, $OFFERS_IBLOCK_ID);
					if(!empty($arSubOfferFields))
					{
						if(count($arSubOfferFields) > 1) $arOfferFields[] = array_merge(array('LOGIC' => 'OR'), $arSubOfferFields);
						else $arOfferFields = array_merge($arOfferFields, $arSubOfferFields);
						$arSubFields['ID'] = \CIBlockElement::SubQuery('PROPERTY_'.$OFFERS_PROPERTY_ID, $arOfferFields);
					}
				}
				
				if(count($arSubFields) > 1) $arFields[] = array_merge(array('LOGIC' => 'OR'), $arSubFields);
				else $arFields = array_merge($arFields, $arSubFields);
				
				$arFields['!ID'] = $arUpdatedIds;
				if($this->stepparams['deactivate_element_first'] > 0) $arFields['>ID'] = $this->stepparams['deactivate_element_first'];
				if($lastElement < $this->stepparams['deactivate_element_last']) $arFields['<=ID'] = $lastElement;
				$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFields, false, false, array('ID'));
				while($arr = $dbRes->Fetch())
				{
					if($arr['ID'] <= $this->stepparams['deactivate_element_processed']) continue;
					if($this->params['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y')
					{
						if($offersExists)
						{
							$this->DeactivateAllOffersByProductId($arr['ID'], $arFields['IBLOCK_ID'], $time, true);
						}
						\CIblockElement::Delete($arr['ID']);
						$this->AddTagIblock($arFields['IBLOCK_ID']);
						$this->stepparams['old_removed_line']++;
					}
					else
					{
						$this->MissingElementsUpdate($arr['ID'], $arFields['IBLOCK_ID'], false);

						if($offersExists)
						{
							$this->DeactivateAllOffersByProductId($arr['ID'], $arFields['IBLOCK_ID'], $time);
						}
					}
					
					$this->stepparams['deactivate_element_processed'] = $arr['ID'];
					$this->SaveStatusImport();
					if($this->CheckTimeEnding($time))
					{
						return $this->GetBreakParams();
					}
				}
				if($offersExists)
				{
					$ret = $this->DeactivateOffersByProductIds($arUpdatedIds, $arFields['IBLOCK_ID'], $time);
					if(is_array($ret)) return $ret;
				}

				$this->stepparams['deactivate_element_first'] = $lastElement;
			}
			$this->SaveStatusImport();
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		}
		
		if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
		if(($this->params['SECTION_EMPTY_DEACTIVATE']=='Y' || $this->params['SECTION_NOTEMPTY_ACTIVATE']=='Y') && class_exists('\Bitrix\Iblock\SectionElementTable'))
		{
			$this->stepparams['curstep'] = 'deactivate_sections';

			$sectionId = (int)$this->params['SECTION_ID'];
			$arFilterSections  = array('IBLOCK_ID' => $this->params['IBLOCK_ID'], 'CHECK_PERMISSIONS' => 'N');
			$arFilterSE = array('IBLOCK_SECTION.IBLOCK_ID' => $this->params['IBLOCK_ID'], 'IBLOCK_ELEMENT.ACTIVE' => 'Y');
			
			if($sectionId)
			{
				$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$sectionId, 'CHECK_PERMISSIONS' => 'N'), false, array('LEFT_MARGIN', 'RIGHT_MARGIN'));
				if($arr = $dbRes->Fetch())
				{
					$arFilterSections['>=LEFT_MARGIN'] = $arr['LEFT_MARGIN'];
					$arFilterSections['<=RIGHT_MARGIN'] = $arr['RIGHT_MARGIN'];
					$arFilterSE['>=IBLOCK_SECTION.LEFT_MARGIN'] = $arr['LEFT_MARGIN'];
					$arFilterSE['<=IBLOCK_SECTION.RIGHT_MARGIN'] = $arr['RIGHT_MARGIN'];
				}
			}
			
			$arListSections = array();
			$dbRes = \CIBlockSection::GetList(array('DEPTH_LEVEL'=>'DESC'), $arFilterSections, false, array('ID', 'IBLOCK_SECTION_ID'));
			while($arr = $dbRes->Fetch())
			{
				$arListSections[$arr['ID']] = ($sectionId==$arr['ID'] ? false : $arr['IBLOCK_SECTION_ID']);
			}
			
			$arActiveSections = array();
			$dbRes = \Bitrix\Iblock\SectionElementTable::GetList(array('filter'=>$arFilterSE, 'group'=>array('IBLOCK_SECTION_ID'), 'select'=>array('IBLOCK_SECTION_ID')));
			while($arr = $dbRes->Fetch())
			{
				$sid = $arr['IBLOCK_SECTION_ID'];
				$arActiveSections[] = $sid;
				while($sid = $arListSections[$sid])
				{
					$arActiveSections[] = $sid;
				}
			}
			
			$sect = new \CIBlockSection();
			if($this->params['SECTION_NOTEMPTY_ACTIVATE']=='Y')
			{
				if(!empty($arActiveSections))
				{
					$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$arActiveSections, 'ACTIVE'=>'N', 'CHECK_PERMISSIONS' => 'N'), false, array('ID'));
					while($arr = $dbRes->Fetch())
					{
						$sect->Update($arr['ID'], array('ACTIVE'=>'Y'));
						$this->AddTagIblock($arFilterSections['IBLOCK_ID']);
						$this->SaveStatusImport();
						if($this->CheckTimeEnding($time)) return $this->GetBreakParams();						
					}
				}
			}
			
			if($this->params['SECTION_EMPTY_DEACTIVATE']=='Y')
			{
				$arInactiveSections = array_diff(array_keys($arListSections), $arActiveSections);
				if(!empty($arInactiveSections))
				{
					$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$arInactiveSections, 'ACTIVE'=>'Y', 'CHECK_PERMISSIONS' => 'N'), false, array('ID'));
					while($arr = $dbRes->Fetch())
					{
						$sect->Update($arr['ID'], array('ACTIVE'=>'N'));
						$this->AddTagIblock($arFilterSections['IBLOCK_ID']);
						$this->SaveStatusImport();
						if($this->CheckTimeEnding($time)) return $this->GetBreakParams();						
					}
				}
			}
		}
		
		if(is_callable(array('CIBlock', 'clearIblockTagCache')))
		{
			if(is_callable(array('\CIBlock', 'enableClearTagCache'))) \CIBlock::enableClearTagCache();
			$bEventRes = true;
			foreach(GetModuleEvents(static::$moduleId, "OnBeforeClearCache", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array($this->params['IBLOCK_ID']))===false)
				{
					$bEventRes = false;
				}
			}
			if($bEventRes)
			{
				\CIBlock::clearIblockTagCache($this->params['IBLOCK_ID']);
			}
			if(is_callable(array('\CIBlock', 'disableClearTagCache'))) \CIBlock::disableClearTagCache();
		}
		
		if($this->params['REMOVE_COMPOSITE_CACHE']=='Y' && class_exists('\Bitrix\Main\Composite\Helper'))
		{
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/cache_files_cleaner.php");
			$obCacheCleaner = new \CFileCacheCleaner('html');
			if($obCacheCleaner->InitPath(''))
			{
				$obCacheCleaner->Start();
				$space_freed = 0;
				while($file = $obCacheCleaner->GetNextFile())
				{
					if(
						is_string($file)
						&& !preg_match("/(\\.enabled|\\.size|.config\\.php)\$/", $file)
					)
					{
						$file_size = filesize($file);

						if(@unlink($file))
						{
							$space_freed+=$file_size;
						}
					}
					if($this->CheckTimeEnding($time))
					{
						\Bitrix\Main\Composite\Helper::updateCacheFileSize(-$space_freed);
						return $this->GetBreakParams();
					}
				}
				\Bitrix\Main\Composite\Helper::updateCacheFileSize(-$space_freed);
			}
			$page = \Bitrix\Main\Composite\Page::getInstance();
			$page->deleteAll();
		}
		
		$this->SaveStatusImport(true);
		
		$oProfile = \Bitrix\EsolImportxml\Profile::getInstance();
		$arEventData = $oProfile->OnEndImport($this->filename, $this->stepparams);
		
		foreach(GetModuleEvents(static::$moduleId, "OnEndImport", true) as $arEvent)
		{
			$arEventData = array('IBLOCK_ID' => $this->params['IBLOCK_ID']);
			foreach($this->stepparams as $k=>$v)
			{
				if(!is_array($v)) $arEventData[ToUpper($k)] = $v;
			}
			$oProfile = new \Bitrix\EsolImportxml\Profile();
			$arProfile = $oProfile->GetFieldsByID($this->pid);
			$arEventData['PROFILE_NAME'] = $arProfile['NAME'];
			$arEventData['IMPORT_START_DATETIME'] = (is_callable(array($arProfile['DATE_START'], 'toString')) ? $arProfile['DATE_START']->toString() : '');
			$arEventData['IMPORT_FINISH_DATETIME'] = ConvertTimeStamp(false, 'FULL');
			
			$bEventRes = ExecuteModuleEventEx($arEvent, array($this->pid, $arEventData));
		}
		
		return $this->GetBreakParams('finish');
	}
	
	public function GetUVFilterParams(&$val, &$op, $key)
	{
		if($val=='{empty}'){$val = false;}
		elseif($val=='{not_empty}'){$op .= '!'; $val = false;}
		elseif(!$key){$op .= '=';}
		elseif($key=='contain'){$op .= '%';}
		elseif($key=='begin'){$val = $val.'%';}
		elseif($key=='end'){$val = '%'.$val;}
		elseif($key=='gt'){$op .= '>';}
		elseif($key=='lt'){$op .= '<';}
		
		if($op=='!!') $op = '';
		elseif($op=='!>') $op = '<';
		elseif($op=='!<') $op = '>';
	}
	
	public function DeactivateAllOffersByProductId($ID, $IBLOCK_ID, $time, $deleteMode = false)
	{
		if(!($arOfferIblock = $this->GetCachedOfferIblock($IBLOCK_ID))) return false;
		$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
		$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
		
		$arFields = array(
			'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
			'PROPERTY_'.$OFFERS_PROPERTY_ID => $ID,
			'CHECK_PERMISSIONS' => 'N'
		);
		
		$arSubFields = $this->GetMissingFilter(true, $OFFERS_IBLOCK_ID);
		
		if(!empty($arSubFields))
		{
			if(count($arSubFields) > 1) $arFields[] = array_merge(array('LOGIC' => 'OR'), $arSubFields);
			else $arFields = array_merge($arFields, $arSubFields);
						
			$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFields, false, false, array('ID'));
			while($arr = $dbRes->Fetch())
			{
				if($deleteMode)
				{
					\CIblockElement::Delete($arr['ID']);
					$this->AddTagIblock($OFFERS_IBLOCK_ID);
					$this->stepparams['offer_old_removed_line']++;
				}
				else
				{
					$this->MissingElementsUpdate($arr['ID'], $OFFERS_IBLOCK_ID, true);
				}
				if($this->CheckTimeEnding($time))
				{
					return $this->GetBreakParams();
				}
			}
		}
	}
	
	public function DeactivateOffersByProductIds(&$arElementIds, $IBLOCK_ID, $time)
	{
		if(!($arOfferIblock = $this->GetCachedOfferIblock($IBLOCK_ID))) return false;
		$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
		$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
		
		while($this->stepparams['deactivate_offer_first'] < $this->stepparams['deactivate_offer_last'])
		{
			$arUpdatedIds = \Bitrix\EsolImportxml\Utils::GetPartIdsFromFile($this->fileOffersId, $this->stepparams['deactivate_offer_first']);
			if(empty($arUpdatedIds))
			{
				$this->stepparams['deactivate_offer_first'] = $this->stepparams['deactivate_offer_last'];
				continue;
			}
			$lastElement = end($arUpdatedIds);

			$arFields = array(
				'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
				'PROPERTY_'.$OFFERS_PROPERTY_ID => $arElementIds,
				'!ID' => $arUpdatedIds,
				'CHECK_PERMISSIONS' => 'N'
			);
			
			$arSubFields = $this->GetMissingFilter(true, $OFFERS_IBLOCK_ID);
			if(!empty($arSubFields))
			{
				if(count($arSubFields) > 1) $arFields[] = array_merge(array('LOGIC' => 'OR'), $arSubFields);
				else $arFields = array_merge($arFields, $arSubFields);
			}
			
			if($this->stepparams['begin_time'])
			{
				$arFields['<TIMESTAMP_X'] = $this->stepparams['begin_time'];
			}
			if($this->stepparams['deactivate_offer_first'] > 0) $arFields['>ID'] = $this->stepparams['deactivate_offer_first'];
			if($lastElement < $this->stepparams['deactivate_offer_last']) $arFields['<=ID'] = $lastElement;
			$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFields, false, false, array('ID'));
			while($arr = $dbRes->Fetch())
			{
				if($this->params['OFFER_MISSING_REMOVE_ELEMENT']=='Y')
				{
					\CIblockElement::Delete($arr['ID']);
					$this->AddTagIblock($OFFERS_IBLOCK_ID);
					$this->stepparams['offer_old_removed_line']++;
				}
				else
				{
					$this->MissingElementsUpdate($arr['ID'], $OFFERS_IBLOCK_ID, true);
				}
				$this->SaveStatusImport();
				if($this->CheckTimeEnding($time))
				{
					return $this->GetBreakParams();
				}
			}
			if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
			$this->stepparams['deactivate_offer_first'] = $lastElement;
		}
		$this->stepparams['deactivate_offer_first'] = 0;
	}
	
	public function MissingElementsUpdate($ID, $IBLOCK_ID, $isOffer = false)
	{
		if(!$ID) return;
		if($isOffer) $this->SetSkuMode(true, $ID, $IBLOCK_ID);
		$prefix = ($isOffer ? 'OFFER' : 'CELEMENT');
		$updateElement = false;
		if($this->params['ELEMENT_MISSING_TO_ZERO']=='Y' || $this->params[$prefix.'_MISSING_TO_ZERO']=='Y')
		{
			//\CCatalogProduct::Update($ID, array('QUANTITY'=>0));
			$this->productor->Update($ID, $IBLOCK_ID, array('QUANTITY'=>0));
			$dbRes2 = \CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID'=>$ID, '>AMOUNT'=>0), false, false, array('ID'));
			while($arStore = $dbRes2->Fetch())
			{
				\CCatalogStoreProduct::Update($arStore["ID"], array('AMOUNT'=>0));
			}
			if($isOffer) $this->stepparams['offer_zero_stock_line']++;
			else $this->stepparams['zero_stock_line']++;
		}
		if($this->params['ELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params[$prefix.'_MISSING_REMOVE_PRICE']=='Y')
		{
			$dbRes = $this->pricer->GetList(array(), array('PRODUCT_ID'=>$ID), false, false, $arKeys);
			while($arPrice = $dbRes->Fetch())
			{
				$this->pricer->Delete($arPrice["ID"]);
			}
		}
		
		$arDefaults = array();
		if($this->params[$prefix.'_MISSING_DEFAULTS'])
		{
			$arDefaults = unserialize(base64_decode($this->params[$prefix.'_MISSING_DEFAULTS']));
			if(!is_array($arDefaults)) $arDefaults = array();
		}
		if(!empty($arDefaults))
		{
			$arElemVals = array();
			$arProps = array();
			$arStores = array();
			$arPrices = array();
			$arProduct = array();
			foreach($arDefaults as $propKey=>$propVal)
			{
				if(strpos($propKey, 'IE_')===0)
				{
					$arElemVals[substr($propKey, 3)] = $propVal;
				}
				elseif(preg_match('/ICAT_STORE(\d+)_AMOUNT/', $propKey, $m))
				{
					$arStores[$m[1]] = array('AMOUNT' => $propVal);
				}
				elseif(preg_match('/ICAT_PRICE(\d+)_PRICE/', $propKey, $m))
				{
					$arPrices[$m[1]] = array('PRICE' => $propVal);
				}
				elseif(strpos($propKey, 'ICAT_')===0)
				{
					$arProduct[substr($propKey, 5)] = $propVal;
				}
				else
				{
					$arProps[$propKey] = $propVal;
				}
			}
			if(!empty($arProduct) || !empty($arPrices) || !empty($arStores))
			{
				$this->SaveProduct($ID, $IBLOCK_ID, $arProduct, $arPrices, $arStores);
			}
			if(!empty($arProps))
			{
				$this->SaveProperties($ID, $IBLOCK_ID, $arProps);
			}
			if(!empty($arElemVals))
			{
				$el = new \CIblockElement();
				$el->Update($ID, $arElemVals);
				$updateElement = true;
				$this->AddTagIblock($IBLOCK_ID);
			}
		}
		
		$el = new \CIblockElement();
		if($this->params['ELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params[$prefix.'_MISSING_DEACTIVATE']=='Y')
		{
			$el->Update($ID, array('ACTIVE'=>'N'));
			$updateElement = true;
			$this->AddTagIblock($IBLOCK_ID);
			if($isOffer) $this->stepparams['offer_killed_line']++;
			else $this->stepparams['killed_line']++;
		}
		
		if(!$updateElement && $this->params['ELEMENT_NOT_UPDATE_WO_CHANGES']!='Y')
		{
			$el->Update($ID, array('ID'=>$ID));
		}
		if($isOffer) $this->SetSkuMode(false);
	}
	
	public function GetMissingFilter($isOffer = false, $IBLOCK_ID = 0, $arUpdatedIds=array())
	{
		$arSubFields = array();
		$prefix = ($isOffer ? 'OFFER' : 'CELEMENT');
		if($this->params[$prefix.'_MISSING_REMOVE_ELEMENT']=='Y') return $arSubFields;
		if($this->params['ELEMENT_MISSING_DEACTIVATE']=='Y' || $this->params[$prefix.'_MISSING_DEACTIVATE']=='Y') $arSubFields['ACTIVE'] = 'Y';
		if($this->params['ELEMENT_MISSING_TO_ZERO']=='Y' || $this->params[$prefix.'_MISSING_TO_ZERO']=='Y') $arSubFields['>CATALOG_QUANTITY'] = 0;
		if($this->params['ELEMENT_MISSING_REMOVE_PRICE']=='Y' || $this->params[$prefix.'_MISSING_REMOVE_PRICE']=='Y') $arSubFields['!CATALOG_PRICE_'.$this->pricer->GetBasePriceId()] = false;
		
		$arDefaults = array();
		if($this->params[$prefix.'_MISSING_DEFAULTS'])
		{
			$arDefaults = unserialize(base64_decode($this->params[$prefix.'_MISSING_DEFAULTS']));
			if(!is_array($arDefaults)) $arDefaults = array();
		}
		if($IBLOCK_ID > 0 && !empty($arDefaults))
		{
			$arProductFields = array();
			$propsDef = $this->GetIblockProperties($IBLOCK_ID);
			foreach($arDefaults as $uid=>$valUid)
			{				
				if(strpos($uid, 'IE_')===0)
				{
					$uid = substr($uid, 3);
				}
				elseif(preg_match('/ICAT_STORE(\d+)_AMOUNT/', $uid, $m))
				{
					$uid = 'CATALOG_STORE_AMOUNT_'.$m[1];
				}
				elseif(preg_match('/ICAT_PRICE(\d+)_PRICE/', $uid, $m))
				{
					$uid = 'CATALOG_PRICE_'.$m[1];
					if($valUid=='-') $valUid = false;
				}
				elseif(strpos($uid, 'ICAT_')===0)
				{
					$field = substr($uid, 5);
					if(in_array($field, array('QUANTITY_TRACE', 'CAN_BUY_ZERO', 'NEGATIVE_AMOUNT_TRACE', 'SUBSCRIBE')) && class_exists('\Bitrix\Catalog\ProductTable'))
					{
						if($field=='NEGATIVE_AMOUNT_TRACE') $configName = 'allow_negative_amount';
						else $configName = 'default_'.ToLower($field);
						if($field=='SUBSCRIBE') $defaultVal = ((string)\Bitrix\Main\Config\Option::get('catalog', $configName) == 'N' ? 'N' : 'Y');
						else $defaultVal = ((string)\Bitrix\Main\Config\Option::get('catalog', $configName) == 'Y' ? 'Y' : 'N');
						$valUid = trim(ToUpper($valUid));
						if($valUid!='D') $valUid = $this->GetBoolValue($valUid);
						if($valUid==$defaultVal) $arProductFields['!'.$field] = array($valUid, 'D');
						else $arProductFields['!'.$field] = $valUid;
					}
					continue;
				}
				elseif($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					if(strlen($valUid)==0) $valUid = false;
					$uid = 'PROPERTY_'.$uid.'_VALUE';
				}
				else
				{
					if($propsDef[$uid]['PROPERTY_TYPE']=='S' && $propsDef[$uid]['USER_TYPE']=='directory')
					{
						$valUid = $this->GetHighloadBlockValue($propsDef[$uid], $valUid);
					}
					elseif($propsDef[$uid]['PROPERTY_TYPE']=='E')
					{
						$valUid = $this->GetIblockElementValue($propsDef[$uid], $valUid, array());
					}
					if(strlen($valUid)==0) $valUid = false;
					$uid = 'PROPERTY_'.$uid;
				}
				$arSubFields['!'.$uid] = $valUid;
			}
			
			if(!empty($arProductFields) && !empty($arUpdatedIds) && $IBLOCK_ID > 0)
			{
				if(count($arProductFields) > 1)
				{
					$arProductFields = array(array_merge(array('LOGIC'=>'OR'), array_map(create_function('$k,$v', 'return array($k=>$v);'), array_keys($arProductFields), $arProductFields)));
				}
				$arProductFields['IBLOCK_ELEMENT.IBLOCK_ID'] = $IBLOCK_ID;
				$arProductFields['!ID'] = $arUpdatedIds;
				$lastElement = end($arUpdatedIds);
				if($this->stepparams['deactivate_element_first'] > 0) $arProductFields['>ID'] = $this->stepparams['deactivate_element_first'];
				if($lastElement < $this->stepparams['deactivate_element_last']) $arProductFields['<=ID'] = $lastElement;
				$dbRes = \Bitrix\Catalog\ProductTable::getList(array(
					'order' => array('ID'=>'ASC'),
					'select' => array('ID'),
					'filter' => $arProductFields
				));
				$arIds = array();
				while($arr = $dbRes->Fetch())
				{
					$arIds[] = $arr['ID'];
				}
				if(!empty($arIds))
				{
					$arSubFields['ID'] = $arIds;
				}
				elseif(empty($arSubFields)) $arSubFields['ID'] = 0;
			}
		}
		
		if(!$isOffer && !$this->saveProductWithOffers && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
		{
			foreach($arSubFields as $k=>$v)
			{
				if(preg_match('/^.?CATALOG_/', $k))
				{
					$arSubFields[] = array('LOGIC' => 'AND', array($k => $v), array('!CATALOG_TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU));
					unset($arSubFields[$k]);
				}
			}
		}

		return $arSubFields;
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
				$arFieldsDef = $this->fl->GetFields($this->params['IBLOCK_ID']);
				$emptyFieldNames = array();
				foreach($emptyFields as $field)
				{
					if(strpos($field, 'IE_')===0)
					{
						$emptyFieldNames[] = $arFieldsDef['element']['items'][$field];
					}
					elseif(strpos($field, 'IP_PROP')===0)
					{
						$emptyFieldNames[] = $arFieldsDef['prop']['items'][$field];
					}
				}
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_UID"), implode(', ', $emptyFieldNames));
				return false;
			}
		}
		
		if($type == 'section' && $this->params['GROUPS']['SECTION'])
		{
			$emptyFields = array();
			$sectionUid = $this->params['SECTION_UID'];
			if(!is_array($sectionUid)) $sectionUid = array($sectionUid);
			foreach($sectionUid as $uidField)
			{
				$uidField = 'ISECT_'.$uidField;
				if(!is_array($this->params['FIELDS']) || count(preg_grep('/;'.$uidField.'$/', $this->params['FIELDS']))==0)
				{
					$emptyFields[] = $uidField;
				}
			}
			if(!empty($emptyFields))
			{
				$arFieldsDef = $this->fl->GetIblockSectionFields('');
				$emptyFieldNames = array();
				foreach($emptyFields as $field)
				{
					$emptyFieldNames[] = $arFieldsDef[$field]['name'];
				}
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_SECTION_UID"), implode(', ', $emptyFieldNames));
				return false;
			}
		}
		
		if($type == 'ibproperty' && $this->params['GROUPS']['IBPROPERTY'])
		{
			$emptyFields = array();
			$propUid = array('IBPROP_NAME', 'IBPROP_CODE');
			foreach($propUid as $uidField)
			{
				if(!is_array($this->params['FIELDS']) || count(preg_grep('/;'.$uidField.'$/', $this->params['FIELDS']))==0)
				{
					$emptyFields[] = $uidField;
				}
			}

			if(count($emptyFields) >= count($propUid))
			{
				$arFieldsDef = $this->fl->GetIbPropertyFields();
				$emptyFieldNames = array();
				foreach($emptyFields as $field)
				{
					$emptyFieldNames[] = $arFieldsDef[$field];
				}
				//$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_SECTION_UID"), implode(', ', $emptyFieldNames));
				return false;
			}
		}
		
		$this->fieldOnlyNew = array();
		$this->fieldOnlyNewOffer = array();
		$this->fieldsForSkuGen = array();
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
				if(strpos($field, 'OFFER_')===0)
				{
					$this->fieldOnlyNewOffer[] = substr($field, 6);
					if(strlen($field2) > 0) $this->fieldOnlyNewOffer[] = $field2;
				}
				else
				{
					$this->fieldOnlyNew[] = $field;
					if(strlen($field2) > 0) $this->fieldOnlyNew[] = $field2;
				}
			}
			
			if(strpos($field, 'OFFER_')===0 && $this->fparams[$k]['USE_FOR_SKU_GENERATE']=='Y')
			{
				$this->fieldsForSkuGen[] = $k;
			}
		}
		$this->conv = new \Bitrix\EsolImportxml\Conversion($this, $this->params['IBLOCK_ID'], $this->fieldSettings);
		
		//$this->xmlObject = simplexml_load_file($this->filename);
		
		$this->InitXml($type);
		
		return true;
	}
	
	public function InitXml($type)
	{
		if($type == 'element')
		{
			if(!isset($this->xmlCurrentRow)) $this->xmlCurrentRow = intval($this->stepparams['xmlCurrentRow']);
			//$this->CheckGroupParams('ELEMENT', 'yml_catalog/shop/offers', 'yml_catalog/shop/offers/offer');
			//$this->CheckGroupParams('ELEMENT', 'yml_catalog/offers', 'yml_catalog/offers/offer');
			if(preg_match('/\/offers$/', $this->params['GROUPS']['ELEMENT'])) $this->CheckGroupParams('ELEMENT', $this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['ELEMENT'].'/offer');
			if(preg_match('/\/'.Loc::getMessage("ESOL_IX_PRODUCTS_TAG_1C").'$/', $this->params['GROUPS']['ELEMENT'])) $this->CheckGroupParams('ELEMENT', $this->params['GROUPS']['ELEMENT'], $this->params['GROUPS']['ELEMENT'].'/'.Loc::getMessage("ESOL_IX_PRODUCT_TAG_1C"));
			
			$count = 0;
			$this->xmlElements = $this->GetXmlObject($count, $this->xmlCurrentRow, $this->params['GROUPS']['ELEMENT']);
			$this->xmlElementsCount = $this->stepparams['total_file_line'] = $count;
		}
		
		if($type == 'section')
		{
			if(!isset($this->xmlSectionCurrentRow)) $this->xmlSectionCurrentRow = intval($this->stepparams['xmlSectionCurrentRow']);
			//$this->CheckGroupParams('SECTION', 'yml_catalog/shop/categories', 'yml_catalog/shop/categories/category');
			//$this->CheckGroupParams('SECTION', 'yml_catalog/categories', 'yml_catalog/categories/category');
			if(preg_match('/\/categories$/', $this->params['GROUPS']['SECTION'])) $this->CheckGroupParams('SECTION', $this->params['GROUPS']['SECTION'], $this->params['GROUPS']['SECTION'].'/category');
			if(preg_match('/\/'.Loc::getMessage("ESOL_IX_SECTIONS_TAG_1C").'$/', $this->params['GROUPS']['SECTION'])) $this->CheckGroupParams('SECTION', $this->params['GROUPS']['SECTION'], $this->params['GROUPS']['SECTION'].'/'.Loc::getMessage("ESOL_IX_SECTION_TAG_1C"));
			
			$count = 0;
			$this->xmlSections = $this->GetXmlObject($count, 0, $this->params['GROUPS']['SECTION'], true);
			$this->xmlSectionsCount = $count;
		}
		
		if($type == 'ibproperty')
		{
			if(!isset($this->xmlIbPropCurrentRow)) $this->xmlIbPropCurrentRow = intval($this->stepparams['xmlIbPropCurrentRow']);			
			$count = 0;
			$this->xmlIbProps = $this->GetXmlObject($count, 0, $this->params['GROUPS']['IBPROPERTY'], true);
			$this->xmlIbPropsCount = $count;
		}
		return true;
	}
	
	public function CheckGroupParams($type, $xpathFrom, $xpathTo)
	{
		if(trim($this->params['GROUPS'][$type], '/')==$xpathFrom)
		{
			$xmlSectionCurrentRow = $this->xmlSectionCurrentRow;
			$xmlCurrentRow = $this->xmlCurrentRow;
			$maxStepRows = $this->maxStepRows;
			$this->maxStepRows = 2;
			$xmlElements = $this->GetXmlObject(($count=0), 0, $xpathTo);
			if(is_array($xmlElements) && count($xmlElements) > 0)
			{
				$this->params['GROUPS'][$type] = $xpathTo;
			}
			$this->xmlSectionCurrentRow = $xmlSectionCurrentRow;
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
		$arXpath = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($arXpath, $this->siteEncoding, $this->fileEncoding);
		$cachedCountRowsKey = $xpath;
		$cachedCountRows = 0;
		if(isset($this->stepparams['count_rows'][$cachedCountRowsKey]))
		{
			$cachedCountRows = (int)$this->stepparams['count_rows'][$cachedCountRowsKey];
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
		$rootNS = '';
		while(($isRead || $xml->read()) && !$break) 
		{
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				$curDepth = $xml->depth;
				$arObjectNames[$curDepth] = $curName = (strlen($rootNS) > 0 && strpos($xml->name, ':')===false ? $rootNS.':' : '').$xml->name;
				$extraDepth = $curDepth + 1;
				while(isset($arObjectNames[$extraDepth]))
				{
					unset($arObjectNames[$extraDepth]);
					$extraDepth++;
				}
				
				$curXPath = implode('/', $arObjectNames);
				$curXPath = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($curXPath, $this->fileEncoding, $this->siteEncoding);
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
					if(strpos($xpath.'/', $curXPath.'/')!==0 && strpos($curXPath.'/', $xpath.'/')!==0)
					{
						$isRead = false;
						$nextTag = $arXpath[$curDepth];
						if(($pos = strpos($nextTag, ':'))!==false) $nextTag = substr($nextTag, $pos+1);
						while(!$isRead && $xml->next($nextTag)) $isRead = true;
						continue;
					}
					if($xpath==$curXPath)
					{
						$countRows++;
						$nextTag = $curName;
						if(($pos = strpos($nextTag, ':'))!==false) $nextTag = substr($nextTag, $pos+1);
						while($countRows < $beginRow && $xml->next($nextTag)) $countRows++;
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
								$nextTag = $curName;
								if(($pos = strpos($nextTag, ':'))!==false) $nextTag = substr($nextTag, $pos+1);
								while($xml->next($nextTag)) $countRows++;
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
					//$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
					if(($pos = strpos($curName, ':'))!==false)
					{
						$rootNS = substr($curName, 0, $pos);
						$curName = substr($curName, strlen($rootNS) + 1);
					}
					$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>', 0, false, $rootNS, true);
					$arObjects[$curDepth] = &$xmlObj;
					if(($pos = strpos($curName, ':'))!==false) $rootNS = substr($curName, 0, $pos);
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
		else $this->stepparams['count_rows'][$cachedCountRowsKey] = $countRows;
			
		if(is_object($xmlObj))
		{
			$this->xmlRowDiff = $beginRow;
			$this->xmlObject = $xmlObj;
			//return $this->xmlObject->xpath('/'.$xpath);
			return $this->Xpath($this->xmlObject, '/'.$xpath);
		}
		return false;
	}
	
	public function GetPartXmlObject($xpath, $wChild=true)
	{
		$xpath = trim(trim($xpath), '/');
		if(strlen($xpath) == 0) return;

		if(!class_exists('\XMLReader'))
		{
			$xmlObject = simplexml_load_file($this->filename);
			//$rows = $xmlObject->xpath('/'.$xpath);
			$rows = $this->Xpath($xmlObject, '/'.$xpath);
			return $rows;
		}
		
		$xpath = preg_replace('/\[\d+\]/', '', $xpath);
		$arXpath = $arXpathOrig = explode('/', trim($xpath, '/'));
		
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
				$curXPath = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($curXPath, $this->fileEncoding, $this->siteEncoding);
				if(strpos($xpath.'/', $curXPath.'/')!==0 && strpos($curXPath.'/', $xpath.'/')!==0)
				{
					if(isset($arObjects[$curDepth]) && !in_array(implode('/', array_slice($arXpathOrig, 0, $curDepth+1)), $this->xpathMulti))
					{
						$break = true;
					}
					continue;
				}
				if(strlen($xpath) > strlen($curXPath) && !$wChild) continue;
				
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
					//$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
					if(($pos = strpos($curName, ':'))!==false)
					{
						$rootNS = substr($curName, 0, $pos);
						$curName = substr($curName, strlen($rootNS) + 1);
					}
					$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>', 0, false, $rootNS, true);
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
				
				//if(strlen($xpath)==strlen($curXPath) && !$wChild) $break = true;
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
		$this->ClearIblocksTagCache();
		$arStepParams = array(
			'params'=> array_merge($this->stepparams, array(
				'xmlCurrentRow' => intval($this->xmlCurrentRow),
				'xmlSectionCurrentRow' => intval($this->xmlSectionCurrentRow),
				'xmlIbPropCurrentRow' => intval($this->xmlIbPropCurrentRow),
				'sectionIds' => $this->sectionIds,
				'propertyIds' => $this->propertyIds,
				'sectionsTmp' => $this->sectionsTmp,
			)),
			'action' => $action,
			'errors' => $this->errors,
			'sessid' => bitrix_sessid()
		);
		
		if($action == 'continue')
		{
			file_put_contents($this->tmpfile, serialize($arStepParams['params']));
			unset($arStepParams['params']['sectionIds'], $arStepParams['params']['propertyIds']);
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
		
		unset($arStepParams['params']['currentelement']);
		unset($arStepParams['params']['currentelementitem']);
		return $arStepParams;
	}
	
	public function CompareUploadValue($key, $val, $needval)
	{
		if((!$key && $needval==$val)
			|| ($needval=='{empty}' && strlen($val)==0)
			|| ($needval=='{not_empty}' && strlen($val) > 0)
			|| ($key=='contain' && strpos($val, $needval)!==false)
			|| ($key=='begin' && substr($val, 0, strlen($needval))==$needval)
			|| ($key=='end' && substr($val, -strlen($needval))==$needval)
			|| ($key=='gt' && $this->GetFloatVal($val) > $this->GetFloatVal($needval))
			|| ($key=='lt' && $this->GetFloatVal($val) < $this->GetFloatVal($needval)))
		{
			return true;
		}else return false;
	}
	
	public function PreCheckSkipLine($key, $val)
	{
		$p = $this->fparams[$key];
		if(is_array($p['CONVERSION']) && !empty($p['CONVERSION'])) return false;
		
		$load = true;
		if($load && is_array($p['UPLOAD_VALUES']) && !empty($p['UPLOAD_VALUES']))
		{
			$subload = false;
			$val = ToLower(trim($val));
			$keys = $p['UPLOAD_KEYS'];
			foreach($p['UPLOAD_VALUES'] as $kv=>$needval)
			{
				$key = (isset($keys[$kv]) ? $keys[$kv] : '');
				$needval = ToLower(trim($needval));
				if($this->CompareUploadValue($key, $val, $needval))
				{
					$subload = true;
				}
			}
			$load = ($load && $subload);
		}
		if($load && is_array($p['NOT_UPLOAD_VALUES']) && !empty($p['NOT_UPLOAD_VALUES']))
		{
			$subload = true;
			$val = ToLower(trim($val));
			$keys = $p['NOT_UPLOAD_KEYS'];
			foreach($v['NOT_UPLOAD_VALUES'] as $kv=>$needval)
			{
				$key = (isset($keys[$kv]) ? $keys[$kv] : '');
				$needval = ToLower(trim($needval));
				if($this->CompareUploadValue($key, $val, $needval))
				{
					$subload = false;
				}
			}
			$load = ($load && $subload);
		}
		
		return !$load;
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
				if($type=='element' && (strpos($field, 'ISECT_')===0 || strpos($field, 'ISUBSECT_')===0)) continue;
				if($type=='section' && strpos($field, 'ISECT_')!==0) continue;
				if($type=='offer' && strpos($field, 'OFFER_')!==0) continue;
				if($type=='subsection' && strpos($field, 'ISUBSECT_')!==0) continue;
				if($type=='ibproperty' && strpos($field, 'IBPROP_')!==0) continue;
				if(strpos($xpath, $this->params['GROUPS'][ToUpper($type)])!==0) continue;
				
				if(is_array($v['UPLOAD_VALUES']) || is_array($v['NOT_UPLOAD_VALUES']) || $v['FILTER_EXPRESSION'])
				{
					$val = $arItem[$k];
					$valOrig = $arItem['~'.$k];
					$this->PrepareFieldsBeforeConv($val, $valOrig, $field, $v);
					if(is_array($val))
					{
						foreach($val as $k2=>$v2)
						{
							$val[$k2] = $this->ApplyConversions($valOrig[$k2], $v['CONVERSION'], array());
						}
						$val = implode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $val);
					}
					else
					{
						$val = $this->ApplyConversions($valOrig, $v['CONVERSION'], array());
					}
					$val = ToLower(trim($val));
				}
				else
				{
					$val = '';
				}
				
				if(is_array($v['UPLOAD_VALUES']))
				{
					$subload = false;
					$keys = $v['UPLOAD_KEYS'];
					foreach($v['UPLOAD_VALUES'] as $kv=>$needval)
					{
						$key = (isset($keys[$kv]) ? $keys[$kv] : '');
						$needval = ToLower(trim($needval));
						if($this->CompareUploadValue($key, $val, $needval))
						{
							$subload = true;
						}
					}
					$load = ($load && $subload);
				}
				
				if(is_array($v['NOT_UPLOAD_VALUES']))
				{
					$subload = true;
					$keys = $v['NOT_UPLOAD_KEYS'];
					foreach($v['NOT_UPLOAD_VALUES'] as $kv=>$needval)
					{
						$key = (isset($keys[$kv]) ? $keys[$kv] : '');
						$needval = ToLower(trim($needval));
						if($this->CompareUploadValue($key, $val, $needval))
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
	
	public function ExecuteFilterExpression($val, $expression, $altReturn = true, $arParams = array())
	{
		foreach($arParams as $k=>$v)
		{
			${$k} = $v;
		}
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
	
	public function GetPathAttr(&$arPath)
	{
		$attr = false;
		if(strpos($arPath[count($arPath)-1], '@')===0)
		{
			$attr = substr(array_pop($arPath), 1);
			$attr = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($attr, $this->siteEncoding, $this->fileEncoding);
		}
		return $attr;
	}
	
	public function GetNextIbPropRecord($time)
	{
		if(!isset($this->xmlIbPropCurrentRow) || !is_numeric($this->xmlIbPropCurrentRow))
		{
			$this->xmlIbPropCurrentRow = 0;
		}
		while(isset($this->xmlIbProps[$this->xmlIbPropCurrentRow]))
		{
			$this->currentXmlObj = $simpleXmlObj = $this->xmlIbProps[$this->xmlIbPropCurrentRow];
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($fieldName, 'IBPROP_')!==0) continue;
				
				$xpath = substr($xpath, strlen($this->params['GROUPS']['IBPROPERTY']) + 1);
				if(strlen($xpath) > 0) $arPath = explode('/', $xpath);
				else $arPath = array();
				$attr = $this->GetPathAttr($arPath);
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
							$val[] = (string)$v->attributes()->{$attr};
						}
					}
					else
					{
						$val = (string)$simpleXmlObj2->attributes()->{$attr};
					}
				}
				else
				{
					if(is_array($simpleXmlObj2))
					{
						$val = array();
						foreach($simpleXmlObj2 as $k=>$v)
						{
							$val[] = (string)$v;
						}
					}
					else
					{
						$val = (string)$simpleXmlObj2;
					}					
				}
				
				$val = $this->GetRealXmlValue($val);
		
				$arItem[$key] = (is_array($val) ? array_map(array($this, 'Trim'), $val) : $this->Trim($val));
				$arItem['~'.$key] = $val;
			}

			$this->xmlIbPropCurrentRow++;
			
			if(!$this->CheckSkipLine($arItem, 'ibproperty'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function GetNextSectionRecord($time=0)
	{
		/*while(isset($this->xmlSections[$this->xmlSectionCurrentRow - $this->xmlRowDiff])
			|| ($this->xmlSectionsCount > $this->xmlSectionCurrentRow
				&& $this->InitXml('section')
				&& isset($this->xmlSections[$this->xmlSectionCurrentRow - $this->xmlRowDiff])))
		{*/
		if(!isset($this->xmlSectionCurrentRow) || !is_numeric($this->xmlSectionCurrentRow))
		{
			$this->xmlSectionCurrentRow = 0;
		}
		$moveCnt = 0;
		while(isset($this->xmlSections[$this->xmlSectionCurrentRow]) && ($moveCnt < count($this->xmlSections)))
		{
			$this->currentXmlObj = $simpleXmlObj = $this->xmlSections[$this->xmlSectionCurrentRow];
			$arItem = array();
			$break = $unset = false;
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($fieldName, 'ISECT_')!==0) continue;
				if(strlen($this->params['GROUPS']['SUBSECTION']) > 0 && strpos($xpath, $this->params['GROUPS']['SUBSECTION'])===0) continue;

				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($m[1], strlen($this->params['GROUPS']['SECTION']) + 1);
					}
				}

				$xpath = substr($xpath, strlen($this->params['GROUPS']['SECTION']) + 1);
				if(strlen($xpath) > 0) $arPath = explode('/', $xpath);
				else $arPath = array();
				$attr = $this->GetPathAttr($arPath);
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
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
				
				if(in_array($fieldName, array('ISECT_PARENT_TMP_ID', 'ISECT_TMP_ID')))
				{
					$conversions = $this->fparams[$key]['CONVERSION'];
					if(!empty($conversions))
					{
						$val = $this->ApplyConversions($val, $conversions, $arItem, array('KEY'=>$fieldName, 'NAME'=>$fieldName), array());
					}
				}
				
				if(!$this->useSectionPathByLink)
				{
					if($fieldName=='ISECT_PARENT_TMP_ID' && trim($val) && !isset($this->sectionIds[trim($val)]))
					{
						$break = true;
						break;
					}
					if($fieldName=='ISECT_TMP_ID' && trim($val) && isset($this->sectionIds[trim($val)]) && !$this->subSectionInSection)
					{
						$unset = true;
						$break = true;
						break;
					}
				}
		
				$arItem[$key] = (is_array($val) ? array_map(array($this, 'Trim'), $val) : $this->Trim($val));
				$arItem['~'.$key] = $val;
			}
			if($break)
			{
				if($unset)
				{
					unset($this->xmlSections[$this->xmlSectionCurrentRow]);
					$this->xmlSections = array_values($this->xmlSections);
					$this->xmlSectionCurrentRow = 0;
					$moveCnt = 0;
				}
				else
				{
					$tmpSection = $this->xmlSections[$this->xmlSectionCurrentRow];
					unset($this->xmlSections[$this->xmlSectionCurrentRow]);
					$this->xmlSections = array_values($this->xmlSections);
					$this->xmlSections[] = $tmpSection;
					$this->xmlSectionCurrentRow = 0;
					$moveCnt++;
				}
				continue;
			}
			if($this->elementInSection)
			{
				$this->xmlSectionCurrentRow++;
			}
			else
			{
				unset($this->xmlSections[$this->xmlSectionCurrentRow]);
				$this->xmlSections = array_values($this->xmlSections);
				$this->xmlSectionCurrentRow = 0;
			}
			
			if(!$this->CheckSkipLine($arItem, 'section'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function GetNextSubsection($ID, $arItem, $xmlSubsectionCurrentRow)
	{
		$currentSectionXpath = $this->currentSectionXpath;
		if(!is_object($this->currentXmlObj)) return false;
		//while(isset($this->xmlSubsections[$xmlSubsectionCurrentRow]))
		while(($simpleXmlObj = $this->currentXmlObj)
			&& ($this->currentSectionXpath = $currentSectionXpath.'['.($xmlSubsectionCurrentRow + 1).']')
			&& ($this->xpathReplace = array('FROM' => $this->params['GROUPS']['SUBSECTION'], 'TO' => $this->currentSectionXpath))
			&& ($subsectionXpath = substr($this->xpath, 1))
			&& ($objXpath = substr($this->ReplaceXpath($this->params['GROUPS']['SUBSECTION']), strlen($subsectionXpath) + 1))
			//&& ($simpleXmlObj->xpath($objXpath))
			&& ($this->Xpath($simpleXmlObj, $objXpath))
			)
		{
			/*$simpleXmlObj = $this->currentXmlObj;
			$this->currentSectionXpath = $currentSectionXpath.'['.($xmlSubsectionCurrentRow + 1).']';
			$this->xpathReplace = array(
				//'FROM' => $currentSectionXpath,
				'FROM' => $this->params['GROUPS']['SUBSECTION'],
				'TO' => $this->currentSectionXpath
			);
			$subsectionXpath = substr($this->xpath, 1);*/
			$this->xmlPartObjects = array();

			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($xpath, $this->params['GROUPS']['SUBSECTION'])!==0) continue;
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($this->ReplaceXpath($m[1]), strlen($subsectionXpath) + 1);
					}
					$conditions[$k2]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k2]['FROM']);
				}
				
				$xpath = substr($this->ReplaceXpath($xpath), strlen($subsectionXpath) + 1);
				$arPath = explode('/', $xpath);
				$attr = $this->GetPathAttr($arPath);
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				$xpath2 = implode('/', $arPath);
				
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
		
				$arItem[$key] = (is_array($val) ? array_map(array($this, 'Trim'), $val) : $this->Trim($val));
				$arItem['~'.$key] = $val;
			}

			if(!$this->CheckSkipLine($arItem, 'subsection'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function GetNextRecord($time)
	{
		while(isset($this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff])
			|| (!$this->elementInSection 
				&& $this->xmlElementsCount > $this->xmlCurrentRow
				&& $this->InitXml('element')
				&& isset($this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff])))
		{
			$this->currentXmlObj = $simpleXmlObj = $this->xmlElements[$this->xmlCurrentRow - $this->xmlRowDiff];
			$this->xmlPartObjects = array();
			
			$skipLine = false;
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($fieldName, 'ISECT_')===0) continue;
				if(strlen($this->params['GROUPS']['OFFER']) > 0 && strpos($xpath, rtrim($this->params['GROUPS']['OFFER'], '/').'/')===0) continue;
				if($this->propertyInElement && strpos($xpath, $this->params['GROUPS']['PROPERTY'])===0) continue;
				
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
				$attr = $this->GetPathAttr($arPath);
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
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
				if($this->PreCheckSkipLine($key, $val))
				{
					$skipLine = true;
					break;
				}
		
				/*$arItem[$fieldName] = (is_array($val) ? array_map('trim', $val) : trim($val));
				$arItem['~'.$fieldName] = $val;*/
				$arItem[$key] = (is_array($val) ? array_map(array($this, 'Trim'), $val) : $this->Trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlCurrentRow++;
			
			if(!$skipLine && !$this->CheckSkipLine($arItem, 'element'))
			{
				return $arItem;
			}
			if($this->CheckTimeEnding($time)) return false;
		}
		
		return false;
	}
	
	public function GetNextOffer($ID, $arParentItem)
	{
		while(isset($this->xmlOffers[$this->xmlOfferCurrentRow]))
		{
			$simpleXmlObj = $this->currentXmlObj;
			//$this->currentXmlObj = $simpleXmlObj = $this->xmlOffers[$this->xmlOfferCurrentRow];
			$this->xmlPartObjects = array();
		
			$this->xpathReplace = array(
				'FROM' => $this->params['GROUPS']['OFFER'],
				'TO' => $this->params['GROUPS']['OFFER'].'['.($this->xmlOfferCurrentRow + 1).']'
			);
			$offerXpath = substr($this->xpath, 1);

			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($xpath.'/', $this->params['GROUPS']['OFFER'].'/')!==0)
				{
					if($fieldName=='VARIABLE')
					{
						$arItem[$key] = $arParentItem[$key];
						$arItem['~'.$key] = $arParentItem['~'.$key];
					}
					continue;
				}
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($this->ReplaceXpath($m[1]), strlen($offerXpath) + 1);
					}
					$conditions[$k2]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k2]['FROM']);
				}
					
				$xpath = substr($this->ReplaceXpath($xpath), strlen($offerXpath) + 1);
				$arPath = explode('/', $xpath);
				$attr = $this->GetPathAttr($arPath);
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				$xpath2 = implode('/', $arPath);
				
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
		
				$arItem[$key] = (is_array($val) ? array_map(array($this, 'Trim'), $val) : $this->Trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlOfferCurrentRow++;

			if(!$this->CheckSkipLine($arItem, 'offer'))
			{
				return $arItem;
			}
		}
		
		return false;
	}
	
	public function GetNextProperty($groupXpath = '')
	{
		if(strlen($groupXpath)==0) $groupXpath = $this->params['GROUPS']['PROPERTY'];
		while(isset($this->xmlProperties[$this->xmlPropertiesCurrentRow]))
		{
			$simpleXmlObj = $this->currentParentXmlObj;
			$this->currentXmlObj = $this->xmlProperties[$this->xmlPropertiesCurrentRow];
			$this->xmlPartObjects = array();
		
			$this->xpathReplace = array(
				'FROM' => $this->params['GROUPS']['PROPERTY'],
				'TO' => $groupXpath.'['.($this->xmlPropertiesCurrentRow + 1).']'
			);
			$propertyXpath = substr($this->parentXpath, 1);
			
			$arItem = array();
			foreach($this->params['FIELDS'] as $key=>$field)
			{
				$val = '';
				list($xpath, $fieldName) = explode(';', $field, 2);
				if(strpos($xpath, $this->params['GROUPS']['PROPERTY'])!==0) continue;
				
				$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
				$conditions = $this->fparams[$key]['CONDITIONS'];
				if(!is_array($conditions)) $conditions = array();
				foreach($conditions as $k2=>$v2)
				{
					if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
					{
						$conditions[$k2]['XPATH'] = substr($this->ReplaceXpath($m[1]), strlen($propertyXpath) + 1);
					}
					$conditions[$k2]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k2]['FROM']);
				}
			
				$xpath = substr($this->ReplaceXpath($xpath), strlen($propertyXpath) + 1);
				$arPath = explode('/', $xpath);
				$attr = $this->GetPathAttr($arPath);
				if(count($arPath) > 0)
				{
					//$simpleXmlObj2 = $simpleXmlObj->xpath(implode('/', $arPath));
					$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
					if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);	
				}
				else $simpleXmlObj2 = $simpleXmlObj;
				$xpath2 = implode('/', $arPath);
				
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
		
				$arItem[$key] = (is_array($val) ? array_map(array($this, 'Trim'), $val) : $this->Trim($val));
				$arItem['~'.$key] = $val;
			}
			$this->xmlPropertiesCurrentRow++;
			
			if(!$this->CheckSkipLine($arItem, 'property'))
			{
				return $arItem;
			}
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
				if(!isset($this->xmlPartsValues[$xpath2]))
				{
					$arValues = array();
					foreach($xmlPart as $k=>$xmlObj)
					{
						if(strlen($arXpath2['subpath'])==0) $xmlObj2 = $xmlObj;
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
						//$arValues[$k] = $val2;
						$arValues[$val2] = $k;
					}
					$this->xmlPartsValues[$xpath2] = $arValues;
				}
				$xmlPartsValues = $this->xmlPartsValues[$xpath2];
				
				if(is_array($xmlPart))
				{
					$valXpath = $xpath;
					$parentXpath = (isset($this->parentXpath) && strlen($this->parentXpath) > 0 ? $this->parentXpath : '');
					$parentXpathWS = trim($parentXpath, '/');
					$xpathReplaced = false;
					if($this->replaceXpathCell)
					{
						$valXpath2 = trim($this->replaceXpathCell, '{}');
						$parentXpath2 = trim($this->xpath, '/');
						if(strlen($parentXpath2) > 0 && strpos($valXpath2, $parentXpath2)===0)
						{
							$valXpath = substr($valXpath2, strlen($parentXpath2)+1);
							if(strlen($parentXpathWS) > 0 && strpos($parentXpath2, $parentXpathWS)===0)
							{
								$valXpath = substr($parentXpath2, strlen($parentXpathWS)+1).'/'.ltrim($valXpath, '/');
							}
							$xpathReplaced = true;
						}
					}
					if(strlen($parentXpath) > 0)
					{
						$valXpath = rtrim($this->parentXpath, '/').'/'.ltrim($valXpath, '/');
						if($xpathReplaced) $valXpath = $this->ReplaceXpath($valXpath);
					}
					$val = $this->GetValueByXpath($valXpath, $simpleXmlObj, true);
					$k = false;
					if(strlen($val) > 0 && isset($xmlPartsValues[$val])) $k = $xmlPartsValues[$val];

					if($k!==false)
					{
						$this->xmlPartObjects[$arXpath2['xpath']] = $xmlPart[$k];
						return $val;
					}
					else return '';
					
					/*foreach($xmlPart as $xmlObj)
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
					}*/
				}
			}
		}
		$arPath = explode('/', $xpath2);
		$attr = $this->GetPathAttr($arPath);
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
		$attr = $this->GetPathAttr($arPath);
		$xpath2 = implode('/', $arPath);
		$xpath3 = '';
		if(strpos($xpath2, '//')!==false && strpos($xpath2, '//') > 0)
		{
			list($xpath2, $xpath3) = explode('//', $xpath2, 2);
		}
		$xpath2 = rtrim($xpath2, '/');
		return array('xpath'=>$xpath2, 'subpath' => $xpath3, 'attr'=>$attr);
	}
	
	public function GetToXpathReplace($arPath, $lastElem, $lastKey, $key, $simpleXmlObj)
	{
		$toXpath = ltrim(implode('/', $arPath).'/'.$lastElem.'['.$lastKey.']', '/');
		if(count($this->Xpath($simpleXmlObj, $toXpath))==0)
		{
			$keyOrig = $key;
			$arPath[] = $lastElem;
			$arNewPath = array();
			while(count($arPath) > 0)
			{
				$arNewPath[] = array_shift($arPath);
				if(count($arPath) > 0)
				{
					$objs = $this->Xpath($simpleXmlObj, implode('/', $arNewPath));
					if(count($objs) > 1)
					{
						$key2 = $key;
						$k = -1;
						while($key2 >= 0 && isset($objs[++$k]))
						{
							$key2 -= count($this->Xpath($objs[$k], implode('/', $arPath)));
							if($key2 >= 0) $key = $key2;
						}
						$lastInd = count($arNewPath) - 1;
						if(!preg_match('/\[\d+\]/', $arNewPath[$lastInd]))
						{
							$arNewPath[$lastInd] = $arNewPath[$lastInd].'['.($k + 1).']';
						}
					}
				}
				else
				{
					$lastInd = count($arNewPath) - 1;
					if(!preg_match('/\[\d+\]/', $arNewPath[$lastInd]))
					{
						$arNewPath[$lastInd] = $arNewPath[$lastInd].'['.($key + 1).']';
					}
				}
			}
			if(count($this->Xpath($simpleXmlObj, implode('/', $arNewPath))) > 0)
			{
				$toXpath = ltrim(implode('/', $arNewPath), '/');
			}
		}
		return $toXpath;
	}
	
	public function CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2, $key=false)
	{
		if(empty($conditions)) return true;
		if($key!==false)
		{
			$arPath = explode('/', $xpath);
			$attr = $this->GetPathAttr($arPath);
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
					//'TO' => ltrim(implode('/', $arPath).'/'.$lastElem.'['.($key2+1).']', '/')
					'TO' => $this->GetToXpathReplace($arPath, $lastElem, ($key2+1), $key, $simpleXmlObj)
				);
				foreach($conditions as $k3=>$v3)
				{
					$conditions[$k3]['XPATH'] = str_replace($this->xpathReplace['FROM'], $this->xpathReplace['TO'], $conditions[$k3]['XPATH']);
					//FIX: a bunch of several values of the same tag with other xml nodes does not work with this line
					//$conditions[$k3]['FROM'] = preg_replace_callback('/^\{(\S*)\}$/', array($this, 'ReplaceConditionXpath'), $conditions[$k3]['FROM']);
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
				$this->replaceXpathCell = $v['CELL'];
				$this->replaceSimpleXmlObj = $simpleXmlObj;
				$this->replaceSimpleXmlObj2 = $simpleXmlObj2;
				$v['FROM'] = preg_replace_callback($pattern, array($this, 'ReplaceConditionXpathToValue'), $v['FROM']);
			}
			
			$xpath2 = $v['XPATH'];

			$generalXpath = $xpath;
			if(strpos($xpath, '@')!==false) $generalXpath = rtrim(substr($xpath, 0, strpos($xpath, '@')), '/');
			/*Attempt of relative seaarch node*/
			if(strpos($xpath2, $generalXpath)!==0 && strpos($xpath2, '[')===false && strpos($generalXpath, '[')===false)
			{
				$diffLevel = 0;
				$sharedXpath = ltrim($generalXpath, '/');
				$arSharedXpath = explode('/', $sharedXpath);
				while(count($arSharedXpath) > 0 && strpos($xpath2, $sharedXpath)!==0)
				{
					array_pop($arSharedXpath);
					$sharedXpath = implode('/', $arSharedXpath);
					$diffLevel++;
				}
				if(strlen($sharedXpath) > 0 && strpos($xpath2, $sharedXpath)===0 && $diffLevel > 0)
				{
					$simpleXmlObjArr = $simpleXmlObj2->xpath(substr(str_repeat('../', $diffLevel), 0, -1));
					if(is_array($simpleXmlObjArr) && count($simpleXmlObjArr)==1) $simpleXmlObjArr = current($simpleXmlObjArr);
					if(is_object($simpleXmlObjArr))
					{
						$simpleXmlObj2 = $simpleXmlObjArr;
						$generalXpath = $sharedXpath;
					}
				}
			}
			/*/Attempt of relative seaarch node*/
			if(strpos($xpath2, $generalXpath)===0)
			{
				//$xpath2 = substr($xpath2, strlen($xpath) + 1);
				$xpath2 = substr($xpath2, strlen($generalXpath));
				$xpath2 = ltrim(preg_replace('/^\[\d*\]/', '', $xpath2), '/');
				$simpleXmlObj = $simpleXmlObj2;
			}
			$arPath = explode('/', $xpath2);
			$attr = $this->GetPathAttr($arPath);
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
								//'TO' => implode('/', $arPath2).'/'.$lastElem.'['.($k2+1).']'
								'TO' => $this->GetToXpathReplace($arPath2, $lastElem, ($k2+1), $key, $simpleXmlObj)
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
		$condVal = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($condVal, $this->fileEncoding, $this->siteEncoding);
		$condVal = preg_replace('/\s+/', ' ', trim($condVal));
		$v['FROM'] = preg_replace('/\s+/', ' ', trim($v['FROM']));
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
	
	public function SaveIbPropRecord($arItem)
	{
		if(count(array_diff(array_map('trim', $arItem), array('')))==0)
		{
			return false;
		}
	
		$IBLOCK_ID = $this->params['IBLOCK_ID'];
		$arFields = array();
		$tmpID = false;
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions))
			{
				$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				if($value===false) continue;
			}
			
			if(strpos($field, 'IBPROP_')===0)
			{
				$fieldName = substr($field, 7);
				if($fieldName=='TMP_ID') $tmpID = $value;
				else $arFields[$fieldName] = $value;
			}
		}
		
		$arFilter = array();
		if(isset($arFields['CODE']) && strlen(trim($arFields['CODE'])) > 0)
		{
			$arFilter['CODE'] = $arFields['CODE'];
		}
		elseif(isset($arFields['NAME']) && strlen(trim($arFields['NAME'])) > 0)
		{
			$arFilter['NAME'] = $arFields['NAME'];
		}
		if(!empty($arFilter))
		{
			$arFilter['IBLOCK_ID'] = $IBLOCK_ID;
			$arFields['IBLOCK_ID'] = $IBLOCK_ID;
			$arFields['ACTIVE'] = 'Y';
			$arFields['MULTIPLE'] = $this->GetBoolValue($arFields['MULTIPLE']);
			$arFields['WITH_DESCRIPTION'] = $this->GetBoolValue($arFields['WITH_DESCRIPTION']);
			$arFields['SMART_FILTER'] = $this->GetBoolValue($arFields['SMART_FILTER']);
			
			if($arFields['SMART_FILTER'] == 'Y')
			{
				if(\CIBlock::GetArrayByID($arFields["IBLOCK_ID"], "SECTION_PROPERTY") != "Y")
				{
					$ib = new \CIBlock;
					$ib->Update($arFields["IBLOCK_ID"], array('SECTION_PROPERTY'=>'Y'));
				}
			}
			
			if(strpos($arFields['PROPERTY_TYPE'], ':')!==false)
			{
				list($ptype, $utype) = explode(':', $arFields['PROPERTY_TYPE'], 2);
				$arFields['PROPERTY_TYPE'] = $ptype;
				$arFields['USER_TYPE'] = $utype;
			}
				
			if(isset($arFields['NAME']) && !isset($arFields['CODE']))
			{
				$arParams = array(
					'max_len' => 50,
					'change_case' => 'U',
					'replace_space' => '_',
					'replace_other' => '_',
					'delete_repeat_replace' => 'Y',
				);
				$propCode = $codePrefix. \CUtil::translit($arFields['NAME'], LANGUAGE_ID, $arParams);
				$propCode = preg_replace('/[^a-zA-Z0-9_]/', '', $propCode);
				$propCode = preg_replace('/^[0-9_]+/', '', $propCode);
				$arFields['CODE'] = $propCode;
			}
			
			$arPropFields = $arFields;
			unset($arPropFields['VALUES']);
			$propID = 0;
			if($arr = \CIBlockProperty::GetList(array(), $arFilter)->Fetch())
			{
				$ibp = new \CIBlockProperty;
				$ibp->Update($arr['ID'], $arPropFields);
				if(isset($arPropFields['SMART_FILTER']))
				{
					$dbRes2 = \Bitrix\Iblock\SectionPropertyTable::getList(array("select" => array("SECTION_ID", "PROPERTY_ID"), "filter" => array("=IBLOCK_ID" => $arFields['IBLOCK_ID'] ,"=PROPERTY_ID" => $arr['ID'])));
					while($arr2 = $dbRes2->Fetch())
					{
						\CIBlockSectionPropertyLink::Set($arr2['SECTION_ID'], $arr2['PROPERTY_ID'], array('SMART_FILTER'=>$arPropFields['SMART_FILTER']));
					}
				}
				$propID = $arr['ID'];
			}
			else
			{
				$this->PreparePropertyCode($arPropFields);
				$ibp = new \CIBlockProperty;
				$propID = $ibp->Add($arPropFields);
			}
			
			if($propID > 0)
			{
				if($tmpID!==false && strlen($tmpID) > 0)
				{
					$this->propertyIds[$tmpID] = $propID;
				}
				if($arFields['PROPERTY_TYPE']=='L' && !empty($arFields['VALUES']))
				{
					$arPropFields['ID'] = $propID;
					$arValues = $arFields['VALUES'];
					if(!is_array($arValues))
					{
						$arValues = explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arValues);
						$arValues = array_diff(array_unique(array_map('trim', $arValues)), array(''));
					}
					foreach($arValues as $value)
					{
						$this->GetListPropertyValue($arPropFields, $value);
					}
				}
			}
		}
		
		$this->SaveStatusImport();
		return $sectionID;
	}
	
	public function SaveSectionRecord($arItem, $parentSectionId=0)
	{
		if(count(array_diff(array_map('trim', $arItem), array('')))==0)
		{
			return false;
		}
	
		$IBLOCK_ID = $this->params['IBLOCK_ID'];
		$SECTION_ID = $this->params['SECTION_ID'];
		$arParams = array();
		$sectionUid = $this->params['SECTION_UID'];
		
		$arFieldsSections = array();
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions) && !in_array($field, array('ISECT_PARENT_TMP_ID', 'ISECT_TMP_ID')))
			{
				$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$field, 'NAME'=>$field), $iblockFields);
				if($value===false) continue;
			}
			
			$prefix = ($parentSectionId > 0 ? 'ISUBSECT_' : 'ISECT_');
			if(strpos($field, $prefix)===0)
			{
				$adata = false;
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
				}
				$fKey = substr($field, strlen($prefix));
				$arFieldsSections[$fKey] = $value;
				
				if(is_array($adata) && count($adata) > 1)
				{
					$arFieldsSections[$adata[0]] = $adata[1];
				}
				
				if($fKey==$sectionUid)
				{
					$arParams = $this->fparams[$key];
				}
			}
		}
		
		if($this->useSectionPathByLink)
		{
			$this->sectionsTmp[$arFieldsSections['TMP_ID']] = array(
				'PARENT' => $arFieldsSections['PARENT_TMP_ID'],
				'NAME' => $arFieldsSections['NAME']
			);
			$this->SaveStatusImport();
			return $arFieldsSections['TMP_ID'];
		}
		
		if($parentSectionId > 0)
		{
			$parentId = $parentSectionId;
		}
		else
		{
			$parentId = ($SECTION_ID ? (int)$SECTION_ID : 0);
			if(isset($arFieldsSections['PARENT_TMP_ID']))
			{
				if(isset($this->sectionIds[$arFieldsSections['PARENT_TMP_ID']]))
				{
					$parentId = $this->sectionIds[$arFieldsSections['PARENT_TMP_ID']];
				}
				unset($arFieldsSections['PARENT_TMP_ID']);
			}
		}
		$tmpId = 0;
		if(isset($arFieldsSections['TMP_ID']))
		{
			$tmpId = $arFieldsSections['TMP_ID'];
			unset($arFieldsSections['TMP_ID']);
		}
	
		$sectionID = 0;
		$sectIds = $this->SaveSection($arFieldsSections, $IBLOCK_ID, $parentId, 0, $arParams);
		if(!empty($sectIds))
		{
			$sectionID = end($sectIds);
			$this->sectionIds[$tmpId] = $sectionID;
			
			$this->SaveSectionRecordAfter($sectionID, $arItem);
		}
		
		$this->SaveStatusImport();
		return $sectionID;
	}
	
	public function SaveSectionRecordAfter($sectionID, $arItem)
	{
		if(!$sectionID) return;
		$currentXpath = $this->currentSectionXpath;
		
		if($this->subSectionInSection)
		{
			$xpath = trim(substr($this->params['GROUPS']['SUBSECTION'], strlen($this->params['GROUPS']['SECTION'])), '/');
			$this->currentSectionXpath = $currentSectionXpath = $this->currentSectionXpath.'/'.$xpath;
			$xpath2 = trim(substr($currentSectionXpath, strlen($this->params['GROUPS']['SECTION'])), '/');
			//if($this->currentXmlObj->xpath($xpath2))
			if($this->Xpath($this->currentXmlObj, $xpath2))
			{
				//$this->xmlSubsections = $xmlSubsections = $this->currentXmlObj->xpath($xpath);
				$this->xmlSubsections = $xmlSubsections = $this->Xpath($this->currentXmlObj, $xpath);
				$xmlSubsectionCurrentRow = 0;
				if($this->stepparams['xmlSubsectionCurrentRowInSection'] > 0)
				{
					$xmlSubsectionCurrentRow = $this->stepparams['xmlSubsectionCurrentRowInSection'];
				}
				$this->stepparams['xmlSubsectionCurrentRowInSection'] = 0;
				while($arSubsectionItem = $this->GetNextSubsection($sectionID, $arItem, $xmlSubsectionCurrentRow))
				{
					$this->SaveSectionRecord($arSubsectionItem, $sectionID);
					$this->currentSectionXpath = $currentSectionXpath;
					$this->xmlSubsections = $xmlSubsections;
					if($this->CheckTimeEnding())
					{
						$this->stepparams['xmlSubsectionCurrentRowInSection'] = $xmlSubsectionCurrentRow;
						return $this->GetBreakParams();
					}
					$xmlSubsectionCurrentRow++;
				}
				$this->currentSectionXpath = $currentSectionXpath;
				$this->xmlSubsections = $xmlSubsections;
			}
		}
		
		if($this->elementInSection)
		{
			$parentXpath = $this->xpath;
			$this->xpath = '/'.trim(preg_replace('/\[\d+\]/', '', $currentXpath), '/').'/'.$this->xpathElementInSection;
			
			$xpath = trim(substr($currentXpath, strlen($this->params['GROUPS']['SECTION'])), '/');
			if(strlen($xpath) > 0) $xpath .= '/';
			$xpath .= $this->xpathElementInSection;
			//$this->xmlElements = $this->currentXmlObj->xpath($xpath);
			$this->xmlElements = $this->Xpath($this->currentXmlObj, $xpath);
			$count = count($this->xmlElements);
			if($count > 0)
			{
				/*$this->xmlElementsCount += $count;
				$this->stepparams['total_file_line'] = $this->xmlElementsCount;*/
				$this->currentParentSectionXmlObj = $this->currentXmlObj;
				$this->xmlCurrentRow = 0;
				if($this->stepparams['xmlCurrentRowInSection'] > 0)
				{
					$this->xmlCurrentRow = $this->stepparams['xmlCurrentRowInSection'];
				}
				$this->stepparams['xmlCurrentRowInSection'] = 0;
				while($arItem = $this->GetNextRecord($time))
				{
					if(is_array($arItem)) $this->SaveRecord($arItem, $sectionID);
					if($this->CheckTimeEnding())
					{
						$this->stepparams['xmlCurrentRowInSection'] = $this->xmlCurrentRow;
						return $this->GetBreakParams();
					}
				}
				//if($this->CheckTimeEnding($time)) return $this->GetBreakParams();
				$this->currentXmlObj = $this->currentParentSectionXmlObj;
			}
			$this->xpath = $parentXpath;
		}
	}
	
	public function SaveRecord($arItem, $sectionID=0)
	{
		$this->stepparams['total_read_line']++;
		if(count(array_diff(array_map('trim', $arItem), array('')))==0)
		{
			return false;
		}
		$this->stepparams['total_line']++;
		
		$IBLOCK_ID = $this->params['IBLOCK_ID'];
		$SECTION_ID = $this->params['SECTION_ID'];
		if($sectionID > 0) $SECTION_ID = $sectionID;
		
		$arFieldsDef = $this->fl->GetFields($IBLOCK_ID);
		$propsDef = $this->GetIblockProperties($IBLOCK_ID);

		$iblockFields = $this->GetIblockFields($IBLOCK_ID);
		$fieldList = preg_grep('/^[^~]/', array_keys($arItem));
		
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if($field!='VARIABLE') continue;

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
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field, 'INDEX'=>$k2), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field, 'INDEX'=>$k2), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field), $iblockFields);
				}
			}
			$arItem[$key] = $value;
			$arItem['~'.$key] = $origValue;
		}

		$arFieldsElement = array();
		$arFieldsElementOrig = array();
		$arFieldsPrices = array();
		$arFieldsProduct = array();
		$arFieldsProductStores = array();
		$arFieldsProductDiscount = array();
		$arFieldsProps = array();
		$arFieldsPropsOrig = array();
		$arFieldsSections = array();
		$arFieldsIpropTemp = array();
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if($field=='VARIABLE') continue;

			$value = $arItem[$key];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$key];
			$origValue = $arItem['~'.$key];
			
			$this->PrepareFieldsBeforeConv($value, $origValue, $field, $this->fparams[$key]);
			$conversions = $this->fparams[$key]['CONVERSION'];
			if(!empty($conversions))
			{
				if(is_array($value))
				{
					foreach($value as $k2=>$v2)
					{
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field, 'INDEX'=>$k2), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field, 'INDEX'=>$k2), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field), $iblockFields);
				}
				if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
			}
			$this->PrepareElementFields($value, $origValue, $field, $this->fparams[$key]);
			
			if(strpos($field, 'IE_')===0)
			{
				$fieldKey = substr($field, 3);
				if($fieldKey=='IBLOCK_SECTION_TMP_ID')
				{
					$arSectionIds = array();
					if(!empty($value))
					{
						if(is_array($value))
						{
							foreach($value as $value2)
							{
								if(isset($this->sectionIds[$value2])) $arSectionIds[] = $this->sectionIds[$value2];
							}
						}
						elseif(isset($this->sectionIds[$value])) $arSectionIds[] = $this->sectionIds[$value];
					}
					if(!empty($arSectionIds))
					{
						$arFieldsElement['IBLOCK_SECTION'] = $arSectionIds;
					}
				}
				elseif($fieldKey=='SECTION_PATH')
				{
					$tmpSep = ($this->fparams[$key]['SECTION_PATH_SEPARATOR'] ? $this->fparams[$key]['SECTION_PATH_SEPARATOR'] : '/');
					if($this->fparams[$key]['SECTION_PATH_SEPARATED']=='Y')
					{
						if(is_array($value))
						{
							$arVals = array();
							foreach($value as $subvalue)
							{
								$arVals = array_merge($arVals, explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $subvalue));
							}
						}
						else
						{
							$arVals = explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $value);
						}
					}
					elseif(is_array($value)) $arVals = $value;
					else $arVals = array($value);
					foreach($arVals as $subvalue)
					{
						$tmpVal = array_map('trim', explode($tmpSep, $subvalue));
						$arFieldsElement[$fieldKey][] = $tmpVal;
						$arFieldsElementOrig[$fieldKey][] = $tmpVal;
					}
				}
				else
				{
					if(strpos($fieldKey, '|')!==false)
					{
						list($fieldKey, $adata) = explode('|', $fieldKey);
						$adata = explode('=', $adata);
						if(count($adata) > 1)
						{
							$arFieldsElement[$adata[0]] = $adata[1];
						}
					}
					if(isset($arFieldsElement[$fieldKey]) && in_array($field, $this->params['ELEMENT_UID']))
					{
						if(!is_array($arFieldsElement[$fieldKey]))
						{
							$arFieldsElement[$fieldKey] = array($arFieldsElement[$fieldKey]);
							$arFieldsElementOrig[$fieldKey] = array($arFieldsElementOrig[$fieldKey]);
						}
						$arFieldsElement[$fieldKey][] = $value;
						$arFieldsElementOrig[$fieldKey][] = $origValue;
					}
					else
					{
						$arFieldsElement[$fieldKey] = $value;
						$arFieldsElementOrig[$fieldKey] = $origValue;
					}
				}
			}
			elseif(strpos($field, 'ISECT')===0)
			{
				$adata = false;
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
				}
				$arSect = explode('_', substr($field, 5), 2);
				$arFieldsSections[$arSect[0]][$arSect[1]] = $value;
				
				if(is_array($adata) && count($adata) > 1)
				{
					$arFieldsSections[$arSect[0]][$adata[0]] = $adata[1];
				}
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$val = $value;
				if(substr($field, -6)=='_PRICE')
				{
					if(!in_array($val, array('', '-')))
					{
						//$val = $this->GetFloatVal($val);
						$val = $this->ApplyMargins($val, $this->fparams[$key]);
					}
				}
				elseif(substr($field, -6)=='_EXTRA')
				{
					$val = $this->GetFloatVal($val);
				}
				
				$arPrice = explode('_', substr($field, 10), 2);
				$pkey = $arPrice[1];
				if($pkey=='PRICE' && $this->fparams[$key]['PRICE_USE_EXT']=='Y')
				{
					$pkey = $pkey.'|QUANTITY_FROM='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_FROM']).'|QUANTITY_TO='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_TO']);
				}
				$arFieldsPrices[$arPrice[0]][$pkey] = $val;
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				$arFieldsProductStores[$arStore[0]][$arStore[1]] = $value;
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						$arFieldsProductDiscount[$adata[0]] = $adata[1];
					}
				}
				$arFieldsProductDiscount[substr($field, 14)] = $value;
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				$val = $value;
				if($field=='ICAT_PURCHASING_PRICE')
				{
					if($val=='') continue;
					$val = $this->GetFloatVal($val);
				}
				elseif($field=='ICAT_MEASURE')
				{
					$val = $this->GetMeasureByStr($val);
				}
				$arFieldsProduct[substr($field, 5)] = $val;
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldName = substr($field, 7);
				if(substr($fieldName, -12)=='_DESCRIPTION') $currentPropDef = $propsDef[substr($fieldName, 0, -12)];
				else $currentPropDef = $propsDef[$fieldName];
				$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $currentPropDef, $fieldName, $value, $origValue, $this->params['ELEMENT_UID']);
			}
			elseif(strpos($field, 'IP_LIST_PROPS')===0)
			{
				$this->GetPropList($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $IBLOCK_ID, $value);
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				$fieldName = substr($field, 11);
				$arFieldsIpropTemp[$fieldName] = $value;
			}
		}

		if($this->sectionInElement)
		{
			$xmlPartObjects = $this->xmlPartObjects;
			$arElementSections = array();
			$this->currentParentXmlObj = $this->currentXmlObj;
			$xpath = trim(substr($this->params['GROUPS']['SECTION'], strlen($this->params['GROUPS']['ELEMENT'])), '/');
			$this->xmlSections = $this->Xpath($this->currentParentXmlObj, $xpath);
			$this->xmlSectionCurrentRow = 0;
			while($arSectionItem = $this->GetNextSectionRecord())
			{
				$this->currentSectionXpath = rtrim($this->params['GROUPS']['SECTION'], '/');
				if(is_array($arSectionItem))
				{
					$sectId = $this->SaveSectionRecord($arSectionItem);
					if(is_numeric($sectId) && $sectId > 0 && !in_array($sectId, $arElementSections))
					{
						$arElementSections[] = $sectId;
					}
				}
			}
			$this->currentXmlObj = $this->currentParentXmlObj;
			if(!empty($arElementSections))
			{
				$arFieldsElement['IBLOCK_SECTION'] = $arElementSections;
			}
			$this->xmlPartObjects = $xmlPartObjects;
		}
		
		if($this->params['NOT_LOAD_ELEMENTS_WO_SECTION']=='Y' 
			&& (!isset($arFieldsElement['IBLOCK_SECTION']) || empty($arFieldsElement['IBLOCK_SECTION']))
			&& (!isset($arFieldsElement['SECTION_PATH']) || empty($arFieldsElement['SECTION_PATH']))
			&& empty($arFieldsSections)
			&& !$sectionID
			)
		{
			$this->stepparams['correct_line']++;
			return false;
		}
		
		$this->AddGroupsProperties($arFieldsProps, $arFieldsPropsOrig, $IBLOCK_ID);
		
		if($sectionID > 0 && !isset($arFieldsElement['IBLOCK_SECTION']))
		{
			$arFieldsElement['IBLOCK_SECTION'] = array($sectionID);
		}

		$arUid = array();
		if(!is_array($this->params['ELEMENT_UID'])) $this->params['ELEMENT_UID'] = array($this->params['ELEMENT_UID']);
		foreach($this->params['ELEMENT_UID'] as $tuid)
		{
			$uid = $valUid = $valUid2 = $nameUid = '';
			$canSubstring = true;
			if(strpos($tuid, 'IE_')===0)
			{
				$nameUid = $arFieldsDef['element']['items'][$tuid];
				$uid = substr($tuid, 3);
				if(strpos($uid, '|')!==false) $uid = current(explode('|', $uid));
				$valUid = $arFieldsElementOrig[$uid];
				$valUid2 = $arFieldsElement[$uid];
				
				if($uid == 'ACTIVE_FROM' || $uid == 'ACTIVE_TO')
				{
					$uid = 'DATE_'.$uid;
					$valUid = $this->GetDateVal($valUid);
					$valUid2 = $this->GetDateVal($valUid2);
				}
			}
			elseif(strpos($tuid, 'IP_PROP')===0)
			{
				$nameUid = $arFieldsDef['prop']['items'][$tuid];
				$uid = substr($tuid, 7);
				$valUid = $arFieldsPropsOrig[$uid];
				$valUid2 = $arFieldsProps[$uid];
				if($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$uid = 'PROPERTY_'.$uid.'_VALUE';
				}
				elseif($propsDef[$uid]['PROPERTY_TYPE']=='N' && !is_numeric($valUid))
				{
					$valUid = $valUid2 = '';
				}
				else
				{
					if($propsDef[$uid]['PROPERTY_TYPE']=='S' && $propsDef[$uid]['USER_TYPE']=='directory')
					{
						$valUid = $this->GetHighloadBlockValue($propsDef[$uid], $valUid);
						$valUid2 = $this->GetHighloadBlockValue($propsDef[$uid], $valUid2);
						$canSubstring = false;
					}
					elseif($propsDef[$uid]['PROPERTY_TYPE']=='E')
					{
						$valUid = $this->GetIblockElementValue($propsDef[$uid], $valUid, $this->fieldSettings[$tuid]);
						$valUid2 = $this->GetIblockElementValue($propsDef[$uid], $valUid2, $this->fieldSettings[$tuid]);
						$canSubstring = false;
					}
					$uid = 'PROPERTY_'.$uid;
				}
			}
			if($uid)
			{
				$arUid[] = array(
					'uid' => $uid,
					'nameUid' => $nameUid,
					'valUid' => $valUid,
					'valUid2' => $valUid2,
					'substring' => ($this->fieldSettings[$tuid]['UID_SEARCH_SUBSTRING']=='Y' && $canSubstring)
				);
			}
		}
		
		$emptyFields = array();
		foreach($arUid as $k=>$v)
		{
			if((is_array($v['valUid']) && count(array_diff($v['valUid'], array('')))==0)
				|| (!is_array($v['valUid']) && strlen(trim($v['valUid']))==0)) $emptyFields[] = $v['nameUid'];
		}
		
		if(!empty($emptyFields) || empty($arUid))
		{
			$bEmptyElemFields = (bool)(count(array_diff($arFieldsElement, array('')))==0 && count(array_diff($arFieldsProps, array('')))==0);
			$res = false;
			
			//$res = (bool)($res && $bEmptyElemFields);
			$res = (bool)($res);
			
			if(!$res)
			{
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_FIELD"), implode(', ', $emptyFields), '').(strlen($arFieldsElement['NAME']) > 0 ? ' ('.$arFieldsElement['NAME'].')' : '');
				$this->stepparams['error_line']++;
			}
			else
			{
				$this->stepparams['correct_line']++;
			}
			return false;
		}
		
		$arDates = array('ACTIVE_FROM', 'ACTIVE_TO', 'DATE_CREATE');
		foreach($arDates as $keyDate)
		{
			if(isset($arFieldsElement[$keyDate]) && strlen($arFieldsElement[$keyDate]) > 0)
			{
				$arFieldsElement[$keyDate] = $this->GetDateVal($arFieldsElement[$keyDate]);
			}
		}
		
		if(isset($arFieldsElement['ACTIVE']))
		{
			$arFieldsElement['ACTIVE'] = $this->GetBoolValue($arFieldsElement['ACTIVE']);
		}
		elseif($this->params['ELEMENT_LOADING_ACTIVATE']=='Y')
		{
			$arFieldsElement['ACTIVE'] = 'Y';
		}

		if(($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y' && isset($arFieldsProduct['QUANTITY']) && $this->GetFloatVal($arFieldsProduct['QUANTITY'])<=0)
			|| ($this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y' && $this->IsEmptyPrice($arFieldsPrices)))
		{
			$arFieldsElement['ACTIVE'] = 'N';
		}
		
		$arKeys = array_merge(array('ID', 'NAME', 'IBLOCK_SECTION_ID'), array_keys($arFieldsElement));
		
		$arFilter = array('IBLOCK_ID'=>$IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N');
		foreach($arUid as $v)
		{
			if(!$v['substring'])
			{
				if(is_array($v['valUid'])) $arSubfilter = array_map('trim', $v['valUid']);
				else 
				{
					$arSubfilter = array(trim($v['valUid']));
					if(trim($v['valUid']) != $v['valUid2'])
					{
						$arSubfilter[] = trim($v['valUid2']);
						if(strlen($v['valUid2']) != strlen(trim($v['valUid2'])))
						{
							$arSubfilter[] = $v['valUid2'];
						}
					}
					if(strlen($v['valUid']) != strlen(trim($v['valUid'])))
					{
						$arSubfilter[] = $v['valUid'];
					}
				}
				
				if(count($arSubfilter) == 1)
				{
					$arSubfilter = $arSubfilter[0];
				}
				$arFilter['='.$v['uid']] = $arSubfilter;
			}
			else
			{
				$arFilter['%'.$v['uid']] = trim($v['valUid']);
			}
		}
		
		if(!empty($arFieldsIpropTemp))
		{
			$arFieldsElement['IPROPERTY_TEMPLATES'] = $arFieldsIpropTemp;
		}
		
		$elemName = '';
		//$dbRes = \CIblockElement::GetList(array(), $arFilter, false, false, $arKeys);
		$dbRes = \Bitrix\EsolImportxml\DataManager\IblockElement::GetList($arFilter, $arKeys);
		while($arElement = $dbRes->Fetch())
		{
			if($this->params['ONLY_DELETE_MODE']=='Y')
			{
				$ID = $arElement['ID'];
				$this->BeforeElementDelete($ID, $IBLOCK_ID);
				\CIblockElement::Delete($ID);
				$this->AfterElementDelete($ID, $IBLOCK_ID);
				unset($ID);
				continue;
			}
			
			$ID = $arElement['ID'];
			$arFieldsProps2 = $arFieldsProps;
			$arFieldsElement2 = $arFieldsElement;
			$arFieldsSections2 = $arFieldsSections;
			$arFieldsProduct2 = $arFieldsProduct;
			$arFieldsPrices2 = $arFieldsPrices;
			$arFieldsProductStores2 = $arFieldsProductStores;
			if($this->conv->SetElementId($ID)
				&& $this->conv->UpdateProperties($arFieldsProps2, $ID)!==false
				&& $this->conv->UpdateElementFields($arFieldsElement2, $ID)!==false
				&& $this->conv->UpdateElementSectionFields($arFieldsSections2, $ID)!==false
				&& $this->conv->UpdateProduct($arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $ID)!==false
				&& $this->conv->SetElementId(0))
			{
				$this->BeforeElementSave($ID, 'update');
				if($this->params['ONLY_CREATE_MODE_ELEMENT']!='Y')
				{
					$this->UnsetUidFields($arFieldsElement2, $arFieldsProps2, $this->params['ELEMENT_UID']);
					if(!empty($this->fieldOnlyNew))
					{
						$this->UnsetExcessSectionFields($this->fieldOnlyNew, $arFieldsSections2, $arFieldsElement2);
					}
					
					$arElementSections = false;
					if($this->params['ELEMENT_ADD_NEW_SECTIONS']=='Y')
					{
						$arElementSections = $this->GetElementSections($ID);
						if(!is_array($arElementSections)) $arElementSections = array();
						if(!is_array($arFieldsElement2['IBLOCK_SECTION'])) $arFieldsElement2['IBLOCK_SECTION'] = array();
						$arFieldsElement2['IBLOCK_SECTION'] = array_merge($arFieldsElement2['IBLOCK_SECTION'], $arElementSections);
					}
					$this->GetSections($arFieldsElement2, $IBLOCK_ID, $SECTION_ID, $arFieldsSections2);
					if($this->params['NOT_LOAD_ELEMENTS_WO_SECTION']=='Y' 
						&& (!isset($arFieldsElement2['IBLOCK_SECTION']) || empty($arFieldsElement2['IBLOCK_SECTION']))) continue;
					
					foreach($arElement as $k=>$v)
					{
						$action = $this->fieldSettings['IE_'.$k]['LOADING_MODE'];
						if($action)
						{
							if($action=='ADD_BEFORE') $arFieldsElement2[$k] = $arFieldsElement2[$k].$v;
							elseif($action=='ADD_AFTER') $arFieldsElement2[$k] = $v.$arFieldsElement2[$k];
						}
					}
					
					if(!empty($this->fieldOnlyNew))
					{
						$this->UnsetExcessFields($this->fieldOnlyNew, $arFieldsElement2, $arFieldsProps2, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $arFieldsProductDiscount);
					}
					
					$this->RemoveProperties($ID, $IBLOCK_ID);
					$this->SaveProperties($ID, $IBLOCK_ID, $arFieldsProps2);
					$this->SaveProduct($ID, $IBLOCK_ID, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2);
					
					$el = new \CIblockElement();
					if($this->UpdateElement($el, $ID, $IBLOCK_ID, $arFieldsElement2, $arElement, $arElementSections))
					{
						//$this->SetTimeBegin($ID);
					}
					else
					{
						$this->stepparams['error_line']++;
						$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_UPDATE_ELEMENT_ERROR"), $el->LAST_ERROR, 'ID = '.$ID);
					}
					
					$elemName = $arElement['NAME'];
					$this->SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $elemName);
					$this->stepparams['element_updated_line']++;
				}
			}
			
			$this->SaveElementId($ID);
			if($elemName && !$arFieldsElement2['NAME']) $arFieldsElement2['NAME'] = $elemName;
			$this->SaveRecordAfter($ID, $IBLOCK_ID, $arItem, $arFieldsElement2);
		}
		
		$allowCreate = (bool)(\Bitrix\EsolImportxml\DataManager\IblockElement::SelectedRowsCount($dbRes)==0 && $this->params['ONLY_DELETE_MODE']!='Y');
		if($allowCreate && $this->params['SEARCH_OFFERS_WO_PRODUCTS']=='Y')
		{
			$res = $this->SaveSKUWithGenerate(0, '', $IBLOCK_ID, $arItem);
			if($res==='timesup') return false;
			if($res===true) $allowCreate = false;
		}
		
		if($allowCreate)
		{
			if($this->params['ONLY_UPDATE_MODE_ELEMENT']!='Y')
			{
				$this->UnsetUidFields($arFieldsElement, $arFieldsProps, $this->params['ELEMENT_UID'], true);
				if(isset($arFieldsElement['ID']))
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NEW_ELEMENT_WITH_ID"), $arFieldsElement['ID'], '');
					return false;
				}
				if(strlen($arFieldsElement['NAME'])==0)
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NOT_SET_FIELD"), $arFieldsDef['element']['items']['IE_NAME']).($arFieldsElement['XML_ID'] ? ' ('.$arFieldsElement['XML_ID'].')' : '');
					return false;
				}
				if($this->params['ELEMENT_NEW_DEACTIVATE']=='Y')
				{
					$arFieldsElement['ACTIVE'] = 'N';
				}
				elseif(!$arFieldsElement['ACTIVE'])
				{
					$arFieldsElement['ACTIVE'] = 'Y';
				}
				$arFieldsElement['IBLOCK_ID'] = $IBLOCK_ID;
				$this->PrepareElementPictures($arFieldsElement);
				$this->GetSections($arFieldsElement, $IBLOCK_ID, $SECTION_ID, $arFieldsSections);
				if($this->params['NOT_LOAD_ELEMENTS_WO_SECTION']=='Y' 
					&& (!isset($arFieldsElement['IBLOCK_SECTION']) || empty($arFieldsElement['IBLOCK_SECTION'])))
				{
					$this->stepparams['correct_line']++;
					return false;
				}
				$this->GetDefaultElementFields($arFieldsElement, $iblockFields);
				$el = new \CIblockElement();
				$ID = $el->Add($arFieldsElement, false, true, true);
				
				if($ID)
				{
					$this->BeforeElementSave($ID, 'add');
					$this->logger->AddElementChanges('IE_', $arFieldsElement);
					$this->AddTagIblock($IBLOCK_ID);
					//$this->SetTimeBegin($ID);
					$this->SaveProperties($ID, $IBLOCK_ID, $arFieldsProps, true);
					$this->PrepareProductAdd($arFieldsProduct, $ID, $IBLOCK_ID);
					$this->SaveProduct($ID, $IBLOCK_ID, $arFieldsProduct, $arFieldsPrices, $arFieldsProductStores);
					$this->SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $arFieldsElement['NAME']);
					//if(!empty($arFieldsElement['IPROPERTY_TEMPLATES']) || $arFieldsElement['NAME'])
					if(true)
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($IBLOCK_ID, $ID);
						$ipropValues->clearValues();
					}
					$this->stepparams['element_added_line']++;
					$this->SaveElementId($ID);
					$this->SaveRecordAfter($ID, $IBLOCK_ID, $arItem, $arFieldsElement);
				}
				else
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_ADD_ELEMENT_ERROR"), $el->LAST_ERROR, $arFieldsElement['NAME']);
					return false;
				}
			}
			else
			{
				$this->logger->SaveElementNotFound($arFilter);
			}
		}
		
		$this->stepparams['correct_line']++;
		$this->SaveStatusImport();
		$this->RemoveTmpImageDirs();
	}
	
	public function SaveRecordAfter($ID, $IBLOCK_ID, $arItem, $arFieldsElement)
	{
		if(!$ID) return;		
		$arFieldsElement['ID'] = $ID;
		$this->stepparams['currentelement'] = $arFieldsElement;
		$this->stepparams['currentelementitem'] = $arItem;
		if($this->params['ELEMENT_UID_SKU']) 
		{
			$isSaved = false;
			if($this->skuInElement)
			{				
				$this->currentParentXmlObj = $this->currentXmlObj;
				$xpath = trim(substr($this->params['GROUPS']['OFFER'], strlen($this->params['GROUPS']['ELEMENT'])), '/');
				//$this->xmlOffers = $this->currentParentXmlObj->xpath($xpath);
				$this->xmlOffers = $this->Xpath($this->currentParentXmlObj, $xpath);
				$this->xmlOfferCurrentRow = 0;
				while($arOfferItem = $this->GetNextOffer($ID, $arItem))
				{
					foreach($this->arSkuAddFields as $key)
					{
						if(array_key_exists($key, $arOfferItem)) continue;
						$arOfferItem[$key] = $arItem[$key];
						$arOfferItem['~'.$key] = $arItem['~'.$key];
					}
					foreach($this->arSkuDuplicateFields as $key=>$key2)
					{
						if(array_key_exists($key2, $arOfferItem) || array_key_exists($key, $arOfferItem)) continue;
						$arOfferItem[$key] = $arOfferItem[$key2];
						$arOfferItem['~'.$key] = $arOfferItem['~'.$key2];
					}
					$this->SaveSKUWithGenerate($ID, $arFieldsElement['NAME'], $IBLOCK_ID, $arOfferItem);
				}
				$this->currentXmlObj = $this->currentParentXmlObj;
				if($this->xmlOfferCurrentRow > 0) $isSaved = true;
				elseif(empty($this->arSkuDuplicateFields)) $isSaved = true;
				else
				{
					foreach($this->arSkuDuplicateFields as $key=>$key2)
					{
						$arItem[$key2] = $arItem[$key];
						$arItem['~'.$key2] = $arItem['~'.$key];
					}
				}
			}
			if(!$isSaved)
			{
				$this->SaveSKUWithGenerate($ID, $arFieldsElement['NAME'], $IBLOCK_ID, $arItem);
			}
		}
		
		if($this->params['ONAFTERSAVE_HANDLER'])
		{
			$this->ExecuteOnAfterSaveHandler($this->params['ONAFTERSAVE_HANDLER'], $ID);
		}
		
		if($this->params['REMOVE_COMPOSITE_CACHE_PART']=='Y')
		{
			if($arElement = \CIblockElement::GetList(array(), array('ID'=>$ID), false, false, array('DETAIL_PAGE_URL'))->GetNext())
			{
				$this->ClearCompositeCache($arElement['DETAIL_PAGE_URL']);
			}
		}
	}
	
	public function AddGroupsProperties(&$arFieldsProps, &$arFieldsPropsOrig, $IBLOCK_ID, $isOffer=false)
	{
		if((!$isOffer && $this->propertyInElement) || ($isOffer && $this->propertyInOffer))
		{
			$xmlPartObjects = $this->xmlPartObjects;
			$propsDef = $this->GetIblockProperties($this->params['IBLOCK_ID']);
			$this->currentParentXmlObj = $this->currentXmlObj;
			$xpath = $this->params['GROUPS']['PROPERTY'];
			if($isOffer)
			{
				$xpath = $this->ReplaceXpath($xpath);
			}
			$groupXpath = $xpath;
			$xpath = trim(substr($xpath, strlen($this->params['GROUPS']['ELEMENT'])), '/');
			$this->parentXpath = $this->xpath;
			$this->xpath = '/'.$this->params['GROUPS']['PROPERTY'];
			//$this->xmlProperties = $this->currentParentXmlObj->xpath($xpath);
			$this->xmlProperties = $this->Xpath($this->currentParentXmlObj, $xpath);
			$this->xmlPropertiesCurrentRow = 0;
			while($arProperty = $this->GetNextProperty($groupXpath))
			{
				$arPropertyFields = array();
				$tmpID = false;
				foreach($this->params['FIELDS'] as $key=>$fieldFull)
				{
					list($xpath, $field) = explode(';', $fieldFull, 2);
					if(strpos($field, 'PROPERTY_')!==0) continue;
					
					$value = $arProperty[$key];
					if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arProperty['~'.$key];
					$origValue = $arProperty['~'.$key];
					
					$conversions = $this->fparams[$key]['CONVERSION'];
					if(!empty($conversions))
					{
						if(is_array($value))
						{
							foreach($value as $k2=>$v2)
							{
								$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arProperty);
								$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arProperty);
							}
						}
						else
						{
							$value = $this->ApplyConversions($value, $conversions, $arProperty);
							$origValue = $this->ApplyConversions($origValue, $conversions, $arProperty);
						}
						if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
					}
					
					$fieldName = substr($field, 9);
					if($fieldName=='TMP_ID') $tmpID = $value;
					else $arPropertyFields[$fieldName] = $value;
				}

				$arProp = false;
				if($tmpID!==false && isset($this->propertyIds[$tmpID]) && $this->propertyIds[$tmpID]) $arProp = $this->GetIblockPropertyById($this->propertyIds[$tmpID], $IBLOCK_ID, true);
				elseif($arPropertyFields['NAME']) $arProp = $this->GetIblockPropertyByName($arPropertyFields['NAME'], $IBLOCK_ID, true);
				elseif($arPropertyFields['CODE']) $arProp = $this->GetIblockPropertyByCode($arPropertyFields['CODE'], $IBLOCK_ID);
			
				if(is_array($arProp) && isset($arProp['ID']))
				{
					$fieldName = $arProp['ID'];
					$currentPropDef = $propsDef[$fieldName];
					$value = $origValue = $arPropertyFields['VALUE'];
					if($arProp['PROPERTY_TYPE']=='E' && !isset($this->fieldSettings['IP_PROP'.$fieldName]['REL_ELEMENT_FIELD'])) $this->fieldSettings['IP_PROP'.$fieldName]['REL_ELEMENT_FIELD'] = 'IE_NAME';
					
					$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $currentPropDef, $fieldName, $value, $origValue, $this->params['ELEMENT_UID']);
					
					if(isset($arPropertyFields['DESCRIPTION']))
					{
						if(!isset($arFieldsProps[$fieldName.'_DESCRIPTION']))
						{
							$arFieldsProps[$fieldName.'_DESCRIPTION'] = $arFieldsPropsOrig[$fieldName.'_DESCRIPTION'] = $arPropertyFields['DESCRIPTION'];
						}
						else
						{
							if(!is_array($arFieldsProps[$fieldName.'_DESCRIPTION']))
							{
								$arFieldsProps[$fieldName.'_DESCRIPTION'] = array($arFieldsProps[$fieldName.'_DESCRIPTION']);
								$arFieldsPropsOrig[$fieldName.'_DESCRIPTION'] = array($arFieldsPropsOrig[$fieldName.'_DESCRIPTION']);
							}
							$arFieldsProps[$fieldName.'_DESCRIPTION'][] = $arPropertyFields['DESCRIPTION'];
							$arFieldsPropsOrig[$fieldName.'_DESCRIPTION'][] = $arPropertyFields['DESCRIPTION'];
						}
					}
					
				}
			}
			$this->xpath = $this->parentXpath;
			$this->parentXpath = '';
			$this->currentXmlObj = $this->currentParentXmlObj;
			$this->xmlPartObjects = $xmlPartObjects;
		}
	}
	
	public function UpdateElement(&$el, $ID, $IBLOCK_ID, $arFieldsElement, $arElement=array(), $arElementSections=array())
	{
		if(!empty($arFieldsElement))
		{
			$this->PrepareElementPictures($arFieldsElement, $isOffer);

			if($this->params['ELEMENT_NOT_CHANGE_SECTIONS']=='Y')
			{
				unset($arFieldsElement['IBLOCK_SECTION'], $arFieldsElement['IBLOCK_SECTION_ID']);
			}
			elseif(!isset($arFieldsElement['IBLOCK_SECTION_ID']) && isset($arFieldsElement['IBLOCK_SECTION']) && is_array($arFieldsElement['IBLOCK_SECTION']) && count($arFieldsElement['IBLOCK_SECTION']) > 0)
			{
				reset($arFieldsElement['IBLOCK_SECTION']);
				$arFieldsElement['IBLOCK_SECTION_ID'] = current($arFieldsElement['IBLOCK_SECTION']);
			}
			foreach($arFieldsElement as $k=>$v)
			{
				if($k=='IBLOCK_SECTION' && is_array($v))
				{
					if(!is_array($arElementSections)) $arElementSections = $this->GetElementSections($ID);
					if(count($v)==count($arElementSections) && count(array_diff($v, $arElementSections))==0)
					{
						unset($arFieldsElement[$k]);
					}
				}
				elseif($k=='PREVIEW_PICTURE' || $k=='DETAIL_PICTURE')
				{
					if(!$this->IsChangedImage($arElement[$k], $arFieldsElement[$k]))
					{
						unset($arFieldsElement[$k]);
					}
				}
				elseif($v==$arElement[$k])
				{
					unset($arFieldsElement[$k]);
				}
			}
			
			if(isset($arFieldsElement['DETAIL_PICTURE']) && is_array($arFieldsElement['DETAIL_PICTURE']) && empty($arFieldsElement['DETAIL_PICTURE'])) unset($arFieldsElement['DETAIL_PICTURE']);
			if(isset($arFieldsElement['DETAIL_PICTURE']))
			{
				if(is_array($arFieldsElement['DETAIL_PICTURE']) && (!isset($arFieldsElement['PREVIEW_PICTURE']) || !is_array($arFieldsElement['PREVIEW_PICTURE']))) $arFieldsElement['PREVIEW_PICTURE'] = array();
			}
			elseif(isset($arFieldsElement['PREVIEW_PICTURE']) && is_array($arFieldsElement['PREVIEW_PICTURE']) && empty($arFieldsElement['PREVIEW_PICTURE'])) unset($arFieldsElement['PREVIEW_PICTURE']);
		}
		
		if(empty($arFieldsElement) && $this->params['ELEMENT_NOT_UPDATE_WO_CHANGES']=='Y') return true;
		if($el->Update($ID, $arFieldsElement, false, true, true))
		{
			$this->logger->AddElementChanges('IE_', $arFieldsElement, $arElement);
			$this->AddTagIblock($IBLOCK_ID);
			//if(!empty($arFieldsElement['IPROPERTY_TEMPLATES']) || $arFieldsElement['NAME'])
			if(true)
			{
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($IBLOCK_ID, $ID);
				$ipropValues->clearValues();
			}
			return true;
		}
		return false;
	}
	
	public function PrepareFieldsBeforeConv(&$value, &$origValue, $field, $arParams)
	{
		if($field=='IE_SECTION_PATH' && $this->useSectionPathByLink)
		{
			$tmpSep = ($arParams['SECTION_PATH_SEPARATOR'] ? $arParams['SECTION_PATH_SEPARATOR'] : '/');
			$value = $origValue = $this->GetSectionPathByLink($value, $tmpSep);
		}
	}
	
	public function PrepareElementFields(&$value, &$origValue, $field, $arParams)
	{
		if($field=='IE_CREATED_BY')
		{
			if($arParams['USER_UID'] && $arParams['USER_UID']!='ID')
			{
				$arFilter = array();
				if($arParams['USER_UID']=='LOGIN')
				{
					$arFilter['LOGIN_EQUAL'] = $value;
				}
				elseif($arParams['USER_UID']=='XML_ID')
				{
					$arFilter[$arParams['USER_UID']] = $value;
				}
				else
				{
					$arFilter['='.$arParams['USER_UID']] = $value;
				}
				$dbRes = \CUser::GetList(($by='ID'), ($order='ASC'), $arFilter, array('FIELDS'=>array('ID')));
				if($arUser = $dbRes->Fetch())
				{
					$value = $origValue = $arUser['ID'];
				}
			}
		}
	}
	
	public function PrepareElementPictures(&$arFieldsElement, $isOffer=false)
	{
		$arPictures = array('PREVIEW_PICTURE', 'DETAIL_PICTURE');
		foreach($arPictures as $picName)
		{
			if($arFieldsElement[$picName])
			{
				$val = $arFieldsElement[$picName];
				$arFile = $this->GetFileArray($val, array(), array('FILETYPE'=>'IMAGE'));
				if(empty($arFile) && strpos($val, $this->params['ELEMENT_MULTIPLE_SEPARATOR'])!==false)
				{
					$arVals = array_diff(array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $val)), array(''));
					if(count($arVals) > 0 && ($val = current($arVals)))
					{
						$arFile = $this->GetFileArray($val, array(), array('FILETYPE'=>'IMAGE'));
					}
				}
				$arFieldsElement[$picName] = $arFile;
			}
			if(isset($arFieldsElement[$picName.'_DESCRIPTION']))
			{
				$arFieldsElement[$picName]['description'] = $arFieldsElement[$picName.'_DESCRIPTION'];
				unset($arFieldsElement[$picName.'_DESCRIPTION']);
			}
		}
		if((isset($arFieldsElement['DETAIL_PICTURE']) && is_array($arFieldsElement['DETAIL_PICTURE'])) && (!isset($arFieldsElement['PREVIEW_PICTURE']) || !is_array($arFieldsElement['PREVIEW_PICTURE'])))
		{
			$arFieldsElement['PREVIEW_PICTURE'] = array();
		}
		
		$arTexts = array('PREVIEW_TEXT', 'DETAIL_TEXT');
		foreach($arTexts as $keyText)
		{
			if($arFieldsElement[$keyText])
			{
				if($this->fieldSettings[($isOffer ? 'OFFER_' : '').'IE_'.$keyText]['LOAD_BY_EXTLINK']=='Y')
				{
					$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
					$path = $arFieldsElement[$keyText];
					$arUrl = parse_url($path);
					$res = $client->get($path);
					$hct = ToLower($client->getHeaders()->get('content-type'));
					$siteEncoding = $fileEncoding = \Bitrix\EsolImportxml\Utils::getSiteEncoding();
					if(preg_match('/charset=(.+)(;|$)/Uis', $hct, $m))
					{
						$fileEncoding = ToLower(trim($m[1]));
					}
					if(class_exists('\DOMDocument') && $arUrl['fragment'])
					{
						if($fileEncoding && !preg_match('/<meta[^>]*http\-equiv=[\'"]content\-type[\'"]/Uis', $res) && !preg_match('/<meta[^>]*charset=/Uis', $res) && ($endHeadPos = stripos($res, '</head>')))
						{
							$res = substr($res, 0, $endHeadPos).'<meta http-equiv="Content-Type" content="text/html; charset='.$fileEncoding.'">'.substr($res, $endHeadPos);
						}
						$doc = new \DOMDocument();
						$doc->preserveWhiteSpace = false;
						$doc->formatOutput = true;
						$doc->loadHTML($res);
						$node = $doc;
						$arParts = preg_split('/\s+/', $arUrl['fragment']);
						$i = 0;
						while(isset($arParts[$i]) && ($node instanceOf \DOMDocument || $node instanceOf \DOMElement))
						{
							$part = $arParts[$i];
							$tagName = (preg_match('/^([^#\.]+)([#\.].*$|$)/', $part, $m) ? $m[1] : '');
							$tagId = (preg_match('/^[^#]*#([^#\.]+)([#\.].*$|$)/', $part, $m) ? $m[1] : '');
							$arClasses = array_diff(explode('.', (preg_match('/^[^\.]*\.([^#]+)([#\.].*$|$)/', $part, $m) ? $m[1] : '')), array(''));
							if($tagName)
							{
								$nodes = $node->getElementsByTagName($tagName);
								if($tagId || !empty($arClasses))
								{
									$find = false;
									$key = 0;
									while(!$find && $key<$nodes->length)
									{
										$node1 = $nodes->item($key);
										$subfind = true;
										if($tagId && $node1->getAttribute('id')!=$tagId) $subfind = false;
										foreach($arClasses as $className)
										{
											if($className && !preg_match('/(^|\s)'.preg_quote($className, '/').'(\s|$)/is', $node1->getAttribute('class'))) $subfind = false;
										}
										$find = $subfind;
										if(!$find) $key++;
									}
									if($find) $node = $nodes->item($key);
									else $node = null;
								}
								else
								{
									$node = $nodes->item(0);
								}
							}
							$i++;
						}
						if($node instanceOf \DOMElement)
						{
							$innerHTML = '';
							$children = $node->childNodes;
							foreach($children as $child)
							{
								$innerHTML .= $child->ownerDocument->saveXML($child);
							}
							if(strlen($innerHTML)==0 && $node->nodeValue) $innerHTML = $node->nodeValue;
							$res = $innerHTML;
						}
						else
						{
							$res = '';
						}
						if($res && $siteEncoding!='utf-8')
						{
							$res = \Bitrix\Main\Text\Encoding::convertEncoding($res, 'utf-8', $siteEncoding);
						}
					}
					elseif($siteEncoding!=$fileEncoding)
					{
						$res = \Bitrix\Main\Text\Encoding::convertEncoding($res, $fileEncoding, $siteEncoding);
					}
					$arFieldsElement[$keyText] = $res;
				}
				else
				{
					$textFile = $_SERVER["DOCUMENT_ROOT"].$arFieldsElement[$keyText];
					if(file_exists($textFile) && is_file($textFile) && is_readable($textFile))
					{
						$arFieldsElement[$keyText] = file_get_contents($textFile);
					}
				}
			}
		}
	}
	
	public function SaveStatusImport($end = false)
	{
		if($this->procfile)
		{
			$writeParams = array_merge($this->stepparams, array(
				'xmlCurrentRow' => intval($this->xmlCurrentRow),
				'xmlSectionCurrentRow' => intval($this->xmlSectionCurrentRow),
				'sectionIds' => $this->sectionIds
			));
			$writeParams['action'] = ($end ? 'finish' : 'continue');
			file_put_contents($this->procfile, \CUtil::PhpToJSObject($writeParams));
		}
	}
	
	public function SetSkuMode($isSku, $ID=0, $IBLOCK_ID=0)
	{
		if($isSku)
		{
			$this->conv->SetSkuMode(true, $this->GetCachedOfferIblock($IBLOCK_ID), $ID);
			$this->offerParentId = $ID;
		}
		else
		{
			$this->conv->SetSkuMode(false);
			$this->offerParentId = null;
		}
	}
	
	public function SaveSKUWithGenerate($ID, $NAME, $IBLOCK_ID, $arItem)
	{
		$ret = false;
		$this->SetSkuMode(true, $ID, $IBLOCK_ID);
		if(!empty($this->fieldsForSkuGen))
		{
			$convertedFields = array();
			$filedList = $this->params['FIELDS'];
			$arItemParams = array();
			foreach($this->fieldsForSkuGen as $key)
			{
				$conversions = $this->fparams[$key]['CONVERSION'];
				$arItem['~~'.$key] = $arItem[$key];
				if(is_array($arItem[$key]))
				{
					$arItemField = array();
					foreach($arItem[$key] as $k=>$v)
					{
						$val = $this->ApplyConversions($v, $conversions, $arItem, array('KEY'=>$key,'INDEX'=>$k));	
						if(is_array($val))
						{
							foreach($val as $subval)
							{
								if(!in_array($subval, $arItemField)) $arItemField[] = $subval;
							}
						}
						else
						{
							if(!in_array($val, $arItemField)) $arItemField[] = $val;
						}
					}
					$arItemParams[$key] = $arItem[$key] = $arItemField;
				}
				else
				{
					$arItem[$key] = $this->ApplyConversions($arItem[$key], $conversions, $arItem, array('KEY'=>$key,'INDEX'=>0));
					$arItemParams[$key] = array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arItem[$key]));
				}
				$convertedFields[] = $key;
			}
			$arItemSKUParams = array();
			$this->GenerateSKUParamsRecursion($arItemSKUParams, $arItemParams);
			
			$extraFields = array();
			foreach($filedList as $key=>$fieldFull)
			{
				if(in_array($key, $this->fieldsForSkuGen)) continue;
				list($xpath, $field) = explode(';', $fieldFull, 2);
				$conversions = $this->fparams[$key]['CONVERSION'];
				$val = $arItem[$key];
				if(!is_array($val)) $val = $this->ApplyConversions($val, $conversions, $arItem);
				if(preg_match('/^OFFER_(ICAT_QUANTITY|ICAT_PURCHASING_PRICE|ICAT_PRICE\d+_PRICE|ICAT_STORE\d+_AMOUNT|ICAT_QUANTITY_TRACE|ICAT_CAN_BUY_ZERO|ICAT_NEGATIVE_AMOUNT_TRACE|ICAT_SUBSCRIBE|IE_ACTIVE)$/', $field)
					|| (is_array($val) && count($val)==count($arItemSKUParams)))
				{
					$val = $arItem[$key];
					$isConv = false;
					if(!is_array($val))
					{
						$val = $this->ApplyConversions($val, $conversions, $arItem);
						$isConv = true;
					}
					if(is_array($val) || strpos($val, $this->params['ELEMENT_MULTIPLE_SEPARATOR'])!==false)
					{
						$arItem['~~'.$key] = $arItem[$key];
						if(is_array($val))
						{
							$arItem[$key] = array();
							foreach($val as $k=>$v)
							{
								if($isConv) $arItem[$key][$k] = $v;
								else $arItem[$key][$k] = $this->ApplyConversions($v, $conversions, $arItem);
							}
							$extraFields[$key] = $arItem[$key];
							if(isset($arItem['~'.$key])) $extraFields['~'.$key] = $arItem['~'.$key];
						}
						else
						{
							$arItem[$key] = $val;	
							$extraFields[$key] = array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arItem[$key]));
							if(isset($arItem['~'.$key])) array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arItem['~'.$key]));
						}
						$convertedFields[] = $key;
					}
				}
			}
			
			foreach($arItemSKUParams as $k=>$v)
			{
				$arSubItem = $arItem;
				foreach($v as $k2=>$v2) $arSubItem[$k2] = $v2;
				foreach($extraFields as $k2=>$v2)
				{
					if(isset($extraFields[$k2][$k])) $arSubItem[$k2] = $extraFields[$k2][$k];
					else $arSubItem[$k2] = current($extraFields[$k2]);
				}
				$ret = (bool)($this->SaveSKU($ID, $NAME, $IBLOCK_ID, $arSubItem, $convertedFields) || $ret);
			}
		}
		else
		{
			$ret = $this->SaveSKU($ID, $NAME, $IBLOCK_ID, $arItem);
		}
		if($ret)
		{
			\CIBlockElement::UpdateSearch($ID, true);
			if(class_exists('\Bitrix\Iblock\PropertyIndex\Manager'))
			{
				\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ID);
			}
		}
		$this->SetSkuMode(false);
		return $ret;
	}
	
	public function GenerateSKUParamsRecursion(&$arItemSKUParams, $arItemParams, $arSubItem = array())
	{
		if(!empty($arItemParams))
		{
			$arKey = array_keys($arItemParams);
			$key = $arKey[0];
			$arCurParams = $arItemParams[$key];
			unset($arItemParams[$key]);
			foreach($arCurParams as $k=>$v)
			{
				$arSubItem[$key] = $v;
				$arSubItem['~'.$key] = $v;
				$this->GenerateSKUParamsRecursion($arItemSKUParams, $arItemParams, $arSubItem);
			}
		}
		else
		{
			$arItemSKUParams[] = $arSubItem;
		}
	}
	
	public function SaveSKU($ID, $NAME, $IBLOCK_ID, $arItem, $convertedFields=array())
	{
		if(!($arOfferIblock = $this->GetCachedOfferIblock($IBLOCK_ID))) return false;
		$OFFERS_IBLOCK_ID = $arOfferIblock['OFFERS_IBLOCK_ID'];
		$OFFERS_PROPERTY_ID = $arOfferIblock['OFFERS_PROPERTY_ID'];
		
		$propsDef = $this->GetIblockProperties($OFFERS_IBLOCK_ID);

		$iblockFields = $this->GetIblockFields($OFFERS_IBLOCK_ID);
		
		$arFieldsElement = array();
		$arFieldsElementOrig = array();
		$arFieldsPrices = array();
		$arFieldsProduct = array();
		$arFieldsProductStores = array();
		$arFieldsProductDiscount = array();
		if($ID > 0)
		{
			$arFieldsProps = array($OFFERS_PROPERTY_ID => $ID);
			$arFieldsPropsOrig = array($OFFERS_PROPERTY_ID => $ID);
		}
		else
		{
			$arFieldsProps = array();
			$arFieldsPropsOrig = array();
		}
		$arFieldsIpropTemp = array();
		$arFieldsForSkuGen = array_map('strval', $this->fieldsForSkuGen);
		//foreach($filedList as $key=>$field)
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);

			if(strpos($field, 'OFFER_')!==0) continue;
			$conversions = $this->fparams[$key]['CONVERSION'];
			$field = substr($field, 6);
			
			$k = $key;
			if(strpos($k, '_')!==false) $k = substr($k, 0, strpos($k, '_'));
			if(!array_key_exists($k, $arItem)) continue;
			$value = $arItem[$k];
			if($this->fparams[$key]['NOT_TRIM']=='Y') $value = $arItem['~'.$k];
			$origValue = $arItem['~'.$k];

			$this->PrepareFieldsBeforeConv($value, $origValue, $field, $this->fparams[$key]);
			if(!empty($conversions) && !in_array($key, $convertedFields))
			{
				if(is_array($value))
				{
					foreach($value as $k2=>$v2)
					{
						$value[$k2] = $this->ApplyConversions($value[$k2], $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field, 'INDEX'=>$k2), $iblockFields);
						$origValue[$k2] = $this->ApplyConversions($origValue[$k2], $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field, 'INDEX'=>$k2), $iblockFields);
					}
				}
				else
				{
					$value = $this->ApplyConversions($value, $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field), $iblockFields);
					$origValue = $this->ApplyConversions($origValue, $conversions, $arItem, array('KEY'=>$key, 'NAME'=>$field), $iblockFields);
				}
				if($value===false || (is_array($value) && count(array_diff($value, array(false)))==0)) continue;
			}
			$this->PrepareElementFields($value, $origValue, $field, $this->fparams[$key]);
			
			if(strpos($field, 'IE_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						$arFieldsElement[$adata[0]] = $adata[1];
					}
				}
				$arFieldsElement[substr($field, 3)] = $value;
				$arFieldsElementOrig[substr($field, 3)] = $origValue;
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$val = $value;
				if(substr($field, -6)=='_PRICE')
				{
					if(!in_array($val, array('', '-')))
					{
						//$val = $this->GetFloatVal($val);
						$val = $this->ApplyMargins($val, $this->fparams[$key]);
					}
				}
				elseif(substr($field, -6)=='_EXTRA')
				{
					$val = $this->GetFloatVal($val);
				}
				
				$arPrice = explode('_', substr($field, 10), 2);
				$pkey = $arPrice[1];
				if($pkey=='PRICE' && $this->fparams[$key]['PRICE_USE_EXT']=='Y')
				{
					$pkey = $pkey.'|QUANTITY_FROM='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_FROM']).'|QUANTITY_TO='.$this->GetFloatVal($this->fparams[$key]['PRICE_QUANTITY_TO']);
				}
				$arFieldsPrices[$arPrice[0]][$pkey] = $val;
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				$arFieldsProductStores[$arStore[0]][$arStore[1]] = $value;
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						$arFieldsProductDiscount[$adata[0]] = $adata[1];
					}
				}
				$arFieldsProductDiscount[substr($field, 14)] = $value;
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				$val = $value;
				if($field=='ICAT_PURCHASING_PRICE')
				{
					if($val=='') continue;
					$val = $this->GetFloatVal($val);
				}
				elseif($field=='ICAT_MEASURE')
				{
					$val = $this->GetMeasureByStr($val);
				}
				$arFieldsProduct[substr($field, 5)] = $val;
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				$fieldName = substr($field, 7);
				$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $propsDef[$fieldName], $fieldName, $value, $origValue);
			}
			elseif(strpos($field, 'IP_LIST_PROPS')===0)
			{
				$this->GetPropList($arFieldsProps, $arFieldsPropsOrig, $this->fparams[$key], $OFFERS_IBLOCK_ID, $value);
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				$fieldName = substr($field, 11);
				$arFieldsIpropTemp[$fieldName] = $value;
			}
		}
		
		$this->AddGroupsProperties($arFieldsProps, $arFieldsPropsOrig, $OFFERS_IBLOCK_ID, true);

		$arUid = array();
		if(!is_array($this->params['ELEMENT_UID_SKU'])) $this->params['ELEMENT_UID_SKU'] = array($this->params['ELEMENT_UID_SKU']);
		if(!in_array('OFFER_IP_PROP'.$OFFERS_PROPERTY_ID, $this->params['ELEMENT_UID_SKU'])) $this->params['ELEMENT_UID_SKU'][] = 'OFFER_IP_PROP'.$OFFERS_PROPERTY_ID;
		foreach($this->params['ELEMENT_UID_SKU'] as $tuid)
		{
			$tuid = substr($tuid, 6);
			$uid = $valUid = $valUid2 = '';
			if(strpos($tuid, 'IE_')===0)
			{
				$uid = substr($tuid, 3);
				if(strpos($uid, '|')!==false) $uid = current(explode('|', $uid));
				$valUid = $arFieldsElementOrig[$uid];
				$valUid2 = $arFieldsElement[$uid];
			}
			elseif(strpos($tuid, 'IP_PROP')===0)
			{
				$uid = substr($tuid, 7);
				$valUid = $arFieldsPropsOrig[$uid];
				$valUid2 = $arFieldsProps[$uid];
				if($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$uid = 'PROPERTY_'.$uid.'_VALUE';
				}
				elseif($propsDef[$uid]['PROPERTY_TYPE']=='N' && !is_numeric($valUid))
				{
					$valUid = $valUid2 = '';
				}
				else
				{
					if($propsDef[$uid]['PROPERTY_TYPE']=='S' && $propsDef[$uid]['USER_TYPE']=='directory')
					{
						$valUid = $this->GetHighloadBlockValue($propsDef[$uid], $valUid);
						$valUid2 = $this->GetHighloadBlockValue($propsDef[$uid], $valUid2);
					}
					elseif($propsDef[$uid]['PROPERTY_TYPE']=='E')
					{
						$valUid = $this->GetIblockElementValue($propsDef[$uid], $valUid, $this->fieldSettings['OFFER_'.$tuid]);
						$valUid2 = $this->GetIblockElementValue($propsDef[$uid], $valUid2, $this->fieldSettings['OFFER_'.$tuid]);
					}
					$uid = 'PROPERTY_'.$uid;
				}
				if(strlen($valUid)==0) $valUid = $valUid2 = false;
			}
			if($uid)
			{
				$arUid[] = array(
					'uid' => $uid,
					'valUid' => $valUid,
					'valUid2' => $valUid2
				);
			}
		}

		$emptyFields = $notEmptyFields = array();
		foreach($arUid as $k=>$v)
		{
			if((is_array($v['valUid']) && count(array_diff($v['valUid'], array('')))>0)
				|| (!is_array($v['valUid']) && strlen(trim($v['valUid']))>0)) $notEmptyFields[] = $v['uid'];
			else $emptyFields[] = $v['uid'];
		}
		
		if(($ID > 0 && count($notEmptyFields) < 2) || ($ID <= 0 && (count($notEmptyFields) < 1 || count($emptyFields) > 1)))
		{
			return false;
		}
		
		$arDates = array('ACTIVE_FROM', 'ACTIVE_TO', 'DATE_CREATE');
		foreach($arDates as $keyDate)
		{
			if(isset($arFieldsElement[$keyDate]) && strlen($arFieldsElement[$keyDate]) > 0)
			{
				$arFieldsElement[$keyDate] = $this->GetDateVal($arFieldsElement[$keyDate]);
			}
		}
		
		if(isset($arFieldsElement['ACTIVE']))
		{
			$arFieldsElement['ACTIVE'] = $this->GetBoolValue($arFieldsElement['ACTIVE']);
		}
		elseif($this->params['ELEMENT_LOADING_ACTIVATE']=='Y')
		{
			$arFieldsElement['ACTIVE'] = 'Y';
		}

		if(($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y' && isset($arFieldsProduct['QUANTITY']) && $this->GetFloatVal($arFieldsProduct['QUANTITY'])<=0)
			|| ($this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y' && $this->IsEmptyPrice($arFieldsPrices)))
		{
			$arFieldsElement['ACTIVE'] = 'N';
		}
		
		$arKeys = array_merge(array('ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_'.$OFFERS_PROPERTY_ID), array_keys($arFieldsElement));
		
		$arFilter = array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'CHECK_PERMISSIONS' => 'N');
		foreach($arUid as $v)
		{
			if(is_array($v['valUid'])) $arSubfilter = array_map('trim', $v['valUid']);
			else 
			{
				$arSubfilter = array(trim($v['valUid']));
				if(trim($v['valUid']) != $v['valUid2'])
				{
					$arSubfilter[] = trim($v['valUid2']);
					if(strlen($v['valUid2']) != strlen(trim($v['valUid2'])))
					{
						$arSubfilter[] = $v['valUid2'];
					}
				}
				if(strlen($v['valUid']) != strlen(trim($v['valUid'])))
				{
					$arSubfilter[] = $v['valUid'];
				}
			}
			if(count($arSubfilter) == 1)
			{
				$arSubfilter = $arSubfilter[0];
			}
			$arFilter['='.$v['uid']] = $arSubfilter;
		}
		
		if(!empty($arFieldsIpropTemp))
		{
			$arFieldsElement['IPROPERTY_TEMPLATES'] = $arFieldsIpropTemp;
		}

		$elemName = '';
		$dbRes = \CIblockElement::GetList(array(), $arFilter, false, false, $arKeys);
		while($arElement = $dbRes->Fetch())
		{
			$OFFER_ID = $arElement['ID'];
			$arFieldsProps2 = $arFieldsProps;
			$arFieldsElement2 = $arFieldsElement;
			$arFieldsProduct2 = $arFieldsProduct;
			$arFieldsPrices2 = $arFieldsPrices;
			$arFieldsProductStores2 = $arFieldsProductStores;
			if($this->conv->SetElementId($OFFER_ID)
				&& $this->conv->UpdateProperties($arFieldsProps2, $OFFER_ID)!==false
				&& $this->conv->UpdateElementFields($arFieldsElement2, $OFFER_ID)!==false
				&& $this->conv->UpdateProduct($arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $OFFER_ID)!==false
				&& $this->conv->SetElementId(0))
			{
				$this->BeforeElementSave($OFFER_ID, 'update');
				if($this->params['ONLY_CREATE_MODE_ELEMENT']!='Y')
				{
					$this->UnsetUidFields($arFieldsElement2, $arFieldsProps2, $this->params['ELEMENT_UID_SKU']);
					if(!empty($this->fieldOnlyNewOffer))
					{
						$this->UnsetExcessFields($this->fieldOnlyNewOffer, $arFieldsElement2, $arFieldsProps2, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $arFieldsProductDiscount);
					}
					
					$this->SaveProperties($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProps2);
					$this->SaveProduct($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProduct2, $arFieldsPrices2, $arFieldsProductStores2, $ID);
					
					$el = new \CIblockElement();
					if($this->UpdateElement($el, $OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsElement2, $arElement))
					{
						//$this->SetTimeBegin($OFFER_ID);
					}
					else
					{
						$this->stepparams['error_line']++;
						$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_UPDATE_OFFER_ERROR"), $el->LAST_ERROR, '');
					}
						
					$elemName = $arElement['NAME'];
					$this->SaveDiscount($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProductDiscount, $elemName, true);
					$this->stepparams['sku_updated_line']++;
				}
			}
			$this->SaveElementId($OFFER_ID, true);
			if(!$ID && $arElement['PROPERTY_'.$OFFERS_PROPERTY_ID.'_VALUE'])
			{
				$this->SaveElementId($arElement['PROPERTY_'.$OFFERS_PROPERTY_ID.'_VALUE']);
			}
		}
		if($elemName && !$arFieldsElement['NAME']) $arFieldsElement['NAME'] = $elemName;
		
		if($dbRes->SelectedRowsCount()==0 && $ID && $this->params['SEARCH_OFFERS_WO_PRODUCTS']!='Y')
		{
			if($this->params['ONLY_UPDATE_MODE_ELEMENT']!='Y')
			{
				$this->UnsetUidFields($arFieldsElement, $arFieldsProps, $this->params['ELEMENT_UID_SKU'], true);
				if(isset($arFieldsElement['ID']))
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_NEW_OFFER_WITH_ID"), $arFieldsElement['ID'], '');
					return false;
				}
				if(strlen($arFieldsElement['NAME'])==0)
				{
					$arFieldsElement['NAME'] = $NAME;
				}
				if($this->params['ELEMENT_NEW_DEACTIVATE']=='Y')
				{
					$arFieldsElement['ACTIVE'] = 'N';
				}
				elseif(!$arFieldsElement['ACTIVE'])
				{
					$arFieldsElement['ACTIVE'] = 'Y';
				}
				$arFieldsElement['IBLOCK_ID'] = $OFFERS_IBLOCK_ID;
				$this->PrepareElementPictures($arFieldsElement, true);
				$this->GetDefaultElementFields($arFieldsElement, $iblockFields);
				$el = new \CIblockElement();
				$OFFER_ID = $el->Add(array_merge($arFieldsElement, array('PROPERTY_VALUES'=>array($OFFERS_PROPERTY_ID => $ID))), false, true, true);
				
				if($OFFER_ID)
				{
					$this->BeforeElementSave($OFFER_ID, 'add');
					$this->logger->AddElementChanges('IE_', $arFieldsElement);
					$this->AddTagIblock($IBLOCK_ID);
					//$this->SetTimeBegin($OFFER_ID);
					$this->SaveProperties($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProps, true);
					$this->PrepareProductAdd($arFieldsProduct, $OFFER_ID, $OFFERS_IBLOCK_ID);
					$this->SaveProduct($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProduct, $arFieldsPrices, $arFieldsProductStores, $ID);
					$this->SaveDiscount($OFFER_ID, $OFFERS_IBLOCK_ID, $arFieldsProductDiscount, $arFieldsElement['NAME'], true);
					//if(!empty($arFieldsElement['IPROPERTY_TEMPLATES']))
					if(true)
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($OFFERS_IBLOCK_ID, $OFFER_ID);
						$ipropValues->clearValues();
					}
					$this->stepparams['sku_added_line']++;
					$this->SaveElementId($OFFER_ID, true);
				}
				else
				{
					$this->stepparams['error_line']++;
					$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_ADD_OFFER_ERROR"), $el->LAST_ERROR, '');
					return false;
				}
			}
			else
			{
				$this->logger->SaveElementNotFound($arFilter);
			}
		}

		if($OFFER_ID)
		{
			if($this->params['ONAFTERSAVE_HANDLER'])
			{
				$this->ExecuteOnAfterSaveHandler($this->params['ONAFTERSAVE_HANDLER'], $OFFER_ID);
			}
		}
		
		/*Update product*/
		if($ID && $OFFER_ID && ($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y' || $this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y') && class_exists('\Bitrix\Catalog\ProductTable') && class_exists('\Bitrix\Catalog\PriceTable'))
		{
			$arOfferIds = array();
			$offersActive = false;
			$dbRes = \CIblockElement::GetList(array(), array(
				'IBLOCK_ID' => $OFFERS_IBLOCK_ID, 
				'PROPERTY_'.$OFFERS_PROPERTY_ID => $ID,
				'CHECK_PERMISSIONS' => 'N'), 
				false, false, array('ID', 'ACTIVE'));
			while($arr = $dbRes->Fetch())
			{
				$arOfferIds[] = $arr['ID'];
				$offersActive = (bool)($offersActive || ($arr['ACTIVE']=='Y'));
			}
			
			if(!empty($arOfferIds))
			{
				$active = false;
				if(!$offersActive) $active = 'N';
				else
				{
					if($this->params['ELEMENT_LOADING_ACTIVATE']=='Y') $active = 'Y';
					if($this->params['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y')
					{
						$existQuantity = \Bitrix\Catalog\ProductTable::getList(array(
							'select' => array('ID', 'QUANTITY'),
							'filter' => array('@ID' => $arOfferIds, '>QUANTITY' => 0),
							'limit' => 1
						))->fetch();
						if(!$existQuantity)  $active = 'N';
					}
					if($this->params['ELEMENT_NO_PRICE_DEACTIVATE']=='Y')
					{
						$existPrice = \Bitrix\Catalog\PriceTable::getList(array(
							'select' => array('ID', 'PRICE'),
							'filter' => array('@PRODUCT_ID' => $arOfferIds, '>PRICE' => 0),
							'limit' => 1
						))->fetch();
						if(!$existPrice)  $active = 'N';
					}
				}
				if($active!==false)
				{
					$arElem = \CIblockElement::GetList(array(), array('ID'=>$ID, 'CHECK_PERMISSIONS' => 'N'), false, false, array('ACTIVE'))->Fetch();
					if($arElem['ACTIVE']!=$active)
					{
						$el = new \CIblockElement();
						$el->Update($ID, array('ACTIVE'=>$active), false, true, true);
						$this->AddTagIblock($IBLOCK_ID);
					}
				}
			}
		}
		if($ID && $OFFER_ID && defined('\Bitrix\Catalog\ProductTable::TYPE_SKU'))
		{
			$this->SaveProduct($ID, $IBLOCK_ID, array('TYPE'=>\Bitrix\Catalog\ProductTable::TYPE_SKU), array(), array());
		}
		/*/Update product*/
		
		return (bool)($OFFER_ID && $OFFER_ID > 0);
	}
	
	public function GetElementSections($ID)
	{
		$arSections = array();
		$dbRes = \CIBlockElement::GetElementGroups($ID, true, array('ID'));
		while($arr = $dbRes->Fetch())
		{
			$arSections[] = $arr['ID'];
		}
		return $arSections;
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
	
	public function UnsetExcessFields($fieldsList, &$arFieldsElement, &$arFieldsProps, &$arFieldsProduct, &$arFieldsPrices, &$arFieldsProductStores, &$arFieldsProductDiscount)
	{
		foreach($fieldsList as $field)
		{
			if(strpos($field, 'IE_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						unset($arFieldsElement[$adata[0]]);
					}
				}
				unset($arFieldsElement[substr($field, 3)]);
			}
			elseif(strpos($field, 'ISECT')===0)
			{
				unset($arFieldsElement['IBLOCK_SECTION']);
			}
			elseif(strpos($field, 'ICAT_PRICE')===0)
			{
				$arPrice = explode('_', substr($field, 10), 2);
				unset($arFieldsPrices[$arPrice[0]][$arPrice[1]]);
				if(empty($arFieldsPrices[$arPrice[0]])) unset($arFieldsPrices[$arPrice[0]]);
			}
			elseif(strpos($field, 'ICAT_STORE')===0)
			{
				$arStore = explode('_', substr($field, 10), 2);
				unset($arFieldsProductStores[$arStore[0]][$arStore[1]]);
				if(empty($arFieldsProductStores[$arStore[0]])) unset($arFieldsProductStores[$arStore[0]]);
			}
			elseif(strpos($field, 'ICAT_DISCOUNT_')===0)
			{
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
					if(count($adata) > 1)
					{
						unset($arFieldsProductDiscount[$adata[0]]);
					}
				}
				unset($arFieldsProductDiscount[substr($field, 14)]);
			}
			elseif(strpos($field, 'ICAT_')===0)
			{
				unset($arFieldsProduct[substr($field, 5)]);
			}
			elseif(strpos($field, 'IP_PROP')===0)
			{
				unset($arFieldsProps[substr($field, 7)]);
			}
			elseif(strpos($field, 'IPROP_TEMP_')===0)
			{
				unset($arFieldsElement['IPROPERTY_TEMPLATES'][substr($field, 11)]);
			}
		}
	}
	
	public function UnsetExcessSectionFields($fieldsList, &$arFieldsSections, &$arFieldsElement)
	{
		foreach($fieldsList as $field)
		{
			if(strpos($field, 'ISECT')===0)
			{
				$adata = false;
				if(strpos($field, '|')!==false)
				{
					list($field, $adata) = explode('|', $field);
					$adata = explode('=', $adata);
				}
				$arSect = explode('_', substr($field, 5), 2);
				unset($arFieldsSections[$arSect[0]][$arSect[1]]);
				
				if(is_array($adata) && count($adata) > 1)
				{
					unset($arFieldsSections[$arSect[0]][$adata[0]]);
				}
			}
			elseif($field=='IE_SECTION_PATH')
			{
				$field = substr($field, 3);
				unset($arFieldsElement[$field]);
			}
		}
	}
	
	public function GetPropField(&$arFieldsProps, &$arFieldsPropsOrig, $fieldSettingsExtra, $propDef, $fieldName, $value, $origValue, $arUids = array())
	{
		if(!isset($arFieldsProps[$fieldName])) $arFieldsProps[$fieldName] = null;
		if(!isset($arFieldsPropsOrig[$fieldName])) $arFieldsPropsOrig[$fieldName] = null;
		$arFieldsPropsItem = &$arFieldsProps[$fieldName];
		$arFieldsPropsOrigItem = &$arFieldsPropsOrig[$fieldName];
		
		if($propDef	&& $propDef['USER_TYPE']=='directory')
		{
			if($fieldSettingsExtra['HLBL_FIELD']) $key2 = $fieldSettingsExtra['HLBL_FIELD'];
			else $key2 = 'UF_NAME';
			if(!isset($arFieldsPropsItem[$key2])) $arFieldsPropsItem[$key2] = null;
			if(!isset($arFieldsPropsOrigItem[$key2])) $arFieldsPropsOrigItem[$key2] = null;
			$arFieldsPropsItem = &$arFieldsPropsItem[$key2];
			$arFieldsPropsOrigItem = &$arFieldsPropsOrigItem[$key2];
		}
		
		if(($propDef['MULTIPLE']=='Y' || in_array('IP_PROP'.$fieldName, $arUids)) && !is_null($arFieldsPropsItem))
		{
			if(!is_array($arFieldsPropsItem))
			{
				$arFieldsPropsItem = array($arFieldsPropsItem);
				$arFieldsPropsOrigItem = array($arFieldsPropsOrigItem);
			}
			if(!is_array($value))
			{
				$value = array($value);
				$origValue = array($origValue);
			}
			$arFieldsPropsItem = array_merge($arFieldsPropsItem, $value);
			$arFieldsPropsOrigItem = array_merge($arFieldsPropsOrigItem, $origValue);
		}
		else
		{
			$arFieldsPropsItem = $value;
			$arFieldsPropsOrigItem = $origValue;
		}
	}
	
	public function GetPropList(&$arFieldsProps, &$arFieldsPropsOrig, $fieldSettingsExtra, $IBLOCK_ID, $value)
	{
		if(strlen($fieldSettingsExtra['PROPLIST_PROPS_SEP'])==0 || strlen($fieldSettingsExtra['PROPLIST_PROPVALS_SEP'])==0) return;
		$arProps = explode($fieldSettingsExtra['PROPLIST_PROPS_SEP'], $value);
		foreach($arProps as $prop)
		{
			$arCurProp = explode($fieldSettingsExtra['PROPLIST_PROPVALS_SEP'], $prop, 2);
			if(count($arCurProp)!=2) continue;
			$arCurProp = array_map('trim', $arCurProp);
			if(strlen($arCurProp[0])==0) continue;
			$createNew = ($fieldSettingsExtra['PROPLIST_CREATE_NEW']=='Y');
			$propDef = $this->GetIblockPropertyByName($arCurProp[0], $IBLOCK_ID, $createNew);
			if($propDef!==false)
			{
				$this->GetPropField($arFieldsProps, $arFieldsPropsOrig, array(), $propDef, $propDef['ID'], $arCurProp[1], $arCurProp[1]);
			}
		}
	}
	
	public function SaveElementId($ID, $offer=false)
	{
		$fn = ($offer ? $this->fileOffersId : $this->fileElementsId);
		$handle = fopen($fn, 'a');
		fwrite($handle, $ID."\r\n");
		fclose($handle);
		$this->logger->SaveElementChanges($ID);
	}
	
	public function BeforeElementSave($ID, $type="update")
	{
		$this->logger->SetNewElement($ID, $type);
	}
	
	public function BeforeElementDelete($ID, $IBLOCK_ID)
	{
		$this->logger->SetNewElement($ID, 'delete');
	}
	
	public function AfterElementDelete($ID, $IBLOCK_ID)
	{
		$this->logger->AddElementChanges('IE_', array('ID'=>$ID));
		$this->logger->SaveElementChanges($ID);
		$this->AddTagIblock($IBLOCK_ID);
		$this->stepparams['element_removed_line']++;
	}
	
	public function AfterSectionSave($ID, $IBLOCK_ID, $arFields, $arSection=array())
	{
		$this->AddTagIblock($IBLOCK_ID);
		//$this->logger->AddSectionChanges($arFields, $arSection);
		
		if($this->params['REMOVE_COMPOSITE_CACHE_PART']=='Y')
		{
			if($arSection = \CIblockSection::GetList(array(), array('ID'=>$ID), false, array('SECTION_PAGE_URL'))->GetNext())
			{
				$this->ClearCompositeCache($arSection['SECTION_PAGE_URL']);
			}
		}
	}
	
	public function ApplyMargins($val, $fieldKey)
	{
		if(is_array($val))
		{
			foreach($val as $k=>$v)
			{
				$val[$k] = $this->ApplyMargins($v, $fieldKey);
			}
			return $val;
		}
		
		if(is_array($fieldKey)) $arParams = $fieldKey;
		else $arParams = $this->fieldSettings[$fieldKey];
		$val = $this->GetFloatVal($val);
		$sval = $val;
		$margins = $arParams['MARGINS'];
		if(is_array($margins) && count($margins) > 0)
		{
			foreach($margins as $margin)
			{
				if((strlen(trim($margin['PRICE_FROM']))==0 || $sval >= $this->GetFloatVal($margin['PRICE_FROM']))
					&& (strlen(trim($margin['PRICE_TO']))==0 || $sval <= $this->GetFloatVal($margin['PRICE_TO'])))
				{
					if($margin['PERCENT_TYPE']=='F')
						$val += ($margin['TYPE'] > 0 ? 1 : -1)*$this->GetFloatVal($margin['PERCENT']);
					else
						$val *= (1 + ($margin['TYPE'] > 0 ? 1 : -1)*$this->GetFloatVal($margin['PERCENT'])/100);
				}
			}
		}
		
		/*Rounding*/
		$roundRule = $arParams['PRICE_ROUND_RULE'];
		$roundRatio = $arParams['PRICE_ROUND_COEFFICIENT'];
		$roundRatio = str_replace(',', '.', $roundRatio);
		if(!preg_match('/^[\d\.]+$/', $roundRatio)) $roundRatio = 1;
		
		if($roundRule=='ROUND')	$val = round($val / $roundRatio) * $roundRatio;
		elseif($roundRule=='CEIL') $val = ceil($val / $roundRatio) * $roundRatio;
		elseif($roundRule=='FLOOR') $val = floor($val / $roundRatio) * $roundRatio;
		/*/Rounding*/
		
		return $val;
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
	
	public function AddTmpFile($fileOrig, $file)
	{
		$this->arTmpImages[$fileOrig] = array('file'=>$file, 'size'=>filesize($file));
	}
	
	public function GetTmpFile($fileOrig)
	{
		if(array_key_exists($fileOrig, $this->arTmpImages))
		{
			if(filesize($this->arTmpImages[$fileOrig]['file'])==$this->arTmpImages[$fileOrig]['size']) return $this->arTmpImages[$fileOrig]['file'];
			else unset($this->arTmpImages[$fileOrig]);
		}
		return false;
	}
	
	public function CreateTmpImageDir()
	{
		$tmpsubdir = $this->imagedir.($this->filecnt++).'/';
		CheckDirPath($tmpsubdir);
		$this->arTmpImageDirs[] = $tmpsubdir;
		return $tmpsubdir;
	}
	
	public function RemoveTmpImageDirs()
	{
		if(!empty($this->arTmpImageDirs))
		{
			foreach($this->arTmpImageDirs as $k=>$v)
			{
				DeleteDirFilesEx(substr($v, strlen($_SERVER['DOCUMENT_ROOT'])));
			}
			$this->arTmpImageDirs = array();
		}
		$this->arTmpImages = array();
	}
	
	public function GetFileArray($file, $arDef=array(), $arParams=array())
	{
		$bNeedImage = (bool)($arParams['FILETYPE']=='IMAGE');
		$bMultiple = (bool)($arParams['MULTIPLE']=='Y');
		$fileTypes = array();
		if($bNeedImage) $fileTypes = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
		elseif($arParams['FILE_TYPE']) $fileTypes = array_diff(array_map('trim', explode(',', ToLower($arParams['FILE_TYPE']))), array(''));
		
		if(is_array($file))
		{
			if($bMultiple)
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
		elseif($tmpFile = $this->GetTmpFile($fileOrig))
		{
			$file = $tmpFile;
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
			$arFile = $this->sftp->MakeFileArray($file, $arParams);
			if($bMultiple && array_key_exists('0', $arFile))
			{
				$arFiles = array();
				foreach($arFile as $subfile)
				{
					if(is_array($subfile)) $arFiles[] = $subfile;
					else $arFiles[] = $this->GetFileArray($subfile, $arDef, $arParams);
				}
				return $arFiles;
			}
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
			$file = preg_replace_callback('/[^:\/?=&#@\+]+/', create_function('$m', 'return urldecode($m[0]);'), $file);
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
				$basename = preg_replace('/\?.*$/', '', bx_basename($file));
				if(preg_match('/^[_+=!?]*\./', $basename) || strlen(trim($basename))==0) $basename = 'f'.$basename;
				$tempPath = $tmpsubdir.$basename;
				$tempPath2 = $tmpsubdir.(\Bitrix\Main\IO\Path::convertLogicalToPhysical($basename));
				$arOptions = array();
				if($this->useProxy) $arOptions = $this->proxySettings;
				$arOptions['disableSslVerification'] = true;
				$arOptions['socketTimeout'] = $arOptions['streamTimeout'] = 10;
				$ob = new \Bitrix\Main\Web\HttpClient($arOptions);
				//$ob->setHeader('User-Agent', 'BitrixSM HttpClient class');
				$ob->setHeader('User-Agent', \Bitrix\EsolImportxml\Utils::GetUserAgent());
				try{
					if(!\CUtil::DetectUTF8($file)) $file = \Bitrix\EsolImportxml\Utils::Win1251Utf8($file);
					$file = preg_replace_callback('/[^:\/?=&#@]+/', create_function('$m', 'return rawurlencode($m[0]);'), $file);
					if($ob->download($file, $tempPath) && $ob->getStatus()!=404) $file = $tempPath2;
					else return array();
				}catch(Exception $ex){}
				
				if(strpos($ob->getHeaders()->get("content-type"), 'text/html')!==false 
					&& (in_array('jpg', $fileTypes) || in_array('jpeg', $fileTypes))
					&& ($arFile = \CFile::MakeFileArray($file))
					&& stripos($arFile['type'], 'image')===false)
				{
					$fileContent = file_get_contents($file);
					if(preg_match_all('/src=[\'"]([^\'"]*)[\'"]/is', $fileContent, $m))
					{
						if($bMultiple)
						{
							$arFiles = array();
							foreach($m[1] as $img)
							{
								$img = trim($img);
								if(preg_match('/data:image\/(.{3,4});base64,/is', $img, $m))
								{
									$subfile = $this->CreateTmpImageDir().'img.'.$m[1];
									file_put_contents($subfile, base64_decode(substr($img, strlen($m[0]))));
									$arFiles[] = $this->GetFileArray($subfile, $arDef, $arParams);
								}
							}
							if(!empty($arFiles)) return array('VALUES' => $arFiles);
						}
						else
						{
							$img = trim(current($m[1]));
							if(preg_match('/data:image\/(.{3,4});base64,/is', $img, $m))
							{
								file_put_contents($file, base64_decode(substr($img, strlen($m[0]))));
							}
						}
					}
				}
			}
		}
		$this->AddTmpFile($fileOrig, $file);
		$arFile = \CFile::MakeFileArray($file);
		
		if(!file_exists($file) && !$arFile['name'] && !\CUtil::DetectUTF8($file))
		{
			$file = \Bitrix\EsolImportxml\Utils::Win1251Utf8($file);
			$arFile = \CFile::MakeFileArray($file);
		}
		
		$dirname = '';
		if(file_exists($file) && is_dir($file))
		{
			$dirname = $file;
		}
		elseif(in_array($arFile['type'], array('application/zip', 'application/x-zip-compressed')) && !empty($fileTypes) && !in_array('zip', $fileTypes))
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
			if($bMultiple && count($arFiles) > 1)
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
			if($this->IsWrongExt($arFile['name'], $ext))
			{
				if(($ext!='jpeg' || (($ext='jpg') && $this->IsWrongExt($arFile['name'], $ext)))
					&& ($ext!='svg+xml' || (($ext='svg') && $this->IsWrongExt($arFile['name'], $ext)))
				)
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
	
	public function IsWrongExt($name, $ext)
	{
		return (bool)(substr($name, -(strlen($ext) + 1))!='.'.$ext);
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
	
	public function SetTimeBegin($ID)
	{
		if($this->stepparams['begin_time']) return;
		$dbRes = \CIblockElement::GetList(array(), array('ID'=>$ID, 'CHECK_PERMISSIONS' => 'N'), false, false, array('TIMESTAMP_X'));
		if($arr = $dbRes->Fetch())
		{
			$this->stepparams['begin_time'] = $arr['TIMESTAMP_X'];
		}
	}
	
	public function IsEmptyPrice($arPrices)
	{
		if(is_array($arPrices))
		{
			foreach($arPrices as $arPrice)
			{
				if($arPrice['PRICE'] > 0)
				{
					return false;
				}
			}
		}
		return true;
	}
	
	public function GetHLBoolValue($val)
	{
		$res = $this->GetBoolValue($val);
		if($res=='Y') return 1;
		else return 0;
	}
	
	public function GetBoolValue($val, $numReturn = false, $defaultValue = false)
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
			return $defaultValue;
		}
	}
	
	public function SaveSection($arFields, $IBLOCK_ID, $parent=0, $level=0, $arParams=array())
	{
		$iblockFields = $this->GetIblockFields($IBLOCK_ID);
		$sectionFields = $this->GetIblockSectionFields($IBLOCK_ID);
		$sectId = false;
		$arPictures = array('PICTURE', 'DETAIL_PICTURE');
		foreach($arPictures as $picName)
		{
			if($arFields[$picName])
			{
				$val = $arFields[$picName];
				if(is_array($val)) $arFields[$val] = current($val);
				$arFile = $this->GetFileArray($val, array(), array('FILETYPE'=>'IMAGE'));
				if(empty($arFile) && strpos($val, $this->params['ELEMENT_MULTIPLE_SEPARATOR'])!==false)
				{
					$arVals = array_diff(array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $val)), array(''));
					if(count($arVals) > 0 && ($val = current($arVals)))
					{
						$arFile = $this->GetFileArray($val, array(), array('FILETYPE'=>'IMAGE'));
					}
				}
				$arFields[$picName] = $arFile;
			}
		}
		
		if(isset($arFields['ACTIVE']))
		{
			$arFields['ACTIVE'] = $this->GetBoolValue($arFields['ACTIVE']);
		}
		
		$arTexts = array('DESCRIPTION');
		foreach($arTexts as $keyText)
		{
			if($arFields[$keyText])
			{
				$textFile = $_SERVER["DOCUMENT_ROOT"].$arFields[$keyText];
				if(file_exists($textFile) && is_file($textFile) && is_readable($textFile))
				{
					$arFields[$keyText] = file_get_contents($textFile);
				}
			}
		}
		
		foreach($arFields as $k=>$v)
		{
			if(isset($sectionFields[$k]))
			{
				$sParams = $sectionFields[$k];
				//$fieldSettings = $this->fieldSettings['ISECT'.$level.'_'.$k];
				$fieldSettings = $this->fieldSettings['ISECT_'.$k];
				if(!is_array($fieldSettings)) $fieldSettings = array();
				if($sParams['MULTIPLE']=='Y')
				{
					if(!is_array($arFields[$k]))
					{
						$separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
						if($fieldSettings['CHANGE_MULTIPLE_SEPARATOR']=='Y')
						{
							$separator = $fieldSettings['MULTIPLE_SEPARATOR'];
						}
						$arFields[$k] = array_map('trim', explode($separator, $arFields[$k]));
					}
					foreach($arFields[$k] as $k2=>$v2)
					{
						$arFields[$k][$k2] = $this->GetSectionField($v2, $sParams, $fieldSettings);
					}
				}
				else
				{
					$arFields[$k] = $this->GetSectionField($arFields[$k], $sParams, $fieldSettings);
				}
			}
			if(strpos($k, 'IPROP_TEMP_')===0)
			{
				$arFields['IPROPERTY_TEMPLATES'][substr($k, 11)] = $v;
				unset($arFields[$k]);
			}
		}
		
		if($parent > 0) $arFields['IBLOCK_SECTION_ID'] = $parent;
		
		$sectionUid = $this->params['SECTION_UID'];
		if(!$arFields[$sectionUid]) $sectionUid = 'NAME';
		if((!is_array($arFields[$sectionUid]) && strlen(trim($arFields[$sectionUid]))==0) || empty($arFields[$sectionUid])) return false;
		$arFilter = array(
			$sectionUid=>$arFields[$sectionUid],
			'IBLOCK_ID'=>$IBLOCK_ID,
			'CHECK_PERMISSIONS' => 'N'
		);
		if(!is_array($arFields[$sectionUid]) && strlen($arFields[$sectionUid])!=strlen(trim($arFields[$sectionUid])))
		{
			$arFilter[$sectionUid] = array($arFields[$sectionUid], trim($arFields[$sectionUid]));
		}
		if(!isset($arFields['IGNORE_PARENT_SECTION']) || $arFields['IGNORE_PARENT_SECTION']!='Y') $arFilter['SECTION_ID'] = $parent;
		else unset($arFields['IGNORE_PARENT_SECTION']);
		
		if($arParams['SECTION_SEARCH_IN_SUBSECTIONS']=='Y')
		{
			if($parent && $arParams['SECTION_SEARCH_WITHOUT_PARENT']!='Y')
			{
				$dbRes2 = \CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID, 'ID'=>$parent, 'CHECK_PERMISSIONS' => 'N'), false, array('ID', 'LEFT_MARGIN', 'RIGHT_MARGIN'));
				if($arParentSection = $dbRes2->Fetch())
				{
					$arFilter['>LEFT_MARGIN'] = $arParentSection['LEFT_MARGIN'];
					$arFilter['<RIGHT_MARGIN'] = $arParentSection['RIGHT_MARGIN'];
				}
			}
			unset($arFilter['SECTION_ID']);
		}
		$dbRes = \CIBlockSection::GetList(array(), $arFilter, false, array_merge(array('ID'), array_keys($arFields)));
		$arSections = array();
		while($arSect = $dbRes->Fetch())
		{
			$sectId = $arSect['ID'];
			if($this->params['ONLY_CREATE_MODE_SECTION']!='Y' && $this->conv->UpdateSectionFields($arFields, $sectId)!==false)
			{
				foreach($arSect as $k=>$v)
				{
					if(isset($arFields[$k]) && ($arFields[$k]==$v || ($k=='NAME' && ToLower($arFields[$k])==ToLower($v)) || $k==$sectionUid)) unset($arFields[$k]);
				}
				if(($arParams['SECTION_SEARCH_IN_SUBSECTIONS']=='Y' || $arParams['SECTION_SEARCH_WITHOUT_PARENT']=='Y') && isset($arFields['IBLOCK_SECTION_ID']))
				{
					unset($arFields['IBLOCK_SECTION_ID']);
				}
				if(!empty($arFields))
				{
					$bs = new \CIBlockSection;
					$bs->Update($sectId, $arFields, true, true, true);
					$this->AfterSectionSave($sectId, $IBLOCK_ID, $arFields, $arSect);
					if(true)
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($IBLOCK_ID, $sectId);
						$ipropValues->clearValues();
					}
				}
				$this->stepparams['section_updated_line']++;
			}
			$arSections[] = $sectId;
		}
		if(empty($arSections) && $this->params['ONLY_UPDATE_MODE_SECTION']!='Y')
		{
			if(strlen(trim($arFields['NAME']))==0) return false;
			if(!isset($arFields['ACTIVE'])) $arFields['ACTIVE'] = 'Y';
			$arFields['IBLOCK_ID'] = $IBLOCK_ID;

			if(($iblockFields['SECTION_CODE']['IS_REQUIRED']=='Y' || $iblockFields['SECTION_CODE']['DEFAULT_VALUE']['TRANSLITERATION']=='Y') && strlen($arFields['CODE'])==0)
			{
				$arFields['CODE'] = $this->Str2Url($arFields['NAME'], $iblockFields['SECTION_CODE']['DEFAULT_VALUE']);
				if($iblockFields['SECTION_CODE']['DEFAULT_VALUE']['UNIQUE']=='Y' && $sectionUid!='CODE')
				{
					$j = 0;
					$jmax = 1000;
					$code = $arFields['CODE'];
					while($j<$jmax && (\CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID, 'CODE'=>$arFields['CODE']), false, array('ID'))->Fetch()) && ($arFields['CODE'] = $code.strval(++$j))){}
				}
			}
			$bs = new \CIBlockSection;
			$sectId = $j = 0;
			$code = $arFields['CODE'];
			$jmax = ($sectionUid=='CODE' ? 1 : 1000);
			while($j<$jmax && !($sectId = $bs->Add($arFields, true, true, true)) && ($arFields['CODE'] = $code.strval(++$j))){}
			if($sectId)
			{
				$this->AfterSectionSave($sectId, $IBLOCK_ID, $arFields);
				$this->stepparams['section_added_line']++;
			}
			else
			{
				$this->errors[] = sprintf(Loc::getMessage("ESOL_IX_ADD_SECTION_ERROR"), $arFields['NAME'], $bs->LAST_ERROR, '');
			}
			$arSections[] = $sectId;
		}
		return $arSections;
	}
	
	public function GetSectionField($val, $sParams, $fieldSettings)
	{
		$userType = $sParams['USER_TYPE_ID'];
		if($userType=='file')
		{
			$val = $this->GetFileArray($val);
		}
		elseif($userType=='boolean')
		{
			$val = $this->GetBoolValue($val, true);
		}
		elseif($userType=='iblock_element')
		{
			$arProp = array('LINK_IBLOCK_ID' => $sParams['SETTINGS']['IBLOCK_ID']);
			$val = $this->GetIblockElementValue($arProp, $val, $fieldSettings);
		}
		return $val;
	}
	
	public function GetSections(&$arElement, $IBLOCK_ID, $SECTION_ID, $arSections)
	{
		if(isset($arElement['IBLOCK_SECTION']) && !empty($arElement['IBLOCK_SECTION']) && $this->params['ELEMENT_ADD_NEW_SECTIONS']!='Y') return;
		//if(!isset($arElement['SECTION_PATH'])) return;
	
		$arMultiSections = array();
		if(is_array($arElement['SECTION_PATH']))
		{
			foreach($arElement['SECTION_PATH'] as $sectionPath)
			{
				if(is_array($sectionPath))
				{
					$tmpSections = array();
					foreach($sectionPath as $k=>$name)
					{
						$tmpSections[$k+1]['NAME'] = $name;
					}
					$arMultiSections[] = $tmpSections;
				}
			}
			unset($arElement['SECTION_PATH']);
		}

		/*if no 1st level*/
		if($SECTION_ID > 0 && !empty($arSections) && !isset($arSections[1]))
		{
			$minKey = min(array_keys($arSections));
			$arSectionsOld = $arSections;
			$arSections = array();
			foreach($arSectionsOld as $k=>$v)
			{
				$arSections[$k - $minKey + 1] = $v;
			}
		}
		/*/if no 1st level*/
		
		if((empty($arSections) || !isset($arSections[1]) || count(array_diff($arSections[1], array('')))==0) && empty($arMultiSections))
		{
			if($SECTION_ID > 0)
			{
				if($this->params['ELEMENT_ADD_NEW_SECTIONS']=='Y' && is_array($arElement['IBLOCK_SECTION']))
					$arElement['IBLOCK_SECTION'][] = $SECTION_ID;
				else
					$arElement['IBLOCK_SECTION'] = array($SECTION_ID);
				return true;
			}
			return false;
		}
		$iblockFields = $this->GetIblockFields($IBLOCK_ID);
		
		if(empty($arMultiSections))
		{
			$arMultiSections[] = $arSections;
			$fromSectionPath = false;
		}
		else
		{
			if(count($arMultiSections)==1 && !empty($arSections))
			{
				foreach($arMultiSections as $k=>$v)
				{
					foreach($arSections as $k2=>$v2)
					{
						$lkey = $k2;
						if($v2[$this->params['SECTION_UID']])
						{
							$fsKey = 'ISECT'.$k2.'_'.$this->params['SECTION_UID'];
							if($this->fieldSettings[$fsKey]['SECTION_SEARCH_IN_SUBSECTIONS'] == 'Y')
							{
								$lkey = max(array_keys($v));
								$v2['IGNORE_PARENT_SECTION'] = 'Y';
							}
						}
						if(isset($v[$lkey]))
						{
							$arMultiSections[$k][$lkey] = array_merge($v[$lkey], $v2);
						}
					}
				}
			}
			$fromSectionPath = true;
		}

		foreach($arMultiSections as $arSections)
		{
			$parent = $i = 0;
			$arParents = array();
			if($SECTION_ID)
			{
				$parent = $SECTION_ID;
				$arParents[] = $SECTION_ID;
			}
			while(++$i && !empty($arSections[$i]))
			{
				$sectionUid = $this->params['SECTION_UID'];
				if(!$arSections[$i][$sectionUid]) $sectionUid = 'NAME';
				if(!$arSections[$i][$sectionUid]) continue;

				if($fromSectionPath) $fsKey = 'IE_SECTION_PATH';
				else $fsKey = 'ISECT'.$i.'_'.$sectionUid;
				
				if(($this->fieldSettings[$fsKey]['SECTION_UID_SEPARATED']=='Y' || is_array($arSections[$i][$sectionUid])) && empty($arSections[$i+1]))
				{
					if(is_array($arSections[$i][$sectionUid])) $arNames = $arSections[$i][$sectionUid];
					else $arNames = explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arSections[$i][$sectionUid]);
					$arNames = array_diff(array_map('trim', $arNames), array(''));
				}
				else
				{
					$arNames = array($arSections[$i][$sectionUid]);
				}
				if(empty($arNames)) continue;
				$arParents = array();
				
				$parentLvl = array();
				foreach($arNames as $name)
				{
					if(isset($this->sections[$parent][$name]) && !empty($this->sections[$parent][$name]))
					{
						$parentLvl = $this->sections[$parent][$name];
					}
					else
					{				
						$arFields = $arSections[$i];
						$arFields[$sectionUid] = $name;
						$sectId = $this->SaveSection($arFields, $IBLOCK_ID, $parent, $i, $this->fieldSettings[$fsKey]);
						$this->sections[$parent][$name] = $sectId;
						if(!empty($sectId)) $parentLvl = $sectId;
					}
					$arParents = array_merge($arParents, $parentLvl);
				}
				$parent = current(array_diff($parentLvl, array(0)));
				if(!$parent)
				{
					$parent = 0;
					/*continue;*/ break;
				}
			}
			
			if(!empty($arParents))
			{
				if(!is_array($arElement['IBLOCK_SECTION'])) $arElement['IBLOCK_SECTION'] = array();
				$arElement['IBLOCK_SECTION'] = array_unique(array_merge($arElement['IBLOCK_SECTION'], $arParents));
				$arElement['IBLOCK_SECTION_ID'] = current($arElement['IBLOCK_SECTION']);
			}
		}
	}
	
	public function GetIblockProperties($IBLOCK_ID, $byName = false)
	{
		if(!$this->props[$IBLOCK_ID])
		{
			$this->props[$IBLOCK_ID] = array();
			$this->propsByNames[$IBLOCK_ID] = array();
			$this->propsByCodes[$IBLOCK_ID] = array();
			$dbRes = \CIBlockProperty::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID));
			while($arProp = $dbRes->Fetch())
			{
				$this->props[$IBLOCK_ID][$arProp['ID']] = $arProp;
				$lName = ToLower($arProp['NAME']);
				if(!isset($this->propsByNames[$IBLOCK_ID][$lName]) || $arProp['ACTIVE']=='Y') $this->propsByNames[$IBLOCK_ID][$lName] = $arProp;
				$lCode = ToLower($arProp['CODE']);
				if(!isset($this->propsByCodes[$IBLOCK_ID][$lCode]) || $arProp['ACTIVE']=='Y') $this->propsByCodes[$IBLOCK_ID][$lCode] = $arProp;
			}
		}
		if(is_string($byName) && $byName=='CODE') return $this->propsByCodes[$IBLOCK_ID];
		elseif($byName) return $this->propsByNames[$IBLOCK_ID];
		else return $this->props[$IBLOCK_ID];
	}
	
	public function GetIblockPropertyByName($name, $IBLOCK_ID, $createNew = false)
	{
		$maxLen = 50;
		$name = trim($name);
		$lowerName = ToLower($name);
		$arProps = $this->GetIblockProperties($IBLOCK_ID, true);
		if(isset($arProps[$lowerName])) return $arProps[$lowerName];
		
		$arPropsByCodes = $this->GetIblockProperties($IBLOCK_ID, 'CODE');
		$arParams = array(
			'max_len' => $maxLen,
			'change_case' => 'U',
			'replace_space' => '_',
			'replace_other' => '_',
			'delete_repeat_replace' => 'Y',
		);
		$code = \CUtil::translit($name, LANGUAGE_ID, $arParams);
		$code = preg_replace('/[^a-zA-Z0-9_]/', '', $code);
		$code = preg_replace('/^[0-9_]+/', '', $code);
		$lowerCode = ToLower($code);
		if(isset($arPropsByCodes[$lowerCode]) && strlen($lowerCode)>=$maxLen)
		{
			$i = 1;
			while(isset($arPropsByCodes[$lowerCode]) && $i < 10000)
			{
				$code = substr($code, 0, -strlen($i)).$i;
				$lowerCode = ToLower($code);
				$i++;
			}
		}
		if(isset($arPropsByCodes[$lowerCode])) return $arPropsByCodes[$lowerCode];
			
		if($createNew)
		{			
			$arFields = Array(
				"NAME" => $name,
				"ACTIVE" => "Y",
				"CODE" => $code,
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $IBLOCK_ID
			);
			$this->PreparePropertyCode($arFields);
			$ibp = new \CIBlockProperty;
			$propID = $ibp->Add($arFields);
			if(!$propID) return false;
			
			$dbRes = \CIBlockProperty::GetList(array(), array('ID'=>$propID));
			if($arProp = $dbRes->Fetch())
			{
				$this->props[$IBLOCK_ID][$arProp['ID']] = $arProp;
				$this->propsByNames[$IBLOCK_ID][ToLower($arProp['NAME'])] = $arProp;
				$this->propsByCodes[$IBLOCK_ID][ToLower($arProp['CODE'])] = $arProp;
				return $arProp;
			}
		}
		return false;
	}
	
	public function PreparePropertyCode(&$arFields)
	{
		if(strlen($arFields['CODE']) > 0)
		{
			$index = 0;
			while(($dbRes2 = \CIBlockProperty::GetList(array(), array('CODE'=>$arFields['CODE'], 'IBLOCK_ID'=>$arFields['IBLOCK_ID']))) && ($arr2 = $dbRes2->Fetch()))
			{
				$index++;
				$arFields['CODE'] = substr($arFields['CODE'], 0, 50 - strlen($index)).$index;
			}
		}
	}
	
	public function GetIblockPropertyByCode($code, $IBLOCK_ID)
	{
		$code = trim($code);
		$lowerCode = ToLower($code);
		$arProps = $this->GetIblockProperties($IBLOCK_ID, 'CODE');
		if(isset($arProps[$lowerCode])) return $arProps[$lowerCode];
		return false;
	}
	
	public function GetIblockPropertyById($id, $IBLOCK_ID)
	{
		$id = (int)$id;
		$arProps = $this->GetIblockProperties($IBLOCK_ID);
		if(isset($arProps[$id])) return $arProps[$id];
		return false;
	}
	
	public function RemoveProperties($ID, $IBLOCK_ID)
	{
		if(is_array($this->params['ADDITIONAL_SETTINGS'][$this->worksheetNum]['ELEMENT_PROPERTIES_REMOVE']))
		{
			$arIds = $this->params['ADDITIONAL_SETTINGS'][$this->worksheetNum]['ELEMENT_PROPERTIES_REMOVE'];
		}
		else
		{
			$arIds = $this->params['ELEMENT_PROPERTIES_REMOVE'];
		}
		if(is_array($arIds) && !empty($arIds))
		{
			$arIblockProps = $this->GetIblockProperties($IBLOCK_ID);
			$arProps = array();
			foreach($arIds as $k=>$v)
			{
				if(strpos($v, 'IP_PROP')===0) $pid = (int)substr($v, strlen('IP_PROP'));
				else $pid = (int)$v;
				if($pid > 0)
				{
					if($arIblockProps[$pid]['PROPERTY_TYPE']=='F') $arProps[$pid] = array("del"=>"Y");
					else $arProps[$pid] = false;
				}
			}
			if(!empty($arProps))
			{
				\CIBlockElement::SetPropertyValuesEx($ID, $IBLOCK_ID, $arProps);
			}
		}
	}
	
	public function GetMultipleProperty($val, $k)
	{
		$separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
		$fsKey = 'IP_PROP'.$k;
		//$fsKey = ($this->conv->GetSkuMode() ? 'OFFER_' : '').'IP_PROP'.$k;
		if($this->fieldSettings[$fsKey]['CHANGE_MULTIPLE_SEPARATOR']=='Y')
		{
			$separator = $this->fieldSettings[$fsKey]['MULTIPLE_SEPARATOR'];
		}
		if(is_array($val))
		{
			$arVal = array();
			foreach($val as $subval)
			{
				if(is_array($subval)) $arVal[] = $subval;
				else $arVal = array_merge($arVal, explode($separator, $subval));
			}
		}
		else
		{
			if(is_array($val)) $arVal = $val;
			else $arVal = explode($separator, $val);
		}
		return $arVal;
	}
	
	public function SaveProperties($ID, $IBLOCK_ID, $arProps, $needUpdate = false)
	{
		if(empty($arProps)) return false;
		$propsDef = $this->GetIblockProperties($IBLOCK_ID);
		
		foreach($arProps as $k=>$prop)
		{
			if(!is_array($prop) && strpos($prop, '#ID#')!==false)
			{
				$arProps[$k] = str_replace('#ID#', $ID, $prop);
			}
		}
		
		foreach($arProps as $k=>$prop)
		{
			if(!is_numeric($k)) continue;
			if($propsDef[$k]['USER_TYPE']=='directory' && $propsDef[$k]['MULTIPLE']=='Y' && is_array($prop))
			{
				$newProp = array();
				foreach($prop as $k2=>$v2)
				{
					$arVal = $this->GetMultipleProperty($v2, $k);
					foreach($arVal as $k3=>$v3)
					{
						$newProp[$k3][$k2] = $v3;
					}
				}
				$arProps[$k] = $newProp;
			}
		}
		
		foreach($arProps as $k=>$prop)
		{
			if(strpos($k, '_DESCRIPTION')!==false) continue;
			if($propsDef[$k]['MULTIPLE']=='Y')
			{
				if($propsDef[$k]['USER_TYPE']=='directory') $arVal = $prop;
				else $arVal = $this->GetMultipleProperty($prop, $k);
				
				$fsKey = 'IP_PROP'.$k;
				if(isset($this->offerParentId) && $this->offerParentId > 0) $fsKey = 'OFFER_'.$fsKey;
				$fromValue = $this->fieldSettings[$fsKey]['MULTIPLE_FROM_VALUE'];
				$toValue = $this->fieldSettings[$fsKey]['MULTIPLE_TO_VALUE'];
				if(is_numeric($fromValue) || is_numeric($toValue))
				{
					$from = (is_numeric($fromValue) ? ((int)$fromValue >= 0 ? ((int)$fromValue - 1) : (int)$fromValue) : 0);
					$to = (is_numeric($toValue) ? ((int)$toValue >= 0 ? ((int)$toValue - max(0, $from)) : (int)$toValue) : 0);
					if($to!=0) $arVal = array_slice($arVal, $from, $to);
					else $arVal = array_slice($arVal, $from);
				}
				
				$newVals = array();
				foreach($arVal as $k2=>$val)
				{
					$arVal[$k2] = $this->GetPropValue($propsDef[$k], (is_string($val) ? trim($val) : $val));
					if(is_array($arVal[$k2]) && isset($arVal[$k2]['VALUES']))
					{
						$newVals = array_merge($newVals, $arVal[$k2]['VALUES']);
						unset($arVal[$k2]);
					}
				}
				if(!empty($newVals)) $arVal = array_merge($arVal, $newVals);
				$arProps[$k] = $arVal;
			}
			else
			{
				$arProps[$k] = $this->GetPropValue($propsDef[$k], $prop);
			}
			
			if($propsDef[$k]['PROPERTY_TYPE']=='F' && is_array($arProps[$k]) && count($arProps[$k])==0)
			{
				unset($arProps[$k]);
			}
			elseif($propsDef[$k]['PROPERTY_TYPE']=='S' && $propsDef[$k]['USER_TYPE']=='video')
			{
				\CIBlockElement::SetPropertyValueCode($ID, $k, $arProps[$k]);
				unset($arProps[$k]);
			}
		}
		foreach($arProps as $k=>$prop)
		{
			if(strpos($k, '_DESCRIPTION')===false) continue;
			$pk = substr($k, 0, strpos($k, '_'));
			if(!isset($arProps[$pk]))
			{
				$dbRes = \CIBlockElement::GetProperty($IBLOCK_ID, $ID, array(), Array("ID"=>$pk));
				while($arPropValue = $dbRes->Fetch())
				{
					if($propsDef[$pk]['MULTIPLE']=='Y')
					{
						$arProps[$pk][] = $arPropValue['VALUE'];
					}
					else
					{
						$arProps[$pk] = $arPropValue['VALUE'];
					}
				}
				if(isset($arProps[$pk]))
				{
					if($propsDef[$pk]['PROPERTY_TYPE']=='F')
					{
						if(is_array($arProps[$pk]))
						{
							foreach($arProps[$pk] as $k2=>$v2)
							{
								$arProps[$pk][$k2] = \CFile::MakeFileArray($v2);
							}
						}
						else
						{
							$arProps[$pk] = \CFile::MakeFileArray($arProps[$pk]);
						}
					}
				}
			}
			if(isset($arProps[$pk]))
			{
				if($propsDef[$pk]['MULTIPLE']=='Y')
				{
					$arVal = $this->GetMultipleProperty($prop, $pk);
					foreach($arProps[$pk] as $k2=>$v2)
					{
						if(isset($arVal[$k2]))
						{
							if(is_array($v2) && isset($v2['VALUE']))
							{
								$v2['DESCRIPTION'] = $arVal[$k2];
								$arProps[$pk][$k2] = $v2;
							}
							else
							{
								$arProps[$pk][$k2] = array(
									'VALUE' => $v2,
									'DESCRIPTION' => $arVal[$k2]
								);
							}
						}
					}
				}
				else
				{
					if(is_array($arProps[$pk]) && isset($arProps[$pk]['VALUE']))
					{
						$arProps[$pk]['DESCRIPTION'] = $prop;
					}
					else
					{
						$arProps[$pk] = array(
							'VALUE' => $arProps[$pk],
							'DESCRIPTION' => $prop
						);
					}
				}
			}
			unset($arProps[$k]);
		}
		
		/*Delete unchanged props*/
		if(!empty($arProps) && $this->params['ELEMENT_IMAGES_FORCE_UPDATE']!='Y')
		{
			$arOldProps = array();
			$dbRes = \CIBlockElement::GetProperty($IBLOCK_ID, $ID, array(), Array("ID"=>array_keys($arProps)));
			while($arr = $dbRes->Fetch())
			{
				if(isset($arProps[$arr['ID']]))
				{
					if($arr['MULTIPLE']=='Y')
					{
						if(!is_array($arOldProps[$arr['ID']])) $arOldProps[$arr['ID']] = array();
						$arOldProps[$arr['ID']][] = (strlen($arr['DESCRIPTION']) > 0 ? array('VALUE' => $arr['VALUE'], 'DESCRIPTION' => $arr['DESCRIPTION']) : $arr['VALUE']);
					}
					else
					{
						$arOldProps[$arr['ID']] = (strlen($arr['DESCRIPTION']) > 0 ? array('VALUE' => $arr['VALUE'], 'DESCRIPTION' => $arr['DESCRIPTION']) : $arr['VALUE']);
					}
				}
			}
			foreach($arOldProps as $pk=>$pv)
			{
				$fsKey = ($this->conv->GetSkuMode() ? 'OFFER_' : '').'IP_PROP'.$pk;
				$saveOldVals = (bool)($this->fieldSettings[$fsKey]['MULTIPLE_SAVE_OLD_VALUES']=='Y');

				if($propsDef[$pk]['MULTIPLE']=='Y' && $propsDef[$pk]['PROPERTY_TYPE']!='F' && $saveOldVals)
				{
					foreach($arProps[$pk] as $fpk2=>$fpv2)
					{
						foreach($pv as $fpk=>$fpv)
						{
							if(is_array($fpv2) && isset($fpv2['VALUE']) && ((is_array($fpv) && $fpv2['VALUE']==$fpv['VALUE']) || (!is_array($fpv) && $fpv2['VALUE']==$fpv)))
							{
								unset($pv[$fpk]);
								break;
							}
							elseif($fpv==$fpv2)
							{
								unset($arProps[$pk][$fpk2]);
								break;
							}
						}
					}
					$arProps[$pk] = array_merge($pv, $arProps[$pk]);
					$arProps[$pk] = array_diff($arProps[$pk], array(''));
				}
				
				if($arProps[$pk]==$pv && (is_array($arProps[$pk]) || is_array($pv) || strlen($arProps[$pk])==strlen($pv)))
				{
					unset($arProps[$pk]);
				}
				else
				{
					if($propsDef[$pk]['PROPERTY_TYPE']=='F')
					{
						if($propsDef[$pk]['MULTIPLE']=='Y')
						{
							if($saveOldVals)
							{
								foreach($arProps[$pk] as $fpk2=>$fpv2)
								{
									foreach($pv as $fpk=>$fpv)
									{
										if(!$this->IsChangedImage($fpv, $fpv2))
										{
											unset($arProps[$pk][$fpk2]);
											break;
										}
									}
								}
								$arProps[$pk] = array_merge($pv, $arProps[$pk]);
								foreach($arProps[$pk] as $fpk2=>$fpv2)
								{
									if(is_numeric($fpv2)) $arProps[$pk][$fpk2] = \CFile::MakeFileArray($fpv2);
								}
								$arProps[$pk] = array_diff($arProps[$pk], array(''));
							}
							
							if(count($pv)==count($arProps[$pk]))
							{
								$isChange = false;
								foreach($pv as $fpk=>$fpv)
								{
									if($this->IsChangedImage($fpv, $arProps[$pk][$fpk]))
									{
										$isChange = true;
									}
								}
								if(!$isChange)
								{
									unset($arProps[$pk]);
								}
							}
						}
						else
						{
							if(!$this->IsChangedImage($pv, $arProps[$pk]))
							{
								unset($arProps[$pk]);
							}
						}
					}
				}
			}
		}
		/*/Delete unchanged props*/

		if(!empty($arProps))
		{
			\CIBlockElement::SetPropertyValuesEx($ID, $IBLOCK_ID, $arProps);
			$this->logger->AddElementChanges('IP_PROP', $arProps, $arOldProps);
			$this->SetProductQuantity($ID, $IBLOCK_ID);
			
			if($needUpdate)
			{
				$el = new \CIblockElement();
				$el->Update($ID, array(), false, true);
				$this->AddTagIblock($IBLOCK_ID);
			}
			elseif($this->params['ELEMENT_NOT_UPDATE_WO_CHANGES']=='Y')
			{
				$arFilterProp = $this->GetFilterProperties($IBLOCK_ID);
				if(!empty($arFilterProp) && count(array_intersect(array_keys($arProps), $arFilterProp)) > 0 && class_exists('\Bitrix\Iblock\PropertyIndex\Manager'))
				{
					\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ID);
				}
			}
		}
	}
	
	public function GetFilterProperties($IBLOCK_ID)
	{
		if(!isset($this->arFilterProperties)) $this->arFilterProperties = array();
		if(!isset($this->arFilterProperties[$IBLOCK_ID]))
		{
			$arProps = array();
			if(class_exists('\Bitrix\Iblock\SectionPropertyTable'))
			{
				$dbRes = \Bitrix\Iblock\SectionPropertyTable::getList(array('filter'=>array('IBLOCK_ID'=>$IBLOCK_ID, 'SMART_FILTER'=>'Y'), 'group'=>array('PROPERTY_ID'), 'select'=>array('PROPERTY_ID')));
				while($arr = $dbRes->fetch())
				{
					$arProps[] = $arr['PROPERTY_ID'];
				}
			}
			$this->arFilterProperties[$IBLOCK_ID] = $arProps;
		}
		return $this->arFilterProperties[$IBLOCK_ID];
	}
	
	public function GetPropValue($arProp, $val)
	{
		$fieldSettings = (isset($this->fieldSettings['OFFER_IP_PROP'.$arProp['ID']]) ? $this->fieldSettings['OFFER_IP_PROP'.$arProp['ID']] : $this->fieldSettings['IP_PROP'.$arProp['ID']]);
		if(is_array($val) && isset($val[0])) $val = $val[0];
		if($arProp['PROPERTY_TYPE']=='F')
		{
			$picSettings = array();
			if($fieldSettings['PICTURE_PROCESSING'])
			{
				$picSettings = $fieldSettings['PICTURE_PROCESSING'];
			}
			$val = $this->GetFileArray($val, $picSettings, $arProp);
			if($arProp['MULTIPLE']=='Y' && is_array($val) && array_key_exists('0', $val)) $val = array('VALUES'=>$val);
		}
		elseif($arProp['PROPERTY_TYPE']=='L')
		{
			$val = $this->GetListPropertyValue($arProp, $val);
		}
		elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
		{
			$val = $this->GetHighloadBlockValue($arProp, $val, true);
		}
		elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='HTML')
		{
			if($fieldSettings['TEXT_HTML']=='text') $val = array('VALUE'=>array('TEXT'=>$val, 'TYPE'=>'TEXT'));
			elseif($fieldSettings['TEXT_HTML']=='html') $val = array('VALUE'=>array('TEXT'=>$val, 'TYPE'=>'HTML'));
		}
		elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='video')
		{
			if(!is_array($val))
			{
				$width = (int)$this->GetFloatVal($fieldSettings['VIDEO_WIDTH']);
				$height = (int)$this->GetFloatVal($fieldSettings['VIDEO_HEIGHT']);
				$val = Array('VALUE' => Array(
					'PATH' => $val,
					'WIDTH' => ($width > 0 ? $width : 400),
					'HEIGHT' => ($height > 0 ? $height : 300),
					'TITLE' => '',
					'DURATION' => '',
					'AUTHOR' => '',
					'DATE' => '',
					'DESC' => ''
				));
			}
		}
		elseif($arProp['USER_TYPE']=='DateTime' || $arProp['USER_TYPE']=='Date')
		{
			$val = $this->GetDateVal($val);
		}
		elseif($arProp['PROPERTY_TYPE']=='N')
		{
			/*if(preg_match('/\d/', $val)) $val = $this->GetFloatVal($val);
			else $val = '';*/
		}
		elseif($arProp['PROPERTY_TYPE']=='E')
		{
			$val = $this->GetIblockElementValue($arProp, $val, $fieldSettings, true);
		}
		elseif($arProp['PROPERTY_TYPE']=='G')
		{
			$relField = $fieldSettings['REL_SECTION_FIELD'];
			if((!$relField || $relField=='ID') && !is_numeric($val))
			{
				$relField = 'NAME';
			}
			if($relField && $relField!='ID' && $val && $arProp['LINK_IBLOCK_ID'])
			{
				$arFilter = array(
					'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
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
	
	public function GetListPropertyValue($arProp, $val)
	{
		if(is_string($val)) $val = array('VALUE'=>$val);
		if($val['VALUE']!==false && strlen($val['VALUE']) > 0)
		{
			$cacheVals = $val['VALUE'];
			if(!isset($this->propVals[$arProp['ID']][$cacheVals]))
			{
				$dbRes = \CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID"=>$arProp['ID'], "VALUE"=>$val['VALUE']));
				if($arPropEnum = $dbRes->Fetch())
				{
					$arPropFields = $val;
					unset($arPropFields['VALUE']);
					$this->CheckXmlIdOfListProperty($arPropFields, $arProp['ID']);
					if(count($arPropFields) > 0)
					{
						$ibpenum = new \CIBlockPropertyEnum;
						$ibpenum->Update($arPropEnum['ID'], $arPropFields);
					}
					$this->propVals[$arProp['ID']][$cacheVals] = $arPropEnum['ID'];
				}
				else
				{
					if(!isset($val['XML_ID'])) $val['XML_ID'] = $this->Str2Url($val['VALUE']);
					$this->CheckXmlIdOfListProperty($val, $arProp['ID']);
					$ibpenum = new \CIBlockPropertyEnum;
					if($propId = $ibpenum->Add(array_merge($val, array('PROPERTY_ID'=>$arProp['ID']))))
					{
						$this->propVals[$arProp['ID']][$cacheVals] = $propId;
					}
					else
					{
						$this->propVals[$arProp['ID']][$cacheVals] = false;
					}
				}
			}
			$val = $this->propVals[$arProp['ID']][$cacheVals];
		}
		return (!is_array($val) ? $val : false);
	}
	
	public function CheckXmlIdOfListProperty(&$val, $propID)
	{
		if(isset($val['XML_ID']))
		{
			$val['XML_ID'] = trim($val['XML_ID']);
			if(strlen($val['XML_ID'])==0)
			{
				unset($val['XML_ID']);
			}
			else
			{
				$dbRes2 = \CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID"=>$propID, "XML_ID"=>$val['XML_ID']));
				if($arPropEnum2 = $dbRes2->Fetch())
				{
					unset($val['XML_ID']);
				}
			}
		}
	}
	
	public function GetDefaultElementFields(&$arElement, $iblockFields)
	{
		$arDefaultFields = array('ACTIVE', 'ACTIVE_FROM', 'ACTIVE_TO', 'NAME', 'PREVIEW_TEXT_TYPE', 'PREVIEW_TEXT', 'DETAIL_TEXT_TYPE', 'DETAIL_TEXT');
		foreach($arDefaultFields as $fieldName)
		{
			if(!isset($arElement[$fieldName]) && $iblockFields[$fieldName]['IS_REQUIRED']=='Y' && isset($iblockFields[$fieldName]['DEFAULT_VALUE']) && is_string($iblockFields[$fieldName]['DEFAULT_VALUE']) && strlen($iblockFields[$fieldName]['DEFAULT_VALUE']) > 0)
			{
				$arElement[$fieldName] = $iblockFields[$fieldName]['DEFAULT_VALUE'];
				if($fieldName=='ACTIVE_FROM')
				{
					if($arElement[$fieldName]=='=now') $arElement[$fieldName] = ConvertTimeStamp(false, "FULL");
					elseif($arElement[$fieldName]=='=today') $arElement[$fieldName] = ConvertTimeStamp(false, "SHORT");
					else unset($arElement[$fieldName]);
				}
				elseif($fieldName=='ACTIVE_TO')
				{
					if((int)$arElement[$fieldName] > 0) $arElement[$fieldName] = ConvertTimeStamp(time()+(int)$arElement[$fieldName]*24*60*60, "FULL");
				}
			}
		}
		$this->GenerateElementCode($arElement, $iblockFields);
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
	
	public function GetIblockFields($IBLOCK_ID)
	{
		if(!$this->iblockFields[$IBLOCK_ID])
		{
			$this->iblockFields[$IBLOCK_ID] = \CIBlock::GetFields($IBLOCK_ID);
		}
		return $this->iblockFields[$IBLOCK_ID];
	}
	
	public function GetIblockSectionFields($IBLOCK_ID)
	{
		if(!isset($this->iblockSectionFields[$IBLOCK_ID]))
		{
			$dbRes = \CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'IBLOCK_'.$IBLOCK_ID.'_SECTION'));
			$arProps = array();
			while($arr = $dbRes->Fetch())
			{
				$arProps[$arr['FIELD_NAME']] = $arr;
			}
			$this->iblockSectionFields[$IBLOCK_ID] = $arProps;
		}
		return $this->iblockSectionFields[$IBLOCK_ID];
	}
	
	public function GetIblockElementValue($arProp, $val, $fsettings, $bAdd = false, $allowNF = false, $allowMultiple = false)
	{
		if(strlen($val)==0) return $val;
		$relField = $fsettings['REL_ELEMENT_FIELD'];
		if((!$relField || $relField=='IE_ID') && !is_numeric($val))
		{
			$relField = 'IE_NAME';
			$bAdd = false;
		}
		if($relField && $relField!='IE_ID' && $arProp['LINK_IBLOCK_ID'])
		{
			$arFilter = array('IBLOCK_ID'=>$arProp['LINK_IBLOCK_ID'], 'CHECK_PERMISSIONS' => 'N');
			if(strpos($relField, 'IE_')===0)
			{
				$arFilter[substr($relField, 3)] = $val;
			}
			elseif(strpos($relField, 'IP_PROP')===0)
			{
				$uid = substr($relField, 7);
				if($propsDef[$uid]['PROPERTY_TYPE']=='L')
				{
					$arFilter['PROPERTY_'.$uid.'_VALUE'] = $val;
				}
				else
				{
					/*if($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
					{
						$val = $this->GetHighloadBlockValue($arProp, $val);
					}*/
					$arFilter['PROPERTY_'.$uid] = $val;
				}
			}

			$dbRes = \Bitrix\EsolImportxml\DataManager\IblockElement::GetList($arFilter, array('ID'), array('ID'=>'ASC'), ($allowMultiple ? false : 1));
			//$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFilter, false, ($allowMultiple ? false : array('nTopCount'=>1)), array('ID'));
			if($arElem = $dbRes->Fetch())
			{
				$val = $arElem['ID'];
				if($allowMultiple)
				{
					$arVals = array();
					while($arElem = $dbRes->Fetch())
					{
						$arVals[] = $arElem['ID'];
					}
					if(count($arVals) > 0)
					{
						array_unshift($arVals, $val);
						$val = array_values($arVals);
					}
				}
			}
			elseif($bAdd && $arFilter['NAME'] && $arFilter['IBLOCK_ID'])
			{
				$iblockFields = $this->GetIblockFields($arFilter['IBLOCK_ID']);
				$this->GenerateElementCode($arFilter, $iblockFields);
				$el = new \CIblockElement();
				$val = $el->Add($arFilter, false, true, true);
				$this->AddTagIblock($arFilter['IBLOCK_ID']);
			}
		}

		return $val;
	}
	
	public function GetHighloadBlockValue($arProp, $val, $bAdd = false)
	{
		if($val && Loader::includeModule('highloadblock') && $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])
		{
			$arFields = $val;
			if(!is_array($arFields))
			{
				$arFields = array('UF_NAME'=>$arFields);
			}

			$arItems = array();
			if(is_array($arFields['UF_NAME']) || is_array($arFields['UF_XML_ID']))
			{
				if(!is_array($arFields['UF_NAME'])) $arFields['UF_NAME'] = array($arFields['UF_NAME']);
				else $arFields['UF_NAME'] = array_values($arFields['UF_NAME']);
				if(!is_array($arFields['UF_XML_ID'])) $arFields['UF_XML_ID'] = array($arFields['UF_XML_ID']);
				else $arFields['UF_XML_ID'] = array_values($arFields['UF_XML_ID']);
				$cnt = max(count($arFields['UF_NAME']), count($arFields['UF_XML_ID']));
				for($i=0; $i<$cnt; $i++)
				{
					$arItem = array();
					foreach($arFields as $k=>$v)
					{
						if(is_array($v) && isset($v[$i])) $arItem[$k] = $v[$i];
						elseif(!is_array($v)) $arItem[$k] = $v;
					}
					$arItems[] = $arItem;
				}
			}
			else
			{
				$arItems[] = $arFields;
			}

			$arResult = array();
			foreach($arItems as $arFields)
			{
				if($arFields['UF_XML_ID']) $cacheKey = 'UF_XML_ID_'.$arFields['UF_XML_ID'];
				else $cacheKey = 'UF_NAME_'.$arFields['UF_NAME'];

				if(!isset($this->propVals[$arProp['ID']][$cacheKey]))
				{
					if(!$this->hlbl[$arProp['ID']] || !$this->hlblFields[$arProp['ID']])
					{
						$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
						if(!$hlblock) continue;
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
					
					if((!isset($arFields['UF_NAME']) || strlen(trim($arFields['UF_NAME']))==0) && (!isset($arFields['UF_XML_ID']) || strlen(trim($arFields['UF_XML_ID']))==0)) continue;
					$this->PrepareHighLoadBlockFields($arFields, $arHLFields);
					
					if($arFields['UF_XML_ID']) $arFilter = array("UF_XML_ID"=>$arFields['UF_XML_ID']);
					else $arFilter = array("UF_NAME"=>$arFields['UF_NAME']);
					$dbRes2 = $entityDataClass::GetList(array('filter'=>$arFilter, 'select'=>array('ID', 'UF_XML_ID'), 'limit'=>1));
					if($arr2 = $dbRes2->Fetch())
					{
						if(count($arFields) > 1 && $bAdd)
						{
							$entityDataClass::Update($arr2['ID'], $arFields);
						}
						$cacheVal = $this->propVals[$arProp['ID']][$cacheKey] = $arr2['UF_XML_ID'];
					}
					else
					{
						if(!isset($arFields['UF_NAME']) || strlen(trim($arFields['UF_NAME']))==0) continue;
						if(!isset($arFields['UF_XML_ID']) || strlen(trim($arFields['UF_XML_ID']))==0) $arFields['UF_XML_ID'] = $this->Str2Url($arFields['UF_NAME']);
						if($bAdd)
						{
							if($entityDataClass::Add($arFields))
								$cacheVal = $this->propVals[$arProp['ID']][$cacheKey] = $arFields['UF_XML_ID'];
							else $cacheVal = $this->propVals[$arProp['ID']][$cacheKey] = false;
						}
						else $cacheVal = $arFields['UF_XML_ID'];
					}
				}
				else
				{
					$cacheVal = $this->propVals[$arProp['ID']][$cacheKey];
				}
				$arResult[] = $cacheVal;
			}

			if(empty($arResult)) return false;
			elseif(count($arResult)==1) return current($arResult);
			else return $arResult;
		}
		return $val;
	}
	
	public function PrepareHighLoadBlockFields(&$arFields, $arHLFields)
	{
		foreach($arFields as $k=>$v)
		{
			if(!isset($arHLFields[$k]))
			{
				unset($arFields[$k]);
			}
			$type = $arHLFields[$k]['USER_TYPE_ID'];
			if($type=='file')
			{
				$arFields[$k] = $this->GetFileArray($v);
				if(empty($arFields[$k])) unset($arFields[$k]);
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
			elseif($type=='hlblock')
			{
				$arFields[$k] = $this->GetHLHLValue($v, $arHLFields[$k]['SETTINGS']);
			}
			if($arHLFields[$k]['MULTIPLE']=='Y' && !is_array($arFields[$k]))
			{
				$arFields[$k] = array($arFields[$k]);
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
	
	public function PrepareProductAdd(&$arFieldsProduct, $ID, $IBLOCK_ID)
	{
		if(!empty($arFieldsProduct)) return;
		if(!isset($this->catalogIblocks)) $this->catalogIblocks = array();
		if(!isset($this->catalogIblocks[$IBLOCK_ID]))
		{
			$this->catalogIblocks[$IBLOCK_ID] = false;
			if(is_callable(array('\Bitrix\Catalog\CatalogIblockTable', 'getList')))
			{
				if($arCatalog = \Bitrix\Catalog\CatalogIblockTable::getList(array('filter'=>array('IBLOCK_ID'=>$IBLOCK_ID), 'limit'=>1))->Fetch())
				{
					$this->catalogIblocks[$IBLOCK_ID] = true;
				}				
			}
		}
		if($this->catalogIblocks[$IBLOCK_ID]) $arFieldsProduct['ID'] = $ID;
	}
	
	public function SaveProduct($ID, $IBLOCK_ID, $arProduct, $arPrices, $arStores, $parentID=false)
	{
		$this->productor->SaveProduct($ID, $IBLOCK_ID, $arProduct, $arPrices, $arStores, $parentID);
	}
	
	public function SetProductQuantity($ID, $IBLOCK_ID=0)
	{
		$this->productor->SetProductQuantity($ID, $IBLOCK_ID);
	}
	
	public function SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer = false)
	{
		if(!isset($this->discountManager))
			$this->discountManager = new \Bitrix\EsolImportxml\DataManager\Discount($this);
		$this->discountManager->SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer);
	}
	
	public function GetMeasureByStr($val)
	{
		if(!$val) return $val;
		if(!isset($this->measureList) || !is_array($this->measureList))
		{
			$this->measureList = array();
			$dbRes = \CCatalogMeasure::getList(array(), array());
			while($arr = $dbRes->Fetch())
			{
				$this->measureList[$arr['ID']] = array_map('ToLower', $arr);
			}
		}
		$valCmp = trim(ToLower($val));
		foreach($this->measureList as $k=>$v)
		{
			if(in_array($valCmp, array($v['MEASURE_TITLE'], $v['SYMBOL_RUS'], $v['SYMBOL_INTL'], $v['SYMBOL_LETTER_INTL'])))
			{
				return $k;
			}
		}
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
		elseif(preg_match('/^\$\{[\'"](([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*)[\'"]\}$/', $m[0], $m2))
		{
			if(!isset($this->convParams)) $this->convParams = array();
			$this->convParams[$m2[1]] = $this->GetValueByXpath($m2[1]);
			$quot = substr(ltrim($m2[0], '${ '), 0, 1);
			return '$this->convParams['.$quot.$m2[1].$quot.']';
		}
		elseif($m[0]=='#HASH#')
		{
			$hash = md5(serialize($this->currentItemValues).serialize($this->params['FIELDS']).serialize($this->fparams));
			return $hash;
		}
		elseif(in_array($m[0], $this->rcurrencies))
		{
			$arRates = $this->GetCurrencyRates();
			$k = trim($m[0], '#');
			return (isset($arRates[$k]) ? floatval($arRates[$k]) : 1);
		}
	}
	
	public function GetValueByXpath($xpath, $simpleXmlObj=null, $singleVal=false)
	{
		if(preg_match('/^\d+$/', $xpath) && isset($this->currentItemValues[$xpath]))
		{
			$val = $this->currentItemValues[$xpath];
			if(is_array($val))
			{
				if($singleVal) $val = current($val);
				elseif(count(preg_grep('/\D/', array_keys($val)))==0) $val = implode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $val);
			}
			return $val;
		}
		if(preg_match('/^[\d,]*$/', $xpath))
		{
			return '{'.$xpath.'}';
		}
		
		$val = '';
		
		/*if(strlen($xpath) > 0) $arPath = explode('/', $xpath);
		else $arPath = array();
		$attr = $this->GetPathAttr($arPath);*/
		$arXPath = $this->GetXPathParts($xpath);
		$curXpath2 = $arXPath['xpath'];
		$subXpath = $arXPath['subpath'];
		$attr = $arXPath['attr'];
		$currentXmlObj = $this->currentXmlObj;
		if(isset($simpleXmlObj)) $currentXmlObj = $simpleXmlObj;
		
		if(strlen($curXpath2) > 0)
		{
			//$curXpath = '/'.ltrim($curXpath2, '/');
			$curXpath = ltrim($curXpath2, '/');
			if(strpos($curXpath, '.')!==0) $curXpath = '/'.$curXpath;
			if(substr($curXpath2, 0, 2)=='//') $curXpath = $curXpath2;
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
				if(strpos($curXpath, $this->xpath)===0)
				{
					//$curXpath = $this->ReplaceXpath($curXpath);
					$curXpath = substr($curXpath, strlen($this->xpath) + 1);
				}
				elseif(isset($this->xmlPartObjects[$curXpath2]))
				{
					//$currentXmlObj = $this->xmlPartObjects[$curXpath2]->xpath($subXpath);
					$currentXmlObj = $this->Xpath($this->xmlPartObjects[$curXpath2], $subXpath);
					$curXpath = '';
				}
				elseif(substr($curXpath, 0, 2)=='//')
				{
					if(!isset($this->xmlSingleElems[$curXpath]))
					{
						$this->xmlSingleElems[$curXpath] = $this->GetPartXmlObject($curXpath, false);
					}
					$currentXmlObj = $this->xmlSingleElems[$curXpath];
					$curXpath = '';
				}
				elseif(substr($curXpath, 0, 1)=='.')
				{
					$node = $this->GetCurrentFieldNode();
					if($node!==false && ($tmpXmlObj = $this->Xpath($node, $curXpath)))
					{
						$currentXmlObj = $tmpXmlObj;
						$curXpath = '';
					}
				}
			}

			//if(strlen($curXpath) > 0) $simpleXmlObj2 = $currentXmlObj->xpath($curXpath);
			if(strlen($curXpath) > 0) $simpleXmlObj2 = $this->Xpath($currentXmlObj, ltrim($curXpath, '/'));
			else $simpleXmlObj2 = $currentXmlObj;
			if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
		}
		else $simpleXmlObj2 = $currentXmlObj;
		//if(is_array($simpleXmlObj2)) $simpleXmlObj2 = current($simpleXmlObj2);
		
		if(is_array($simpleXmlObj2))
		{
			$arVals = array();
			foreach($simpleXmlObj2 as $sxml)
			{
				if($attr!==false)
				{
					if(is_callable(array($sxml, 'attributes')))
					{
						$arVals[] = (string)$sxml->attributes()->{$attr};
					}
				}
				else
				{
					$arVals[] = (string)$sxml;					
				}
			}
			if($singleVal) $val = current($arVals);
			else $val = implode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arVals);
		}
		else
		{
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
		}
		
		$val = $this->GetRealXmlValue($val);		
		return $val;
	}
	
	public function GetCurrentFieldNode()
	{
		$key = $this->currentFieldKey;
		$arFields = $this->params['FIELDS'];
		if(!array_key_exists($key, $arFields)) return false;
		$field = $arFields[$key];
		list($xpath, $fieldName) = explode(';', $field, 2);
		$simpleXmlObj = $this->currentXmlObj;
		
		$conditionIndex = trim($this->fparams[$key]['INDEX_LOAD_VALUE']);
		$conditions = $this->fparams[$key]['CONDITIONS'];
		if(!is_array($conditions)) $conditions = array();
		foreach($conditions as $k2=>$v2)
		{
			if(preg_match('/^\{(\S*)\}$/', $v2['CELL'], $m))
			{
				$conditions[$k2]['XPATH'] = substr($m[1], strlen(trim($this->xpath, '/')) + 1);
			}
		}
		
		$xpath = substr($xpath, strlen(trim($this->xpath, '/')) + 1);
		$arPath = array_diff(explode('/', $xpath), array(''));
		$attr = $this->GetPathAttr($arPath);
		if(count($arPath) > 0)
		{
			$simpleXmlObj2 = $this->Xpath($simpleXmlObj, implode('/', $arPath));
			if(count($simpleXmlObj2)==1) $simpleXmlObj2 = current($simpleXmlObj2);
		}
		else $simpleXmlObj2 = $simpleXmlObj;
		
		$val = false;
		if(is_array($simpleXmlObj2))
		{
			$val = array();
			foreach($simpleXmlObj2 as $k=>$v)
			{
				if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $v, $k))
				{
					$val[] = $v;
				}
			}
			if(is_numeric($conditionIndex)) $val = $val[$conditionIndex - 1];
			elseif(count($val)==1) $val = current($val);
		}
		else
		{
			if($this->CheckConditions($conditions, $xpath, $simpleXmlObj, $simpleXmlObj2))
			{
				$val = $simpleXmlObj2;
			}
		}
	
		if(is_array($val))
		{
			if(array_key_exists($this->currentFieldIndex, $val)) $val = $val[$this->currentFieldIndex];
			else $val = current($val);
		}
		if(!($val instanceof \SimpleXMLElement)) $val = false;
		return $val;
	}
	
	public function Xpath($simpleXmlObj, $xpath)
	{
		$xpath = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($xpath, $this->siteEncoding, $this->fileEncoding);
		if(preg_match('/((^|\/)[^\/]+):/', $xpath, $m))
		{
			if(strpos($m[1], '/')===0) $xpath = '/'.substr($xpath, strlen($m[1]) + 1);
			$nss = $simpleXmlObj->getNamespaces(true);
			$nsKey = trim($m[1], '/');
			if(isset($nss[$nsKey]))
			{
				$simpleXmlObj->registerXPathNamespace($nsKey, $nss[$nsKey]);
			}
		}
		$xpath = trim($xpath);
		if(strlen($xpath) > 0 && $xpath!='.') return $simpleXmlObj->xpath($xpath);
		else return $simpleXmlObj;
	}
	
	public function ApplyConversions($val, $arConv, $arItem, $field=false, $iblockFields=array())
	{
		$fieldName = $fieldKey = $fieldIndex = false;
		if(!is_array($field))
		{
			$fieldName = $field;
		}
		else
		{
			if($field['NAME']) $fieldName = $field['NAME'];
			if(strlen($field['KEY']) > 0) $fieldKey = $field['KEY'];
			if(strlen($field['INDEX']) > 0) $fieldIndex = $field['INDEX'];
		}
		$this->currentFieldKey = $fieldKey;
		$this->currentFieldIndex = $fieldIndex;
		
		if(is_array($arConv))
		{
			$execConv = false;
			$this->currentItemValues = $arItem;
			$prefixPattern = '/(\{([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*\}|'.'\$\{[\'"]([^\s\}]*[\'"][^\'"\}]*[\'"])*[^\s\}]*[\'"]\}|#HASH#|'.implode('|', $this->rcurrencies).')/';
			foreach($arConv as $k=>$v)
			{
				$condVal = $val;

				if(preg_match('/^\{(\S*)\}$/', $v['CELL'], $m))
				{
					$condVal = $this->GetValueByXpath($m[1]);
				}

				if(strlen($v['FROM']) > 0) $v['FROM'] = preg_replace_callback($prefixPattern, array($this, 'ConversionReplaceValues'), $v['FROM']);
				if($v['CELL']=='ELSE') $v['WHEN'] = '';
				$condValNum = $this->GetFloatVal($condVal);
				$fromNum = $this->GetFloatVal($v['FROM']);
				if(($v['CELL']=='ELSE' && !$execConv)
					|| ($v['WHEN']=='EQ' && $condVal==$v['FROM'])
					|| ($v['WHEN']=='NEQ' && $condVal!=$v['FROM'])
					|| ($v['WHEN']=='GT' && $condValNum > $fromNum)
					|| ($v['WHEN']=='LT' && $condValNum < $fromNum)
					|| ($v['WHEN']=='GEQ' && $condValNum >= $fromNum)
					|| ($v['WHEN']=='LEQ' && $condValNum <= $fromNum)
					|| ($v['WHEN']=='CONTAIN' && strpos($condVal, $v['FROM'])!==false)
					|| ($v['WHEN']=='NOT_CONTAIN' && strpos($condVal, $v['FROM'])===false)
					|| ($v['WHEN']=='REGEXP' && preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
					|| ($v['WHEN']=='NOT_REGEXP' && !preg_match('/'.ToLower($v['FROM']).'/i', ToLower($condVal)))
					|| ($v['WHEN']=='EMPTY' && strlen($condVal)==0)
					|| ($v['WHEN']=='NOT_EMPTY' && strlen($condVal) > 0)
					|| ($v['WHEN']=='ANY'))
				{
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
								$arParams = $iblockFields[$paramName]['DEFAULT_VALUE'];
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
	
	public function GetOfferParentId()
	{
		return (isset($this->offerParentId) ? $this->offerParentId : false);
	}
	
	public function GetFieldSettings($key)
	{
		$fieldSettings = $this->fieldSettings[$key];
		if(!is_array($fieldSettings)) $fieldSettings = array();
		return $fieldSettings;
	}
	
	public function GetCurrentIblock()
	{
		return $this->params['IBLOCK_ID'];
	}
	
	public function GetCachedOfferIblock($IBLOCK_ID)
	{
		if(!$this->iblockoffers || !isset($this->iblockoffers[$IBLOCK_ID]))
		{
			$this->iblockoffers[$IBLOCK_ID] = \Bitrix\EsolImportxml\Utils::GetOfferIblock($IBLOCK_ID, true);
		}
		return $this->iblockoffers[$IBLOCK_ID];
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
			&& file_exists($_SERVER['DOCUMENT_ROOT'].\Bitrix\Main\IO\Path::convertLogicalToPhysical($arFile['SRC']))
			&& (!isset($arNewFile['description']) || $arNewFile['description']==$arFile['DESCRIPTION']))
		{
			return false;
		}
		return true;
	}
	
	public function GetFloatVal($val, $precision=0)
	{
		if(is_array($val)) $val = current($val);
		$val = floatval(preg_replace('/[^\d\.\-]+/', '', str_replace(',', '.', $val)));
		if($precision > 0) $val = round($val, $precision);
		return $val;
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
	
	public function Trim($str)
	{
		$str = trim($str);
		$str = preg_replace('/(^(\xC2\xA0|\s)+|(\xC2\xA0|\s)+$)/s', '', $str);
		return $str;
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
	
	public function ClearCompositeCache($link='')
	{
		if(!class_exists('\Bitrix\Main\Composite\Helper')) return;
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/cache_files_cleaner.php");
		
		if(!isset($this->compositDomains) || !is_array($this->compositDomains))
		{
			$compositeOptions = \CHTMLPagesCache::getOptions();
			$compositDomains = $compositeOptions['DOMAINS'];
			if(!is_array($compositDomains)) $compositDomains = array();
			$this->compositDomains = $compositDomains;
		}
		
		if(strlen($link) > 0 && !empty($this->compositDomains))
		{
			foreach($this->compositDomains as $host)
			{
				$page = new \Bitrix\Main\Composite\Page($link, $host);
				$page->delete();	
			}
		}
	}
	
	public function AddTagIblock($IBLOCK_ID)
	{
		$IBLOCK_ID = (int)$IBLOCK_ID;
		if($IBLOCK_ID <= 0) return;
		$this->tagIblocks[$IBLOCK_ID] = $IBLOCK_ID;
	}
	
	public function ClearIblocksTagCache($checkTime = false)
	{
		if($this->params['REMOVE_CACHE_AFTER_IMPORT']=='Y') return;
		if($checkTime && (time() - $this->timeBeginTagCache < 60)) return;
		if(is_callable(array('\CIBlock', 'clearIblockTagCache')))
		{
			if(is_callable(array('\CIBlock', 'enableClearTagCache'))) \CIBlock::enableClearTagCache();
			foreach($this->tagIblocks as $IBLOCK_ID)
			{
				\CIBlock::clearIblockTagCache($IBLOCK_ID);
			}
			if(is_callable(array('\CIBlock', 'disableClearTagCache'))) \CIBlock::disableClearTagCache();
		}
		$this->tagIblocks = array();
		$this->timeBeginTagCache = time();
	}
	
	public function GetRealXmlValue($val)
	{
		$val = \Bitrix\EsolImportxml\Utils::ConvertDataEncoding($val, $this->fileEncoding, $this->siteEncoding);
		if($this->params['HTML_ENTITY_DECODE']=='Y')
		{
			if(is_array($val))
			{
				foreach($val as $k=>$v)
				{
					$val[$k] = html_entity_decode($v, ENT_QUOTES | ENT_HTML5, $this->siteEncoding);
				}
			}
			else
			{
				$val = html_entity_decode($val, ENT_QUOTES | ENT_HTML5, $this->siteEncoding);
			}
		}
		return $val;
	}
	
	public function GetSectionPathByLink($tmpId, $sep)
	{
		$arPath = array();
		while(isset($this->sectionsTmp[$tmpId]))
		{
			array_unshift($arPath, $this->sectionsTmp[$tmpId]['NAME']);
			$tmpId = $this->sectionsTmp[$tmpId]['PARENT'];
		}
		return implode($sep, $arPath);
	}
	
	public function InSection($sectionId=false)
	{
		if(!$sectionId) return false;
		$sid = 0;
		foreach($this->params['FIELDS'] as $key=>$fieldFull)
		{
			list($xpath, $field) = explode(';', $fieldFull, 2);
			if($field=='IE_IBLOCK_SECTION_TMP_ID')
			{
				$sid = $this->currentItemValues[$key];
				break;
			}
		}
		if(!$sid || !isset($this->sectionIds[$sid])) return false;
		
		if(!isset($this->sectIdtoSectIds)) $this->sectIdtoSectIds = array();
		if(!isset($this->sectIdtoSectIds[$sid]))
		{	
			$realSectId = $this->sectionIds[$sid];
			$arRealIds = array();
			while($realSectId)
			{
				$arRealIds[] = $realSectId;
				$dbRes = \CIBlockSection::GetList(array(), array('ID'=>$realSectId, 'CHECK_PERMISSIONS' => 'N'), false, array('IBLOCK_SECTION_ID'));
				$arSect = $dbRes->Fetch();
				$realSectId = (int)$arSect['IBLOCK_SECTION_ID'];
			}
			
			$arIds = array();
			foreach($arRealIds as $id)
			{
				$id = array_search($id, $this->sectionIds);
				if($id) $arIds[] = $id;
			}
			$this->sectIdtoSectIds[$sid] = $arIds;
		}

		return (bool)in_array($sectionId, $this->sectIdtoSectIds[$sid]);
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