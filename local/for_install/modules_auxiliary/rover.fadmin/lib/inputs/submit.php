<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 18:30
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Class Submit
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Submit extends Input
{
	public static $type = self::TYPE__SUBMIT;

    const SEPARATOR = '__';

	/**
	 * @var string
	 */
	protected $popup;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (isset($params['popup']))
			$this->popup = $params['popup'];

		$this->addEventHandler(self::EVENT__AFTER_LOAD_VALUE, array($this, 'afterLoadValue'));
		$this->addEventHandler(self::EVENT__BEFORE_SAVE_VALUE, array($this,  'beforeSaveValue'));
	}

	/**
	 * not save
	 * @param Event $event
	 * @return EventResult
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function beforeSaveValue(Event $event)
	{
		return $this->getEvent()->getErrorResult($this);
	}

	/**
	 * @param Event $event
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function afterLoadValue(Event $event)
	{
		if ($event->getSender() !== $this)
			return;

		$this->value = $this->default;
	}

    /**
     * @return string
     */
    public function getPopup()
    {
        return $this->popup;
    }

    /**
     * @param $popup
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setPopup($popup)
    {
        $this->popup = $popup;

        return $this;
    }
}