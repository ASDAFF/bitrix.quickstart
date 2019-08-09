<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/zip.php');

class CKDAExportExcelWriterXlsx {
    private $workSheetHandler = null;
    private $stringsHandler = null;
	private $workbookHandler = null;
	private $stylesHandler = null;

    private $fields = 0;
	private $numRows = 0;
	private $arTotalRows = array();
	private $totalCols = 0;
	private $arListIndexes = array();
    private $curCel = 0;
    private $numStrings = 0;
    private $dirPath = '';
    private $outputFile = '';
	private $tmpFile = '';
	private $titles = array();
	private $imageDir = '';
	private $curImgIndex = 1;
	private $curRelationshipIndex = 1;
	private $ee = false;
	private $arMergeCells = array();
	private $arHyperLinks = array();
	private $styleFonts = array();
	private $styleFills = array();
	private $styleFillIds = array();
	private $styleCellXfs = array();
	private $arStyles = array();
	private $arRowStyles = array();
	private $arStyleIds = array();
	private $arBorders = array();
	private $currentStyleId = 0;
	private $linkCells = array();
	private $defaultWidth = 200;
	private $defaultWidthRatio = 9;
	private $imagePixel = 7600;
	private $imageHeightRatio = 1.2;
	private $defaultRowHeight = 14.4;
	private $firstSectionLevel = 0;
	private $currentSectionLevel = 0;
	private $arDropdowns = array();
	private $arFunctions = array();
	private $titlesRowNum = 0;
	private $mergeSheets = false;
	
	function __construct($arParams = array(), $ee = false)
	{
		$this->dirPath = $arParams['TMPDIR'];
		$this->outputFile = $arParams['OUTPUTFILE'];
		$this->arListIndexes = $arParams['LIST_INDEXES'];
		reset($this->arListIndexes);
		$this->indexFirstList = current($this->arListIndexes);
		$this->indexLastList = end($this->arListIndexes);
		$this->arListIndexesFile = $this->arListIndexes;
		$this->mergeSheets = (bool)($arParams['PARAMS']['MERGE_SHEETS']=='Y');
		if($this->mergeSheets) $this->arListIndexesFile = array_slice($this->arListIndexesFile, 0, 1, true);
		//$this->fields = $arParams['FIELDS'];
		$this->arTotalRows = $arParams['ROWS'];
		//$this->tmpFile = $arParams['TMPFILE'];
		$this->arFparams = $arParams['EXTRAPARAMS'];
		$this->params = $arParams['PARAMS'];
		$this->arTitles = $arParams['PARAMS']['FIELDS_LIST_NAMES'];
		$this->arListTitle = $arParams['PARAMS']['LIST_NAME'];
		$this->imageDir = $arParams['IMAGEDIR'];
		$this->arDisplayParams = $arParams['PARAMS']['DISPLAY_PARAMS'];
		$this->arTextRowsTop = $arParams['PARAMS']['TEXT_ROWS_TOP'];
		$this->arTextRowsTop2 = $arParams['PARAMS']['TEXT_ROWS_TOP2'];
		$this->arHideColumnTitles = $arParams['PARAMS']['HIDE_COLUMN_TITLES'];
		$this->arEnableAutofilters = $arParams['PARAMS']['ENABLE_AUTOFILTER'];
		$this->arEnableProtections = $arParams['PARAMS']['ENABLE_PROTECTION'];
		$this->arLabelColors = $arParams['PARAMS']['LIST_LABEL_COLOR'];
		$this->SetEObject($ee);
		
		if($this->params['ROW_MIN_HEIGHT'])
		{
			$minHeight = $this->ee->GetFloatVal($this->params['ROW_MIN_HEIGHT']) * 0.6;
			if($minHeight > 5) $this->defaultRowHeight = $minHeight;
		}
		
		$this->arColLetters = range('A', 'Z');
		foreach(range('A', 'Z') as $v1)
		{
			foreach(range('A', 'Z') as $v2)
			{
				$this->arColLetters[] = $v1.$v2;
			}
		}
		foreach($this->arTitles as $arListTitles)
		{
			$arLetters = range('A', 'Z');
			$letter = current($arLetters);
			while(count($this->arColLetters) < count($arListTitles))
			{
				foreach(range('A', 'Z') as $v1)
				{
					foreach(range('A', 'Z') as $v2)
					{
						$this->arColLetters[] = $letter.$v1.$v2;
					}
				}
				$letter = next($arLetters);
			}
		}
		
		$funcFile = realpath(dirname(__FILE__).'/../..').'/lib/PHPExcel/PHPExcel/locale/ru/functions';
		if(file_exists($funcFile))
		{
			$fileContent = file_get_contents($funcFile);
			if((!defined('BX_UTF') || !BX_UTF) && CUtil::DetectUTF8($fileContent))
			{
				$fileContent = \Bitrix\Main\Text\Encoding::convertEncoding($fileContent, 'UTF-8', 'CP1251');
			}
			elseif((defined('BX_UTF') && BX_UTF) && !CUtil::DetectUTF8($fileContent))
			{
				$fileContent = \Bitrix\Main\Text\Encoding::convertEncoding($fileContent, 'CP1251', 'UTF-8');
			}
			$arLines = explode("\r\n", $fileContent);
			$arFunctions = array();
			foreach($arLines as $buffer)
			{
				$buffer = trim($buffer);
				if(($pos = strpos($buffer, '#'))!==false) $buffer = substr($buffer, 0, $pos);
				if(strpos($buffer, '=')!==false)
				{
					$arBuffer = array_diff(array_map('trim', explode('=', $buffer)), array(''));
					if(count($arBuffer)==2)
					{
						$arFunctions[current($arBuffer)] = end($arBuffer);
					}
				}
			}
			uasort($arFunctions, create_function('$a,$b', 'return strlen($a)<strlen($b) ? 1 : -1;'));
			$this->arFunctions = $arFunctions;
		}
	}
	
	public function SetEObject($ee)
	{
		$this->ee = $ee;
		if(!is_object($this->ee))
		{
			$this->ee = new CKDAExportExcel();
		}
	}
	
