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
 * @package    KDAPHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_Writer_Excel2007_Workbook
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Writer_Excel2007_Workbook extends KDAPHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write workbook to XML format
	 *
	 * @param 	KDAPHPExcel	$pKDAPHPExcel
	 * @param	boolean		$recalcRequired	Indicate whether formulas should be recalculated before writing
	 * @return 	string 		XML Output
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	public function writeWorkbook(KDAPHPExcel $pKDAPHPExcel = null, $recalcRequired = FALSE)
	{
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new KDAPHPExcel_Shared_XMLWriter(KDAPHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		} else {
			$objWriter = new KDAPHPExcel_Shared_XMLWriter(KDAPHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');

		// workbook
		$objWriter->startElement('workbook');
		$objWriter->writeAttribute('xml:space', 'preserve');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
		$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

			// fileVersion
			$this->_writeFileVersion($objWriter);

			// workbookPr
			$this->_writeWorkbookPr($objWriter);

			// workbookProtection
			$this->_writeWorkbookProtection($objWriter, $pKDAPHPExcel);

			// bookViews
			if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
				$this->_writeBookViews($objWriter, $pKDAPHPExcel);
			}

			// sheets
			$this->_writeSheets($objWriter, $pKDAPHPExcel);

			// definedNames
			$this->_writeDefinedNames($objWriter, $pKDAPHPExcel);

			// calcPr
			$this->_writeCalcPr($objWriter,$recalcRequired);

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write file version
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter $objWriter 		XML Writer
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeFileVersion(KDAPHPExcel_Shared_XMLWriter $objWriter = null)
	{
		$objWriter->startElement('fileVersion');
		$objWriter->writeAttribute('appName', 'xl');
		$objWriter->writeAttribute('lastEdited', '4');
		$objWriter->writeAttribute('lowestEdited', '4');
		$objWriter->writeAttribute('rupBuild', '4505');
		$objWriter->endElement();
	}

	/**
	 * Write WorkbookPr
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter $objWriter 		XML Writer
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeWorkbookPr(KDAPHPExcel_Shared_XMLWriter $objWriter = null)
	{
		$objWriter->startElement('workbookPr');

		if (KDAPHPExcel_Shared_Date::getExcelCalendar() == KDAPHPExcel_Shared_Date::CALENDAR_MAC_1904) {
			$objWriter->writeAttribute('date1904', '1');
		}

		$objWriter->writeAttribute('codeName', 'ThisWorkbook');

		$objWriter->endElement();
	}

	/**
	 * Write BookViews
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel					$pKDAPHPExcel
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeBookViews(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel $pKDAPHPExcel = null)
	{
		// bookViews
		$objWriter->startElement('bookViews');

			// workbookView
			$objWriter->startElement('workbookView');

			$objWriter->writeAttribute('activeTab', $pKDAPHPExcel->getActiveSheetIndex());
			$objWriter->writeAttribute('autoFilterDateGrouping', '1');
			$objWriter->writeAttribute('firstSheet', '0');
			$objWriter->writeAttribute('minimized', '0');
			$objWriter->writeAttribute('showHorizontalScroll', '1');
			$objWriter->writeAttribute('showSheetTabs', '1');
			$objWriter->writeAttribute('showVerticalScroll', '1');
			$objWriter->writeAttribute('tabRatio', '600');
			$objWriter->writeAttribute('visibility', 'visible');

			$objWriter->endElement();

		$objWriter->endElement();
	}

	/**
	 * Write WorkbookProtection
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel					$pKDAPHPExcel
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeWorkbookProtection(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel $pKDAPHPExcel = null)
	{
		if ($pKDAPHPExcel->getSecurity()->isSecurityEnabled()) {
			$objWriter->startElement('workbookProtection');
			$objWriter->writeAttribute('lockRevision',		($pKDAPHPExcel->getSecurity()->getLockRevision() ? 'true' : 'false'));
			$objWriter->writeAttribute('lockStructure', 	($pKDAPHPExcel->getSecurity()->getLockStructure() ? 'true' : 'false'));
			$objWriter->writeAttribute('lockWindows', 		($pKDAPHPExcel->getSecurity()->getLockWindows() ? 'true' : 'false'));

			if ($pKDAPHPExcel->getSecurity()->getRevisionsPassword() != '') {
				$objWriter->writeAttribute('revisionsPassword',	$pKDAPHPExcel->getSecurity()->getRevisionsPassword());
			}

			if ($pKDAPHPExcel->getSecurity()->getWorkbookPassword() != '') {
				$objWriter->writeAttribute('workbookPassword',	$pKDAPHPExcel->getSecurity()->getWorkbookPassword());
			}

			$objWriter->endElement();
		}
	}

	/**
	 * Write calcPr
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter		XML Writer
	 * @param	boolean						$recalcRequired	Indicate whether formulas should be recalculated before writing
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeCalcPr(KDAPHPExcel_Shared_XMLWriter $objWriter = null, $recalcRequired = TRUE)
	{
		$objWriter->startElement('calcPr');

		$objWriter->writeAttribute('calcId', 			'124519');
		$objWriter->writeAttribute('calcMode', 			'auto');
		//	fullCalcOnLoad isn't needed if we've recalculating for the save
		$objWriter->writeAttribute('fullCalcOnLoad', 	($recalcRequired) ? '0' : '1');

		$objWriter->endElement();
	}

	/**
	 * Write sheets
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel					$pKDAPHPExcel
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeSheets(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel $pKDAPHPExcel = null)
	{
		// Write sheets
		$objWriter->startElement('sheets');
		$sheetCount = $pKDAPHPExcel->getSheetCount();
		for ($i = 0; $i < $sheetCount; ++$i) {
			// sheet
			$this->_writeSheet(
				$objWriter,
				$pKDAPHPExcel->getSheet($i)->getTitle(),
				($i + 1),
				($i + 1 + 3),
				$pKDAPHPExcel->getSheet($i)->getSheetState()
			);
		}

		$objWriter->endElement();
	}

	/**
	 * Write sheet
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	string 						$pSheetname 		Sheet name
	 * @param 	int							$pSheetId	 		Sheet id
	 * @param 	int							$pRelId				Relationship ID
	 * @param   string                      $sheetState         Sheet state (visible, hidden, veryHidden)
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeSheet(KDAPHPExcel_Shared_XMLWriter $objWriter = null, $pSheetname = '', $pSheetId = 1, $pRelId = 1, $sheetState = 'visible')
	{
		if ($pSheetname != '') {
			// Write sheet
			$objWriter->startElement('sheet');
			$objWriter->writeAttribute('name', 		$pSheetname);
			$objWriter->writeAttribute('sheetId', 	$pSheetId);
			if ($sheetState != 'visible' && $sheetState != '') {
				$objWriter->writeAttribute('state', $sheetState);
			}
			$objWriter->writeAttribute('r:id', 		'rId' . $pRelId);
			$objWriter->endElement();
		} else {
			throw new KDAPHPExcel_Writer_Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write Defined Names
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel					$pKDAPHPExcel
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeDefinedNames(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel $pKDAPHPExcel = null)
	{
		// Write defined names
		$objWriter->startElement('definedNames');

		// Named ranges
		if (count($pKDAPHPExcel->getNamedRanges()) > 0) {
			// Named ranges
			$this->_writeNamedRanges($objWriter, $pKDAPHPExcel);
		}

		// Other defined names
		$sheetCount = $pKDAPHPExcel->getSheetCount();
		for ($i = 0; $i < $sheetCount; ++$i) {
			// definedName for autoFilter
			$this->_writeDefinedNameForAutofilter($objWriter, $pKDAPHPExcel->getSheet($i), $i);

			// definedName for Print_Titles
			$this->_writeDefinedNameForPrintTitles($objWriter, $pKDAPHPExcel->getSheet($i), $i);

			// definedName for Print_Area
			$this->_writeDefinedNameForPrintArea($objWriter, $pKDAPHPExcel->getSheet($i), $i);
		}

		$objWriter->endElement();
	}

	/**
	 * Write named ranges
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel					$pKDAPHPExcel
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeNamedRanges(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel $pKDAPHPExcel)
	{
		// Loop named ranges
		$namedRanges = $pKDAPHPExcel->getNamedRanges();
		foreach ($namedRanges as $namedRange) {
			$this->_writeDefinedNameForNamedRange($objWriter, $namedRange);
		}
	}

	/**
	 * Write Defined Name for named range
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel_NamedRange			$pNamedRange
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeDefinedNameForNamedRange(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel_NamedRange $pNamedRange)
	{
		// definedName for named range
		$objWriter->startElement('definedName');
		$objWriter->writeAttribute('name',			$pNamedRange->getName());
		if ($pNamedRange->getLocalOnly()) {
			$objWriter->writeAttribute('localSheetId',	$pNamedRange->getScope()->getParent()->getIndex($pNamedRange->getScope()));
		}

		// Create absolute coordinate and write as raw text
		$range = KDAPHPExcel_Cell::splitRange($pNamedRange->getRange());
		for ($i = 0; $i < count($range); $i++) {
			$range[$i][0] = '\'' . str_replace("'", "''", $pNamedRange->getWorksheet()->getTitle()) . '\'!' . KDAPHPExcel_Cell::absoluteReference($range[$i][0]);
			if (isset($range[$i][1])) {
				$range[$i][1] = KDAPHPExcel_Cell::absoluteReference($range[$i][1]);
			}
		}
		$range = KDAPHPExcel_Cell::buildRange($range);

		$objWriter->writeRawData($range);

		$objWriter->endElement();
	}

	/**
	 * Write Defined Name for autoFilter
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel_Worksheet			$pSheet
	 * @param 	int							$pSheetId
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeDefinedNameForAutofilter(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel_Worksheet $pSheet = null, $pSheetId = 0)
	{
		// definedName for autoFilter
		$autoFilterRange = $pSheet->getAutoFilter()->getRange();
		if (!empty($autoFilterRange)) {
			$objWriter->startElement('definedName');
			$objWriter->writeAttribute('name',			'_xlnm._FilterDatabase');
			$objWriter->writeAttribute('localSheetId',	$pSheetId);
			$objWriter->writeAttribute('hidden',		'1');

			// Create absolute coordinate and write as raw text
			$range = KDAPHPExcel_Cell::splitRange($autoFilterRange);
			$range = $range[0];
			//	Strip any worksheet ref so we can make the cell ref absolute
			if (strpos($range[0],'!') !== false) {
				list($ws,$range[0]) = explode('!',$range[0]);
			}

			$range[0] = KDAPHPExcel_Cell::absoluteCoordinate($range[0]);
			$range[1] = KDAPHPExcel_Cell::absoluteCoordinate($range[1]);
			$range = implode(':', $range);

			$objWriter->writeRawData('\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . $range);

			$objWriter->endElement();
		}
	}

	/**
	 * Write Defined Name for PrintTitles
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel_Worksheet			$pSheet
	 * @param 	int							$pSheetId
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeDefinedNameForPrintTitles(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel_Worksheet $pSheet = null, $pSheetId = 0)
	{
		// definedName for PrintTitles
		if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet() || $pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
			$objWriter->startElement('definedName');
			$objWriter->writeAttribute('name',			'_xlnm.Print_Titles');
			$objWriter->writeAttribute('localSheetId',	$pSheetId);

			// Setting string
			$settingString = '';

			// Columns to repeat
			if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
				$repeat = $pSheet->getPageSetup()->getColumnsToRepeatAtLeft();

				$settingString .= '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
			}

			// Rows to repeat
			if ($pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
				if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
					$settingString .= ',';
				}

				$repeat = $pSheet->getPageSetup()->getRowsToRepeatAtTop();

				$settingString .= '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
			}

			$objWriter->writeRawData($settingString);

			$objWriter->endElement();
		}
	}

	/**
	 * Write Defined Name for PrintTitles
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter	$objWriter 		XML Writer
	 * @param 	KDAPHPExcel_Worksheet			$pSheet
	 * @param 	int							$pSheetId
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeDefinedNameForPrintArea(KDAPHPExcel_Shared_XMLWriter $objWriter = null, KDAPHPExcel_Worksheet $pSheet = null, $pSheetId = 0)
	{
		// definedName for PrintArea
		if ($pSheet->getPageSetup()->isPrintAreaSet()) {
			$objWriter->startElement('definedName');
			$objWriter->writeAttribute('name',			'_xlnm.Print_Area');
			$objWriter->writeAttribute('localSheetId',	$pSheetId);

			// Setting string
			$settingString = '';

			// Print area
			$printArea = KDAPHPExcel_Cell::splitRange($pSheet->getPageSetup()->getPrintArea());

			$chunks = array();
			foreach ($printArea as $printAreaRect) {
				$printAreaRect[0] = KDAPHPExcel_Cell::absoluteReference($printAreaRect[0]);
				$printAreaRect[1] = KDAPHPExcel_Cell::absoluteReference($printAreaRect[1]);
				$chunks[] = '\'' . str_replace("'", "''", $pSheet->getTitle()) . '\'!' . implode(':', $printAreaRect);
			}

			$objWriter->writeRawData(implode(',', $chunks));

			$objWriter->endElement();
		}
	}
}
