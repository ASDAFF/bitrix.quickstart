<?php
class CKDAExportExcelWriterDbf {	
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
		$this->arTotalRows = $arParams['ROWS'];
		$this->arFparams = $arParams['EXTRAPARAMS'];
		$this->params = $arParams['PARAMS'];
		$this->arTitles = $arParams['PARAMS']['FIELDS_LIST_NAMES'];
		$this->arListTitle = $arParams['PARAMS']['LIST_NAME'];
		$this->arTextRowsTop = $arParams['PARAMS']['TEXT_ROWS_TOP'];
		$this->arTextRowsTop2 = $arParams['PARAMS']['TEXT_ROWS_TOP2'];
		$this->arHideColumnTitles = $arParams['PARAMS']['HIDE_COLUMN_TITLES'];
		$this->arStrLengths = $arParams['STRING_LENGTHS'];
		$this->SetEObject($ee);

		foreach($this->arListIndexes as $indexKey=>$listIndex)
		{
			$arFields = $this->fields = $this->ee->GetFieldList($listIndex);
			$cols = count($arFields);
			$this->maxColumn = max($this->maxColumn, $cols);
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
		
		$key = 0;
		foreach($this->arListIndexes as $indexKey=>$listIndex)
		{
			$key++;
			if($key > 1 || (isset($this->currentListIndexKey) && $this->currentListIndexKey > $indexKey))
			{
				continue;
			}
			if($this->openWorkSheet($listIndex)===false) continue;

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
				
				if(0 && $this->hideColumnTitles!='Y')
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
						if(in_array($this->fparams[$k]['NUMBER_FORMAT'], array(1, 2, 3, 4)))
						{
							$val = $this->ee->GetFloatVal($val);
						}
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
		/*if($this->curRow > 0)
		{
			$this->csvHandler = fopen($this->outputFile, 'a+');
			return;
		}
		
		if(file_exists($this->outputFile)) unlink($this->outputFile);
		$this->csvHandler = fopen($this->outputFile, 'a+');*/
		
		/*$pFilename = $_SERVER['DOCUMENT_ROOT'].'/upload/test.dbf';
		file_put_contents($pFilename, '');
		$table = new \Xbase\WritableTable($pFilename);
		if($table = $table->create($pFilename, array(array('id', 'C', 50), array('name', 'C', 50))))
		{
			$record = $table->appendRecord();
			$record->setStringByName('id', 'test');
			$record->setStringByName('name', 'test2');
			$table->writeRecord();

			$record = $table->appendRecord();
			$record->setStringByName('id', 'test');
			$record->setStringByName('name', 'test2');
			$table->writeRecord();
			$table->close();
		}*/
    }
	
    public function openWorkSheet($listIndex)
    {
		$this->titles = $this->arTitles[$listIndex];
		$this->listTitle = $this->arListTitle[$listIndex];
		$this->textRowsTop = $this->arTextRowsTop[$listIndex];
		$this->textRowsTop2 = $this->arTextRowsTop2[$listIndex];
		$this->fparams = $this->arFparams[$listIndex];
		$this->hideColumnTitles = $this->arHideColumnTitles[$listIndex];
		$this->strLengths = $this->arStrLengths[$listIndex];
		$this->tmpFile = $this->dirPath.'data_'.$listIndex.'.txt';
		$arFields = $this->fields = $this->ee->GetFieldList($listIndex);
		
		$rows = $this->arTotalRows[$listIndex];
		$this->qntHeadLines = 0;
		if(is_array($this->textRowsTop)) $this->qntHeadLines += count($this->textRowsTop);
		//if($this->hideColumnTitles!='Y') $this->qntHeadLines += 1;
		$rows += $this->qntHeadLines;
		if(is_array($this->textRowsTop2)) $rows += count($this->textRowsTop2);
		
		$sheetId = array_search($listIndex, $this->arListIndexes);
		if($sheetId==false) $sheetId = 1;
		else $sheetId++;
		$this->sheetId = $sheetId;

		$arBdfFields = array();
		foreach($this->titles as $k=>$title)
		{
			$type = 'C';
			if(in_array($this->fparams[$k]['NUMBER_FORMAT'], array(1, 2, 3, 4))) $type = 'N';
			$decimals = 0;
			if(in_array($this->fparams[$k]['NUMBER_FORMAT'], array(2, 4))) $decimals = 2;
			$this->titles[$k] = $title = substr($title, 0, 10);
			$arBdfFields[] = array($title, $type, max(10, (int)$this->strLengths[$k] + 1), $decimals);
		}

		if($this->curRow > 0)
		{
			$this->table->openContinueWrite($this->outputFile, true);
		}
		else
		{
			file_put_contents($this->outputFile, '');
			$table = new \Xbase\WritableTable($this->outputFile);
			$this->table = $table->create($this->outputFile, $arBdfFields, $rows);
		}
		return $this->table;
    }

	private function WriteLine($pValues = null) {
		if(!is_array($pValues)) return;
		
		/*while(count($pValues) < $this->maxColumn) $pValues[] = '';
		if(count($pValues) > $this->maxColumn) $pValues = array_slice($pValues, 0, $this->maxColumn);*/

		$record = $this->table->appendRecord();
		foreach($pValues as $k=>$element)
		{
			$record->setStringByName($this->titles[$k], $element);
		}
		$this->table->writeRecord();
		$this->curRow++;
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
		if($this->table) $this->table->close();
    }
	
	public function GetCellValue($val)
	{
		return $val;
	}
}
?>