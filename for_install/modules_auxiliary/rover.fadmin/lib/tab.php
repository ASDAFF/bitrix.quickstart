<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 18:34
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin;

use Bitrix\Main;
use Bitrix\Main\ArgumentNullException;
use Rover\Fadmin\Inputs\Input;

/**
 * Class Tab
 *
 * @package Rover\Fadmin
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Tab
{
	/**
	 * inputs container
	 * @var array
	 */
	protected $inputs = array();

	/**
	 * @var string
	 */
	protected $name;

    /**
     * @var string
     */
	protected $label;

    /**
     * @var string
     */
	protected $description;

    /**
     * @var bool
     */
	protected $preset;

    /**
     * @var string
     */
	protected $presetId = '';

    /**
     * @var string
     */
	protected $siteId = '';

	/**
	 * @var Options
	 */
	public $options;

	/**
	 * @param array   $params
	 * @param Options $options
	 * @throws ArgumentNullException
	 */
	public function __construct(array $params, Options $options)
	{
		if (is_null($params['name']))
			throw new ArgumentNullException('name');

		if (is_null($params['label']))
			throw new ArgumentNullException('label');

		$this->options      = $options;
		$this->name         = htmlspecialcharsbx($params['name']);
		$this->setLabel($params['label']);

		$this->preset = isset($params['preset'])
			? (bool)$params['preset']
			: false;

		$this->setPresetId($params['presetId']);
		$this->setDescription($params['description']);
		$this->setSiteId($params['siteId']);
	}

	/**
	 * @param array   $params
	 * @param Options $options
	 * @return Tab
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function factory(array $params, Options $options)
	{
		$tab = new Tab($params, $options);

		if (isset($params['inputs']) && is_array($params['inputs']))
			foreach ($params['inputs'] as $inputParams)
				$tab->addInput(Input::factory($inputParams, $tab));

		return $tab;
	}

	/**
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param $label
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setLabel($label)
	{
		$this->label = html_entity_decode(trim($label));
	}

	/**
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param $description
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setDescription($description = '')
	{
		$this->description = trim($description);
	}

	/**
	 * @return string
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getPresetId()
	{
		return $this->presetId;
	}

	/**
	 * @param $presetId
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setPresetId($presetId = '')
	{
		$this->presetId = htmlspecialcharsbx($presetId);
	}

	/**
	 * @return bool
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function isPreset()
	{
		return (bool)$this->preset;
	}

	/**
	 *
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getSiteId()
	{
		return $this->siteId;
	}

	/**
	 * @param $siteId
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setSiteId($siteId = '')
	{
		$this->siteId = htmlspecialcharsbx($siteId);
	}

	/**
	 * @return string
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getModuleId()
	{
		return $this->options->getModuleId();
	}

	/**
	 * @param $filter
	 * @return Input|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function search(array $filter)
	{
		foreach ($this->inputs as $input) {
			/**
			 * @var Input $input
			 */
			if (isset($filter['id']) && strlen($filter['id'])
				&& $filter['id'] == $input->getValueId())
				return $input;

			if (isset($filter['name']) && strlen($filter['name'])
				&& $filter['name'] == $input->getValueName())
				return $input;
		}

		return null;
	}

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function __clone()
	{
		$newInputs = array();

		foreach ($this->inputs as $input) {
			/** @var Input $input */
			/** @var Input $newInput */
			$newInput = clone $input;
			$newInput->setTab($this);
			$newInputs[] = $newInput;
		}

		$this->inputs = $newInputs;
	}

    /**
     * @param      $inputName
     * @param bool $reload
     * @return mixed|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getValue($inputName, $reload = false)
	{
		$input = $this->searchByName($inputName);

		if ($input instanceof Input)
			return $input->getValue($reload);

		return null;
	}

	/**
	 * @param $name
	 * @return Input|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function searchByName($name)
	{
		$filter = array(
			'name' => Options::getFullName($name, $this->getPresetId(), $this->getSiteId())
        );

		return $this->search($filter);
	}

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function clear()
	{
		foreach ($this->inputs as $input)
			/**
			 * @var Input $input
			 */
			$input->removeValue();
	}

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getName()
	{
		return Options::getFullName($this->name, $this->presetId, $this->siteId);
	}

	/**
	 * @param Input $input
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function addInput(Input $input)
	{
		$this->inputs[] = $input;
	}

	/**
	 * @param array $input
	 * @return Input
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function addInputArray(array $input)
	{
		$input = Input::factory($input, $this);
		$this->inputs[] = $input;

		return $input;
	}

	/**
	 * @throws Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function sort()
	{
		if (!count($this->inputs))
			return;

		uasort($this->inputs, function(Input $i1, Input $i2){
			if($i1->getSort() < $i2->getSort()) return -1;
			elseif($i1->getSort() > $i2->getSort()) return 1;
			else return 0;
		});
	}

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setValuesFromRequest()
	{
	    $tab = $this;

        if(false === $this->options->runEvent(
                Options::EVENT__BEFORE_ADD_VALUES_TO_TAB_FROM_REQUEST,
                compact('tab')))
            return;

		foreach ($this->inputs as $input)
			/**
			 * @var Input $input
			 */
			$input->setValueFromRequest();
	}

    /**
     * @param bool $reload
     * @return mixed|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPresetName($reload = false)
	{
		$preset = $this->getPreset($reload);

		if (is_array($preset) && isset($preset['name']))
			return $preset['name'];

		return null;
	}

    /**
     * @param bool $reload
     * @return mixed|null
     * @throws ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getPreset($reload = false)
    {
        if (!$this->isPreset())
            throw new Main\ArgumentOutOfRangeException('tab');

        if (!$this->getPresetId())
            throw new ArgumentNullException('presetId');

        return $this->options->preset->getById(
            $this->getPresetId(), $this->siteId, $reload);
    }

	/**
	 * @param            $name
	 * @param            $value
	 * @return bool
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setInputValue($name, $value)
	{
		$input = $this->searchByName($name);

		if (!$input instanceof Input)
			return false;

		$input->setValue($value);

		return true;
	}

	/**
	 * @return Input[]
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getInputs()
	{
		return $this->inputs;
	}

    /**
     * @param $name
     * @throws ArgumentNullException
     * @throws Main\NotSupportedException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function setPresetName($name)
    {
        if (!$this->isPreset())
            throw new Main\NotSupportedException();

        $name = trim($name);
        if (!strlen($name))
            throw new ArgumentNullException('name');

        $this->options->preset->updateName($this->getPresetId(), $name, $this->getSiteId());
    }
}