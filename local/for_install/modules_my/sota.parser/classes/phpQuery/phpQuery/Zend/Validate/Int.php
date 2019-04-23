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
class Zend_Validate_Int extends Zend_Validate_Abstract
{

    const NOT_INT = 'notInt';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_INT => "'%value%' does not appear to be an integer"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid integer
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string)$value;

        $this->_setValue($valueString);

        $locale = localeconv();

        $valueFiltered = str_replace($locale['decimal_point'], '.', $valueString);
        $valueFiltered = str_replace($locale['thousands_sep'], '', $valueFiltered);

        if (strval(intval($valueFiltered)) != $valueFiltered) {
            $this->_error();
            return false;
        }

        return true;
    }

}
