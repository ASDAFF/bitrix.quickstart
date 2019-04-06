<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 17.09.2017
 * Time: 15:07
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\SystemException;
use Rover\Fadmin\Layout\Request as RequestAbstract;
use Rover\Fadmin\Options;
use Rover\Fadmin\Tab;

/**
 * Class Request
 *
 * @package Rover\Fadmin\Layout\Admin
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Request extends RequestAbstract
{

    /**
     * @var mixed|string
     */
    protected $moduleId;

    /**
     * @var string
     */
    protected $activeTab;

    /**
     * @var mixed|string
     */
    protected $requestMethod = 'POST';

    /**
     * @var bool
     */
    protected $update;

    /**
     * @var bool
     */
    protected $apply;

    /**
     * @var bool
     */
    protected $restoreDefaults;

    /**
     * Request constructor.
     *
     * @param Options $options
     * @param array   $params
     */
    public function __construct(Options $options, array $params = array())
    {
        parent::__construct($options, $params);

        $this->moduleId = htmlspecialcharsbx($this->options->getModuleId());

        if (!empty($this->params['request_method']))
            $this->requestMethod = htmlspecialcharsbx($this->params['request_method']);

        $this->activeTab = isset($this->params['active_tab'])
            ? trim($this->params['active_tab'])
            : null;

        $this->update = isset($this->params['update'])
            ? (bool)$this->params['update']
            : false;

        $this->apply = isset($this->params['apply'])
            ? (bool)$this->params['apply']
            : false;

        $this->restoreDefaults  = isset($this->params['restore_defaults'])
            ? (bool)$this->params['restore_defaults']
            : false;
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setValues()
    {
        if (!$this->check())
            return;

        if(strlen($this->restoreDefaults) > 0)
            $this->restoreDefaults();
        else
            try {
                if ($this->options->tabMap->setValuesFromRequest())
                    $this->redirect();
            } catch (\Exception $e) {
                $this->options->message->addError($e->getMessage());
            }
    }

    /**
     * @param null $activeTab
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function redirect($activeTab = null)
    {
        $request = Application::getInstance()->getContext()->getRequest();

        if (strlen($this->update) && strlen($request["back_url_settings"]))
            parent::redirect($request["back_url_settings"]);

        $activeTab = $activeTab
            ? 'tabControl_active_tab=' . $activeTab
            : $this->activeTab;

        global $APPLICATION;

        parent::redirect($APPLICATION->GetCurPage()
            . "?mid=" . urlencode($this->moduleId)
            . "&lang=" . urlencode(LANGUAGE_ID)
            . "&back_url_settings=" . urlencode($request["back_url_settings"])
            . "&" . $activeTab);
    }

    /**
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function addPreset()
    {
        $presetId = parent::addPreset();

        if ($presetId){
            $presetTab = $this->options->tabMap->getTabByPresetId($presetId, '', true);
            if (!$presetTab instanceof Tab)
                throw new ArgumentOutOfRangeException('presetId');

            $this->redirect($presetTab->getName());
        }
    }

    /**
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function removePreset()
    {
        try{
            if (parent::removePreset())
                $this->redirect();
        } catch (\Exception $e) {
            throw new SystemException($e->getMessage());
        }
    }

    /**
     * @return bool
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function check()
    {
        return (($this->requestMethod === 'POST')
            && (strlen($this->update.$this->apply.$this->restoreDefaults) > 0)
            && check_bitrix_sessid());
    }

    /**
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function restoreDefaults()
    {
        parent::restoreDefaults();
        $this->redirect();
    }
}