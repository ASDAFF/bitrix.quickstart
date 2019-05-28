<?
/**
 * ������ �������� ��������� ��� �������� ID �������� (������� "������� ��������"),
 * ������ �������� ���������� ��������� ��� ������ ������� ������� ����� CRON.
 *
 * ��������! ���� ��� ����� ��������� ��������� YML ��������� ����� CRON, �� ��� �������
 * �������� �������� ����� ������� ������� � ����� ������ � ����� ����� � � ��� ���������
 * $SHOP_ID ������� ��������������� ID ������� ��������, ��� �������� ��������� ��������.
 */
$SHOP_ID = 1;

/**
 * ============== ��������� ���� �� ������������� ������������� ========================
 */
set_time_limit(0);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);
define("MIBIX_DEBUG_YAMEXPORT",false);

$MODULE_ID = "mibix.yamexport";
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../../');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
if (!CModule::IncludeModule($MODULE_ID) || !CModule::IncludeModule("iblock")) return;

// �������� �������� � �������� ������� ��������� XML-�����
$YAM_EXPORT = CMibixYandexExport::get_step_settings($SHOP_ID);
if(is_array($YAM_EXPORT) && count($YAM_EXPORT) > 0)
{
    $YAM_EXPORT_LIMIT = $YAM_EXPORT["step_limit"]; // ���������� ���������, �������������� �� 1 ���
    $YAM_EXPORT_PATH = $DOCUMENT_ROOT . $YAM_EXPORT["step_path"]; // ���� ���������� ��������������� xml-����
	//echo ($YAM_EXPORT_PATH);

    CMibixYandexExport::CreateYML($YAM_EXPORT_PATH, $YAM_EXPORT_LIMIT, true, $SHOP_ID);
}
else
{
    echo "Error config steps load. Please check fill of the limits for the shop.";
}

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");
?>
