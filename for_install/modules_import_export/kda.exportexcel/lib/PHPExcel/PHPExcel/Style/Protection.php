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
 * @package    KDAPHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.4.5, 2007-08-23
 */


/**
 * KDAPHPExcel_Style_Protection
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Style_Protection extends KDAPHPExcel_Style_Supervisor implements KDAPHPExcel_IComparable
{
	/** Protection styles */
	const PROTECTION_INHERIT		= 'inherit';
	const PROTECTION_PROTECTED		= 'protected';
	const PROTECTION_UNPROTECTED	= 'unprotected';

	/**
	 * Locked
	 *
	 * @var string
	 */
	protected $_locked;

	/**
	 * Hidden
	 *
	 * @var string
	 */
	protected $_hidden;

	/**
     * Create a new KDAPHPExcel_Style_Protection
	 *
	 * @param	boolean	$isSupervisor	Flag indicating if this is a supervisor or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
	 * @param	boolean	$isConditional	Flag indicating if this is a conditional style or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
     */
    public function __construct($isSupervisor = FALSE, $isConditional = FALSE)
    {
    	// Supervisor?
		parent::__construct($isSupervisor);

    	// Initialise values
		if (!$isConditional) {
	    	$this->_locked			= self::PROTECTION_INHERIT;
	    	$this->_hidden			= self::PROTECTION_INHERIT;
		}
    }

	/**
	 * Get the shared style component for the currently active cell in currently active sheet.
	 * Only used for style supervisor
	 *
	 * @return KDAPHPExcel_Style_Protection
	 */
	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getProtection();
	}

	/**
	 * Build style array from subcomponents
	 *
	 * @param array $array
	 * @return array
	 */
	public function getStyleArray($array)
	{
		return array('protection' => $array);
	}

    /**
     * Apply styles from array
     *
     * <code>
     * $objKDAPHPExcel->getActiveSheet()->getStyle('B2')->getLocked()->applyFromArray(
     *		array(
     *			'locked' => TRUE,
     *			'hidden' => FALSE
     *		)
     * );
     * </code>
     *
     * @param	array	$pStyles	Array containing style information
     * @throws	KDAPHPExcel_Exception
     * @return KDAPHPExcel_Style_Protection
     */
	public function applyFromArray($pStyles = NULL) {
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			} else {
				if (isset($pStyles['locked'])) {
					$this->setLocked($pStyles['locked']);
				}
				if (isset($pStyles['hidden'])) {
					$this->setHidden($pStyles['hidden']);
				}
			}
		} else {
			throw new KDAPHPExcel_Exception("Invalid style array passed.");
		}
		return $this;
	}

    /**
     * Get locked
     *
     * @return string
     */
    public function getLocked() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getLocked();
		}
    	return $this->_locked;
    }

    /**
     * Set locked
     *
     * @param string $pValue
     * @return KDAPHPExcel_Style_Protection
     */
    public function setLocked($pValue = self::PROTECTION_INHERIT) {
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('locked' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_locked = $pValue;
		}
		return $this;
    }

    /**
     * Get hidden
     *
     * @return string
     */
    public function getHidden() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHidden();
		}
    	return $this->_hidden;
    }

    /**
     * Set hidden
     *
     * @param string $pValue
     * @return KDAPHPExcel_Style_Protection
     */
    public function setHidden($pValue = self::PROTECTION_INHERIT) {
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('hidden' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_hidden = $pValue;
		}
		return $this;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}
    	return md5(
    		  $this->_locked
    		. $this->_hidden
    		. __CLASS__
    	);
    }

}
