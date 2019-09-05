<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 06.09.2019
 * Time: 0:41
 */

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
if (!is_object($USER)) $USER = new CUser;
$arAuthResult = $USER->Login($_REQUEST['login'], $_REQUEST['pass'], "Y");
$APPLICATION->arAuthResult = $arAuthResult;
exit(json_encode($arAuthResult));