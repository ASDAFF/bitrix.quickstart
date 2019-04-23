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
class Zend_Validate_Ip extends Zend_Validate_Abstract
{

    const NOT_IP_ADDRESS = 'notIpAddress';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_IP_ADDRESS => "'%value%' does not appear to be a valid IP address"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid IP address
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string)$value;

        $this->_setValue($valueString);

        if (ip2long($valueString) === false) {
            $this->_error();
            return false;
        }

        return true;
    }

}
