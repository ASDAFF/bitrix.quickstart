<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 18:26
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
use Bitrix\Main\Event;

/**
 * Class Number
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Number extends Text
{
	/**
	 * @var string
	 */
	public static $type = self::TYPE__NUMBER;


	/**
	 * @var int
	 */
	protected $min;

	/**
	 * @var int
	 */
	protected $max;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (isset($params['min']))
			$this->min = (int)$params['min'];

		if (isset($params['max']))
			$this->max = (int)$params['max'];

		$this->addEventHandler(self::EVENT__BEFORE_SAVE_REQUEST, array($this, 'beforeSaveRequest'));
	}



	/**
	 * @param Event $event
	 * @return \Bitrix\Main\EventResult
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function beforeSaveRequest(Event $event)
	{
		if ($event->getSender() !== $this)
			return $this->getEvent()->getErrorResult($this);

		$value = $event->getParameter('value');

		// not integer
		if ($value != intval($value))
			$value = $this->default;

		// min
		if (!is_null($this->min) && $value < $this->min)
			$value = $this->default;

		// max
		if (!is_null($this->max) && $value > $this->max)
			$value = $this->default;

		return $this->getEvent()->getSuccessResult($this, compact('value'));
	}

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

}