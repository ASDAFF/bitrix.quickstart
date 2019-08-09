<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/zip.php');

class CKDAExportExcelWriterCsv {
	private $_lineEnding = PHP_EOL;
	private $_delimiter	= ',';
	private $_enclosure	= '"';
	private $_useBOM = false;
	private $_encoding = 'UTF-8';
	private $_yandexAdapt = false;
	
    private $fields = 0;
	private $totalCols = 0;
	private $maxColumn = 0;
	private $curRow = 0;
	private $arListIndexes = array();
    private $dirPath = '';
    private $outputFile = '';
	private $tmpFile = '';
	private $titles = array();
	private $ee = false;
	
	function __construct($arParams = array(), $ee = false)
	{
		$this->dirPath = $arParams['TMPDIR'];
		$this->outputFile = $arParams['OUTPUTFILE'];
		$this->arListIndexes = $arParams['LIST_INDEXES'];
		$this->arFparams = $arParams['EXTRAPARAMS'];
		$this->params = $arParams['PARAMS'];
		$this->arTitles = $arParams['PARAMS']['FIELDS_LIST_NAMES'];
		$this->arListTitle = $arParams['PARAMS']['LIST_NAME'];
		$this->arTextRowsTop = $arParams['PARAMS']['TEXT_ROWS_TOP'];
		$this->arTextRowsTop2 = $arParams['PARAMS']['TEXT_ROWS_TOP2'];
		$this->arHideColumnTitles = $arParams['PARAMS']['HIDE_COLUMN_TITLES'];
		$this->SetEObject($ee);
		
		foreach($this->arListIndexes as $indexKey=>$listIndex)
		{
			$arFields = $this->fields = $this->ee->GetFieldList($listIndex);
			$cols = count($arFields);
			$this->maxColumn = max($this->maxColumn, $cols);
		}
		
		if($arParams['PARAMS']['CSV_SEPARATOR']) $this->SetDelimiter($arParams['PARAMS']['CSV_SEPARATOR']);
		if($arParams['PARAMS']['CSV_ENCODING']) $this->SetEncoding($arParams['PARAMS']['CSV_ENCODING']);
		if(isset($arParams['PARAMS']['CSV_ENCLOSURE'])) $this->SetEnclosure($arParams['PARAMS']['CSV_ENCLOSURE']);
		if($arParams['PARAMS']['CSV_YANDEX']=='Y') $this->SetYandexAdapt(true);
		if($this->GetEncoding()=='UTF-8') $this->SetUseBOM(true);
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
			if(isset($this->currentListIndexKey) && $this->currentListIndexKey > $indexKey)
			{
				continue;
			}
			$this->openWorkSheet($listIndex);
		
			$arFields = $this->fields;
			$fieldsCount = $this->totalCols = count($arFields);
		
			$handle = fopen($this->tmpFile, 'r');
			if(isset($this->currentListIndexKey) && $this->currentListIndexKey == $indexKey && isset($this->currentFilePosition))
			{
				fseek($handle, $this->currentFilePosition);
			}
			else
			{
				$this->AddTextRows($this->textRowsTop);
				
				if($this->hideColumnTitles!='Y')
				{
					$arLine = array();
					foreach($arFields as $k=>$field)
					{
						$val = $this->GetCellValue($this->titles[$k]);
						$arLine[] = $val;
					}
					$this->WriteLine($arLine);
				}
				
				$this->AddTextRows($this->textRowsTop2);
			}
			
			while(!feof($handle)) 
			{
				$buffer = trim(fgets($handle));
				if(strlen($buffer) < 1) continue;
				$arElement = unserialize(base64_decode($buffer));
				if(empty($arElement)) continue;
				
				if(isset($arElement['RTYPE']) && ($arElement['RTYPE']=='SECTION_PATH' || preg_match('/^SECTION_(\d+)$/', $arElement['RTYPE'], $m)))
				{
					$val = $this->GetCellValue($arElement['NAME']);
					$arLine = array($val);
					$this->WriteLine($arLine);
				}
				else
				{
					$arLine = array();
					foreach($arFields as $k=>$field)
					{
						$val = $this->GetCellValue((isset($arElement[$field.'_'.$k]) ? $arElement[$field.'_'.$k] : $arElement[$field]));
						$arLine[] = $val;
					}
					$this->WriteLine($arLine);
				}
				
				if($this->ee->CheckTimeEnding())
				{
					$this->currentListIndexKey = $indexKey;
					$this->currentFilePosition = ftell($handle);
					unset($this->ee);
					fclose($handle);
					$this->closeExcelWriter(true);
					return false;
				}
			}
			fclose($handle);
		}
		$this->closeExcelWriter();
	}
	
    public function openExcelWriter()
    {
		if($this->curRow > 0)
		{
			$this->csvHandler = fopen($this->outputFile, 'a+');
			return;
		}
		
		if(file_exists($this->outputFile)) unlink($this->outputFile);
		$this->csvHandler = fopen($this->outputFile, 'a+');
		if($this->_useBOM) {
			fwrite($this->csvHandler, "\xEF\xBB\xBF");
		}
    }
	
    public function openWorkSheet($listIndex)
    {
		$this->titles = $this->arTitles[$listIndex];
		$this->listTitle = $this->arListTitle[$listIndex];
		$this->textRowsTop = $this->arTextRowsTop[$listIndex];
		$this->textRowsTop2 = $this->arTextRowsTop2[$listIndex];
		$this->fparams = $this->arFparams[$listIndex];
		$this->hideColumnTitles = $this->arHideColumnTitles[$listIndex];
		$this->tmpFile = $this->dirPath.'data_'.$listIndex.'.txt';
		$arFields = $this->fields = $this->ee->GetFieldList($listIndex);
		
		$sheetId = array_search($listIndex, $this->arListIndexes);
		if($sheetId==false) $sheetId = 1;
		else $sheetId++;
		$this->sheetId = $sheetId;
    }

	private function WriteLine($pValues = null) {
		if(!is_array($pValues)) return;
		
		while(count($pValues) < $this->maxColumn) $pValues[] = '';
		if(count($pValues) > $this->maxColumn) $pValues = array_slice($pValues, 0, $this->maxColumn);
		
		$writeDelimiter = false;
		$line = '';

		foreach ($pValues as $element) {
			$element = str_replace($this->_enclosure, $this->_enclosure . $this->_enclosure, $element);
			if($this->_enclosure == '' || $this->_yandexAdapt)
			{
				$element = preg_replace("/\s+/s", ' ', $element);
			}
			if($this->_enclosure == '') $element = str_replace($this->_delimiter, '', $element);

			if ($writeDelimiter) $line .= $this->_delimiter;
			else $writeDelimiter = true;

			if($this->_yandexAdapt && preg_match('/^[\d\w_\-,;. ]*$/', $element))
			{
				$line .= $element;
			}
			else
			{
				$line .= $this->_enclosure . $element . $this->_enclosure;
			}
		}

		$line .= $this->_lineEnding;
		fwrite($this->csvHandler, $line);
		$this->curRow++;
	}
	
	public function GetEncoding()
	{
		return $this->_encoding;
	}
	
	public function SetEncoding($encoding)
	{
		$this->_encoding = $encoding;
	}
	
	public function SetDelimiter($pValue = ',') {
		if($pValue=='\t') $pValue = "\t";
		$this->_delimiter = $pValue;
		return $this;
	}
	
	public function SetEnclosure($pValue = '"') {
		if ($pValue == '') {
			$pValue = null;
		}
		$this->_enclosure = $pValue;
	}
	
	public function SetLineEnding($pValue = PHP_EOL) {
		$this->_lineEnding = $pValue;
	}
	
	public function SetUseBOM($pValue = false) {
		$this->_useBOM = $pValue;
	}
	
	public function SetYandexAdapt($pValue = false) {
		$this->_yandexAdapt = $pValue;
	}
	
	public function AddTextRows($textRows)
	{
		if(!empty($textRows))
		{
			foreach($textRows as $k=>$v)
			{
				$val = $this->GetCellValue($v);
				$arLine = array($val);
				$this->WriteLine($arLine);
			}
		}
	}

    public function closeExcelWriter($break = false)
    {
		fclose($this->csvHandler);
    }
	
	public function GetCellValue($val)
	{
		if($this->GetEncoding()=='CP1251')
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
}
?>