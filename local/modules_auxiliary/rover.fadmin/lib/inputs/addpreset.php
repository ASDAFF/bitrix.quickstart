<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 15.01.2016
 * Time: 23:03
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Rover\Fadmin\Tab;

Loc::loadMessages(__FILE__);
/**
 * Class Addpreset
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Addpreset extends Submit
{
	/**
	 * @var string
	 */
	public static $type = self::TYPE__ADD_PRESET;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function __construct(array $params, Tab $tab)
	{
		$params['name'] = self::$type;

		parent::__construct($params, $tab);

		// add events
		$this->addEventHandler(self::EVENT__AFTER_LOAD_VALUE, array($this,   'afterLoadValue'));
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
	 * value = default value
	 * @param Event $event
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function afterLoadValue(Event $event)
	{
		if ($event->getSender() !== $this)
			return;

		$this->value = $this->default;
	}
}