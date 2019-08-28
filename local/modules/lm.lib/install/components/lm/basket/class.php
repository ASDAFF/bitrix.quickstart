<?php

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

CJSCore::init(array('fx'));

class BasketComponent extends \CBitrixComponent
{

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

}