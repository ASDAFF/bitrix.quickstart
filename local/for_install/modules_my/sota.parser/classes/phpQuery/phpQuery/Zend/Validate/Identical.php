<?php
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

/** Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Identical extends Zend_Validate_Abstract
{
    /**#@+
     * Error codes
     * @const string
     */
    const NOT_SAME = 'notSame';
    const MISSING_TOKEN = 'missingToken';
    /**#@-*/

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SAME => 'Tokens do not match',
        self::MISSING_TOKEN => 'No token was provided to match against',
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_token;

    /**
     * Sets validator options
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token = null)
    {
        if (null !== $token) {
            $this->setToken($token);
        }
    }

    /**
     * Set token against which to compare
     *
     * @param  string $token
     * @return Zend_Validate_Identical
     */
    public function setToken($token)
    {
        $this->_token = (string)$token;
        return $this;
    }

    /**
     * Retrieve token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        $token = $this->getToken();

        if (empty($token)) {
            $this->_error(self::MISSING_TOKEN);
            return false;
        }

        if ($value !== $token) {
            $this->_error(self::NOT_SAME);
            return false;
        }

        return true;
    }
}
