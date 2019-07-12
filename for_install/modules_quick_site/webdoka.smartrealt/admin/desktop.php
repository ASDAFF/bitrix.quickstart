<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
IncludeModuleLangFile(__FILE__);
$module_id = 'webdoka.smartrealt';

$B_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($B_RIGHT == 'D' || !CModule::IncludeModule($module_id))
{
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$APPLICATION->SetTitle(GetMessage('B_MENU_TITLE'));

if($_REQUEST['mode'] == 'list')
{
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_js.php');
}
else
{
	require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
}

$adminPage->ShowSectionIndex($module_id . '_menu', $module_id);

if($_REQUEST['mode'] == 'list')
{
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_js.php');
}
else
{
	require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
}
?>