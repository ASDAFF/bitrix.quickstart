<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: text/html; charset='.SITE_CHARSET);
if(isset($_REQUEST['name']) && strlen($_REQUEST['name']))
{
    $params = isset($_REQUEST['params']) && is_array($_REQUEST['params']) && count($_REQUEST['params']) > 0 ? $_REQUEST['params'] : array();
    $APPLICATION->IncludeComponent($_REQUEST['name'], ".ajax", $params);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
