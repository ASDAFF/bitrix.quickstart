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
 * KDAPHPExcel_Writer_Excel2007_Rels
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Writer_Excel2007_Rels extends KDAPHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write relationships to XML format
	 *
	 * @param 	KDAPHPExcel	$pKDAPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	public function writeRelationships(KDAPHPExcel $pKDAPHPExcel = null)
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

		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			$customPropertyList = $pKDAPHPExcel->getProperties()->getCustomProperties();
			if (!empty($customPropertyList)) {
				// Relationship docProps/app.xml
				$this->_writeRelationship(
					$objWriter,
					4,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties',
					'docProps/custom.xml'
				);

			}

			// Relationship docProps/app.xml
			$this->_writeRelationship(
				$objWriter,
				3,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties',
				'docProps/app.xml'
			);

			// Relationship docProps/core.xml
			$this->_writeRelationship(
				$objWriter,
				2,
				'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties',
				'docProps/core.xml'
			);

			// Relationship xl/workbook.xml
			$this->_writeRelationship(
				$objWriter,
				1,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument',
				'xl/workbook.xml'
			);

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write workbook relationships to XML format
	 *
	 * @param 	KDAPHPExcel	$pKDAPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	public function writeWorkbookRelationships(KDAPHPExcel $pKDAPHPExcel = null)
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

		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Relationship styles.xml
			$this->_writeRelationship(
				$objWriter,
				1,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles',
				'styles.xml'
			);

			// Relationship theme/theme1.xml
			$this->_writeRelationship(
				$objWriter,
				2,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme',
				'theme/theme1.xml'
			);

			// Relationship sharedStrings.xml
			$this->_writeRelationship(
				$objWriter,
				3,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings',
				'sharedStrings.xml'
			);

			// Relationships with sheets
			$sheetCount = $pKDAPHPExcel->getSheetCount();
			for ($i = 0; $i < $sheetCount; ++$i) {
				$this->_writeRelationship(
					$objWriter,
					($i + 1 + 3),
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet',
					'worksheets/sheet' . ($i + 1) . '.xml'
				);
			}

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write worksheet relationships to XML format
	 *
	 * Numbering is as follows:
	 * 	rId1 				- Drawings
	 *  rId_hyperlink_x 	- Hyperlinks
	 *
	 * @param 	KDAPHPExcel_Worksheet	$pWorksheet
	 * @param 	int					$pWorksheetId
	 * @param	boolean				$includeCharts	Flag indicating if we should write charts
	 * @return 	string 				XML Output
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	public function writeWorksheetRelationships(KDAPHPExcel_Worksheet $pWorksheet = null, $pWorksheetId = 1, $includeCharts = FALSE)
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

		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Write drawing relationships?
			$d = 0;
			if ($includeCharts) {
				$charts = $pWorksheet->getChartCollection();
			} else {
				$charts = array();
			}
			if (($pWorksheet->getDrawingCollection()->count() > 0) ||
				(count($charts) > 0)) {
				$this->_writeRelationship(
					$objWriter,
					++$d,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing',
					'../drawings/drawing' . $pWorksheetId . '.xml'
				);
			}

			// Write chart relationships?
