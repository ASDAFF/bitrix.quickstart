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
 * @package    KDAPHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_RichText
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_RichText implements KDAPHPExcel_IComparable
{
    /**
     * Rich text elements
     *
     * @var KDAPHPExcel_RichText_ITextElement[]
     */
    private $_richTextElements;

    /**
     * Create a new KDAPHPExcel_RichText instance
     *
     * @param KDAPHPExcel_Cell $pCell
     * @throws KDAPHPExcel_Exception
     */
    public function __construct(KDAPHPExcel_Cell $pCell = null)
    {
        // Initialise variables
        $this->_richTextElements = array();

        // Rich-Text string attached to cell?
        if ($pCell !== NULL) {
            // Add cell text and style
            if ($pCell->getValue() != "") {
                $objRun = new KDAPHPExcel_RichText_Run($pCell->getValue());
                $objRun->setFont(clone $pCell->getParent()->getStyle($pCell->getCoordinate())->getFont());
                $this->addText($objRun);
            }

            // Set parent value
            $pCell->setValueExplicit($this, KDAPHPExcel_Cell_DataType::TYPE_STRING);
        }
    }

    /**
     * Add text
     *
     * @param KDAPHPExcel_RichText_ITextElement $pText Rich text element
     * @throws KDAPHPExcel_Exception
     * @return KDAPHPExcel_RichText
     */
    public function addText(KDAPHPExcel_RichText_ITextElement $pText = null)
    {
        $this->_richTextElements[] = $pText;
        return $this;
    }

    /**
     * Create text
     *
     * @param string $pText Text
     * @return KDAPHPExcel_RichText_TextElement
     * @throws KDAPHPExcel_Exception
     */
    public function createText($pText = '')
    {
        $objText = new KDAPHPExcel_RichText_TextElement($pText);
        $this->addText($objText);
        return $objText;
    }

    /**
     * Create text run
     *
     * @param string $pText Text
     * @return KDAPHPExcel_RichText_Run
     * @throws KDAPHPExcel_Exception
     */
    public function createTextRun($pText = '')
    {
        $objText = new KDAPHPExcel_RichText_Run($pText);
        $this->addText($objText);
        return $objText;
    }

    /**
     * Get plain text
     *
     * @return string
     */
    public function getPlainText()
    {
        // Return value
        $returnValue = '';

        // Loop through all KDAPHPExcel_RichText_ITextElement
        foreach ($this->_richTextElements as $text) {
            $returnValue .= $text->getText();
        }

        // Return
        return $returnValue;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getPlainText();
    }

    /**
     * Get Rich Text elements
     *
     * @return KDAPHPExcel_RichText_ITextElement[]
     */
    public function getRichTextElements()
    {
        return $this->_richTextElements;
    }

    /**
     * Set Rich Text elements
     *
     * @param KDAPHPExcel_RichText_ITextElement[] $pElements Array of elements
     * @throws KDAPHPExcel_Exception
     * @return KDAPHPExcel_RichText
     */
    public function setRichTextElements($pElements = null)
    {
        if (is_array($pElements)) {
            $this->_richTextElements = $pElements;
        } else {
            throw new KDAPHPExcel_Exception("Invalid KDAPHPExcel_RichText_ITextElement[] array passed.");
        }
        return $this;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode()
    {
        $hashElements = '';
        foreach ($this->_richTextElements as $element) {
            $hashElements .= $element->getHashCode();
        }

        return md5(
              $hashElements
            . __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
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
