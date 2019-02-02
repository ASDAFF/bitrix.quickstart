<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mnr
 * Date: 28.10.14
 * Time: 13:28
 * To change this template use File | Settings | File Templates.
 */

namespace Cpeople\Classes\Forms\Commands;

use Cpeople\Classes\Forms\Command;
use Cpeople\Classes\Forms\Form;

class BitrixEmailCommand extends Command
{
    private $emailEvent;
    private $fieldsUppercase;
    /**
     * @var Form
     */
    private $form;

    public function __construct($isCritical, $emailEvent, $fieldsUppercase = TRUE)
    {
        parent::__construct($isCritical);

        $this->emailEvent = $emailEvent;
        $this->fieldsUppercase = $fieldsUppercase;
    }

    public function execute(Form $form)
    {
        $sendData = $form->getData();

        if($this->fieldsUppercase)
        {
            $submitArray  = array_change_key_case($sendData, CASE_UPPER);
        }

        $result = \CEvent::Send($this->emailEvent, SITE_ID, $submitArray);
        if(!$result && $this->isCritical)
        {
            throw new \Exception('CEvent::Send false');
        }
        elseif(!$result && !$this->isCritical)
        {
            $form->setErrors(array($this->getErrorMessage('ошибка отправки почты (CEvent::Send)')));
        }
    }
}