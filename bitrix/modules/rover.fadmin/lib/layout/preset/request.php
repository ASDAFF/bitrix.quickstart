<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 17.09.2017
 * Time: 19:45
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Preset;

use Bitrix\Main\ArgumentOutOfRangeException;
use Rover\Fadmin\Layout\Request as RequestAbstract;
use Rover\Fadmin\Options;
use Rover\Fadmin\Tab;

/**
 * Class Request
 *
 * @package Rover\Fadmin\Layout\Preset
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Request extends RequestAbstract
{
    const INPUT__APPLY      = 'apply';
    const INPUT__SAVE       = 'save';
    const INPUT__FORM_ID    = 'form_id';

    /**
     * @var string
     */
    protected $curPage;

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getCurPage()
    {
        if (is_null($this->curPage)){
            global $APPLICATION;
            $this->curPage = $APPLICATION->GetCurPage();
        }

        return $this->curPage;
    }

    /**
     * Request constructor.
     *
     * @param Options $options
     * @param array   $params
     */
    public function __construct(Options $options, array $params = array())
    {
        parent::__construct($options, $params);

        $this->params['back_url'] = isset($this->params['back_url'])
            ? trim($this->params['back_url'])
            : '';

        $this->params['this_url'] = isset($this->params['this_url'])
            ? trim($this->params['this_url'])
            : $this->getCurPage();

        $this->params['preset_id'] = isset($this->params['preset_id'])
            ? trim($this->params['preset_id'])
            : 0;
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function removePreset()
    {
        try{
            if (parent::removePreset() && strlen($this->params['back_url']))
                $this->redirect($this->params['back_url']);
        } catch (\Exception $e) {
            $this->options->message->addError($e->getMessage());
        }
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setValues()
    {
        if ((!$this->request->get(self::INPUT__APPLY)
                && !$this->request->get(self::INPUT__SAVE))
            || !$this->request->get(self::INPUT__FORM_ID))
            return;

        try {
            $tab = $this->options->tabMap->getTabByPresetId($this->params['preset_id']);
            if (!$tab instanceof Tab)
                throw new ArgumentOutOfRangeException('preset_id');

            $tab->setValuesFromRequest();
            $redirectUrl = $this->request->get(self::INPUT__SAVE) && strlen($this->params['back_url'])
                ? $this->params['back_url']
                : $this->params['this_url'];
            $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            $this->options->message->addError($e->getMessage());
        }
    }
}