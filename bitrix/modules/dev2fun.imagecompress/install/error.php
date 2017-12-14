<?php
/**
 * Created by PhpStorm.
 * User: darkfriend <hi@darkfriend.ru>
 * Date: 06.05.2017
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
if(!check_bitrix_sessid()) return;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

Loader::includeModule('main');

CAdminMessage::ShowMessage(array(
    "MESSAGE"=>$GLOBALS['D2F_COMPRESSIMAGE_ERROR'],
    "TYPE"=>"ERROR"
));
echo BeginNote();
echo $GLOBALS['D2F_COMPRESSIMAGE_ERROR_NOTES'];
echo EndNote();

echo '<a href="'.$APPLICATION->GetCurPageParam('STEP=1',['STEP']).'">'.Loc::getMessage('D2F_COMPRESSIMAGE_GOTO_FIRST').'</a><br><br>';
echo '<a href="/bitrix/admin/partner_modules.php">'.Loc::getMessage('D2F_COMPRESSIMAGE_GOTO_MODULES').'</a>';