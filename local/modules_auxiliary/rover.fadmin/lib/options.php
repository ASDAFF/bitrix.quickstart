<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 08.01.2016
 * Time: 18:35
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main;
use \Bitrix\Main\ArgumentNullException;
use \Rover\Fadmin\Inputs\Input;
use \Bitrix\Main\Application;
use \Rover\Fadmin\Options\Message;
use \Rover\Fadmin\Options\Settings;
use \Rover\Fadmin\Options\Event;
use \Rover\Fadmin\Options\TabMap;
use \Rover\Fadmin\Options\Preset;

Loc::LoadMessages(__FILE__);
/**
 * Class Options
 * ����������� ����� ������� �����.
 * ������ ���� ����������� �������, ������� ���������� ������� � ������� � ���������� ����������
 *
 * @package Rover\Fadmin
 * @author  Pavel Shulaev (http://rover-it.me)
 */
abstract class Options
{
	/**
	 * events
	 */
	const EVENT__BEFORE_GET_REQUEST     = 'beforeGetRequest';
	const EVENT__BEFORE_REDIRECT_AFTER_REQUEST  = 'beforeRedirectAfterRequest';
	const EVENT__BEFORE_ADD_VALUES_FROM_REQUEST = 'beforeAddValuesFromRequest';
	const EVENT__BEFORE_ADD_VALUES_TO_TAB_FROM_REQUEST = 'beforeAddValuesToTabFromRequest';
	const EVENT__AFTER_ADD_VALUES_FROM_REQUEST  = 'afterAddValuesFromRequest';
	const EVENT__BEFORE_ADD_PRESET      = 'beforeAddPreset';
	const EVENT__AFTER_ADD_PRESET       = 'afterAddPreset';
	const EVENT__BEFORE_REMOVE_PRESET   = 'beforeRemovePreset';
	const EVENT__AFTER_REMOVE_PRESET    = 'afterRemovePreset';
	const EVENT__BEFORE_MAKE_PRESET_TAB = 'beforeMakePresetTab';
	const EVENT__AFTER_MAKE_PRESET_TAB  = 'afterMakePresetTab';
	const EVENT__BEFORE_GET_TAB_INFO    = 'beforeGetTabInfo';
	const EVENT__AFTER_GET_TABS         = 'afterGetTabs';
	const EVENT__BEFORE_SHOW_TAB        = 'beforeShowTab';

	const SEPARATOR = '__';

	/**
	 * current module id
	 * @var string
	 */
	protected $moduleId;

	/**
	 * tabs helper
	 * @var TabMap
	 */
	public $tabMap;

	/**
	 * message driver
	 * @var Message
	 */
	public $message;

	/**
	 * options values cache
	 * @var array
	 */
	protected $cache = array();

	/**
	 * settings driver
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Event
	 */
	public $event;

	/**
	 * @var Preset
	 */
	public $preset;

	/**
	 * unique instance for each module
	 * @var array
	 */
	protected static $instances = array();

    /**
     * config cache
     * @var array
     */
	protected $config;
	/**
	 * for singleton
	 * @param $moduleId
	 * @return static
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getInstance($moduleId)
	{
		if (!isset(self::$instances[$moduleId]))
			self::$instances[$moduleId] = new static($moduleId);

		return self::$instances[$moduleId];
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed|null
	 * @throws ArgumentNullException
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function __call($name, $arguments)
	{
		if (0 !== strpos($name, 'get'))
			throw new Main\SystemException('unacceptable method name');

		$name       = substr($name, 3);
		$isPreset   = (0 === strpos($name, 'Preset'));

		if ($isPreset)
			$name = substr($name, 6);

		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $name, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match)
			$match = strtoupper($match);

		$constName = 'static::OPTION__' . implode('_', $ret);

		if (!defined($constName))
			throw new Main\SystemException('option "' . $constName . '" not found');

		return $isPreset
			? $this->getPresetValue(constant($constName), $arguments[0], $arguments[1], $arguments[2])
			: $this->getNormalValue(constant($constName), $arguments[0], $arguments[1]);
	}

	/**
	 * @param $moduleId
	 * @throws Main\ArgumentNullException
	 */
	protected function __construct($moduleId)
	{
		if (!strlen($moduleId))
			throw new ArgumentNullException('moduleId');

		$this->moduleId = $moduleId;

		$this->message  = new Message();
		$this->event    = new Event($this);
		$this->preset   = new Preset($this);
		$this->tabMap   = new TabMap($this);
		$this->settings = new Settings($this);
	}

