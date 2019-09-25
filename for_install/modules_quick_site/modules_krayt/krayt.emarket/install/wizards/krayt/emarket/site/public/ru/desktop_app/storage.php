<?php
require($_SERVER["DOCUMENT_ROOT"]."/desktop_app/headers.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

/** @var CAllMain $APPLICATION */
$APPLICATION->IncludeComponent('bitrix:webdav.disk', '', array('VISUAL' => false));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");