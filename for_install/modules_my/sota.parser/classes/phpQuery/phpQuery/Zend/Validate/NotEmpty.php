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
class Zend_Validate_NotEmpty extends Zend_Validate_Abstract
{

    const IS_EMPTY = 'isEmpty';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::IS_EMPTY => "Value is empty, but a non-empty value is required"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is not an empty value.
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue((string)$value);

        if (is_string($value)
            && (('' === $value)
                || preg_match('/^\s+$/s', $value))
        ) {
            $this->_error();
            return false;
        } elseif (!is_string($value) && empty($value)) {
            $this->_error();
            return false;
        }

        return true;
    }

}
