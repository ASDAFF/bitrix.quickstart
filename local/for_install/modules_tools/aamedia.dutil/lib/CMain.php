<?
namespace AAM\DUtil;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Config\Configuration as Conf;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CMain
{
    /**
     * After click "apply" or "default" on module settings page
     * we need to process option values
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function ProcessingOfResults()
    {
        self::SwitchPhpDebugMode();
        self::AdminNotify();
        self::RobotsTXT();
    }

    /**
     * Update values in .settings.php
     * in section "exception_handling"
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function SwitchPhpDebugMode()
    {
        $arConfig = Conf::getInstance();
        $handl = $arConfig->get('exception_handling');

        //main settings
        $debug = Option::get("aamedia.dutil", "debug");
        $handled_errors_types = Option::get("aamedia.dutil", "handled_errors_types");
        $exception_errors_types = Option::get("aamedia.dutil", "exception_errors_types");
        $ignore_silence = Option::get("aamedia.dutil", "ignore_silence");
        $assertion_throws_exception = Option::get("aamedia.dutil", "assertion_throws_exception");
        $assertion_error_type = Option::get("aamedia.dutil", "assertion_error_type");

        ($debug === "N") ? ($handl['debug'] = false) : ($handl['debug'] = true);
        $handl['handled_errors_types'] = intval($handled_errors_types);
        $handl['exception_errors_types'] = intval($exception_errors_types);
        ($ignore_silence === "N") ? ($handl['ignore_silence'] = false) : ($handl['ignore_silence'] = true);
        ($assertion_throws_exception === "N") ? ($handl['assertion_throws_exception'] = false) : ($handl['assertion_throws_exception'] = true);
        $handl['assertion_error_type'] = intval($assertion_error_type);

        //log settings
        $log = Option::get("aamedia.dutil", "log");
        $log_file = Option::get("aamedia.dutil", "log_file");
        $log_file_size = Option::get("aamedia.dutil", "log_file_size");

        if ($log === "N")
            $handl['log'] = NULL;
        else
        {
            $handl['log'] = array(
                'settings' => array(
                    'file' => $log_file,
                    'log_size' => intval($log_file_size)
                )
            );
        }

        $arConfig->add('exception_handling', $handl);
        $arConfig->saveConfiguration();
    }

    /**
     * Show or hide notify with warning
     * "PHP DEbug mode is on"
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function AdminNotify()
    {
        $debug = Option::get("aamedia.dutil", "debug");
        if ($debug === "Y")
            \CAdminNotify::Add
            (
                array
                (
                    'MESSAGE' => Loc::getMessage("AAM_DUTIL_NOTIFY_TEXT").'<a href="/bitrix/admin/settings.php?mid=aamedia.dutil">'. Loc::getMessage("AAM_DUTIL_DEACTIVATE") .'</a>',
                    'TAG' => 'phpdebugon_notify',
                    'MODULE_ID' => 'aamedia.dutil',
                    'ENABLE_CLOSE' => 'Y',
                )
            );
        else
            \CAdminNotify::DeleteByTag
            (
                'phpdebugon_notify'
            );
    }

    public static function RobotsTXT()
    {
        if ((\COption::GetOptionString('aamedia.dutil', 'site_index') === "Y") &&
            (\COption::GetOptionString('main', 'update_devsrv') === "Y"))
        {
            \CHandlers::CreateRobotsTXT();
        }

        if (\COption::GetOptionString('aamedia.dutil', 'site_index') !== "Y")
        {
            \CHandlers::DeleteRobotsTXT();
        }
    }
}