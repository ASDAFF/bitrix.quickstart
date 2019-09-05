<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 06.09.2019
 * Time: 0:33
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
$mail = $request->get("mail");
if($mail){
    $el = new CIBlockElement;
    $PROP = array();
    $PROP['MAIL'] = $mail;
    $arLoadProductArray = Array(
        "MODIFIED_BY"    => $USER->GetID(),
        "IBLOCK_ID"      => 3,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => $mail,
        "ACTIVE"         => "Y"
    );
    if($PRODUCT_ID = $el->Add($arLoadProductArray))
        $result['elementId'] = $PRODUCT_ID;
    else
        $result['error'] = $el->LAST_ERROR;
    //отправка почты админу
    $arEventFields= array(
        "MAIL" => $name
    );
    if($msg = CEvent::Send("SUBSCRIBE", SITE_ID, $arEventFields, "N", 48)){
        $result['sendMsg'] = "Подписка успешно оформлена";
    }else{
        $result['sendMsg'] = "Ошибка, попробуйте еще раз";
    }
}else{
    $result['error'] = "Заполните пустые поля";
}
exit(json_encode($result));