<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 17:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
/**
 * Class Selectbox
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Radio extends Input
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var string
     */
	public static $type = self::TYPE__RADIO;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (isset($params['options']))
			$this->options = $params['options'];
	}

    /**
     * @param array $options
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function getOptions()
    {
        return $this->options;
    }
}