<?php
/********************************************************************************
 * Simple delivery handler.
 * It uses fixed delivery price for any location groups. Needs at least one group of locations to be configured.
 ********************************************************************************/
CModule::IncludeModule('sale');
CModule::IncludeModule('epages.pickpoint');

include GetLangFileName(dirname(__FILE__).'/', '/delivery_pickpoint.php');

class CDeliveryPickPoint
{
    public function Init()
    {
        return array(
            'SID' => 'pickpoint',
            'NAME' => GetMessage('PP_NAME'),
            'DESCRIPTION' => GetMessage('DESCRIPTION'),
            'DESCRIPTION_INNER' => GetMessage('DESCRIPTION_INNER'),
            'BASE_CURRENCY' => COption::GetOptionString('sale', 'default_currency', 'RUB'),
            'HANDLER' => __FILE__,
            'GETEXTRAINFOPARAMS' => 'CDeliveryPickPoint::GetExtraInfoParams',
            'DBGETSETTINGS' => array(
                'CDeliveryPickPoint',
                'GetSettings',
            ),
            'DBSETSETTINGS' => array(
                'CDeliveryPickPoint',
                'SetSettings',
            ),
            'GETCONFIG' => array(
                'CDeliveryPickPoint',
                'GetConfig',
            ),

            'COMPABILITY' => array(
                'CDeliveryPickPoint',
                'Compability',
            ),
            'CALCULATOR' => array(
                'CDeliveryPickPoint',
                'Calculate',
            ),
            'PROFILES' => array(
                'postamat' => array(
                    'TITLE' => GetMessage('PICKPOINT_MAIN'),
                    'DESCRIPTION' => GetMessage('PICKPOINT_SMALL_DESCRIPTION'),

                    'RESTRICTIONS_WEIGHT' => array(0),
                    'RESTRICTIONS_SUM' => array(0),
                ),
            ),
        );
    }

    public function GetConfig()
    {
        $arConfig = array(
            'CONFIG_GROUPS' => array(
                'postamat' => GetMessage('PICKPOINT_MAIN'),
            ),
            'CONFIG' => array(),
        );

        return $arConfig;
    }

    public function GetSettings($strSettings)
    {
        return unserialize($strSettings);
    }

    public function SetSettings($arSettings)
    {
        foreach ($arSettings as $key => $value) {
            if (strlen($value) > 0) {
                $arSettings[$key] = doubleval($value);
            } else {
                unset($arSettings[$key]);
            }
        }

        return serialize($arSettings);
    }

    public function __GetLocationPrice($LOCATION_ID, $arConfig)
    {
        if (!CheckPickpointLicense(COption::GetOptionString('epages.pickpoint', 'pp_ikn_number', ''))) {
            return false;
        }

        $obCity = CPickpoint::SelectCityByBXID($LOCATION_ID);
        if ($arCity = $obCity->Fetch()) {
            if ($arCity['ACTIVE'] == 'Y') {
                return floatval($arCity['PRICE']);
            }
        }

        return false;
    }

    public function __GetPrice($arOrder)
    {
        return CPickpoint::Calculate($arOrder);
    }

    public function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
    {
        return array(
            'RESULT' => 'OK',
            'VALUE' => self::__GetPrice($arOrder),
        );
    }

    public function Compability($arOrder, $arConfig)
    {
        $CSaleLocation = new CSaleLocation();
        $locationData = $CSaleLocation->GetByID($arOrder['LOCATION_TO']);
        if (self::isCityAvailable($locationData['CITY_NAME_ORIG'], $locationData['REGION_NAME_ORIG'])
            || self::isCityAvailable($locationData['CITY_NAME_LANG'], $locationData['REGION_NAME_LANG'])
        ) {
            return array('postamat');
        }

        return array();
    }

    private static function isCityAvailable($cityName, $regionName)
    {
        $cities = array();
        $isInRegion = strlen($regionName) > 0;
        if (($citiesHandle = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/epages.pickpoint/cities.csv', 'r'))
            !== false
        ) {
            while (($row = fgets($citiesHandle)) !== false) {
                $data = explode(';', $row);
                if ($isInRegion) {
                    $cities[$data[3]][$data[1]] = ('true' === trim($data[4]));
                } else {
                    $cities[$data[1]] = ('true' === trim($data[4]));
                }
            }
            fclose($citiesHandle);
        }

        if ($isInRegion) {
            if (isset($cities[$regionName][$cityName])) {
                return true;
            }
            $regionName = str_replace(
                GetMessage('PICKPOINT_REGION_WORD_FULL'),
                GetMessage('PICKPOINT_REGION_WORD_SHORT'),
                $regionName
            );
            if (isset($cities[$regionName][$cityName])) {
                return true;
            }
            $regionName = str_replace(
                GetMessage('PICKPOINT_REGION_WORD_SHORT'),
                GetMessage('PICKPOINT_REGION_WORD_FULL'),
                $regionName
            );
            if (isset($cities[$regionName][$cityName])) {
                return true;
            }
        } else {
            if (isset($cities[$cityName])) {
                return true;
            }
        }

        return false;
    }
}

AddEventHandler(
    'sale',
    'onSaleDeliveryHandlersBuildList',
    array(
        'CDeliveryPickPoint',
        'Init',
    )
);