//			$chartCount = 0;
//			$charts = $pWorksheet->getChartCollection();
//			echo 'Chart Rels: ' , count($charts) , '<br />';
//			if (count($charts) > 0) {
//				foreach($charts as $chart) {
//					$this->_writeRelationship(
//						$objWriter,
//						++$d,
//						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart',
//						'../charts/chart' . ++$chartCount . '.xml'
//					);
//				}
//			}
//
			// Write hyperlink relationships?
			$i = 1;
			foreach ($pWorksheet->getHyperlinkCollection() as $hyperlink) {
				if (!$hyperlink->isInternal()) {
					$this->_writeRelationship(
						$objWriter,
						'_hyperlink_' . $i,
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink',
						$hyperlink->getUrl(),
						'External'
					);

					++$i;
				}
			}

			// Write comments relationship?
			$i = 1;
			if (count($pWorksheet->getComments()) > 0) {
				$this->_writeRelationship(
					$objWriter,
					'_comments_vml' . $i,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing',
					'../drawings/vmlDrawing' . $pWorksheetId . '.vml'
				);

				$this->_writeRelationship(
					$objWriter,
					'_comments' . $i,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments',
					'../comments' . $pWorksheetId . '.xml'
				);
			}

			// Write header/footer relationship?
			$i = 1;
			if (count($pWorksheet->getHeaderFooter()->getImages()) > 0) {
				$this->_writeRelationship(
					$objWriter,
					'_headerfooter_vml' . $i,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing',
					'../drawings/vmlDrawingHF' . $pWorksheetId . '.vml'
				);
			}

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write drawing relationships to XML format
	 *
	 * @param 	KDAPHPExcel_Worksheet	$pWorksheet
	 * @param	int					&$chartRef		Chart ID
	 * @param	boolean				$includeCharts	Flag indicating if we should write charts
	 * @return 	string 				XML Output
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	public function writeDrawingRelationships(KDAPHPExcel_Worksheet $pWorksheet = null, &$chartRef, $includeCharts = FALSE)
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

		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Loop through images and write relationships
			$i = 1;
			$iterator = $pWorksheet->getDrawingCollection()->getIterator();
			while ($iterator->valid()) {
				if ($iterator->current() instanceof KDAPHPExcel_Worksheet_Drawing
					|| $iterator->current() instanceof KDAPHPExcel_Worksheet_MemoryDrawing) {
					// Write relationship for image drawing
					$this->_writeRelationship(
						$objWriter,
						$i,
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
						'../media/' . str_replace(' ', '', $iterator->current()->getIndexedFilename())
					);
				}

				$iterator->next();
				++$i;
			}

			if ($includeCharts) {
				// Loop through charts and write relationships
				$chartCount = $pWorksheet->getChartCount();
				if ($chartCount > 0) {
					for ($c = 0; $c < $chartCount; ++$c) {
						$this->_writeRelationship(
							$objWriter,
							$i++,
							'http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart',
							'../charts/chart' . ++$chartRef . '.xml'
						);
					}
				}
			}

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write header/footer drawing relationships to XML format
	 *
	 * @param 	KDAPHPExcel_Worksheet			$pWorksheet
	 * @return 	string 						XML Output
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	public function writeHeaderFooterDrawingRelationships(KDAPHPExcel_Worksheet $pWorksheet = null)
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

		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Loop through images and write relationships
			foreach ($pWorksheet->getHeaderFooter()->getImages() as $key => $value) {
				// Write relationship for image drawing
				$this->_writeRelationship(
					$objWriter,
					$key,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
					'../media/' . $value->getIndexedFilename()
				);
			}

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write Override content type
	 *
	 * @param 	KDAPHPExcel_Shared_XMLWriter 	$objWriter 		XML Writer
	 * @param 	int							$pId			Relationship ID. rId will be prepended!
	 * @param 	string						$pType			Relationship type
	 * @param 	string 						$pTarget		Relationship target
	 * @param 	string 						$pTargetMode	Relationship target mode
	 * @throws 	KDAPHPExcel_Writer_Exception
	 */
	private function _writeRelationship(KDAPHPExcel_Shared_XMLWriter $objWriter = null, $pId = 1, $pType = '', $pTarget = '', $pTargetMode = '')
	{
		if ($pType != '' && $pTarget != '') {
			// Write relationship
			$objWriter->startElement('Relationship');
			$objWriter->writeAttribute('Id', 		'rId' . $pId);
			$objWriter->writeAttribute('Type', 		$pType);
			$objWriter->writeAttribute('Target',	$pTarget);

			if ($pTargetMode != '') {
				$objWriter->writeAttribute('TargetMode',	$pTargetMode);
			}

			$objWriter->endElement();
		} else {
			throw new KDAPHPExcel_Writer_Exception("Invalid parameters passed.");
		}
	}
}
