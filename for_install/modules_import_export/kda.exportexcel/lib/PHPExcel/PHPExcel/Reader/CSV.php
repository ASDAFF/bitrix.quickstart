<?php
/**
 * KDAPHPExcel
 *
 * Copyright (c) 2006 - 2013 KDAPHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */


/** KDAPHPExcel root directory */
if (!defined('KDAPHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('KDAPHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(KDAPHPEXCEL_ROOT . 'KDAPHPExcel/Autoloader.php');
}

/**
 * KDAPHPExcel_Reader_CSV
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Reader_CSV extends KDAPHPExcel_Reader_Abstract implements KDAPHPExcel_Reader_IReader
{
	/**
	 * Input encoding
	 *
	 * @access	private
	 * @var	string
	 */
	private $_inputEncoding	= 'UTF-8';

	/**
	 * Delimiter
	 *
	 * @access	private
	 * @var string
	 */
	private $_delimiter		= ';';

	/**
	 * Enclosure
	 *
	 * @access	private
	 * @var	string
	 */
	private $_enclosure		= '"';

	/**
	 * Line ending
	 *
	 * @access	private
	 * @var	string
	 */
	private $_lineEnding	= PHP_EOL;

	/**
	 * Sheet index to read
	 *
	 * @access	private
	 * @var	int
	 */
	private $_sheetIndex	= 0;

	/**
	 * Load rows contiguously
	 *
	 * @access	private
	 * @var	int
	 */
	private $_contiguous	= false;

	/**
	 * Row counter for loading rows contiguously
	 *
	 * @var	int
	 */
	private $_contiguousRow	= -1;
	
	/**
	 * File position for start reading
	 *
	 * @var	int
	 */
	private $_startFilePos = 0;
	
	/**
	 * File row for start reading
	 *
	 * @var	int
	 */
	private $_startFileRow = 1;


	/**
	 * Create a new KDAPHPExcel_Reader_CSV
	 */
	public function __construct() {
		$this->_readFilter		= new KDAPHPExcel_Reader_DefaultReadFilter();
	}

	/**
	 * Validate that the current file is a CSV file
	 *
	 * @return boolean
	 */
	protected function _isValidFormat()
	{
		return TRUE;
	}
	
	/**
	 * Set start file position and row
	 *
	 * @param array $pos file position
	 */
	public function setStartFilePosRow($arPos)
	{
		$this->_startFilePos = $arPos['pos'];
		$this->_startFileRow = $arPos['row'];
	}
	
	/**
	 * Get start file position
	 *
	 * @return int
	 */
	public function getStartFilePos()
	{
		if(isset($this->_startFilePos)) return (int)$this->_startFilePos;
		else return 0;
	}
	
	/**
	 * Get start file row
	 *
	 * @return int
	 */
	public function getStartFileRow()
	{
		if(isset($this->_startFileRow)) return (int)$this->_startFileRow;
		else return 1;
	}

	/**
	 * Set input encoding
	 *
	 * @param string $pValue Input encoding
	 */
	public function setInputEncoding($pValue = 'UTF-8')
	{
		$this->_inputEncoding = $pValue;
		return $this;
	}

	/**
	 * Get input encoding
	 *
	 * @return string
	 */
	public function getInputEncoding()
	{
		return $this->_inputEncoding;
	}

	/**
	 * Move filepointer past any BOM marker
	 *
	 */
	protected function _skipBOM()
	{
		rewind($this->_fileHandle);

		switch ($this->_inputEncoding) {
			case 'UTF-8':
				fgets($this->_fileHandle, 4) == "\xEF\xBB\xBF" ?
				fseek($this->_fileHandle, 3) : fseek($this->_fileHandle, 0);
				break;
			case 'UTF-16LE':
				fgets($this->_fileHandle, 3) == "\xFF\xFE" ?
				fseek($this->_fileHandle, 2) : fseek($this->_fileHandle, 0);
				break;
			case 'UTF-16BE':
				fgets($this->_fileHandle, 3) == "\xFE\xFF" ?
				fseek($this->_fileHandle, 2) : fseek($this->_fileHandle, 0);
				break;
			case 'UTF-32LE':
				fgets($this->_fileHandle, 5) == "\xFF\xFE\x00\x00" ?
				fseek($this->_fileHandle, 4) : fseek($this->_fileHandle, 0);
				break;
			case 'UTF-32BE':
				fgets($this->_fileHandle, 5) == "\x00\x00\xFE\xFF" ?
				fseek($this->_fileHandle, 4) : fseek($this->_fileHandle, 0);
				break;
			default:
				break;
		}
	}

	/**
	 * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
	 *
	 * @param 	string 		$pFilename
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public function listWorksheetInfo($pFilename)
	{
		// Open file
		$this->_openFile($pFilename);
		if (!$this->_isValidFormat()) {
			fclose ($this->_fileHandle);
			throw new KDAPHPExcel_Reader_Exception($pFilename . " is an Invalid Spreadsheet file.");
		}
		$fileHandle = $this->_fileHandle;
		
		// Skip BOM, if any
		$this->_skipBOM();

		$escapeEnclosures = array( "\\" . $this->_enclosure, $this->_enclosure . $this->_enclosure );

		$worksheetInfo = array();
		$worksheetInfo[0]['worksheetName'] = 'Worksheet';
		$worksheetInfo[0]['lastColumnLetter'] = 'A';
		$worksheetInfo[0]['lastColumnIndex'] = 0;
		$worksheetInfo[0]['totalRows'] = 0;
		$worksheetInfo[0]['totalColumns'] = 0;

		// Loop through each line of the file in turn
		while (($rowData = $this->fgetcsv($fileHandle)) !== FALSE) {
			$worksheetInfo[0]['totalRows']++;
			$worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
		}

		$worksheetInfo[0]['lastColumnLetter'] = KDAPHPExcel_Cell::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex']);
		$worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

		// Close file
		fclose($fileHandle);

		return $worksheetInfo;
	}

	/**
	 * Loads KDAPHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @return KDAPHPExcel
	 * @throws KDAPHPExcel_Reader_Exception
	 */
	public function load($pFilename)
	{
		if(file_exists($pFilename))
		{
			$handle = fopen($pFilename, "r");
			$contents = fread($handle, 262144);
			fclose($handle);
			
			$encontents = preg_replace('/%[A-F0-9]{2}/', '', $contents);
			if(!(CUtil::DetectUTF8($encontents)))
			{
				if(!function_exists('iconv') || iconv('CP1251', 'CP1251', $encontents)==$encontents)
				{
					$this->setInputEncoding('CP1251');
				}
				elseif(function_exists('iconv') && iconv('CP866', 'CP866', $encontents)==$encontents)
				{
					$this->setInputEncoding('CP866');
				}
			}
			
			$contents = str_replace('&quot;', '', html_entity_decode($contents, ENT_NOQUOTES));
			$enclosure = $this->getEnclosure();
			$subcontent = preg_replace("/''/Uis", "", $contents);
			$subcontent = preg_replace("/'[^']*'/Uis", "", $subcontent);
			$subcontent = preg_replace("/'[^']*$/Uis", "", $subcontent);
			$subcontent = preg_replace("/[,;~\|\t\s\r\n]/Uis", "", $subcontent);
			if(strlen($subcontent) < 5) //bom length
			{
				$enclosure = "'";
				$this->setEnclosure($enclosure);
			}
			
			$arEnclosures = array($enclosure, ($enclosure=="'" ? '"' : "'"));
			$correctSettings = false;
			$loop = 0;
			while(!$correctSettings && $loop < 3)
			{
				$enclosure = $arEnclosures[$loop%2];
				$this->setEnclosure($enclosure);
				$subcontent = preg_replace('/'.$enclosure.$enclosure.'/Uis', '', $contents);
				$subcontent = preg_replace('/'.$enclosure.'[^'.$enclosure.']*'.$enclosure.'/Uis', '', $subcontent);
				$arAllLines = explode("\n", $subcontent);
				//$arLines = array_slice($arLines, 0, round(count($arAllLines)/2));
				$arLines = array();
				$lKey = $lLength = 0;
				$slength = strlen($subcontent);
				while(($lLength < $slength / 2 || $lKey < 10) && isset($arAllLines[$lKey]))
				{
					$arLines[] = $arAllLines[$lKey];
					$lLength += strlen($arAllLines[$lKey]) + 1;
					$lKey++;
				}
				$arLines = array_diff($arLines, array(''));
				
				$setDelimiter = false;
				if(count($arLines) > 0)
				{
					$arDelemiters = array(",", ";", "~", "|", "\t");
					$arCnt = array();
					foreach($arDelemiters as $v2)
					{
						$subcnt = substr_count($arLines[0], $v2);
						if($subcnt > 0) $arCnt[$v2] = $subcnt;
					}
					foreach($arDelemiters as $v2)
					{
						if(!isset($arCnt[$v2])) continue;
						foreach($arLines as $k=>$v)
						{
							if(isset($arCnt[$v2]) && $arCnt[$v2]!=substr_count($v, $v2)) unset($arCnt[$v2]);
						}
					}
					if(count($arCnt)==1)
					{
						$setDelimiter = true;
						$arKeys = array_keys($arCnt);
						$this->setDelimiter($arKeys[0]);
					}
				}
				
				if(!$setDelimiter && substr_count($subcontent, ',') > substr_count($subcontent, ';'))
				{
					$this->setDelimiter(',');
				}
				
				$correctSettings = $this->checkCsvSettings($pFilename);
				
				$loop++;
			}
		}
		
		// Create new KDAPHPExcel
		$objKDAPHPExcel = new KDAPHPExcel();

		// Load into this instance
		return $this->loadIntoExisting($pFilename, $objKDAPHPExcel);
	}
	
	public function checkCsvSettings($pFilename)
	{
		$lineEnding = ini_get('auto_detect_line_endings');
		ini_set('auto_detect_line_endings', true);

		$this->_openFile($pFilename);
		if (!$this->_isValidFormat()) {
			return true;
		}
		$fileHandle = $this->_fileHandle;
		$this->_skipBOM();

		$escapeEnclosures = array( "\\" . $this->_enclosure,
								   $this->_enclosure . $this->_enclosure
								 );

		if(defined('LANGUAGE_ID') && LANGUAGE_ID=='ru')
		{
			$locate = 'ru_RU.'.$this->getInputEncoding();
			exec('locale -a | grep ru', $arLocates);
			if(is_array($arLocates) && count($arLocates) > 0)
			{
				foreach($arLocates as $loc)
				{
					$arLocates[ToLower($loc)] = $loc;
				}
				$locateLower = ToLower($locate);
				if(!isset($arLocates[$locateLower]) && isset($arLocates['russian']))
				{
					$locate = $arLocates['russian'];
				}
			}
			
			setLocale(LC_CTYPE, $locate);
		}

		$correctSettings = true;
		$rowColumns = 0;
		$loop = 0;
		while (($rowData = $this->fgetcsv($fileHandle)) !== FALSE && $loop < 50 && $correctSettings) 
		{
			if($loop > 0 && count($rowData)!=$rowColumns)
			{
				$correctSettings = false;
			}
			$rowColumns = count($rowData);
			$loop++;
		}

		fclose($fileHandle);
		ini_set('auto_detect_line_endings', $lineEnding);

		return $correctSettings;
	}

	/**
	 * Loads KDAPHPExcel from file into KDAPHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	KDAPHPExcel	$objKDAPHPExcel
	 * @return 	KDAPHPExcel
	 * @throws 	KDAPHPExcel_Reader_Exception
	 */
	public function loadIntoExisting($pFilename, KDAPHPExcel $objKDAPHPExcel)
	{
		$lineEnding = ini_get('auto_detect_line_endings');
		ini_set('auto_detect_line_endings', true);

		// Open file
		$this->_openFile($pFilename);
		if (!$this->_isValidFormat()) {
			fclose ($this->_fileHandle);
			throw new KDAPHPExcel_Reader_Exception($pFilename . " is an Invalid Spreadsheet file.");
		}
		$fileHandle = $this->_fileHandle;

		// Skip BOM, if any
		$this->_skipBOM();

		// Create new KDAPHPExcel object
		while ($objKDAPHPExcel->getSheetCount() <= $this->_sheetIndex) {
			$objKDAPHPExcel->createSheet();
		}
		$sheet = $objKDAPHPExcel->setActiveSheetIndex($this->_sheetIndex);

		$escapeEnclosures = array( "\\" . $this->_enclosure,
								   $this->_enclosure . $this->_enclosure
								 );

		// Set our starting row based on whether we're in contiguous mode or not
		//$currentRow = 1;
		$currentRow = $this->getStartFileRow();
		if ($this->_contiguous) {
			$currentRow = ($this->_contiguousRow == -1) ? $sheet->getHighestRow(): $this->_contiguousRow;
		}

		if(defined('LANGUAGE_ID') && LANGUAGE_ID=='ru')
		{
			setLocale(LC_CTYPE, 'ru_RU.'.$this->getInputEncoding());
		}
		$startFilePos = $this->getStartFilePos();
		if($startFilePos > 0) fseek($fileHandle, $startFilePos);
		// Loop through each line of the file in turn
		//while (($rowData = fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== FALSE) {
		while (($rowData = $this->fgetcsv($fileHandle)) !== FALSE) {
			if(method_exists($this->_readFilter, 'getEndRow') && $currentRow > $this->_readFilter->getEndRow()) {
				break;
			}
			if(method_exists($this->_readFilter, 'setFilePosRow')) {
				$this->_readFilter->setFilePosRow($currentRow + 1, ftell($fileHandle));
			}
			$columnLetter = 'A';
			foreach($rowData as $rowDatum) {
				if ($rowDatum != '' && $this->_readFilter->readCell($columnLetter, $currentRow)) {
					// Unescape enclosures
					$rowDatum = str_replace($escapeEnclosures, $this->_enclosure, $rowDatum);

					// Convert encoding if necessary
					if ($this->_inputEncoding !== 'UTF-8') {
						$rowDatum = KDAPHPExcel_Shared_String::ConvertEncoding($rowDatum, 'UTF-8', $this->_inputEncoding);
					}

					// Set cell value
					$sheet->getCell($columnLetter . $currentRow)->setValue($rowDatum);
				}
				++$columnLetter;
			}
			++$currentRow;
		}

		// Close file
		fclose($fileHandle);

		if ($this->_contiguous) {
			$this->_contiguousRow = $currentRow;
		}

		ini_set('auto_detect_line_endings', $lineEnding);
		if(defined('LANGUAGE_ID') && LANGUAGE_ID=='ru')
		{
			setLocale(LC_CTYPE, 'ru_RU.'.$this->getSiteEncoding());
		}

		// Return
		return $objKDAPHPExcel;
	}
	
	public function fgetcsv($fileHandle)
	{
		if($this->getInputEncoding()=='CP866')
		{
			return $this->fgetcsvCustom($fileHandle, 65536, $this->_delimiter, $this->_enclosure);
		}
		else
		{
			return fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure);
		}
	}
	
	public function fgetcsvCustom($f, $length, $d=",", $q='"')
	{
		$list = array();
		$st = fgets($f, $length);
		if ($st === false || $st === null) return $st;
		if (trim($st) === "") return array("");
		while ($st !== "" && $st !== false) {
			if ($st[0] !== $q) {
				# Non-quoted.
				list ($field) = explode($d, $st, 2);
				$st = $this->substr($st, $this->strlen($field)+$this->strlen($d));
			} else {
				# Quoted field.
				$st = $this->substr($st, 1);
				$field = "";
				while (1) {
					# Find until finishing quote (EXCLUDING) or eol (including)
					preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
					$part = $p[1];
					$partlen = $this->strlen($part);
					$st = $this->substr($st, $this->strlen($p[0]));
					$field .= str_replace($q.$q, $q, $part);
					if ($this->strlen($st) && $st[0] === $q) {
						# Found finishing quote.
						list ($dummy) = explode($d, $st, 2);
						$st = $this->substr($st, $this->strlen($dummy)+$this->strlen($d));
						break;
					} else {
						# No finishing quote - newline.
						$st = fgets($f, $length);
					}
				}

			}
			$list[] = $field;
		}
		return $list;
	}

	/**
	 * Get delimiter
	 *
	 * @return string
	 */
	public function getDelimiter() {
		return $this->_delimiter;
	}

	/**
	 * Set delimiter
	 *
	 * @param	string	$pValue		Delimiter, defaults to ,
	 * @return	KDAPHPExcel_Reader_CSV
	 */
	public function setDelimiter($pValue = ',') {
		$this->_delimiter = $pValue;
		return $this;
	}

	/**
	 * Get enclosure
	 *
	 * @return string
	 */
	public function getEnclosure() {
		return $this->_enclosure;
	}

	/**
	 * Set enclosure
	 *
	 * @param	string	$pValue		Enclosure, defaults to "
	 * @return KDAPHPExcel_Reader_CSV
	 */
	public function setEnclosure($pValue = '"') {
		if ($pValue == '') {
			$pValue = '"';
		}
		$this->_enclosure = $pValue;
		return $this;
	}

	/**
	 * Get line ending
	 *
	 * @return string
	 */
	public function getLineEnding() {
		return $this->_lineEnding;
	}

	/**
	 * Set line ending
	 *
	 * @param	string	$pValue		Line ending, defaults to OS line ending (PHP_EOL)
	 * @return KDAPHPExcel_Reader_CSV
	 */
	public function setLineEnding($pValue = PHP_EOL) {
		$this->_lineEnding = $pValue;
		return $this;
	}

	/**
	 * Get sheet index
	 *
	 * @return integer
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	integer		$pValue		Sheet index
	 * @return KDAPHPExcel_Reader_CSV
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}

	/**
	 * Set Contiguous
	 *
	 * @param boolean $contiguous
	 */
	public function setContiguous($contiguous = FALSE)
	{
		$this->_contiguous = (bool) $contiguous;
		if (!$contiguous) {
			$this->_contiguousRow = -1;
		}

		return $this;
	}

	/**
	 * Get Contiguous
	 *
	 * @return boolean
	 */
	public function getContiguous() {
		return $this->_contiguous;
	}
	
	public static function getSiteEncoding()
	{
		if (defined('BX_UTF'))
			$logicalEncoding = "UTF-8";
		elseif (defined("SITE_CHARSET") && (strlen(SITE_CHARSET) > 0))
			$logicalEncoding = SITE_CHARSET;
		elseif (defined("LANG_CHARSET") && (strlen(LANG_CHARSET) > 0))
			$logicalEncoding = LANG_CHARSET;
		elseif (defined("BX_DEFAULT_CHARSET"))
			$logicalEncoding = BX_DEFAULT_CHARSET;
		else
			$logicalEncoding = "CP1251";

		return strtoupper($logicalEncoding);
	}

	public function substr($str, $start, $length=null)
	{
		
		if(function_exists('mb_substr')){ 
			return mb_substr($str, $start, ($length===null ? 2000000000 : $length), $this->getInputEncoding());
		}
		if($length===null) return substr($str, $start);
		else return substr($str, $start, $length);
	}
	
	public function strlen($str)
	{
		if(function_exists('mb_strlen')){ 
			return mb_strlen($str, $this->getInputEncoding());
		}
		return strlen($str);
	}
}
