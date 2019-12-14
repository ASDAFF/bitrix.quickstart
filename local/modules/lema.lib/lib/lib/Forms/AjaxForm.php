<?php

namespace Lema\Forms;


/**
 * Class AjaxForm
 * @package Lema\Forms
 */
class AjaxForm extends Form
{
    /**
     * @var null
     */
    protected $iblockId = null;

    /**
     * Send message from bitrix (CEvent::Send)
     *
     * @param $event
     * @param array $data
     * @param string $siteId
     * @return bool
     *
     * @access public
     */
    public function sendMessage($event, array $data, $siteId = 's1')
    {
        if(!\CEvent::Send($event, $siteId, $data))
        {
            $this->setError('email_send', 'Произошла ошибка при отправке сообщения');
            return false;
        }
        return true;
    }

    /**
     * Add record to iblock
     *
     * @param $iblockId
     * @param array $data
     * @return bool
     *
     * @access public
     */
    public function addRecord($iblockId, array $data)
    {
        if(empty($iblockId))
        {
            $this->setError('empty_iblock', 'Не указан инфоблок');
            return false;
        }

        \Bitrix\Main\Loader::IncludeModule('iblock');

        $el = new \CIBlockElement();

        if(!$el->Add(array_merge(array('IBLOCK_ID' => $iblockId), $data)))
        {
            $this->setError('add_record', 'Произошла ошибка при добавлени записи: ' . $el->LAST_ERROR);
            return false;
        }
        return true;
    }

    /**
     * Send message (CEvent::Send) and add record to iblock
     *
     * @param $iblockId
     * @param array $addParams
     * @param $event
     * @param array $messageParams
     * @param string $siteId
     * @return bool
     *
     * @access public
     */
    public function formActionFull($iblockId, array $addParams, $event, array $messageParams = array(), $siteId = 's1')
    {
        return $this->sendMessage($event, $messageParams, $siteId) && $this->addRecord($iblockId, $addParams);
    }
}