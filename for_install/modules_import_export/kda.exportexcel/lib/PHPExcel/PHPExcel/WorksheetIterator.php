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
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_WorksheetIterator
 *
 * Used to iterate worksheets in KDAPHPExcel
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_WorksheetIterator implements Iterator
{
    /**
     * Spreadsheet to iterate
     *
     * @var KDAPHPExcel
     */
    private $_subject;

    /**
     * Current iterator position
     *
     * @var int
     */
    private $_position = 0;

    /**
     * Create a new worksheet iterator
     *
     * @param KDAPHPExcel         $subject
     */
    public function __construct(KDAPHPExcel $subject = null)
    {
        // Set subject
        $this->_subject = $subject;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_subject);
    }

    /**
     * Rewind iterator
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Current KDAPHPExcel_Worksheet
     *
     * @return KDAPHPExcel_Worksheet
     */
    public function current()
    {
        return $this->_subject->getSheet($this->_position);
    }

    /**
     * Current key
     *
     * @return int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * Next value
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * More KDAPHPExcel_Worksheet instances available?
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_position < $this->_subject->getSheetCount();
    }
}
