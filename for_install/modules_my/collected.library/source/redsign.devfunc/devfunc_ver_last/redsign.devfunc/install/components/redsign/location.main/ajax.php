<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Context;
use \Bitrix\Main\Localization\Loc;
use \Redsign\DevFunc\Sale\Location\Location;
use \Bitrix\Main\Web\Uri;

define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);

if (!is_string($_REQUEST['siteId'])) {
    die();
}
if (preg_match('/^[a-z0-9_]{2}$/i', $_REQUEST['siteId']) === 1) {
    define('SITE_ID', $_REQUEST['siteId']);
}

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

Loc::loadMessages(__FILE__);

$arResult = array(
    'ERROR' => false,
    'SUCCESS' => false,
);

if (!Loader::includeModule('redsign.devfunc') || !Loader::includeModule('sale'))
{
    $arResult['ERROR'] = Loc::getMessage('RS_MODULE_NOT_INSTALLED');
}

$context = Context::getCurrent();
$request = $context->getRequest();

$action = trim($request->get('action'));
if (empty($arResult['ERROR']) && check_bitrix_sessid())
{
    switch ($action)
	{
        case 'change':
            $locationId = $request->get('id');
            Location::setMyCity($locationId);
            $arResult['SUCCESS'] = true;
            $arResult['id'] = $locationId;
			
			$arRegions = \Redsign\DevFunc\Sale\Location\Region::getRegions();
		
			if (is_array($arRegions) && count($arRegions) > 0)
			{
				$server = $context->getServer();
				foreach ($arRegions as $arRegion)
				{
					if ($locationId == $arRegion['LOCATION_ID'])
					{
						$arRegionCurrent = $arRegion;
						break;
					}
				}
				unset($arRegion);
				
				if (!$arRegionCurrent)
				{
					$arRegionCurrent = \Redsign\DevFunc\Sale\Location\Region::getDefaultRegion();
				}

				if (
					is_array($arRegionCurrent['LIST_DOMAINS']) && count($arRegionCurrent['LIST_DOMAINS']) > 0
					&& !in_array($server->getServerName(), $arRegionCurrent['LIST_DOMAINS'])
				)
				{
					$uriString = $request->getRequestUri();

					$uri = new Uri('/');
					// $uri = new Uri($uriString);
					$uri->setHost(reset($arRegionCurrent['LIST_DOMAINS']));
					$redirect = $uri->getUri();

					// LocalRedirect($redirect, true);
					$arResult['redirect'] = $redirect;
					
				}
			}

            break;

        default:
            break;
    }
}

if (strtolower(SITE_CHARSET) != 'utf-8')
{
    $arResult = $APPLICATION->ConvertCharsetArray($arResult, SITE_CHARSET, 'utf-8');
}

header('Content-Type: application/json');
echo json_encode($arResult);

$context->getResponse()->flush();
die();
