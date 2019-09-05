<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 06.09.2019
 * Time: 0:47
 */

define("NO_KEEP_STATISTIC", true);?>
<?define("NOT_CHECK_PERMISSIONS", true); ?>
<?define("NEED_AUTH", true); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?>
<?
use Bitrix\Main\Mail\Event;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Context;
use Bitrix\Main\Config\Option;
CModule::IncludeModule("iblock");
$request = Context::getCurrent()->getRequest();
$name = $request->get("name");
$phone = $request->get("phone");
$result['error'] = "Какаято ошибка";
exit(json_encode($result));