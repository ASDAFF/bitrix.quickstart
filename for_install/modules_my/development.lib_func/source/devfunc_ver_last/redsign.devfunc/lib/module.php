<?php

namespace Redsign\DevFunc;

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Module {

    public static function registerInstallation($arParams = array()) {
        global $APPLICATION;

        if (!Loader::includeModule('main'))
            return false;

        require_once(Application::getDocumentRoot().'/bitrix/modules/main/classes/general/update_client.php');

        $arUpdateList = \CUpdateClient::GetUpdatesList($errorMessage, 'ru', 'Y');
        if (array_key_exists('CLIENT', $arUpdateList) && !empty($arUpdateList['CLIENT'][0]['@']['LICENSE'])) {
            $bitrixActiveFrom = $arUpdateList['CLIENT'][0]['@']['DATE_FROM'];
            $bitrixActiveTo = $arUpdateList['CLIENT'][0]['@']['DATE_TO'];
            $bitrixEdition = $arUpdateList['CLIENT'][0]['@']['LICENSE'];
            $bitrixName = $arUpdateList['CLIENT'][0]['@']['NAME'];
        } else {
            $edition = 'UNKNOWN';
        }

        $url = 'https://portal.redsign.ru/mp_clients/';
        $bitrixKey = \CUpdateClient::GetLicenseKey();

        $defaultParams = array(
            'action' => 'devfunc_called',
            'devfunc-action' => 'install',
            'mp_code' => array('redsign.devfunc'),
            'bitrix_name' => $bitrixName,
            'bitrix_active_from' => $bitrixActiveFrom,
            'bitrix_active_to' => $bitrixActiveTo,
            'bitrix_key_hash' => md5('BITRIX'.$bitrixKey.'LICENCE'),
            'bitrix_version' => SM_VERSION,
            'bitrix_edition' => $bitrixEdition,
            'site_name' => $APPLICATION->ConvertCharset(Option::get('main', 'site_name'), SITE_CHARSET, 'windows-1251'),
            'site_url' => $APPLICATION->ConvertCharset(Option::get('main', 'server_name'), SITE_CHARSET, 'windows-1251'),
            'site_default_email' => $APPLICATION->ConvertCharset(Option::get('main', 'email_from'), SITE_CHARSET, 'windows-1251'),
            'server_ip' => ($_SERVER['HTTP_X_REAL_IP'] ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['SERVER_ADDR']),
            'server_host' => $_SERVER['HTTP_HOST'],
        );

        foreach ($defaultParams as $key => $value) {
            if (!array_key_exists($key, $arParams)) {
                $arParams[$key] = $value;
            }
        }

        $firstMpCode = reset($arParams['mp_code']);
        $arParams['mp_code'] = serialize($arParams['mp_code']);

        if (empty($arParams['module_version'])) {
            if ($info = \CModule::CreateModuleObject($firstMpCode)) {
                $arParams['module_version'] = $info->MODULE_VERSION.' ('.SM_VERSION.')';
            }
        }

        $httpClient = new HttpClient();
        $response = $httpClient->post($url, $arParams, true);

        return $response;
    }

}
