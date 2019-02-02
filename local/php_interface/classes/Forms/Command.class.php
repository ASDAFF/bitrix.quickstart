<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mnr
 * Date: 31.10.14
 * Time: 16:39
 * To change this template use File | Settings | File Templates.
 */

namespace Cpeople\Classes\Forms;

abstract class Command
{
    protected $isCritical;

    abstract public function execute(Form $form);

    public function __construct($isCritical)
    {
        $this->isCritical = $isCritical;
    }

    protected function getErrorMessage($message)
    {
        return array(get_class($this) => $message);
    }
}