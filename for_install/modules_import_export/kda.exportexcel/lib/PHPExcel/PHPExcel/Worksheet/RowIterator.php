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
 * @package	KDAPHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_Worksheet_RowIterator
 *
 * Used to iterate rows in a KDAPHPExcel_Worksheet
 *
 * @category   KDAPHPExcel
 * @package	KDAPHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Worksheet_RowIterator implements Iterator
{
	/**
	 * KDAPHPExcel_Worksheet to iterate
	 *
	 * @var KDAPHPExcel_Worksheet
	 */
	private $_subject;

	/**
	 * Current iterator position
	 *
	 * @var int
	 */
	private $_position = 1;

	/**
	 * Start position
	 *
	 * @var int
	 */
	private $_startRow = 1;


	/**
	 * Create a new row iterator
	 *
	 * @param	KDAPHPExcel_Worksheet	$subject	The worksheet to iterate over
	 * @param	integer				$startRow	The row number at which to start iterating
	 */
	public function __construct(KDAPHPExcel_Worksheet $subject = null, $startRow = 1) {
		// Set subject
		$this->_subject = $subject;
		$this->resetStart($startRow);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		unset($this->_subject);
	}

	/**
	 * (Re)Set the start row and the current row pointer
	 *
	 * @param integer	$startRow	The row number at which to start iterating
	 */
	public function resetStart($startRow = 1) {
		$this->_startRow = $startRow;
		$this->seek($startRow);
	}

	/**
	 * Set the row pointer to the selected row
	 *
	 * @param integer	$row	The row number to set the current pointer at
	 */
	public function seek($row = 1) {
		$this->_position = $row;
	}

	/**
	 * Rewind the iterator to the starting row
	 */
	public function rewind() {
		$this->_position = $this->_startRow;
	}

	/**
	 * Return the current row in this worksheet
	 *
	 * @return KDAPHPExcel_Worksheet_Row
	 */
	public function current() {
		return new KDAPHPExcel_Worksheet_Row($this->_subject, $this->_position);
	}

	/**
	 * Return the current iterator key
	 *
	 * @return int
	 */
	public function key() {
		return $this->_position;
	}

	/**
	 * Set the iterator to its next value
	 */
	public function next() {
		++$this->_position;
	}

	/**
	 * Set the iterator to its previous value
	 */
	public function prev() {
		if ($this->_position > 1)
			--$this->_position;
	}

	/**
	 * Indicate if more rows exist in the worksheet
	 *
	 * @return boolean
	 */
	public function valid() {
		return $this->_position <= $this->_subject->getHighestRow();
	}
}
