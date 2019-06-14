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
class Selectbox extends Input
{
    /**
     * @var string
     */
	public static $type = self::TYPE__SELECTBOX;

    /**
     * @var array
     */
    protected $options = array();
    /**
     * @var
     */
    protected $size;

	const MAX_MULTI_SIZE = 7;

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

		if (isset($params['size']) && intval($params['size']))
			$this->size = intval($params['size']);
		elseif ($params['multiple'])
			$this->size = count($this->options) > self::MAX_MULTI_SIZE
				? self::MAX_MULTI_SIZE
				: count($this->options);
		else
			$this->size = 1;
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



    /**
     * @return int
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setSize($size)
    {
        $this->size = intval($size);

        return $this;
    }
}