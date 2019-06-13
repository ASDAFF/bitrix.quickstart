<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 21.01.2016
 * Time: 20:18
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Options;

use \Bitrix\Main\ArgumentNullException;;

use Bitrix\Main\ArgumentOutOfRangeException;
use \Rover\Fadmin\Options;
use \Rover\Fadmin\Tab;
use \Rover\Fadmin\Inputs\Input;

/**
 * Class TabMap
 *
 * @package Rover\Fadmin
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class TabMap
{
	/**
	 * tabs collection
	 * @var array
	 */
	protected $tabMap = array();

	/**
	 * preset tabs collection
	 * @var array
	 */
	protected $presetMap = array();

	/**
	 * for events
	 * @var Options
	 */
	protected $options;

	/**
	 * @param Options $options
	 * @throws ArgumentNullException
	 */
	public function __construct(Options $options)
	{
		$this->options = $options;
	}

    /**
     * @param bool $reload
     * @return array|mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected function getTabsParams($reload = false)
    {
        $config = $this->options->getConfigCache($reload);

        return is_array($config) && isset($config['tabs'])
            ? $config['tabs']
            : array();
    }
	/**
	 * @param bool|false $reload
	 * @return Tab[]
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getTabs($reload = false)
	{
		if (!count($this->tabMap) || $reload)
			$this->reloadTabs();

		$tabs = $this->tabMap;

		$this->options->runEvent(Options::EVENT__AFTER_GET_TABS, compact('tabs'));

		return $tabs;
	}

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function reloadTabs()
	{
		$this->tabMap       = array();
		$this->presetMap    = array();

		$tabsParams         = $this->getTabsParams();

		foreach ($tabsParams as $tabParams){

			if (empty($tabParams))
				continue;

			if (isset($tabParams['preset']) && $tabParams['preset']){
				$siteId = $tabParams['siteId'] ?: '';
				// preset tab can be only one on current site
				if (isset($this->presetMap[$siteId]))
					continue;

				$this->presetMap[$siteId] = true;

				$presets = $this->options->preset->getList($siteId);

				if (is_array($presets) && count($presets)){
					foreach ($presets as $preset){

						$tabParams['presetId']  = $preset['id'];
						$tabParams['label']     = $preset['name'];

						// event before create preset tab
						if (false === $this->options->runEvent(
                            Options::EVENT__BEFORE_MAKE_PRESET_TAB,
                            compact('tabParams')))
						continue;

						$tab = Tab::factory($tabParams, $this->options);

						// event after create preset tab
						$this->options->runEvent(
							Options::EVENT__AFTER_MAKE_PRESET_TAB,
							compact('tab'));

						$this->tabMap[] = $tab;
					}
				}
			} else {
				$this->tabMap[] = Tab::factory($tabParams, $this->options);
			}
		}
	}

	/**
	 * @param            $presetId
	 * @param string     $siteId
	 * @param bool|false $reload
	 * @return null|Tab
	 * @throws ArgumentNullException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getTabByPresetId($presetId, $siteId = '', $reload = false)
	{
		if (!$presetId)
			throw new ArgumentNullException('presetId');

		foreach ($this->getTabs($reload) as $tab)
            /**
             * @var Tab $tab
             */
            if ($tab->isPreset()
                && ($tab->getSiteId() == $siteId)
                && ($tab->getPresetId() == $presetId))
                return $tab;

		return null;
	}

	/**
	 * @param            $valueName
	 * @param bool|false $reload
	 * @return null|Input
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getInputByValueName($valueName, $reload = false)
	{
		$aTabs = $this->getTabs($reload);

		$filter = array('name' => $valueName);

		foreach ($aTabs as $tab){
			/**
			 * @var Tab $tab
			 */
			$input = $tab->search($filter);
			if ($input instanceof Input)
				return $input;
		}

		return null;
	}

    /**
     * @param        $name
     * @param string $siteId
     * @param bool   $reload
     * @return mixed|null|Tab
     * @author Pavel Shulaev (http://rover-it.me)
     */
	public function searchTabByName($name, $siteId = '', $reload = false)
	{
		$tabs   = $this->getTabs($reload);
        $siteId = trim($siteId);

		foreach ($tabs as $tab){
            /**
             * @var Tab $tab
             */
            if ($tab->getName() != $name)
			    continue;

            if (strlen($siteId) && ($siteId != $tab->getSiteId()))
                continue;

            return $tab;
        }

		return null;
	}

    /**
     * @param        $inputName
     * @param string $presetId
     * @param string $siteId
     * @param bool   $reload
     * @return null|Input
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function searchInputByName($inputName, $presetId = '', $siteId = '', $reload = false)
    {
        $tabs = $this->getTabs($reload);

        foreach ($tabs as $tab) {
            /**
             * @var Tab $tab
             */
            if ((strlen($presetId) && ($tab->getPresetId() != $presetId))
                || (strlen($siteId) && ($tab->getSiteId() != $siteId)))
                continue;

            $input = $tab->searchByName($inputName);

            if ($input instanceof Input)
                return $input;
        }

        return null;
    }

    /**
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setValuesFromRequest()
    {
        if(false === $this->options->runEvent(
            Options::EVENT__BEFORE_ADD_VALUES_FROM_REQUEST))
            return false;

        $tabs = $this->getTabs();

        foreach ($tabs as $tab)
            $tab->setValuesFromRequest();

        if(false === $this->options->runEvent(
                Options::EVENT__AFTER_ADD_VALUES_FROM_REQUEST,
                compact('tabs')))
            return false;

        return true;
    }

    /**
     * @param        $value
     * @param string $siteId
     * @return bool|int|mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function addPreset($value, $siteId = '')
    {
        $params = compact('siteId', 'value');
        if (false === $this->options->runEvent(Options::EVENT__BEFORE_ADD_PRESET, $params))
            return false;

        if (!isset($params['name']))
            $params['name'] = $params['value'];

        $params['id'] = $this->options->preset->add(
            $params['name'],
            $params['siteId']
        );

        //reload tabs
        $this->reloadTabs();
        $this->options->runEvent(Options::EVENT__AFTER_ADD_PRESET, $params);

        return $params['id'];
    }

    /**
     * @param        $id
     * @param string $siteId
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function removePreset($id, $siteId = '')
    {
        $id = intval($id);
        if (!$id)
            throw new ArgumentNullException('id');

        $params = compact('siteId', 'id');

        // action beforeRemovePreset
        if(false === $this->options->runEvent(
                Options::EVENT__BEFORE_REMOVE_PRESET,
                $params))
            return false;

        /**
         * @var Tab $presetTab
         */
        $presetTab = $this->getTabByPresetId($params['id'], $params['siteId']);

        if ($presetTab instanceof Tab === false)
            throw new ArgumentOutOfRangeException('tab');

        $presetTab->clear();

        $this->options->preset->remove($id, $siteId);

        // action afterRemovePreset
        $this->options->runEvent(Options::EVENT__AFTER_REMOVE_PRESET,
            compact('siteId'));

        return true;
    }
}