	public function Save()
	{
		$this->openExcelWriter();
		
		foreach($this->arListIndexes as $indexKey=>$listIndex)
		{
			if(isset($this->currentListIndexKey) && $this->currentListIndexKey > $indexKey) continue;
			if($this->openWorkSheet($listIndex)===false) continue;
		
			$arFields = $this->fields;
			$fieldsCount = $this->totalCols = count($arFields);
			
			$this->arColsWidth = array();
			$this->allColsWidth = 0;
			for($i=1; $i<=$this->totalCols; $i++)
			{
				$colWidth = $this->defaultWidth;
				if(isset($this->fparams[$i-1]['DISPLAY_WIDTH']) && (int)$this->fparams[$i-1]['DISPLAY_WIDTH'] > 0) $colWidth = (int)$this->fparams[$i-1]['DISPLAY_WIDTH'];
				$this->arColsWidth[$i-1] = $colWidth;
				$this->allColsWidth += $colWidth;
			}
			
			$this->linkCells = array();
			foreach($this->fparams as $k=>$v)
			{
				if(isset($v['CONVERSION']) && is_array($v['CONVERSION']))
				{
					foreach($v['CONVERSION'] as $k2=>$v2)
					{
						if($v2['THEN']=='ADD_LINK') $this->linkCells[$k] = $k;
					}
				}
			}
		
			$handle = fopen($this->tmpFile, 'r');
			if(isset($this->currentListIndexKey) && $this->currentListIndexKey == $indexKey && isset($this->currentFilePosition))
			{
				fseek($handle, $this->currentFilePosition);
			}
			elseif(!$this->mergeSheets || $this->currentListIsFirst)
			{
				$this->AddTextRows($this->textRowsTop, 'TEXT_ROWS_TOP', $fieldsCount);
				
				if($this->hideColumnTitles!='Y')
				{
					$this->writeRowStart(($colHeight = 0), $this->displayParams['COLUMN_TITLES']);
					$this->titlesRowNum = $this->numRows;
					foreach($arFields as $k=>$field)
					{
						$val = $this->GetCellValue($this->titles[$k]);
						$this->writeStringCell($val);
					}
					$this->writeRowEnd();
				}
				
				$this->AddTextRows($this->textRowsTop2, 'TEXT_ROWS_TOP2', $fieldsCount);
			}
			
			while(!feof($handle)) 
			{
				$buffer = trim(fgets($handle));
				if(strlen($buffer) < 1) continue;
				$arElement = unserialize(base64_decode($buffer));
				if(empty($arElement)) continue;

				$colHeight = 0;
				$arColHeight = array();
				
				if(isset($arElement['RTYPE']) && ($arElement['RTYPE']=='SECTION_PATH' || preg_match('/^SECTION_(\d+)$/', $arElement['RTYPE'], $m)))
				{
					$sectionLevel = $m[1];
					if($this->firstSectionLevel == 0) $this->firstSectionLevel = $sectionLevel;
					$level = $this->currentSectionLevel = $sectionLevel - $this->firstSectionLevel;
					$rowParams = array();
					if($arElement['ELEMENT_CNT'] > 0 && $this->params['EXPORT_GROUP_PRODUCTS']=='Y' && $this->params['EXPORT_GROUP_OPEN']!='Y') $rowParams['collapsed'] = 1;
					if($this->params['EXPORT_GROUP_SUBSECTIONS']=='Y')
					{
						if($this->params['EXPORT_GROUP_OPEN']!='Y') $rowParams['collapsed'] = 1;
						if($level > 0)
						{
							if($this->params['EXPORT_GROUP_OPEN']!='Y') $rowParams['hidden'] = 1;
							$rowParams['outlineLevel'] = $level;
						}
					}
					$arCellStyles = array();
					if($this->params['EXPORT_GROUP_INDENT']=='Y' && $level > 0)
					{
						$arCellStyles['INDENT'] = $level;
					}
					
					$this->writeRowStart(($curHeight = 0), $this->displayParams[$arElement['RTYPE']], $rowParams);
					$val = $this->GetCellValue($arElement['NAME']);
					$this->writeStringCell($val, count($arFields), $arCellStyles);
					$this->writeRowEnd();
				}
				else
				{
					$level = 1;
					if($this->currentSectionLevel > 0) $level = $this->currentSectionLevel + 1;
					$rowParams = array();
					if($this->params['EXPORT_GROUP_OPEN']!='Y') $rowParams['hidden'] = 1;
					if($this->params['EXPORT_GROUP_SUBSECTIONS']=='Y') $rowParams['outlineLevel'] = $level;
					else $rowParams['outlineLevel'] = 1;
					
					/*Multicell*/
					$arVals = $arValStyles = $fullCells = $multiCells = array();
					$cellKey = 0;
					foreach($arFields as $k=>$field)
					{
						$valIndex = 0;
						$val = (isset($arElement[$field.'_'.$k]) ? $arElement[$field.'_'.$k] : $arElement[$field]);
						if(is_array($val) && isset($val['TYPE']) && $val['TYPE']=='MULTICELL')
						{
							foreach($val as $kVal=>$vVal)
							{
								if(!is_numeric($kVal) && $kVal=='TYPE') continue;
								if(is_array($vVal) && isset($vVal['VALUE'])) $arVals[$valIndex][$k] = (string)$vVal['VALUE'];
								elseif(!is_array($vVal)) $arVals[$valIndex][$k] = (string)$vVal;
								else $arVals[$valIndex][$k] = '';
								if(is_array($vVal) && isset($vVal['STYLE'])) $arValStyles[$valIndex][$k] = $vVal['STYLE'];
								foreach($arFields as $k2=>$field2)
								{
									if(!isset($arVals[$valIndex][$k2])) $arVals[$valIndex][$k2] = '';
								}
								$fullCells[$cellKey] = $valIndex;
								$valIndex++;
							}								
						}
						if($valIndex==0)
						{
							$arVals[$valIndex][$k] = $val;
							$fullCells[$cellKey] = $valIndex;
						}
						else
						{
							$multiCells[] = $k;
						}
						$cellKey++;
					}
					$cntVals = count($arVals);
					if($cntVals > 1)
					{
						foreach($fullCells as $cellKey=>$rowKey)
						{
							if($rowKey + 1 < $cntVals)
							{
								$this->arMergeCells[] = '<mergeCell ref="'.$this->arColLetters[$cellKey].($this->numRows + $rowKey + 1).':'.$this->arColLetters[$cellKey].($this->numRows + $cntVals).'"/>';
							}
						}
					}
					/*/Multicell*/
					
					/*Images prepare*/
					$arImgVals = array();
					foreach($arFields as $k=>$field)
					{
						if((!isset($this->fparams[$k]['INSERT_PICTURE']) || $this->fparams[$k]['INSERT_PICTURE']!='Y') || !$this->ee->IsPictureField($field)) continue;
						//$val = $this->GetCellValue((isset($arElement[$field.'_'.$k]) ? $arElement[$field.'_'.$k] : $arElement[$field]));
						$arVals2 = (isset($arElement[$field.'_'.$k]) ? $arElement[$field.'_'.$k] : $arElement[$field]);
						if(!is_array($arVals2)) $arVals2 = array($arVals2);
						$isMulty = (bool)(array_key_exists('TYPE', $arVals2));
						foreach($arVals2 as $mkey=>$val)
						{
							if($mkey==='TYPE') continue;
							$val = $this->GetCellValue($val);
							if($this->ee->IsMultipleField($field))
							{
								if($this->fparams[$k]['CHANGE_MULTIPLE_SEPARATOR']=='Y') $separator = $this->fparams[$k]['MULTIPLE_SEPARATOR'];
								else $separator = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
								$arCurImgVals = explode($separator, $val);
							}
							else
							{
								$arCurImgVals = array($val);
							}
							$colWidth = $this->arColsWidth[$k];
							$picsWidth = 0;
							foreach($arCurImgVals as $key=>$val)
							{
								if(!$val) continue;
								$link = '';
								if(preg_match('/<a[^>]+class="kda\-ee\-conversion\-link"[^>]+href="([^"]*)"[^>]*>(.*)<\/a>/Uis', $val, $m))
								{
									$link = $m[1];
									$val = $m[2];
								}
								list($width, $height, $type, $attr) = getimagesize($this->dirPath.'data/xl/media/'.$val);
								$picsWidth += $width;
								$arCurImgVals[$key] = array('VALUE'=>$val, 'LINK'=>$link);
							}
							$textalign = $this->getAlignmentText($this->fparams[$k]);
							if($textalign=='left')
							{
								$leftOffset = $this->imagePixel;
							}
							elseif($textalign=='center')
							{
								$leftOffset = max(1, ($colWidth-$picsWidth) / 2) * $this->imagePixel;
							}
							elseif($textalign=='right')
							{
								$leftOffset = max(1, $colWidth-$picsWidth) * $this->imagePixel;
							}
							
							//$leftOffset = $this->imagePixel;
							foreach($arCurImgVals as $key=>$val)
							{
								$link = $val['LINK'];
								$val = $val['VALUE'];
								if(!$val) continue;
								list($width, $height, $type, $attr) = getimagesize($this->dirPath.'data/xl/media/'.$val);
								if((int)$height > $colHeight)
								{
									$colHeight = (int)$height*$this->imageHeightRatio + 2;
								}
								if($isMulty && (!isset($arColHeight[$mkey]) || (int)$height > $arColHeight[$mkey]))
								{
									$arColHeight[$mkey] = (int)$height*$this->imageHeightRatio + 2;
								}
								
								$width = (int)$width * $this->imagePixel;
								$height = (int)$height * $this->imagePixel;
								$arImgVals[$k][] = array(
									'LINK' => $link,
									'VALUE' => $val,
									'LEFT_OFFSET' => $leftOffset,
									'WIDTH' => $width,
									'HEIGHT' => $height,
									'INDEX' => ($isMulty ? $mkey : 0)
								);
							}
						}
					}
					/*/Images prepare*/
					
					$currentRow = $this->numRows;
					$maxIndex = count($arVals) - 1;
					$arHeights = array();
					foreach($arVals as $valIndex=>$arValue)
					{
						if($maxIndex > $valIndex) $curHeight = 0;
						else $curHeight = max(0, $colHeight - array_sum($arHeights)*2);
						if(isset($arColHeight[$valIndex]) && $arColHeight[$valIndex] > $curHeight)
						{
							$curHeight = $arColHeight[$valIndex];
						}
						$this->writeRowStart($curHeight, array(), $rowParams);
						$arHeights[$valIndex] = ($curHeight > 0 ? $curHeight : $this->defaultRowHeight);
						
						/*Formula*/
						$arCellTypes = array();
						foreach($arFields as $k=>$field)
						{
							$val = trim($arValue[$k]);
							if(strpos($val, '=')!==0) continue;
							$isFormula = $isMathFormula = false;
							$val = substr($val, 1);
							foreach($this->arFunctions as $funcCode=>$funcText)
							{
								if(strpos($val, $funcText.'(')===false) continue;
								if(strpos($val, $funcText.'(')===0) $isFormula = true;
								$isMathFormula = ($isMathFormula || $this->IsMathFormula($funcCode));
								$val = str_replace($funcText.'(', $funcCode.'(', $val);
							}
							if($isFormula)
							{
								$val = str_replace(';', ',', $val);
								if(!empty($multiCells))
								{
									$arMCLetters = array();
									foreach($multiCells as $cellKey)
									{
										$arMCLetters[] = $this->arColLetters[$cellKey];
									}
									$val = preg_replace('/('.implode('|', $arMCLetters).')0/', '${1}'.$this->numRows, $val);
								}
								$val = preg_replace('/([A-Z]{1,3})0/', '${1}'.($currentRow+1), $val);
								if($isMathFormula && preg_match_all('/([A-Z]{1,3})'.$this->numRows.'(\D|$)/', $val, $m))
								{
									foreach($m[1] as $letter)
									{
										if(($index = array_search($letter, $this->arColLetters))!==false)
										{
											$arCellTypes[$index] = 'NUMBER';
										}
									}
								}
								$arValue[$k] = $val;
								$arCellTypes[$k] = 'FORMULA';
							}
						}
						/*/Formula*/

						foreach($arFields as $k=>$field)
						{
							$arCellStyles = $this->fparams[$k];
							if(!is_array($arCellStyles)) $arCellStyles = array();
							if(isset($arValStyles[$valIndex][$k])) $arCellStyles = array_merge($arCellStyles, $arValStyles[$valIndex][$k]);
							if($k==0 && $this->params['EXPORT_GROUP_INDENT']=='Y' && $level > 0)
							{
								$arCellStyles['INDENT'] = $level;
							}
							if((isset($this->fparams[$k]['INSERT_PICTURE']) && $this->fparams[$k]['INSERT_PICTURE']=='Y') && $this->ee->IsPictureField($field))
							{
								$this->writeStringCell('', 1, $arCellStyles);
								continue;
							}
							$val = $this->GetCellValue($arValue[$k]);
							$this->writeStringCell($val, 1, $arCellStyles, true, $arCellTypes[$k]);
						}
						$this->writeRowEnd();
					}
					
					/*Images output*/
					foreach($arImgVals as $k=>$arImgs)
					{
						$leftOffset = null;
						$prevImgRow = -1;
						foreach($arImgs as $arImg)
						{
							$link = trim($arImg['LINK']);
							$val = $arImg['VALUE'];
							$width = $arImg['WIDTH'];
							$height = $arImg['HEIGHT'];
							$currentImgRow = $currentRow + (int)$arImg['INDEX'];
							if($prevImgRow!=$currentImgRow)
							{
								$prevImgRow = $currentImgRow;
								$leftOffset = null;
							}
							if(!isset($leftOffset)) $leftOffset = $arImg['LEFT_OFFSET'];
							
							$rowOffset = 0;
							while(isset($arHeights[$rowOffset+1]) && $height > $arHeights[$rowOffset]*$this->imagePixel*2/$this->imageHeightRatio)
							{
								$height -= $arHeights[$rowOffset]*$this->imagePixel*2/$this->imageHeightRatio;
								$rowOffset++;
							}
							$height = (int)$height;
						
							fwrite($this->drawingsHandler, '<xdr:twoCellAnchor>'.
								'<xdr:from><xdr:col>'.$k.'</xdr:col><xdr:colOff>'.$leftOffset.'</xdr:colOff><xdr:row>'.$currentImgRow.'</xdr:row><xdr:rowOff>'.$this->imagePixel.'</xdr:rowOff></xdr:from>'.
								'<xdr:to><xdr:col>'.$k.'</xdr:col><xdr:colOff>'.($leftOffset + $width).'</xdr:colOff><xdr:row>'.($currentImgRow+$rowOffset).'</xdr:row><xdr:rowOff>'.($height + $this->imagePixel).'</xdr:rowOff></xdr:to>'.
								'<xdr:pic>'.
									'<xdr:nvPicPr>'.
									(
										strlen($link) > 0 ? 
										'<xdr:cNvPr id="'.$this->curImgIndex.'" name="'.$val.'"><a:hlinkClick xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:id="rId'.($this->curImgIndex + 1).'"/></xdr:cNvPr>' :
										'<xdr:cNvPr id="'.$this->curImgIndex.'" name="'.$val.'"/>'
									).
									'<xdr:cNvPicPr><a:picLocks noChangeAspect="1"/></xdr:cNvPicPr></xdr:nvPicPr>'.
									'<xdr:blipFill><a:blip xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:embed="rId'.$this->curImgIndex.'" cstate="print"><a:extLst><a:ext uri="{28A0092B-C50C-407E-A947-70E740481C1C}"><a14:useLocalDpi xmlns:a14="http://schemas.microsoft.com/office/drawing/2010/main" val="0"/></a:ext></a:extLst></a:blip><a:stretch><a:fillRect/></a:stretch></xdr:blipFill>'.
									'<xdr:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></xdr:spPr>'.
								'</xdr:pic>'.
								'<xdr:clientData/>'.
								'</xdr:twoCellAnchor>');
							fwrite($this->drawingRelsHandler, '<Relationship Id="rId'.$this->curImgIndex.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/'.$val.'"/>');
							if(strlen($link) > 0)
							{
								fwrite($this->drawingRelsHandler, '<Relationship Id="rId'.($this->curImgIndex + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="'.$this->getValueForXml($link).'" TargetMode="External"/>');
								$this->curImgIndex++;
							}
							$leftOffset += $width;
							$this->curImgIndex++;
						}
					}
					/*/Images output*/
				}
				
				if($this->ee->CheckTimeEnding())
				{
					$this->currentListIndexKey = $indexKey;
					$this->currentFilePosition = ftell($handle);
					unset($this->ee);
					fclose($handle);
					$this->closeWorkSheet($listIndex, true);
					return false;
				}
			}
			fclose($handle);
			$this->closeWorkSheet($listIndex);
		}
		$this->closeExcelWriter();
	}
	
    public function openExcelWriter()
    {
		$countCells = 0;
		foreach($this->arListIndexes as $listIndex)
		{
			$arFields = $this->ee->GetFieldList($listIndex);
			$cols = count($arFields);
			$rows = $this->arTotalRows[$listIndex];
			$countCells += $cols * ($rows + 1);
		}
		
        $dirPath = $this->dirPath;
		if(file_exists($dirPath.'data/'))
		{
			$this->stringsHandler = fopen($dirPath.'data/xl/sharedStrings.xml', 'a+');
			$this->stylesHandler = fopen($dirPath.'data/xl/styles.xml', 'a+');
			return;
		}	
		
        CheckDirPath($dirPath.'data/');
        CheckDirPath($dirPath.'data/xl/');
        CheckDirPath($dirPath.'data/xl/worksheets/');
		$zipObj = CBXArchive::GetArchive(dirname(__FILE__).'/../../source/example.xlsx', 'ZIP');
		$zipObj->Unpack($dirPath.'data/');
		unlink($dirPath.'data/xl/worksheets/sheet1.xml');

		/*Core*/
		$time = time();
		$date = date('Y-m-d', $time).'T'.date('H:i:s', $time);
		$coreHandler = fopen($dirPath.'data/docProps/core.xml', 'w+');
		fwrite($coreHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.
			'<dc:creator>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_AUTHOR'])).'</dc:creator>'.
			(strlen(trim($this->params['DOCPARAM_TITLE'])) > 0 ? '<dc:title>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_TITLE'])).'</dc:title>' : '').
			(strlen(trim($this->params['DOCPARAM_SUBJECT'])) > 0 ? '<dc:subject>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_SUBJECT'])).'</dc:subject>' : '').
			(strlen(trim($this->params['DOCPARAM_DESCRIPTION'])) > 0 ? '<dc:description>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_DESCRIPTION'])).'</dc:description>' : '').
			(strlen(trim($this->params['DOCPARAM_KEYWORDS'])) > 0 ? '<cp:keywords>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_KEYWORDS'])).'</cp:keywords>' : '').
			(strlen(trim($this->params['DOCPARAM_CATEGORY'])) > 0 ? '<cp:category>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_CATEGORY'])).'</cp:category>' : '').
			'<cp:lastModifiedBy></cp:lastModifiedBy><dcterms:created xsi:type="dcterms:W3CDTF">'.$date.'Z</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">'.$date.'Z</dcterms:modified></cp:coreProperties>');
		fclose($coreHandler);
		/*Core*/
		
		/*Workbook*/
		$this->workbookHandler = fopen($dirPath.'data/xl/workbook.xml', 'w+');
		fwrite($this->workbookHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'.
				'<fileVersion appName="xl" lastEdited="5" lowestEdited="4" rupBuild="9302"/>'.
				'<workbookPr filterPrivacy="1" defaultThemeVersion="124226"/>'.
				'<bookViews><workbookView xWindow="240" yWindow="108" windowWidth="14808" windowHeight="8016"/></bookViews>'.
				'<sheets>');
		
		$i = 1;
		foreach($this->arListIndexesFile as $listIndex)
		{
			$listTitle = htmlspecialchars($this->GetCellValue($this->arListTitle[$listIndex]), ENT_QUOTES, 'UTF-8');
			$listTitle = preg_replace('/[\x00-\x13]/', '', $listTitle);
			$listTitle = trim(strtr($listTitle, array('\\'=>' ', '/'=>' ', ':'=>' ', '?'=>' ', '*'=>' ', '['=>' ', ']'=>' ')));
			$listTitle = substr($listTitle, 0, 31);
			if(strlen($listTitle)==0) $listTitle = 'Sheet';
			fwrite($this->workbookHandler, '<sheet name="'.$listTitle.'" sheetId="'.$i.'" r:id="rId'.$i.'"/>');
			$i++;
		}
		fwrite($this->workbookHandler, '</sheets>'.
				'<calcPr calcId="122211"/></workbook>');
		fclose($this->workbookHandler);
		/*/Workbook*/
		
		/*Workbook.rels*/
		$this->workbookHandlerRels = fopen($dirPath.'data/xl/_rels/workbook.xml.rels', 'w+');
		fwrite($this->workbookHandlerRels, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">');
		$ind = 1;
		foreach($this->arListIndexesFile as $listIndex)
		{
			fwrite($this->workbookHandlerRels, '<Relationship Id="rId'.$ind.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.$ind.'.xml"/>');
			$ind++;
		}
		fwrite($this->workbookHandlerRels, '<Relationship Id="rId'.($ind++).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml"/>'.
			'<Relationship Id="rId'.($ind++).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'.
			'<Relationship Id="rId'.($ind++).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'.
			'</Relationships>');
		fclose($this->workbookHandlerRels);
		/*/Workbook.rels*/
		
		/*Content_Types*/
		$this->contentTypesHandler = fopen($dirPath.'data/[Content_Types].xml', 'w+');
		fwrite($this->contentTypesHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'.
			'<Default Extension="jpeg" ContentType="image/jpeg"/>'.
			'<Default Extension="jpg" ContentType="image/jpeg"/>'.
			'<Default Extension="png" ContentType="image/png"/>'.
			'<Default Extension="gif" ContentType="image/gif"/>'.
			'<Default Extension="bmp" ContentType="image/bmp"/>'.
			'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'.
			'<Default Extension="xml" ContentType="application/xml"/>'.
			'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>');
		$ind = 1;
		foreach($this->arListIndexesFile as $listIndex)
		{
			fwrite($this->contentTypesHandler, '<Override PartName="/xl/worksheets/sheet'.$ind.'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'.
				'<Override PartName="/xl/drawings/drawing'.$ind.'.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>');
			$ind++;
		}
		fwrite($this->contentTypesHandler, '<Override PartName="/xl/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>'.
			'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'.
			'<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'.
			'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'.
			'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'.
			'</Types>');
		fclose($this->contentTypesHandler);
		/*/Content_Types*/
		
		/*App*/
		$listCount = count($this->arListIndexesFile);
		$this->appHandler = fopen($dirPath.'data/docProps/app.xml', 'w+');
		fwrite($this->appHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'.
			'<Application>Microsoft Excel</Application>'.
			'<DocSecurity>0</DocSecurity>'.
			'<ScaleCrop>false</ScaleCrop>'.
			'<HeadingPairs>'.
			'<vt:vector size="2" baseType="variant">'.
			'<vt:variant><vt:lpstr>Sheets</vt:lpstr></vt:variant>'.
			'<vt:variant><vt:i4>'.$listCount.'</vt:i4></vt:variant>'.
			'</vt:vector>'.
			'</HeadingPairs>'.
			'<TitlesOfParts>'.
			'<vt:vector size="'.$listCount.'" baseType="lpstr">');
		$ind = 1;
		foreach($this->arListIndexesFile as $listIndex)
		{
			$listTitle = htmlspecialchars($this->GetCellValue($this->arListTitle[$listIndex]), ENT_QUOTES, 'UTF-8');
			$listTitle = preg_replace( '/[\x00-\x13]/', '', $listTitle );
			fwrite($this->appHandler, '<vt:lpstr>'.$listTitle.'</vt:lpstr>');
			$ind++;
		}
		fwrite($this->appHandler, '</vt:vector>'.
			'</TitlesOfParts>'.
			'<Company>'.$this->getValueForXml($this->GetCellValue($this->params['DOCPARAM_ORG'])).'</Company>'.
			'<LinksUpToDate>false</LinksUpToDate>'.
			'<SharedDoc>false</SharedDoc>'.
			'<HyperlinksChanged>false</HyperlinksChanged>'.
			'<AppVersion>14.0300</AppVersion>'.
			'</Properties>');
		fclose($this->appHandler);
		/*/App*/
		
        $this->stringsHandler = fopen($dirPath.'data/xl/sharedStrings.xml', 'w+');
		$this->stylesHandler = fopen($dirPath.'data/xl/styles.xml', 'w+');
		
        fwrite($this->stringsHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?'.
            '><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.$countCells.'" uniqueCount="'.$countCells.'">');
			
		/*Drawings*/
		CheckDirPath($dirPath.'data/xl/drawings/');
		CheckDirPath($dirPath.'data/xl/drawings/_rels/');
		
		if($this->imageDir)
		{
			$emptyDir = true;
			$dh = opendir($this->imageDir);
			while ($emptyDir && ($file = readdir($dh)) !== false)
			{
				if($file!='.' && $file!='..') $emptyDir = false;
			}
			closedir($dh);
			if(!$emptyDir)
			{
				CopyDirFiles($this->imageDir, $dirPath.'data/xl/media/', true, true);
			}
		}
		/*/Drawings*/
		
		$this->styleFonts = array(
			'<font>'.
				'<sz val="11"/>'.
				'<color theme="1"/>'.
				'<name val="Calibri"/>'.
				'<family val="2"/>'.
				'<scheme val="minor"/>'.
			'</font>',
			'<font>'.
				'<sz val="'.((int)$this->params['FONT_SIZE'] ? (int)$this->params['FONT_SIZE'] : '11').'"/>'.
				($this->params['FONT_COLOR'] ? '<color rgb="FF'.htmlspecialcharsex(ToUpper(substr($this->params['FONT_COLOR'], 1))).'"/>' : '<color theme="1"/>').
				($this->params['STYLE_BOLD']=='Y' ? '<b/>' : '').
				($this->params['STYLE_ITALIC']=='Y' ? '<i/>' : '').
				($this->params['STYLE_UNDERLINE']=='Y' ? '<u/>' : '').
				'<name val="'.($this->params['FONT_FAMILY'] ? htmlspecialcharsex($this->params['FONT_FAMILY']) : 'Calibri').'"/>'.
				'<family val="2"/>'.
				'<scheme val="minor"/>'.
			'</font>'
		);
		
		$this->styleFills = array(
			'<fill><patternFill patternType="none"/></fill>',
			'<fill><patternFill patternType="gray125"/></fill>'
		);
		/*$this->styleFills = array();
		if($this->params['BACKGROUND_COLOR'])
		{
			$this->styleFills[] = '<fill>'.
					'<patternFill patternType="solid">'.
						'<fgColor rgb="FF'.htmlspecialcharsex(ToUpper(substr($this->params['BACKGROUND_COLOR'], 1))).'"/>'.
						'<bgColor indexed="64"/>'.
					'</patternFill>'.
				'</fill>';
		}
		else
		{
			$this->styleFills[] = '<fill><patternFill patternType="none"/></fill>';
		}
		$this->styleFills[] = '<fill><patternFill patternType="gray125"/></fill>';*/
		
		$this->arBorders[] = '<border><left/><right/><top/><bottom/><diagonal/></border>';

		$this->styleCellXfs = array(
			'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyAlignment="1" applyProtection="1">'.$this->getAlignment().'</xf>'
		);
    }
	
    public function openWorkSheet($listIndex)
    {
		$this->tmpFile = $this->dirPath.'data_'.$listIndex.'.txt';
		if(!file_exists($this->tmpFile)) return false;
		$this->closeWorkSheetRelsHandler();
		$this->titles = $this->arTitles[$listIndex];
		$this->listTitle = $this->arListTitle[$listIndex];
		$this->displayParams = $this->arDisplayParams[$listIndex];
		$this->textRowsTop = $this->arTextRowsTop[$listIndex];
		$this->textRowsTop2 = $this->arTextRowsTop2[$listIndex];
		$this->fparams = $this->arFparams[$listIndex];
		$this->hideColumnTitles = $this->arHideColumnTitles[$listIndex];
		$this->enableAutofilter = $this->arEnableAutofilters[$listIndex];
		$this->enableProtection = $this->arEnableProtections[$listIndex];
		$this->labelColor = ToUpper(trim($this->arLabelColors[$listIndex]));
		if(preg_match('/^#[0-9A-F]{6}$/', $this->labelColor)) $this->labelColor = 'FF'.substr($this->labelColor, 1);
		else $this->labelColor = '';
		
		$arFields = $this->fields = $this->ee->GetFieldList($listIndex);
		$cols = count($arFields);
		$rows = $this->arTotalRows[$listIndex];
		$this->qntHeadLines = 0;
		if(is_array($this->textRowsTop)) $this->qntHeadLines += count($this->textRowsTop);
		if($this->hideColumnTitles!='Y') $this->qntHeadLines += 1;
		$rows += $this->qntHeadLines;
		if(is_array($this->textRowsTop2)) $rows += count($this->textRowsTop2);
		$dirPath = $this->dirPath;
		$this->currentListIsFirst = (bool)($listIndex==$this->indexFirstList);
		$this->currentListIsLast = (bool)($listIndex==$this->indexLastList);		
		
		$sheetId = array_search($listIndex, $this->arListIndexesFile);
		if($sheetId==false) $sheetId = 1;
		else $sheetId++;
		$this->sheetId = $sheetId;
		
		if(file_exists($dirPath.'data/xl/worksheets/sheet'.$sheetId.'.xml'))
		{
			$this->workSheetHandler = fopen($dirPath.'data/xl/worksheets/sheet'.$sheetId.'.xml', 'a+');
			$this->drawingsHandler = fopen($dirPath.'data/xl/drawings/drawing'.$sheetId.'.xml', 'a+');
			$this->drawingRelsHandler = fopen($dirPath.'data/xl/drawings/_rels/drawing'.$sheetId.'.xml.rels', 'a+');
			$this->workSheetRelsHandler = fopen($dirPath.'data/xl/worksheets/_rels/sheet'.$sheetId.'.xml.rels', 'a+');
			return true;
		}
		
		$this->arMergeCells = array();
		$this->arHyperLinks = array();
		$this->curRelationshipIndex = 1;
		$this->curImgIndex = 1;
		$this->numRows = 0;
		
		$this->workSheetHandler = fopen($dirPath.'data/xl/worksheets/sheet'.$sheetId.'.xml', 'w+');
        fwrite($this->workSheetHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'.
			'<sheetPr>'.(strlen($this->labelColor) > 0 ? '<tabColor rgb="'.$this->labelColor.'"/>' : '').'<outlinePr summaryBelow="0"/></sheetPr>'.
            '<dimension ref="A1:'.$this->arColLetters[$cols - 1].$rows.'"/><sheetViews>'.
			($this->params['DISPLAY_LOCK_HEADERS']=='Y' ? 
				'<sheetView '.($sheetId==1 ? 'tabSelected="1" ' : '').'showRuler="0" zoomScaleNormal="100" workbookViewId="0">'.
				'<pane ySplit="'.$this->qntHeadLines.'" topLeftCell="A'.($this->qntHeadLines + 1).'" activePane="bottomLeft" state="frozen"/>'.
				'</sheetView>'
				:
				'<sheetView '.($sheetId==1 ? 'tabSelected="1" ' : '').'showRuler="0" zoomScaleNormal="100" workbookViewId="0"/>'
			).
            '</sheetViews><sheetFormatPr defaultRowHeight="'.$this->defaultRowHeight.'" outlineLevelRow="1"/>'.
			'<cols>');
		for($i=1; $i<=$cols; $i++)
		{
			$width = $this->defaultWidth;
			if(isset($this->fparams[$i-1]['DISPLAY_WIDTH']) && (int)$this->fparams[$i-1]['DISPLAY_WIDTH'] > 0) $width = (int)$this->fparams[$i-1]['DISPLAY_WIDTH'];
			fwrite($this->workSheetHandler, '<col min="'.$i.'" max="'.$i.'" width="'.($width / $this->defaultWidthRatio).'" customWidth="1"/>');
		}
		fwrite($this->workSheetHandler, '</cols><sheetData>');
		
		/*Drawings*/
		$this->drawingsHandler = fopen($dirPath.'data/xl/drawings/drawing'.$sheetId.'.xml', 'w+');
		$this->drawingRelsHandler = fopen($dirPath.'data/xl/drawings/_rels/drawing'.$sheetId.'.xml.rels', 'w+');
		
		fwrite($this->drawingsHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">');
		fwrite($this->drawingRelsHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">');
			
		$this->writeWorksheetRels('<Relationship Id="rId'.($this->curRelationshipIndex++).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing'.$sheetId.'.xml"/>');
		/*/Drawings*/
		return true;
    }

    public function writeRowStart(&$colHeight, $arStyles = array(), $rowParams = array())
    {
		$colHeight = (float)$colHeight;
		if($colHeight > 0) $colHeight = ($colHeight / 2);
		
		if(!is_array($arStyles)) $arStyles = array();
		$this->arRowStyles = array_diff($arStyles, array(''));
		$this->setCurrentStyle($colHeight, array_merge($arStyles, array('BACKGROUND_COLOR'=>'')));
		
		if($arStyles['HIDE_UNDER_GROUP']=='Y' && !isset($rowParams['outlineLevel']) /*&& $this->numRows > 0*/)
		{
			$rowParams['outlineLevel'] = 1;
			$rowParams['hidden'] = 1;
		}
		elseif(($this->params['EXPORT_GROUP_PRODUCTS']!='Y' && $this->params['EXPORT_GROUP_SUBSECTIONS']!='Y'))
		{
			$rowParams = array();
		}
		
		$addParams = '';
		if(!empty($rowParams))
		{
			foreach($rowParams as $k=>$v)
			{
				$addParams .= ' '.$k.'="'.$v.'"';
			}
		}
        $this->numRows++;
        //fwrite($this->workSheetHandler, '<row r="'.$this->numRows.'" spans="1:'.$this->totalCols.'"'.($this->currentStyleId > 0 ? ' s="'.$this->currentStyleId.'" customFormat="1"': '').($colHeight > $this->defaultRowHeight ? ' ht="'.$colHeight.'"': '').$addParams.' customHeight="1">');
		
		if($colHeight > $this->defaultRowHeight)
		{
			$addParams .= ' ht="'.$colHeight.'" customHeight="1"';
		}
		elseif($this->params['ROW_AUTO_HEIGHT']!='Y')
		{
			$addParams .= ' customHeight="1"';
		}
		
		fwrite($this->workSheetHandler, '<row r="'.$this->numRows.'" spans="1:'.$this->totalCols.'"'.($this->currentStyleId > 0 ? ' s="'.$this->currentStyleId.'" customFormat="1"': '').$addParams.'>');
        $this->curCel = 0;
		$this->setCurrentStyle($colHeight, $arStyles);
    }
	
	public function setCurrentStyle(&$colHeight, $arStyles, $saveStyle = true)
	{
		if(!is_array($arStyles)) $arStyles = array();

		$this->arStyles = $arStyles;
		$styleId = 0;
		if(!empty($arStyles))
		{
			foreach($arStyles as $k=>$v)
			{
				if(!in_array($k, array('FONT_FAMILY', 'FONT_SIZE', 'FONT_COLOR', 'STYLE_BOLD', 'STYLE_ITALIC', 'STYLE_UNDERLINE', 'BACKGROUND_COLOR', 'ROW_HEIGHT', 'TEXT_ALIGN', 'VERTICAL_ALIGN', 'BORDER_STYLE', 'BORDER_STYLE_SIDE', 'BORDER_COLOR', 'INDENT', 'NUMBER_FORMAT', 'PROTECTION')))
				{
					unset($arStyles[$k]);
				}
			}
			ksort($arStyles);
			$hash = md5(serialize($arStyles));
			if(!isset($this->arStyleIds[$hash]))
			{
				$fontColHeight = 0;
				$arFont = array();
				$setFont = false;
				if($arStyles['FONT_FAMILY'])
				{
					$arFont[] = '<name val="'.htmlspecialcharsex($arStyles['FONT_FAMILY']).'"/>';
					$setFont = true;
				}
				else $arFont[] = '<name val="'.($this->params['FONT_FAMILY'] ? htmlspecialcharsex($this->params['FONT_FAMILY']) : 'Calibri').'"/>';
				if((int)$arStyles['FONT_SIZE'] > 0)
				{
					$arFont[] = '<sz val="'.(int)$arStyles['FONT_SIZE'].'"/>';
					$fontColHeight = /*($this->defaultRowHeight / 11) **/ 1.5 * (int)$arStyles['FONT_SIZE'];
					$setFont = true;
				}
				else $arFont[] = '<sz val="11"/>';
				if($arStyles['FONT_COLOR'])
				{
					$arFont[] = '<color rgb="FF'.htmlspecialcharsex(ToUpper(substr($arStyles['FONT_COLOR'], 1))).'"/>';
					$setFont = true;
				}
				else $arFont[] = '<color theme="1"/>';
				if($arStyles['STYLE_BOLD']=='Y')
				{
					$arFont[] = '<b/>';
					$setFont = true;
				}
				if($arStyles['STYLE_ITALIC']=='Y')
				{
					$arFont[] = '<i/>';
					$setFont = true;
				}
				if($arStyles['STYLE_UNDERLINE']=='Y')
				{
					$arFont[] = '<u/>';
					$setFont = true;
				}
				if($setFont)
				{
					$arFont[] = '<family val="1"/>';
					$arFont[] = '<charset val="204"/>';
				}
				
				$fontId = 0;
				if($setFont)
				{
					$this->styleFonts[] = '<font>'.implode('', $arFont).'</font>';
					$fontId = count($this->styleFonts) - 1;
				}
				
				$fillId = 0;
				if($arStyles['BACKGROUND_COLOR'])
				{
					$bg = $arStyles['BACKGROUND_COLOR'];
					if(!isset($this->styleFillIds[$bg]))
					{
						$this->styleFills[] = '<fill>'.
								'<patternFill patternType="solid">'.
									'<fgColor rgb="FF'.htmlspecialcharsex(ToUpper(substr($bg, 1))).'"/>'.
									'<bgColor indexed="64"/>'.
								'</patternFill>'.
							'</fill>';
						$this->styleFillIds[$bg] = count($this->styleFills) - 1;
					}
					$fillId = $this->styleFillIds[$bg];
				}
				
				$setAlignment = (bool)(($arStyles['TEXT_ALIGN'] && $arStyles['TEXT_ALIGN']!=$this->params['DISPLAY_TEXT_ALIGN']) || ($arStyles['VERTICAL_ALIGN'] && $arStyles['VERTICAL_ALIGN']!=$this->params['DISPLAY_VERTICAL_ALIGN']));
				
				$borderId = 0;
				$borderStyle = (($arStyles['BORDER_STYLE'] && $arStyles['BORDER_STYLE']!='NONE') ? ' style="'.ToLower($arStyles['BORDER_STYLE']).'"' : '');
				$borderStyleSide = ($arStyles['BORDER_STYLE_SIDE'] ? $arStyles['BORDER_STYLE_SIDE'] : 'lrtb');
				if(!$saveStyle && $borderStyle)
				{
					if($arStyles['BORDER_COLOR']) $borderColor = '<color rgb="FF'.htmlspecialcharsex(ToUpper(substr($arStyles['BORDER_COLOR'], 1))).'"/>';
					else $borderColor = '<color auto="1"/>';
					$borderXml = '';
					if(strpos($borderStyleSide, 'l')!==false) $borderXml .= ($borderColor ? '<left'.$borderStyle.'>'.$borderColor.'</left>' : '<left'.$borderStyle.'/>');
					if(strpos($borderStyleSide, 'r')!==false) $borderXml .= ($borderColor ? '<right'.$borderStyle.'>'.$borderColor.'</right>' : '<right'.$borderStyle.'/>');
					if(strpos($borderStyleSide, 't')!==false) $borderXml .= ($borderColor ? '<top'.$borderStyle.'>'.$borderColor.'</top>' : '<top'.$borderStyle.'/>');
					if(strpos($borderStyleSide, 'b')!==false) $borderXml .= ($borderColor ? '<bottom'.$borderStyle.'>'.$borderColor.'</bottom>' : '<bottom'.$borderStyle.'/>');
					$this->arBorders[] = '<border>'.$borderXml.'<diagonal/></border>';
					$borderId = count($this->arBorders) - 1;
				}
				
				if($fontId > 0 || $fillId > 0 || $setAlignment || $borderId > 0 || $arStyles['INDENT'] || $arStyles['NUMBER_FORMAT'] || $arStyles['PROTECTION'])
				{
					$numFmtId = 'numFmtId="0"';
					if((int)$arStyles['NUMBER_FORMAT'] > 0) $numFmtId = 'numFmtId="'.(int)$arStyles['NUMBER_FORMAT'].'" applyNumberFormat="1"'.((int)$arStyles['NUMBER_FORMAT']==49 ? ' quotePrefix="1"' : '');
					$this->styleCellXfs[] = '<xf '.$numFmtId.' fontId="'.$fontId.'" fillId="'.$fillId.'" '.($borderId > 0 ? 'borderId="'.$borderId.'" applyBorder="1"' : 'borderId="0"').' xfId="'.count($this->styleCellXfs).'"'.($fontId > 0 ? ' applyFont="1"' : '').($fillId > 0 ? ' applyFill="1"' : '').' applyAlignment="1" applyProtection="1">'.$this->getAlignment($arStyles).$this->getProtection($arStyles).'</xf>';
					$curStyleId = count($this->styleCellXfs) - 1;
				}
				else
				{
					$curStyleId = 0;
				}
				
				$this->arStyleIds[$hash] = array(
					'STYLE_ID' => $curStyleId,
					'COL_HEIGHT' => $fontColHeight,
				);
			}
			
			$styleId = $this->arStyleIds[$hash]['STYLE_ID'];
			if($this->arStyleIds[$hash]['COL_HEIGHT'] > $colHeight) $colHeight = $this->arStyleIds[$hash]['COL_HEIGHT'];
			if($arStyles['ROW_HEIGHT'] > $colHeight) $colHeight = (float)$arStyles['ROW_HEIGHT'] / 2;
		}
		if($saveStyle) $this->currentStyleId = $styleId;
		return $styleId;
	}
	
	public function getAlignment($arStyles = array())
	{
		$textAlign = ToLower($arStyles['TEXT_ALIGN'] ? $arStyles['TEXT_ALIGN'] : $this->params['DISPLAY_TEXT_ALIGN']);
		if(!in_array($textAlign, array('left', 'center', 'right'))) $textAlign = 'left';
		$verticalAlign = ToLower($arStyles['VERTICAL_ALIGN'] ? $arStyles['VERTICAL_ALIGN'] : $this->params['DISPLAY_VERTICAL_ALIGN']);
		if(!in_array($verticalAlign, array('top', 'center', 'bottom'))) $verticalAlign = 'top';
		
		$alignment = '<alignment horizontal="'.$textAlign.'" vertical="'.$verticalAlign.'" wrapText="1"'.($arStyles['INDENT'] > 0 ? ' indent="'.(int)$arStyles['INDENT'].'"' : '').'/>';
		return $alignment;
	}
	
	public function getProtection($arStyles = array())
	{
		$protection = '';
		if($arStyles['PROTECTION']=='N')
		{
			$protection = '<protection locked="0"/>';
		}
		return $protection;
	}
	
	public function getAlignmentText($arStyles = array())
	{
		$textAlign = ToLower($arStyles['TEXT_ALIGN'] ? $arStyles['TEXT_ALIGN'] : $this->params['DISPLAY_TEXT_ALIGN']);
		if(!in_array($textAlign, array('left', 'center', 'right'))) $textAlign = 'left';
		return $textAlign;
	}

    public function writeNumberCell($value)
    {
        $this->curCel++;
        fwrite($this->workSheetHandler, '<c r="'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"><v>'.$value.'</v></c>');
    }
	
	public function GetStylesWithDefault($arStyles)
	{
		if(!is_array($arStyles)) $arStyles = array();
		$arKeys = array('FONT_FAMILY', 'FONT_SIZE', 'FONT_COLOR', 'STYLE_BOLD', 'STYLE_ITALIC', 'STYLE_UNDERLINE', 'BORDER_STYLE', 'BORDER_STYLE_SIDE', 'BORDER_COLOR');
		foreach($arKeys as $key)
		{
			if(!$arStyles[$key] && $this->params[$key])
			{
				$arStyles[$key] = $this->params[$key];
			}
		}
		
		if(is_array($this->arRowStyles)) $arStyles = array_merge($arStyles, $this->arRowStyles);
		return $arStyles;
	}
	
    public function writeStringCell($value, $colspan=1, $arStyles=array(), $isData=false, $cellType='')
    {
		$origValue = $value;
		$arStyles = $this->GetStylesWithDefault($arStyles);
		if((int)$arStyles['NUMBER_FORMAT'] > 0 && !in_array((int)$arStyles['NUMBER_FORMAT'], array(49)) && strlen($cellType)==0) $cellType = 'NUMBER';
        $this->curCel++;
		$cell = $this->curCel;
        if (1) {
			$currentStyleId = $this->currentStyleId;
			if(isset($this->linkCells[$cell - 1]) && preg_match('/<a[^>]+class="kda\-ee\-conversion\-link"[^>]+href="([^"]*)"[^>]*>(.*)<\/a>/Uis', $value, $m))
			{
				$cellName = $this->arColLetters[$cell - 1].$this->numRows;
				$this->addRelLink($cellName, $m[1]);
				$value = $origValue = $m[2];
				$arStyles['FONT_COLOR'] = '#0000FF';
				$arStyles['STYLE_UNDERLINE'] = 'Y';
				$currentStyleId = $this->setCurrentStyle(($colHeight=0), $arStyles, false);
			}
			elseif(!empty($arStyles))
			{
				$currentStyleId = $this->setCurrentStyle(($colHeight=0), $arStyles, false);
			}
			
			$attrs = '';
			//if($colspan > 1) $attrs .= ' s="'.$this->curCel.'"';
			if($currentStyleId > 0) $attrs .= ' s="'.$currentStyleId.'"';
			if(strlen((string)$value) > 0)
			{
				$value = $this->getValueForXml($value);
				if($cellType=='FORMULA')
				{
					fwrite($this->workSheetHandler, '<c r="'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"'.$attrs.'><f>'.$value.'</f></c>');
				}
				elseif($cellType=='NUMBER')
				{
					fwrite($this->workSheetHandler, '<c r="'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"'.$attrs.'><v>'.floatval($value).'</v></c>');
				}
				else
				{
					fwrite($this->stringsHandler, '<si><t>'.$value.'</t></si>');
					fwrite($this->workSheetHandler, '<c r="'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"'.$attrs.' t="s"><v>'.$this->numStrings.'</v></c>');
					$this->numStrings++;
				}
			}
			else
			{
				fwrite($this->workSheetHandler, '<c r="'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"'.$attrs.'></c>');
			}
			
			if($colspan > 1)
			{
				for($i=1; $i<$colspan; $i++)
				{
					$this->curCel++;
					fwrite($this->workSheetHandler, '<c r="'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"'.$attrs.'/>');
				}
				$this->arMergeCells[] = '<mergeCell ref="'.$this->arColLetters[$cell - 1].$this->numRows.':'.$this->arColLetters[$this->curCel - 1].$this->numRows.'"/>';
			}
			
			if((isset($this->fparams[$cell - 1]['MAKE_DROPDOWN']) && $this->fparams[$cell - 1]['MAKE_DROPDOWN']=='Y') && $isData && $colspan < 2)
			{
				$ddVal = trim((string)$origValue);
				if(strlen($ddVal) > 0)
				{
					$ddCell = $cell - 1;
					if(!isset($this->arDropdowns[$ddCell])) $this->arDropdowns[$ddCell] = array();
					if(!in_array($ddVal, $this->arDropdowns[$ddCell])) $this->arDropdowns[$ddCell][] = $ddVal;
				}
			}
        }
    }
	
	public function getValueForXml($value, $quotes=true)
	{
		if($quotes) $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		else $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
		//$value = preg_replace('/[\x00-\x13]/', '', $value);
		//$value = preg_replace('/[\x00-\x1f]/', '', $value);
		$value = preg_replace('/[\x00-\x09\x0b-\x0c\x0e-\x1f]/', '', $value);
		return $value;
	}

    public function writeRowEnd()
    {
        fwrite($this->workSheetHandler, '</row>');
    }
	
	public function addRelLink($cellName, $link)
	{
		$rid = 'rId'.($this->curRelationshipIndex);
		$this->writeWorksheetRels('<Relationship Id="'.$rid.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="'.$this->getValueForXml(trim($link)).'" TargetMode="External"/>');
		$this->arHyperLinks[] = '<hyperlink ref="'.$cellName.'" r:id="'.$rid.'"/>';
		$this->curRelationshipIndex++;
	}

	public function writeWorksheetRels($str)
	{
		if(!isset($this->workSheetRelsHandler))
		{
			$dirPath = $this->dirPath;
			CheckDirPath($dirPath.'data/xl/worksheets/_rels/');
			$this->workSheetRelsHandler = fopen($dirPath.'data/xl/worksheets/_rels/sheet'.$this->sheetId.'.xml.rels', 'w+');
			
			fwrite($this->workSheetRelsHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
				'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">');
		}
		
		fwrite($this->workSheetRelsHandler, $str);
	}
	
	public function AddTextRows($textRows, $textRowsName, $fieldsCount)
	{
		if(!empty($textRows))
		{
			foreach($textRows as $k=>$v)
			{
				$cellType = '';
				$dataKey = $textRowsName.'_'.$k;
				$colHeight = 0;
				/*Picture*/
				if(preg_match('/^\[\[(\d+)\]\]$/', $v, $m))
				{
					$fileId = $m[1];
					$maxWidth = ($this->displayParams[$dataKey]['PICTURE_WIDTH'] ? $this->displayParams[$dataKey]['PICTURE_WIDTH'] : 3000);
					$maxHeight = ($this->displayParams[$dataKey]['PICTURE_HEIGHT'] ? $this->displayParams[$dataKey]['PICTURE_HEIGHT'] : 3000);
					$arFileOrig = CKDAExportUtils::GetFileArray($fileId);
					$arFile = CFile::MakeFileArray($fileId);
					CFile::ResizeImage($arFile, array("width" => $maxWidth, "height" => $maxHeight));
					$fileNum = 0;
					while(($val = $fileNum.'_'.$arFileOrig['FILE_NAME']) && file_exists($this->dirPath.'data/xl/media/'.$val))
					{
						$fileNum++;
					}
					copy($arFile['tmp_name'], $this->dirPath.'data/xl/media/'.$val);
					
					list($width, $height, $type, $attr) = getimagesize($this->dirPath.'data/xl/media/'.$val);
					$colHeight = (int)$height*$this->imageHeightRatio + 2;
					$width = (int)$width * $this->imagePixel;
					$height = (int)$height * $this->imagePixel;
					
					$textalign = $this->getAlignmentText($this->displayParams[$dataKey]);
					if($textalign=='left')
					{
						$firstCol = 0;
						$leftWidth = $this->imagePixel;
						for($i=1; $i<=$this->totalCols; $i++)
						{
							$colWidth = $this->arColsWidth[$i-1] * $this->imagePixel;
							if($width > $colWidth) $width -= $colWidth;
							else break;
						}
						$lastCol = $i - 1;
					}
					elseif($textalign=='center')
					{
						$allColsWidth = $this->allColsWidth * $this->imagePixel;
						$leftWidth = round(max(0, $allColsWidth - $width) / 2);
						$width += $leftWidth;
						for($i=1; $i<=$this->totalCols; $i++)
						{
							$colWidth = $this->arColsWidth[$i-1] * $this->imagePixel;
							if($leftWidth > $colWidth) $leftWidth -= $colWidth;
							else break;
						}
						$firstCol = $i - 1;
						
						for($i=1; $i<=$this->totalCols; $i++)
						{
							$colWidth = $this->arColsWidth[$i-1] * $this->imagePixel;
							if($width > $colWidth) $width -= $colWidth;
							else break;
						}
						$lastCol = $i - 1;
					}
					elseif($textalign=='right')
					{
						$allColsWidth = $this->allColsWidth * $this->imagePixel;
						$leftWidth = max(0, $allColsWidth - $width);
						$width += $leftWidth;
						for($i=1; $i<=$this->totalCols; $i++)
						{
							$colWidth = $this->arColsWidth[$i-1] * $this->imagePixel;
							if($leftWidth > $colWidth) $leftWidth -= $colWidth;
							else break;
						}
						$firstCol = $i - 1;
						
						for($i=1; $i<=$this->totalCols; $i++)
						{
							$colWidth = $this->arColsWidth[$i-1] * $this->imagePixel;
							if($width > $colWidth) $width -= $colWidth;
							else break;
						}
						$lastCol = $i - 1;
					}
					
					fwrite($this->drawingsHandler, '<xdr:twoCellAnchor editAs="oneCell">'.
						'<xdr:from><xdr:col>'.$firstCol.'</xdr:col><xdr:colOff>'.$leftWidth.'</xdr:colOff><xdr:row>'.$this->numRows.'</xdr:row><xdr:rowOff>'.$this->imagePixel.'</xdr:rowOff></xdr:from>'.
						'<xdr:to><xdr:col>'.$lastCol.'</xdr:col><xdr:colOff>'.$width.'</xdr:colOff><xdr:row>'.$this->numRows.'</xdr:row><xdr:rowOff>'.($height + $this->imagePixel).'</xdr:rowOff></xdr:to>'.
						'<xdr:pic>'.
							'<xdr:nvPicPr><xdr:cNvPr id="'.$this->curImgIndex.'" name="'.$val.'"/><xdr:cNvPicPr><a:picLocks noChangeAspect="1"/></xdr:cNvPicPr></xdr:nvPicPr>'.
							'<xdr:blipFill><a:blip xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:embed="rId'.$this->curImgIndex.'" cstate="print"><a:extLst><a:ext uri="{28A0092B-C50C-407E-A947-70E740481C1C}"><a14:useLocalDpi xmlns:a14="http://schemas.microsoft.com/office/drawing/2010/main" val="0"/></a:ext></a:extLst></a:blip><a:stretch><a:fillRect/></a:stretch></xdr:blipFill>'.
							'<xdr:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></xdr:spPr>'.
						'</xdr:pic>'.
						'<xdr:clientData/>'.
						'</xdr:twoCellAnchor>');
					fwrite($this->drawingRelsHandler, '<Relationship Id="rId'.$this->curImgIndex.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/'.$val.'"/>');
					$this->curImgIndex++;
					$v = '';
				}
				/*/Picture*/
				
				/*Formula*/
				$v2 = trim($v);
				if(strpos(trim($v2), '=')===0)
				{
					$isFormula = $isMathFormula = false;
					$v2 = substr($v2, 1);
					foreach($this->arFunctions as $funcCode=>$funcText)
					{
						if(strpos($v2, $funcText.'(')===false) continue;
						if(strpos($v2, $funcText.'(')===0) $isFormula = true;
						$isMathFormula = ($isMathFormula || $this->IsMathFormula($funcCode));
						$v2 = str_replace($funcText.'(', $funcCode.'(', $v2);
					}
					if($isFormula)
					{
						$v2 = str_replace(';', ',', $v2);
						$v = $v2;
						$cellType = 'FORMULA';
					}
				}
				/*/Formula*/
				
				$this->writeRowStart($colHeight, $this->displayParams[$dataKey]);
				$val = $this->GetCellValue($v);
				$this->writeStringCell($val, $fieldsCount, array(), false, $cellType);
				$this->writeRowEnd();
			}
		}
	}
	
	public function closeWorkSheet($listIndex, $break=false)
	{
		if($break || ($this->mergeSheets && !$this->currentListIsLast))
		{
			fclose($this->workSheetHandler);
		}
		
        fwrite($this->workSheetHandler, '</sheetData>');
		if($this->enableProtection=='Y')
		{
			fwrite($this->workSheetHandler, '<sheetProtection password="'.ToUpper(substr(md5(mt_rand()), 0, 4)).'" sheet="1" objects="1" scenarios="1" sort="0" autoFilter="0"/>');
		}
		else
		{
			fwrite($this->workSheetHandler, '<sheetProtection formatCells="0" formatColumns="0" formatRows="0" insertColumns="0" insertRows="0" insertHyperlinks="0" deleteColumns="0" deleteRows="0" sort="0" autoFilter="0" pivotTables="0"/>');
		}
			
		/*Autofilter*/
		if($this->enableAutofilter=='Y' && $this->hideColumnTitles!='Y')
		{
			$arFieldsKeys = array_keys($this->fields);
			$fieldsKeys = '';
			if(count($arFieldsKeys) > 1)
			{
				$fieldsKeys = ($this->arColLetters[current($arFieldsKeys)].$this->titlesRowNum).':'.($this->arColLetters[end($arFieldsKeys)].$this->titlesRowNum);
			}
			elseif(count($arFieldsKeys) == 1)
			{
				$fieldsKeys = ($this->arColLetters[current($arFieldsKeys)].$this->titlesRowNum);
			}
			if(strlen($fieldsKeys) > 0)
			{
				fwrite($this->workSheetHandler, '<autoFilter ref="'.$fieldsKeys.'"/>');
			}
		}
		/*/Autofilter*/
		
		if(!empty($this->arMergeCells))
		{
			fwrite($this->workSheetHandler, '<mergeCells count="'.count($this->arMergeCells).'">'.implode('', $this->arMergeCells).'</mergeCells>');
		}
		if(!empty($this->arDropdowns))
		{
			fwrite($this->workSheetHandler, '<dataValidations count="'.count($this->arDropdowns).'">');
			foreach($this->arDropdowns as $k=>$dd)
			{
				$vals = '';
				$lenVals = 0;
				foreach($dd as $k2=>$v2)
				{
					$dd[$k2] = str_replace('"', '""', $this->getValueForXml((string)$dd[$k2], false));
					$lenVals += ($lenVals > 0 ? 1 : 0) + strlen($dd[$k2]);
					if($lenVals < 256)
					{
						$vals .= (strlen($vals) > 0 ? ',' : '').$dd[$k2];
					}
				}
				$letter = $this->arColLetters[$k];
				
				fwrite($this->workSheetHandler, '<dataValidation type="list" allowBlank="1" showInputMessage="1" showErrorMessage="1" sqref="'.$letter.'1:'.$letter.$this->numRows.'"><formula1>"'.$vals.'"</formula1></dataValidation>');
			}
			fwrite($this->workSheetHandler, '</dataValidations>');
		}
		if(!empty($this->arHyperLinks))
		{
			fwrite($this->workSheetHandler, '<hyperlinks>'.implode('', $this->arHyperLinks).'</hyperlinks>');
		}
        fwrite($this->workSheetHandler, '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'.
			'<pageSetup orientation="portrait"/>'.
			'<headerFooter alignWithMargins="0"/>'.
			'<ignoredErrors><ignoredError sqref="A1:'.$this->arColLetters[$this->totalCols - 1].$this->numRows.'" numberStoredAsText="1"/></ignoredErrors>'.
			($this->curImgIndex > 1 ? '<drawing r:id="rId1"/>' : '').
			'</worksheet>');
		fclose($this->workSheetHandler);
	}
	
	public function closeWorkSheetRelsHandler()
	{
		$needWrite = (bool)(!$this->mergeSheets || $this->currentListIsLast);
		
		if(isset($this->workSheetRelsHandler))
		{
			if($needWrite)
			{
				fwrite($this->workSheetRelsHandler, '</Relationships>');
			}
			fclose($this->workSheetRelsHandler);
			unset($this->workSheetRelsHandler);
		}
		
		if($this->drawingsHandler)
		{
			if($needWrite)
			{
				fwrite($this->drawingsHandler, '</xdr:wsDr>');
				fwrite($this->drawingRelsHandler, '</Relationships>');
			}
			fclose($this->drawingsHandler);
			fclose($this->drawingRelsHandler);
		}
	}

    public function closeExcelWriter($break = false)
    {
		if($break)
		{
			fclose($this->stringsHandler);
			fclose($this->stylesHandler);
			return true;
		}
		
        fwrite($this->stringsHandler, '</sst>');
        fclose($this->stringsHandler);
		
		$this->closeWorkSheetRelsHandler();
		
		fwrite($this->stylesHandler, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
			'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.
			'<fonts count="'.count($this->styleFonts).'">'.
				implode('', $this->styleFonts).
			'</fonts>'.
			'<fills count="'.count($this->styleFills).'">'.
				implode('', $this->styleFills).
			'</fills>'.
			'<borders count="'.count($this->arBorders).'">'.
				implode('', $this->arBorders).
			'</borders>'.
			'<cellStyleXfs count="1">'.
				'<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'.
			'</cellStyleXfs>'.
			'<cellXfs count="'.count($this->styleCellXfs).'">'.
				implode('', $this->styleCellXfs).
			'</cellXfs>'.
			'<cellStyles count="1">'.
				'<cellStyle name="Normal" xfId="0" builtinId="0"/>'.
			'</cellStyles>'.
			'<dxfs count="0"/>'.
			'<tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleMedium9"/>'.
			'<extLst>'.
				'<ext uri="{EB79DEF2-80B8-43e5-95BD-54CBDDF9020C}" xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main">'.
					'<x14:slicerStyles defaultSlicerStyle="SlicerStyleLight1"/>'.
				'</ext>'.
			'</extLst>'.
			'</styleSheet>');
		fclose($this->stylesHandler);

		$dirPath = $this->dirPath;

		if(file_exists($this->outputFile)) unlink($this->outputFile);
		if(\CKDAExportUtils::CanUseZipArchive() && ($zipObj = new ZipArchive()) && $zipObj->open($this->outputFile, ZipArchive::OVERWRITE|ZipArchive::CREATE)===true)
		{
			/*$zipObj = new ZipArchive();
			$zipObj->open($this->outputFile, ZipArchive::CREATE);*/
			$this->AddToZipArchive($zipObj, $dirPath.'data/', '');
			$zipObj->close();
		}
		else
		{
			$zipObj = CBXArchive::GetArchive($this->outputFile, 'ZIP');
			$zipObj->SetOptions(array(
				"COMPRESS" =>true,
				"ADD_PATH" => false,
				"REMOVE_PATH" => $dirPath.'data/',
			));
			$zipObj->Pack($dirPath.'data/');
		}
    }
	
	public function AddToZipArchive($zip, $basedir, $subdir)
	{
		$arFiles = array_diff(scandir($basedir.$subdir), array('.', '..'));
		foreach($arFiles as $file)
		{
			$fn = $basedir.$subdir.$file;
			if(is_dir($fn))
			{
				$this->AddToZipArchive($zip, $basedir, $subdir.$file.'/');
			}
			else
			{
				$zip->addFile($fn, $subdir.$file);
			}
		}
	}
	
	public function GetCellValue($val)
	{
		$val = substr($val, 0, 32767);
		if(!defined('BX_UTF') || !BX_UTF)
		{
			$val = $GLOBALS['APPLICATION']->ConvertCharset($val, 'CP1251', 'UTF-8');
		}
		return $val;
	}
	
	public function IsMathFormula($funcCode)
	{
		return (bool)(in_array($funcCode, array('ABS', 'ACOS', 'ASIN', 'ASINH', 'ATAN', 'ATAN2', 'ATANH', 'CEILING', 'COMBIN', 'COS', 'COSH', 'DEGREES', 'EVEN', 'EXP', 'FACT', 'FACTDOUBLE', 'FLOOR', 'GCD', 'INT', 'LCM', 'LN', 'LOG', 'LOG10', 'MDETERM', 'MINVERSE', 'MMULT', 'MOD', 'MROUND', 'MULTINOMIAL', 'ODD', 'PI', 'POWER', 'PRODUCT', 'QUOTIENT', 'RADIANS', 'RAND', 'RANDBETWEEN', 'ROMAN', 'ROUND', 'ROUNDDOWN', 'ROUNDUP', 'SERIESSUM', 'SIGN', 'SIN', 'SINH', 'SQRT', 'SQRTPI', 'SUBTOTAL', 'SUM', 'SUMIF', 'SUMIFS', 'SUMPRODUCT', 'SUMSQ', 'SUMX2MY2', 'SUMX2PY2', 'SUMXMY2', 'TAN', 'TANH', 'TRUNC')));
	}
}
?>