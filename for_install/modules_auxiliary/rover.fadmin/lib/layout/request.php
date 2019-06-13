<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 17.09.2017
 * Time: 15:05
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Rover\Fadmin\Inputs\Addpreset;
use Rover\Fadmin\Inputs\Removepreset;
use Rover\Fadmin\Options;

/**
 * Class Request
 *
 * @package Rover\Fadmin\Layout
 * @author  Pavel Shulaev (https://rover-it.me)
 */
abstract class Request
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var \Bitrix\Main\HttpRequest
     */
    protected $request;

    /**
     * Request constructor.
     *
     * @param Options $options
     * @param array   $params
     */
    public function __construct(Options $options, array $params = array())
    {
        $this->options  = $options;
        $this->params   = $params;
        $this->request  = Application::getInstance()->getContext()->getRequest();
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function process()
    {
        // action before
        if(false === $this->options->runEvent(Options::EVENT__BEFORE_GET_REQUEST))
            return;

        if ($this->request->get(Addpreset::$type)) {

            $this->addPreset();

        } elseif ($this->request->get(Removepreset::$type)) {

            $this->removePreset();

        } else {

            $this->setValues();

        }
    }

    abstract function setValues();

    /**
     * @return int
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function addPreset()
    {
        list($siteId, $value) = explode(Addpreset::SEPARATOR,
            $this->request->get(Addpreset::$type));

        return intval($this->options->tabMap->addPreset($value, $siteId));
    }

    /**
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function removePreset()
    {
        list($siteId, $id) = explode(Removepreset::SEPARATOR,
            $this->request->get(Removepreset::$type));

        return $this->options->tabMap->removePreset($id, $siteId);
    }

    /**
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function restoreDefaults()
    {
        Option::delete($this->options->getModuleId());
    }

    /**
     * @param $url
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function redirect($url)
    {
        $params = compact('url');

        if (false === $this->options->runEvent(Options::EVENT__BEFORE_REDIRECT_AFTER_REQUEST, $params))
            return;

        if (empty($params['url']))
            return;

        LocalRedirect($params['url']);
    }
}