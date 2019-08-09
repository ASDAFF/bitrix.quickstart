<?php
/**
 * KDAPHPExcel
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
 * @package    KDAPHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_RichText_Run
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_RichText_Run extends KDAPHPExcel_RichText_TextElement implements KDAPHPExcel_RichText_ITextElement
{
	/**
	 * Font
	 *
	 * @var KDAPHPExcel_Style_Font
	 */
	private $_font;

    /**
     * Create a new KDAPHPExcel_RichText_Run instance
     *
     * @param 	string		$pText		Text
     */
    public function __construct($pText = '')
    {
    	// Initialise variables
    	$this->setText($pText);
    	$this->_font = new KDAPHPExcel_Style_Font();
    }

	/**
	 * Get font
	 *
	 * @return KDAPHPExcel_Style_Font
	 */
	public function getFont() {
		return $this->_font;
	}

	/**
	 * Set font
	 *
	 * @param	KDAPHPExcel_Style_Font		$pFont		Font
	 * @throws 	KDAPHPExcel_Exception
	 * @return KDAPHPExcel_RichText_ITextElement
	 */
	public function setFont(KDAPHPExcel_Style_Font $pFont = null) {
		$this->_font = $pFont;
		return $this;
	}

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->getText()
    		. $this->_font->getHashCode()
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
