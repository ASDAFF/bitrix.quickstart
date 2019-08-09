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
 * @package    KDAPHPExcel
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */


/**	KDAPHPExcel root directory */
if (!defined('KDAPHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('KDAPHPEXCEL_ROOT', dirname(__FILE__) . '/../');
	require(KDAPHPEXCEL_ROOT . 'KDAPHPExcel/Autoloader.php');
}

/**
 * KDAPHPExcel_IOFactory
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_IOFactory
{
	/**
	 * Search locations
	 *
	 * @var	array
	 * @access	private
	 * @static
	 */
	private static $_searchLocations = array(
		array( 'type' => 'IWriter', 'path' => 'KDAPHPExcel/Writer/{0}.php', 'class' => 'KDAPHPExcel_Writer_{0}' ),
		array( 'type' => 'IReader', 'path' => 'KDAPHPExcel/Reader/{0}.php', 'class' => 'KDAPHPExcel_Reader_{0}' )
	);

	/**
	 * Autoresolve classes
	 *
	 * @var	array
	 * @access	private
	 * @static
	 */
	private static $_autoResolveClasses = array(
		'Excel2007',
		'Excel5',
		'Excel2003XML',
		'OOCalc',
		'SYLK',
		'Gnumeric',
		'HTML',
		'CSV',
	);

    /**
     *	Private constructor for KDAPHPExcel_IOFactory
     */
    private function __construct() { }

    /**
     * Get search locations
     *
	 * @static
	 * @access	public
     * @return	array
     */
	public static function getSearchLocations() {
		return self::$_searchLocations;
	}	//	function getSearchLocations()

	/**
	 * Set search locations
	 *
	 * @static
	 * @access	public
	 * @param	array $value
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public static function setSearchLocations($value) {
		if (is_array($value)) {
			self::$_searchLocations = $value;
		} else {
			throw new KDAPHPExcel_Reader_Exception('Invalid parameter passed.');
		}
	}	//	function setSearchLocations()

	/**
	 * Add search location
	 *
	 * @static
	 * @access	public
	 * @param	string $type		Example: IWriter
	 * @param	string $location	Example: KDAPHPExcel/Writer/{0}.php
	 * @param	string $classname 	Example: KDAPHPExcel_Writer_{0}
	 */
	public static function addSearchLocation($type = '', $location = '', $classname = '') {
		self::$_searchLocations[] = array( 'type' => $type, 'path' => $location, 'class' => $classname );
	}	//	function addSearchLocation()

	/**
	 * Create KDAPHPExcel_Writer_IWriter
	 *
	 * @static
	 * @access	public
	 * @param	KDAPHPExcel $phpExcel
	 * @param	string  $writerType	Example: Excel2007
	 * @return	KDAPHPExcel_Writer_IWriter
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public static function createWriter(KDAPHPExcel $phpExcel, $writerType = '') {
		// Search type
		$searchType = 'IWriter';

		// Include class
		foreach (self::$_searchLocations as $searchLocation) {
			if ($searchLocation['type'] == $searchType) {
				$className = str_replace('{0}', $writerType, $searchLocation['class']);

				$instance = new $className($phpExcel);
				if ($instance !== NULL) {
					return $instance;
				}
			}
		}

		// Nothing found...
		throw new KDAPHPExcel_Reader_Exception("No $searchType found for type $writerType");
	}	//	function createWriter()

	/**
	 * Create KDAPHPExcel_Reader_IReader
	 *
	 * @static
	 * @access	public
	 * @param	string $readerType	Example: Excel2007
	 * @return	KDAPHPExcel_Reader_IReader
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public static function createReader($readerType = '') {
		// Search type
		$searchType = 'IReader';

		// Include class
		foreach (self::$_searchLocations as $searchLocation) {
			if ($searchLocation['type'] == $searchType) {
				$className = str_replace('{0}', $readerType, $searchLocation['class']);

				$instance = new $className();
				if ($instance !== NULL) {
					return $instance;
				}
			}
		}

		// Nothing found...
		throw new KDAPHPExcel_Reader_Exception("No $searchType found for type $readerType");
	}	//	function createReader()

	/**
	 * Loads KDAPHPExcel from file using automatic KDAPHPExcel_Reader_IReader resolution
	 *
	 * @static
	 * @access public
	 * @param 	string 		$pFilename		The name of the spreadsheet file
	 * @return	KDAPHPExcel
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public static function load($pFilename) {
		$reader = self::createReaderForFile($pFilename);
		return $reader->load($pFilename);
	}	//	function load()

	/**
	 * Identify file type using automatic KDAPHPExcel_Reader_IReader resolution
	 *
	 * @static
	 * @access public
	 * @param 	string 		$pFilename		The name of the spreadsheet file to identify
	 * @return	string
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public static function identify($pFilename) {
		$reader = self::createReaderForFile($pFilename);
		$className = get_class($reader);
		$classType = explode('_',$className);
		unset($reader);
		return array_pop($classType);
	}	//	function identify()

	/**
	 * Create KDAPHPExcel_Reader_IReader for file using automatic KDAPHPExcel_Reader_IReader resolution
	 *
	 * @static
	 * @access	public
	 * @param 	string 		$pFilename		The name of the spreadsheet file
	 * @return	KDAPHPExcel_Reader_IReader
	 * @throws	KDAPHPExcel_Reader_Exception
	 */
	public static function createReaderForFile($pFilename, $ext=false) {

		// First, lucky guess by inspecting file extension
		$pathinfo = pathinfo($pFilename);
		if($ext!==false) $pathinfo['extension'] = $ext;

		$extensionType = NULL;
		if (isset($pathinfo['extension'])) {
			switch (strtolower($pathinfo['extension'])) {
				case 'xlsx':			//	Excel (OfficeOpenXML) Spreadsheet
				case 'xlsm':			//	Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
				case 'xltx':			//	Excel (OfficeOpenXML) Template
				case 'xltm':			//	Excel (OfficeOpenXML) Macro Template (macros will be discarded)
					$extensionType = 'Excel2007';
					break;
				case 'xls':				//	Excel (BIFF) Spreadsheet
				case 'xlt':				//	Excel (BIFF) Template
					$extensionType = 'Excel5';
					
					/*Exception for wrong extension*/
					if(file_get_contents($pFilename, FALSE, NULL, 0, 8)!=KDAPHPExcel_Shared_OLERead::IDENTIFIER_OLE
						/*&& class_exists('ZipArchive') 
						&& ($zip = new ZipArchive) 
						&& ($zip->open($pFilename) === true)
						&& $zip->close()*/
						&& ($zip = CBXArchive::GetArchive($pFilename, 'ZIP'))
						&& $zip->GetProperties())
					{
						$extensionType = 'Excel2007';
					}
					/*/Exception for wrong extension*/
					break;
				case 'ods':				//	Open/Libre Offic Calc
				case 'ots':				//	Open/Libre Offic Calc Template
					$extensionType = 'OOCalc';
					break;
				case 'slk':
					$extensionType = 'SYLK';
					break;
				case 'xml':				//	Excel 2003 SpreadSheetML
					$extensionType = 'Excel2003XML';
					break;
				case 'gnumeric':
					$extensionType = 'Gnumeric';
					break;
				case 'htm':
				case 'html':
					$extensionType = 'HTML';
					break;
				case 'csv':
					// Do nothing
					// We must not try to use CSV reader since it loads
					// all files including Excel files etc.
					break;
				case 'dbf':
					$extensionType = 'DBF';
					break;
				default:
					break;
			}

			if ($extensionType !== NULL) {
				$reader = self::createReader($extensionType);
				// Let's see if we are lucky
				//if (isset($reader) && $reader->canRead($pFilename)) {
				if (isset($reader)) {
					return $reader;
				}
			}
		}

		// If we reach here then "lucky guess" didn't give any result
		// Try walking through all the options in self::$_autoResolveClasses
		foreach (self::$_autoResolveClasses as $autoResolveClass) {
			//	Ignore our original guess, we know that won't work
			if ($autoResolveClass !== $extensionType) {
				$reader = self::createReader($autoResolveClass);
				if ($reader->canRead($pFilename)) {
					return $reader;
				}
			}
		}

		throw new KDAPHPExcel_Reader_Exception('Unable to identify a reader for this file');
	}	//	function createReaderForFile()
}
