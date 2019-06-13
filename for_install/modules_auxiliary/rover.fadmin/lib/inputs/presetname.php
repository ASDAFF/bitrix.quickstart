<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 07.12.2016
 * Time: 1:06
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Bitrix\Main\Localization\Loc;
use Rover\Fadmin\Tab;
use Bitrix\Main\Event;

Loc::loadMessages(__FILE__);

/**
 * Class PresetName
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class PresetName extends Text
{
	public static $type = self::TYPE__PRESET_NAME;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (!$this->tab->isPreset())
			return;

		$presetId = $this->tab->getPresetId();

		if (!$presetId)
			return;

		$value = $this->getValue();
		if (empty($value))
			$this->setValue($this->tab->options
				->preset->getNameById($presetId, $this->tab->getSiteId()));

		$this->addEventHandler(self::EVENT__BEFORE_SAVE_REQUEST, array($this, 'beforeSaveRequest'));
	}

	/**
	 * @param Event $event
	 * @return \Bitrix\Main\EventResult|bool
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function beforeSaveRequest(Event $event)
	{
		if ($event->getSender() !== $this)
			return $this->getEvent()->getErrorResult($this);

		if (!$this->tab->isPreset())
			return true;

		$presetId = $this->tab->getPresetId();

		if (!$presetId)
			return true;

		$value = $event->getParameter('value');

		if (empty($value)){
			$this->tab->options->message->addError(
				Loc::getMessage('rover-fa__presetname-no-name',
                    array('#last-preset-name#' => $this->getValue())));
			return $this->getEvent()->getErrorResult($this);
		}

		$this->tab->options->preset->updateName($presetId, $value,
			$this->tab->getSiteId());

		return $this->getEvent()->getSuccessResult($this, compact('value'));
	}
}