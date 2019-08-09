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
 * KDAPHPExcel_Worksheet_MemoryDrawing
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Worksheet_MemoryDrawing extends KDAPHPExcel_Worksheet_BaseDrawing implements KDAPHPExcel_IComparable
{
	/* Rendering functions */
	const RENDERING_DEFAULT					= 'imagepng';
	const RENDERING_PNG						= 'imagepng';
	const RENDERING_GIF						= 'imagegif';
	const RENDERING_JPEG					= 'imagejpeg';

	/* MIME types */
	const MIMETYPE_DEFAULT					= 'image/png';
	const MIMETYPE_PNG						= 'image/png';
	const MIMETYPE_GIF						= 'image/gif';
	const MIMETYPE_JPEG						= 'image/jpeg';

	/**
	 * Image resource
	 *
	 * @var resource
	 */
	private $_imageResource;

	/**
	 * Rendering function
	 *
	 * @var string
	 */
	private $_renderingFunction;

	/**
	 * Mime type
	 *
	 * @var string
	 */
	private $_mimeType;

	/**
	 * Unique name
	 *
	 * @var string
	 */
	private $_uniqueName;

	/**
	 * Path
	 *
	 * @var string
	 */
	private $_path;
	
    /**
     * Create a new KDAPHPExcel_Worksheet_MemoryDrawing
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_imageResource		= null;
    	$this->_renderingFunction 	= self::RENDERING_DEFAULT;
    	$this->_mimeType			= self::MIMETYPE_DEFAULT;
    	$this->_uniqueName			= md5(rand(0, 9999). time() . rand(0, 9999));

    	// Initialize parent
    	parent::__construct();
    }
	
    public function getPath() {
    	return $this->_path;
    }

    public function setPath($path) {
    	$this->_path = $path;
    }
	
    public function createPath() {
		if(!$this->getRenderingFunction() || !$this->getImageResource() || !$this->getIndexedFilename()) return;
		
		$temp_path = CFile::GetTempName('', $this->getIndexedFilename());
		$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
		\Bitrix\Main\IO\Directory::createDirectory($dir);
		
		if(call_user_func($this->getRenderingFunction(), $this->getImageResource(), $temp_path))
		{
			$draw = substr($temp_path, strlen($_SERVER["DOCUMENT_ROOT"]));
			$this->setPath($draw);
		}
		$this->setImageResource();
    }

    /**
     * Get image resource
     *
     * @return resource
     */
    public function getImageResource() {
    	return $this->_imageResource;
    }

    /**
     * Set image resource
     *
     * @param	$value resource
     * @return KDAPHPExcel_Worksheet_MemoryDrawing
     */
    public function setImageResource($value = null) {
    	$this->_imageResource = $value;

    	if (!is_null($this->_imageResource)) {
	    	// Get width/height
	    	$this->_width	= imagesx($this->_imageResource);
	    	$this->_height	= imagesy($this->_imageResource);
			$this->createPath();
    	}
    	return $this;
    }

    /**
     * Get rendering function
     *
     * @return string
     */
    public function getRenderingFunction() {
    	return $this->_renderingFunction;
    }

    /**
     * Set rendering function
     *
     * @param string $value
     * @return KDAPHPExcel_Worksheet_MemoryDrawing
     */
    public function setRenderingFunction($value = KDAPHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT) {
    	$this->_renderingFunction = $value;
		$this->createPath();
    	return $this;
    }

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType() {
    	return $this->_mimeType;
    }

    /**
     * Set mime type
     *
     * @param string $value
     * @return KDAPHPExcel_Worksheet_MemoryDrawing
     */
    public function setMimeType($value = KDAPHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT) {
    	$this->_mimeType = $value;
    	return $this;
    }

    /**
     * Get indexed filename (using image index)
     *
     * @return string
     */
    public function getIndexedFilename() {
		$extension 	= strtolower($this->getMimeType());
		$extension 	= explode('/', $extension);
		$extension 	= $extension[1];

    	return $this->_uniqueName . $this->getImageIndex() . '.' . $extension;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_renderingFunction
    		. $this->_mimeType
    		. $this->_uniqueName
    		. parent::getHashCode()
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
