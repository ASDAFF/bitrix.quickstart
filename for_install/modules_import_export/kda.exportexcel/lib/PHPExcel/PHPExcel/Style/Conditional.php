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
 * @package    KDAPHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_Style_Conditional
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Style_Conditional implements KDAPHPExcel_IComparable
{
	/* Condition types */
	const CONDITION_NONE					= 'none';
	const CONDITION_CELLIS					= 'cellIs';
	const CONDITION_CONTAINSTEXT			= 'containsText';
	const CONDITION_EXPRESSION 				= 'expression';

	/* Operator types */
	const OPERATOR_NONE						= '';
	const OPERATOR_BEGINSWITH				= 'beginsWith';
	const OPERATOR_ENDSWITH					= 'endsWith';
	const OPERATOR_EQUAL					= 'equal';
	const OPERATOR_GREATERTHAN				= 'greaterThan';
	const OPERATOR_GREATERTHANOREQUAL		= 'greaterThanOrEqual';
	const OPERATOR_LESSTHAN					= 'lessThan';
	const OPERATOR_LESSTHANOREQUAL			= 'lessThanOrEqual';
	const OPERATOR_NOTEQUAL					= 'notEqual';
	const OPERATOR_CONTAINSTEXT				= 'containsText';
	const OPERATOR_NOTCONTAINS				= 'notContains';
	const OPERATOR_BETWEEN					= 'between';

	/**
	 * Condition type
	 *
	 * @var int
	 */
	private $_conditionType;

	/**
	 * Operator type
	 *
	 * @var int
	 */
	private $_operatorType;

	/**
	 * Text
	 *
	 * @var string
	 */
	private $_text;

	/**
	 * Condition
	 *
	 * @var string[]
	 */
	private $_condition = array();

	/**
	 * Style
	 *
	 * @var KDAPHPExcel_Style
	 */
	private $_style;

    /**
     * Create a new KDAPHPExcel_Style_Conditional
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_conditionType		= KDAPHPExcel_Style_Conditional::CONDITION_NONE;
    	$this->_operatorType		= KDAPHPExcel_Style_Conditional::OPERATOR_NONE;
    	$this->_text    			= null;
    	$this->_condition			= array();
    	$this->_style				= new KDAPHPExcel_Style(FALSE, TRUE);
    }

    /**
     * Get Condition type
     *
     * @return string
     */
    public function getConditionType() {
    	return $this->_conditionType;
    }

    /**
     * Set Condition type
     *
     * @param string $pValue	KDAPHPExcel_Style_Conditional condition type
     * @return KDAPHPExcel_Style_Conditional
     */
    public function setConditionType($pValue = KDAPHPExcel_Style_Conditional::CONDITION_NONE) {
    	$this->_conditionType = $pValue;
    	return $this;
    }

    /**
     * Get Operator type
     *
     * @return string
     */
    public function getOperatorType() {
    	return $this->_operatorType;
    }

    /**
     * Set Operator type
     *
     * @param string $pValue	KDAPHPExcel_Style_Conditional operator type
     * @return KDAPHPExcel_Style_Conditional
     */
    public function setOperatorType($pValue = KDAPHPExcel_Style_Conditional::OPERATOR_NONE) {
    	$this->_operatorType = $pValue;
    	return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() {
        return $this->_text;
    }

    /**
     * Set text
     *
     * @param string $value
     * @return KDAPHPExcel_Style_Conditional
     */
    public function setText($value = null) {
           $this->_text = $value;
           return $this;
    }

    /**
     * Get Condition
     *
     * @deprecated Deprecated, use getConditions instead
     * @return string
     */
    public function getCondition() {
    	if (isset($this->_condition[0])) {
    		return $this->_condition[0];
    	}

    	return '';
    }

    /**
     * Set Condition
     *
     * @deprecated Deprecated, use setConditions instead
     * @param string $pValue	Condition
     * @return KDAPHPExcel_Style_Conditional
     */
    public function setCondition($pValue = '') {
    	if (!is_array($pValue))
    		$pValue = array($pValue);

    	return $this->setConditions($pValue);
    }

    /**
     * Get Conditions
     *
     * @return string[]
     */
    public function getConditions() {
    	return $this->_condition;
    }

    /**
     * Set Conditions
     *
     * @param string[] $pValue	Condition
     * @return KDAPHPExcel_Style_Conditional
     */
    public function setConditions($pValue) {
    	if (!is_array($pValue))
    		$pValue = array($pValue);

    	$this->_condition = $pValue;
    	return $this;
    }

    /**
     * Add Condition
     *
     * @param string $pValue	Condition
     * @return KDAPHPExcel_Style_Conditional
     */
    public function addCondition($pValue = '') {
    	$this->_condition[] = $pValue;
    	return $this;
    }

    /**
     * Get Style
     *
     * @return KDAPHPExcel_Style
     */
    public function getStyle() {
    	return $this->_style;
    }

    /**
     * Set Style
     *
     * @param 	KDAPHPExcel_Style $pValue
     * @throws 	KDAPHPExcel_Exception
     * @return KDAPHPExcel_Style_Conditional
     */
    public function setStyle(KDAPHPExcel_Style $pValue = null) {
   		$this->_style = $pValue;
   		return $this;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_conditionType
    		. $this->_operatorType
    		. implode(';', $this->_condition)
    		. $this->_style->getHashCode()
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
