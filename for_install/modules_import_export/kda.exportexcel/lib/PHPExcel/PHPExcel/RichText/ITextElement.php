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
 * KDAPHPExcel_RichText_ITextElement
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
interface KDAPHPExcel_RichText_ITextElement
{
	/**
	 * Get text
	 *
	 * @return string	Text
	 */
	public function getText();

	/**
	 * Set text
	 *
	 * @param 	$pText string	Text
	 * @return KDAPHPExcel_RichText_ITextElement
	 */
	public function setText($pText = '');

	/**
	 * Get font
	 *
	 * @return KDAPHPExcel_Style_Font
	 */
	public function getFont();

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode();
}
