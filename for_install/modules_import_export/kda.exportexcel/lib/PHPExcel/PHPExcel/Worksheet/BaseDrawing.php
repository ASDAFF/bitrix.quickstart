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
 * @package    KDAPHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_Worksheet_BaseDrawing
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Worksheet_BaseDrawing implements KDAPHPExcel_IComparable
{
	/**
	 * Image counter
	 *
	 * @var int
	 */
	private static $_imageCounter = 0;

	/**
	 * Image index
	 *
	 * @var int
	 */
	private $_imageIndex = 0;

	/**
	 * Name
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $_description;

	/**
	 * Worksheet
	 *
	 * @var KDAPHPExcel_Worksheet
	 */
	protected $_worksheet;

	/**
	 * Coordinates
	 *
	 * @var string
	 */
	protected $_coordinates;

	/**
	 * Offset X
	 *
	 * @var int
	 */
	protected $_offsetX;

	/**
	 * Offset Y
	 *
	 * @var int
	 */
	protected $_offsetY;

	/**
	 * Width
	 *
	 * @var int
	 */
	protected $_width;

	/**
	 * Height
	 *
	 * @var int
	 */
	protected $_height;

	/**
	 * Proportional resize
	 *
	 * @var boolean
	 */
	protected $_resizeProportional;

	/**
	 * Rotation
	 *
	 * @var int
	 */
	protected $_rotation;

	/**
	 * Shadow
	 *
	 * @var KDAPHPExcel_Worksheet_Drawing_Shadow
	 */
	protected $_shadow;

    /**
     * Create a new KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_name				= '';
    	$this->_description			= '';
    	$this->_worksheet			= null;
    	$this->_coordinates			= 'A1';
    	$this->_offsetX				= 0;
    	$this->_offsetY				= 0;
    	$this->_width				= 0;
    	$this->_height				= 0;
    	$this->_resizeProportional	= true;
    	$this->_rotation			= 0;
    	$this->_shadow				= new KDAPHPExcel_Worksheet_Drawing_Shadow();

		// Set image index
		self::$_imageCounter++;
		$this->_imageIndex 			= self::$_imageCounter;
    }

    /**
     * Get image index
     *
     * @return int
     */
    public function getImageIndex() {
    	return $this->_imageIndex;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName() {
    	return $this->_name;
    }

    /**
     * Set Name
     *
     * @param string $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setName($pValue = '') {
    	$this->_name = $pValue;
    	return $this;
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription() {
    	return $this->_description;
    }

    /**
     * Set Description
     *
     * @param string $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setDescription($pValue = '') {
    	$this->_description = $pValue;
    	return $this;
    }

    /**
     * Get Worksheet
     *
     * @return KDAPHPExcel_Worksheet
     */
    public function getWorksheet() {
    	return $this->_worksheet;
    }

    /**
     * Set Worksheet
     *
     * @param 	KDAPHPExcel_Worksheet 	$pValue
     * @param 	bool				$pOverrideOld	If a Worksheet has already been assigned, overwrite it and remove image from old Worksheet?
     * @throws 	KDAPHPExcel_Exception
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setWorksheet(KDAPHPExcel_Worksheet $pValue = null, $pOverrideOld = false) {
    	if (is_null($this->_worksheet)) {
    		// Add drawing to KDAPHPExcel_Worksheet
	    	$this->_worksheet = $pValue;
	    	$this->_worksheet->getCell($this->_coordinates);
	    	$this->_worksheet->getDrawingCollection()->append($this);
    	} else {
    		if ($pOverrideOld) {
    			// Remove drawing from old KDAPHPExcel_Worksheet
    			$iterator = $this->_worksheet->getDrawingCollection()->getIterator();

    			while ($iterator->valid()) {
    				if ($iterator->current()->getHashCode() == $this->getHashCode()) {
    					$this->_worksheet->getDrawingCollection()->offsetUnset( $iterator->key() );
    					$this->_worksheet = null;
    					break;
    				}
    			}

    			// Set new KDAPHPExcel_Worksheet
    			$this->setWorksheet($pValue);
    		} else {
    			throw new KDAPHPExcel_Exception("A KDAPHPExcel_Worksheet has already been assigned. Drawings can only exist on one KDAPHPExcel_Worksheet.");
    		}
    	}
    	return $this;
    }

    /**
     * Get Coordinates
     *
     * @return string
     */
    public function getCoordinates() {
    	return $this->_coordinates;
    }

    /**
     * Set Coordinates
     *
     * @param string $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setCoordinates($pValue = 'A1') {
    	$this->_coordinates = $pValue;
    	return $this;
    }

    /**
     * Get OffsetX
     *
     * @return int
     */
    public function getOffsetX() {
    	return $this->_offsetX;
    }

    /**
     * Set OffsetX
     *
     * @param int $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setOffsetX($pValue = 0) {
    	$this->_offsetX = $pValue;
    	return $this;
    }

    /**
     * Get OffsetY
     *
     * @return int
     */
    public function getOffsetY() {
    	return $this->_offsetY;
    }

    /**
     * Set OffsetY
     *
     * @param int $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setOffsetY($pValue = 0) {
    	$this->_offsetY = $pValue;
    	return $this;
    }

    /**
     * Get Width
     *
     * @return int
     */
    public function getWidth() {
    	return $this->_width;
    }

    /**
     * Set Width
     *
     * @param int $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setWidth($pValue = 0) {
    	// Resize proportional?
    	if ($this->_resizeProportional && $pValue != 0) {
    		$ratio = $this->_height / $this->_width;
    		$this->_height = round($ratio * $pValue);
    	}

    	// Set width
    	$this->_width = $pValue;

    	return $this;
    }

    /**
     * Get Height
     *
     * @return int
     */
    public function getHeight() {
    	return $this->_height;
    }

    /**
     * Set Height
     *
     * @param int $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setHeight($pValue = 0) {
    	// Resize proportional?
    	if ($this->_resizeProportional && $pValue != 0) {
    		$ratio = $this->_width / $this->_height;
    		$this->_width = round($ratio * $pValue);
    	}

    	// Set height
    	$this->_height = $pValue;

    	return $this;
    }

    /**
     * Set width and height with proportional resize
	 * Example:
	 * <code>
	 * $objDrawing->setResizeProportional(true);
	 * $objDrawing->setWidthAndHeight(160,120);
	 * </code>
	 *
     * @author Vincent@luo MSN:kele_100@hotmail.com
     * @param int $width
     * @param int $height
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
	public function setWidthAndHeight($width = 0, $height = 0) {
		$xratio = $width / $this->_width;
		$yratio = $height / $this->_height;
		if ($this->_resizeProportional && !($width == 0 || $height == 0)) {
			if (($xratio * $this->_height) < $height) {
				$this->_height = ceil($xratio * $this->_height);
				$this->_width  = $width;
			} else {
				$this->_width	= ceil($yratio * $this->_width);
				$this->_height	= $height;
			}
		}
		return $this;
	}

    /**
     * Get ResizeProportional
     *
     * @return boolean
     */
    public function getResizeProportional() {
    	return $this->_resizeProportional;
    }

    /**
     * Set ResizeProportional
     *
     * @param boolean $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setResizeProportional($pValue = true) {
    	$this->_resizeProportional = $pValue;
    	return $this;
    }

    /**
     * Get Rotation
     *
     * @return int
     */
    public function getRotation() {
    	return $this->_rotation;
    }

    /**
     * Set Rotation
     *
     * @param int $pValue
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setRotation($pValue = 0) {
    	$this->_rotation = $pValue;
    	return $this;
    }

    /**
     * Get Shadow
     *
     * @return KDAPHPExcel_Worksheet_Drawing_Shadow
     */
    public function getShadow() {
    	return $this->_shadow;
    }

    /**
     * Set Shadow
     *
     * @param 	KDAPHPExcel_Worksheet_Drawing_Shadow $pValue
     * @throws 	KDAPHPExcel_Exception
     * @return KDAPHPExcel_Worksheet_BaseDrawing
     */
    public function setShadow(KDAPHPExcel_Worksheet_Drawing_Shadow $pValue = null) {
   		$this->_shadow = $pValue;
   		return $this;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_name
    		. $this->_description
    		. $this->_worksheet->getHashCode()
    		. $this->_coordinates
    		. $this->_offsetX
    		. $this->_offsetY
    		. $this->_width
    		. $this->_height
    		. $this->_rotation
    		. $this->_shadow->getHashCode()
    		. __CLASS__
    	);
    }

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
