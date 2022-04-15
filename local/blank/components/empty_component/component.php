<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$app = Application::getInstance();
$request = $app->getContext()->getRequest();

if ($this->StartResultCache()) {
    $arResult['DATA'] = array();

    // Cancel cache data
    if ($arParams['ID'] < 10) {
        $this->AbortResultCache();
    }

    $this->IncludeComponentTemplate();
}

