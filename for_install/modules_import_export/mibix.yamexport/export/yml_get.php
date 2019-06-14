<?
set_time_limit(0);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);

$SHOP_ID = 3;
if (isset($_REQUEST['ID']) && IntVal($_REQUEST['ID']) > 0)
    $SHOP_ID = IntVal($_REQUEST['ID']);

$MODULE_ID = "mibix.yamexport";
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../../');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
if (!CModule::IncludeModule($MODULE_ID) || !CModule::IncludeModule("iblock")) return;

CMibixYandexExport::GetYML($SHOP_ID);

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");
?>
