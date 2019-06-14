<?php

/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */


/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Hex extends Zend_Validate_Abstract
{
    /**
     * Validation failure message key for when the value contains characters other than hexadecimal digits
     */
    const NOT_HEX = 'notHex';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_HEX => "'%value%' has not only hexadecimal digit characters"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains only hexadecimal digit characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string)$value;

        $this->_setValue($valueString);

        if (!ctype_xdigit($valueString)) {
            $this->_error();
            return false;
        }

        return true;
    }

}
