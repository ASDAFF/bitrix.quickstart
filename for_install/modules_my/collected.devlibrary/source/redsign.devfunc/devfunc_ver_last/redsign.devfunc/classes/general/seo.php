<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Uri;

class RSSeo
{
    public static function addMetaOG()
    {

        global $APPLICATION;
        $arMetaPropName = array(
            'og:type',
            'twitter:card',
            'og:site_name',
            'og:url',
            'og:title',
            'og:description',
            'og:image',
            'fb:admins',
            'fb:app_id',
        );

        $Asset = Asset::getInstance();

        $arDirProps = $APPLICATION->GetDirPropertyList();
        $arPageProps = $APPLICATION->GetPagePropertyList();

        if ($arDirProps) {
            $arPageProps = array_merge($arDirProps, $arPageProps);
        }

        if (!isset($arPageProps['OG:TYPE'])) {
            $Asset->addString('<meta property="og:type" content="website">', $bUnique = true);
            $Asset->addString('<meta property="twitter:card" content="summary">', $bUnique = true);
        }

        foreach ($arMetaPropName as $sMetaPropName) {

            $key = strtoupper($sMetaPropName);

            if (isset($arPageProps[$key])) {

                switch ($key) {
                    case 'OG:IMAGE':
                        if (!empty($arPageProps[$key])) {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . CHTTP::URN2URI($arPageProps[$key]) . '">', $bUnique = true);
                        }
                        break;

                    default:
                        $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . htmlspecialcharsbx($arPageProps[$key]) . '">', $bUnique = true);
                        break;
                }

            } else {

                switch ($key) {
                    case 'OG:SITE_NAME':
                        $dbSite = CSite::GetByID(SITE_ID);
                        if ($arSite = $dbSite->GetNext()) {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . $arSite['SITE_NAME'] . '">', $bUnique = true);
                        }
                        
                        break;

                    case 'OG:TITLE':
                        if (!empty($arPageProps['TITLE'])) {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . $arPageProps['TITLE'] . '">', $bUnique = true);
                        } else {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . $APPLICATION->GetTitle() . '">', $bUnique = true);
                        }
                        break;

                    case 'OG:DESCRIPTION':
                        if (!empty($arPageProps['DESCRIPTION'])) {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . $arPageProps['DESCRIPTION'] . '">', $bUnique = true);
                        }
                        break;

                    case 'OG:URL':
                        if (!empty($arPageProps['CANONICAL'])) {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . $arPageProps['CANONICAL'] . '">', $bUnique = true);
                        } else {
                            $request = Application::getInstance()->getContext()->getRequest();
                            $uriString = $request->getRequestUri();
                            //$uri = new Uri($uriString);

                            $Asset->addString('<meta property="og:url" content="' . CHTTP::URN2URI($uriString) . '">', $bUnique = true);
                        }
                        break;

                    case 'FB:APP_ID':
                        $sFbApiId = COption::GetOptionString('socialservices', 'facebook_appid');
                        if ($fbApiId && strlen($sFbApiId)) {
                            $Asset->addString('<meta property="' . $sMetaPropName . '" content="' . $sFbApiId . '">', $bUnique = true);
                        }
                        break;

                    default:
                        break;
                }
            }
        }
    }
}