	/**
	 * @param $name
	 * @param $params
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function runEvent($name, &$params = array())
	{
		if (!method_exists($this, $name))
			return true;

		try{
			return $this->$name($params);
		} catch (\Exception $e) {
			$this->message->addError($e->getMessage());

			if ($this->settings->getLogErrors())
				$this->writeException2Log($e);

			return false;
		}
	}

    /**
     * @param bool $reload
     * @return array|mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getConfigCache($reload = false)
    {
        if (is_null($this->config) || $reload)
            $this->config = $this->getConfig();

        return $this->config;
    }

    /**
     * @return mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
	abstract public function getConfig();

	/**
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getModuleId()
	{
		return $this->moduleId;
	}

	/**
	 * generate param string
	 * @param        $name
	 * @param string $presetId
	 * @param string $siteId
	 * @return string
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getFullName($name, $presetId = '', $siteId = '')
	{
		if (strlen($presetId))
			$name = htmlspecialcharsbx($presetId) . self::SEPARATOR . $name;

		if (strlen($siteId))
			$name = htmlspecialcharsbx($siteId) . self::SEPARATOR . $name;

		return $name;
	}

	/**
	 * returns value from preset
	 * @param            $inputName
	 * @param            $presetId
	 * @param string     $siteId
	 * @param bool|false $reload
	 * @return mixed
	 * @throws ArgumentNullException
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getPresetValue($inputName, $presetId, $siteId = '', $reload = false)
	{
		if (is_null($presetId))
			throw new ArgumentNullException('presetId');

		return $this->getValue($inputName, $presetId, $siteId, $reload);
	}

	/**
	 * returns value by name
	 * @param            $inputName
	 * @param string     $siteId
	 * @param bool|false $reload
	 * @return mixed|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getNormalValue($inputName, $siteId = '', $reload = false)
	{
		return $this->getValue($inputName, '', $siteId, $reload);
	}

	/**
	 * @param            $inputName
	 * @param string     $presetId
	 * @param string     $siteId
	 * @param bool|false $reload
	 * @return mixed
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getValue($inputName, $presetId = '', $siteId = '', $reload = false)
	{
		if (is_null($inputName))
			throw new ArgumentNullException('inputName');

		$key = md5($inputName . $presetId . $siteId);

		if (!isset($this->cache[$key]) || $reload) {

			$input = $this->tabMap->searchInputByName($inputName, $presetId, $siteId, $reload);

			if ($input instanceof Input)
				$this->cache[$key] = $input->getValue();
			else
				throw new Main\SystemException('input "' . $inputName . '" not found');
		}

		return $this->cache[$key];
	}

	/**
	 * @param        $inputName
	 * @param string $presetId
	 * @param string $siteId
	 * @return mixed
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getDefaultValue($inputName, $presetId = '', $siteId = '')
	{
		$input = $this->tabMap->searchInputByName($inputName, $presetId, $siteId);

		if ($input instanceof Input)
			return $input->getDefault();

		throw new Main\SystemException('input ' . $inputName . ' not found');
	}

	/**
	 * @param \Exception $e
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	public static function writeException2Log(\Exception $e)
	{
		Application::getInstance()
			->getExceptionHandler()
			->writeToLog($e);
	}
}