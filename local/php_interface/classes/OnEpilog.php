<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class OnEpilog
{
    function OnEpilogHandler() {
        if (defined('ERROR_404') && ERROR_404 == 'Y') {
            $template = '404';
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $template . '/header.php';
            include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
            include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $template . '/footer.php';
        }
    }
}