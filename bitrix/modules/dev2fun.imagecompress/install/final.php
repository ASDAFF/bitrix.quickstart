<?php
/**
 * Created by PhpStorm.
 * User: darkfriend <hi@darkfriend.ru>
 * Date: 06.05.2017
 * Time: 19:15
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
if(!check_bitrix_sessid()) return;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

Loader::includeModule('main');

CAdminMessage::ShowMessage(array(
    "MESSAGE"=>Loc::getMessage('D2F_IMAGECOMPRESS_INSTALL_SUCCESS'),
    "TYPE"=>"OK"
));
echo BeginNote();
echo Loc::getMessage("D2F_IMAGECOMPRESS_INSTALL_LAST_MSG");
echo EndNote();