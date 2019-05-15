<?
define("NO_KEEP_STATISTIC", true);
define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once 'sotbitwidget.php';

$SotbitWidget = new SotbitWidget();
$SotbitWidget->getData();

require_once 'sotbitwidget_template.php';
?>